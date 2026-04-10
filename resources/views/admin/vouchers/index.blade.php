@extends('layouts.dashboard')
@section('page-title', 'Vouchers')
@section('title', 'Vouchers')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Vouchers</h1>
        <p class="text-gray-500 text-sm mt-1">Manage all issued vouchers</p>
    </div>
    <a href="{{ route('admin.vouchers.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-1"></i> Issue Voucher
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Recipient</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Value</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Expires</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($vouchers as $voucher)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-semibold text-green-700">{{ $voucher->code }}</td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <p class="font-medium text-gray-900">{{ $voucher->recipient->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $voucher->recipient->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900">£{{ number_format($voucher->remaining_value, 2) }}</p>
                        <p class="text-xs text-gray-400">of £{{ number_format($voucher->value, 2) }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $voucher->expiry_date->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $voucher->status === 'active' ? 'badge-active' : ($voucher->status === 'redeemed' ? 'badge-redeemed' : 'badge-expired') }}">
                            {{ ucfirst(str_replace('_', ' ', $voucher->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded">View</a>
                        @if($voucher->status === 'active')
                        <form method="POST" action="{{ route('admin.vouchers.revoke', $voucher) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded ml-1">Revoke</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No vouchers issued yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $vouchers->links() }}</div>
    @endif
</div>
@endsection
