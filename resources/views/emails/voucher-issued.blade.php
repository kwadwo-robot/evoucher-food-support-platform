<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Food Voucher – eVoucher Food Support</title>
  <style>
    body, table, td, a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
    table, td { mso-table-lspace:0pt; mso-table-rspace:0pt; }
    img { -ms-interpolation-mode:bicubic; border:0; outline:none; text-decoration:none; }
    body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; }
    .wrapper { width:100%; background:#f0f4f8; padding:32px 0; }
    .container { max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
    .header { background:linear-gradient(135deg,#1e3a5f 0%,#2d6a4f 100%); padding:36px 40px 28px; text-align:center; }
    .header-logo { font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-.5px; margin-bottom:4px; }
    .header-logo span { color:#4ade80; }
    .header-sub { font-size:13px; color:rgba(255,255,255,.7); }
    .greeting { padding:32px 40px 0; }
    .greeting h1 { font-size:22px; font-weight:700; color:#1e3a5f; margin:0 0 8px; }
    .greeting p { font-size:15px; color:#4a5568; line-height:1.6; margin:0; }
    .credentials { padding:28px 40px; }
    .credentials-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:20px; margin-bottom:20px; }
    .credentials-box h3 { font-size:14px; font-weight:700; color:#166534; margin:0 0 12px; }
    .credentials-item { margin-bottom:10px; }
    .credentials-label { font-size:12px; color:#4a7c59; font-weight:600; }
    .credentials-value { font-size:14px; color:#1e3a5f; font-weight:700; font-family:monospace; }
    .voucher-wrap { padding:28px 40px; }
    .voucher-card { background:linear-gradient(135deg,#1e3a5f 0%,#2d6a4f 100%); border-radius:16px; padding:32px 28px; position:relative; overflow:hidden; color:#fff; }
    .voucher-platform { font-size:11px; font-weight:600; letter-spacing:1.5px; opacity:.8; }
    .voucher-title { font-size:18px; font-weight:700; margin:8px 0 16px; }
    .voucher-amount { font-size:48px; font-weight:900; line-height:1; }
    .voucher-amount-sub { font-size:12px; opacity:.9; margin-bottom:24px; }
    .voucher-code-label { font-size:11px; opacity:.8; margin-top:20px; }
    .voucher-code { font-size:20px; font-weight:700; letter-spacing:2px; margin:8px 0 20px; font-family:monospace; }
    .voucher-meta { display:flex; justify-content:space-between; font-size:12px; }
    .voucher-meta-item { flex:1; }
    .voucher-meta-item strong { display:block; font-size:11px; opacity:.8; }
    .qr-section { text-align:center; margin-top:24px; padding-top:20px; border-top:1px solid rgba(255,255,255,.2); }
    .qr-label { font-size:11px; opacity:.8; display:block; margin-bottom:12px; }
    .qr-code-img { max-width:120px; height:auto; }
    .info { padding:28px 40px; }
    .info p { font-size:14px; color:#4a5568; line-height:1.6; margin:0 0 12px; }
    .info ul { margin:12px 0; padding-left:20px; }
    .info li { font-size:14px; color:#4a5568; line-height:1.6; margin-bottom:8px; }
    .cta-wrap { padding:0 40px 28px; }
    .cta-btn { display:inline-block; background:#2d6a4f; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-size:14px; font-weight:700; transition:background .3s; }
    .cta-btn:hover { background:#1e4d3a; }
    .divider { height:1px; background:#e2e8f0; margin:0 40px; }
    .footer { padding:28px 40px; text-align:center; font-size:12px; color:#718096; }
    .footer p { margin:0 0 8px; }
    .footer a { color:#2d6a4f; text-decoration:none; }
    .notice { margin:28px 40px 0; background:#fef3c7; border:1px solid #fcd34d; border-radius:10px; padding:14px 18px; font-size:13px; color:#92400e; line-height:1.6; }
    .notice strong { font-weight:700; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="container">
    <div class="header">
      <div class="header-logo">eVoucher <span>Food Support</span></div>
      <div class="header-sub">Northamptonshire Community Food Programme</div>
    </div>
    <div class="greeting">
      <h1>You've received a food voucher! 🎉</h1>
      <p>Hello <strong>{{ $recipientName }}</strong>, a food support voucher has been issued to you. You can use it to purchase food items from participating local shops.</p>
    </div>
    @if($tempPassword)
    <div class="credentials">
      <div class="credentials-box">
        <h3>✅ Your Account Has Been Created</h3>
        <p style="font-size:13px;color:#4a7c59;margin:0 0 12px;">Use these credentials to log in to your eVoucher dashboard:</p>
        <div class="credentials-item">
          <div class="credentials-label">Email Address</div>
          <div class="credentials-value">{{ $recipientEmail }}</div>
        </div>
        <div class="credentials-item">
          <div class="credentials-label">Temporary Password</div>
          <div class="credentials-value">{{ $tempPassword }}</div>
        </div>
        <p style="font-size:12px;color:#4a7c59;margin:12px 0 0;"><strong>Important:</strong> We recommend changing your password after your first login for security.</p>
      </div>
    </div>
    @endif
    <div class="voucher-wrap">
      <div class="voucher-card">
        <div class="voucher-platform">eVoucher Food Support</div>
        <div class="voucher-title">Food Support Voucher</div>
        <div class="voucher-amount">£{{ number_format($voucher->value, 2) }}</div>
        <div class="voucher-amount-sub">Voucher Value</div>
        <div class="voucher-code-label">Voucher Code</div>
        <div class="voucher-code">{{ $voucher->code }}</div>
        <div class="voucher-meta">
          <div class="voucher-meta-item">
            <strong>Expires</strong>
            {{ $voucher->expiry_date->format('d M Y') }}
          </div>
          <div class="voucher-meta-item">
            <strong>Issued By</strong>
            {{ $issuedByName }}
          </div>
          <div class="voucher-meta-item">
            <strong>Status</strong>
            Active
          </div>
        </div>
        @if($qrCodeBase64)
        <div class="qr-section">
          <span class="qr-label">Scan to Redeem</span>
          <img src="{{ $qrCodeBase64 }}" alt="Voucher QR Code" class="qr-code-img" />
        </div>
        @endif
      </div>
    </div>
    @if($voucher->notes)
    <div class="notice">
      <strong>Note from issuer:</strong> {{ $voucher->notes }}
    </div>
    @endif
    <div class="info">
      <p>Here's how to use your voucher:</p>
      <ul>
        <li><strong>Option 1: Direct Redemption</strong> – Show the QR code above or your voucher code to a participating shop.</li>
        <li><strong>Option 2: Online Redemption</strong> – Log in to your eVoucher dashboard using the button below to browse and redeem food items.</li>
        <li>Browse available food items from local shops in your area.</li>
        <li>Your voucher balance will be deducted automatically upon redemption.</li>
      </ul>
      <p>Your voucher is valid until <strong>{{ $voucher->expiry_date->format('d F Y') }}</strong>. Please use it before it expires.</p>
    </div>
    <div class="cta-wrap">
      <a href="{{ $dashboardUrl }}" class="cta-btn">View My Voucher &amp; Browse Food</a>
    </div>
    <div class="divider"></div>
    <div class="footer">
      <p>This email was sent by <strong>eVoucher Food Support Platform</strong></p>
      <p>Northamptonshire Community Food Programme</p>
      <p style="margin-top:8px;">
        If you have any questions, contact us at
        <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
      </p>
      <p style="margin-top:12px;font-size:11px;color:#cbd5e1;">
        Please do not reply directly to this email. This is an automated notification.
      </p>
    </div>
  </div>
</div>
</body>
</html>
