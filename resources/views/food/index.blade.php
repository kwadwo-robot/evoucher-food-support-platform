@if(auth()->check())
@extends('layouts.dashboard')

@section('page-title', 'Browse Food')

@section('content')
<div style="padding:24px">
  <div style="margin-bottom:28px">
    <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin-bottom:6px">Available Food Items</h1>
    <p style="color:#64748b;font-size:14px">Near-expiry food available for voucher redemption in Northamptonshire</p>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:24px;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Search</label>
      <input type="text" placeholder="Search items..." style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Shop</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>All Shops</option>
        @foreach($shops as $shop)
        <option value="{{ $shop->id }}">{{ $shop->shop_name }} ({{ $shop->food_count }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Type</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>All Types</option>
        <option>Free Items</option>
        <option>Discounted</option>
        <option>Surplus</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Sort By</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>Newest</option>
        <option>Price: Low to High</option>
        <option>Price: High to Low</option>
        <option>Expiring Soon</option>
      </select>
    </div>
    <div style="display:flex;align-items:flex-end">
      <button style="width:100%;padding:8px 14px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600">Filter</button>
    </div>
  </div>

  <div style="margin-bottom:16px;padding:12px;background:#dcfce7;border-radius:8px;border-left:4px solid #16a34a;color:#15803d;font-size:13px">
    <i class="fas fa-check-circle mr-2"></i> Showing {{ count($items) }} of {{ count($items) }} items
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:20px">
    @foreach($items as $item)
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.05)">
      <div style="position:relative;height:160px;background:#f1f5f9;overflow:hidden">
        @if($item->image_url)
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:cover">
        @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e2e8f0;color:#94a3b8">
          <i class="fas fa-image" style="font-size:32px"></i>
        </div>
        @endif
        <div style="position:absolute;top:8px;right:8px;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#dcfce7;color:#15803d">
          {{ $item->quantity }} left
        </div>
      </div>
      <div style="padding:16px">
        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $item->name }}</h3>
        <div style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fef9c3;color:#a16207;margin-bottom:8px">
          <i class="fas fa-tag"></i> {{ $item->food_type }} — {{ $item->food_category }}
        </div>
        <p style="font-size:13px;color:#64748b;margin-bottom:8px;line-height:1.5">{{ Str::limit($item->description, 60) }}</p>
        <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px">
          <span style="font-size:18px;font-weight:700;color:#16a34a">£{{ number_format($item->price, 2) }}</span>
        </div>
        <div style="margin-top:12px;padding:8px 12px;background:#f0fdf4;border-radius:8px;font-size:12px;color:#15803d;font-weight:500">
          <i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($item->collection_address, 45) }}
        </div>
        <a href="{{ route('food.show', $item->id) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:10px;background:#16a34a;color:#fff;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
          <i class="fas fa-ticket"></i> Redeem with Voucher
        </a>
      </div>
    </div>
    @endforeach
  </div>

  <div style="margin-top:40px;padding:32px;background:#fff;border-radius:14px;border:1px solid #e2e8f0;text-align:center">
    <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Want to browse by shop?</h2>
    <p style="color:#64748b;font-size:14px;margin-bottom:16px">View all participating shops and their available items</p>
    <a href="{{ route('shops.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#16a34a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
      <i class="fas fa-store"></i> Browse All Shops
    </a>
  </div>
</div>
@endsection

