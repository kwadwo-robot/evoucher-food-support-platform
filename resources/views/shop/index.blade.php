@extends('layouts.app')
@section('title', 'Browse Shops')
@section('content')

<div style="background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);color:#fff;padding:40px 20px;margin-bottom:30px;border-radius:12px">
    <div style="max-width:1200px;margin:0 auto">
        <h1 style="margin:0;font-size:32px;font-weight:900;margin-bottom:12px">Browse Local Shops</h1>
        <p style="margin:0;opacity:.9">Find food items from participating local shops in your area</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:0 20px">
    <!-- Search -->
    <form method="GET" style="display:flex;gap:12px;margin-bottom:24px">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search shops..." 
            class="form-input" style="flex:1;padding:10px;border:1px solid #e2e8f0;border-radius:6px">
        <button type="submit" class="btn btn-primary" style="padding:10px 20px">
            <i class="fas fa-search"></i> Search
        </button>
    </form>

    <!-- Shops Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:30px">
        @forelse($shops as $shop)
        <a href="{{ route('shops.show', $shop) }}" style="text-decoration:none;display:block">
            <div class="shop-card" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1);transition:all .3s ease;background:#fff">
                <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);padding:30px;text-align:center;color:#fff">
                    <div style="font-size:48px;margin-bottom:12px">🏪</div>
                    <h3 style="margin:0;font-size:18px;font-weight:700">{{ $shop->shopProfile->shop_name ?? $shop->name }}</h3>
                </div>
                
                <div style="padding:16px">
                    @if($shop->shopProfile && $shop->shopProfile->address)
                    <p style="font-size:12px;color:#64748b;margin:0 0 12px 0">
                        <i class="fas fa-map-marker-alt" style="color:#10b981;margin-right:6px"></i>
                        {{ $shop->shopProfile->address }}
                    </p>
                    @endif
                    
                    @if($shop->shopProfile && $shop->shopProfile->phone)
                    <p style="font-size:12px;color:#64748b;margin:0 0 12px 0">
                        <i class="fas fa-phone" style="color:#10b981;margin-right:6px"></i>
                        {{ $shop->shopProfile->phone }}
                    </p>
                    @endif
                    
                    <div style="display:flex;gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid #e2e8f0">
                        <div style="flex:1;text-align:center">
                            <div style="font-size:18px;font-weight:900;color:#10b981">
                                {{ \App\Models\FoodListing::where('shop_user_id', $shop->id)->where('is_active', true)->count() }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px">Items</div>
                        </div>
                        <div style="flex:1;text-align:center">
                            <div style="font-size:18px;font-weight:900;color:#3b82f6">
                                {{ \App\Models\FoodListing::where('shop_user_id', $shop->id)->where('listing_type', 'free')->where('is_active', true)->count() }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px">Free</div>
                        </div>
                        <div style="flex:1;text-align:center">
                            <div style="font-size:18px;font-weight:900;color:#f59e0b">
                                {{ \App\Models\FoodListing::where('shop_user_id', $shop->id)->where('listing_type', 'discounted')->where('is_active', true)->count() }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px">Discount</div>
                        </div>
                    </div>
                    
                    <button style="width:100%;margin-top:12px;padding:10px;background:#10b981;color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;transition:background .3s">
                        View Items →
                    </button>
                </div>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:#64748b">
            <div style="font-size:48px;margin-bottom:12px">🏪</div>
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 8px 0">No shops found</h3>
            <p style="margin:0">Try adjusting your search terms</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($shops->hasPages())
    <div style="display:flex;justify-content:center;margin-top:30px">
        {{ $shops->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<style>
.shop-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

.shop-card button:hover {
    background: #059669 !important;
}
</style>

@endsection
