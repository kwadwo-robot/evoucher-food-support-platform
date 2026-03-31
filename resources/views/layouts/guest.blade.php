<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'eVoucher') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Figtree', sans-serif; }
            .btn-primary { @apply px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600; }
            .form-input { @apply w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500; }
            .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="mb-6">
                <a href="/" class="flex items-center">
                    <x-logo class="w-12 h-12" />
                    <span class="ml-2 text-xl font-bold text-gray-800">eVoucher</span>
                </a>
            </div>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
