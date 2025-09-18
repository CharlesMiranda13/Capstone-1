<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // Show subscription details
    public function show($plan)
    {
        $plans = [
            'basic' => [
                'name' => 'Basic Plan',
                'price' => '₱0', // or monthly fee later
                'features' => [
                    'Profile listing in HealConnect',
                    'Access to all features',
                    'Priority support',
                    'Individual client management tools',
                ],
            ],
            'premium' => [
                'name' => 'Premium Plan',
                'price' => '₱0', // or monthly fee later
                'features' => [
                    'Profile listing in HealConnect',
                    'Multiple therapist profiles',
                    'Access to all features',
                    'Priority support',
                    'Team management & scheduling tools',
                ],
            ],
        ];

        if (!array_key_exists($plan, $plans)) {
            abort(404, 'Plan not found.');
        }

        return view('subscriptions.show', [
            'planKey' => $plan,
            'plan' => $plans[$plan],
        ]);
    }

    // Store subscription choice
    public function store(Request $request, $plan)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }

        $user = Auth::user();
        $user->plan = $plan; 
        $user->save();

        return redirect()->route('dashboard')->with('success', 'You have subscribed to the ' . ucfirst($plan) . ' plan!');
    }
}
