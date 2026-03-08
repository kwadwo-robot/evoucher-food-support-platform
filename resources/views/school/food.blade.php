@extends('layouts.dashboard')
@section('title','Browse Food')
@section('page-title','Browse Food')
@section('content')
<div class="page-hd">
  <h1>Browse Food</h1>
  <p>View free, discounted and surplus food listings available for School/Care collection in Northamptonshire.</p>
</div>

<!-- Filter Bar -->
<div class="card mb-6">
  <div class="card-body" style="padding:16px 20px">
    <form method="GET" action="{{ route('school.food') }}" class="flex flex-wrap gap-3 items-end">
      <div style="flex:1;min-width:200px">
        <label class="form-label">Search Items</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search food items..." class="form-input">
      </div>
      <div style="min-width:160px">
        <label class="form-label">Listing Type</label>
        <select name="type" class="form-select">
          <option value="">All Types</option>
          <option value="free" {{ request('type') === 'free' ? 'selected' : '' }}>Free Items</option>
          <option value="discounted" {{ request('type') === 'discounted' ? 'selected' : '' }}>Discounted Items</option>
          <option value="surplus" {{ request('type') === 'surplus' ? 'selected' : '' }}>Surplus Food</option>
        </select>
      </div>
      <div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-search"></i> Filter
        </button>
        @if(request('search') || request('type'))
        <a href="{{ route('school.food') }}" class="btn btn-secondary ml-2">Clear</a>
        @endif
      </div>
    </form>
  </div>
</div>

<!-- Legend -->
<div class="flex flex-wrap gap-3 mb-5">
  <div class="flex items-center gap-2" style="font-size:12.5px;color:#64748b">
    <span class="badge badge-green"><i class="fas fa-gift"></i> Free</span>
    <span>Free items available to all</span>
  </div>
  <div class="flex items-center gap-2" style="font-size:12.5px;color:#64748b">
    <span class="badge badge-blue"><i class="fas fa-tag"></i> Discounted</span>
    <span>Discounted food items</span>
  </div>
  <div class="flex items-center gap-2" style="font-size:12.5px;color:#64748b">
    <span class="badge badge-purple"><i class="fas fa-boxes-stacked"></i> Surplus</span>
    <span>Surplus food for School/Care collection only</span>
  </div>
</div>

@if($listings->isEmpty())
  <div class="empty-state">
    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
    <h3>No Listings Available</h3>
    <p>There are no free or surplus food listings available at the moment. Check back soon.</p>
  </div>
