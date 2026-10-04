<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('account');
        }
        return view('pages.auth.login', [
            'meta' => [
                'title' => 'Sign In | Tōramally',
                'description' => 'Sign in to your Tōramally account to view your orders, commissions and saved sizes.',
                'nav' => '',
            ]
        ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('account');
        }
        return view('pages.auth.register', [
            'meta' => [
                'title' => 'Create Account | Tōramally',
                'description' => 'Create your Tōramally account to order handcrafted pairs, request custom commissions and save your measurements.',
                'nav' => '',
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'     => 'required|string|max:80',
            'last_name'      => 'required|string|max:80',
            'email'          => 'required|string|email|max:190|unique:customers,email',
            'phone'          => 'required|string|max:40',
            'password'       => 'required|string|min:6',
            'line1'          => 'required|string|max:190',
            'line2'          => 'nullable|string|max:190',
            'city'           => 'required|string|max:80',
            'state'          => 'required|string|max:80',
            'postcode'       => 'required|string|max:20',
            'country'        => 'nullable|string|max:60',
        ], [
            'email.unique' => 'An account with this email already exists. Please sign in.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'error' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $customer = Customer::create([
            'first_name'       => trim($request->input('first_name')),
            'last_name'        => trim($request->input('last_name')),
            'email'            => mb_strtolower(trim($request->input('email'))),
            'phone'            => trim($request->input('phone')),
            'password'         => $request->input('password'),
            'country'          => trim($request->input('country', 'India')),
            'status'           => 'active',
            'marketing_opt_in' => $request->boolean('marketing_opt_in', true),
            'last_login_at'    => now(),
        ]);

        $address = CustomerAddress::create([
            'customer_id' => $customer->id,
            'label'       => 'Home',
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

        Auth::login($customer, true);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Account created successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->full_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'address' => $address,
            ]);
        }

        return redirect()->intended(route('account'))->with('success', 'Welcome to the House of Tōramally.');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $throttleKey = 'login:' . mb_strtolower(trim($request->input('email'))) . '|' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Too many login attempts. Please try again in {$seconds} seconds.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => $msg], 429);
            }
            return back()->withErrors(['email' => $msg])->withInput();
        }

        $customer = Customer::where('email', mb_strtolower(trim($request->input('email'))))->first();

        if (!$customer || !Hash::check($request->input('password'), $customer->password_hash)) {
            RateLimiter::hit($throttleKey, 300);
            $msg = 'The email or password does not match our records.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => $msg], 422);
            }
            return back()->withErrors(['email' => $msg])->withInput();
        }

        RateLimiter::clear($throttleKey);
        $customer->update(['last_login_at' => now()]);
        Auth::login($customer, $request->boolean('remember', true));

        $defaultAddress = $customer->defaultAddress ?? $customer->addresses()->first();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Signed in successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->full_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'address' => $defaultAddress,
            ]);
        }

        return redirect()->intended(route('account'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Signed out.']);
        }

        return redirect()->route('home');
    }

    public function account()
    {
        $customer = Auth::user();
        if (!$customer) {
            return redirect()->route('login');
        }

        $orders = $customer->orders()->with(['items'])->get();
        $addresses = $customer->addresses()->get();

        return view('pages.auth.account', [
            'customer' => $customer,
            'orders' => $orders,
            'addresses' => $addresses,
            'meta' => [
                'title' => 'My Account | Tōramally',
                'description' => 'View your account details, order history and saved delivery addresses.',
                'nav' => '',
            ]
        ]);
    }

    public function status()
    {
        if (Auth::check()) {
            $customer = Auth::user();
            $address = $customer->defaultAddress ?? $customer->addresses()->first();
            return response()->json([
                'logged_in' => true,
                'customer' => [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'name' => $customer->full_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'address' => $address,
            ]);
        }

        return response()->json([
            'logged_in' => false,
        ]);
    }
}
