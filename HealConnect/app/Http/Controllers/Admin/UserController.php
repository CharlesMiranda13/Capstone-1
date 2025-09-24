<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Show all users
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin'); // exclude admin

   
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }

   
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

        return back()->with('success', 'User has been verified.');
    }

    // Decline user 
    public function decline($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'Unverified'; 
        $user->is_verified_by_admin = false;
        $user->save();

        return back()->with('error', 'User has been declined.');
    }

    // Edit user form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('User.Admin.edit_user', compact('user'));
    }

    // Update user details
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|string',
        ]);

        $user->update($request->all());

        return redirect()->route('admin.manage-users')
            ->with('success', 'User updated successfully.');
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

        return view('User.Admin.admin', compact(
            'totalUsers', 
            'totalPatients', 
            'totalTherapists', 
            'totalClinics',
            'pendingUsers'
    ));
    }
    
}
