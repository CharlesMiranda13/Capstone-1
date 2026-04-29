<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EmployeeController extends Controller
{
    /** ---------------- VIEW EMPLOYEES ---------------- */
    public function index(Request $request)
    {
        $clinic = Auth::user();

        $query = User::where('clinic_id', $clinic->id)
                     ->where('role', 'employee');

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $employees = $query->paginate(10);

        // Get unique positions for dropdown
        $positions = User::where('clinic_id', $clinic->id)
                         ->where('role', 'employee')
                         ->pluck('position')
                         ->unique()
                         ->sort();

        return view('user.therapist.clinic.employees', compact('employees', 'positions'));
    }

    /** ---------------- ADD EMPLOYEE ---------------- */
    public function store(Request $request)
    {
        $clinic = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'email' => 'required|email|unique:users,email',
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $profilePath = $request->hasFile('profile_picture') 
            ? $request->file('profile_picture')->store('profile_pictures', 'public')
            : null;

        $employeeUser = new User([
            'name' => $request->name,
            'gender' => $request->gender,
            'email' => $request->email,
            'position' => $request->position,
            'clinic_id' => $clinic->id,
            'profile_picture' => $profilePath,
            'password' => bcrypt('password123'),
        ]);
        $employeeUser->role = 'employee';
        $employeeUser->status = 'Active'; // Employees usually start active or pending depending on business logic, I'll set Active since they are created by admin/clinic. Let's look at previous code: it didn't set status, so it was default or null. Let's set it to 'Active' to be safe, or leave it. Actually, previous code didn't set status.
        $employeeUser->save();

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
            'gender' => 'required|in:Male,Female',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'position' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $employee->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee->update($request->only('name','gender' ,'email', 'position'));

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

    /** ---------------- MANAGE EMPLOYEE SCHEDULE ---------------- 
    *public function manageSchedule($id)
    *{
    *    $employee = User::findOrFail($id);
    *    return view('user.therapist.clinic.employee_schedule', compact('employee'));
    *}
    */
}
