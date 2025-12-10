@extends('layouts.app')

@section('title', 'Terms & Conditions')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endsection

@section('content')
<div class="legal-container">
    <h2>Terms & Conditions</h2>
    <div class="legal-content">
        {!! nl2br(e($settings->terms ?? 'Terms & Conditions not set yet.')) !!}
    </div>
</div>
@endsection
