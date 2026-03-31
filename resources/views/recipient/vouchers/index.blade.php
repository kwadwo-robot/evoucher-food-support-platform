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
                <button onclick="showQRCode('{{ $voucher->code }}', '{{ $voucher->remaining_value }}', '{{ $voucher->expiry_date->format('d M Y') }}', '{{ auth()->user()->name }}', '{{ auth()->user()->email }}', '{{ auth()->user()->phone ?? 'N/A' }}')" class="text-xs bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded-lg font-medium">
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
<div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="closeQRCode(event)">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Voucher QR Code</h3>
                <button onclick="closeQRCode()" class="text-gray-400 hover:text-gray-600">
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
                <button onclick="printQRCode()" class="flex-1 bg-blue-600 text-white hover:bg-blue-700 px-3 py-2 rounded-lg font-medium text-sm">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
                <button onclick="downloadQRCode()" class="flex-1 bg-gray-200 text-gray-900 hover:bg-gray-300 px-3 py-2 rounded-lg font-medium text-sm">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button onclick="closeQRCode()" class="flex-1 bg-gray-100 text-gray-900 hover:bg-gray-200 px-3 py-2 rounded-lg font-medium text-sm">
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
let currentQRData = {};

function showQRCode(voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone) {
    // Clear previous QR code
    document.getElementById('qrCode').innerHTML = '';

    // Store QR data for print/download
    currentQRData = {
        code: voucherCode,
        amount: amount,
        expiry: expiryDate,
        recipient: recipientName,
        email: recipientEmail,
        phone: recipientPhone
    };

    // Prepare QR data as JSON
    const qrData = {
        code: voucherCode,
        recipient: recipientName,
        email: recipientEmail,
        phone: recipientPhone,
        amount: amount,
        expiry: expiryDate
    };

    // Generate QR Code
    new QRCode(document.getElementById('qrCode'), {
        text: JSON.stringify(qrData),
        width: 250,
        height: 250,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    // Update details
    document.getElementById('qrVoucherCode').textContent = voucherCode;
    document.getElementById('qrVoucherAmount').textContent = '£' + amount;
    document.getElementById('qrVoucherExpiry').textContent = expiryDate;
    document.getElementById('qrRecipientName').textContent = recipientName;

    // Show modal
    document.getElementById('qrCodeModal').classList.remove('hidden');
}

function closeQRCode(event) {
    if (event && event.target.id !== 'qrCodeModal') return;
    document.getElementById('qrCodeModal').classList.add('hidden');
}

function printQRCode() {
    const qrCanvas = document.querySelector('#qrCode canvas');
    if (!qrCanvas) {
        alert('QR Code not found. Please try again.');
        return;
    }

    // Convert canvas to image
    const qrImage = qrCanvas.toDataURL('image/png');

    const printWindow = window.open('', '', 'height=600,width=800');
    const voucherCode = currentQRData.code;
    const amount = currentQRData.amount;
    const expiryDate = currentQRData.expiry;
    const recipientName = currentQRData.recipient;
    const recipientEmail = currentQRData.email;
    const recipientPhone = currentQRData.phone;

    const printContent = `
        <!DOCTYPE html>
        <html>
            <head>
                <title>eVoucher - ${voucherCode}</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body { 
                        font-family: 'Arial', sans-serif; 
                        background: white;
                        padding: 20px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        border: 3px solid #16a34a;
                        padding: 40px;
                        background: white;
                        border-radius: 8px;
                    }
                    .header { 
                        text-align: center; 
                        margin-bottom: 30px;
                        border-bottom: 3px solid #16a34a;
                        padding-bottom: 20px;
                    }
                    .header h1 {
                        margin: 0;
                        color: #16a34a;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .header p {
                        margin: 5px 0 0 0;
                        color: #666;
                        font-size: 14px;
                    }
                    .qr-container { 
                        text-align: center; 
                        margin: 40px 0;
                        padding: 30px;
                        background: #f9f9f9;
                        border-radius: 8px;
                        border: 2px dashed #ddd;
                    }
                    .qr-container img {
                        max-width: 350px;
                        height: auto;
                        border: 2px solid #000;
                        padding: 10px;
                        background: white;
                    }
                    .details { 
                        margin: 30px 0;
                    }
                    .detail-row { 
                        display: flex; 
                        justify-content: space-between; 
                        padding: 12px 0; 
                        border-bottom: 1px solid #ddd;
                        font-size: 14px;
                    }
                    .detail-label { 
                        font-weight: bold;
                        width: 40%;
                        color: #333;
                    }
                    .detail-value {
                        width: 60%;
                        text-align: right;
                        color: #555;
                    }
                    .detail-value.code {
                        font-family: 'Courier New', monospace;
                        font-weight: bold;
                        font-size: 16px;
                        color: #16a34a;
                    }
                    .detail-value.amount {
                        font-weight: bold;
                        font-size: 16px;
                        color: #16a34a;
                    }
                    .footer { 
                        text-align: center; 
                        margin-top: 30px; 
                        padding-top: 20px;
                        border-top: 3px solid #16a34a;
                        font-size: 12px; 
                        color: #666;
                    }
                    .instructions {
                        background: #e7f3ff;
                        border-left: 5px solid #2196F3;
                        padding: 15px;
                        margin: 20px 0;
                        font-size: 13px;
                        line-height: 1.6;
                    }
                    .instructions strong {
                        color: #1976d2;
                    }
                    @media print {
                        body { margin: 0; padding: 0; }
                        .container { border: none; box-shadow: none; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>🎫 eVoucher</h1>
                        <p>Food Support Platform - Northamptonshire</p>
                    </div>
                    
                    <div class="qr-container">
                        <p style="margin-bottom: 15px; font-weight: bold; color: #333;">Scan this QR Code at the shop</p>
                        <img src="${qrImage}" alt="QR Code" />
                    </div>

                    <div class="details">
                        <div class="detail-row">
                            <span class="detail-label">Voucher Code:</span>
                            <span class="detail-value code">${voucherCode}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Recipient:</span>
                            <span class="detail-value">${recipientName}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">${recipientEmail}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">${recipientPhone}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Amount:</span>
                            <span class="detail-value amount">£${amount}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Expiry Date:</span>
                            <span class="detail-value">${expiryDate}</span>
                        </div>
                    </div>

                    <div class="instructions">
                        <strong>📋 How to use this voucher:</strong><br>
                        1. Visit one of our partner shops<br>
                        2. Show this QR code or voucher code to the staff<br>
                        3. They will scan the code or enter it manually<br>
                        4. Select the food item you want to collect<br>
                        5. Complete the redemption<br><br>
                        <strong>⏰ Important:</strong> This voucher is valid until ${expiryDate}. After this date, it cannot be redeemed.
                    </div>

                    <div class="footer">
                        <p>This is an official eVoucher from the Food Support Platform</p>
                        <p style="margin-top: 10px;">For support or questions, please contact the eVoucher platform</p>
                        <p style="margin-top: 10px; font-size: 11px;">Printed on: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
            </body>
        </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for content to load then print
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

function downloadQRCode() {
    const canvas = document.querySelector('#qrCode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = document.getElementById('qrVoucherCode').textContent + '_QRCode.png';
        link.click();
    } else {
        alert('QR Code not found. Please try again.');
    }
}

// Close modal when pressing Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeQRCode();
    }
});
</script>
@endsection
