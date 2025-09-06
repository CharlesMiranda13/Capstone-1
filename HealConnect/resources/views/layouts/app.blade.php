<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HealConnect')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="@yield('body-class')">
    @yield('loading')
    
    <div id="page-content" style="display:none;">
        @include('header/footer.header')

        <main>
            @yield('content')
        </main>

        @include('header/footer.footer')
    </div>

    <script src="{{ asset('js/include.js') }}"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
</body> 
</html>
