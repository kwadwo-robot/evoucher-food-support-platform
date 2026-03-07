@extends('layouts.dashboard')
@section('title','My Listings')
@section('page-title','My Food Listings')
@section('topbar-actions')
<a href="{{ route('shop.listings.create') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-plus"></i> Add Listing
</a>
@endsection
@section('content')
<div class="page-hd">
  <h1>My Food Listings</h1>
  <p>All food items you have listed on the platform</p>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-list text-green-600"></i> Listings <span class="badge badge-gray ml-2">{{ count($listings) }}</span></div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>Expiry</th><th>Value</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($listings as $listing)
        <tr>
          <td>
            <div style="font-weight:600;color:#0f172a">{{ $listing->item_name }}</div>
            <div style="font-size:11.5px;color:#94a3b8">{{ Str::limit($listing->description, 40) }}</div>
          </td>
          <td>
            @if($listing->listing_type === 'free')
              <span class="badge badge-green"><i class="fas fa-gift"></i> Free</span>
            @elseif($listing->listing_type === 'discounted')
              <span class="badge badge-blue"><i class="fas fa-tag"></i> Food to Go</span>
            @elseif($listing->listing_type === 'surplus')
              <span class="badge badge-purple"><i class="fas fa-boxes"></i> Surplus</span>
            @else
              <span class="badge badge-gray">{{ ucfirst($listing->listing_type ?? 'free') }}</span>
            @endif
          </td>
          <td><span class="badge badge-gray">{{ $listing->quantity }}</span></td>
          <td>
            @php $exp = \Carbon\Carbon::parse($listing->expiry_date); @endphp
            <span class="{{ $exp->isPast() ? 'text-red-500' : ($exp->diffInDays() < 3 ? 'text-yellow-600' : 'text-slate-600') }} font-medium text-xs">
              {{ $exp->format('d M Y') }}
            </span>
          </td>
          <td style="font-weight:600;color:#16a34a">{{ $listing->voucher_value > 0 ? '£'.number_format($listing->voucher_value,2) : 'Free' }}</td>
          <td>
            @if($listing->status === 'available')<span class="badge badge-green">Available</span>
            @elseif($listing->status === 'reserved')<span class="badge badge-yellow">Reserved</span>
            @elseif($listing->status === 'collected')<span class="badge badge-blue">Collected</span>
            @elseif($listing->status === 'expired')<span class="badge badge-red">Expired</span>
            @else<span class="badge badge-gray">{{ ucfirst($listing->status) }}</span>@endif
          </td>
          <td>
            <div class="flex items-center gap-2">
              <a href="{{ route('shop.listings.edit', $listing->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-edit"></i> Edit
              </a>
              <form method="POST" action="{{ route('shop.listings.destroy', $listing->id) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this listing?')">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6">
          <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-basket-shopping"></i></div>
            <h3>No listings yet</h3>
            <p>Start by adding your first food item</p>
            <a href="{{ route('shop.listings.create') }}" class="btn btn-primary mt-4"><i class="fas fa-plus"></i> Add First Listing</a>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
