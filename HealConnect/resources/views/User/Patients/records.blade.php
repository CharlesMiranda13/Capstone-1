@extends('layouts.patient_layout')

@section('title', 'Medical Records')

@section('content')
<main class="records-main">
    <div class="records-content">
        <h2>Medical Records</h2>
        <table class="records-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Doctor</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <h2 style="text-align: center; margin-top: 20px;">
                    TEKA LANG WALA PA TO
                </h2>
            </tbody>
        </table>
    </div>
</main>
@endsection

