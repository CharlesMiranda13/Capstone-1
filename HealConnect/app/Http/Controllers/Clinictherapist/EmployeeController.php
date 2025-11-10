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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'role' => 'employee',
            'clinic_id' => $clinic->id,
            'password' => bcrypt('password123'), // default password
        ]);

        return back()->with('success', 'Employee added successfully!');
    }

    /** ---------------- EDIT EMPLOYEE (AJAX) ---------------- */
    public function edit($id)
    {
        $employee = User::findOrFail($id);

        // Return a view snippet for modal
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
        ]);

        $employee->update($request->only('name', 'email', 'position'));

        // If AJAX request, return success message
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Employee updated successfully']);
        }

        return back()->with('success', 'Employee updated successfully!');
    }

    /** ---------------- DELETE EMPLOYEE ---------------- */
    public function destroy(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        // AJAX-friendly response
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Employee deleted successfully!');
    }

    /** ---------------- MANAGE EMPLOYEE SCHEDULE ---------------- */
    public function manageSchedule($id)
    {
        $employee = User::findOrFail($id);

        // Return view snippet for modal
        return view('user.therapist.clinic.employee_schedule', compact('employee'));
    }
}
