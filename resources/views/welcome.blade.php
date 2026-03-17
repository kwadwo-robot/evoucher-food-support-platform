<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('app.platform_title') }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;color:#0f172a;background:#fff}
/* Colors */
:root{--primary:#0f4c81;--primary-light:#1a5fa0;--accent:#16a34a;--accent-light:#22c55e;--gray-light:#f8fafc;--gray-border:#e2e8f0}
/* Nav */
.nav{display:flex;align-items:center;padding:16px 40px;gap:20px;border-bottom:1px solid var(--gray-border);background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.05);position:relative}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-img{width:40px;height:40px;object-fit:contain}
.nav-logo-text{font-size:13px;font-weight:700;color:#0f172a;display:flex;flex-direction:column;line-height:1.2}
.nav-logo-text-small{font-size:10px;color:#64748b;font-weight:600}
.nav-links{display:flex;align-items:center;gap:12px;margin-left:auto}
.nav-link{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;white-space:nowrap;color:#0f172a}
.nav-link:hover{background:var(--gray-light);color:var(--primary)}
.nav-link-primary{background:var(--accent);color:#fff}
.nav-link-primary:hover{background:var(--accent-light)}
.nav-mobile-toggle{display:none;background:none;border:none;font-size:20px;cursor:pointer;color:#0f172a;padding:8px 12px;margin-left:auto}
.nav-desktop-only{display:inline-flex !important}
/* Hero */
.hero{background:linear-gradient(135deg,var(--primary) 0%,#1a5fa0 100%);padding:120px 40px;text-align:center;color:#fff}
.hero-content{max-width:900px;margin:0 auto}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;padding:8px 18px;border-radius:20px;font-size:12.5px;font-weight:600;margin-bottom:24px;letter-spacing:.04em;backdrop-filter:blur(10px)}
.hero-title{font-size:clamp(42px,6vw,64px);font-weight:900;line-height:1.1;margin-bottom:20px;letter-spacing:-.02em}
.hero-title span{color:var(--accent-light)}
.hero-desc{font-size:18px;color:rgba(255,255,255,.85);line-height:1.8;max-width:700px;margin:0 auto 40px}
.hero-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap}
.btn{padding:14px 32px;border-radius:10px;font-size:15px;font-weight:700;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{background:var(--accent-light);transform:translateY(-2px);box-shadow:0 8px 24px rgba(22,163,74,.3)}
.btn-secondary{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(10px)}
.btn-secondary:hover{background:rgba(255,255,255,.25);border-color:rgba(255,255,255,.4)}
/* Sections */
.section{padding:80px 40px}
.section-title{font-size:clamp(32px,5vw,48px);font-weight:900;color:#0f172a;text-align:center;margin-bottom:16px;letter-spacing:-.02em}
.section-sub{font-size:16px;color:#64748b;text-align:center;max-width:600px;margin:0 auto 56px;line-height:1.7}
.section-alt{background:var(--gray-light)}
/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:40px;max-width:1000px;margin:0 auto;text-align:center}
.stat-item .stat-num{font-size:36px;font-weight:900;color:var(--primary);margin-bottom:8px}
.stat-item .stat-lbl{font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.06em}
/* Cards */
.cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:32px;max-width:1100px;margin:0 auto}
.card{border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);border:1px solid var(--gray-border);background:#fff;transition:all .25s}
.card:hover{box-shadow:0 12px 32px rgba(0,0,0,.12);transform:translateY(-2px)}
.card-icon{width:100%;height:120px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:48px}
.card-body{padding:24px}
.card-num{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:var(--accent);color:#fff;border-radius:50%;font-size:16px;font-weight:800;margin-bottom:12px}
.card-title{font-size:18px;font-weight:800;color:#0f172a;margin-bottom:8px}
.card-desc{font-size:14px;color:#64748b;line-height:1.7}
/* Roles */
.roles-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:1000px;margin:0 auto}
.role-card{background:#fff;border-radius:14px;padding:24px;border:1px solid var(--gray-border);text-align:center;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:all .2s}
.role-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-2px)}
.role-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px}
.role-name{font-size:15px;font-weight:800;color:#0f172a;margin-bottom:6px}
.role-desc{font-size:12.5px;color:#64748b;line-height:1.6}
/* CTA */
.cta-section{background:linear-gradient(135deg,var(--primary) 0%,#1a5fa0 100%);padding:80px 40px;text-align:center;color:#fff}
.cta-section h2{font-size:clamp(32px,5vw,48px);font-weight:900;margin-bottom:16px;letter-spacing:-.02em}
.cta-section p{font-size:16px;color:rgba(255,255,255,.85);max-width:500px;margin:0 auto 36px;line-height:1.7}
/* Footer */
.footer{background:linear-gradient(135deg,var(--primary) 0%,#1a5fa0 100%);padding:60px 40px 32px;border-top:1px solid rgba(255,255,255,.07)}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;max-width:1100px;margin:0 auto 40px}
.footer-brand{}
.footer-logo{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.footer-logo-img{width:36px;height:36px;object-fit:contain}
.footer-logo-text{font-size:16px;font-weight:800;color:#fff}
.footer-tagline{font-size:13.5px;color:rgba(255,255,255,.6);line-height:1.7;max-width:280px}
.footer-col-title{font-size:12px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px}
.footer-link{display:block;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:10px;transition:color .15s}
.footer-link:hover{color:var(--accent-light)}
.footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:24px;max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.footer-copy{font-size:12.5px;color:rgba(255,255,255,.4)}
.footer-badges{display:flex;gap:12px;align-items:center}
.footer-badge{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px}
/* Responsive */
@media(max-width:900px){
  .footer-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:640px){
  .nav{padding:12px 16px;display:flex;justify-content:space-between;align-items:center}
  .nav-logo{flex-shrink:0;order:1}
  .nav-logo-text{font-size:11px}
  .nav-logo-img{width:32px;height:32px}
  .nav-mobile-toggle{display:inline-flex !important;order:2;margin-left:auto;margin-right:0}
  .nav-links{position:absolute;top:100%;left:0;right:0;flex-direction:column;background:#fff;border-bottom:1px solid var(--gray-border);padding:12px;gap:8px;max-height:0;overflow:hidden;transition:max-height .3s ease;z-index:999}
  .nav-links.nav-mobile-open{max-height:500px}
  .nav-link{padding:10px 12px;font-size:12px;width:100%;text-align:left;border-radius:6px}
  .nav-link-primary{width:100%}
  .nav-desktop-only{display:none !important}
  .nav-signin-btn{display:inline-flex !important}
  .hero{padding:60px 20px}
  .section{padding:40px 20px}
  .cta-section{padding:40px 20px}
  .footer{padding:30px 20px 20px}
  .footer-grid{grid-template-columns:1fr}
  .footer-bottom{flex-direction:column;text-align:center}
  .cards-grid{grid-template-columns:1fr}
  .roles-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" x-data="{ mobileMenuOpen: false }">
  <a href="/" class="nav-logo">
    <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="nav-logo-img">
    <div class="nav-logo-text">
      <span class="nav-logo-text-small">BAKUP CIC</span>
      <span>eVoucher</span>
    </div>
  </a>
  
  <!-- Mobile Menu Button -->
  <button @click="mobileMenuOpen = !mobileMenuOpen" class="nav-mobile-toggle">
    <i class="fas fa-bars"></i>
  </button>
  
  <div class="nav-links" :class="{ 'nav-mobile-open': mobileMenuOpen }">
    <!-- Desktop Only Links -->
    <a href="{{ url('/food') }}" class="nav-link nav-desktop-only"><i class="fas fa-basket-shopping mr-1"></i> {{ __('app.browse_food') }}</a>
    <a href="{{ url('/shops') }}" class="nav-link nav-desktop-only"><i class="fas fa-store mr-1"></i> {{ __('app.shops') }}</a>

    <!-- Language Switcher -->
    <div style="position:relative;display:inline-block" x-data="{ open: false }">
      <button @click="open = !open" id="langBtn" class="nav-link" style="display:flex;align-items:center;gap:6px;cursor:pointer;border:none;background:none;width:100%">
        <i class="fas fa-globe"></i>
        <span style="font-size:12px;font-weight:700;text-transform:uppercase" id="langCode">{{ app()->getLocale() }}</span>
        <i class="fas fa-chevron-down" style="font-size:10px"></i>
      </button>
      <div x-show="open" @click.away="open = false" x-transition style="position:absolute;right:0;top:110%;background:#fff;border:1px solid var(--gray-border);border-radius:10px;min-width:160px;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.1)">
        <a href="#" onclick="switchLanguage('en'); return false;" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px;border-radius:8px 8px 0 0">🇬🇧 English</a>
        <a href="#" onclick="switchLanguage('ar'); return false;" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px">🇸🇦 العربية</a>
        <a href="#" onclick="switchLanguage('ro'); return false;" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px">🇷🇴 Română</a>
        <a href="#" onclick="switchLanguage('pl'); return false;" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:#0f172a;text-decoration:none;font-size:13px;border-radius:0 0 8px 8px">🇵🇱 Polski</a>
      </div>
    </div>
    <script>
      function switchLanguage(locale) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/lang/${locale}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              document.getElementById('langCode').textContent = locale.toUpperCase();
              window.location.reload();
            }
          })
          .catch(error => console.error('Error:', error));
      }
    </script>

    @auth
    <!-- Notification Bell -->
    <div style="position:relative" x-data="{ open: false }" class="nav-desktop-only">
      <button @click="open = !open" class="nav-link" style="position:relative;padding:8px 12px;border-radius:8px;color:#0f172a;transition:all .15s;border:none;background:none;font-size:16px;cursor:pointer">
        <i class="fas fa-bell"></i>
        <span style="position:absolute;top:4px;right:6px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px">0</span>
      </button>
      <div x-show="open" @click.away="open = false" x-transition style="position:absolute;right:0;top:110%;width:320px;background:#fff;border:1px solid var(--gray-border);border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.08);z-index:999;overflow:hidden">
        <div style="padding:16px;border-bottom:1px solid var(--gray-border)">
          <div style="font-size:14px;font-weight:700;color:#0f172a">{{ __('app.notifications') }}</div>
          <div style="font-size:12px;color:#94a3b8;margin-top:2px">{{ __('app.no_new_notifications') }}</div>
        </div>
        <div style="padding:24px;text-align:center;color:#94a3b8;font-size:13px">
          <i class="fas fa-bell-slash" style="font-size:24px;margin-bottom:8px;display:block;color:#cbd5e1"></i>
          {{ __('app.no_notifications_yet') }}
        </div>
      </div>
    </div>
    <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="nav-link nav-link-primary nav-desktop-only">
      <i class="fas fa-tachometer-alt mr-1"></i> {{ __('app.dashboard') }}
    </a>
    @else
    <a href="{{ route('login') }}" class="nav-link nav-signin-btn">{{ __('app.sign_in') }}</a>
    <a href="{{ route('register') }}" class="nav-link nav-link-primary">{{ __('app.get_started_free') }}</a>
    @endauth
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <div class="hero-badge"><i class="fas fa-map-marker-alt"></i> {{ __('app.northamptonshire_pilot') }}</div>
    <h1 class="hero-title">{{ __('app.connecting') }} <span>{{ __('app.food') }}</span><br>{{ __('app.with_people_in_need') }}</h1>
    <p class="hero-desc">{{ __('app.hero_description') }}</p>
    <div class="hero-btns">
      <a href="{{ route('register') }}" class="btn btn-primary"><i class="fas fa-rocket"></i> {{ __('app.get_started_free') }}</a>
      <a href="{{ url('/food') }}" class="btn btn-secondary"><i class="fas fa-basket-shopping"></i> {{ __('app.browse_food') }}</a>
      <button onclick="openDonateModal()" class="btn btn-secondary"><i class="fas fa-heart"></i> {{ __('app.donate') }}</button>
    </div>
  </div>
</div>

<!-- STATS -->
<div class="section">
  <div class="stats-grid">
    <div class="stat-item">
      <div class="stat-num">100%</div>
      <div class="stat-lbl">{{ __('app.free_to_use') }}</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">0</div>
      <div class="stat-lbl">{{ __('app.food_wasted') }}</div>
    </div>
    <div class="stat-item">
      <div class="stat-num">6</div>
      <div class="stat-lbl">{{ __('app.user_roles') }}</div>
    </div>

  </div>
</div>

<!-- HOW IT WORKS -->
<div class="section section-alt">
  <h2 class="section-title">{{ __('app.how_it_works') }}</h2>
  <p class="section-sub">{{ __('app.how_it_works_description') }}</p>
  <div class="cards-grid">
    <div class="card">
      <div class="card-icon">🏪</div>
      <div class="card-body">
        <div class="card-num">1</div>
        <div class="card-title">{{ __('app.shops_list_food') }}</div>
        <div class="card-desc">{{ __('app.shops_list_food_description') }}</div>
      </div>
    </div>
    <div class="card">
      <div class="card-icon">🎫</div>
      <div class="card-body">
        <div class="card-num">2</div>
        <div class="card-title">{{ __('app.recipients_get_vouchers') }}</div>
        <div class="card-desc">{{ __('app.recipients_get_vouchers_description') }}</div>
      </div>
    </div>
    <div class="card">
      <div class="card-icon">🤝</div>
      <div class="card-body">
        <div class="card-num">3</div>
        <div class="card-title">{{ __('app.organisations_fund_it') }}</div>
        <div class="card-desc">{{ __('app.organisations_fund_it_description') }}</div>
      </div>
    </div>
  </div>
</div>

<!-- WHO USES EVOUCHER -->
<div class="section">
  <h2 class="section-title">{{ __('app.who_uses_evoucher') }}</h2>
  <p class="section-sub">{{ __('app.who_uses_evoucher_description') }}</p>
  <div class="roles-grid">
    <div class="role-card">
      <div class="role-icon" style="background:#e0e7ff;color:#4f46e5">👑</div>
      <div class="role-name">{{ __('app.super_admin') }}</div>
      <div class="role-desc">{{ __('app.super_admin_description') }}</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#fef3c7;color:#d97706">🔐</div>
      <div class="role-name">{{ __('app.admin') }}</div>
      <div class="role-desc">{{ __('app.admin_description') }}</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#dbeafe;color:#0284c7">🏪</div>
      <div class="role-name">{{ __('app.local_shops') }}</div>
      <div class="role-desc">{{ __('app.local_shops_description') }}</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#f0fdf4;color:#16a34a">👤</div>
      <div class="role-name">{{ __('app.recipients') }}</div>
      <div class="role-desc">{{ __('app.recipients_description') }}</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#fce7f3;color:#ec4899">🤲</div>
      <div class="role-name">{{ __('app.vcfse') }}</div>
      <div class="role-desc">{{ __('app.vcfse_description') }}</div>
    </div>
    <div class="role-card">
      <div class="role-icon" style="background:#f3e8ff;color:#a855f7">🎓</div>
      <div class="role-name">{{ __('app.schools_care') }}</div>
      <div class="role-desc">{{ __('app.schools_care_description') }}</div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-section">
  <h2>{{ __('app.ready_to_get_started') }}</h2>
  <p>{{ __('app.ready_to_get_started_description') }}</p>
  <a href="{{ route('register') }}" class="btn btn-primary" style="background:#fff;color:var(--primary)"><i class="fas fa-rocket"></i> {{ __('app.get_started_free') }}</a>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo">
        <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" class="footer-logo-img">
        <div class="footer-logo-text">eVoucher</div>
      </div>
      <div class="footer-tagline">{{ __('app.footer_tagline') }}</div>
    </div>
    <div>
      <div class="footer-col-title">{{ __('app.platform') }}</div>
      <a href="{{ url('/food') }}" class="footer-link">{{ __('app.browse_food') }}</a>
      <a href="{{ url('/shops') }}" class="footer-link">{{ __('app.shops') }}</a>
      <a href="{{ route('login') }}" class="footer-link">{{ __('app.sign_in') }}</a>
    </div>
    <div>
      <div class="footer-col-title">{{ __('app.company') }}</div>
      <a href="#" class="footer-link">{{ __('app.about_us') }}</a>
      <a href="#" class="footer-link">{{ __('app.contact') }}</a>
      <a href="#" class="footer-link">{{ __('app.volunteer') }}</a>
    </div>
    <div>
      <div class="footer-col-title">{{ __('app.legal') }}</div>
      <a href="#" class="footer-link">{{ __('app.privacy_policy') }}</a>
      <a href="#" class="footer-link">{{ __('app.terms_of_use') }}</a>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-copy">{{ __('app.footer_copy', ['year' => date('Y')]) }}</div>
    <div class="footer-badges">
      <div class="footer-badge">Northamptonshire Pilot</div>
      <div class="footer-badge">BAKUP CIC</div>
    </div>
  </div>
</footer>

<script>
function openDonateModal() {
  alert('Donate functionality coming soon!');
}
</script>
</body>
</html>
