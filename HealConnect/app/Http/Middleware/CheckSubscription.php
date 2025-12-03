<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // If not logged in, continue
        if (!$user) {
            return $next($request);
        }
        // therapist subscribe
        if (($user->role === 'therapist' || $user->role === 'clinic')
            && $user->subscription_status !== 'active') 
        {
            return redirect()->route('subscription.required')
                ->with('error', 'You must activate your subscription to access HealConnect features and Provide your services.');
        }
        return $next($request);
    }
}