@else
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6" style="grid-auto-rows: 1fr;">
    @foreach($listings as $item)
    <div class="food-card" style="display: flex; flex-direction: column; height: 100%;">
      @if($item->image_url)
        <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="food-card-img">
      @else
        <div class="food-card-img-placeholder">🥦</div>
      @endif
      <div class="food-card-body" style="display: flex; flex-direction: column; flex: 1;">
        <!-- Type Badge -->
        <div class="mb-2">
          @if($item->listing_type === 'surplus')
            <span class="badge badge-purple"><i class="fas fa-boxes-stacked"></i> Free Surplus</span>
          @else
            <span class="badge badge-green"><i class="fas fa-gift"></i> Free</span>
          @endif
        </div>
        <div style="font-size:14.5px;font-weight:700;color:#0f172a;margin-bottom:4px;line-height:1.3">
          {{ $item->item_name }}
        </div>
        @if($item->description)
        <div style="font-size:12.5px;color:#64748b;margin-bottom:8px;line-height:1.5">
          {{ Str::limit($item->description, 80) }}
        </div>
        @endif
        <div style="font-size:12px;color:#94a3b8;margin-bottom:10px">
          <div class="flex items-center gap-1 mb-1">
            <i class="fas fa-store" style="width:14px"></i>
            {{ $item->shop->shopProfile->shop_name ?? $item->shop->name ?? 'Unknown Shop' }}
          </div>
          @php
            // Get the current allocation for this item for the logged-in user
            $allocation = \App\Models\SurplusAllocation::where('food_listing_id', $item->id)
              ->where('school_care_user_id', Auth::id())
              ->where('status', 'pending')
              ->first();
          @endphp
          @if($allocation && $item->listing_type === 'surplus')
          <div class="flex items-center gap-1 mb-1">
            <i class="fas fa-calendar-alt" style="width:14px"></i>
            @php
              $hoursLeft = $allocation->expires_at->diffInHours(now());
              $minutesLeft = $allocation->expires_at->diffInMinutes(now()) % 60;
            @endphp
            @if($hoursLeft > 0 || $minutesLeft > 0)
              Claim expires in {{ $hoursLeft }}h {{ $minutesLeft }}m
            @else
              Allocation expired
            @endif
          </div>
          @endif
          <div class="flex items-center gap-1 mb-1">
            <i class="fas fa-cubes" style="width:14px"></i>
            Qty: {{ $item->quantity }}
          </div>
          @if($item->collection_address)
          <div class="flex items-center gap-1">
            <i class="fas fa-map-marker-alt" style="width:14px"></i>
            {{ Str::limit($item->collection_address, 50) }}
          </div>
          @endif
        </div>
        @if($item->collection_instructions)
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px;font-size:12px;color:#15803d;margin-bottom:10px">
          <i class="fas fa-info-circle"></i> {{ $item->collection_instructions }}
        </div>
        @endif
        <!-- Expiry Warning - Only for Surplus Items with Allocations and Discounted Items -->
        @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->expiry_date), false); @endphp
        @if($item->listing_type === 'surplus' && $allocation)
          @php
            $hoursLeft = $allocation->expires_at->diffInHours(now());
            $minutesLeft = $allocation->expires_at->diffInMinutes(now()) % 60;
          @endphp
          @if($hoursLeft > 0 || $minutesLeft > 0)
            @if($hoursLeft < 1)
              <div class="badge badge-red w-full justify-center mb-2" style="display:flex">
                <i class="fas fa-hourglass-end"></i> Claim expires in {{ $minutesLeft }} minutes!
              </div>
            @elseif($hoursLeft <= 1)
              <div class="badge badge-yellow w-full justify-center mb-2" style="display:flex">
                <i class="fas fa-clock"></i> Claim expires in {{ $hoursLeft }}h {{ $minutesLeft }}m
              </div>
            @else
              <div class="badge badge-green w-full justify-center mb-2" style="display:flex">
                <i class="fas fa-hourglass-start"></i> Claim expires in {{ $hoursLeft }}h {{ $minutesLeft }}m
              </div>
            @endif
          @else
            <div class="badge badge-red w-full justify-center mb-2" style="display:flex">
              <i class="fas fa-times-circle"></i> Allocation expired
            </div>
          @endif
        @elseif($item->listing_type === 'discounted' && $daysLeft <= 2)
          <div class="badge badge-red w-full justify-center mb-2" style="display:flex">
            <i class="fas fa-exclamation-triangle"></i> Expires in {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }}!
          </div>
        @elseif($item->listing_type === 'discounted' && $daysLeft <= 5)
          <div class="badge badge-yellow w-full justify-center mb-2" style="display:flex">
            <i class="fas fa-clock"></i> Expires in {{ $daysLeft }} days
          </div>
        @endif
        <!-- Collection Time -->
        @if($item->collection_time)
        <div style="font-size:12px;color:#64748b;margin-bottom:10px">
          <i class="fas fa-clock"></i> Collection: {{ $item->collection_time }}
        </div>
        @endif
        <div class="flex items-center justify-between">
          <span style="font-size:18px;font-weight:800;color:#16a34a">FREE</span>
          <span style="font-size:11px;color:#94a3b8">{{ $item->listing_type === 'surplus' ? 'School/Care Collection' : 'Available to All' }}</span>
        </div>
        <!-- Action Buttons for School/Care -->
        <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #e2e8f0;">
        @if($item->listing_type === 'surplus')
          <!-- Claim Surplus Food -->
          <form method="POST" action="{{ route('school.food.claim', $item->id) }}" style="margin-top:12px">
            @csrf
            <button type="submit" class="btn w-full" style="font-size:13px;padding:10px 16px;font-weight:600;border-radius:6px;background:#1f2937;color:#ffffff;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.3);transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.5)';this.style.transform='translateY(-2px)';this.style.background='#111827'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.3)';this.style.transform='translateY(0)';this.style.background='#1f2937'" onclick="return confirm('Are you sure you want to claim this item? This will reserve it for your organization.')">
              <i class="fas fa-hand-holding-heart" style="margin-right:6px"></i> Claim Now
            </button>
          </form>
        @elseif($item->listing_type === 'free')
          <!-- Claim Free Food -->
          <form method="POST" action="{{ route('school.food.claim', $item->id) }}" style="margin-top:12px">
            @csrf
            <button type="submit" class="btn w-full" style="font-size:13px;padding:10px 16px;font-weight:600;border-radius:6px;background:#1f2937;color:#ffffff;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.3);transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.5)';this.style.transform='translateY(-2px)';this.style.background='#111827'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.3)';this.style.transform='translateY(0)';this.style.background='#1f2937'" onclick="return confirm('Are you sure you want to claim this item? This will reserve it for your organization.')">
              <i class="fas fa-hand-holding-heart" style="margin-right:6px"></i> Claim Now
            </button>
          </form>
        @elseif($item->listing_type === 'discounted')
          <!-- Redeem Discounted Food with Voucher -->
          @php
            $userVouchers = \App\Models\Voucher::where('recipient_user_id', Auth::id())
              ->where('status', 'active')
              ->where('remaining_value', '>', 0)
              ->get();
          @endphp
          @if($userVouchers->isEmpty())
            <div style="margin-top:12px;padding:10px;background:#fef3c7;border-radius:6px;text-align:center;color:#92400e;font-weight:600;font-size:12px">
              <i class="fas fa-exclamation-triangle"></i> No active vouchers available
            </div>
          @else
            <form method="POST" action="{{ route('school.food.redeem', $item->id) }}" style="margin-top:12px">
              @csrf
              <div style="margin-bottom:8px">
                <label style="font-size:12px;color:#64748b;font-weight:600;display:block;margin-bottom:4px">Select Voucher (£{{ $item->discounted_price }})</label>
                <select name="voucher_id" class="form-select" style="font-size:12px;padding:6px 8px;border:1px solid #cbd5e1;border-radius:4px;width:100%" required>
                  <option value="">-- Choose a voucher --</option>
                  @foreach($userVouchers as $voucher)
                    @if($voucher->remaining_value >= $item->discounted_price)
                      <option value="{{ $voucher->id }}">Voucher #{{ $voucher->id }} (Balance: £{{ number_format($voucher->remaining_value, 2) }})</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <button type="submit" class="btn w-full" style="font-size:13px;padding:10px 16px;font-weight:600;border-radius:6px;background:#1f2937;color:#ffffff;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.3);transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.5)';this.style.transform='translateY(-2px)';this.style.background='#111827'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.3)';this.style.transform='translateY(0)';this.style.background='#1f2937'" onclick="return confirm('Are you sure you want to redeem this item? This will deduct £{{ $item->discounted_price }} from your voucher.')">
                <i class="fas fa-hand-holding-heart" style="margin-right:6px"></i> Redeem Now
              </button>
            </form>
          @endif
        @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <!-- Pagination -->
  @if($listings->hasPages())
  <div class="flex justify-center">
    {{ $listings->appends(request()->query())->links() }}
  </div>
  @endif
@endif
@endsection
