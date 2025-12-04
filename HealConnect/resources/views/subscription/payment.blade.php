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
        default:
            $layout = 'layouts.therapist';
            break;
    }
@endphp

@extends($layout)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/subscription.css') }}">
@endsection

@section('title', 'Payment - ' . $plan['name'])

@section('content')
<div class="subscription-container">
    <div class="payment-card">

        <h2 class="page-title">Complete Your Payment</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- PLAN SUMMARY -->
        <div class="plan-box">
            <h3 class="plan-name">{{ $plan['name'] }}</h3>
            <p class="plan-description">{{ $plan['description'] }}</p>

            <h2 class="price">{{ $plan['price'] }}</h2>

            <h5 class="include-title">What's Included:</h5>
            <ul class="feature-list">
                @foreach($plan['features'] as $feature)
                <li>
                    <i class="bi bi-check-circle-fill"></i> {{ $feature }}
                </li>
                @endforeach
            </ul>
        </div>

        <!-- INFO BOX -->
        <div class="info-box">
            <h6><strong>Payment Information:</strong></h6>
            <ul>
                <li>Secure payment via Stripe</li>
                <li>Charged: {{ $plan['price'] }}</li>
                <li>Renews monthly</li>
                <li>Cancel anytime</li>
            </ul>
        </div>

        <!-- CHECKOUT BUTTON -->
        <form action="{{ route('payment.checkout') }}" method="POST">
            @csrf
            <button class="checkout-btn">
                <i class="bi bi-lock-fill"></i> Proceed to Secure Payment
            </button>
        </form>

        <p class="powered">
            <i class="bi bi-shield-check"></i> Powered by <strong>Stripe</strong>
        </p>

        <div class="back-container">
            <a href="{{ route('pricing.index') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Plans
            </a>
        </div>

    </div>
</div>
@endsection
