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

        // Only enforce for therapists, clinics, and their employees
        if ($user->role === 'therapist' || $user->role === 'clinic' || $user->role === 'employee') {
            $statusProvider = $user;
            
            // If employee, check their clinic's subscription
            if ($user->role === 'employee' && $user->clinic_id) {
                $statusProvider = \App\Models\User::find($user->clinic_id);
            }

            if ($statusProvider && !$statusProvider->canAccessSystem()) {
                $message = 'You must activate your subscription to access HealConnect features and provide your services.';
                
                if ($statusProvider->subscription_status === 'expired') {
                    $message = ($user->role === 'employee') 
                        ? 'Your clinic\'s subscription has expired. Please contact your administrator.'
                        : 'Your subscription has expired. Please renew your subscription to continue using HealConnect features.';
                } elseif ($statusProvider->subscription_status === 'inactive' && $statusProvider->customer_count >= 3) {
                    $message = ($user->role === 'employee')
                        ? 'Your clinic has reached the limit of 3 customers for the trial version. Please contact your administrator.'
                        : 'You have reached the limit of 3 customers for the trial version. Please activate your subscription to accept more customers and continue using the system.';
                }

                return redirect()->route('subscription.required')
                    ->with('error', $message);
            }
        }

        return $next($request);
    }
}
