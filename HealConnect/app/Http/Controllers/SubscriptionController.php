<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


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
        if (Auth::check()) {
            if (Auth::user()->role === 'therapist') {
                return redirect()->route('subscribe.show', 'pro solo');
            } elseif (Auth::user()->role === 'clinic') {
                return redirect()->route('subscribe.show', 'pro clinic');
            }
        }

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
            $secretKey = config('services.paymongo.secret_key');
            
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'payment_method_types' => ['card', 'gcash', 'grab_pay', 'paymaya'],
                            'line_items' => [[
                                'currency' => 'PHP',
                                'amount' => $planDetails['price_amount'] * 100, // Amount in cents
                                'description' => $planDetails['description'],
                                'name' => $planDetails['name'],
                                'quantity' => 1,
                            ]],
                            'success_url' => route('payment.success') . '?u=' . $user->id,
                            'cancel_url' => route('payment.cancel'),
                            'description' => "Subscription for " . $planDetails['name'],
                            'metadata' => [
                                'user_id' => $user->id,
                                'plan' => $plan
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                $error = $response->json('errors.0.detail') ?? 'Payment gateway error';
                return back()->with('error', 'Payment setup failed: ' . $error);
            }

            $sessionId = $response->json('data.id');
            $checkoutUrl = $response->json('data.attributes.checkout_url');

            // Store the session ID BEFORE redirecting since PayMongo doesn't pass it back
            $user->stripe_subscription_id = $sessionId;
            $user->save();
            
            return redirect($checkoutUrl);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment setup failed: ' . $e->getMessage());
        }
    }

    public function paymentSuccess(Request $request)
    {
        \Log::info('Entering paymentSuccess', ['all' => $request->all()]);
        
        // Try to get user from 'u' parameter if session is lost
        $userId = $request->get('u');
        $user = Auth::user();

        if (!$user && $userId) {
            $user = \App\Models\User::find($userId);
        }

        if (!$user) {
            return redirect()->route('login')
                             ->with('error', 'Could not identify your account. Please log in.');
        }

        $sessionId = $user->stripe_subscription_id;

        if (!$sessionId) {
            return redirect()->route('pricing.index')
                             ->with('error', 'No active payment session found for your account.');
        }

        try {
            $secretKey = config('services.paymongo.secret_key');
            
            $response = Http::withBasicAuth($secretKey, '')
                ->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}");

            if ($response->failed()) {
                \Log::error('PayMongo Session Verification Failed', ['session_id' => $sessionId, 'response' => $response->body()]);
                return redirect()->route('pricing.index')
                                 ->with('error', 'Could not verify payment status with PayMongo.');
            }

            $sessionAttributes = $response->json('data.attributes');
            
            // Log the response for debugging
            \Log::info('PayMongo Checkout Session Response', ['session_id' => $sessionId, 'data' => $sessionAttributes]);

            // Try to find the payment status from payment_intent or payments array
            $paymentStatus = 'unknown';
            
            // Check payment_intent object
            if (isset($sessionAttributes['payment_intent']['attributes']['status'])) {
                $paymentStatus = $sessionAttributes['payment_intent']['attributes']['status'];
            } elseif (isset($sessionAttributes['payment_intent']['data']['attributes']['status'])) {
                $paymentStatus = $sessionAttributes['payment_intent']['data']['attributes']['status'];
            }
            
            // Fallback: check payments array
            if ($paymentStatus !== 'succeeded' && !empty($sessionAttributes['payments'])) {
                foreach ($sessionAttributes['payments'] as $payment) {
                    $status = $payment['attributes']['status'] ?? 
                             ($payment['data']['attributes']['status'] ?? 'unknown');
                    if ($status === 'succeeded') {
                        $paymentStatus = 'succeeded';
                        break;
                    }
                }
            }

            if ($paymentStatus === 'succeeded') {
                // The user is already identified at the start of this function
                
                // Paymongo doesn't have a direct 'plan' metadata in the same way if not passed, 
                // but we can use session or our metadata backup
                $plan = session('selected_plan') ?? ($sessionAttributes['metadata']['plan'] ?? null); 

                if (!$plan) {
                  // Fallback: try to match from description or just use what we have
                  $description = $sessionAttributes['description'] ?? '';
                  $plan = str_contains($description, 'Pro Solo') ? 'pro solo' : 'pro clinic';
                }

                $user->plan = $plan;
                $user->subscription_status = 'active';
                $user->stripe_subscription_id = $sessionId; // Storing the checkout session ID
                $user->subscription_started_at = now();
                $user->save();
                \Log::info('User subscription activated', ['user_id' => $user->id, 'plan' => $plan, 'session_id' => $sessionId]);

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