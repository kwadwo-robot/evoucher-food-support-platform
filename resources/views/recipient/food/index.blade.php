@extends('layouts.dashboard')
@section('title','Browse Food')
@section('page-title','Browse Food')
@section('content')
<div class="page-hd">
  <h1>Available Food Items</h1>
  <p>Browse food items available for redemption in Northamptonshire</p>
</div>
@if(count($listings) > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
  @foreach($listings as $item)
  <a href="{{ route('recipient.food.show', $item->id) }}" style="text-decoration:none">
    <div class="food-card">
      @if($item->image_url)
      <img src="{{ $item->image_url }}" class="food-card-img" alt="{{ $item->item_name }}">
      @else
      <div class="food-card-img-placeholder">🥗</div>
      @endif
      <div class="food-card-body">
        <div class="flex items-start justify-between mb-2">
          <div style="font-size:15px;font-weight:700;color:#0f172a">{{ $item->item_name }}</div>
          <span class="badge badge-green" style="flex-shrink:0;margin-left:8px">{{ $item->quantity }} left</span>
        </div>
        <div style="font-size:12.5px;color:#64748b;margin-bottom:10px">{{ Str::limit($item->description, 60) }}</div>
        <div class="divider" style="margin:10px 0"></div>
        <div class="flex items-center justify-between">
          <div>
            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Shop</div>
            <div style="font-size:12.5px;font-weight:600;color:#334155">{{ $item->shopUser->name ?? 'Local Shop' }}</div>
          </div>
          <div style="text-align:right">
            <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Expires</div>
            <div style="font-size:12.5px;font-weight:600;color:#ef4444">{{ \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') }}</div>
          </div>
        </div>
        <div style="margin-top:10px;padding:8px 12px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;gap:6px">
          <i class="fas fa-map-marker-alt" style="color:#16a34a;font-size:11px"></i>
          <span style="font-size:12px;color:#15803d;font-weight:500">{{ Str::limit($item->collection_address, 40) }}</span>
        </div>
      </div>
    </div>
  </a>
  @endforeach
</div>
@else
<div class="card">
  <div class="empty-state" style="padding:64px 24px">
    <div class="empty-icon"><i class="fas fa-basket-shopping"></i></div>
    <h3>No food available right now</h3>
    <p>Check back soon — local shops regularly add new near-expiry items</p>
  </div>
</div>
@endif
@endsection
