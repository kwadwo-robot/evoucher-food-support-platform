@extends('layouts.dashboard')
@section('page-title', 'Browse Food')
@section('title', 'Browse Food')
@section('content')
<div class="page-hd">
    <h1>Browse Food</h1>
    <p>Available food items near you in Northamptonshire</p>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <p class="text-blue-800"><i class="fas fa-info-circle mr-2"></i>You can only see discounted food items. Browse and redeem your vouchers below.</p>
</div>

<!-- Filters -->
<div style="background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;margin-bottom:20px">
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px">
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..."
                class="form-input" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
        </div>
        
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
        
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Town</label>
            <select name="town" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
                <option value="">All Towns</option>
                <optgroup label="North Northamptonshire">
                    <option value="Wellingborough" {{ request('town') === 'Wellingborough' ? 'selected' : '' }}>Wellingborough</option>
                    <option value="Kettering" {{ request('town') === 'Kettering' ? 'selected' : '' }}>Kettering</option>
                    <option value="Corby" {{ request('town') === 'Corby' ? 'selected' : '' }}>Corby</option>
                </optgroup>
                <optgroup label="East Northamptonshire">
                    <option value="Rushden" {{ request('town') === 'Rushden' ? 'selected' : '' }}>Rushden</option>
                    <option value="Higham Ferrers" {{ request('town') === 'Higham Ferrers' ? 'selected' : '' }}>Higham Ferrers</option>
                    <option value="Raunds" {{ request('town') === 'Raunds' ? 'selected' : '' }}>Raunds</option>
                    <option value="Irthlingborough" {{ request('town') === 'Irthlingborough' ? 'selected' : '' }}>Irthlingborough</option>
                    <option value="Oundle" {{ request('town') === 'Oundle' ? 'selected' : '' }}>Oundle</option>
                    <option value="Thrapston" {{ request('town') === 'Thrapston' ? 'selected' : '' }}>Thrapston</option>
                </optgroup>
                <optgroup label="West Northamptonshire">
                    <option value="Northampton" {{ request('town') === 'Northampton' ? 'selected' : '' }}>Northampton</option>
                    <option value="Daventry" {{ request('town') === 'Daventry' ? 'selected' : '' }}>Daventry</option>
                    <option value="Brackley" {{ request('town') === 'Brackley' ? 'selected' : '' }}>Brackley</option>
                    <option value="Towcester" {{ request('town') === 'Towcester' ? 'selected' : '' }}>Towcester</option>
                </optgroup>
            </select>
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Sort By</label>
            <select name="sort" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
                <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="expiring" {{ $sortBy === 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
            </select>
        </div>
        
        <div style="display:flex;align-items:flex-end">
            <button type="submit" style="width:100%;padding:8px 12px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    @forelse($listings as $listing)
    @php $inCart = in_array($listing->id, session('recipient_cart', [])); @endphp
    <div class="food-card" style="display:flex;flex-direction:column">
        <a href="{{ route('recipient.food.show', $listing) }}" style="text-decoration:none;display:block;flex:1">
        @if($listing->image_url)
        <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}" class="food-card-img" loading="lazy">
        @else
        <div class="food-card-img-placeholder">🍎</div>
        @endif
        <div class="food-card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                <h3 style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.3">{{ $listing->item_name }}</h3>
                <span class="badge badge-orange" style="flex-shrink:0">
                    <i class="fas fa-tag"></i> Discounted
                </span>
            </div>
            @if($listing->listing_type === 'discounted' && $listing->discounted_price)
            <div style="margin-bottom:8px">
                <span style="font-size:16px;font-weight:800;color:#c2410c">£{{ number_format($listing->discounted_price, 2) }}</span>
                @if($listing->original_price)
                <span style="font-size:12px;color:#94a3b8;text-decoration:line-through;margin-left:6px">£{{ number_format($listing->original_price, 2) }}</span>
                @endif
                @if($listing->voucher_value > 0)
                <div style="font-size:11px;color:#16a34a;margin-top:2px"><i class="fas fa-ticket"></i> Voucher covers £{{ number_format($listing->voucher_value, 2) }}</div>
                @endif
            </div>
            @elseif($listing->voucher_value > 0)
            <div style="font-size:14px;font-weight:700;color:#16a34a;margin-bottom:8px">
                <i class="fas fa-ticket"></i> Voucher Value: £{{ number_format($listing->voucher_value, 2) }}
            </div>
            @else
            <div style="font-size:13px;font-weight:700;color:#16a34a;margin-bottom:8px">
                <i class="fas fa-tag"></i> Discounted Item
            </div>
            @endif
            @if($listing->description)
            <p style="font-size:12px;color:#64748b;margin-bottom:8px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $listing->description }}</p>
            @endif
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:11.5px;color:#94a3b8">
                <span><i class="fas fa-calendar mr-1"></i>Exp: {{ $listing->expiry_date->format('d M Y') }}</span>
                <span><i class="fas fa-box mr-1"></i>Qty: {{ $listing->quantity }}</span>
            </div>
            @if($listing->shopProfile)
            <p style="font-size:11.5px;color:#64748b;margin-top:8px"><i class="fas fa-store mr-1" style="color:#16a34a"></i>{{ $listing->shopProfile->shop_name ?? 'Local Shop' }}</p>
            @endif
        </div>
        </a>
        {{-- Add to Cart / In Cart button --}}
        <div style="padding:0 12px 12px">
            @if($inCart)
            <div style="display:flex;gap:8px">
                <a href="{{ route('recipient.cart') }}" class="btn btn-primary" style="flex:1;justify-content:center;font-size:12px;padding:7px 10px">
                    <i class="fas fa-shopping-cart"></i> In Cart — View Cart
                </a>
                <form method="POST" action="{{ route('recipient.cart.remove', $listing) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Remove" style="padding:7px 10px">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
            @else
            <form method="POST" action="{{ route('recipient.cart.add', $listing) }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:12px;padding:7px 10px">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-icon"><i class="fas fa-store"></i></div>
        <h3>No food items available</h3>
        <p>Check back soon for new listings in your area</p>
    </div>
    @endforelse
</div>
@if($listings->hasPages())
<div style="margin-top:24px">{{ $listings->appends(request()->query())->links() }}</div>
@endif
@endsection
