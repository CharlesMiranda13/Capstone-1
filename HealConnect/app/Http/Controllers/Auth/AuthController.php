<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Appointment;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.adminlogin');
    }

    // Handle login submission
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['email'] = strtolower($credentials['email']); 

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::guard('web')->user();

            if ($user->role === 'therapist') {
                return redirect()->intended(route('therapist.home'));
            } elseif ($user->role === 'clinic') {
                return redirect()->intended(route('clinic.home'));
            } else {
                return redirect()->intended(route('patient.home'));
            }
        }

        // If both fail
        return back()->withErrors([
            'email' => 'Invalid credentials!',
        ])->withInput();
    }

    public function dashboard()
    {
        return view('user.admin.admin');
    }

    // View Reports
    public function reports()
    {
        // Subscription Stats
        $subscriptionStats = [
            'total' => User::whereIn('role', ['therapist', 'clinic'])->whereNotNull('plan')->count(),
            'active' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'active')->count(),
            'inactive' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'inactive')->count(),
            'expired' => User::whereIn('role', ['therapist', 'clinic'])->where('subscription_status', 'expired')->count(),
            'pro_solo' => User::where('plan', 'pro solo')->where('subscription_status', 'active')->count(),
            'pro_clinic' => User::where('plan', 'pro clinic')->where('subscription_status', 'active')->count(),
        ];

        // User Registration Stats
        $userStats = [
            'total_users' => User::count(),
            'patients' => User::where('role', 'patient')->count(),
            'therapists' => User::where('role', 'therapist')->count(),
            'clinics' => User::where('role', 'clinic')->count(),
            'verified' => User::where('status', 'verified')->count(),
            'pending' => User::where('status', 'pending')->count(),
        ];

        // Recent registrations (last 30 days)
        $recentRegistrations = User::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Appointment Stats
        $appointmentStats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        return view('user.admin.reports', compact('subscriptionStats', 'userStats', 'recentRegistrations', 'appointmentStats'));
    }
}