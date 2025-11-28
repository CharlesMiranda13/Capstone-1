<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    public function setting()
    {
        $settings = Setting::first();
        return view('user.admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || $admin->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $settings = Setting::first() ?? new Setting();

        // --- PASSWORD UPDATE ---
        if ($request->filled('current_password') || $request->filled('new_password') || $request->filled('new_password_confirmation')) {

            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            // Ensure current password is correct
            if (!Hash::check($request->current_password, $admin->password)) {
                return back()->withInput()->with('error', 'Current password is incorrect.');
            }

            // Update password
            $admin->password = Hash::make($request->new_password);
            $admin->save();

            Auth::guard('admin')->login($admin);

            return back()->with('success', 'Password updated successfully.');
        }

        // --- GENERAL SETTINGS UPDATE ---
        $settings->system_name = $request->input('system_name', $settings->system_name);
        $settings->description = $request->input('description', $settings->description);
        $settings->contact_email = $request->input('contact_email', $settings->contact_email);
        $settings->phone_number = $request->input('phone_number', $settings->phone_number);
        $settings->terms = $request->input('terms', $settings->terms);
        $settings->privacy = $request->input('privacy', $settings->privacy);

        if ($request->hasFile('logo')) {
            $settings->logo = $request->file('logo')->store('public/logo');
        }

        if ($request->hasFile('telehealth_consent')) {
            $settings->telehealth_consent = $request->file('telehealth_consent')->store('public/consent_forms');
        }

        if ($request->hasFile('compliance_docs')) {
            $paths = [];
            foreach ($request->file('compliance_docs') as $file) {
                $paths[] = $file->store('public/compliance_docs');
            }
            $settings->compliance_docs = json_encode($paths);
        }

        $settings->save();

        return back()->with('success', 'Settings updated successfully.');
    }
}
