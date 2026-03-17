@props(['variant' => 'dark'])

@php
$isDark = $variant === 'dark';
$bgClass = $isDark ? 'bg-slate-950' : 'bg-white';
$borderClass = $isDark ? 'border-slate-800' : 'border-slate-200';
$textClass = $isDark ? 'text-white' : 'text-slate-900';
$mutedClass = $isDark ? 'text-slate-400' : 'text-slate-600';
$linkClass = $isDark ? 'hover:text-green-400' : 'hover:text-green-600';
@endphp

<footer class="{{ $bgClass }} {{ $borderClass }} border-t">
    <div class="max-w-6xl mx-auto px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="w-9 h-9 object-contain">
                    <span class="text-base font-bold {{ $textClass }}">eVoucher</span>
                </div>
                <p class="text-sm {{ $mutedClass }} leading-relaxed max-w-xs">
                    Connecting near-expiry food with families in need across Northamptonshire. Free to use and community powered.
                </p>
            </div>

            <!-- Platform -->
            <div>
                <h3 class="text-xs font-bold {{ $textClass }} uppercase tracking-wider mb-4 opacity-60">Platform</h3>
                <ul class="space-y-3">
                    <li><a href="{{ url('/food') }}" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Browse Food</a></li>
                    <li><a href="{{ url('/shops') }}" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Browse Shops</a></li>
                    <li><a href="{{ route('register') }}" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Get Started</a></li>
                    <li><a href="{{ route('login') }}" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Sign In</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-xs font-bold {{ $textClass }} uppercase tracking-wider mb-4 opacity-60">Support</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">About Us</a></li>
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Contact</a></li>
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Terms of Use</a></li>
                </ul>
            </div>

            <!-- Community -->
            <div>
                <h3 class="text-xs font-bold {{ $textClass }} uppercase tracking-wider mb-4 opacity-60">Community</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">BAKUP CIC</a></li>

                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Volunteer</a></li>
                    <li><a href="#" class="text-sm {{ $mutedClass }} {{ $linkClass }} transition-colors">Donate</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom -->
        <div class="border-t {{ $borderClass }} pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <div class="{{ $mutedClass }}">© {{ date('Y') }} eVoucher Food Support Platform — Built for BAKUP CIC · Northamptonshire Pilot</div>
            <div class="flex gap-3">

                <span class="px-3 py-1 rounded text-xs font-semibold {{ $isDark ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">BAKUP CIC</span>
                <span class="px-3 py-1 rounded text-xs font-semibold {{ $isDark ? 'bg-slate-800 text-slate-300' : 'bg-slate-100 text-slate-600' }}">Zero Waste</span>
            </div>
        </div>
    </div>
</footer>
