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
    <p style="font-size:14px;color:#64748b">Near-expiry food available for voucher redemption in Northamptonshire</p>
  </div>

  <!-- Filters -->
  <div style="background:#fff;padding:20px;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:24px">
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
      <!-- Search -->
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..." 
          style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
      </div>

      <!-- Shop Filter -->
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Shop</label>
        <select name="shop_id" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
          <option value="">All Shops</option>
          @foreach($shops as $shop)
          <option value="{{ $shop['id'] }}" {{ request('shop_id') == $shop['id'] ? 'selected' : '' }}>
            {{ $shop['name'] }} ({{ $shop['count'] }})
          </option>
          @endforeach
        </select>
      </div>

      <!-- Type Filter -->
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Type</label>
        <select name="type" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
          <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Types</option>
          <option value="free" {{ request('type') === 'free' ? 'selected' : '' }}>Free Items</option>
          <option value="discounted" {{ request('type') === 'discounted' ? 'selected' : '' }}>Discounted</option>
          <option value="surplus" {{ request('type') === 'surplus' ? 'selected' : '' }}>Surplus</option>
        </select>
      </div>

      <!-- Sort -->
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Sort By</label>
        <select name="sort" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
          <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest</option>
          <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
          <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
          <option value="expiring" {{ $sortBy === 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
        </select>
      </div>

      <!-- Submit -->
      <div style="display:flex;align-items:flex-end">
        <button type="submit" style="width:100%;padding:8px 12px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer">
          <i class="fas fa-filter"></i> Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Results -->
  @if(count($listings) > 0)
  <div style="margin-bottom:20px;padding:12px 16px;background:#f0fdf4;border-left:4px solid #16a34a;border-radius:6px;font-size:13px;color:#15803d">
    <i class="fas fa-info-circle"></i> Showing {{ $listings->count() }} of {{ $listings->total() }} items
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:30px">
    @foreach($listings as $item)
    <div class="food-card">
      @if($item->image_url)
      <img src="{{ $item->image_url }}" style="width:100%;height:180px;object-fit:cover" alt="{{ $item->item_name }}" loading="lazy">
      @else
      <div style="width:100%;height:160px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:48px">🥗</div>
      @endif
      <div style="padding:18px">
        <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:8px">
          <div style="font-size:16px;font-weight:800;color:#0f172a">{{ $item->item_name }}</div>
          <span class="badge badge-green" style="flex-shrink:0;margin-left:8px">{{ $item->quantity }} left</span>
        </div>
        <div style="margin-bottom:8px">
          @if($item->listing_type === 'free')
            <span class="badge badge-green"><i class="fas fa-gift"></i> Free</span>
          @elseif($item->listing_type === 'discounted')
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#dbeafe;color:#1d4ed8"><i class="fas fa-tag"></i> Food to Go — Discounted</span>
            @if($item->discounted_price)
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#fef9c3;color:#a16207;margin-left:4px">£{{ number_format($item->discounted_price,2) }}</span>
            @endif
          @else
            <span class="badge badge-green"><i class="fas fa-gift"></i> Free</span>
          @endif
        </div>
        <div style="font-size:12.5px;color:#64748b;margin-bottom:12px;line-height:1.6">{{ Str::limit($item->description, 70) }}</div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f1f5f9">
          <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Shop</div>
            <a href="{{ route('shops.show', $item->shop_user_id) }}" style="font-size:13px;font-weight:600;color:#334155;text-decoration:none;cursor:pointer;border-bottom:1px solid #cbd5e1">
              {{ $item->shopUser->name ?? 'Local Shop' }}
            </a>
          </div>
          <div style="text-align:right">
            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Expires</div>
            <div style="font-size:13px;font-weight:700;color:#ef4444">{{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}</div>
          </div>
        </div>
        <div style="margin-top:12px;padding:8px 12px;background:#f0fdf4;border-radius:8px;font-size:12px;color:#15803d;font-weight:500">
          <i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($item->collection_address, 45) }}
        </div>
        @auth
        <a href="{{ route('recipient.food.show', $item->id) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:10px;background:#16a34a;color:#fff;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none">
          <i class="fas fa-ticket"></i> Redeem with Voucher
        </a>
        @else
        <a href="{{ route('login') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:10px;background:#f1f5f9;color:#334155;border-radius:9px;font-size:13px;font-weight:700;text-decoration:none;border:1px solid #e2e8f0">
          <i class="fas fa-sign-in-alt"></i> Sign in to Redeem
        </a>
        @endauth
      </div>
    </div>
    @endforeach
  </div>

  <!-- Pagination -->
  <div style="display:flex;justify-content:center;gap:8px;padding:20px;flex-wrap:wrap">
    {{ $listings->appends(request()->query())->links() }}
  </div>
  @else
  <div style="text-align:center;padding:80px 24px;background:#fff;border-radius:16px;border:1px solid #e2e8f0">
    <div style="font-size:48px;margin-bottom:16px">🥗</div>
    <div style="font-size:18px;font-weight:700;color:#334155;margin-bottom:8px">No food available right now</div>
    <div style="font-size:14px;color:#94a3b8">Local shops regularly add new near-expiry items. Check back soon!</div>
  </div>
  @endif

  <!-- Browse Shops Link -->
  <div style="margin-top:40px;padding:20px;background:#f0fdf4;border-radius:12px;border:1px solid #dcfce7;text-align:center">
    <h3 style="font-size:16px;font-weight:700;color:#15803d;margin-bottom:8px">Want to browse by shop?</h3>
    <p style="font-size:13px;color:#64748b;margin-bottom:12px">View all participating shops and their available items</p>
    <a href="{{ route('shops.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#16a34a;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
      <i class="fas fa-store"></i> Browse All Shops
    </a>
  </div>
  </div>

  <!-- Footer -->
  <footer style="background:linear-gradient(135deg,#0f4c81 0%,#1a5fa0 100%);color:#fff;padding:40px 24px;margin-top:60px;border-top:1px solid rgba(255,255,255,.1)">
    <div style="max-width:1200px;margin:0 auto">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;margin-bottom:30px">
        <div>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
            <img src="{{ asset('images/logo.png') }}" alt="eVoucher Logo" style="width:36px;height:36px;object-fit:contain">
            <div>
              <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.04em">BAKUP CIC</div>
              <div style="font-size:16px;font-weight:800">eVoucher</div>
            </div>
          </div>
          <p style="font-size:13px;color:rgba(255,255,255,.6);line-height:1.6">Connecting culturally appropriate local food with people through a simple, three-step process for convenience, improvement in health, wellbeing, local economy and reduction in food waste.</p>
        </div>
        <div>
          <h4 style="font-size:14px;font-weight:700;margin-bottom:12px">Quick Links</h4>
          <ul style="list-style:none;padding:0;margin:0">
            <li style="margin-bottom:8px"><a href="{{ route('food.index') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Browse Food</a></li>
            <li style="margin-bottom:8px"><a href="{{ route('shops.index') }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Browse Shops</a></li>
            @auth
            <li style="margin-bottom:8px"><a href="{{ route(auth()->user()->getDashboardRoute()) }}" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Dashboard</a></li>
            @endauth
          </ul>
        </div>
        <div>
          <h4 style="font-size:14px;font-weight:700;margin-bottom:12px">Support</h4>
          <ul style="list-style:none;padding:0;margin:0">
            <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">About Us</a></li>
            <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Contact</a></li>
            <li style="margin-bottom:8px"><a href="#" style="color:#94a3b8;text-decoration:none;font-size:13px;transition:color .2s">Privacy Policy</a></li>
          </ul>
        </div>
      </div>
      <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
        <p style="margin:0;font-size:12px;color:rgba(255,255,255,.4)">© 2026 eVoucher Food Support Platform — Built for BAKUP CIC · Northamptonshire Pilot</p>
        <div style="display:flex;gap:12px">
          <span style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px">NHS Backed</span>
          <span style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px">BAKUP CIC</span>
          <span style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px">Zero Waste</span>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
