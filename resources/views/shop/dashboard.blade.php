@extends('layouts.dashboard')
@section('title','Shop Dashboard')
@section('page-title','Shop Dashboard')
@section('topbar-actions')
<a href="{{ route('shop.listings.create') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-plus"></i> Add Listing
</a>
@endsection
@section('content')
<div class="page-hd">
  <h1>Welcome, {{ auth()->user()->name }}</h1>
  <p>Manage your food listings and track voucher redemptions</p>
</div>
<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-list"></i></div>
    <div class="stat-label">Total Listings</div>
    <div class="stat-value">{{ $totalListings ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-circle-check"></i></div>
    <div class="stat-label">Available</div>
    <div class="stat-value">{{ $availableListings ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-check-double"></i></div>
    <div class="stat-label">Redeemed</div>
    <div class="stat-value">{{ $redeemedCount ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-clock"></i></div>
    <div class="stat-label">Expiring Soon</div>
    <div class="stat-value">{{ $expiringSoon ?? 0 }}</div>
  </div>
</div>
<!-- Payout Stats -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
  <div class="stat-card" style="border-left:4px solid #16a34a">
    <div class="stat-label">Amount Owed to You</div>
    <div class="stat-value text-green-600">£{{ number_format($unpaidAmount ?? 0, 2) }}</div>
    <div class="stat-sub">Collected, not yet requested</div>
    @if(($unpaidAmount ?? 0) > 0)
    <a href="{{ route('shop.payouts.index') }}" class="btn btn-primary btn-sm mt-2" style="font-size:12px">
      <i class="fas fa-paper-plane mr-1"></i>Request Payout
    </a>
    @endif
  </div>
  <div class="stat-card" style="border-left:4px solid #f59e0b">
    <div class="stat-label">Pending Payout Requests</div>
    <div class="stat-value text-yellow-600">{{ $pendingPayouts ?? 0 }}</div>
    <div class="stat-sub">Awaiting admin payment</div>
  </div>
  <div class="stat-card" style="border-left:4px solid #3b82f6">
    <div class="stat-label">Total Paid Out</div>
    <div class="stat-value text-blue-600">£{{ number_format($totalPaidOut ?? 0, 2) }}</div>
    <div class="stat-sub">All time</div>
  </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Recent Listings -->
  <div class="card lg:col-span-2">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-basket-shopping text-yellow-500"></i> My Listings</div>
      <a href="{{ route('shop.listings.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Item</th><th>Qty</th><th>Expiry</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse($listings ?? [] as $listing)
          <tr>
            <td>
              <div style="font-weight:600;color:#0f172a">{{ $listing->item_name }}</div>
              <div style="font-size:11.5px;color:#94a3b8">{{ Str::limit($listing->collection_address, 35) }}</div>
            </td>
            <td><span class="badge badge-gray">{{ $listing->quantity }}</span></td>
            <td style="font-size:12px">
              @php $exp = \Carbon\Carbon::parse($listing->expiry_date); @endphp
              <span class="{{ $exp->isPast() ? 'text-red-500' : ($exp->diffInDays() < 3 ? 'text-yellow-600' : 'text-slate-600') }} font-medium text-xs">
                {{ $exp->format('d M Y') }}
              </span>
            </td>
            <td>
              @if($listing->status === 'available')<span class="badge badge-green">Available</span>
              @elseif($listing->status === 'reserved')<span class="badge badge-yellow">Reserved</span>
              @elseif($listing->status === 'collected')<span class="badge badge-blue">Collected</span>
              @else<span class="badge badge-gray">{{ ucfirst($listing->status) }}</span>@endif
            </td>
            <td>
              <a href="{{ route('shop.listings.edit', $listing->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-basket-shopping"></i></div><h3>No listings yet</h3><p><a href="{{ route('shop.listings.create') }}" style="color:#16a34a;font-weight:600">Add your first food listing</a></p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <!-- Recent Redemptions -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-check-circle text-green-600"></i> Recent Redemptions</div>
      <a href="{{ route('shop.redemptions') }}" class="btn btn-secondary btn-sm">All</a>
    </div>
    <div style="padding:0">
      @forelse($recentRedemptions ?? [] as $r)
      <div style="padding:14px 20px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:14px;flex-shrink:0"><i class="fas fa-check"></i></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $r->foodListing->item_name ?? 'Item' }}</div>
          <div style="font-size:11.5px;color:#94a3b8">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</div>
        </div>
        @if($r->status === 'pending')<span class="badge badge-yellow">Pending</span>
        @elseif($r->status === 'confirmed')<span class="badge badge-green">Confirmed</span>
        @else<span class="badge badge-gray">{{ ucfirst($r->status) }}</span>@endif
      </div>
      @empty
      <div class="empty-state"><div class="empty-icon"><i class="fas fa-check-circle"></i></div><h3>No redemptions yet</h3></div>
      @endforelse
    </div>
  </div>
</div>
@endsection
