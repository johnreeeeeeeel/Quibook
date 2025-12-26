<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Flashcraft')</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Vite Assets Alternative -->
    <!--
    <link rel="stylesheet" href="{{ asset('build/assets/app-BOEqnadv.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-tn0RQdqM.css') }}">
    <script src="{{ asset('build/assets/app-YXKCAwTu.js') }}" defer></script>
    -->

    <style>
        body {
            background-color: #F2F0EC;
            font-family: "Quicksand", sans-serif;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
