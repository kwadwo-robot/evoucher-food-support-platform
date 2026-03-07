<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Pending — eVoucher Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>*{box-sizing:border-box;margin:0;padding:0}body{font-family:'Inter',sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}</style>
</head>
<body>
<div style="max-width:480px;width:100%;background:#fff;border-radius:20px;padding:48px 40px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.1);border:1px solid #e2e8f0">
  <div style="width:72px;height:72px;background:#fef9c3;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:32px;margin:0 auto 24px">⏳</div>
  <h1 style="font-size:24px;font-weight:900;color:#0f172a;margin-bottom:10px">Account Pending Approval</h1>
  <p style="font-size:14px;color:#64748b;line-height:1.7;margin-bottom:28px">
    Thank you for registering, <strong>{{ auth()->user()->name }}</strong>! Your account is currently under review. An administrator will approve your account shortly.
  </p>
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;margin-bottom:24px;font-size:13px;color:#15803d;text-align:left">
    <div style="font-weight:700;margin-bottom:6px"><i class="fas fa-info-circle mr-1"></i> What happens next?</div>
    <div>1. Admin reviews your registration</div>
    <div>2. You receive an email confirmation</div>
    <div>3. You can then access your dashboard</div>
  </div>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" style="width:100%;padding:12px;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">
      <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
    </button>
  </form>
  <div style="margin-top:16px">
    <a href="/" style="font-size:13px;color:#94a3b8;text-decoration:none"><i class="fas fa-home mr-1"></i> Back to Home</a>
  </div>
</div>
</body>
</html>
