<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = session('admin_user_id');

        if (!$adminId) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'Unauthenticated admin session.'], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Please sign in to access the Atelier Admin Panel.');
        }

        $admin = AdminUser::where('id', $adminId)->where('active', 1)->first();

        if (!$admin) {
            session()->forget(['admin_user_id', 'admin_user_name', 'admin_user_role']);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'error' => 'Admin account inactive or not found.'], 401);
            }
            return redirect()->route('admin.login')->with('error', 'Admin account is not active.');
        }

        view()->share('currentAdmin', $admin);

        return $next($request);
    }
}
