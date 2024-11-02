<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* CSS for the Logo */
.logo {
    text-align: center;
    margin-top: 20px;
}

.logo h1 {
    font-size: 2.5em;              /* Adjusts the font size */
    font-weight: bold;             /* Makes the text bold */
    color: #112127;                /* Set a color for the text */
    letter-spacing: 2px;           /* Adds spacing between letters */
    font-family: 'Arial', sans-serif;
    transition: color 0.3s ease;   /* Smooth color transition */
}

.logo h1:hover {
    color: #1b4352;                /* Darker color on hover */
}

/* Optional underline effect */
.logo h1::after {
    content: "";
    display: block;
    width: 50px;
    height: 4px;
    background-color: #010403;     /* Same color as the text */
    margin: 8px auto 0;            /* Center underline and add margin */
    transition: width 0.3s ease;   /* Transition for the underline */
}

.logo h1:hover::after {
    width: 80px;                   /* Expands underline on hover */
}

        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="logo">
                <a href="/">
                    <h1>BILL SCAN</h1>
                    {{-- <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 fill-current text-gray-500" alt=""> --}}

                    {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
