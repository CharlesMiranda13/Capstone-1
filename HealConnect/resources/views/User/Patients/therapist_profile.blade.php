@extends('layouts.patient_layout')

@section('title', 'PT - Profile')

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
                <p class="role">{{ ucfirst($therapist->role_display) }}</p>

                <p class="bio">
                    {{ $therapist->description ?? 'A compassionate and dedicated therapist ready to assist you.' }}
                </p>

                <div class="contact-info">
                    <p><i class="fa-solid fa-location-dot"></i> {{ $therapist->address ?? 'Location not specified' }}</p>
                    <p><i class="fa-solid fa-envelope"></i> {{ $therapist->email }}</p>
                    <p><i class="fa-solid fa-phone"></i> {{ $therapist->phone ?? 'Phone not specified' }}</p>
                    <p><i class="fa-solid fa-briefcase"></i> 
                        {{ $therapist->experience_years ? round($therapist->experience_years) . ' years experience' : 'Experience not specified' }}
                    </p>
                </div>
                
                <div class="app"> 
                    <a href="{{ route('patient.appointments.create', $therapist->id) }}" class="btn-book">
                        <i class="fa-solid fa-calendar-check"></i> Book Appointment
                    </a>

                    <a href="{{ route('messages', ['receiver_id' => $therapist->id]) }}" class="btn-book">
                        <i class="fa-solid fa-comments"></i> Message Therapist
                    </a>

                </div>
            </div>

            {{-- RIGHT: DETAILS --}}
            <div class="profile-right">

                {{-- SPECIALIZATIONS --}}
                <div class="card-section">
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
                <div class="card-section">
                    <h4><i class="fa-solid fa-calendar-days"></i> Availability</h4>
                    @if($therapist->availability && count($therapist->availability) > 0)
                        <ul class="availability">
                            @foreach($therapist->availability as $slot)
                            <li>
                                @if(isset($slot['day_of_week']) && is_numeric($slot['day_of_week']) && $slot['day_of_week'] >= 0 && $slot['day_of_week'] <= 6)
                                    <span class="date">{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$slot['day_of_week']] }}</span>
                                @elseif(isset($slot['date']))
                                    <span class="date">{{ \Carbon\Carbon::parse($slot['date'])->format('F j, Y') }}</span>
                                @else
                                    <span class="date">N/A</span>
                                @endif

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
                <div class="card-section">
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
                <div class="card-section">
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
            </div>
        </section>
    </div>
</main>
@endsection
