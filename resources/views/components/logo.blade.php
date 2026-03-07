@props(['color' => 'dark', 'size' => 'md'])

@php
$sizeClasses = match($size) {
    'sm' => 'w-8 h-8',
    'md' => 'w-10 h-10',
    'lg' => 'w-12 h-12',
    default => 'w-10 h-10',
};

$textColor = $color === 'light' ? 'text-white' : 'text-slate-900';
$subTextColor = $color === 'light' ? 'text-gray-300' : 'text-gray-600';
@endphp

<div class="flex items-center gap-2">
    <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="{{ $sizeClasses }} object-contain">
    <div class="flex flex-col leading-tight">
        <span class="text-xs font-semibold {{ $subTextColor }}">BAKUP CIC</span>
        <span class="text-sm font-bold {{ $textColor }}">eVoucher</span>
    </div>
</div>
