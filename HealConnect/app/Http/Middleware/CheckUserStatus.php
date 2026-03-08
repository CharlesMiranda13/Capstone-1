<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Allow access to the settings page even for expired accounts,
        // so clinics/therapists can upload a new business permit.
        if ($request->is('*/settings') || $request->is('*/settings/*')) {
            return $next($request);
        }

        if (!$user->is_verified_by_admin || $user->status !== 'Active' || ($user->role !== 'admin' && !$user->canAccessSystem())) {
            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
