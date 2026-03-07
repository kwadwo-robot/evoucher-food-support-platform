@extends('layouts.dashboard')
@section('title','Food Listings')
@section('page-title','Food Listings')
@section('content')
<div class="page-hd">
  <h1>Food Listings</h1>
  <p>Monitor all food items listed by local shops across Northamptonshire</p>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-basket-shopping text-yellow-500"></i> All Listings <span class="badge badge-gray ml-2">{{ count($listings) }}</span></div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Item</th><th>Shop</th><th>Qty</th><th>Expiry</th><th>Location</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($listings as $listing)
        <tr>
          <td>
            <div style="font-weight:600;color:#0f172a">{{ $listing->item_name }}</div>
            <div style="font-size:11.5px;color:#94a3b8">{{ Str::limit($listing->description, 40) }}</div>
          </td>
          <td style="font-size:12.5px;color:#64748b">{{ $listing->shopUser->name ?? '—' }}</td>
          <td><span class="badge badge-gray">{{ $listing->quantity }}</span></td>
          <td style="font-size:12px">
            @php $exp = \Carbon\Carbon::parse($listing->expiry_date); @endphp
            <span class="{{ $exp->isPast() ? 'text-red-500' : ($exp->diffInDays() < 3 ? 'text-yellow-600' : 'text-slate-600') }} font-medium text-xs">
              {{ $exp->format('d M Y') }}
            </span>
          </td>
          <td style="font-size:12px;color:#64748b">{{ Str::limit($listing->collection_address, 30) }}</td>
          <td>
            @if($listing->status === 'available')<span class="badge badge-green">Available</span>
            @elseif($listing->status === 'reserved')<span class="badge badge-yellow">Reserved</span>
            @elseif($listing->status === 'collected')<span class="badge badge-blue">Collected</span>
            @elseif($listing->status === 'expired')<span class="badge badge-red">Expired</span>
            @else<span class="badge badge-gray">{{ ucfirst($listing->status) }}</span>@endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-basket-shopping"></i></div><h3>No listings yet</h3><p>Shops haven't added any food items yet</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
