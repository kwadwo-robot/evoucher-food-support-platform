// QR Code functionality for recipient vouchers
window.showQRCode = function(voucherCode, amount, expiryDate, recipientName, email, phone) {
    // Clear previous QR code
    document.getElementById('qrCode').innerHTML = '';
    
    // Generate QR code
    const qrcodeContainer = document.getElementById('qrCode');
    const qrCodeData = `${voucherCode}|${amount}|${expiryDate}|${recipientName}`;
    
    // Use QRCode.js library if available, otherwise create a simple text representation
    if (typeof QRCode !== 'undefined') {
        new QRCode(qrcodeContainer, {
            text: qrCodeData,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    } else {
        // Fallback: Display QR code placeholder
        qrcodeContainer.innerHTML = '<div style="width: 200px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc;">QR Code</div>';
    }
    
    // Update voucher details
    document.getElementById('qrVoucherCode').textContent = voucherCode;
    document.getElementById('qrVoucherAmount').textContent = amount;
    document.getElementById('qrVoucherExpiry').textContent = expiryDate;
    document.getElementById('qrRecipientName').textContent = recipientName;
    
    // Show modal
    const modal = document.getElementById('qrCodeModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
};

window.closeQRCode = function() {
    const modal = document.getElementById('qrCodeModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.downloadQRCode = function() {
    const canvas = document.querySelector('#qrCode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = document.getElementById('qrVoucherCode').textContent + '_QRCode.png';
        link.click();
    } else {
        alert('QR Code not found. Please try again.');
    }
};

// Close modal when pressing Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        window.closeQRCode();
    }
});

window.printQRCode = function() {
    const printContent = document.getElementById('qrCodeModal').innerHTML;
    const printWindow = window.open('', '', 'height=400,width=600');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
};

console.log('QR Code script loaded');
