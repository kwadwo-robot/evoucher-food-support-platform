<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2d5016; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .footer { background-color: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .button { display: inline-block; background-color: #2d5016; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .voucher-code { background-color: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; font-family: monospace; font-size: 16px; margin: 15px 0; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th, .details-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .details-table th { background-color: #f5f5f5; font-weight: bold; }
        .section-title { color: #2d5016; font-size: 18px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; }
        .option { background-color: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #2d5016; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 You've received a food voucher!</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $recipientName }},</p>
            
            <p>A food support voucher has been issued to you. You can use this voucher in <strong>two ways</strong>:</p>
            
            <div class="option">
                <h3 style="margin-top: 0;">Option 1: Use Your Voucher Code at Participating Shops (No Registration Required)</h3>
                <p>Simply provide your voucher code to any participating food shop to redeem your voucher immediately.</p>
                <div class="voucher-code">
                    {{ $voucherCode }}
                </div>
            </div>
            
            <div class="option">
                <h3 style="margin-top: 0;">Option 2: Register & Use Online Platform</h3>
                <p>If you'd like to register and manage your voucher on our platform, you can:</p>
                <ol>
                    <li>Click the button below to create your account</li>
                    <li>Login with your email and set a password</li>
                    <li>View your voucher balance and browse available food items</li>
                    <li>Redeem your voucher online</li>
                </ol>
                <center>
                    <a href="{{ $registrationUrl }}" class="button">Register & View Voucher</a>
                </center>
            </div>
            
            <div class="section-title">Voucher Details</div>
            <table class="details-table">
                <tr>
                    <th>Detail</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td><strong>Voucher Code</strong></td>
                    <td>{{ $voucherCode }}</td>
                </tr>
                <tr>
                    <td><strong>Amount</strong></td>
                    <td>£{{ number_format($voucherValue, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Expires</strong></td>
                    <td>{{ $expiryDate->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>Active ✓</td>
                </tr>
            </table>
            
            <div class="section-title">How to Use Your Voucher</div>
            
            <h4>At a Participating Shop:</h4>
            <ol>
                <li>Show your voucher code: <strong>{{ $voucherCode }}</strong></li>
                <li>Select your food items</li>
                <li>The shop will enter your code and deduct the amount from your voucher</li>
                <li>Your remaining balance will be updated</li>
            </ol>
            
            <h4>Online (After Registration):</h4>
            <ol>
                <li>Register using the link above</li>
                <li>Login to your account</li>
                <li>Browse available food items from participating shops</li>
                <li>Add items to your cart</li>
                <li>Checkout and your voucher will be automatically applied</li>
            </ol>
            
            <div class="section-title">Important Information</div>
            <ul>
                <li>⏰ Your voucher is valid until <strong>{{ $expiryDate->format('d F Y') }}</strong></li>
                <li>🏪 Only valid at participating food shops</li>
                <li>💳 Cannot be exchanged for cash</li>
                <li>📱 Keep your voucher code safe</li>
            </ul>
            
            <div class="section-title">Need Help?</div>
            <p>If you have any questions about your voucher or how to use it, please contact us at:</p>
            <ul>
                <li><strong>Email:</strong> support@evoucher.org</li>
                <li><strong>Phone:</strong> +44 (0) 1604 123456</li>
            </ul>
            
            <p>Thank you for using the eVoucher Food Support Programme!</p>
            
            <p>Best regards,<br>
            <strong>The eVoucher Team</strong></p>
        </div>
        
        <div class="footer">
            <p>This email was sent by the eVoucher Food Support Platform. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
