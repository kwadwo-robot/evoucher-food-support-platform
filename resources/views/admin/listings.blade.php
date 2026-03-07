@extends('layouts.dashboard')
@section('title', 'Food Listings')

@section('content')
<div class="page-header">
    <div class="page-title">Food Listings</div>
    <div class="page-desc">All food items listed by local shops on the platform</div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">All Listings <span style="font-size:13px;font-weight:500;color:#64748b;">({{ $listings->total() ?? 0 }} total)</span></span>
        <form method="GET" style="display:flex;gap:8px;">
            <select name="status" class="form-select" style="width:140px;">
                <option value="">All Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>Collected</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-filter"></i></button>
        </form>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Shop</th>
                    <th>Qty</th>
                    <th>Expiry</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Listed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                <tr>
                    <td>
                        <div style="font-weight:600;color:#0f172a;">{{ $listing->item_name }}</div>
                        <div style="font-size:12px;color:#94a3b8;">{{ Str::limit($listing->description, 40) }}</div>
                    </td>
                    <td style="color:#64748b;">{{ $listing->shop->name ?? 'N/A' }}</td>
                    <td style="font-weight:600;">{{ $listing->quantity }}</td>
                    <td>
                        @php $days = now()->diffInDays($listing->expiry_date, false); @endphp
                        <span class="badge {{ $days < 0 ? 'badge-red' : ($days <= 1 ? 'badge-orange' : ($days <= 3 ? 'badge-yellow' : 'badge-green')) }}">
                            {{ $listing->expiry_date->format('d M Y') }}
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:13px;">{{ Str::limit($listing->collection_address, 30) }}</td>
                    <td>
                        <span class="badge {{ $listing->status === 'available' ? 'badge-green' : ($listing->status === 'reserved' ? 'badge-yellow' : ($listing->status === 'collected' ? 'badge-blue' : 'badge-red')) }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                    </td>
                    <td style="color:#64748b;font-size:12px;">{{ $listing->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">No listings found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($listings) && $listings->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">{{ $listings->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