@else
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Food — eVoucher Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#f1f5f9;color:#0f172a}
.topnav{background:#0f172a;padding:14px 32px;display:flex;align-items:center;gap:16px}
.topnav-logo{display:flex;align-items:center;gap:8px;text-decoration:none}
.topnav-logo-icon{width:34px;height:34px;background:#16a34a;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px}
.topnav-logo-text{font-size:14px;font-weight:800;color:#fff}
.food-card{background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.food-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-2px)}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-green{background:#dcfce7;color:#15803d}
.badge-red{background:#fee2e2;color:#b91c1c}
.badge-yellow{background:#fef9c3;color:#a16207}
.filter-btn{padding:8px 14px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;transition:all .2s}
.filter-btn:hover{background:#f1f5f9;border-color:#cbd5e1}
.filter-btn.active{background:#16a34a;color:#fff;border-color:#16a34a}
</style>
</head>
<body>
<nav class="topnav">
  <a href="/" class="topnav-logo" style="display:flex;align-items:center;gap:10px;text-decoration:none">
    <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" style="width:40px;height:40px;object-fit:contain">
    <div style="display:flex;flex-direction:column;line-height:1.2">
      <span style="font-size:10px;color:rgba(255,255,255,.6);font-weight:600">BAKUP CIC</span>
      <span style="font-size:13px;color:#fff;font-weight:700">eVoucher</span>
    </div>
  </a>
  <div style="margin-left:auto;display:flex;gap:10px">
    @auth
    <a href="{{ route(auth()->user()->getDashboardRoute()) }}" style="padding:7px 16px;background:#16a34a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">Dashboard</a>
    @else
    <a href="{{ route('login') }}" style="padding:7px 16px;background:rgba(255,255,255,.1);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">Sign In</a>
    <a href="{{ route('register') }}" style="padding:7px 16px;background:#16a34a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">Register</a>
    @endauth
  </div>
</nav>

<div style="max-width:1200px;margin:0 auto;padding:32px 24px">
  <div style="margin-bottom:28px">
    <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin-bottom:6px">Available Food Items</h1>
    <p style="color:#64748b;font-size:14px">Near-expiry food available for voucher redemption in Northamptonshire</p>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-bottom:24px;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Search</label>
      <input type="text" placeholder="Search items..." style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Shop</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>All Shops</option>
        @foreach($shops as $shop)
        <option value="{{ $shop->id }}">{{ $shop->shop_name }} ({{ $shop->food_count }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Type</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>All Types</option>
        <option>Free Items</option>
        <option>Discounted</option>
        <option>Surplus</option>
      </select>
    </div>
    <div>
      <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px">Sort By</label>
      <select style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px">
        <option>Newest</option>
        <option>Price: Low to High</option>
        <option>Price: High to Low</option>
        <option>Expiring Soon</option>
      </select>
    </div>
    <div style="display:flex;align-items:flex-end">
      <button style="width:100%;padding:8px 14px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600">Filter</button>
    </div>
  </div>

  <div style="margin-bottom:16px;padding:12px;background:#dcfce7;border-radius:8px;border-left:4px solid #16a34a;color:#15803d;font-size:13px">
    <i class="fas fa-check-circle mr-2"></i> Showing {{ count($items) }} of {{ count($items) }} items
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:20px">
    @foreach($items as $item)
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;overflow:hidden;transition:all .2s;box-shadow:0 1px 3px rgba(0,0,0,.05)">
      <div style="position:relative;height:160px;background:#f1f5f9;overflow:hidden">
        @if($item->image_url)
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:cover">
        @else
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e2e8f0;color:#94a3b8">
          <i class="fas fa-image" style="font-size:32px"></i>
        </div>
        @endif
        <div style="position:absolute;top:8px;right:8px;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#dcfce7;color:#15803d">
          {{ $item->quantity }} left
        </div>
      </div>
      <div style="padding:16px">
        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $item->name }}</h3>
        <div style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fef9c3;color:#a16207;margin-bottom:8px">
          <i class="fas fa-tag"></i> {{ $item->food_type }} — {{ $item->food_category }}
        </div>
        <p style="font-size:13px;color:#64748b;margin-bottom:8px;line-height:1.5">{{ Str::limit($item->description, 60) }}</p>
        <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px">
          <span style="font-size:18px;font-weight:700;color:#16a34a">£{{ number_format($item->price, 2) }}</span>
        </div>
        <div style="margin-top:12px;padding:8px 12px;background:#f0fdf4;border-radius:8px;font-size:12px;color:#15803d;font-weight:500">
          <i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($item->collection_address, 45) }}
        </div>
        <a href="{{ route('food.show', $item->id) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:10px;background:#16a34a;color:#fff;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
          <i class="fas fa-ticket"></i> Redeem with Voucher
        </a>
      </div>
    </div>
    @endforeach
  </div>

  <div style="margin-top:40px;padding:32px;background:#fff;border-radius:14px;border:1px solid #e2e8f0;text-align:center">
    <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:8px">Want to browse by shop?</h2>
    <p style="color:#64748b;font-size:14px;margin-bottom:16px">View all participating shops and their available items</p>
    <a href="{{ route('shops.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#16a34a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
      <i class="fas fa-store"></i> Browse All Shops
    </a>
  </div>
</div>

<footer style="background:#0f172a;color:#fff;padding:32px;margin-top:60px">
  <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:32px;margin-bottom:32px">
    <div>
      <h4 style="font-weight:700;margin-bottom:12px">Navigation</h4>
      <ul style="list-style:none">
        <li style="margin-bottom:8px"><a href="{{ route('food.index') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Browse Food</a></li>
        <li style="margin-bottom:8px"><a href="{{ route('shops.index') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Browse Shops</a></li>
      </ul>
    </div>
    <div>
      <h4 style="font-weight:700;margin-bottom:12px">Account</h4>
      <ul style="list-style:none">
        @auth
        <li style="margin-bottom:8px"><a href="{{ route(auth()->user()->getDashboardRoute()) }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Dashboard</a></li>
        @else
        <li style="margin-bottom:8px"><a href="{{ route('login') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Sign In</a></li>
        <li style="margin-bottom:8px"><a href="{{ route('register') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Register</a></li>
        @endauth
      </ul>
    </div>
    <div>
      <h4 style="font-weight:700;margin-bottom:12px">Company</h4>
      <ul style="list-style:none">
        <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">About Us</a></li>
        <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Contact</a></li>
        <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Privacy Policy</a></li>
      </ul>
    </div>
  </div>
  <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:16px;text-align:center;color:#64748b;font-size:12px">
    <p>&copy; 2026 eVoucher Food Support Platform. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
@endif
