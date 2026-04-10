let currentQRData = {};

window.showQRCode = function(voucherCode, amount, expiryDate, recipientName, recipientEmail, recipientPhone) {
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
    };
    // Generate QR code using qrcode.js library
    const qrcodeContainer = document.getElementById('qrCode');
    if (qrcodeContainer) {
        new QRCode(qrcodeContainer, {
            text: JSON.stringify(qrData),
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }
    // Populate modal fields
    document.getElementById('voucherCodeDisplay').textContent = voucherCode;
    document.getElementById('amountDisplay').textContent = amount;
    document.getElementById('expiryDisplay').textContent = expiryDate;
    document.getElementById('recipientDisplay').textContent = recipientName;
    // Show modal
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
};

window.closeQRCode = function() {
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.printQRCode = function() {
    const printWindow = window.open('', '', 'height=400,width=600');
    const qrCodeElement = document.getElementById('qrCode');
    const qrImage = qrCodeElement.querySelector('canvas');
    if (qrImage) {
        const printContent = '<html><head><title>QR Code</title></head><body>';
        printWindow.document.write(printContent);
        printWindow.document.write('<h2>' + currentQRData.code + '</h2>');
        printWindow.document.write(qrImage.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 250);
    } else {
        alert('QR Code not found. Please try again.');
    }
};

window.downloadQRCode = function() {
    const canvas = document.querySelector('#qrCode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = document.getElementById('voucherCodeDisplay').textContent + '_QRCode.png';
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
