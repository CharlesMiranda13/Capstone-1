<h3>Edit Employee</h3>
<form method="POST" action="{{ route('clinic.employees.update', $employee->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="{{ $employee->name }}" required>
    </div>

    <div class="form-group">
        <label>Gender</label>
        <select name="gender" required>
            <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>

    <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" value="{{ $employee->email }}" required>
    </div>

    <div class="form-group">
        <label>Position</label>
        <input type="text" name="position" value="{{ $employee->position }}" required>
    </div>

    <div class="form-group">
        <label>Profile Picture</label>
        <input type="file" name="profile_picture">
        @if($employee->profile_picture)
            <div style="margin-top: 10px;">
                <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                     alt="Current Photo" 
                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
            </div>
        @endif
    </div>

    <button type="submit" class="submit-btn">Update Employee</button>
</form>
