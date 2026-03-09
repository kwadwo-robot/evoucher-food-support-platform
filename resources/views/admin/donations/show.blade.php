@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.donations.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Donations
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Donation #{{ $donation->id }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Donation Details Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Donation Details</h2>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Donor Email</label>
                            <p class="text-gray-900 font-medium break-all">{{ $donation->donor_email ?? $donation->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Amount</label>
                            <p class="text-2xl font-bold text-green-600">£{{ number_format($donation->amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Status</label>
                            <div>
                                @if($donation->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Completed
                                    </span>
                                @elseif($donation->status === 'processing')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 1119.414 5.414 1 1 0 11-1.414-1.414A5.002 5.002 0 005.659 5.659V3a1 1 0 01-1-1zm.008 9a1 1 0 011.992 0A5.002 5.002 0 0114.341 14.341v2.101a1 1 0 11-1.992 0v-2.1a7.002 7.002 0 01-6.341-6.341z" clip-rule="evenodd"></path>
                                        </svg>
                                        Processing
                                    </span>
                                @elseif($donation->status === 'failed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Currency</label>
                            <p class="text-gray-900 font-medium">{{ $donation->currency ?? 'GBP' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Date Created</label>
                        <p class="text-gray-900 font-medium">{{ $donation->created_at->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Payment Information</h2>
                </div>
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Stripe Payment ID</label>
                        <p class="text-gray-900 font-mono text-sm break-all bg-gray-50 p-3 rounded border border-gray-200">{{ $donation->stripe_payment_id ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Payment Intent ID</label>
                        <p class="text-gray-900 font-mono text-sm break-all bg-gray-50 p-3 rounded border border-gray-200">{{ $donation->payment_intent_id ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Payment Method ID</label>
                        <p class="text-gray-900 font-mono text-sm break-all bg-gray-50 p-3 rounded border border-gray-200">{{ $donation->payment_method_id ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information Card -->
            @if($donation->notes)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Additional Information</h2>
                </div>
                <div class="px-6 py-6 space-y-4">
                    @php
                        $notes = json_decode($donation->notes, true);
                        if (is_array($notes)) {
                            foreach ($notes as $key => $value) {
                                $label = ucfirst(str_replace('_', ' ', $key));
                                if (is_array($value)) {
                                    $displayValue = json_encode($value);
                                } else {
                                    $displayValue = $value;
                                }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">{{ $label }}</label>
                        <p class="text-gray-900 font-medium break-all">{{ $displayValue }}</p>
                    </div>
                    @php
                            }
                        }
                    @endphp
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-8">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Summary</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Donation ID</label>
                        <p class="text-lg font-bold text-gray-900">#{{ $donation->id }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Amount</label>
                        <p class="text-3xl font-bold text-green-600">£{{ number_format($donation->amount, 2) }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Status</label>
                        <div>
                            @if($donation->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @elseif($donation->status === 'processing')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Processing
                                </span>
                            @elseif($donation->status === 'failed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Failed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($donation->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Date</label>
                        <p class="text-gray-900 font-medium">{{ $donation->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
