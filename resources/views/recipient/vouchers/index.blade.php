@extends('layouts.dashboard')
@section('page-title', 'My Vouchers')
@section('title', 'My Vouchers')
@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('recipient.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Vouchers</h1>
        <p class="text-gray-500 text-sm mt-1">All your eVouchers</p>
    </div>
</div>

<div class="space-y-4">
    @forelse($vouchers as $voucher)
    <div class="bg-white rounded-xl border {{ $voucher->status === 'active' ? 'border-green-200' : 'border-gray-100' }} p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 {{ $voucher->status === 'active' ? 'bg-green-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-ticket-alt {{ $voucher->status === 'active' ? 'text-green-600' : 'text-gray-400' }} text-lg"></i>
                </div>
                <div>
                    <p class="font-mono font-bold text-lg {{ $voucher->status === 'active' ? 'text-green-700' : 'text-gray-500' }}">{{ $voucher->code }}</p>
                    <p class="text-xs text-gray-500">Issued {{ $voucher->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold {{ $voucher->status === 'active' ? 'text-green-600' : 'text-gray-400' }}">£{{ number_format($voucher->remaining_value, 2) }}</p>
                <p class="text-xs text-gray-400">of £{{ number_format($voucher->value, 2) }}</p>
            </div>
        </div>
        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
            <div class="flex items-center gap-3 text-xs text-gray-500">
                <span><i class="fas fa-calendar mr-1"></i>Exp: {{ $voucher->expiry_date->format('d M Y') }}</span>
                <span class="{{ $voucher->status === 'active' ? 'badge-active' : ($voucher->status === 'redeemed' ? 'badge-redeemed' : 'badge-expired') }}">
                    {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
                </span>
            </div>
            @if($voucher->status === 'active')
            <a href="{{ route('recipient.food.browse') }}" class="text-xs bg-green-600 text-white hover:bg-green-700 px-3 py-1.5 rounded-lg font-medium">
                Use Voucher
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-ticket-alt text-5xl mb-3 block"></i>
        <p class="font-medium">No vouchers yet</p>
        <p class="text-sm mt-1">Vouchers will appear here once issued to you</p>
    </div>
    @endforelse
</div>
@endsection
