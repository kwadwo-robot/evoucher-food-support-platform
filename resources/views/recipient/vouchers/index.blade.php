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
            <div class="flex gap-2">
                <button onclick="showQRCodeWrapper('{{ $voucher->code }}', '{{ $voucher->remaining_value }}', '{{ $voucher->expiry_date->format('d M Y') }}', '{{ auth()->user()->name }}', '{{ auth()->user()->email }}', '{{ auth()->user()->phone ?? 'N/A' }}')" class="text-xs bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded-lg font-medium">
                    <i class="fas fa-qrcode mr-1"></i>View QR
                </button>
                <a href="{{ route('recipient.food.browse') }}" class="text-xs bg-green-600 text-white hover:bg-green-700 px-3 py-1.5 rounded-lg font-medium">
                    Use Voucher
                </a>
            </div>
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

<!-- QR Code Modal -->
<div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="closeQRCodeWrapper(event)">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Voucher QR Code</h3>
                <button onclick="closeQRCodeWrapper()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- QR Code -->
            <div class="bg-gray-50 p-6 rounded-lg mb-4 text-center">
                <div id="qrCode" class="inline-block"></div>
            </div>
            
            <!-- Voucher Details -->
            <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Voucher Code:</span>
                    <span id="qrVoucherCode" class="font-mono font-bold text-gray-900">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span id="qrVoucherAmount" class="font-bold text-green-600">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Expiry:</span>
                    <span id="qrVoucherExpiry" class="text-gray-900">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Recipient:</span>
                    <span id="qrRecipientName" class="text-gray-900">-</span>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4 text-xs text-blue-800">
                <p><strong>How to use:</strong> Show this QR code to the shop staff, or let them scan it with their device.</p>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-2">
                <button onclick="printQRCodeWrapper()" class="flex-1 bg-blue-600 text-white hover:bg-blue-700 px-3 py-2 rounded-lg font-medium text-sm">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
                <button onclick="downloadQRCodeWrapper()" class="flex-1 bg-gray-200 text-gray-900 hover:bg-gray-300 px-3 py-2 rounded-lg font-medium text-sm">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button onclick="closeQRCodeWrapper()" class="flex-1 bg-gray-100 text-gray-900 hover:bg-gray-200 px-3 py-2 rounded-lg font-medium text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden print container -->
<div id="printContainer" style="display: none;"></div>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>
// Ensure wrapper functions are available globally
window.showQRCodeWrapper = function(voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone) {
    if (typeof showQRCode === 'function') {
        showQRCode(voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone);
    } else {
        setTimeout(() => window.showQRCodeWrapper(voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone), 100);
    }
};
window.closeQRCodeWrapper = function() {
    if (typeof closeQRCode === 'function') {
        closeQRCode();
    } else {
        setTimeout(window.closeQRCodeWrapper, 100);
    }
};
window.printQRCodeWrapper = function() {
    if (typeof printQRCode === 'function') {
        printQRCode();
    } else {
        setTimeout(window.printQRCodeWrapper, 100);
    }
};
window.downloadQRCodeWrapper = function() {
    if (typeof downloadQRCode === 'function') {
        downloadQRCode();
    } else {
        setTimeout(window.downloadQRCodeWrapper, 100);
    }
};
</script>
@endsection
