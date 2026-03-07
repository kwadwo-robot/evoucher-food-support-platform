@extends('layouts.dashboard')
@section('title', 'My Food Listings')

@section('content')
<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <div class="page-title">My Food Listings</div>
        <div class="page-desc">Manage your near-expiry food items listed on the platform</div>
    </div>
    <a href="{{ route('shop.listings.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Listing
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">All Listings <span style="font-size:13px;font-weight:500;color:#64748b;">({{ $listings->total() ?? 0 }})</span></span>
        <form method="GET" style="display:flex;gap:8px;">
            <select name="status" class="form-select" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>Collected</option>
            </select>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr><th>Item</th><th>Qty</th><th>Expiry</th><th>Collection</th><th>Status</th><th>Listed</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#0f172a;">{{ $listing->item_name }}</div>
                        <div style="font-size:12px;color:#94a3b8;">{{ Str::limit($listing->description, 45) }}</div>
                    </td>
                    <td style="font-weight:600;">{{ $listing->quantity }}</td>
                    <td>
                        @php $days = now()->diffInDays($listing->expiry_date, false); @endphp
                        <span class="badge {{ $days < 0 ? 'badge-red' : ($days <= 1 ? 'badge-orange' : ($days <= 3 ? 'badge-yellow' : 'badge-green')) }}">
                            {{ $listing->expiry_date->format('d M Y') }}
                            @if($days >= 0 && $days <= 3) ({{ $days }}d) @endif
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:13px;">
                        <div>{{ Str::limit($listing->collection_address, 30) }}</div>
                        <div style="font-size:11.5px;color:#94a3b8;">{{ $listing->collection_time }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $listing->status === 'available' ? 'badge-green' : ($listing->status === 'reserved' ? 'badge-yellow' : ($listing->status === 'collected' ? 'badge-blue' : 'badge-red')) }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:12px;">{{ $listing->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('shop.listings.edit', $listing->id) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i></a>
                            @if($listing->status === 'reserved')
                            <form method="POST" action="{{ route('shop.listings.mark-collected', $listing->id) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm">Mark Collected</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('shop.listings.destroy', $listing->id) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this listing?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:56px;color:#94a3b8;">
                    <i class="fas fa-boxes-stacked" style="font-size:36px;color:#e2e8f0;display:block;margin-bottom:12px;"></i>
                    <div style="font-weight:600;margin-bottom:6px;">No listings yet</div>
                    <a href="{{ route('shop.listings.create') }}" class="btn btn-primary" style="margin-top:8px;">Add Your First Listing</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($listings) && $listings->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">{{ $listings->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
