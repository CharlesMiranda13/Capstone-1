<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HealConnect')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/service.css') }}">
</head>
<body>
    {{-- Header --}}
    @include('Header/footer.header')

    {{-- Main Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('Header/footer.footer')

    <script src="{{ asset('js/include.js') }}"></script>
</body>
</html>
