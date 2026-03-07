@extends('layouts.dashboard')
@section('page-title', 'Browse Food')
@section('title', 'Browse Food')
@section('content')
<div class="page-hd">
    <h1>Browse Food</h1>
    <p>Available food items near you in Northamptonshire</p>
</div>

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search food items..."
        class="form-input" style="flex:1">
    <select name="type" class="form-select" style="width:auto;min-width:160px">
        <option value="">All Types</option>
        <option value="free" {{ request('type') === 'free' ? 'selected' : '' }}>Free Items</option>
        <option value="discounted" {{ request('type') === 'discounted' ? 'selected' : '' }}>Food to Go (Discounted)</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    @forelse($listings as $listing)
    <a href="{{ route('recipient.food.show', $listing) }}" class="food-card" style="text-decoration:none;display:block">
        @if($listing->image_url)
        <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}" class="food-card-img">
        @else
        <div class="food-card-img-placeholder">🍎</div>
        @endif
        <div class="food-card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                <h3 style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.3">{{ $listing->item_name }}</h3>
                @if($listing->listing_type === 'discounted')
                <span class="badge badge-orange" style="flex-shrink:0">
                    <i class="fas fa-tag"></i> Food to Go
                </span>
                @else
                <span class="badge badge-green" style="flex-shrink:0">
                    <i class="fas fa-gift"></i> Free
                </span>
                @endif
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
                <i class="fas fa-gift"></i> Free Item
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
            <p style="font-size:11.5px;color:#64748b;margin-top:8px"><i class="fas fa-store mr-1 text-green-400" style="color:#16a34a"></i>{{ $listing->shopProfile->shop_name ?? 'Local Shop' }}</p>
            @endif
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1">
        <div class="empty-icon"><i class="fas fa-store"></i></div>
        <h3>No food items available</h3>
        <p>Check back soon for new listings in your area</p>
    </div>
    @endforelse
</div>
@if($listings->hasPages())
<div style="margin-top:24px">{{ $listings->links() }}</div>
@endif
@endsection
