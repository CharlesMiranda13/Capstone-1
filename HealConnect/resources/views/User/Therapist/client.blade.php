@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    switch ($user->role) {
        case 'therapist':
            $layout = 'layouts.therapist';
            break;
        case 'clinic':
            $layout = 'layouts.clinic_layout';
            break;
        default;
            $layout = 'layouts.therapist';
            break;
    }
@endphp

@extends($layout)

@section('title', 'Clients')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/client.css') }}">
<link rel="stylesheet" href="{{ asset('css/patient-profile.css') }}">
@endsection



@section('content')
<main class="therapist-patients">
    <div class="w-100">
        <div class="patients-header page-header-row">
            <h2 class="page-title-new">My Patients</h2>
            <p class="page-subtitle">Manage patient records and view health profiles</p>
        </div>
        
        <div class="search-filter-new">
            <form method="GET" action="{{ route($user->role === 'clinic' ? 'clinic.clients' : 'therapist.clients') }}" class="search-filter-form-new">
                <div class="search-input-wrapper">
                    <i class="fa fa-search search-icon-inside"></i>
                    <input type="text" name="search" placeholder="Search patients..."
                        value="{{ request('search') }}" class="search-input-new">
                </div>

                <select name="gender" class="filter-select-new" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>

                <button type="submit" class="hc-btn hc-btn-primary hc-btn-search">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>

        @if($patients->count() > 0)
        <div class="hc-table-container hc-table-responsive">
            <table class="hc-table" id="tableView">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td class="patient-info-cell">
                                <div class="patient-avatar-name">
                                    @if($patient->profile_picture)
                                        <img src="{{ asset('storage/' . $patient->profile_picture) }}" alt="{{ $patient->name }}" class="patient-avatar">
                                    @else
                                        <div class="patient-avatar-init">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="patient-meta">
                                        <span class="patient-name-main">{{ $patient->name }}</span>
                                        <span class="patient-gender-tag">{{ ucfirst($patient->gender ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="email-text">{{ $patient->email }}</span></td>
                            <td><span class="phone-text">{{ $patient->phone ?? '—' }}</span></td>
                            <td class="text-center">
                                <div class="hc-dropdown">
                                    <button class="hc-dropdown-toggle">Actions</button>
                                    <div class="hc-dropdown-menu">
                                        <a href="{{ route('therapist.patients_records', ['patientId' => $patient->id]) }}" class="hc-dropdown-item">
                                            <i class="fa fa-folder-open"></i> Medical Records
                                        </a>
                                        <button class="hc-dropdown-item openModalBtn" 
                                                data-link="{{ route('therapist.patients.profile', $patient->id) }}?embed=1">
                                           <i class="fa fa-user-circle"></i> View Profile
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $patients->withQueryString()->links('pagination.custom') }}
        </div>
        @else
            <p>You currently have no patients.</p>
        @endif
    </div>
</main>

{{-- Patient Profile Modal - OUTSIDE the main container --}}
<div id="patientModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modal-body"></div>
    </div>
</div>

@endsection