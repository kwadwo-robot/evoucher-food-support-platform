<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'eVoucher Food Support'))</title>
    <meta name="description" content="eVoucher Food Support Platform - Northamptonshire">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16a34a', 50: '#f0fdf4', 100: '#dcfce7', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534' },
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .btn-primary { @apply bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors; }
        .btn-secondary { @apply bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2 px-4 rounded-lg transition-colors; }
        .btn-danger { @apply bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors; }
        .card { @apply bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6; }
        .form-input { @apply block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm px-3 py-2 border; }
        .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
        .badge-active { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800; }
        .badge-pending { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800; }
        .badge-expired { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800; }
        .badge-redeemed { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800; }
        .stat-card { @apply bg-white rounded-xl shadow-sm border border-gray-100 p-4; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
             <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="eVoucher" style="width:32px;height:32px;object-fit:contain">
            <div style="display:flex;flex-direction:column;line-height:1.2">
                <div style="font-size:9px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em">BAKUP CIC</div>
                <span style="font-size:14px;font-weight:900;color:#0f172a">eVoucher</span>
            </div>
        </a>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('food.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
                    <i class="fas fa-store text-xs"></i> Browse Food
                </a>
                @auth
                <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-green-700">
                    <i class="fas fa-tachometer-alt text-xs"></i> Dashboard
                </a>
                @endauth
            </div>
            <div class="flex items-center gap-2">
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-green-700 px-3 py-2 rounded-lg hover:bg-gray-50">
                        <div class="w-7 h-7 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-700 font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <span class="hidden sm:block max-w-24 truncate">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                        </div>
                        <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-home w-4 text-center text-gray-400"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-green-700 font-medium px-3 py-2">Login</a>
                <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-1.5 px-4 rounded-lg transition-colors">Register</a>
                @endauth
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
    <div x-show="mobileOpen" x-transition class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('food.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50">
            <i class="fas fa-store"></i> Browse Food
        </a>
        @auth
        <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 w-full">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50">
            <i class="fas fa-sign-in-alt"></i> Login
        </a>
        <a href="{{ route('register') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-green-700 hover:bg-green-50">
            <i class="fas fa-user-plus"></i> Register
        </a>
        @endauth
    </div>
</nav>

@if(session('success'))
<div class="max-w-7xl mx-auto px-4 pt-4" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        <i class="fas fa-check-circle text-green-500"></i>
        <span class="text-sm flex-1">{{ session('success') }}</span>
        <button @click="show = false" class="text-green-500"><i class="fas fa-times"></i></button>
    </div>
</div>
@endif
@if(session('error'))
<div class="max-w-7xl mx-auto px-4 pt-4" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span class="text-sm flex-1">{{ session('error') }}</span>
        <button @click="show = false" class="text-red-500"><i class="fas fa-times"></i></button>
    </div>
</div>
@endif
@if(session('warning'))
<div class="max-w-7xl mx-auto px-4 pt-4" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
        <span class="text-sm flex-1">{{ session('warning') }}</span>
        <button @click="show = false" class="text-yellow-500"><i class="fas fa-times"></i></button>
    </div>
</div>
@endif

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
</main>

<!-- Footer removed - using page-specific footers -->

@stack('scripts')
</body>
</html>
