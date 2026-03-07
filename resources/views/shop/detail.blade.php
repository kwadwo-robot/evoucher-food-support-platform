@extends('layouts.app')
@section('title', $shopProfile->shop_name ?? $shop->name)
@section('content')

<div style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);color:#fff;padding:40px 20px;margin-bottom:30px;border-radius:12px">
    <div style="max-width:1200px;margin:0 auto">
        <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px">
            <div style="font-size:48px">🏪</div>
            <div>
                <h1 style="margin:0;font-size:32px;font-weight:900">{{ $shopProfile->shop_name ?? $shop->name }}</h1>
                <p style="margin:8px 0 0 0;opacity:.9">Browse all food items from this shop</p>
            </div>
        </div>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;margin-top:20px">
            <div style="background:rgba(255,255,255,.2);padding:15px;border-radius:8px;text-align:center">
                <div style="font-size:24px;font-weight:900">{{ $totalItems }}</div>
                <div style="font-size:12px;opacity:.8;margin-top:4px">Total Items</div>
            </div>
            <div style="background:rgba(255,255,255,.2);padding:15px;border-radius:8px;text-align:center">
                <div style="font-size:24px;font-weight:900">{{ $freeItems }}</div>
                <div style="font-size:12px;opacity:.8;margin-top:4px">Free Items</div>
            </div>
            <div style="background:rgba(255,255,255,.2);padding:15px;border-radius:8px;text-align:center">
                <div style="font-size:24px;font-weight:900">{{ $discountedItems }}</div>
                <div style="font-size:12px;opacity:.8;margin-top:4px">Discounted</div>
            </div>
            <div style="background:rgba(255,255,255,.2);padding:15px;border-radius:8px;text-align:center">
                <div style="font-size:24px;font-weight:900">{{ $surplusItems }}</div>
                <div style="font-size:12px;opacity:.8;margin-top:4px">Surplus</div>
            </div>
        </div>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:0 20px">
    <!-- Filters -->
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:24px">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search items..." 
            class="form-input" style="padding:10px;border:1px solid #e2e8f0;border-radius:6px">
        
        <select name="type" class="form-input" style="padding:10px;border:1px solid #e2e8f0;border-radius:6px">
            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
            <option value="free" {{ $type === 'free' ? 'selected' : '' }}>Free Items</option>
            <option value="discounted" {{ $type === 'discounted' ? 'selected' : '' }}>Discounted</option>
            <option value="surplus" {{ $type === 'surplus' ? 'selected' : '' }}>Surplus</option>
        </select>
        
        <select name="sort" class="form-input" style="padding:10px;border:1px solid #e2e8f0;border-radius:6px">
            <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest First</option>
            <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="expiring" {{ $sortBy === 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
        </select>
        
        <button type="submit" class="btn btn-primary" style="padding:10px">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>

    <!-- Food Items Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;margin-bottom:30px">
        @forelse($listings as $listing)
        <div class="food-card" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1)">
            @if($listing->image_url)
            <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}" 
                class="food-card-img" style="width:100%;height:180px;object-fit:cover;loading:lazy">
            @else
            <div class="food-card-img-placeholder" style="width:100%;height:180px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;font-size:48px">
                🍎
            </div>
            @endif
            
            <div style="padding:12px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0;line-height:1.3">{{ $listing->item_name }}</h3>
                    <span class="badge" style="flex-shrink:0;font-size:11px;padding:4px 8px;border-radius:4px;
                        {{ $listing->listing_type === 'free' ? 'background:#dcfce7;color:#15803d' : '' }}
                        {{ $listing->listing_type === 'discounted' ? 'background:#fed7aa;color:#c2410c' : '' }}
                        {{ $listing->listing_type === 'surplus' ? 'background:#f3e8ff;color:#7e22ce' : '' }}">
                        @if($listing->listing_type === 'free')
                            <i class="fas fa-gift"></i> Free
                        @elseif($listing->listing_type === 'discounted')
                            <i class="fas fa-tag"></i> Discounted
                        @else
                            <i class="fas fa-leaf"></i> Surplus
                        @endif
                    </span>
                </div>
                
                @if($listing->listing_type === 'discounted' && $listing->discounted_price)
                <div style="margin-bottom:8px">
                    <span style="font-size:16px;font-weight:800;color:#c2410c">£{{ number_format($listing->discounted_price, 2) }}</span>
                    @if($listing->original_price)
                    <span style="font-size:12px;color:#94a3b8;text-decoration:line-through;margin-left:6px">£{{ number_format($listing->original_price, 2) }}</span>
                    @endif
                </div>
                @elseif($listing->voucher_value > 0)
                <div style="font-size:13px;font-weight:700;color:#16a34a;margin-bottom:8px">
                    <i class="fas fa-ticket"></i> £{{ number_format($listing->voucher_value, 2) }}
                </div>
                @endif
                
                @if($listing->description)
                <p style="font-size:12px;color:#64748b;margin:8px 0;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                    {{ $listing->description }}
                </p>
                @endif
                
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:#94a3b8;margin-top:8px">
                    <span><i class="fas fa-calendar"></i> {{ $listing->expiry_date->format('d M') }}</span>
                    <span><i class="fas fa-box"></i> Qty: {{ $listing->quantity }}</span>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:#64748b">
            <div style="font-size:48px;margin-bottom:12px">📦</div>
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 8px 0">No items found</h3>
            <p style="margin:0">Try adjusting your filters or search terms</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($listings->hasPages())
    <div style="display:flex;justify-content:center;margin-top:30px">
        {{ $listings->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.badge {
    display:inline-flex;
    align-items:center;
    gap:4px;
    font-weight:600;
}
</style>

@endsection
