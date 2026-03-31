<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - eVoucher Platform</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            background-color: #f9fafb;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a4f 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header-logo {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }
        .header-logo span {
            color: #4ade80;
        }
        .header-sub {
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e3a5f;
            margin-bottom: 20px;
        }
        .message {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        .password-box {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
        }
        .password-label {
            font-size: 12px;
            color: #4a7c59;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .password-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a5f;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            word-break: break-all;
        }
        .instructions {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
            font-size: 14px;
            color: #1e40af;
        }
        .instructions strong {
            display: block;
            margin-bottom: 12px;
            font-size: 15px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background-color: #2d6a4f;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            margin: 25px 0;
            text-align: center;
            transition: background-color 0.3s;
        }
        .cta-button:hover {
            background-color: #1e4d3a;
        }
        .security-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 25px 0;
            border-radius: 6px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }
        .security-note strong {
            display: block;
            margin-bottom: 8px;
        }
        .footer {
            border-top: 1px solid #e5e7eb;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #718096;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer strong {
            color: #1e3a5f;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="header-logo">eVoucher <span>Food Support</span></div>
                <div class="header-sub">Northamptonshire Community Food Programme</div>
            </div>
            
            <div class="content">
                <div class="greeting">
                    Hello {{ $user->name }},
                </div>
                <div class="message">
                    You are receiving this email because we received a password reset request for your eVoucher account. 
                    Click the button below to reset your password. This link will expire in 60 minutes.
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ $url }}" class="cta-button">
                        Reset Password
                    </a>
                </div>
                
                <div class="message" style="margin-top: 30px;">
                    Or copy and paste this link in your browser:
                </div>
                <div style="background-color: #f3f4f6; padding: 12px; border-radius: 6px; word-break: break-all; font-size: 12px; color: #4a5568; font-family: monospace;">
                    {{ $url }}
                </div>
                
                <div class="security-note">
                    <strong>⚠️ Security Notice:</strong> If you did not request a password reset, please ignore this email. 
                    Your account will remain secure. If you have any concerns, contact our support team immediately.
                </div>
            </div>
            
            <div class="footer">
                <p><strong>eVoucher Food Support Platform</strong></p>
                <p>Supporting vulnerable families with food assistance</p>
                <p style="margin-top: 15px; font-size: 11px;">
                    This is an automated message. Please do not reply to this email.
                </p>
                <p style="margin-top: 10px; font-size: 11px;">
                    © 2026 eVoucher. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
