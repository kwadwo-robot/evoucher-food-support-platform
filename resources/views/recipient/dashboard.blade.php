@extends('layouts.dashboard')
@section('title','My Dashboard')
@section('page-title','My Dashboard')
@section('content')
<div class="page-hd">
  <h1>Welcome, {{ auth()->user()->name }}</h1>
  <p>Your eVoucher account — browse food and redeem your vouchers</p>
</div>
<!-- Voucher Balance Card -->
@if($active_vouchers->first())
<div class="voucher-card mb-6">
  <div class="flex items-start justify-between relative z-10">
    <div>
      <div style="font-size:12px;font-weight:600;opacity:.7;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px">
        @if($active_vouchers->first()->status === 'partially_used')
          Partial Voucher &nbsp;<span style="background:rgba(255,255,255,.2);padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700">Partial</span>
        @else
          Active Voucher
        @endif
      </div>
      <div style="font-size:36px;font-weight:900;line-height:1">£{{ number_format($active_vouchers->first()->remaining_value ?? $active_vouchers->first()->amount, 2) }}</div>
      <div style="font-size:13px;opacity:.8;margin-top:4px">
        @if($active_vouchers->first()->status === 'partially_used')
          Remaining Balance
        @else
          Available Balance
        @endif
      </div>
    </div>
    <div style="text-align:right">
      <code style="background:rgba(255,255,255,.15);padding:6px 14px;border-radius:8px;font-size:14px;font-weight:800;letter-spacing:.1em;display:block;margin-bottom:8px">{{ $active_vouchers->first()->code }}</code>
      <div style="font-size:11.5px;opacity:.7">Expires {{ \Carbon\Carbon::parse($active_vouchers->first()->expiry_date)->format('d M Y') }}</div>
    </div>
  </div>
  <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,.15);display:flex;gap:12px;position:relative;z-index:10">
    <a href="{{ route('recipient.food.browse') }}" class="btn" style="background:rgba(255,255,255,.2);color:#fff;flex:1;justify-content:center;backdrop-filter:blur(4px)">
      <i class="fas fa-basket-shopping"></i> Browse Food
    </a>
    <a href="{{ route('recipient.vouchers') }}" class="btn" style="background:rgba(255,255,255,.1);color:#fff;justify-content:center;backdrop-filter:blur(4px)">
      <i class="fas fa-ticket"></i> All Vouchers
    </a>
  </div>
</div>
@else
<div class="card mb-6" style="border:2px dashed #e2e8f0">
  <div class="card-body text-center" style="padding:32px">
    <div style="font-size:36px;margin-bottom:12px">🎫</div>
    <div style="font-size:15px;font-weight:700;color:#334155;margin-bottom:6px">No Active Voucher</div>
    <div style="font-size:13px;color:#94a3b8">Contact your support worker or admin to receive a voucher</div>
  </div>
</div>
@endif

<!-- Stats Row -->
<div class="grid grid-cols-3 gap-4 mb-6">
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#16a34a">{{ $totalVouchers ?? 0 }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Total Vouchers</div>
  </div>
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#3b82f6">{{ $totalRedemptions ?? 0 }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Redemptions</div>
  </div>
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#a855f7">{{ $availableItems ?? 0 }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Items Available</div>
  </div>
</div>

<!-- Available Food -->
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-basket-shopping text-yellow-500"></i> Available Food Near You</div>
    <a href="{{ route('recipient.food.browse') }}" class="btn btn-secondary btn-sm">Browse All</a>
  </div>
  <div style="padding:16px">
    @if(count($availableFood ?? []) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @foreach($availableFood as $item)
      <a href="{{ route('recipient.food.show', $item->id) }}" style="text-decoration:none">
        <div class="food-card">
          @if($item->image_url)
          <img src="{{ $item->image_url }}" class="food-card-img" alt="{{ $item->item_name }}">
          @else
          <div class="food-card-img-placeholder">🥖</div>
          @endif
          <div class="food-card-body">
            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $item->item_name }}</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:8px">{{ $item->shopUser->name ?? 'Local Shop' }}</div>
            <div class="flex items-center justify-between">
              <span style="font-size:11.5px;color:#94a3b8"><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($item->collection_address, 25) }}</span>
              <span class="badge badge-red" style="font-size:10.5px"><i class="fas fa-clock mr-1"></i>Exp: {{ \Carbon\Carbon::parse($item->expiry_date)->format('d M') }}</span>
            </div>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    @else
    <div class="empty-state">
      <div class="empty-icon"><i class="fas fa-basket-shopping"></i></div>
      <h3>No food available right now</h3>
      <p>Check back soon — shops regularly add new listings</p>
    </div>
    @endif
  </div>
</div>
@endsection
