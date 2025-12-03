<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    // Define plans once
    protected $plans = [
        'pro solo' => [
            'name' => 'Pro Solo',
            'price' => '₱499 /month',
            'description' => 'For Independent Physical Therapists',
            'features' => [
                'Profile listing in HealConnect',
                'Access to all features',
                'Individual client management tools',
            ],
        ],
        'pro clinic' => [
            'name' => 'Pro Clinic',
            'price' => '₱999 /month',
            'description' => 'For Clinics or Therapy Teams',
            'features' => [
                'Profile listing in HealConnect',
                'Multiple therapist profiles',
                'Access to all features',
                'Team management & scheduling tools',
            ],
        ],
    ];

    // Show pricing page with all plans
    public function index()
    {
        return view('pricing', ['plans' => $this->plans]);
    }

    // Show single plan details
    public function show($plan)
    {
        if (!array_key_exists($plan, $this->plans)) {
            abort(404, 'Plan not found.');
        }

        return view('subscription.show', [
            'planKey' => $plan,
            'plan' => $this->plans[$plan],
        ]);
    }

    // Activate subscription
    public function store(Request $request, $plan)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }

        if (!array_key_exists($plan, $this->plans)) {
            abort(404, 'Plan not found.');
        }

        $user = Auth::user();
        $user->plan = $plan;
        $user->subscription_status = 'active';
        $user->save();

        if ($user->role === 'clinic') {
            return redirect()->route('clinic.home')
                             ->with('success', 'You have activated the ' . ucfirst($plan) . ' plan!');
        }

        return redirect()->route('therapist.home')
                         ->with('success', 'You have activated the ' . ucfirst($plan) . ' plan!');
    }
}
