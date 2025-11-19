<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EmployeeController extends Controller
{
    /** ---------------- VIEW EMPLOYEES ---------------- */
    public function index()
    {
        $clinic = Auth::user();
        $employees = User::where('clinic_id', $clinic->id)
                         ->where('role', 'employee')
                         ->get();

        return view('user.therapist.clinic.employees', compact('employees'));
    }

    /** ---------------- ADD EMPLOYEE ---------------- */
    public function store(Request $request)
    {
        $clinic = Auth::user();

        // Corrected validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Handle profile picture
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Create employee
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'role' => 'employee',
            'clinic_id' => $clinic->id,
            'profile_picture' => $profilePath,
            'password' => bcrypt('password123'), // default password
        ]);

        return back()->with('success', 'Employee added successfully!');
    }

    /** ---------------- EDIT EMPLOYEE (AJAX) ---------------- */
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        return view('user.therapist.clinic.employee_edit', compact('employee'));
    }

    /** ---------------- UPDATE EMPLOYEE ---------------- */
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Update profile picture if uploaded
        if ($request->hasFile('profile_picture')) {
            $employee->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee->update($request->only('name', 'email', 'position'));

        return back()->with('success', 'Employee updated successfully!');
    }

    /** ---------------- DELETE EMPLOYEE ---------------- */
    public function destroy(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Employee deleted successfully!');
    }

    /** ---------------- MANAGE EMPLOYEE SCHEDULE ---------------- */
    public function manageSchedule($id)
    {
        $employee = User::findOrFail($id);
        return view('user.therapist.clinic.employee_schedule', compact('employee'));
    }
}
