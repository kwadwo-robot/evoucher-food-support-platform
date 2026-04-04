@extends('layouts.app')

@section('title', 'Service Fee Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Service Fee Transaction #{{ $transaction->id }}</h1>
            <a href="{{ route('admin.service-fees.index') }}" class="text-blue-600 hover:text-blue-900">
                ← Back to Service Fees
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Transaction Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Transaction Details</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Transaction ID</span>
                        <p class="font-semibold text-gray-900">#{{ $transaction->id }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Payout Request ID</span>
                        <p class="font-semibold text-gray-900">
                            <a href="{{ route('admin.payouts.show', $transaction->payout_request_id) }}" class="text-blue-600 hover:text-blue-900">
                                #{{ $transaction->payout_request_id }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Status</span>
                        <p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $transaction->status_badge }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Created Date</span>
                        <p class="font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Shop Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Shop Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Shop Name</span>
                        <p class="font-semibold text-gray-900">{{ $transaction->shop->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Shop Email</span>
                        <p class="font-semibold text-gray-900">{{ $transaction->shop->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Shop ID</span>
                        <p class="font-semibold text-gray-900">#{{ $transaction->shop_user_id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Breakdown -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Fee Breakdown</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Total Amount</div>
                    <div class="text-2xl font-bold text-gray-900">£{{ number_format($transaction->total_amount, 2) }}</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Service Fee %</div>
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($transaction->service_fee_percentage, 2) }}%</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Fee Amount</div>
                    <div class="text-2xl font-bold text-red-600">£{{ number_format($transaction->service_fee_amount, 2) }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Amount After Fee</div>
                    <div class="text-2xl font-bold text-green-600">£{{ number_format($transaction->amount_after_fee, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Payout Details -->
        @if($transaction->payoutRequest)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Related Payout Request</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500">Payout Status</span>
                    <p class="font-semibold text-gray-900">{{ ucfirst($transaction->payoutRequest->status) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Redemption Count</span>
                    <p class="font-semibold text-gray-900">{{ $transaction->payoutRequest->redemption_count }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Payment Reference</span>
                    <p class="font-semibold text-gray-900">{{ $transaction->payoutRequest->payment_reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Paid At</span>
                    <p class="font-semibold text-gray-900">
                        {{ $transaction->payoutRequest->paid_at ? $transaction->payoutRequest->paid_at->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
