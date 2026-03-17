@extends('layouts.dashboard')
@section('title', __('app.admin_dashboard'))
@section('page-title', __('app.admin_overview'))
@section('topbar-actions')
<a href="{{ route('admin.vouchers.index') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-plus"></i> {{ __('app.issue_voucher') }}
</a>
@endsection
@section('content')
<div class="page-hd">
  <h1>{{ __('app.platform_overview') }}</h1>
  <p>{{ __('app.home_pilot_badge') }} — {{ __('app.loading') }}</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <a href="{{ route('admin.users.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="flex items-start justify-between mb-3">
      <div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-users"></i></div>
      <span class="badge badge-green">+{{ $stats['new_users_this_month'] ?? 0 }} this month</span>
    </div>
    <div class="stat-label">{{ __('app.total_users') }}</div>
    <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.vouchers.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="flex items-start justify-between mb-3">
      <div class="stat-icon" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-ticket"></i></div>
      <span class="badge badge-blue">{{ __('app.active') }}</span>
    </div>
    <div class="stat-label">{{ __('app.active_vouchers') }}</div>
    <div class="stat-value">{{ $stats['active_vouchers'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.listings.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="flex items-start justify-between mb-3">
      <div class="stat-icon" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-basket-shopping"></i></div>
      <span class="badge badge-yellow">{{ __('app.available') }}</span>
    </div>
    <div class="stat-label">{{ __('app.food_listings') }}</div>
    <div class="stat-value">{{ $stats['total_food_listings'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.donations.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="flex items-start justify-between mb-3">
      <div class="stat-icon" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-sterling-sign"></i></div>
      <span class="badge badge-green">{{ __('app.donations') }}</span>
    </div>
    <div class="stat-label">{{ __('app.total_donated') }}</div>
    <div class="stat-value">£{{ number_format($stats['total_donated'] ?? 0, 2) }}</div>
  </a>
</div>

<!-- Second row stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <a href="{{ route('admin.reports.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7;width:36px;height:36px;font-size:15px;border-radius:9px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-check-double"></i></div>
    <div class="stat-label">Redeems</div>
    <div class="stat-value" style="font-size:22px">{{ $stats['total_redemptions'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.shops.index') }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="stat-icon mb-3" style="background:#fff7ed;color:#f97316;width:36px;height:36px;font-size:15px;border-radius:9px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-store"></i></div>
    <div class="stat-label">Local Shops</div>
    <div class="stat-value" style="font-size:22px">{{ $stats['total_shops'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.users.index', ['role' => 'donor']) }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="stat-icon mb-3" style="background:#ecfeff;color:#0891b2;width:36px;height:36px;font-size:15px;border-radius:9px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-hand-holding-heart"></i></div>
    <div class="stat-label">Donors</div>
    <div class="stat-value" style="font-size:22px">{{ $stats['total_donors'] ?? 0 }}</div>
  </a>
  <a href="{{ route('admin.users.index', ['filter' => 'pending']) }}" class="stat-card hover:shadow-lg hover:scale-105 transition-all cursor-pointer">
    <div class="stat-icon mb-3" style="background:#fef2f2;color:#ef4444;width:36px;height:36px;font-size:15px;border-radius:9px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-clock"></i></div>
    <div class="stat-label">Pending Approvals</div>
    <div class="stat-value" style="font-size:22px">{{ $stats['pending_approvals'] ?? 0 }}</div>
  </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <!-- Chart -->
  <div class="card lg:col-span-2">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-chart-line text-green-600"></i> Voucher Activity (Last 6 Months)</div>
    </div>
    <div class="card-body">
      <canvas id="voucherChart" height="200"></canvas>
    </div>
  </div>
  <!-- Quick Actions -->
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-bolt text-yellow-500"></i> Quick Actions</div></div>
    <div class="card-body" style="padding:12px">
      <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-1">
        <div style="width:36px;height:36px;background:#f0fdf4;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:14px;flex-shrink:0"><i class="fas fa-users"></i></div>
        <div><div style="font-size:13px;font-weight:600;color:#0f172a">Manage Users</div><div style="font-size:11.5px;color:#94a3b8">View & approve accounts</div></div>
        <i class="fas fa-chevron-right text-slate-300 ml-auto text-xs"></i>
      </a>
      <a href="{{ route('admin.vouchers.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-1">
        <div style="width:36px;height:36px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#3b82f6;font-size:14px;flex-shrink:0"><i class="fas fa-ticket"></i></div>
        <div><div style="font-size:13px;font-weight:600;color:#0f172a">Issue Voucher</div><div style="font-size:11.5px;color:#94a3b8">Create & assign vouchers</div></div>
        <i class="fas fa-chevron-right text-slate-300 ml-auto text-xs"></i>
      </a>
      <a href="{{ route('admin.listings.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-1">
        <div style="width:36px;height:36px;background:#fef9c3;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#ca8a04;font-size:14px;flex-shrink:0"><i class="fas fa-basket-shopping"></i></div>
        <div><div style="font-size:13px;font-weight:600;color:#0f172a">Food Listings</div><div style="font-size:11.5px;color:#94a3b8">Monitor all listings</div></div>
        <i class="fas fa-chevron-right text-slate-300 ml-auto text-xs"></i>
      </a>
      <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-1">
        <div style="width:36px;height:36px;background:#f3e8ff;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#a855f7;font-size:14px;flex-shrink:0"><i class="fas fa-chart-bar"></i></div>
        <div><div style="font-size:13px;font-weight:600;color:#0f172a">Reports</div><div style="font-size:11.5px;color:#94a3b8">Pilot performance data</div></div>
        <i class="fas fa-chevron-right text-slate-300 ml-auto text-xs"></i>
      </a>
      <a href="{{ route('admin.donations.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition">
        <div style="width:36px;height:36px;background:#fff7ed;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#f97316;font-size:14px;flex-shrink:0"><i class="fas fa-credit-card"></i></div>
        <div><div style="font-size:13px;font-weight:600;color:#0f172a">Payments</div><div style="font-size:11.5px;color:#94a3b8">Donation tracking</div></div>
        <i class="fas fa-chevron-right text-slate-300 ml-auto text-xs"></i>
      </a>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Recent Food Listings -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-basket-shopping text-yellow-500"></i> Recent Food Listings</div>
      <a href="{{ route('admin.listings.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Item</th><th>Shop</th><th>Expiry</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($recentListings ?? [] as $listing)
          <tr>
            <td><div style="font-weight:600;color:#0f172a">{{ $listing->item_name }}</div></td>
            <td style="color:#64748b;font-size:12px">{{ $listing->shopUser->name ?? '—' }}</td>
            <td style="font-size:12px">{{ \Carbon\Carbon::parse($listing->expiry_date)->format('d M Y') }}</td>
            <td>
              @if($listing->status === 'available')<span class="badge badge-green">Available</span>
              @elseif($listing->status === 'reserved')<span class="badge badge-yellow">Reserved</span>
              @elseif($listing->status === 'collected')<span class="badge badge-blue">Collected</span>
              @else<span class="badge badge-gray">{{ ucfirst($listing->status) }}</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center py-8 text-slate-400">No listings yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <!-- Recent Vouchers -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-ticket text-blue-500"></i> Recent Vouchers</div>
      <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Code</th><th>Recipient</th><th>Value</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($recentVouchers ?? [] as $v)
          <tr>
            <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $v->code }}</code></td>
            <td style="font-size:12px;color:#64748b">{{ $v->recipient->name ?? '—' }}</td>
            <td style="font-weight:600">£{{ number_format($v->remaining_value, 2) }}</td>
            <td>
              @if($v->status === 'active')<span class="badge badge-green">Active</span>
              @elseif($v->status === 'redeemed')<span class="badge badge-blue">Redeemed</span>
              @elseif($v->status === 'expired')<span class="badge badge-red">Expired</span>
              @elseif($v->status === 'partially_used')<span class="badge badge-yellow">Partial</span>
              @else<span class="badge badge-gray">{{ ucfirst($v->status) }}</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center py-8 text-slate-400">No vouchers yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Recent Donations -->
<div class="card mt-6">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-heart text-red-500"></i> Recent Donations</div>
    <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary btn-sm">View All</a>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Email</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        @forelse($recentDonations ?? [] as $donation)
        <tr>
          <td style="font-size:12px;color:#64748b">{{ $donation->donor_email }}</td>
          <td style="font-weight:600">£{{ number_format($donation->amount, 2) }}</td>
          <td>
            @if($donation->status === 'completed')<span class="badge badge-green">Completed</span>
            @elseif($donation->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @elseif($donation->status === 'failed')<span class="badge badge-red">Failed</span>
            @else<span class="badge badge-gray">{{ ucfirst($donation->status) }}</span>@endif
          </td>
          <td style="font-size:12px;color:#64748b">{{ $donation->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center py-8 text-slate-400">No donations yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
@section('scripts')
<script>
const ctx = document.getElementById('voucherChart');
if(ctx){
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($voucherActivityData['months'] ?? ['Oct','Nov','Dec','Jan','Feb','Mar']) !!},
      datasets: [
        { label: 'Issued', data: {!! json_encode($voucherActivityData['issued'] ?? [0,0,0,0,0,0]) !!}, backgroundColor: 'rgba(22,163,74,.8)', borderRadius: 6 },
        { label: 'Redeemed', data: {!! json_encode($voucherActivityData['redeemed'] ?? [0,0,0,0,0,0]) !!}, backgroundColor: 'rgba(59,130,246,.8)', borderRadius: 6 }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'top', labels: { font: { family: 'Inter', size: 12 }, usePointStyle: true } } },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Inter', size: 11 } } },
        x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } }
      }
    }
  });
}
</script>
@endsection
