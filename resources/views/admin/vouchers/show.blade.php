@extends('layouts.dashboard')
@section('title', 'Voucher Details')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Voucher Details</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-xs text-gray-500 mb-1">Voucher Code</p>
                <p class="font-mono text-2xl font-bold text-green-700">{{ $voucher->code }}</p>
            </div>
            <span class="{{ $voucher->status === 'active' ? 'badge-active' : ($voucher->status === 'redeemed' ? 'badge-redeemed' : 'badge-expired') }} text-sm px-3 py-1">
                {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs text-gray-500">Original Value</p>
                <p class="text-xl font-bold text-gray-900">£{{ number_format($voucher->value, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Remaining Value</p>
                <p class="text-xl font-bold text-green-600">£{{ number_format($voucher->remaining_value, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Issued To</p>
                <p class="font-medium text-gray-900">{{ $voucher->recipient->name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-400">{{ $voucher->recipient->email ?? '' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Expiry Date</p>
                <p class="font-medium text-gray-900">{{ $voucher->expiry_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Issued Date</p>
                <p class="font-medium text-gray-900">{{ $voucher->created_at->format('d M Y') }}</p>
            </div>
        </div>

        @if($voucher->notes)
        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs text-gray-500 mb-1">Notes</p>
            <p class="text-sm text-gray-700">{{ $voucher->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Redemption History -->
    @if($voucher->redemptions->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Redemption History</h2>
        <div class="space-y-3">
            @foreach($voucher->redemptions as $r)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $r->foodListing->item_name ?? 'Food Item' }}</p>
                    <p class="text-xs text-gray-500">{{ $r->redeemed_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">£{{ number_format($r->amount_used, 2) }}</p>
                    <span class="{{ $r->status === 'confirmed' ? 'badge-active' : 'badge-pending' }}">{{ ucfirst($r->status) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
