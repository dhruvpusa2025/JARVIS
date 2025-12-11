<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JARVIS')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
</head>

<body>
    @include('partials.sidebar')
    @include('partials.mobile-nav')

    <div class="main-content">
        <header class="header">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <h1>@yield('header_title')</h1>
            @yield('header_action')
        </header>

        @yield('content')
    </div>

    <!-- JARVIS API Client -->
    <script src="{{ asset('js/jarvis.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>