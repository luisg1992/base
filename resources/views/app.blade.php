<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/ico" href="{{ asset('favicon.ico') }}">

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js')}}"></script>
    <script src="{{ asset('assets/js/config.js')}}"></script>

    <!-- Vite CSS and JS (if needed for other assets) -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Aquí es donde Inertia renderiza las vistas Vue -->
    @inertia
    <script src="{{ asset('assets/js/main.js')}}"></script>
</body>
</html>
