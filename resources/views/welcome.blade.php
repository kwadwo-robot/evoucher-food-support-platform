<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>eVoucher Food Support Platform — Northamptonshire</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;color:#0f172a;background:#fff}
/* Nav */
.nav{display:flex;align-items:center;padding:18px 40px;gap:20px;border-bottom:1px solid #e2e8f0;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-img{width:40px;height:40px;object-fit:contain}
.nav-logo-text{font-size:13px;font-weight:700;color:#0f172a;display:flex;flex-direction:column;line-height:1.2}
.nav-logo-text-small{font-size:10px;color:#64748b;font-weight:600}
.nav-links{display:flex;align-items:center;gap:12px;margin-left:auto}
.nav-link{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;white-space:nowrap;color:#0f172a}
.nav-link:hover{background:#f1f5f9;color:#16a34a}
.nav-link-primary{background:#16a34a;color:#fff}
.nav-link-primary:hover{background:#15803d}
.notif-bell{position:relative;cursor:pointer;padding:8px 12px;border-radius:8px;color:#0f172a;transition:all .15s;border:none;background:none;font-size:16px}
.notif-bell:hover{background:#f1f5f9;color:#16a34a}
.notif-badge{position:absolute;top:4px;right:6px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px}
.notif-dropdown{position:absolute;right:0;top:110%;width:320px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);z-index:999;overflow:hidden}
/* Hero */
.hero{background:#fff;padding:80px 40px;border-bottom:1px solid #e2e8f0}
.hero-content{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
.hero-text h1{font-size:clamp(36px,5vw,56px);font-weight:900;color:#0f172a;line-height:1.1;margin-bottom:20px}
.hero-text h1 span{color:#16a34a}
.hero-text p{font-size:16px;color:#64748b;line-height:1.75;margin-bottom:32px;max-width:520px}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #dcfce7;color:#16a34a;padding:6px 16px;border-radius:20px;font-size:12.5px;font-weight:600;margin-bottom:24px;letter-spacing:.04em}
.hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.btn-primary{padding:14px 32px;background:#16a34a;color:#fff;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer}
.btn-primary:hover{background:#15803d;transform:translateY(-1px);box-shadow:0 8px 24px rgba(22,163,74,.2)}
.btn-secondary{padding:14px 32px;background:#f1f5f9;color:#0f172a;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;border:1px solid #e2e8f0;cursor:pointer}
.btn-secondary:hover{background:#e2e8f0}
.hero-graphic{text-align:center;font-size:120px;line-height:1}
/* Stats */
.stats-section{background:#f8fafc;padding:60px 40px;border-bottom:1px solid #e2e8f0}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:40px;max-width:1100px;margin:0 auto;text-align:center}
.stat-item .stat-num{font-size:32px;font-weight:900;color:#16a34a;margin-bottom:8px}
.stat-item .stat-lbl{font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.06em}
/* Sections */
.section{padding:80px 40px;background:#fff;border-bottom:1px solid #e2e8f0}
.section-title{font-size:clamp(28px,4vw,42px);font-weight:900;color:#0f172a;text-align:center;margin-bottom:12px}
.section-sub{font-size:16px;color:#64748b;text-align:center;max-width:560px;margin:0 auto 56px;line-height:1.7}
/* How it works */
.hiw-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:32px;max-width:1100px;margin:0 auto}
.hiw-card{border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);border:1px solid #e2e8f0;background:#fff;transition:all .25s}
.hiw-card:hover{box-shadow:0 12px 32px rgba(0,0,0,.12);transform:translateY(-2px)}
.hiw-icon{width:100%;height:120px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:48px}
.hiw-body{padding:24px}
.hiw-num{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;background:#16a34a;color:#fff;border-radius:50%;font-size:16px;font-weight:800;margin-bottom:12px}
.hiw-title{font-size:18px;font-weight:800;color:#0f172a;margin-bottom:8px}
.hiw-desc{font-size:14px;color:#64748b;line-height:1.7}
/* Roles */
.roles-section{background:#f8fafc;padding:80px 40px;border-bottom:1px solid #e2e8f0}
.roles-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;max-width:1000px;margin:0 auto}
.role-card{background:#fff;border-radius:14px;padding:24px;border:1px solid #e2e8f0;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:all .2s}
.role-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-2px)}
.role-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px}
.role-name{font-size:15px;font-weight:800;color:#0f172a;margin-bottom:6px}
.role-desc{font-size:12.5px;color:#64748b;line-height:1.6}
/* CTA */
.cta-section{background:#f0fdf4;padding:80px 40px;text-align:center;color:#0f172a;border-bottom:1px solid #e2e8f0}
.cta-section h2{font-size:clamp(28px,4vw,42px);font-weight:900;margin-bottom:12px}
.cta-section p{font-size:16px;color:#64748b;max-width:500px;margin:0 auto 36px;line-height:1.7}
/* Footer */
.footer{background:#0f172a;padding:60px 40px 32px;border-top:1px solid rgba(255,255,255,.07)}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;max-width:1100px;margin:0 auto 40px}
.footer-brand{}
.footer-logo{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.footer-logo-img{width:36px;height:36px;object-fit:contain}
.footer-logo-text{font-size:16px;font-weight:800;color:#fff}
.footer-tagline{font-size:13.5px;color:rgba(255,255,255,.5);line-height:1.7;max-width:280px}
.footer-col-title{font-size:12px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px}
.footer-link{display:block;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:10px;transition:color .15s}
.footer-link:hover{color:#4ade80}
.footer-bottom{border-top:1px solid rgba(255,255,255,.07);padding-top:24px;max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.footer-copy{font-size:12.5px;color:rgba(255,255,255,.35)}
.footer-badges{display:flex;gap:12px;align-items:center}
.footer-badge{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px}
/* Responsive */
@media(max-width:900px){
  .hero-content{grid-template-columns:1fr}
  .footer-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:640px){
  .nav{padding:16px 20px}
  .hero{padding:60px 20px}
  .stats-section{padding:40px 20px}
  .section{padding:60px 20px}
  .roles-section{padding:60px 20px}
  .cta-section{padding:60px 20px}
  .footer{padding:40px 20px 24px}
  .footer-grid{grid-template-columns:1fr}
  .footer-bottom{flex-direction:column;text-align:center}
  .nav-links .nav-link{display:none}
  .nav-links .nav-link-primary{display:inline-flex}
  .hiw-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
  <a href="/" class="nav-logo">
    <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="nav-logo-img">
    <div class="nav-logo-text">
      <span class="nav-logo-text-small">BAKUP CIC</span>
      <span>eVoucher</span>
    </div>
  </a>
  <div class="nav-links">
    <a href="{{ url('/food') }}" class="nav-link"><i class="fas fa-basket-shopping mr-1"></i> Browse Food</a>
    <a href="{{ url('/shops') }}" class="nav-link"><i class="fas fa-store mr-1"></i> Shops</a>

    <!-- Language Switcher -->
    <div style="position:relative;display:inline-block" x-data="{ open: false }">
      <button @click="open = !open" class="nav-link" style="display:flex;align-items:center;gap:6px;cursor:pointer;border:none;background:none">
        <i class="fas fa-globe"></i>
        <span style="font-size:12px;font-weight:700;text-transform:uppercase">{{ app()->getLocale() }}</span>
        <i class="fas fa-chevron-down" style="font-size:10px"></i>
      </button>
      <div x-show="open" @click.away="open = false" x-transition style="position:absolute;right:0;top:110%;background:#fff;border:1px solid #e2e8f0;border-radius:10px;min-width:160px;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.1)">
        <a href="{{ route('lang.switch', 'en') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px;border-radius:8px 8px 0 0">🇬🇧 English</a>
        <a href="{{ route('lang.switch', 'ar') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px">🇸🇦 العربية</a>
        <a href="{{ route('lang.switch', 'ro') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px">🇷🇴 Română</a>
        <a href="{{ route('lang.switch', 'pl') }}" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px;border-radius:0 0 8px 8px">🇵🇱 Polski</a>
      </div>
    </div>

    @auth
    <!-- Notification Bell -->
    <div style="position:relative" x-data="{ open: false }">
      <button @click="open = !open" class="notif-bell">
        <i class="fas fa-bell"></i>
        <span class="notif-badge">0</span>
      </button>
      <div x-show="open" @click.away="open = false" x-transition class="notif-dropdown">
        <div style="padding:16px;border-bottom:1px solid #e2e8f0">
          <div style="font-size:14px;font-weight:700;color:#0f172a">Notifications</div>
          <div style="font-size:12px;color:#94a3b8;margin-top:2px">You have 0 new notifications</div>
        </div>
        <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px">
          <i class="fas fa-bell-slash" style="font-size:24px;margin-bottom:8px;display:block;color:#cbd5e1"></i>
          No notifications yet
        </div>
      </div>
    </div>
    <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="nav-link nav-link-primary">
      <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
    </a>
    @else
    <a href="{{ route('login') }}" class="nav-link">Sign In</a>
    <a href="{{ route('register') }}" class="nav-link nav-link-primary">Get Started Free</a>
    @endauth
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <div class="hero-text">
      <div class="hero-badge"><i class="fas fa-map-marker-alt"></i> Northamptonshire Pilot Programme</div>
      <h1>Connecting <span>Food</span><br>with People in Need</h1>
      <p>Local shops list near-expiry food. Recipients use vouchers to collect it. Organisations fund the vouchers. Together we reduce food waste and food poverty.</p>
      <div class="hero-btns">
        <a href="{{ route('register') }}" class="btn-primary"><i class="fas fa-rocket"></i> Get Started Free</a>
        <a href="{{ url('/food') }}" class="btn-secondary"><i class="fas fa-basket-shopping"></i> Browse Food</a>
        <button onclick="openDonateModal()" class="btn-secondary"><i class="fas fa-heart"></i> Donate</button>
      </div>
    </div>
    <div class="hero-graphic">🥗</div>
  </div>
</div>

<!-- STATS -->
<div class="stats-section">
  <div class="stats-grid">
    <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Free to Use</div></div>
    <div class="stat-item"><div class="stat-num">0</div><div class="stat-lbl">Food Wasted</div></div>
    <div class="stat-item"><div class="stat-num">6</div><div class="stat-lbl">User Roles</div></div>
    <div class="stat-item"><div class="stat-num">NHS</div><div class="stat-lbl">Community Backed</div></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<div class="section">
  <div class="section-title">How It Works</div>
  <div class="section-sub">A simple three-step process that connects local shops, recipients, and organisations</div>
  <div class="hiw-grid">
    <div class="hiw-card">
      <div class="hiw-icon">🏪</div>
      <div class="hiw-body">
        <div class="hiw-num">1</div>
        <div class="hiw-title">Shops List Food</div>
        <div class="hiw-desc">Local shops and retailers list food items that are close to their expiry date, including photos, collection details, and availability.</div>
      </div>
    </div>
    <div class="hiw-card">
      <div class="hiw-icon">🎫</div>
      <div class="hiw-body">
        <div class="hiw-num">2</div>
        <div class="hiw-title">Recipients Get Vouchers</div>
        <div class="hiw-desc">Approved recipients receive digital vouchers by email and through the platform, which they can use to redeem food items.</div>
      </div>
    </div>
    <div class="hiw-card">
      <div class="hiw-icon">🤝</div>
      <div class="hiw-body">
        <div class="hiw-num">3</div>
        <div class="hiw-title">Organisations Fund It</div>
        <div class="hiw-desc">VCFSE organisations, schools, and care organisations donate funds through Stripe to keep the voucher programme running.</div>
      </div>
    </div>
  </div>
</div>

<!-- USER ROLES -->
<div class="roles-section">
  <div class="section-title">Who Uses eVoucher?</div>
  <div class="section-sub">The platform serves six distinct user types, each with their own dedicated dashboard</div>
  <div class="roles-grid">
    <div class="role-card">
      <div class="role-icon" style="background:rgba(239,68,68,.2);color:#fca5a5"><i class="fas fa-shield-halved"></i></div>
      <div class="role-name">Super Admin</div>
      <div class="role-desc">Full platform control, user management, and system configuration</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:rgba(59,130,246,.2);color:#93c5fd"><i class="fas fa-user-shield"></i></div>
      <div class="role-name">Admin</div>
      <div class="role-desc">Manage users, issue vouchers, track donations, and view reports</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:rgba(249,115,22,.2);color:#fed7aa"><i class="fas fa-store"></i></div>
      <div class="role-name">Local Shops</div>
      <div class="role-desc">List near-expiry food items and manage redemptions from recipients</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:rgba(22,163,74,.2);color:#86efac"><i class="fas fa-user-heart"></i></div>
      <div class="role-name">Recipients</div>
      <div class="role-desc">Browse food listings and redeem vouchers for near-expiry food items</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:rgba(168,85,247,.2);color:#d8b4fe"><i class="fas fa-building-columns"></i></div>
      <div class="role-name">VCFSE</div>
      <div class="role-desc">Voluntary and community organisations that fund the voucher programme</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:rgba(202,138,4,.2);color:#fcd34d"><i class="fas fa-school"></i></div>
      <div class="role-name">Schools & Care</div>
      <div class="role-desc">Educational and care organisations supporting community food security</div>
    </div>
  </div>
</div>

<!-- CTA SECTION -->
<div class="cta-section">
  <h2>Ready to Get Started?</h2>
  <p>Join the Northamptonshire eVoucher pilot and help us reduce food waste while supporting families in need.</p>
  <div class="hero-btns" style="justify-content:center">
    <a href="{{ route('register') }}" class="btn-primary"><i class="fas fa-rocket"></i> Get Started Free</a>
    <a href="{{ url('/food') }}" class="btn-secondary"><i class="fas fa-basket-shopping"></i> Browse Food</a>
    <button onclick="openDonateModal()" class="btn-secondary"><i class="fas fa-heart"></i> Donate</button>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo">
        <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="footer-logo-img">
        <div class="footer-logo-text">eVoucher</div>
      </div>
      <p class="footer-tagline">Connecting near-expiry food with families in need across Northamptonshire. Free to use, community powered, and backed by the NHS.</p>
    </div>
    <div>
      <div class="footer-col-title">Platform</div>
      <a href="{{ url('/food') }}" class="footer-link">Browse Food</a>
      <a href="{{ url('/shops') }}" class="footer-link">Browse Shops</a>
      <a href="{{ route('register') }}" class="footer-link">Get Started</a>
      <a href="{{ route('login') }}" class="footer-link">Sign In</a>
    </div>
    <div>
      <div class="footer-col-title">Support</div>
      <a href="#" class="footer-link">About Us</a>
      <a href="#" class="footer-link">Contact</a>
      <a href="#" class="footer-link">Privacy Policy</a>
      <a href="#" class="footer-link">Terms of Use</a>
    </div>
    <div>
      <div class="footer-col-title">Community</div>
      <a href="#" class="footer-link">BAKUP CIC</a>
      <a href="#" class="footer-link">NHS Partnership</a>
      <a href="#" class="footer-link">Volunteer</a>
      <a href="#" class="footer-link">Donate</a>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-copy">© {{ date('Y') }} eVoucher Food Support Platform — Built for BAKUP CIC · Northamptonshire Pilot</div>
    <div class="footer-badges">
      <span class="footer-badge">NHS Backed</span>
      <span class="footer-badge">BAKUP CIC</span>
      <span class="footer-badge">Zero Waste</span>
    </div>
  </div>
</footer>

<!-- Donate Modal -->
<div id="donateModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);align-items:center;justify-content:center;z-index:9999">
  <div style="background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);padding:32px;max-width:420px;width:90%;border:1px solid #e2e8f0">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
      <h2 style="font-size:22px;font-weight:800;color:#0f172a">Make a Donation</h2>
      <button onclick="closeDonateModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#64748b;line-height:1">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form id="donate-form" style="display:flex;flex-direction:column;gap:16px">
      <div>
        <label style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;color:#0f172a">Donation Amount (£)</label>
        <div style="display:flex;gap:8px;margin-bottom:12px">
          <button type="button" style="flex:1;padding:10px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:all .15s;color:#0f172a" onclick="setAmount(5)">£5</button>
          <button type="button" style="flex:1;padding:10px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;color:#0f172a" onclick="setAmount(10)">£10</button>
          <button type="button" style="flex:1;padding:10px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;color:#0f172a" onclick="setAmount(20)">£20</button>
          <button type="button" style="flex:1;padding:10px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;color:#0f172a" onclick="setAmount(50)">£50</button>
        </div>
        <input type="number" id="donateAmount" name="amount" min="1" step="0.01" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;background:#fff;border-radius:8px;font-size:14px;color:#0f172a" placeholder="Or enter custom amount">
      </div>
      <div>
        <label style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;color:#0f172a">Email</label>
        <input type="email" name="email" required style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;background:#fff;border-radius:8px;font-size:14px;color:#0f172a" placeholder="your@email.com">
      </div>
      <div id="card-element" style="padding:12px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc"></div>
      <div id="card-errors" style="color:#dc2626;font-size:13px"></div>
      <button type="submit" style="width:100%;background:#16a34a;color:#fff;padding:13px;border-radius:10px;border:none;font-weight:700;cursor:pointer;font-size:15px">
        <i class="fas fa-lock mr-2"></i>Donate Securely
      </button>
    </form>
    <p style="font-size:12px;color:#94a3b8;margin-top:16px;text-align:center">
      <i class="fas fa-shield-alt mr-1"></i>Your donation is secure and encrypted with Stripe
    </p>
  </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
  let stripe, elements, cardElement;

  function openDonateModal() {
    document.getElementById('donateModal').style.display = 'flex';
    if (!stripe) {
      stripe = Stripe('{{ config("services.stripe.public") }}');
      elements = stripe.elements();
      cardElement = elements.create('card');
      cardElement.mount('#card-element');
      cardElement.addEventListener('change', (event) => {
        document.getElementById('card-errors').textContent = event.error ? event.error.message : '';
      });
    }
  }

  function closeDonateModal() {
    document.getElementById('donateModal').style.display = 'none';
  }

  function setAmount(amount) {
    document.getElementById('donateAmount').value = amount;
  }

  document.getElementById('donate-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const amount = document.getElementById('donateAmount').value;
    alert('Thank you for your donation of £' + amount + '! This feature will be fully integrated with Stripe.');
    closeDonateModal();
  });
</script>
</body>
</html>
