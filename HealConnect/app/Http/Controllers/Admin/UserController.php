<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::all();
        return view('User.Admin.manage_users', compact('users'));
    }

    // Verify user
    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'verified';
        $user->save();

        return back()->with('success', 'User has been verified.');
    }

    // Decline user (status only, not delete)
    public function decline($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'declined'; 
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
}
