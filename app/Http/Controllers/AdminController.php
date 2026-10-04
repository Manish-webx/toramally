<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AdminUser;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductColour;
use App\Models\ProductImage;
use App\Models\ProductStock;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /* =========================================================================
     * AUTHENTICATION
     * ========================================================================= */

    public function loginForm()
    {
        if (session('admin_user_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = AdminUser::where('email', strtolower(trim($request->email)))
            ->where('active', 1)
            ->first();

        if (!$admin || !password_verify($request->password, $admin->password_hash)) {
            return back()->withInput($request->only('email'))->with('error', 'Invalid atelier credentials or account inactive.');
        }

        $admin->update([
            'last_login_at' => now(),
        ]);

        session([
            'admin_user_id' => $admin->id,
            'admin_user_name' => $admin->name,
            'admin_user_email' => $admin->email,
            'admin_user_role' => $admin->role,
        ]);

        ActivityLog::create([
            'admin_id' => $admin->id,
            'action' => 'admin_login',
            'entity' => 'admin_users',
            'entity_id' => $admin->id,
            'details' => 'Admin signed in successfully from IP ' . $request->ip(),
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $admin->name);
    }

    public function logout(Request $request)
    {
        $adminId = session('admin_user_id');
        if ($adminId) {
            ActivityLog::create([
                'admin_id' => $adminId,
                'action' => 'admin_logout',
                'entity' => 'admin_users',
                'entity_id' => $adminId,
                'details' => 'Admin signed out',
                'ip' => $request->ip(),
            ]);
        }

        session()->forget(['admin_user_id', 'admin_user_name', 'admin_user_email', 'admin_user_role']);
        return redirect()->route('admin.login')->with('success', 'You have been signed out safely.');
    }

    /* =========================================================================
     * DASHBOARD
     * ========================================================================= */

    public function dashboard()
    {
        $totalRevenue = Order::whereNotIn('status', ['Cancelled', 'Draft'])->sum('total_inr');
        $totalOrders = Order::count();
        $pendingOrders = Order::whereIn('status', ['Order Placed', 'Payment Pending', 'Enquiry received'])->count();
        $inCraftingOrders = Order::whereIn('status', ['In Production', 'In Crafting', 'Confirmed'])->count();
        
        $lowStockCount = ProductStock::where('qty', '<=', 2)->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalCommissions = Commission::count();

        $recentOrders = Order::with(['customer', 'items'])
            ->latest()
            ->take(6)
            ->get();

        $recentCommissions = Commission::latest()
            ->take(5)
            ->get();

        $recentEnquiries = Enquiry::latest()
            ->take(5)
            ->get();

        $lowStockItems = ProductStock::with('product')
            ->where('qty', '<=', 2)
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'inCraftingOrders',
            'lowStockCount',
            'totalCustomers',
            'totalProducts',
            'totalCommissions',
            'recentOrders',
            'recentCommissions',
            'recentEnquiries',
            'lowStockItems'
        ));
    }

    /* =========================================================================
     * ORDERS MANAGEMENT
     * ========================================================================= */

    public function ordersIndex(Request $request)
    {
        $query = Order::with(['customer', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_no', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('ship_name', 'like', "%{$s}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'placed' => Order::where('status', 'Order Placed')->count(),
            'confirmed' => Order::where('status', 'Confirmed')->count(),
            'crafting' => Order::whereIn('status', ['In Production', 'In Crafting'])->count(),
            'shipped' => Order::where('status', 'Shipped')->count(),
            'delivered' => Order::where('status', 'Delivered')->count(),
            'cancelled' => Order::where('status', 'Cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function orderShow($id)
    {
        $order = Order::with(['customer', 'items', 'statusHistory.admin', 'payments', 'shipment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function orderUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:60',
            'notes' => 'nullable|string|max:500',
            'notify_customer' => 'nullable|boolean',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'note' => $request->notes ?: "Status changed from {$oldStatus} to {$request->status}",
            'customer_notified' => (bool)$request->notify_customer,
            'admin_id' => session('admin_user_id'),
        ]);

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'update_order_status',
            'entity' => 'orders',
            'entity_id' => $order->id,
            'details' => "Order #{$order->order_no} status changed from {$oldStatus} to {$request->status}",
            'ip' => $request->ip(),
        ]);

        return back()->with('success', "Order #{$order->order_no} status updated to {$request->status}.");
    }

    public function orderUpdatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|string|in:pending,paid,partial,refunded,failed',
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return back()->with('success', "Payment status updated to " . ucfirst($request->payment_status));
    }

    public function orderSaveNotes(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->admin_notes = $request->admin_notes;
        $order->save();

        return back()->with('success', "Atelier notes saved for Order #{$order->order_no}");
    }

    public function orderInvoice($id)
    {
        $order = Order::with(['customer', 'items'])->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    /* =========================================================================
     * INVENTORY MANAGEMENT
     * ========================================================================= */

    public function inventoryIndex(Request $request)
    {
        $query = Product::with(['colours', 'stocks'])->orderBy('sort', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%")
                    ->orWhere('silhouette', 'like', "%{$s}%");
            });
        }

        $products = $query->paginate(15)->withQueryString();

        $standardSizes = ['UK 5', 'UK 5.5', 'UK 6', 'UK 6.5', 'UK 7', 'UK 7.5', 'UK 8', 'UK 8.5', 'UK 9', 'UK 9.5', 'UK 10', 'UK 10.5', 'UK 11', 'UK 11.5', 'UK 12'];
        $womenSizes = ['UK 3', 'UK 4', 'UK 5', 'UK 6', 'UK 7', 'UK 8'];
        $beltSizes = ['80 cm', '85 cm', '90 cm', '95 cm', '100 cm', '105 cm', '110 cm'];

        $categories = Category::orderBy('sort')->orderBy('name')->get();

        return view('admin.inventory.index', compact('products', 'standardSizes', 'womenSizes', 'beltSizes', 'categories'));
    }

    public function inventoryUpdate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'colour_id' => 'nullable|integer',
            'size' => 'required|string',
            'qty' => 'required|integer|min:0',
        ]);

        $stock = ProductStock::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'colour_id' => $request->colour_id ?: null,
                'size' => $request->size,
            ],
            [
                'qty' => $request->qty,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'qty' => $stock->qty]);
        }

        return back()->with('success', 'Stock updated successfully.');
    }

    /* =========================================================================
     * CATEGORIES MANAGEMENT
     * ========================================================================= */

    public function categoriesIndex(Request $request)
    {
        $query = Category::withCount('products')->orderBy('sort', 'asc')->orderBy('name', 'asc');

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active' ? 1 : 0);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $categories = $query->paginate(20)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function categoryCreate()
    {
        return view('admin.categories.form', [
            'category' => new Category(['active' => true, 'sort' => 0]),
            'isEdit' => false,
        ]);
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'slug' => 'nullable|string|max:60|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'sort' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $category = Category::create([
            'name' => trim($request->name),
            'slug' => $slug,
            'description' => $request->description,
            'sort' => (int) ($request->sort ?? 0),
            'active' => $request->has('active') ? 1 : 0,
        ]);

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'create_category',
            'entity' => 'categories',
            'entity_id' => $category->id,
            'details' => "Created category '{$category->name}' (slug: {$category->slug})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', "Category '{$category->name}' created successfully.");
    }

    public function categoryEdit($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        return view('admin.categories.form', [
            'category' => $category,
            'isEdit' => true,
        ]);
    }

    public function categoryUpdate(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:60',
            'slug' => "nullable|string|max:60|unique:categories,slug,{$id}",
            'description' => 'nullable|string|max:1000',
            'sort' => 'nullable|integer',
        ]);

        $oldName = $category->name;
        $newName = trim($request->name);
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($newName);

        $category->update([
            'name' => $newName,
            'slug' => $slug,
            'description' => $request->description,
            'sort' => (int) ($request->sort ?? 0),
            'active' => $request->has('active') ? 1 : 0,
        ]);

        // If category name updated, update matching products
        if ($oldName !== $newName) {
            Product::where('category', $oldName)->update(['category' => $newName]);
        }

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'update_category',
            'entity' => 'categories',
            'entity_id' => $category->id,
            'details' => "Updated category '{$category->name}' (slug: {$category->slug})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', "Category '{$category->name}' updated successfully.");
    }

    public function categoryToggle($id)
    {
        $category = Category::findOrFail($id);
        $category->active = !$category->active;
        $category->save();

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'toggle_category',
            'entity' => 'categories',
            'entity_id' => $category->id,
            'details' => "Toggled category '{$category->name}' active state to " . ($category->active ? 'Active' : 'Inactive'),
            'ip' => request()->ip(),
        ]);

        return back()->with('success', "Category '{$category->name}' status updated.");
    }

    public function categoryDelete($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because {$category->products_count} product(s) are assigned to it. Please reassign or remove the products first.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'delete_category',
            'entity' => 'categories',
            'entity_id' => $id,
            'details' => "Deleted category '{$name}'",
            'ip' => request()->ip(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', "Category '{$name}' deleted successfully.");
    }

    /* =========================================================================
     * PRODUCTS MANAGEMENT
     * ========================================================================= */

    public function productsIndex(Request $request)
    {
        $query = Product::with(['colours', 'stocks'])->orderBy('sort', 'asc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%")
                    ->orWhere('silhouette', 'like', "%{$s}%");
            });
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('sort')->orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function productCreate()
    {
        $crafts = Collection::where('type', 'craft')->orderBy('sort')->get();
        $categories = Category::where('active', true)->orderBy('sort')->orderBy('name')->get();
        return view('admin.products.form', [
            'product' => new Product(),
            'crafts' => $crafts,
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|unique:products,slug',
            'category' => 'required|string|max:40',
            'silhouette' => 'required|string|max:60',
            'base_price' => 'required|numeric|min:0',
            'availability' => 'required|string|max:40',
            'lead_min' => 'required|integer|min:0',
            'lead_max' => 'required|integer|min:0',
            'poetic' => 'nullable|string|max:255',
            'story' => 'nullable|string',
            'material' => 'nullable|string|max:60',
            'construction' => 'nullable|string|max:60',
            'last_name' => 'nullable|string|max:40',
            'hsn_code' => 'nullable|string|max:20',
            'craft_id' => 'nullable|integer',
            'status' => 'required|string|in:Published,Draft,Archived',
        ]);

        $data = $request->except(['_token', 'colours', 'initial_sizes', 'drawing_shape', 'drawing_art', 'colour_names', 'colour_hexes', 'new_images', 'new_image_colours', 'new_image_kinds', 'new_image_alts', 'new_image_sorts']);
        $data['drawing_json'] = json_encode([
            'shape' => $request->drawing_shape ?: Str::slug($request->silhouette),
            'art' => $request->drawing_art ?: null,
        ]);
        $data['featured'] = (bool)$request->featured;

        $product = Product::create($data);

        // Handle Colours
        $colourMap = [];
        if ($request->filled('colour_names')) {
            $names = $request->colour_names;
            $hexes = $request->colour_hexes ?? [];
            foreach ($names as $idx => $cName) {
                $trimmed = trim($cName);
                if ($trimmed) {
                    $col = ProductColour::create([
                        'product_id' => $product->id,
                        'name' => $trimmed,
                        'hex' => $hexes[$idx] ?? '#000000',
                        'price_diff' => 0,
                        'sort' => $idx * 10,
                    ]);
                    $colourMap[$trimmed] = $col->id;
                }
            }
        }

        // Handle New Images Upload
        if ($request->hasFile('new_images')) {
            $files = $request->file('new_images');
            $newColours = $request->input('new_image_colours', []);
            $newKinds = $request->input('new_image_kinds', []);
            $newAlts = $request->input('new_image_alts', []);
            $newSorts = $request->input('new_image_sorts', []);

            $destDir = public_path('uploads/products');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }

            foreach ($files as $idx => $file) {
                if ($file && $file->isValid()) {
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = 'prod_' . $product->id . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $file->move($destDir, $filename);
                    $relPath = 'uploads/products/' . $filename;

                    $chosenCol = $newColours[$idx] ?? null;
                    $colourId = null;
                    if ($chosenCol !== null && $chosenCol !== '' && $chosenCol !== '*') {
                        if (is_numeric($chosenCol)) {
                            $colourId = (int)$chosenCol;
                        } elseif (isset($colourMap[$chosenCol])) {
                            $colourId = $colourMap[$chosenCol];
                        } else {
                            $colModel = ProductColour::where('product_id', $product->id)->where('name', $chosenCol)->first();
                            $colourId = $colModel?->id;
                        }
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'colour_id' => $colourId ?: null,
                        'path' => $relPath,
                        'alt' => !empty($newAlts[$idx]) ? trim($newAlts[$idx]) : ($product->name . ($chosenCol && $chosenCol !== '*' ? ' - ' . $chosenCol : '')),
                        'kind' => $newKinds[$idx] ?? 'side',
                        'sort' => (int)($newSorts[$idx] ?? ($idx * 10)),
                    ]);
                }
            }
        }

        // Initialize default stock entries
        if ($request->filled('init_sizes')) {
            foreach ($request->init_sizes as $sz) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'colour_id' => null,
                    'size' => trim($sz),
                    'qty' => (int)($request->init_qty ?? 0),
                ]);
            }
        }

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'create_product',
            'entity' => 'products',
            'entity_id' => $product->id,
            'details' => "Created product {$product->name} (slug: {$product->slug})",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created successfully.");
    }

    public function productEdit($id)
    {
        $product = Product::with(['colours', 'stocks', 'images'])->findOrFail($id);
        $crafts = Collection::where('type', 'craft')->orderBy('sort')->get();
        $categories = Category::orderBy('sort')->orderBy('name')->get();
        return view('admin.products.form', [
            'product' => $product,
            'crafts' => $crafts,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:120',
            'slug' => "required|string|max:120|unique:products,slug,{$id}",
            'category' => 'required|string|max:40',
            'silhouette' => 'required|string|max:60',
            'base_price' => 'required|numeric|min:0',
            'availability' => 'required|string|max:40',
            'lead_min' => 'required|integer|min:0',
            'lead_max' => 'required|integer|min:0',
            'status' => 'required|string|in:Published,Draft,Archived',
        ]);

        $data = $request->except([
            '_token', 'colours', 'drawing_shape', 'drawing_art',
            'colour_names', 'colour_hexes', 'colour_ids',
            'existing_image_ids', 'existing_image_colours', 'existing_image_kinds', 'existing_image_alts', 'existing_image_sorts',
            'delete_image_ids', 'new_images', 'new_image_colours', 'new_image_kinds', 'new_image_alts', 'new_image_sorts'
        ]);
        $data['drawing_json'] = json_encode([
            'shape' => $request->drawing_shape ?: Str::slug($request->silhouette),
            'art' => $request->drawing_art ?: null,
        ]);
        $data['featured'] = (bool)$request->featured;

        $product->update($data);

        // Synchronize colours
        $colourMap = [];
        if ($request->has('colour_names')) {
            $submittedNames = $request->colour_names;
            $submittedHexes = $request->colour_hexes ?? [];
            $submittedIds = $request->colour_ids ?? [];

            $existingColours = ProductColour::where('product_id', $product->id)->get()->keyBy('id');
            $keptIds = [];

            foreach ($submittedNames as $idx => $cName) {
                $trimmed = trim($cName);
                if ($trimmed !== '') {
                    $cid = $submittedIds[$idx] ?? null;
                    if ($cid && isset($existingColours[$cid])) {
                        $existingColours[$cid]->update([
                            'name' => $trimmed,
                            'hex' => $submittedHexes[$idx] ?? '#000000',
                            'sort' => $idx * 10,
                        ]);
                        $keptIds[] = (int)$cid;
                        $colourMap[$trimmed] = (int)$cid;
                    } else {
                        $newCol = ProductColour::create([
                            'product_id' => $product->id,
                            'name' => $trimmed,
                            'hex' => $submittedHexes[$idx] ?? '#000000',
                            'price_diff' => 0,
                            'sort' => $idx * 10,
                        ]);
                        $keptIds[] = $newCol->id;
                        $colourMap[$trimmed] = $newCol->id;
                    }
                }
            }

            $toDelete = $existingColours->keys()->diff($keptIds);
            if ($toDelete->isNotEmpty()) {
                ProductColour::whereIn('id', $toDelete)->delete();
                ProductImage::where('product_id', $product->id)->whereIn('colour_id', $toDelete)->update(['colour_id' => null]);
            }
        }

        // Handle Existing Images Deletion
        if ($request->filled('delete_image_ids')) {
            $delIds = (array)$request->delete_image_ids;
            $imgsToDelete = ProductImage::where('product_id', $product->id)->whereIn('id', $delIds)->get();
            foreach ($imgsToDelete as $dimg) {
                $fullPath = public_path($dimg->path);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                $dimg->delete();
            }
        }

        // Handle Existing Images Update
        if ($request->filled('existing_image_ids')) {
            $eIds = (array)$request->existing_image_ids;
            $eColours = $request->input('existing_image_colours', []);
            $eKinds = $request->input('existing_image_kinds', []);
            $eAlts = $request->input('existing_image_alts', []);
            $eSorts = $request->input('existing_image_sorts', []);
            $deletedIds = (array)($request->delete_image_ids ?? []);

            foreach ($eIds as $imgId) {
                if (in_array($imgId, $deletedIds)) continue;

                $img = ProductImage::where('product_id', $product->id)->find($imgId);
                if ($img) {
                    $rawCol = $eColours[$imgId] ?? null;
                    $cid = null;
                    if ($rawCol !== null && $rawCol !== '' && $rawCol !== '*') {
                        if (is_numeric($rawCol)) {
                            $cid = (int)$rawCol;
                        } elseif (isset($colourMap[$rawCol])) {
                            $cid = $colourMap[$rawCol];
                        } else {
                            $colModel = ProductColour::where('product_id', $product->id)->where('name', $rawCol)->first();
                            $cid = $colModel?->id;
                        }
                    }
                    $img->update([
                        'colour_id' => $cid ?: null,
                        'kind' => $eKinds[$imgId] ?? $img->kind,
                        'alt' => $eAlts[$imgId] ?? $img->alt,
                        'sort' => (int)($eSorts[$imgId] ?? $img->sort),
                    ]);
                }
            }
        }

        // Handle New Images Upload
        if ($request->hasFile('new_images')) {
            $files = $request->file('new_images');
            $newColours = $request->input('new_image_colours', []);
            $newKinds = $request->input('new_image_kinds', []);
            $newAlts = $request->input('new_image_alts', []);
            $newSorts = $request->input('new_image_sorts', []);

            $destDir = public_path('uploads/products');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }

            foreach ($files as $idx => $file) {
                if ($file && $file->isValid()) {
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = 'prod_' . $product->id . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $file->move($destDir, $filename);
                    $relPath = 'uploads/products/' . $filename;

                    $chosenCol = $newColours[$idx] ?? null;
                    $colourId = null;
                    if ($chosenCol !== null && $chosenCol !== '' && $chosenCol !== '*') {
                        if (is_numeric($chosenCol)) {
                            $colourId = (int)$chosenCol;
                        } elseif (isset($colourMap[$chosenCol])) {
                            $colourId = $colourMap[$chosenCol];
                        } else {
                            $colModel = ProductColour::where('product_id', $product->id)->where('name', $chosenCol)->first();
                            $colourId = $colModel?->id;
                        }
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'colour_id' => $colourId ?: null,
                        'path' => $relPath,
                        'alt' => !empty($newAlts[$idx]) ? trim($newAlts[$idx]) : ($product->name . ($chosenCol && $chosenCol !== '*' ? ' - ' . $chosenCol : '')),
                        'kind' => $newKinds[$idx] ?? 'side',
                        'sort' => (int)($newSorts[$idx] ?? ($idx * 10)),
                    ]);
                }
            }
        }

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'update_product',
            'entity' => 'products',
            'entity_id' => $product->id,
            'details' => "Updated product {$product->name}",
            'ip' => $request->ip(),
        ]);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function productToggleStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'Published' ? 'Draft' : 'Published';
        $product->save();

        return back()->with('success', "Product '{$product->name}' status changed to {$product->status}.");
    }

    /* =========================================================================
     * BESPOKE COMMISSIONS
     * ========================================================================= */

    public function commissionsIndex(Request $request)
    {
        $query = Commission::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('ref', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%")
                    ->orWhere('contact', 'like', "%{$s}%");
            });
        }

        $commissions = $query->paginate(20)->withQueryString();
        return view('admin.commissions.index', compact('commissions'));
    }

    public function commissionShow($id)
    {
        $commission = Commission::findOrFail($id);
        $uploads = Upload::where('owner_type', 'commission')->where('owner_id', $commission->id)->get();
        $build = json_decode($commission->build_json, true) ?: [];
        return view('admin.commissions.show', compact('commission', 'uploads', 'build'));
    }

    public function commissionUpdate(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        $request->validate([
            'status' => 'required|string|max:60',
            'quote_inr' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $commission->status = $request->status;
        $commission->quote_inr = $request->quote_inr ?: null;
        $commission->admin_notes = $request->admin_notes;
        $commission->save();

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'update_commission',
            'entity' => 'commissions',
            'entity_id' => $commission->id,
            'details' => "Updated commission #{$commission->ref}",
            'ip' => $request->ip(),
        ]);

        return back()->with('success', "Commission #{$commission->ref} updated successfully.");
    }

    /* =========================================================================
     * CUSTOMERS & PATRONS
     * ========================================================================= */

    public function customersIndex(Request $request)
    {
        $query = Customer::with(['orders', 'addresses'])->latest();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function customerShow($id)
    {
        $customer = Customer::with(['orders.items', 'addresses', 'sizes'])->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    /* =========================================================================
     * ENQUIRIES & APPOINTMENTS
     * ========================================================================= */

    public function enquiriesIndex(Request $request)
    {
        $query = Enquiry::latest();

        if ($request->filled('kind')) {
            $query->where('kind', $request->kind);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enquiries = $query->paginate(20)->withQueryString();
        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function enquiryUpdateStatus(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->status = $request->status ?: 'Resolved';
        $enquiry->save();

        return back()->with('success', 'Enquiry status updated.');
    }

    /* =========================================================================
     * SUBSCRIBERS
     * ========================================================================= */

    public function subscribersIndex(Request $request)
    {
        if ($request->get('export') === 'csv') {
            $subscribers = Subscriber::all();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="toramally_subscribers_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($subscribers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Email', 'Source', 'Status', 'Subscribed Date']);
                foreach ($subscribers as $s) {
                    fputcsv($file, [$s->id, $s->email, $s->source, $s->status, $s->created_at]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $subscribers = Subscriber::latest()->paginate(30);
        return view('admin.subscribers.index', compact('subscribers'));
    }

    /* =========================================================================
     * SETTINGS
     * ========================================================================= */

    public function settingsIndex()
    {
        $settings = Setting::all()->pluck('v', 'k')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $k => $v) {
            Setting::updateOrCreate(['k' => $k], ['v' => (string)$v]);
        }

        ActivityLog::create([
            'admin_id' => session('admin_user_id'),
            'action' => 'update_settings',
            'entity' => 'settings',
            'details' => 'Admin updated atelier store settings',
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Atelier settings saved successfully.');
    }
}
