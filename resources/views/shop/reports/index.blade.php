@extends('layouts.dashboard')
@section('title','Shop Reports')
@section('page-title','Shop Reports')
@section('content')
<div class="page-hd">
  <h1>Shop Reports</h1>
  <p>View comprehensive statistics and reports for your shop</p>
</div>

<!-- Statistics Grid -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-list"></i></div>
    <div class="stat-label">Total Listings</div>
    <div class="stat-value">{{ $totalListings }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#dbeafe;color:#3b82f6"><i class="fas fa-check-circle"></i></div>
    <div class="stat-label">Available</div>
    <div class="stat-value">{{ $availableListings }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-check-double"></i></div>
    <div class="stat-label">Redeemed</div>
    <div class="stat-value">{{ $redeemedListings }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fee2e2;color:#ef4444"><i class="fas fa-clock"></i></div>
    <div class="stat-label">Expired</div>
    <div class="stat-value">{{ $expiredListings }}</div>
  </div>
</div>

<!-- Redeem Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
  <div class="stat-card" style="border-left:4px solid #16a34a">
    <div class="stat-label">Total Redeems</div>
    <div class="stat-value text-green-600">{{ $totalRedemptions }}</div>
    <div class="stat-sub">Items redeemed by recipients</div>
  </div>
  <div class="stat-card" style="border-left:4px solid #3b82f6">
    <div class="stat-label">Total Amount Redeemed</div>
    <div class="stat-value text-blue-600">£{{ number_format($totalAmountRedeemed ?? 0, 2) }}</div>
    <div class="stat-sub">Total voucher value used</div>
  </div>
</div>

<!-- Recent Redeems -->
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-history text-blue-500"></i> Recent Redeems</div>
  </div>
  @if(count($recentRedemptions) > 0)
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Item</th><th>Recipient</th><th>Voucher</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($recentRedemptions as $redemption)
        <tr>
          <td style="font-weight:600;color:#0f172a">{{ $redemption->foodListing->item_name ?? '—' }}</td>
          <td style="font-size:12.5px;color:#64748b">{{ $redemption->recipient->name ?? $redemption->recipient->email ?? '—' }}</td>
          <td><code style="background:#f0fdf4;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $redemption->voucher->code ?? '—' }}</code></td>
          <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($redemption->created_at)->format('d M Y H:i') }}</td>
          <td style="font-weight:600;color:#0f172a">£{{ number_format($redemption->amount_used ?? 0, 2) }}</td>
          <td>
            @if($redemption->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @elseif($redemption->status === 'confirmed')<span class="badge badge-green">Confirmed</span>
            @elseif($redemption->status === 'cancelled')<span class="badge badge-red">Cancelled</span>
            @else<span class="badge badge-gray">{{ ucfirst($redemption->status) }}</span>@endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="empty-state" style="padding:64px 24px">
    <div class="empty-icon"><i class="fas fa-chart-bar"></i></div>
    <h3>No redeems yet</h3>
    <p>Your shop's redeem history will appear here</p>
  </div>
  @endif
</div>
@endsection
