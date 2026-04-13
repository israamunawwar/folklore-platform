<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceRedirectCustomer
{
    public function handle(Request $request, Closure $next)
    {
        // إذا كان المستخدم مسجل دخول
        if (Auth::check()) {
            // وإذا كانت رتبته ليست من الرتب الإدارية
            $adminRoles = ['admin', 'moderator', 'publisher'];

            if (!in_array(Auth::user()->role, $adminRoles)) {
                // اطرده فوراً لصفحة الزوار الرئيسية
                return redirect('/');
            }
        }

        return $next($request);
    }
}
