<h3>Edit Employee</h3>
<form method="POST" action="{{ route('clinic.employees.update', $employee->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="hc-form-row">
        <div class="hc-form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="hc-input" value="{{ $employee->name }}" required>
        </div>
        <div class="hc-form-group">
            <label>Gender</label>
            <select name="gender" class="hc-select" required>
                <option value="Male" {{ $employee->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $employee->gender == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
    </div>

    <div class="hc-form-row">
        <div class="hc-form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="hc-input" value="{{ $employee->email }}" required>
        </div>
        <div class="hc-form-group">
            <label>Position</label>
            <input type="text" name="position" class="hc-input" value="{{ $employee->position }}" required>
        </div>
    </div>

    <div class="hc-form-group">
        <label>Profile Picture</label>
        <input type="file" name="profile_picture" class="hc-file-input">
        @if($employee->profile_picture)
            <div class="current-avatar-preview">
                <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                     alt="Current Photo" 
                     class="patient-avatar-large">
                <span class="muted-text">Current Profile Picture</span>
            </div>
        @endif
    </div>

    <div class="modal-footer-new">
        <button type="submit" class="hc-btn hc-btn-primary hc-btn-block">Update Employee</button>
    </div>
</form>
