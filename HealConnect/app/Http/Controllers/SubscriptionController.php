<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    protected $plans = [
        'pro solo' => [
            'name' => 'Pro Solo',
            'price' => '₱499 /month',
            'price_amount' => 499,
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
            'price_amount' => 999,
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

        session([
            'selected_plan' => $plan,
            'plan_details' => $this->plans[$plan]
        ]);

        return redirect()->route('payment.show');
    }

    public function showPayment()
    {
        if (!session('selected_plan')) {
            return redirect()->route('pricing.index')
                             ->with('error', 'Please select a plan first.');
        }

        $plan = session('selected_plan');
        $planDetails = session('plan_details');

        return view('subscription.payment', [
            'planKey' => $plan,
            'plan' => $planDetails
        ]);
    }

    public function createCheckoutSession(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }

        $plan = session('selected_plan');
        
        if (!$plan || !array_key_exists($plan, $this->plans)) {
            return redirect()->route('pricing.index')
                             ->with('error', 'Invalid plan selected.');
        }

        $planDetails = $this->plans[$plan];
        $user = Auth::user();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'php',
                        'unit_amount' => $planDetails['price_amount'] * 100,
                        'product_data' => [
                            'name' => $planDetails['name'],
                            'description' => $planDetails['description'],
                        ],
                        'recurring' => [
                            'interval' => 'month',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'plan' => $plan,
                ],
            ]);

            return redirect($checkoutSession->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment setup failed: ' . $e->getMessage());
        }
    }

    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('pricing.index')
                             ->with('error', 'Invalid payment session.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $user = Auth::user();
                $plan = $session->metadata->plan;

                $user->plan = $plan;
                $user->subscription_status = 'active';
                $user->stripe_subscription_id = $session->subscription;
                $user->subscription_started_at = now();
                $user->save();

                session()->forget(['selected_plan', 'plan_details']);

                if ($user->role === 'clinic') {
                    return redirect()->route('clinic.home')
                                     ->with('success', 'Payment successful! You have activated the ' . $this->plans[$plan]['name'] . ' plan!');
                }

                return redirect()->route('therapist.home')
                                 ->with('success', 'Payment successful! You have activated the ' . $this->plans[$plan]['name'] . ' plan!');
            }

            return redirect()->route('pricing.index')
                             ->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            return redirect()->route('pricing.index')
                             ->with('error', 'Payment verification error: ' . $e->getMessage());
        }
    }

    public function paymentCancel()
    {
        return redirect()->route('payment.show')
                         ->with('error', 'Payment was cancelled. Please try again.');
    }
}