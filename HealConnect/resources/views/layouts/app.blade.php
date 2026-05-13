<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HealConnect')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pricing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aboutus.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tts.css') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
</head>
<body class="@yield('body-class')">
    @yield('loading')
    
    <div id="page-content" class="main-wrapper" style="display:none;">
        @include('header/footer.header')
        <main>
            @if(session('success'))
                <div class="alert alert-success" style="text-align:center; margin:20px auto;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="text-align:center; margin:20px auto;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        @include('header/footer.footer')
    </div>
    <script src="https://meet.jit.si/external_api.js"></script>
    <script src="{{ asset('js/include.js') }}"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
    <script src="{{ asset('js/tts.js') }}"></script>
    <script src="{{ asset('js/modal-system.js')}}"></script>
    @yield('scripts')
</body> 
</html>
