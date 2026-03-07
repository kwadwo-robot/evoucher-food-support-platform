<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — eVoucher Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.auth-wrap{display:flex;width:100%;max-width:960px;min-height:580px;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15)}
.auth-left{flex:1;background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f172a 100%);padding:48px 40px;display:flex;flex-direction:column;justify-content:space-between;min-width:340px}
.auth-right{flex:1;background:#fff;padding:48px 40px;display:flex;flex-direction:column;justify-content:center}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:40px}
.logo-icon{width:42px;height:42px;background:#16a34a;border-radius:11px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:19px}
.logo-text{font-size:16px;font-weight:800;color:#fff}
.auth-tagline{font-size:clamp(22px,3vw,30px);font-weight:900;color:#fff;line-height:1.2;margin-bottom:12px}
.auth-sub{font-size:14px;color:rgba(255,255,255,.55);line-height:1.7}
.auth-features{margin-top:32px;display:flex;flex-direction:column;gap:12px}
.auth-feature{display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(255,255,255,.7)}
.auth-feature-icon{width:28px;height:28px;background:rgba(22,163,74,.2);border-radius:7px;display:flex;align-items:center;justify-content:center;color:#4ade80;font-size:12px;flex-shrink:0}
.form-label{display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:5px;letter-spacing:.01em}
.form-input{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;outline:none;transition:border .15s;font-family:'Inter',sans-serif}
.form-input:focus{border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.1)}
.btn-submit{width:100%;padding:13px;background:#16a34a;color:#fff;border:none;border-radius:10px;font-size:14.5px;font-weight:700;cursor:pointer;transition:all .15s;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-submit:hover{background:#15803d;transform:translateY(-1px);box-shadow:0 4px 12px rgba(22,163,74,.3)}
@media(max-width:640px){.auth-left{display:none}.auth-right{padding:32px 24px}.auth-wrap{max-width:440px;border-radius:16px}}
</style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-left">
    <div>
      <div class="logo">
        <div class="logo-icon"><i class="fas fa-leaf"></i></div>
        <div class="logo-text">eVoucher Platform</div>
      </div>
      <div class="auth-tagline">Welcome Back to eVoucher</div>
      <div class="auth-sub">Sign in to access your dashboard and manage your account on the Northamptonshire Food Support Platform.</div>
      <div class="auth-features">
        <div class="auth-feature"><div class="auth-feature-icon"><i class="fas fa-shield-halved"></i></div> Secure role-based access</div>
        <div class="auth-feature"><div class="auth-feature-icon"><i class="fas fa-ticket"></i></div> Manage your vouchers</div>
        <div class="auth-feature"><div class="auth-feature-icon"><i class="fas fa-basket-shopping"></i></div> Browse available food</div>
        <div class="auth-feature"><div class="auth-feature-icon"><i class="fas fa-chart-bar"></i></div> Track your activity</div>
      </div>
    </div>
    <div style="font-size:12px;color:rgba(255,255,255,.25)">Northamptonshire Pilot · BAKUP CIC · © {{ date('Y') }}</div>
  </div>
  <div class="auth-right">
    <div style="max-width:360px;width:100%;margin:0 auto">
      <h1 style="font-size:24px;font-weight:900;color:#0f172a;margin-bottom:6px">Sign In</h1>
      <p style="font-size:13.5px;color:#94a3b8;margin-bottom:28px">Enter your credentials to access your account</p>
      @if($errors->any())
      <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#b91c1c;display:flex;align-items:center;gap:8px">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
      </div>
      @endif
      @if(session('status'))
      <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#15803d;display:flex;align-items:center;gap:8px">
        <i class="fas fa-check-circle"></i> {{ session('status') }}
      </div>
      @endif
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div style="margin-bottom:18px">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="you@example.com" required autofocus>
        </div>
        <div style="margin-bottom:8px">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-input" placeholder="Your password" required>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
          <label style="display:flex;align-items:center;gap:7px;font-size:13px;color:#64748b;cursor:pointer">
            <input type="checkbox" name="remember" style="accent-color:#16a34a"> Remember me
          </label>
          @if(Route::has('password.request'))
          <a href="{{ route('password.request') }}" style="font-size:13px;color:#16a34a;font-weight:600;text-decoration:none">Forgot password?</a>
          @endif
        </div>
        <button type="submit" class="btn-submit">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
      </form>
      <div style="text-align:center;margin-top:24px;font-size:13.5px;color:#94a3b8">
        Don't have an account? <a href="{{ route('register') }}" style="color:#16a34a;font-weight:700;text-decoration:none">Register here</a>
      </div>
      <div style="margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;text-align:center">
        <a href="/" style="font-size:12.5px;color:#94a3b8;text-decoration:none"><i class="fas fa-arrow-left mr-1"></i> Back to Home</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
