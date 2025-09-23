@extends('layouts.clinic_layout')

@section('title', 'Clinic Appointment')

@section('styles')
    <link rel="stylesheet" href="{{ asset('Css/clinic.css') }}">
@endsection

@section('content')
<main class="appointment-main">
    <div class="appointment-content">
        <h2>List of Services</h2>
        <form action="#" method="POST" class="appointment-form">
            @csrf
            <h3 style = "text-align: center">otw</h3>
        </form>
    </div>
</main>
@endsection