<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApprovedMail;


class UserController extends Controller
{
    // Show all users

    public function index(Request $request)
    {
        $query = User::query();

        // Only clinics and independent therapists
        $query->whereIn('role', ['patient','clinic', 'therapist'])
          ->whereNull('clinic_id'); // exclude clinic employees

        // Apply dropdown role filter
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return view('User.Admin.manage_users', compact('users'));
    }


    // Verify user
    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Active';
        $user->is_verified_by_admin = true; 
        $user->save();

        try{
            Mail::to($user->email)->send(new AccountApprovedMail($user));
        } catch (\Exception $e) {

            \Log::error('Failed to send account approval email: ' . $e->getMessage());
        }

        return back()->with('success', 'User has been approved and notified via email.');
    }

    // Decline user 
    public function decline(Request $request, $id)
    {
        $user = User::findOrFail($id);

    
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
    
        $user->status = 'Declined';
        $user->is_verified_by_admin = false;
        $user->save();
   
        try {
            Mail::to($user->email)->send(new \App\Mail\AccountDeniedMail($user, $request->reason));
        } catch (\Exception $e) {
            \Log::error('Failed to send account decline email: ' . $e->getMessage());
        }

        return back()->with('error', 'User has been declined and notified via email.');
    }

    // Permanently delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.manage-users')
            ->with('success', 'User deleted successfully.');
    }


    // View reports page
    public function dashboard()
    {
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalPatients = User::where('role', 'patient')->count();
        $totalTherapists = User::where('role', 'therapist')->count();
        $totalClinics = User::where('role', 'clinic')->count();
        $pendingUsers = User::where('status', 'pending')->count();

        // Monthly user growth
        $userData = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('role', '!=', 'admin')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyCounts = [];

        foreach (range(1, 12) as $m) {
            $monthString = date('Y-m', mktime(0, 0, 0, $m, 1));
            $record = $userData->firstWhere('month', $monthString);
            $monthlyCounts[] = $record ? $record->count : 0;
        }

        $monthlyData = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'values' => $monthlyCounts
        ];

        // User Role Distribution
        $userRoleData = [
            'patients' => $totalPatients,
            'therapists' => $totalTherapists,
            'clinics' => $totalClinics,
        ];

        return view('User.Admin.admin', compact(
            'totalUsers',
            'totalPatients',
            'totalTherapists',
            'totalClinics',
            'pendingUsers',
            'monthlyData',
            'userRoleData'
        ));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $employees = [];

        if ($user->role === 'clinic') {
            $employees = User::where('clinic_id', $user->id)->get();
        }
        $validIds = $user->valid_id_path ? json_decode($user->valid_id_path, true) : null;

        return view('User.Admin.user_details', compact('user', 'employees'));
    }
    
}
