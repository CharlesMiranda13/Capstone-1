<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class SubscriptionManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['therapist', 'clinic'])
                     ->whereNotNull('plan');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest('subscription_started_at')
                              ->paginate(20);

        // Calculate stats
        $stats = [
            'total' => User::whereIn('role', ['therapist', 'clinic'])->whereNotNull('plan')->count(),
            'active' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'active')->count(),
            'inactive' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'inactive')->count(),
            'expired' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'expired')->count(),
            'pro_solo' => User::where('plan', 'pro solo')->where('subscription_status', 'active')->count(),
            'pro_clinic' => User::where('plan', 'pro clinic')->where('subscription_status', 'active')->count(),
        ];

        return view('user.admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        $subscriptionDetails = null;
        
        // Fetch Paymongo session details if available
        if ($user->stripe_subscription_id) { // Keeping the column name for now
            try {
                $secretKey = config('services.paymongo.secret_key');
                $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                    ->get("https://api.paymongo.com/v1/checkout_sessions/{$user->stripe_subscription_id}");
                
                if ($response->successful()) {
                    $session = $response->json('data');
                    $attributes = $session['attributes'];
                    
                    // Try to find the payment status from payment_intent or payments array
                    $paymentStatus = 'unknown';
                    if (isset($attributes['payment_intent']['attributes']['status'])) {
                        $paymentStatus = $attributes['payment_intent']['attributes']['status'];
                    } elseif (isset($attributes['payment_intent']['data']['attributes']['status'])) {
                        $paymentStatus = $attributes['payment_intent']['data']['attributes']['status'];
                    }
                    
                    if ($paymentStatus !== 'succeeded' && !empty($attributes['payments'])) {
                        foreach ($attributes['payments'] as $payment) {
                            $status = $payment['attributes']['status'] ?? 
                                     ($payment['data']['attributes']['status'] ?? 'unknown');
                            if ($status === 'succeeded') {
                                $paymentStatus = 'succeeded';
                                break;
                            }
                        }
                    }

                    // Create a custom object with all needed data
                    $subscriptionDetails = (object) [
                        'id' => $session['id'],
                        'status' => $paymentStatus,
                        'current_period_start' => $user->subscription_started_at->timestamp ?? null,
                        'current_period_end' => null, 
                        'cancel_at_period_end' => false,
                        'created' => $attributes['created_at'] ?? null,
                        'currency' => 'PHP',
                        'plan_amount' => $attributes['line_items'][0]['amount'] ?? null,
                    ];
                }
                
            } catch (\Exception $e) {
                \Log::error('Paymongo session retrieval error: ' . $e->getMessage());
                $subscriptionDetails = null;
            }
        }

        return view('user.admin.subscriptions.show', compact('user', 'subscriptionDetails'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,expired',
        ]);

        $user = User::findOrFail($id);
        $user->subscription_status = $request->status;
        
        // Paymongo doesn't have a direct 'cancel' for a simple checkout session once paid,
        // unless you use their webhooks and subscription API.
        // For now, we update the local status.
        
        $user->save();

        return back()->with('success', 'Subscription status updated successfully!');
    }

    public function manualActivate(Request $request, $id)
    {
        $request->validate([
            'plan' => 'required|in:pro solo,pro clinic',
        ]);

        $user = User::findOrFail($id);
        $user->plan = $request->plan;
        $user->subscription_status = 'active';
        $user->subscription_started_at = now();
        $user->save();

        return back()->with('success', 'Subscription manually activated!');
    }

    public function cancel($id)
    {
        $user = User::findOrFail($id);

        // Update local status as Paymongo checkout sessions are one-off or handled via webhooks for renewals
        $user->subscription_status = 'inactive';
        $user->save();

        return back()->with('success', 'Subscription cancelled successfully!');
    }
}