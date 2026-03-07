<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>eVoucher Food Support Platform — Northamptonshire</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;color:#0f172a}
.hero{background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%);min-height:100vh;display:flex;flex-direction:column}
.nav{display:flex;align-items:center;padding:20px 40px;gap:20px;border-bottom:1px solid rgba(255,255,255,.07)}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-icon{width:40px;height:40px;background:#16a34a;border-radius:11px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px}
.nav-logo-text{font-size:16px;font-weight:800;color:#fff}
.nav-links{display:flex;align-items:center;gap:6px;margin-left:auto}
.nav-link{padding:8px 16px;border-radius:8px;font-size:13.5px;font-weight:600;text-decoration:none;transition:all .15s}
.nav-link-ghost{color:rgba(255,255,255,.7)}
.nav-link-ghost:hover{color:#fff;background:rgba(255,255,255,.08)}
.nav-link-primary{background:#16a34a;color:#fff}
.nav-link-primary:hover{background:#15803d}
.hero-content{flex:1;display:flex;align-items:center;justify-content:center;padding:60px 40px;text-align:center}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(22,163,74,.15);border:1px solid rgba(22,163,74,.3);color:#4ade80;padding:6px 16px;border-radius:20px;font-size:12.5px;font-weight:600;margin-bottom:24px;letter-spacing:.04em}
.hero-title{font-size:clamp(36px,6vw,72px);font-weight:900;color:#fff;line-height:1.1;margin-bottom:20px}
.hero-title span{color:#4ade80}
.hero-sub{font-size:clamp(15px,2vw,19px);color:rgba(255,255,255,.65);max-width:600px;margin:0 auto 36px;line-height:1.7}
.hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn-hero-primary{padding:14px 32px;background:#16a34a;color:#fff;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
.btn-hero-primary:hover{background:#15803d;transform:translateY(-1px);box-shadow:0 8px 24px rgba(22,163,74,.4)}
.btn-hero-secondary{padding:14px 32px;background:rgba(255,255,255,.1);color:#fff;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(4px)}
.btn-hero-secondary:hover{background:rgba(255,255,255,.15)}
.stats-bar{display:flex;justify-content:center;gap:48px;padding:28px 40px;border-top:1px solid rgba(255,255,255,.07);flex-wrap:wrap}
.stat-item{text-align:center}
.stat-num{font-size:28px;font-weight:900;color:#4ade80}
.stat-lbl{font-size:12px;color:rgba(255,255,255,.5);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:2px}
.section{padding:80px 40px}
.section-title{font-size:clamp(28px,4vw,42px);font-weight:900;color:#0f172a;text-align:center;margin-bottom:12px}
.section-sub{font-size:16px;color:#64748b;text-align:center;max-width:560px;margin:0 auto 56px;line-height:1.7}
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:1100px;margin:0 auto}
.feature-card{padding:28px;border-radius:16px;border:1px solid #e2e8f0;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:all .2s}
.feature-card:hover{box-shadow:0 12px 32px rgba(0,0,0,.1);transform:translateY(-2px)}
.feature-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px}
.feature-title{font-size:17px;font-weight:800;color:#0f172a;margin-bottom:8px}
.feature-desc{font-size:13.5px;color:#64748b;line-height:1.7}
.roles-section{background:#f8fafc;padding:80px 40px}
.roles-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;max-width:1000px;margin:0 auto}
.role-card{background:#fff;border-radius:14px;padding:24px;border:1px solid #e2e8f0;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.04);transition:all .2s}
.role-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);transform:translateY(-2px)}
.role-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px}
.role-name{font-size:15px;font-weight:800;color:#0f172a;margin-bottom:6px}
.role-desc{font-size:12.5px;color:#64748b;line-height:1.6}
.cta-section{background:linear-gradient(135deg,#0f172a,#1e293b);padding:80px 40px;text-align:center}
.footer{background:#0f172a;padding:32px 40px;text-align:center;color:rgba(255,255,255,.4);font-size:13px;border-top:1px solid rgba(255,255,255,.07)}
@media(max-width:640px){
  .nav{padding:16px 20px}
  .hero-content{padding:40px 20px}
  .stats-bar{gap:24px;padding:20px}
  .section{padding:60px 20px}
  .roles-section{padding:60px 20px}
  .cta-section{padding:60px 20px}
  .footer{padding:24px 20px}
  .nav-links .nav-link-ghost{display:none}
}
</style>
</head>
<body>
<div class="hero">
  <!-- Nav -->
  <nav class="nav">
    <a href="/" class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-leaf"></i></div>
      <div class="nav-logo-text">eVoucher</div>
    </a>
    <div class="nav-links">
      <a href="{{ url('/food') }}" class="nav-link nav-link-ghost"><i class="fas fa-basket-shopping mr-1"></i> {{ __('app.nav_browse_food') }}</a>
      <!-- Language Switcher -->
      <div class="lang-switcher" style="position:relative;display:inline-block">
        <button class="nav-link nav-link-ghost" onclick="document.getElementById('lang-menu').classList.toggle('open')" style="display:flex;align-items:center;gap:6px;cursor:pointer;border:none;background:none">
          <i class="fas fa-globe"></i>
          <span style="font-size:12px;font-weight:700;text-transform:uppercase">{{ app()->getLocale() }}</span>
          <i class="fas fa-chevron-down" style="font-size:10px"></i>
        </button>
        <div id="lang-menu" class="lang-dropdown" style="display:none;position:absolute;right:0;top:110%;background:#1e293b;border:1px solid #334155;border-radius:10px;min-width:160px;z-index:999;box-shadow:0 8px 24px rgba(0,0,0,.4)">
          <a href="{{ route('lang.switch', 'en') }}" class="lang-opt {{ app()->getLocale()==='en'?'active':'' }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#e2e8f0;text-decoration:none;font-size:13px;border-radius:8px 8px 0 0">🇬🇧 English</a>
          <a href="{{ route('lang.switch', 'ar') }}" class="lang-opt {{ app()->getLocale()==='ar'?'active':'' }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#e2e8f0;text-decoration:none;font-size:13px">🇸🇦 العربية</a>
          <a href="{{ route('lang.switch', 'ro') }}" class="lang-opt {{ app()->getLocale()==='ro'?'active':'' }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#e2e8f0;text-decoration:none;font-size:13px">🇷🇴 Română</a>
          <a href="{{ route('lang.switch', 'pl') }}" class="lang-opt {{ app()->getLocale()==='pl'?'active':'' }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#e2e8f0;text-decoration:none;font-size:13px;border-radius:0 0 8px 8px">🇵🇱 Polski</a>
        </div>
      </div>
      @auth
      <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="nav-link nav-link-primary">
        <i class="fas fa-tachometer-alt mr-1"></i> {{ __('app.nav_dashboard') }}
      </a>
      @else
      <a href="{{ route('login') }}" class="nav-link nav-link-ghost">{{ __('app.nav_sign_in') }}</a>
      <a href="{{ route('register') }}" class="nav-link nav-link-primary">{{ __('app.home_cta_start') }}</a>
      @endauth
    </div>
  </nav>
  <!-- Hero -->
  <div class="hero-content">
    <div>
      <div class="hero-badge"><i class="fas fa-map-marker-alt"></i> Northamptonshire Pilot Programme</div>
      <h1 class="hero-title">Connecting <span>Food</span><br>with People in Need</h1>
      <p class="hero-sub">Local shops list near-expiry food. Recipients use vouchers to collect it. Organisations fund the vouchers. Together we reduce food waste and food poverty.</p>
      <div class="hero-btns">
        <a href="{{ route('register') }}" class="btn-hero-primary"><i class="fas fa-rocket"></i> Get Started Free</a>
        <a href="{{ url('/food') }}" class="btn-hero-secondary"><i class="fas fa-basket-shopping"></i> Browse Food</a>
      </div>
    </div>
  </div>
  <!-- Stats Bar -->
  <div class="stats-bar">
    <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Free to Use</div></div>
    <div class="stat-item"><div class="stat-num">0</div><div class="stat-lbl">Food Wasted</div></div>
    <div class="stat-item"><div class="stat-num">6</div><div class="stat-lbl">User Roles</div></div>
    <div class="stat-item"><div class="stat-num">NHS</div><div class="stat-lbl">Community Backed</div></div>
  </div>
</div>

<!-- How It Works -->
<div class="section" style="background:#fff">
  <div class="section-title">How It Works</div>
  <div class="section-sub">A simple three-step process that connects local shops, recipients, and organisations</div>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-store"></i></div>
      <div class="feature-title">1. Shops List Food</div>
      <div class="feature-desc">Local shops and retailers list food items that are close to their expiry date, including photos, collection details, and availability.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-ticket"></i></div>
      <div class="feature-title">2. Recipients Get Vouchers</div>
      <div class="feature-desc">Approved recipients receive digital vouchers by email and through the platform, which they can use to redeem food items.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-hand-holding-heart"></i></div>
      <div class="feature-title">3. Organisations Fund It</div>
      <div class="feature-desc">VCFSE organisations, schools, and care organisations donate funds through Stripe to keep the voucher programme running.</div>
    </div>
  </div>
</div>

<!-- User Roles -->
<div class="roles-section">
  <div class="section-title">Who Uses eVoucher?</div>
  <div class="section-sub">The platform serves six distinct user types, each with their own dedicated dashboard</div>
  <div class="roles-grid">
    <div class="role-card">
      <div class="role-icon" style="background:#fef2f2;color:#ef4444"><i class="fas fa-shield-halved"></i></div>
      <div class="role-name">Super Admin</div>
      <div class="role-desc">Full platform control, user management, and system configuration</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-user-shield"></i></div>
      <div class="role-name">Admin</div>
      <div class="role-desc">Manage users, issue vouchers, track donations, and view reports</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#fff7ed;color:#f97316"><i class="fas fa-store"></i></div>
      <div class="role-name">Local Shops</div>
      <div class="role-desc">List near-expiry food items and manage redemptions from recipients</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-user-heart"></i></div>
      <div class="role-name">Recipients</div>
      <div class="role-desc">Browse food listings and redeem vouchers for near-expiry food items</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-building-columns"></i></div>
      <div class="role-name">VCFSE</div>
      <div class="role-desc">Voluntary and community organisations that fund the voucher programme</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-school"></i></div>
      <div class="role-name">Schools & Care</div>
      <div class="role-desc">Educational and care organisations supporting community food security</div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-section">
  <div style="font-size:clamp(28px,4vw,42px);font-weight:900;color:#fff;margin-bottom:12px">Ready to Get Started?</div>
  <div style="font-size:16px;color:rgba(255,255,255,.65);max-width:500px;margin:0 auto 36px;line-height:1.7">
    Join the Northamptonshire eVoucher pilot and help us reduce food waste while supporting families in need.
  </div>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
    <a href="{{ route('register') }}" class="btn-hero-primary"><i class="fas fa-rocket"></i> Register Now</a>
    <a href="{{ route('login') }}" class="btn-hero-secondary"><i class="fas fa-sign-in-alt"></i> Sign In</a>
  </div>
</div>

<div class="footer">
  <div style="margin-bottom:8px">
    <strong style="color:rgba(255,255,255,.6)">eVoucher Food Support Platform</strong> — Northamptonshire Pilot
  </div>
  <div>Built for BAKUP CIC · Reducing food waste · Supporting communities · © {{ date('Y') }}</div>
</div>
</body>
</html>
