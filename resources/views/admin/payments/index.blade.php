@extends('layouts.dashboard')
@section('page-title', 'Payments')
@section('title', 'Payments & Donations')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payments & Donations</h1>
        <p class="text-gray-500 text-sm mt-1">All donation transactions</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="stat-card">
        <p class="text-xs text-gray-500 mb-1">Total Received</p>
        <p class="text-2xl font-bold text-green-600">£{{ number_format($total_received, 2) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs text-gray-500 mb-1">Completed</p>
        <p class="text-2xl font-bold text-gray-900">{{ $completed_count }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs text-gray-500 mb-1">Pending</p>
        <p class="text-2xl font-bold text-amber-600">{{ $pending_count }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Donor</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $payment->donor->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->donor->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3 font-semibold text-green-600">£{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-4 py-3 text-gray-500 capitalize hidden sm:table-cell">{{ str_replace('_', ' ', $payment->donor->role ?? 'donor') }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $payment->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $payment->status === 'completed' ? 'badge-active' : ($payment->status === 'pending' ? 'badge-pending' : 'badge-expired') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No payments yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
