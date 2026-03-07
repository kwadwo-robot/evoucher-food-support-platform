@extends('layouts.dashboard')
@section('title','Browse Food')
@section('page-title','Browse Food')
@section('content')
<div class="page-hd">
  <h1>Browse Food</h1>
  <p>View free, discounted and surplus food listings available for VCFSE collection in Northamptonshire.</p>
</div>

<!-- Filter Bar -->
<div class="card mb-6">
  <div class="card-body" style="padding:16px 20px">
    <form method="GET" action="{{ route('vcfse.food') }}" class="flex flex-wrap gap-3 items-end">
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
        <a href="{{ route('vcfse.food') }}" class="btn btn-secondary ml-2">Clear</a>
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
    <span>Surplus food for VCFSE collection only</span>
  </div>
</div>

@if($listings->isEmpty())
  <div class="empty-state">
    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
    <h3>No Listings Available</h3>
    <p>There are no free or surplus food listings available at the moment. Check back soon.</p>
  </div>
@else
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
    @foreach($listings as $item)
    <div class="food-card">
      @if($item->image_url)
        <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="food-card-img">
      @else
        <div class="food-card-img-placeholder">🥦</div>
      @endif
      <div class="food-card-body">
        <!-- Type Badge -->
        <div class="mb-2">
          @if($item->listing_type === 'surplus')
            <span class="badge badge-purple"><i class="fas fa-boxes-stacked"></i> Surplus</span>
          @elseif($item->listing_type === 'discounted')
            <span class="badge badge-blue"><i class="fas fa-tag"></i> Discounted</span>
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
          <div class="flex items-center gap-1 mb-1">
            <i class="fas fa-calendar-alt" style="width:14px"></i>
            Expires: {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}
          </div>
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
        <!-- Surplus Allocation Timer -->
        @if($item->listing_type === 'surplus')
          @php
            $allocation = \App\Models\SurplusAllocation::where('food_listing_id', $item->id)
              ->where('status', 'pending')
              ->first();
            $timeRemaining = $allocation ? $allocation->getTimeRemainingMinutes() : null;
            $isExpired = $allocation ? $allocation->isExpired() : false;
          @endphp
          @if($allocation && !$isExpired)
            <div class="badge badge-orange w-full justify-center mb-2" style="display:flex;background:#fed7aa;color:#92400e;border:1px solid #fdba74">
              <i class="fas fa-hourglass-end"></i> 
              @if($timeRemaining > 0)
                {{ $timeRemaining }} mins remaining
              @else
                Expires soon!
              @endif
            </div>
          @elseif($isExpired)
            <div class="badge badge-red w-full justify-center mb-2" style="display:flex">
              <i class="fas fa-times-circle"></i> Allocation Expired
            </div>
          @endif
        @else
          <!-- Expiry Warning -->
          @php $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->expiry_date), false); @endphp
          @if($daysLeft <= 2)
            <div class="badge badge-red w-full justify-center mb-2" style="display:flex">
              <i class="fas fa-exclamation-triangle"></i> Expires in {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }}!
            </div>
          @elseif($daysLeft <= 5)
            <div class="badge badge-yellow w-full justify-center mb-2" style="display:flex">
              <i class="fas fa-clock"></i> Expires in {{ $daysLeft }} days
            </div>
          @endif
        @endif
        <!-- Collection Time -->
        @if($item->collection_time)
        <div style="font-size:12px;color:#64748b;margin-bottom:10px">
          <i class="fas fa-clock"></i> Collection: {{ $item->collection_time }}
        </div>
        @endif
        <div class="flex items-center justify-between gap-2">
          <span style="font-size:18px;font-weight:800;color:#16a34a">FREE</span>
          @if($item->listing_type === 'surplus')
            @php
              $allocation = \App\Models\SurplusAllocation::where('food_listing_id', $item->id)
                ->where('status', 'pending')
                ->first();
            @endphp
            @if($allocation && !$allocation->isExpired())
              <form method="POST" action="{{ route('vcfse.food.claim', $item->id) }}" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success" style="font-size:11px;padding:6px 12px" onclick="return confirm('Are you sure you want to claim this item?')">
                  <i class="fas fa-check"></i> Claim
                </button>
              </form>
            @endif
          @endif
          <span style="font-size:11px;color:#94a3b8">{{ $item->listing_type === 'surplus' ? 'VCFSE Collection' : 'Available to All' }}</span>
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

<script>
// Claim button functionality is now handled by form submission
</script>
