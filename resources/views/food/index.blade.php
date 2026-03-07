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
</style>
</head>
<body>
<nav class="topnav">
  <a href="/" class="topnav-logo">
    <div class="topnav-logo-icon"><i class="fas fa-leaf"></i></div>
    <div class="topnav-logo-text">eVoucher</div>
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
<div style="max-width:1100px;margin:0 auto;padding:32px 24px">
  <div style="margin-bottom:28px">
    <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin-bottom:6px">Available Food Items</h1>
    <p style="font-size:14px;color:#64748b">Near-expiry food available for voucher redemption in Northamptonshire</p>
  </div>
  @if(count($listings) > 0)
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
    @foreach($listings as $item)
    <div class="food-card">
      @if($item->image_url)
      <img src="{{ $item->image_url }}" style="width:100%;height:180px;object-fit:cover" alt="{{ $item->item_name }}">
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
            <div style="font-size:13px;font-weight:600;color:#334155">{{ $item->shopUser->name ?? 'Local Shop' }}</div>
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
  @else
  <div style="text-align:center;padding:80px 24px;background:#fff;border-radius:16px;border:1px solid #e2e8f0">
    <div style="font-size:48px;margin-bottom:16px">🥗</div>
    <div style="font-size:18px;font-weight:700;color:#334155;margin-bottom:8px">No food available right now</div>
    <div style="font-size:14px;color:#94a3b8">Local shops regularly add new near-expiry items. Check back soon!</div>
  </div>
  @endif
</div>
</body>
</html>
