<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function search(Request $request)
    {
        $q = (string) $request->query('q', '');
        return response()->json([
            'ok' => true,
            'results' => search_site($q)
        ]);
    }

    public function formSchemas(): array
    {
        return [
            'contact'       => ['req' => ['name', 'email', 'type', 'message'], 'opt' => ['phone', 'country']],
            'appointment'   => ['req' => ['name', 'contact', 'type', 'location', 'date'], 'opt' => ['time', 'notes']],
            'wedding'       => ['req' => ['name', 'contact', 'date', 'for'], 'opt' => ['pairs', 'notes']],
            'restoration'   => ['req' => ['name', 'contact', 'service'], 'opt' => ['notes']],
            'oneofone'      => ['req' => ['name', 'email', 'idea'], 'opt' => ['phone', 'country']],
            'designers'     => ['req' => ['name', 'email', 'type', 'message'], 'opt' => ['company', 'linesheet']],
            'order_request' => ['req' => ['name', 'contact', 'items'], 'opt' => ['gift', 'currency']],
        ];
    }

    public function form(Request $request)
    {
        // Honeypot check
        if (!empty($request->input('website')) || !empty($request->input('_gotcha'))) {
            return response()->json(['ok' => true]);
        }

        $kind = (string) $request->input('kind', '');

        // If order_request, delegate to createOrder if structured or handle registration
        if ($kind === 'order_request') {
            return $this->createOrder($request);
        }

        $schemas = $this->formSchemas();
        $schema = $schemas[$kind] ?? null;

        if (!$schema) {
            return response()->json(['ok' => false, 'error' => 'Unknown form.'], 400);
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $key = 'form:' . $kind . ':' . $ip;
        if (RateLimiter::tooManyAttempts($key, 8)) {
            return response()->json(['ok' => false, 'error' => 'Too many messages. Please try again later or message us on WhatsApp.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $data = [];
        foreach (array_merge($schema['req'], $schema['opt']) as $f) {
            $data[$f] = trim(mb_substr((string) $request->input($f, ''), 0, $f === 'items' ? 5000 : 2000));
        }

        $missing = array_values(array_filter($schema['req'], fn($f) => $data[$f] === ''));
        if (!empty($missing)) {
            return response()->json(['ok' => false, 'error' => 'Please complete the highlighted fields.', 'fields' => $missing], 422);
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(['ok' => false, 'error' => 'Please enter a valid email address.', 'fields' => ['email']], 422);
        }

        $email = $data['email'] ?? (filter_var($data['contact'] ?? '', FILTER_VALIDATE_EMAIL) ? $data['contact'] : null);
        $phone = $data['phone'] ?? ($email ? null : ($data['contact'] ?? null));

        $enquiry = Enquiry::create([
            'kind' => $kind,
            'name' => $data['name'] ?? null,
            'email' => $email,
            'phone' => $phone,
            'payload_json' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'ip' => $ip,
        ]);

        $this->saveUploadedFiles($request, 'enquiry', $enquiry->id);

        return response()->json(['ok' => true, 'message' => 'Received. A member of the House will reply within one working day.']);
    }

    /**
     * Handle Customer Order Placement.
     * Enforces customer account registration/login with all personal & delivery location info.
     */
    public function createOrder(Request $request)
    {
        $ip = $request->ip() ?? '0.0.0.0';
        $key = 'order:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 12)) {
            return response()->json(['ok' => false, 'error' => 'Too many order requests. Please try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $itemsRaw = $request->input('bag') ?? $request->input('items');
        $items = is_array($itemsRaw) ? $itemsRaw : json_decode((string) $itemsRaw, true);

        // If items passed as text or array
        $gift = trim((string) $request->input('gift', ''));
        $cur = (string) $request->input('currency', 'INR');

        /** @var Customer|null $customer */
        $customer = Auth::user();

        // If user is not authenticated, validate and register their account first
        if (!$customer) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required_without:name|string|max:80',
                'last_name'  => 'nullable|string|max:80',
                'name'       => 'nullable|string|max:120',
                'email'      => 'required|string|email|max:190',
                'phone'      => 'required|string|max:40',
                'password'   => 'required|string|min:6',
                'line1'      => 'required|string|max:190',
                'line2'      => 'nullable|string|max:190',
                'city'       => 'required|string|max:80',
                'state'      => 'required|string|max:80',
                'postcode'   => 'required|string|max:20',
                'country'    => 'nullable|string|max:60',
            ], [
                'first_name.required_without' => 'Please provide your first name.',
                'email.required'              => 'Please provide your email address.',
                'phone.required'              => 'Please provide your phone/WhatsApp number.',
                'password.required'           => 'Please set an account password (minimum 6 characters).',
                'line1.required'              => 'Please provide your delivery location/address.',
                'city.required'               => 'Please provide your city.',
                'state.required'              => 'Please provide your state.',
                'postcode.required'           => 'Please provide your postal pincode.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'ok' => false,
                    'requires_account' => true,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                    'fields' => array_keys($validator->errors()->toArray())
                ], 422);
            }

            $email = mb_strtolower(trim($request->input('email')));
            $customer = Customer::where('email', $email)->first();

            if ($customer) {
                // Email already registered: check password or prompt sign in
                if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $customer->password_hash)) {
                    return response()->json([
                        'ok' => false,
                        'requires_account' => true,
                        'error' => 'An account with this email already exists. Please sign in with your password to place your order.',
                        'fields' => ['password']
                    ], 422);
                }
            } else {
                $firstName = trim($request->input('first_name', ''));
                $lastName = trim($request->input('last_name', ''));
                if (!$firstName && $request->filled('name')) {
                    $parts = explode(' ', trim($request->input('name')), 2);
                    $firstName = $parts[0];
                    $lastName = $parts[1] ?? '';
                }

                $customer = Customer::create([
                    'first_name'       => $firstName,
                    'last_name'        => $lastName,
                    'email'            => $email,
                    'phone'            => trim($request->input('phone')),
                    'password'         => $request->input('password'),
                    'country'          => trim($request->input('country', 'India')),
                    'status'           => 'active',
                    'marketing_opt_in' => $request->boolean('marketing_opt_in', true),
                    'last_login_at'    => now(),
                ]);

                CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'label'       => 'Delivery',
                    'name'        => $customer->full_name,
                    'line1'       => trim($request->input('line1')),
                    'line2'       => trim($request->input('line2', '')),
                    'city'        => trim($request->input('city')),
                    'state'       => trim($request->input('state')),
                    'postcode'    => trim($request->input('postcode')),
                    'country'     => trim($request->input('country', 'India')),
                    'phone'       => $customer->phone,
                    'is_default'  => true,
                ]);
            }

            Auth::login($customer, true);
        }

        // Get shipping address
        $address = $customer->defaultAddress ?? $customer->addresses()->first();
        $shipLine1 = $request->input('line1') ?: ($address->line1 ?? 'Flagship House');
        $shipLine2 = $request->input('line2') ?: ($address->line2 ?? null);
        $shipCity = $request->input('city') ?: ($address->city ?? 'Kolkata');
        $shipState = $request->input('state') ?: ($address->state ?? 'West Bengal');
        $shipPostcode = $request->input('postcode') ?: ($address->postcode ?? '700016');
        $shipCountry = $request->input('country') ?: ($address->country ?? 'India');
        $shipName = $customer->full_name ?: ($address->name ?? 'Valued Patron');
        $shipPhone = $customer->phone ?: ($address->phone ?? null);

        // Calculate Order Totals
        $orderCount = Order::count();
        $orderNo = setting('order_prefix', 'TM-') . date('y') . '-' . str_pad((string) (1 + $orderCount), 4, '0', STR_PAD_LEFT);

        $subtotalInr = 0;
        $orderItemRecords = [];

        if (is_array($items) && !empty($items)) {
            foreach ($items as $itm) {
                $qty = max(1, (int) ($itm['qty'] ?? 1));
                $price = (int) ($itm['price'] ?? 0);
                $subtotalInr += ($price * $qty);

                // Find product if possible
                $prodId = null;
                if (!empty($itm['key'])) {
                    $p = Product::where('slug', $itm['key'])->first();
                    if ($p) $prodId = $p->id;
                }

                $orderItemRecords[] = [
                    'product_id'     => $prodId,
                    'name'           => mb_substr($itm['name'] ?? 'Handcrafted Pair', 0, 160),
                    'colour'         => mb_substr($itm['colour'] ?? '', 0, 60),
                    'size'           => mb_substr($itm['size'] ?? '', 0, 20),
                    'qty'            => $qty,
                    'unit_price_inr' => $price,
                    'is_custom'      => !empty($itm['custom']),
                    'made_to_order'  => true,
                ];
            }
        } else {
            // Textual items or fallback
            $subtotalInr = (int) $request->input('subtotal', 0);
            $orderItemRecords[] = [
                'product_id'     => null,
                'name'           => 'Custom Commission / Order Request',
                'colour'         => '',
                'size'           => '',
                'qty'            => 1,
                'unit_price_inr' => $subtotalInr,
                'is_custom'      => true,
                'made_to_order'  => true,
            ];
        }

        $totalInr = $subtotalInr;

        $order = Order::create([
            'order_no'         => $orderNo,
            'customer_id'      => $customer->id,
            'email'            => $customer->email,
            'phone'            => $shipPhone,
            'ship_name'        => $shipName,
            'ship_line1'       => $shipLine1,
            'ship_line2'       => $shipLine2,
            'ship_city'        => $shipCity,
            'ship_state'       => $shipState,
            'ship_postcode'    => $shipPostcode,
            'ship_country'     => $shipCountry,
            'bill_same'        => true,
            'currency'         => $cur ?: 'INR',
            'subtotal_inr'     => $subtotalInr,
            'total_inr'        => $totalInr,
            'total_charged'    => $totalInr,
            'gift_note'        => $gift ?: null,
            'status'           => 'Received',
            'payment_status'   => 'Pending Confirmation',
            'access_token'     => Str::random(32),
        ]);

        foreach ($orderItemRecords as $rec) {
            $rec['order_id'] = $order->id;
            OrderItem::create($rec);
        }

        OrderStatusHistory::create([
            'order_id'          => $order->id,
            'status'            => 'Received',
            'note'              => 'Order placed online by customer.',
            'customer_notified' => true,
        ]);

        // Also record enquiry for notifications
        Enquiry::create([
            'kind'         => 'order_request',
            'name'         => $customer->full_name,
            'email'        => $customer->email,
            'phone'        => $customer->phone,
            'payload_json' => json_encode([
                'order_no' => $orderNo,
                'order_id' => $order->id,
                'total'    => $totalInr,
                'items'    => $items,
                'address'  => [
                    'line1'    => $shipLine1,
                    'line2'    => $shipLine2,
                    'city'     => $shipCity,
                    'state'    => $shipState,
                    'postcode' => $shipPostcode,
                    'country'  => $shipCountry,
                ]
            ], JSON_UNESCAPED_UNICODE),
            'ip'           => $ip,
        ]);

        return response()->json([
            'ok'               => true,
            'order_no'         => $orderNo,
            'order_id'         => $order->id,
            'customer'         => [
                'name'  => $customer->full_name,
                'email' => $customer->email,
            ],
            'message'          => "Thank you {$customer->first_name}. Your order request ({$orderNo}) has been placed. A member of the House will confirm and send a secure invoice.",
            'redirect'         => route('received') . '?order=' . urlencode($orderNo)
        ]);
    }

    public function commission(Request $request)
    {
        // Honeypot check
        if (!empty($request->input('website')) || !empty($request->input('_gotcha'))) {
            return response()->json(['ok' => true]);
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $key = 'commission:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 8)) {
            return response()->json(['ok' => false, 'error' => 'Too many requests. Please try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        $name = trim(mb_substr((string) $request->input('name', ''), 0, 120));
        $contact = trim(mb_substr((string) $request->input('contact', ''), 0, 190));
        $buildRaw = $request->input('build');
        $build = is_array($buildRaw) ? $buildRaw : json_decode((string) $buildRaw, true);

        $fields = [];
        if ($name === '') $fields[] = 'name';
        if ($contact === '') $fields[] = 'contact';
        if (!empty($fields) || !is_array($build)) {
            return response()->json(['ok' => false, 'error' => 'Please add your name and a way to reach you.', 'fields' => $fields], 422);
        }

        $allowed = ['sil', 'colour', 'hex', 'craft', 'art', 'custom', 'initials', 'place', 'gold', 'nails', 'size', 'notes'];
        $build = array_intersect_key($build, array_flip($allowed));

        $count = Commission::count();
        $ref = setting('commission_prefix', 'CM-') . date('y') . str_pad((string) (1 + $count), 4, '0', STR_PAD_LEFT);

        $comm = Commission::create([
            'ref' => $ref,
            'name' => $name,
            'contact' => $contact,
            'build_json' => json_encode($build, JSON_UNESCAPED_UNICODE),
            'estimate_inr' => max(0, (int) $request->input('estimate', 0)),
        ]);

        $this->saveUploadedFiles($request, 'commission', $comm->id);

        return response()->json([
            'ok' => true,
            'ref' => $ref,
            'message' => 'Received. Your reference is ' . $ref . '. A member of the House will reply within one working day.'
        ]);
    }

    public function newsletter(Request $request)
    {
        $email = trim((string) $request->input('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['ok' => false, 'error' => 'Please enter a valid email address.'], 422);
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $key = 'newsletter:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['ok' => false, 'error' => 'Please try again later.'], 429);
        }
        RateLimiter::hit($key, 3600);

        Subscriber::updateOrCreate(
            ['email' => mb_strtolower($email)],
            ['source' => 'footer', 'status' => 'subscribed']
        );

        return response()->json(['ok' => true, 'message' => 'Thank you. A letter will find you soon.']);
    }

    protected function saveUploadedFiles(Request $request, string $ownerType, int $ownerId): void
    {
        if (!$request->hasFile('files')) {
            return;
        }

        $files = $request->file('files');
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach (array_slice($files, 0, 5) as $file) {
            if (!$file->isValid()) continue;
            
            $mime = $file->getClientMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif', 'application/pdf'];
            if (!in_array($mime, $allowedMimes, true)) continue;

            $path = $file->store('customer_uploads', 'local');
            Upload::create([
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'path' => $path,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 190),
                'mime' => $mime,
                'size_bytes' => $file->getSize(),
            ]);
        }
    }
}
