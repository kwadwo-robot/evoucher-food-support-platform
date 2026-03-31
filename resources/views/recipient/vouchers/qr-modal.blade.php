@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('recipient.vouchers.index') }}" class="text-green-600 hover:text-green-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Back to Vouchers') }}
            </a>
        </div>

        <!-- QR Code Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-2">{{ __('Voucher QR Code') }}</h1>
            <p class="text-center text-gray-600 mb-8">{{ __('Show this QR code to the shop to redeem your voucher') }}</p>

            <!-- QR Code Image -->
            <div class="flex justify-center mb-8 bg-gray-50 p-6 rounded-lg">
                <div id="qrCodeContainer">
                    <img id="qrImage" 
                         src="{{ route('qr.generate', $voucher->id) }}" 
                         alt="QR Code" 
                         class="w-64 h-64 border-4 border-green-600 rounded-lg"
                         onerror="handleQRError()">
                </div>
            </div>

            <!-- Voucher Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">{{ __('Voucher Details') }}</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Voucher Code') }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $voucher->code }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Amount') }}</p>
                        <p class="text-lg font-bold text-green-600">
                            @if($voucher->amount == 0)
                                {{ __('Free') }}
                            @else
                                £{{ number_format($voucher->amount, 2) }}
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Issued Date') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $voucher->created_at->format('d M Y') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Expiry Date') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $voucher->expiry_date->format('d M Y') }}</p>
                    </div>
                    
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">{{ __('Your Name') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    </div>
                    
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">{{ __('Your Email') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            <div class="bg-green-50 border-l-4 border-green-600 p-4 mb-8">
                <p class="text-green-800 font-semibold">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('This voucher is valid and ready to be redeemed') }}
                </p>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-8">
                <h3 class="font-semibold text-blue-900 mb-2">{{ __('How to Use') }}</h3>
                <ul class="text-blue-800 text-sm space-y-2">
                    <li>✓ {{ __('Show this QR code to the shop staff') }}</li>
                    <li>✓ {{ __('They will scan the code to verify your voucher') }}</li>
                    <li>✓ {{ __('Or you can print this page and show the printed QR code') }}</li>
                    <li>✓ {{ __('The shop will confirm the redemption') }}</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 flex-wrap">
                <button onclick="printQRCode()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 00-2 2v2a2 2 0 002 2h10a2 2 0 002-2v-2a2 2 0 00-2-2h-2m-4-4V9m0 4v6m0-6a2 2 0 110-4 2 2 0 010 4z"></path>
                    </svg>
                    {{ __('Print QR Code') }}
                </button>

                <button onclick="downloadQRCode()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    {{ __('Download QR Code') }}
                </button>

                <button onclick="shareQRCode()" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C9.589 12.938 10 12.052 10 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3c.064 0 .128-.001.192-.003.243.962.807 1.85 1.684 2.342m0 0a9.968 9.968 0 00-5.468-1.384c-5.487 0-9.972 4.5-9.972 10s4.485 10 9.972 10c5.487 0 9.972-4.5 9.972-10 0-.266-.015-.53-.045-.79m0 0a9.964 9.964 0 01-5.514-9.165"></path>
                    </svg>
                    {{ __('Share') }}
                </button>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-600 rounded">
                <p class="text-yellow-800 text-sm">
                    <strong>{{ __('Note:') }}</strong> {{ __('Keep this QR code safe. Do not share it with anyone else. Each voucher can only be redeemed once.') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Stylesheet -->
<style media="print">
    body {
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 100%;
    }
    #printContent {
        display: block !important;
    }
    button, a, .no-print {
        display: none !important;
    }
    .bg-white {
        box-shadow: none;
        border: 1px solid #ccc;
    }
</style>

<script>
    function printQRCode() {
        window.print();
    }

    function downloadQRCode() {
        const link = document.createElement('a');
        link.href = document.getElementById('qrImage').src;
        link.download = 'voucher-{{ $voucher->code }}-qr.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function shareQRCode() {
        const shareData = {
            title: '{{ __("eVoucher QR Code") }}',
            text: `{{ __("Redeem voucher") }} {{ $voucher->code }} - £{{ number_format($voucher->amount, 2) }}`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData).catch(err => console.log('Error sharing:', err));
        } else {
            // Fallback: Copy to clipboard
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('{{ __("Link copied to clipboard!") }}');
            });
        }
    }

    function handleQRError() {
        console.error('Failed to load QR code image');
        document.getElementById('qrCodeContainer').innerHTML = '<p class="text-red-600">{{ __("Failed to generate QR code") }}</p>';
    }
</script>
@endsection
