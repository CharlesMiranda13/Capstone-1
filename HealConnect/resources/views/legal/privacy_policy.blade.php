@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>Privacy Policy</h2>
    <div class="legal-content">
        {!! nl2br(e($settings->privacy ?? 'Privacy Policy not set yet.')) !!}
    </div>
</div>
@endsection
