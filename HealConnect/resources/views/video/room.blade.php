<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Call - HealConnect</title>
    <link rel="stylesheet" href="{{ asset('css/video-call.css') }}">
</head>
<body>
    <div id="room-data" 
         data-room="{{ $room }}" 
         data-user="{{ $user->name }}" 
         data-token="{{ $token ?? '' }}" 
         style="display:none;">
    </div>

    <div class="video-container">
        <div class="loading-screen" id="loading-screen">
            <div class="loading-spinner"></div>
            <p>Connecting to call...</p>
        </div>

        <div id="call-frame"></div>
    </div>

    <script src="https://unpkg.com/@daily-co/daily-js"></script>
    <script src="{{ asset('js/video-call.js') }}"></script>
</body>
</html>