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

    // Decline user
    public function decline($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'declined'; 
        $user->save();

        return back()->with('error', 'User has been declined.');
    }
}