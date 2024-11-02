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


}

.logo h6 {
    border: 1px solid black;
    padding: 4px;
    font-size: 1em;              /* Adjusts the font size */
    font-weight: bold;             /* Makes the text bold */
    color: #112127;                /* Set a color for the text */
    letter-spacing: 2px;           /* Adds spacing between letters */
    font-family: 'Arial', sans-serif;
    transition: color 0.3s ease;   /* Smooth color transition */
}

.logo h6:hover {
    color: #1b4352;                /* Darker color on hover */
}



        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 ">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
