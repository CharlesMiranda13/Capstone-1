<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

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
        
        // Fetch Stripe subscription details if available
        if ($user->stripe_subscription_id) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                
                // Retrieve the full subscription
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                
                // Get the subscription item (contains period dates)
                $subscriptionItem = $subscription->items->data[0] ?? null;
                
                // Create a custom object with all needed data
                $subscriptionDetails = (object) [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'current_period_start' => $subscriptionItem->current_period_start ?? null,
                    'current_period_end' => $subscriptionItem->current_period_end ?? null,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'created' => $subscription->created,
                    'currency' => $subscription->currency,
                    'plan_amount' => $subscriptionItem->plan->amount ?? null,
                ];
                
            } catch (\Exception $e) {
                \Log::error('Stripe subscription retrieval error: ' . $e->getMessage());
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
        
        if ($request->status === 'inactive' || $request->status === 'expired') {
            // Optionally cancel Stripe subscription
            if ($user->stripe_subscription_id) {
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                    $subscription->cancel();
                } catch (\Exception $e) {
                    return back()->with('error', 'Failed to cancel Stripe subscription: ' . $e->getMessage());
                }
            }
        }
        
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

        if ($user->stripe_subscription_id) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                $subscription->cancel();
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to cancel Stripe subscription: ' . $e->getMessage());
            }
        }

        $user->subscription_status = 'inactive';
        $user->save();

        return back()->with('success', 'Subscription cancelled successfully!');
    }
}