@extends('layouts.clinic_layout')

@section('title', 'Clinic Appointment')


@section('content')
<main class="appointment-main">
    <div class="appointment-content">
        <h2>Medical Records</h2>
        <form action="#" method="POST" class="appointment-form">
            @csrf
            <h3 style = "text-align: center">otw</h3>
        </form>
    </div>
</main>
@endsection