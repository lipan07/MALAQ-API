<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isAdmin()) {
            // Super admin may impersonate app users; allow access so they can use "Leave impersonation"
            if ($request->session()->has('impersonator_id')) {
                $impersonator = User::find($request->session()->get('impersonator_id'));
                if ($impersonator && $impersonator->isSuperAdmin()) {
                    return $next($request);
                }
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'You do not have access to the admin panel.']);
        }

        return $next($request);
    }
}
