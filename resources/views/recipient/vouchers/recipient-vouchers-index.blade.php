@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header mb-4">
                <h1>My Vouchers</h1>
                <p class="text-muted">All your eVouchers</p>
            </div>

            @if($vouchers->isEmpty())
                <div class="alert alert-info">
                    <p>You don't have any vouchers yet. <a href="{{ route('recipient.browse-food') }}">Browse available food</a></p>
                </div>
            @else
                <div class="vouchers-list">
                    @foreach($vouchers as $voucher)
                        <div class="voucher-card mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="voucher-icon">
                                                <i class="fas fa-gift" style="font-size: 2rem; color: #28a745;"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h5 class="card-title mb-1">{{ $voucher->code }}</h5>
                                            <p class="text-muted small mb-0">Issued {{ $voucher->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="amount-display">
                                                <h4 class="text-success mb-0">£{{ number_format($voucher->amount, 2) }}</h4>
                                                <p class="text-muted small">of £{{ number_format($voucher->original_amount ?? $voucher->amount, 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="expiry-display">
                                                <p class="mb-0">
                                                    <i class="far fa-calendar"></i>
                                                    Exp: {{ $voucher->expiry_date->format('d M Y') }}
                                                </p>
                                                <span class="badge badge-success">{{ $voucher->is_redeemed ? 'Redeemed' : 'Active' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="action-buttons">
                                                <!-- View QR Code Button -->
                                                <button class="btn btn-sm btn-info mr-2" 
                                                        onclick="showQRCode('{{ $voucher->id }}', '{{ $voucher->code }}', '{{ $voucher->amount }}', '{{ $voucher->expiry_date->format('Y-m-d') }}', '{{ auth()->user()->name }}', '{{ auth()->user()->email }}', '{{ auth()->user()->phone ?? 'N/A' }}')"
                                                        title="View QR Code">
                                                    <i class="fas fa-qrcode"></i> View QR
                                                </button>
                                                
                                                <!-- Use Voucher Button -->
                                                <a href="{{ route('recipient.use-voucher', $voucher->id) }}" class="btn btn-sm btn-success" title="Use Voucher">
                                                    <i class="fas fa-check"></i> Use Voucher
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrCodeModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Voucher QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- QR Code Container -->
                <div class="text-center mb-4">
                    <div id="qrCode" style="display: inline-block; padding: 20px; background: white; border: 2px solid #ddd; border-radius: 8px;"></div>
                </div>

                <!-- Voucher Details -->
                <div class="voucher-details">
                    <h6 class="mb-3">Voucher Details</h6>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Voucher Code:</th>
                                <td id="qrVoucherCode" style="font-weight: bold;">-</td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td id="qrVoucherAmount">-</td>
                            </tr>
                            <tr>
                                <th>Expiry Date:</th>
                                <td id="qrVoucherExpiry">-</td>
                            </tr>
                            <tr>
                                <th>Recipient Name:</th>
                                <td id="qrRecipientName">-</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="qrRecipientEmail">-</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td id="qrRecipientPhone">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Instructions -->
                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <strong>How to use:</strong> Show this QR code to the shop staff, or let them scan it with their device. 
                        You can also print this page to have a physical copy.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printQRCode()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-info" onclick="downloadQRCode()">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
function showQRCode(voucherId, voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone) {
    // Clear previous QR code
    document.getElementById('qrCode').innerHTML = '';

    // Prepare QR data
    const qrData = {
        voucher_code: voucherCode,
        recipient_name: recipientName,
        recipient_email: recipientEmail,
        recipient_phone: recipientPhone,
        amount: amount,
        expiry_date: expiryDate
    };

    // Generate QR Code using QRCode.js library
    new QRCode(document.getElementById('qrCode'), {
        text: JSON.stringify(qrData),
        width: 300,
        height: 300,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    // Update voucher details
    document.getElementById('qrVoucherCode').textContent = voucherCode;
    document.getElementById('qrVoucherAmount').textContent = '£' + parseFloat(amount).toFixed(2);
    document.getElementById('qrVoucherExpiry').textContent = expiryDate;
    document.getElementById('qrRecipientName').textContent = recipientName;
    document.getElementById('qrRecipientEmail').textContent = recipientEmail;
    document.getElementById('qrRecipientPhone').textContent = recipientPhone;

    // Show modal
    $('#qrCodeModal').modal('show');
}

function printQRCode() {
    const printWindow = window.open('', '', 'height=600,width=800');
    const qrElement = document.getElementById('qrCode').innerHTML;
    const voucherCode = document.getElementById('qrVoucherCode').textContent;
    const amount = document.getElementById('qrVoucherAmount').textContent;
    const expiryDate = document.getElementById('qrVoucherExpiry').textContent;
    const recipientName = document.getElementById('qrRecipientName').textContent;
    const recipientEmail = document.getElementById('qrRecipientEmail').textContent;
    const recipientPhone = document.getElementById('qrRecipientPhone').textContent;

    const printContent = `
        <!DOCTYPE html>
        <html>
            <head>
                <title>eVoucher - ${voucherCode}</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0; 
                        padding: 20px;
                        background: white;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        border: 2px solid #333;
                        padding: 30px;
                        background: white;
                    }
                    .header { 
                        text-align: center; 
                        margin-bottom: 30px;
                        border-bottom: 2px solid #28a745;
                        padding-bottom: 20px;
                    }
                    .header h1 {
                        margin: 0;
                        color: #28a745;
                        font-size: 24px;
                    }
                    .header p {
                        margin: 5px 0 0 0;
                        color: #666;
                        font-size: 14px;
                    }
                    .qr-container { 
                        text-align: center; 
                        margin: 30px 0;
                        padding: 20px;
                        background: #f9f9f9;
                        border-radius: 8px;
                    }
                    .qr-container img {
                        max-width: 300px;
                        height: auto;
                    }
                    .details { 
                        margin: 30px 0;
                    }
                    .detail-row { 
                        display: flex; 
                        justify-content: space-between; 
                        padding: 10px 0; 
                        border-bottom: 1px solid #ddd;
                    }
                    .detail-label { 
                        font-weight: bold;
                        width: 40%;
                    }
                    .detail-value {
                        width: 60%;
                        text-align: right;
                    }
                    .footer { 
                        text-align: center; 
                        margin-top: 30px; 
                        padding-top: 20px;
                        border-top: 2px solid #28a745;
                        font-size: 12px; 
                        color: #666;
                    }
                    .instructions {
                        background: #e7f3ff;
                        border-left: 4px solid #2196F3;
                        padding: 15px;
                        margin: 20px 0;
                        font-size: 13px;
                    }
                    @media print {
                        body { margin: 0; padding: 0; }
                        .container { border: none; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>eVoucher</h1>
                        <p>Food Support Platform</p>
                    </div>
                    
                    <div class="qr-container">
                        ${qrElement}
                    </div>

                    <div class="details">
                        <div class="detail-row">
                            <span class="detail-label">Voucher Code:</span>
                            <span class="detail-value"><strong>${voucherCode}</strong></span>
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
                            <span class="detail-value"><strong>${amount}</strong></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Expiry Date:</span>
                            <span class="detail-value">${expiryDate}</span>
                        </div>
                    </div>

                    <div class="instructions">
                        <strong>How to use this voucher:</strong><br>
                        1. Present this QR code to the shop staff<br>
                        2. They will scan the code or enter the voucher code manually<br>
                        3. Select the food item you want to collect<br>
                        4. Complete the redemption
                    </div>

                    <div class="footer">
                        <p>This voucher is valid until ${expiryDate}</p>
                        <p>For support, contact the eVoucher platform</p>
                    </div>
                </div>
            </body>
        </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
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
</script>

<style>
.voucher-card {
    transition: all 0.3s ease;
}

.voucher-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.voucher-icon {
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.action-buttons .btn {
    white-space: nowrap;
    flex: 1;
    min-width: 100px;
}

@media (max-width: 768px) {
    .voucher-card .row {
        flex-direction: column;
    }
    
    .voucher-card .col-md-2,
    .voucher-card .col-md-4 {
        margin-bottom: 10px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
}
</style>
@endsection
