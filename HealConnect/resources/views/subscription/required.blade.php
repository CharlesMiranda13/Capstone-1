@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();

    switch ($user->role ?? 'therapist') {
        case 'therapist':
            $layout = 'layouts.therapist';
            break;
        case 'clinic':
            $layout = 'layouts.clinic_layout';
            break;
        default:
            $layout = 'layouts.app';
            break;
    }
@endphp

@extends($layout)

@section('title', 'Subscription Required')

@section('content')
<div class="subscription-warning" style="text-align:center; margin-top:50px;">
    <h2>Your Plan is Inactive</h2>

    @if($user->role === 'clinic')
        <p>You must activate your subscription to continue using Clinic features.</p>
        <a href="{{ route('subscribe.show', 'pro clinic') }}" class="btn btn-primary btn-lg">Choose Plan</a>
    @elseif($user->role === 'therapist')
        <p>You must activate your subscription to continue offering therapy services.</p>
        <a href="{{ route('subscribe.show', 'pro solo') }}" class="btn btn-primary btn-lg">Choose Plan</a>
    @endif
</div>
@endsection
