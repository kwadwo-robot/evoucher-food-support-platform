<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome to eVoucher Food Support</title>
  <style>
    body { margin:0; padding:0; background:#f0f4f8; font-family:'Segoe UI',Arial,sans-serif; }
    .wrapper { width:100%; background:#f0f4f8; padding:32px 0; }
    .container { max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
    .header { background:linear-gradient(135deg,#1e3a5f 0%,#2d6a4f 100%); padding:36px 40px 28px; text-align:center; }
    .header-logo { font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-.5px; margin-bottom:4px; }
    .header-logo span { color:#4ade80; }
    .header-sub { font-size:13px; color:rgba(255,255,255,.7); }
    .body { padding:36px 40px; }
    .body h1 { font-size:22px; font-weight:700; color:#1e3a5f; margin:0 0 12px; }
    .body p { font-size:15px; color:#4a5568; line-height:1.7; margin:0 0 14px; }
    .role-badge {
      display:inline-block;
      background:#e0f2fe;
      color:#0369a1;
      border-radius:20px;
      padding:5px 16px;
      font-size:13px;
      font-weight:700;
      margin-bottom:20px;
    }
    .info-box {
      background:#f8fafc;
      border:1px solid #e2e8f0;
      border-radius:10px;
      padding:18px 20px;
      margin-bottom:24px;
    }
    .info-box p { margin:0 0 6px; font-size:13px; color:#64748b; }
    .info-box p:last-child { margin:0; }
    .info-box strong { color:#1e3a5f; }
    .cta-wrap { text-align:center; margin-bottom:28px; }
    .cta-btn {
      display:inline-block;
      background:linear-gradient(135deg,#1e3a5f,#2d6a4f);
      color:#ffffff !important;
      text-decoration:none;
      font-size:15px;
      font-weight:700;
      padding:14px 36px;
      border-radius:10px;
    }
    .notice {
      background:#fffbeb;
      border:1px solid #fde68a;
      border-radius:10px;
      padding:14px 18px;
      font-size:13px;
      color:#92400e;
      line-height:1.6;
      margin-bottom:24px;
    }
    .divider { height:1px; background:#e2e8f0; margin:0 40px; }
    .footer { padding:24px 40px; text-align:center; }
    .footer p { font-size:12px; color:#94a3b8; line-height:1.6; margin:0 0 4px; }
    .footer a { color:#2d6a4f; text-decoration:none; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="container">
    <div class="header">
      <div class="header-logo">eVoucher <span>Food Support</span></div>
      <div class="header-sub">Northamptonshire Community Food Programme</div>
    </div>
    <div class="body">
      <h1>Welcome, {{ $userName }}! 👋</h1>
      <div class="role-badge">{{ $roleLabel }}</div>
      @if($userRole === 'recipient')
      <p>Thank you for registering with the <strong>eVoucher Food Support Platform</strong>. Your account has been created successfully and you can now access vouchers immediately.</p>

      <div class="info-box">
        <p><strong>Name:</strong> {{ $userName }}</p>
        <p><strong>Email:</strong> {{ $userEmail }}</p>
        <p><strong>Account Type:</strong> {{ $roleLabel }}</p>
        <p><strong>Status:</strong> Active</p>
      </div>

      <div class="notice" style="background:#dcfce7;border-color:#86efac;color:#166534">
        <strong>✓ Account Active:</strong> Your account is ready to use. You can now log in and start browsing available vouchers and food listings.
      </div>

      <p>You can log in immediately to access your dashboard and start using the platform. If you have any questions, please contact our support team.</p>
      @else
      <p>Thank you for registering with the <strong>eVoucher Food Support Platform</strong>. Your account has been created and is currently <strong>pending approval</strong> by our admin team.</p>

      <div class="info-box">
        <p><strong>Name:</strong> {{ $userName }}</p>
        <p><strong>Email:</strong> {{ $userEmail }}</p>
        <p><strong>Account Type:</strong> {{ $roleLabel }}</p>
        <p><strong>Status:</strong> Pending Approval</p>
      </div>

      <div class="notice">
        <strong>⏳ Awaiting Approval:</strong> Your account is being reviewed by our admin team. You will receive another email once your account has been approved and you can log in.
      </div>

      <p>Once approved, you will be able to log in and access your dashboard. If you have any questions in the meantime, please contact our support team.</p>
      @endif
    </div>

    <div class="cta-wrap">
      <a href="{{ $loginUrl }}" class="cta-btn">Go to Login Page</a>
    </div>

    <div class="divider"></div>
    <div class="footer">
      <p>This email was sent by <strong>eVoucher Food Support Platform</strong></p>
      <p>Questions? <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
    </div>
  </div>
</div>
</body>
</html>
