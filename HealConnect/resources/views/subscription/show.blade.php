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

@section('title', 'Subscribe - ' . $plan['name'])

@section('styles')
<link rel="stylesheet" href="{{ asset('css/subscription.css') }}">
@endsection

@section('content')
<div class="subscription-wrapper">
    <div class="subscription-card">
        <h1 class="subscription-title">{{ $plan['name'] }}</h1>

        <div class="subscription-price">
            {{ $plan['price'] }}
        </div>

        <h4 class="subscription-subtitle">What's Included</h4>

        <ul class="subscription-features">
            @foreach($plan['features'] as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>

        <form action="{{ route('subscribe.store', $planKey) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary subscribe-btn">
                Activate {{ $plan['name'] }}
            </button>
        </form>
    </div>
</div>
@endsection
