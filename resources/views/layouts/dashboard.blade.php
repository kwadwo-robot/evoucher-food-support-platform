<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard') — eVoucher Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>window.tailwind={config:{corePlugins:{preflight:false}}}</script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
*{box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#f1f5f9;color:#0f172a;min-height:100vh}
:root{
  --sw:260px;--sb:#0f172a;--sb-hover:#1e293b;--accent:#16a34a;
  --accent2:#15803d;--border:#e2e8f0;--muted:#64748b;
}
/* Sidebar */
.sidebar{position:fixed;top:0;left:0;width:var(--sw);height:100vh;background:var(--sb);
  display:flex;flex-direction:column;z-index:50;overflow-y:auto;transition:transform .25s ease;}
.sidebar::-webkit-scrollbar{width:4px}
.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px}
.sb-logo{padding:20px 16px 16px;border-bottom:1px solid rgba(255,255,255,.07);
  display:flex;align-items:center;gap:10px;}
.sb-logo-icon{width:38px;height:38px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.sb-logo-icon img{width:100%;height:100%;object-fit:contain;border-radius:10px;}
.sb-logo-name{font-size:14.5px;font-weight:800;color:#fff;line-height:1.2;}
.sb-logo-sub{font-size:10px;color:rgba(255,255,255,.35);font-weight:400;letter-spacing:.05em;}
.sb-section{padding:18px 16px 6px;font-size:10px;font-weight:700;
  color:rgba(255,255,255,.22);letter-spacing:.1em;text-transform:uppercase;}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;margin:1px 8px;
  border-radius:8px;color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;
  font-weight:500;transition:all .15s;cursor:pointer;}
.nav-item:hover{background:var(--sb-hover);color:#fff;}
.nav-item.active{background:var(--accent);color:#fff;font-weight:600;}
.nav-item .ni{width:18px;text-align:center;font-size:13.5px;flex-shrink:0;}
.nav-item .nb{margin-left:auto;background:rgba(255,255,255,.15);color:rgba(255,255,255,.9);
  font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;}
.nav-item.active .nb{background:rgba(255,255,255,.25);}
.sb-footer{margin-top:auto;padding:14px 12px;border-top:1px solid rgba(255,255,255,.07);}
/* Topbar */
.topbar{position:fixed;top:0;left:var(--sw);right:0;height:58px;background:#fff;
  border-bottom:1px solid var(--border);display:flex;align-items:center;
  padding:0 24px;gap:14px;z-index:40;}
.topbar-title{font-size:14.5px;font-weight:700;color:#0f172a;flex:1;}
.tb-btn{width:34px;height:34px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;color:#64748b;cursor:pointer;
  transition:all .15s;text-decoration:none;font-size:13px;}
.tb-btn:hover{background:#f1f5f9;color:#0f172a;}
.user-pill{display:flex;align-items:center;gap:8px;padding:5px 12px 5px 5px;
  background:#f8fafc;border:1px solid var(--border);border-radius:10px;cursor:pointer;transition:all .15s;}
.user-pill:hover{background:#f1f5f9;}
.u-avatar{width:28px;height:28px;border-radius:7px;background:var(--accent);
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;}
.u-name{font-size:12.5px;font-weight:600;color:#0f172a;}
.u-role{font-size:10px;color:#94a3b8;text-transform:capitalize;}
/* Main */
.main-wrap{margin-left:var(--sw);padding-top:58px;min-height:100vh;}
.page-content{padding:26px 28px 48px;}
/* Cards */
.card{background:#fff;border-radius:14px;border:1px solid var(--border);
  box-shadow:0 1px 3px rgba(0,0,0,.05),0 1px 2px rgba(0,0,0,.03);overflow:hidden;}
.card-hd{display:flex;align-items:center;justify-content:space-between;
  padding:15px 20px;border-bottom:1px solid #f8fafc;}
.card-title{font-size:13.5px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;}
.card-body{padding:20px;}
/* Stat Cards */
.stat-card{background:#fff;border-radius:14px;border:1px solid var(--border);
  padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:all .2s;}
.stat-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08);transform:translateY(-1px);}
.stat-icon{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;
  justify-content:center;font-size:18px;flex-shrink:0;}
.stat-label{font-size:11.5px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;}
.stat-value{font-size:26px;font-weight:800;color:#0f172a;line-height:1.1;margin:4px 0 2px;}
.stat-change{font-size:11.5px;font-weight:500;}
/* Badges */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;
  font-size:11px;font-weight:600;letter-spacing:.02em;}
.badge-green{background:#dcfce7;color:#15803d;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-yellow{background:#fef9c3;color:#a16207;}
.badge-red{background:#fee2e2;color:#b91c1c;}
.badge-purple{background:#f3e8ff;color:#7e22ce;}
.badge-gray{background:#f1f5f9;color:#475569;}
.badge-orange{background:#ffedd5;color:#c2410c;}
/* Tables */
.data-table{width:100%;border-collapse:collapse;}
.data-table th{padding:10px 16px;text-align:left;font-size:11px;font-weight:700;
  color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border);
  background:#fafafa;}
.data-table td{padding:13px 16px;font-size:13px;color:#334155;border-bottom:1px solid #f8fafc;}
.data-table tr:last-child td{border-bottom:none;}
.data-table tr:hover td{background:#fafafa;}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:9px;
  font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none;border:none;}
.btn-primary{background:var(--accent);color:#fff;}
.btn-primary:hover{background:var(--accent2);}
.btn-secondary{background:#f8fafc;color:#334155;border:1px solid var(--border);}
.btn-secondary:hover{background:#f1f5f9;}
.btn-danger{background:#fee2e2;color:#b91c1c;}
.btn-danger:hover{background:#fecaca;}
.btn-sm{padding:5px 12px;font-size:12px;}
/* Forms — use !important to override Tailwind preflight reset */
.form-label{display:block !important;font-size:12.5px !important;font-weight:600 !important;color:#374151 !important;margin-bottom:5px !important;}
.form-input{width:100% !important;padding:9px 13px !important;border:1px solid var(--border) !important;border-radius:9px !important;
  font-size:13.5px !important;color:#0f172a !important;background:#fff !important;outline:none !important;transition:border .15s !important;font-family:'Inter',sans-serif !important;display:block !important;}
.form-input:focus{border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(22,163,74,.1) !important;}
.form-select{width:100% !important;padding:9px 13px !important;border:1px solid var(--border) !important;border-radius:9px !important;
  font-size:13.5px !important;color:#0f172a !important;background:#fff !important;outline:none !important;transition:border .15s !important;font-family:'Inter',sans-serif !important;display:block !important;appearance:auto !important;-webkit-appearance:auto !important;}
.form-select:focus{border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(22,163,74,.1) !important;}
.form-textarea{width:100% !important;padding:9px 13px !important;border:1px solid var(--border) !important;border-radius:9px !important;
  font-size:13.5px !important;color:#0f172a !important;background:#fff !important;outline:none !important;transition:border .15s !important;
  font-family:'Inter',sans-serif !important;resize:vertical !important;display:block !important;}
.form-textarea:focus{border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(22,163,74,.1) !important;}
/* Alerts */
.alert{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:500;margin-bottom:16px;
  display:flex;align-items:flex-start;gap:10px;}
/* Standalone alert variants — work with or without the base .alert class */
.alert-success{padding:12px 16px !important;border-radius:10px !important;font-size:13px !important;font-weight:500 !important;margin-bottom:16px !important;display:flex !important;align-items:flex-start !important;gap:10px !important;background:#dcfce7 !important;color:#15803d !important;border:1px solid #bbf7d0 !important;}
.alert-error{padding:12px 16px !important;border-radius:10px !important;font-size:13px !important;font-weight:500 !important;margin-bottom:16px !important;display:flex !important;align-items:flex-start !important;gap:10px !important;background:#fee2e2 !important;color:#b91c1c !important;border:1px solid #fecaca !important;}
.alert-warning{padding:12px 16px !important;border-radius:10px !important;font-size:13px !important;font-weight:500 !important;margin-bottom:16px !important;display:flex !important;align-items:flex-start !important;gap:10px !important;background:#fef9c3 !important;color:#a16207 !important;border:1px solid #fde68a !important;}
.alert-info{padding:12px 16px !important;border-radius:10px !important;font-size:13px !important;font-weight:500 !important;margin-bottom:16px !important;display:flex !important;align-items:flex-start !important;gap:10px !important;background:#dbeafe !important;color:#1d4ed8 !important;border:1px solid #bfdbfe !important;}
/* Page Header */
.page-hd{margin-bottom:24px;}
.page-hd h1{font-size:22px;font-weight:800;color:#0f172a;line-height:1.2;}
.page-hd p{font-size:13.5px;color:var(--muted);margin-top:4px;}
/* Empty State */
.empty-state{text-align:center;padding:48px 24px;}
.empty-state .empty-icon{font-size:40px;color:#cbd5e1;margin-bottom:12px;}
.empty-state h3{font-size:15px;font-weight:700;color:#334155;margin-bottom:6px;}
.empty-state p{font-size:13px;color:var(--muted);}
/* Voucher Card */
.voucher-card{background:linear-gradient(135deg,#16a34a 0%,#15803d 50%,#166534 100%);
  border-radius:16px;padding:24px;color:#fff;position:relative;overflow:hidden;}
.voucher-card::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;
  background:rgba(255,255,255,.06);border-radius:50%;}
.voucher-card::after{content:'';position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;
  background:rgba(255,255,255,.04);border-radius:50%;}
/* Food Card */
.food-card{background:#fff;border-radius:14px;border:1px solid var(--border);overflow:hidden;
  transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.05);}
.food-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-2px);}
.food-card-img{width:100%;height:160px;object-fit:cover;background:#f1f5f9;}
.food-card-img-placeholder{width:100%;height:160px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);
  display:flex;align-items:center;justify-content:center;font-size:40px;}
.food-card-body{padding:16px;}
/* Responsive */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .topbar{left:0;}
  .main-wrap{margin-left:0;}
  .page-content{padding:20px 16px 40px;}
  .topbar-title{font-size:13px;}
}
/* Direct element overrides — force proper form styling regardless of Tailwind reset */
input[type=text],input[type=email],input[type=password],input[type=number],input[type=date],input[type=tel],input[type=url],select,textarea{
  border:1px solid #e2e8f0 !important;border-radius:9px !important;padding:9px 13px !important;
  font-size:13.5px !important;color:#0f172a !important;background:#fff !important;
  font-family:'Inter',sans-serif !important;width:100% !important;display:block !important;
  outline:none !important;box-sizing:border-box !important;}
input[type=text]:focus,input[type=email]:focus,input[type=password]:focus,input[type=number]:focus,input[type=date]:focus,input[type=tel]:focus,input[type=url]:focus,select:focus,textarea:focus{
  border-color:#16a34a !important;box-shadow:0 0 0 3px rgba(22,163,74,.1) !important;}
input[type=checkbox],input[type=radio]{width:auto !important;display:inline-block !important;padding:0 !important;}
/* Scrollbar */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
/* Divider */
.divider{height:1px;background:var(--border);margin:20px 0;}
/* Progress */
.progress-bar{height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;}
.progress-fill{height:100%;background:var(--accent);border-radius:3px;transition:width .3s;}
</style>
@yield('extra-css')
</head>
<body>
<div x-data="{ sidebarOpen: false }">

<!-- Sidebar Overlay (mobile) -->
<div class="fixed inset-0 bg-black/40 z-40 md:hidden"
     x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:leave="transition ease-in duration-150"></div>

<!-- Sidebar -->
<aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
  <div class="sb-logo">
    <div class="sb-logo-icon"><img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo"></div>
    <div>
      <div class="sb-logo-name">eVoucher</div>
      <div class="sb-logo-sub">BAKUP CIC</div>
    </div>
  </div>

  @php $role = auth()->user()->role ?? 'recipient'; @endphp

  @if(in_array($role, ['admin','super_admin']))
  <div class="sb-section">{{ __('app.main') }}</div>
  <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-pie"></i></span> {{ __('app.overview') }}
  </a>
  <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-users"></i></span> {{ __('app.users') }}
    @if(isset($pendingCount) && $pendingCount > 0)<span class="nb">{{ $pendingCount }}</span>@endif
  </a>
  <div class="sb-section">{{ __('app.operations') }}</div>
  <a href="{{ route('admin.listings.index') }}" class="nav-item {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-basket-shopping"></i></span> {{ __('app.food_listings') }}
  </a>
  <a href="{{ route('admin.vouchers.index') }}" class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-ticket"></i></span> {{ __('app.vouchers') }}
  </a>
  <a href="{{ route('admin.donations.index') }}" class="nav-item {{ request()->routeIs('admin.donations.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-credit-card"></i></span> {{ __('app.payments') }}
  </a>
  <a href="{{ route('admin.fund-loads.index') }}" class="nav-item {{ request()->routeIs('admin.fund-loads.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-wallet"></i></span> {{ __('app.load_funds') }}
  </a>
  <a href="{{ route('admin.payouts.index') }}" class="nav-item {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-money-bill-transfer"></i></span> {{ __('app.shop_payouts') }}
  </a>
  <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-bar"></i></span> {{ __('app.reports') }}
  </a>
  <a href="{{ route('admin.food-breakdown') }}" class="nav-item {{ request()->routeIs('admin.food-breakdown') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-box-open"></i></span> {{ __('app.food_breakdown') }}
  </a>
  <div class="sb-section">{{ __('app.management') }}</div>
  <a href="{{ route('admin.broadcasts.index') }}" class="nav-item {{ request()->routeIs('admin.broadcasts.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-bullhorn"></i></span> {{ __('app.broadcasts') }}
  </a>
  <a href="{{ route('admin.logs.index') }}" class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-list"></i></span> {{ __('app.system_logs') }}
  </a>
  <a href="{{ route('admin.bank-deposits.index') }}" class="nav-item {{ request()->routeIs('admin.bank-deposits.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-bank"></i></span> {{ __('app.bank_deposits') }}
  </a>
  <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-cog"></i></span> {{ __('app.settings') }}
  </a>
  @endif

  @if($role === 'local_shop')
  <div class="sb-section">{{ __('app.shop') }}</div>
  <a href="{{ route('shop.dashboard') }}" class="nav-item {{ request()->routeIs('shop.dashboard') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-store"></i></span> {{ __('app.dashboard') }}
  </a>
  <a href="{{ route('shop.listings.index') }}" class="nav-item {{ request()->routeIs('shop.listings.index') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-list"></i></span> {{ __('app.my_listings') }}
  </a>
  <a href="{{ route('shop.listings.create') }}" class="nav-item {{ request()->routeIs('shop.listings.create') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-plus-circle"></i></span> {{ __('app.add_listing') }}
  </a>
  <a href="{{ route('shop.redemptions') }}" class="nav-item {{ request()->routeIs('shop.redemptions') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-check-circle"></i></span> {{ __('app.redemptions') }}
  </a>
  <a href="{{ route('shop.verify') }}" class="nav-item {{ request()->routeIs('shop.verify') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-qrcode"></i></span> {{ __('app.verify_voucher') }}
  </a>
  <div class="sb-section">{{ __('app.reports_finance') }}</div>
  <a href="{{ route('shop.payouts.index') }}" class="nav-item {{ request()->routeIs('shop.payouts.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-money-bill-transfer"></i></span> {{ __('app.my_payouts') }}
  </a>
  <a href="{{ route('shop.reports.index') }}" class="nav-item {{ request()->routeIs('shop.reports.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-bar"></i></span> {{ __('app.all_reports') }}
  </a>
  @endif

  @if($role === 'recipient')
  <div class="sb-section">{{ __('app.my_account') }}
  <a href="{{ route('recipient.dashboard') }}" class="nav-item {{ request()->routeIs('recipient.dashboard') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-home"></i></span> {{ __('app.dashboard') }}
  </a>
  <a href="{{ route('recipient.vouchers') }}" class="nav-item {{ request()->routeIs('recipient.vouchers') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-ticket"></i></span> {{ __('app.my_vouchers') }}
  </a>
  <a href="{{ route('recipient.food.browse') }}" class="nav-item {{ request()->routeIs('recipient.food.browse') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-basket-shopping"></i></span> {{ __('app.browse_food') }}
  </a>
  <a href="{{ route('recipient.cart') }}" class="nav-item {{ request()->routeIs('recipient.cart') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-shopping-cart"></i></span> {{ __('app.my_cart') }}
    @php $cartCount = count(session('recipient_cart', [])); @endphp
    @if($cartCount > 0)
    <span class="nb" style="background:#dc2626;color:#fff">{{ $cartCount }}</span>
    @endif
  </a>
  <div class="sb-section">Reports</div>
  <a href="{{ route('recipient.history') }}" class="nav-item {{ request()->routeIs('recipient.history') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-history"></i></span> {{ __('app.redemption_history') }}
  </a>
  <a href="{{ route('recipient.reports.export-pdf') }}" class="nav-item">
    <span class="ni"><i class="fas fa-file-pdf"></i></span> {{ __('app.export_pdf') }}
  </a>
  <a href="{{ route('recipient.reports.export-excel') }}" class="nav-item">
    <span class="ni"><i class="fas fa-file-excel"></i></span> {{ __('app.export_excel') }}
  </a>
  @endif

  @if($role === 'vcfse')
  <div class="sb-section">{{ __('app.organisation') }}</div>
  <a href="{{ route('vcfse.dashboard') }}" class="nav-item {{ request()->routeIs('vcfse.dashboard') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-building"></i></span> {{ __('app.dashboard') }}
  </a>
  <a href="{{ route('vcfse.fund-load') }}" class="nav-item {{ request()->routeIs('vcfse.fund-load') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-wallet"></i></span> {{ __('app.load_funds') }}
  </a>
  <a href="{{ route('vcfse.food') }}" class="nav-item {{ request()->routeIs('vcfse.food') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-box-open"></i></span> {{ __('app.browse_food') }}
  </a>
  <a href="{{ route('vcfse.food-breakdown') }}" class="nav-item {{ request()->routeIs('vcfse.food-breakdown') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-pie"></i></span> {{ __('app.food_breakdown') }}
  </a>
  <div class="sb-section">{{ __('app.vouchers') }}</div>
  <a href="{{ route('vcfse.vouchers.create') }}" class="nav-item {{ request()->routeIs('vcfse.vouchers.create') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-plus-circle"></i></span> {{ __('app.issue_voucher') }}
  </a>
  <a href="{{ route('vcfse.vouchers.index') }}" class="nav-item {{ request()->routeIs('vcfse.vouchers.index', 'vcfse.vouchers.show') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-ticket"></i></span> {{ __('app.my_vouchers') }}
  </a>
  <div class="sb-section">Reports</div>
  <a href="{{ route('vcfse.reports') }}" class="nav-item {{ request()->routeIs('vcfse.reports') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-bar"></i></span> {{ __('app.reports') }}
  </a>
  <a href="{{ route('vcfse.bank-deposit-notification.create') }}" class="nav-item {{ request()->routeIs('vcfse.bank-deposit-notification.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-university"></i></span> {{ __('app.bank_deposit') }}
  </a>
  @endif

  @if($role === 'school_care')
  <div class="sb-section">{{ __('app.organisation') }}</div>
  <a href="{{ route('school.dashboard') }}" class="nav-item {{ request()->routeIs('school.dashboard') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-school"></i></span> {{ __('app.dashboard') }}
  </a>
  <a href="{{ route('school.fund-load') }}" class="nav-item {{ request()->routeIs('school.fund-load') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-wallet"></i></span> {{ __('app.load_funds') }}
  </a>
  <a href="{{ route('school.food') }}" class="nav-item {{ request()->routeIs('school.food') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-box-open"></i></span> {{ __('app.browse_food') }}
  </a>
  <a href="{{ route('school.food-breakdown') }}" class="nav-item {{ request()->routeIs('school.food-breakdown') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-pie"></i></span> {{ __('app.food_breakdown') }}
  </a>
  <div class="sb-section">{{ __('app.vouchers') }}</div>
  <a href="{{ route('school.vouchers.create') }}" class="nav-item {{ request()->routeIs('school.vouchers.create') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-plus-circle"></i></span> {{ __('app.issue_voucher') }}
  </a>
  <a href="{{ route('school.vouchers.index') }}" class="nav-item {{ request()->routeIs('school.vouchers.index', 'school.vouchers.show') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-ticket"></i></span> {{ __('app.my_vouchers') }}
  </a>
  <div class="sb-section">Reports</div>
  <a href="{{ route('school.reports') }}" class="nav-item {{ request()->routeIs('school.reports') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-chart-bar"></i></span> {{ __('app.reports') }}
  </a>
  <a href="{{ route('school.bank-deposit-notification.create') }}" class="nav-item {{ request()->routeIs('school.bank-deposit-notification.*') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-university"></i></span> {{ __('app.bank_deposit') }}
  </a>
  @endif

  <div class="sb-section">{{ __('app.account') }}</div>
  @php
    $profileRoute = match($role) {
      'local_shop' => 'shop.profile',
      'recipient' => 'recipient.profile',
      'vcfse' => 'vcfse.profile',
      'school_care' => 'school.profile',
      default => null,
    };
  @endphp
  @if($profileRoute)
  <a href="{{ route($profileRoute) }}" class="nav-item {{ request()->routeIs($profileRoute) ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-user-cog"></i></span> {{ __('app.profile_settings') }}
  </a>
  @endif
  <a href="{{ route('password.change') }}" class="nav-item {{ request()->routeIs('password.change') ? 'active' : '' }}">
    <span class="ni"><i class="fas fa-lock"></i></span> {{ __('app.change_password') }}
  </a>
  @if($role !== 'recipient')
  <a href="{{ url('/food') }}" class="nav-item">
    <span class="ni"><i class="fas fa-globe"></i></span> {{ __('app.public_listings') }}
  </a>
  @endif

  <div class="sb-footer">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="nav-item w-full text-left" style="background:none;border:none;width:100%;">
        <span class="ni"><i class="fas fa-sign-out-alt"></i></span>
        <span>{{ __('app.sign_out') }}</span>
      </button>
    </form>
  </div>
</aside>

<!-- Topbar -->
<header class="topbar">
  <button class="tb-btn md:hidden" @click="sidebarOpen=!sidebarOpen">
    <i class="fas fa-bars"></i>
  </button>
  <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
  <div class="flex items-center gap-3">
    @yield('topbar-actions')
    <!-- PWA Install Button -->
    <button id="pwa-install-btn" onclick="installPWA()" class="tb-btn" title="Install App" style="display:none;">
      <i class="fas fa-download"></i>
    </button>
    
    <!-- Notifications Button -->
    <div class="relative" x-data="{ notifOpen: false }">
      <button @click="notifOpen=!notifOpen" class="tb-btn" title="Notifications" id="notif-btn">
        <i class="fas fa-bell"></i>
        <span id="notif-badge" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" style="display:none;font-size:10px;font-weight:bold">0</span>
      </button>
      <div x-show="notifOpen" x-cloak @click.away="notifOpen=false"
           class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl border border-slate-200 shadow-lg py-2 z-50 max-h-96 overflow-y-auto"
           x-transition>
        <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
          <span class="font-semibold text-sm">Notifications</span>
          <div class="flex gap-2">
            <button onclick="markAllAsRead()" class="text-xs text-green-600 hover:text-green-700 font-medium">Mark All Read</button>
            <a href="{{ route('notifications.index') }}" class="text-xs text-green-600 hover:text-green-700">View All</a>
          </div>
        </div>
        <div id="notif-list" style="max-height:300px;overflow-y:auto">
          <div class="px-4 py-3 text-sm text-slate-500 text-center">Loading...</div>
        </div>
      </div>
    </div>
    <a href="{{ url('/food') }}" class="tb-btn" title="Public Listings">
      <i class="fas fa-globe"></i>
    </a>
    <!-- Language Switcher -->
    <div class="relative" x-data="{ langOpen: false }">
      <button @click="langOpen=!langOpen" class="tb-btn flex items-center gap-1" title="Language" style="width:auto;padding:0 10px;" id="langBtn">
        <i class="fas fa-language"></i>
        <span style="font-size:11px;font-weight:700;text-transform:uppercase" id="langCode">{{ app()->getLocale() }}</span>
      </button>
      <div x-show="langOpen" x-cloak @click.away="langOpen=false"
           class="absolute right-0 top-full mt-2 bg-white rounded-xl border border-slate-200 shadow-lg py-1 z-50"
           style="min-width:160px" x-transition>
        <a href="#" onclick="switchLanguage('en', event)" class="flex items-center gap-2 px-4 py-2 text-sm lang-option {{ app()->getLocale()==='en' ? 'text-green-600 font-semibold bg-green-50' : 'text-slate-700 hover:bg-slate-50' }}" data-lang="en">🇬🇧 English</a>
        <a href="#" onclick="switchLanguage('ar', event)" class="flex items-center gap-2 px-4 py-2 text-sm lang-option {{ app()->getLocale()==='ar' ? 'text-green-600 font-semibold bg-green-50' : 'text-slate-700 hover:bg-slate-50' }}" data-lang="ar">🇸🇦 العربية</a>
        <a href="#" onclick="switchLanguage('ro', event)" class="flex items-center gap-2 px-4 py-2 text-sm lang-option {{ app()->getLocale()==='ro' ? 'text-green-600 font-semibold bg-green-50' : 'text-slate-700 hover:bg-slate-50' }}" data-lang="ro">🇷🇴 Română</a>
        <a href="#" onclick="switchLanguage('pl', event)" class="flex items-center gap-2 px-4 py-2 text-sm lang-option {{ app()->getLocale()==='pl' ? 'text-green-600 font-semibold bg-green-50' : 'text-slate-700 hover:bg-slate-50' }}" data-lang="pl">🇵🇱 Polski</a>
      </div>
    </div>
    <div class="relative" x-data="{ open: false }">
      <div class="user-pill" @click="open=!open">
        <div class="u-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
        <div>
          <div class="u-name">{{ auth()->user()->name ?? 'User' }}</div>
          <div class="u-role">{{ ucfirst(auth()->user()->role ?? '') }}</div>
        </div>
        <i class="fas fa-chevron-down text-xs text-slate-400 ml-1"></i>
      </div>
      <div x-show="open" x-cloak @click.away="open=false"
           class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl border border-slate-200 shadow-lg py-1 z-50"
           x-transition>
        @if(isset($profileRoute) && $profileRoute)
        <a href="{{ route($profileRoute) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
          <i class="fas fa-user-cog w-4 text-slate-400"></i> Profile Settings
        </a>
        @endif
        <a href="{{ route('password.change') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
          <i class="fas fa-lock w-4 text-slate-400"></i> Change Password
        </a>
        <div class="border-t border-slate-100 my-1"></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 w-full text-left">
            <i class="fas fa-sign-out-alt w-4"></i> Sign Out
          </button>
        </form>
      </div>
    </div>
  </div>
</header>

<!-- Main Content -->
<main class="main-wrap">
  <div class="page-content">
    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(isset($errors) && $errors->any())
    <div class="alert alert-error">
      <i class="fas fa-exclamation-circle"></i>
      <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    </div>
    @endif
     @yield('content')
  </div>
</main>
</div><!-- x-data -->

<script>
  // Notification polling configuration
  let notificationPollInterval = null;
  let lastNotificationCount = 0;
  let isNotificationDropdownOpen = false;
  let notificationFetchTimeout = null;
  
  // Fetch unread notifications and update badge
  async function fetchNotifications() {
    try {
      const response = await fetch('/notifications/unread', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      });
      const data = await response.json();
      
      // Update badge count
      const badge = document.getElementById('notif-badge');
      const list = document.getElementById('notif-list');
      
      // Use the count from the API (includes pending redemptions for shop users)
      let badgeCount = data.count;
      
      // Only update if count changed (reduce DOM updates)
      if (badgeCount !== lastNotificationCount) {
        lastNotificationCount = badgeCount;
        if (badgeCount > 0) {
          badge.textContent = badgeCount;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      }
      
      // Only update notification list if dropdown is open
      if (isNotificationDropdownOpen) {
        // Update notification list
        if (data.notifications && data.notifications.length > 0) {
          let html = '';
          data.notifications.forEach(notif => {
            html += `
              <div class="px-4 py-3 border-b border-slate-100 hover:bg-slate-50 cursor-pointer transition" onclick="markAsRead(${notif.id})">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 mt-1">
                    <div class="w-2 h-2 rounded-full ${notif.read_at ? 'bg-slate-300' : 'bg-green-500'}"></div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900">${notif.title}</p>
                    <p class="text-xs text-slate-600 mt-0.5">${notif.message}</p>
                    <p class="text-xs text-slate-400 mt-1">${new Date(notif.created_at).toLocaleString()}</p>
                  </div>
                </div>
              </div>
            `;
          });
          list.innerHTML = html;
        } else {
          list.innerHTML = '<div class="px-4 py-6 text-sm text-slate-500 text-center">No notifications</div>';
        }
      }
    } catch (error) {
      console.error('Error fetching notifications:', error);
    }
  }
  
  // Mark notification as read
  async function markAsRead(notifId) {
    try {
      const response = await fetch(`/notifications/${notifId}/read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      });
      if (response.ok) {
        fetchNotifications();
      }
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }
  
  // Mark all notifications as read
  async function markAllAsRead() {
    try {
      const response = await fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      });
      if (response.ok) {
        fetchNotifications();
      }
    } catch (error) {
      console.error('Error marking all notifications as read:', error);
    }
  }
  
  // Start polling notifications (only when dropdown is open)
  function startNotificationPolling() {
    if (!notificationPollInterval) {
      fetchNotifications(); // Fetch immediately
      notificationPollInterval    // Polling moved to smart polling that only runs when dropdown is open// Poll every 30 seconds instead of 10
    }
  }
  
  // Stop polling notifications
  function stopNotificationPolling() {
    if (notificationPollInterval) {
      clearInterval(notificationPollInterval);
      notificationPollInterval = null;
    }
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    // Fetch badge count immediately (lightweight)
    fetchNotifications();
    
    // Start polling when notification button is clicked
    const notifBtn = document.getElementById('notif-btn');
    if (notifBtn) {
      notifBtn.addEventListener('click', function() {
        isNotificationDropdownOpen = !isNotificationDropdownOpen;
        if (isNotificationDropdownOpen) {
          startNotificationPolling();
        } else {
          stopNotificationPolling();
        }
      });
    }
    
    // Stop polling when dropdown closes
    const notifDropdown = document.querySelector('[x-show="notifOpen"]');
    if (notifDropdown) {
      notifDropdown.addEventListener('click.away', function() {
        isNotificationDropdownOpen = false;
        stopNotificationPolling();
      });
    }
  });
  
  // Language switching function
  function switchLanguage(lang, event) {
    event.preventDefault();
    
    // Update the language code display
    document.getElementById('langCode').textContent = lang.toUpperCase();
    
    // Update the active language option styling
    document.querySelectorAll('.lang-option').forEach(option => {
      if (option.dataset.lang === lang) {
        option.classList.add('text-green-600', 'font-semibold', 'bg-green-50');
        option.classList.remove('text-slate-700', 'hover:bg-slate-50');
      } else {
        option.classList.remove('text-green-600', 'font-semibold', 'bg-green-50');
        option.classList.add('text-slate-700', 'hover:bg-slate-50');
      }
    });
    
    // Send AJAX request to switch language
    const langRoute = '{{ route("lang.switch", "") }}';
    fetch(langRoute + '/' + lang, {
      method: 'GET',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    }).then(response => {
      if (response.ok) {
        // Reload the page to apply language changes
        window.location.reload();
      }
    }).catch(error => {
      console.error('Error switching language:', error);
    });
  }
</script>

@yield('scripts')
</body>
</html>
