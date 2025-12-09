@extends('layouts.app')

@section('title', $therapist->name . ' - Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="therapist-profile">
    <div class="container">
        <div class="header">
            <h2>{{ $therapist->name }}'s Profile</h2>
        </div>

        {{-- PROFILE CARD --}}
        <section class="profile-card">

            {{-- LEFT: PROFILE INFO --}}
            <div class="profile-left">
                <div class="profile-pic">
                    <img src="{{ $therapist->profile_picture 
                        ? asset('storage/' . $therapist->profile_picture) 
                        : asset('images/logo1.png') }}" 
                        alt="{{ $therapist->name }}">
                </div>

                <h3>{{ $therapist->name }}</h3>

                <div class="role-display">
                    <p class="role">{{ ucfirst($therapist->role_display) }}</p>
                </div>

                <div class="bio-section">
                    <p class="bio">
                        {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                    </p>
                </div>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ $therapist->address ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ $therapist->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $therapist->phone ?? 'Phone not specified' }}</p>
                    <p><i class="fa-solid fa-briefcase"></i>
                        {{ $therapist->experience_years ? round($therapist->experience_years) . ' years experience' : 'Experience not specified' }}
                    </p>

                    @if(str_contains(strtolower($therapist->role_display), 'independent'))
                    <p class="therapist-gender">
                        <i class="fa-solid fa-venus-mars"></i> {{ ucfirst($therapist->gender) ?? 'Not specified' }}
                    </p>
                    @endif
                </div>
                
                <div class ="app"> 
                    <a href="{{ url('/logandsign') }}" class="btn-book">
                        <i class="fa-solid fa-calendar-check"></i> Book Appointment
                    </a>

                    <a href="{{ url('/logandsign') }}" class="btn-book">
                        <i class="fa-solid fa-comments"></i> Message Therapist
                    </a>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="profile-right {{ strtolower($therapist->role) === 'clinic' ? 'three-col' : 'two-col' }}">

                {{-- SPECIALIZATIONS --}}
                <div class="card-section col-1 row-1">
                    <h4><i class="fa-solid fa-user-md"></i> Specializations</h4>
                    @if($therapist->specialization)
                        <ul class="specializations">
                            @foreach(explode(',', $therapist->specialization) as $spec)
                                <li>{{ trim($spec) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>N/A</p>
                    @endif
                </div>

                {{-- AVAILABILITY --}}
                <div class="card-section col-2 row-1">
                    <h4><i class="fa-solid fa-calendar-days"></i> Availability</h4>
                    @if(!empty($therapistAvailability) && count($therapistAvailability) > 0)
                        <ul class="availability">
                            @foreach($therapistAvailability as $slot)
                                <li>
                                    {{-- DATE or DAY --}}
                                    @if(isset($slot['day_of_week']) && is_numeric($slot['day_of_week']))
                                        <span class="date">{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$slot['day_of_week']] }}</span>
                                    @elseif(isset($slot['date']))
                                        <span class="date">{{ \Carbon\Carbon::parse($slot['date'])->format('F j, Y') }}</span>
                                    @else
                                        <span class="date">N/A</span>
                                    @endif

                                    {{-- TIME --}}
                                    @if(isset($slot['start_time']) && isset($slot['end_time']))
                                        <span class="time">
                                            {{ \Carbon\Carbon::parse($slot['start_time'])->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($slot['end_time'])->format('g:i A') }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No availability set.</p>
                    @endif
                </div>

                {{-- SERVICES --}}
                <div class="card-section col-1 row-2">
                    <h4><i class="fa-solid fa-list"></i> Services Offered</h4>
                    @if($servicesList && count($servicesList) > 0)
                        <ul class="services-list">
                            @foreach($servicesList as $service)
                                <li>{{ ucfirst(trim($service)) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No services added yet.</p>
                    @endif
                </div>

                {{-- FEES --}}
                <div class="card-section col-2 row-2">
                    <h4><i class="fa-solid fa-money-bill"></i> Fees</h4>
                    @if(!empty($price))
                        <ul class="fees-list">
                            @foreach($servicesData as $service)
                                <li>{{ $price }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>Fees not specified yet. Please inquire with the therapist.</p>
                    @endif
                </div>

                {{-- EMPLOYEES / STAFF --}}
                @if(strtolower($therapist->role) === 'clinic' || strtolower($therapist->role_display) === 'clinic')
                <div class="card-section col-3 staff-section">
                    <h4><i class="fa-solid fa-users"></i> Our Staff</h4>
                    
                    @if(isset($employees) && $employees->count() > 0)
                    <div class="employees-list">
                        @foreach($employees as $employee)
                        <div class="employee-item">

                            <div class="employee-avatar">
                                <img src="{{ $employee->profile_picture 
                                    ? asset('storage/' . $employee->profile_picture) 
                                    : asset('images/logo1.png') }}"
                                    alt="{{ $employee->name }}">
                            </div>

                            <div class="employee-info">
                                <p class="employee-name">{{ $employee->name }}</p>
                                <p class="employee-position">{{ $employee->position }}</p>

                                @if($employee->gender)
                                <p class="employee-gender">{{ ucfirst($employee->gender) }}</p>
                                @endif
                            </div>

                        </div>
                        @endforeach
                    </div>
                    @else
                        <p>No staff members added yet.</p>
                    @endif
                </div>
                @endif
            </div>
        </section>
    </div>
</main>
@endsection
