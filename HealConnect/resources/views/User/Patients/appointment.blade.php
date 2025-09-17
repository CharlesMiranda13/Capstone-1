@extends('layouts.patient_layout')

@section('title', 'Patient Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('Css/patient.css') }}">
@endsection

@section('content')
<main class="appointment-main">
    <div class="appointment-content">
        <h2>Book an Appointment</h2>
        <form action="#" method="POST" class="appointment-form">
            @csrf
            <h3 style = "text-align: center">otw</h3>
        </form>
    </div>