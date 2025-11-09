<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'YoPrint CSV Uploader')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('axios.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('index.css') }}">
</head>
<body>
    <div class="container">
        <div class="content">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>

