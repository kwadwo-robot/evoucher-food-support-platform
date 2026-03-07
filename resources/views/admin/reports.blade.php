@extends('layouts.dashboard')
@section('title','Reports')
@section('page-title','Platform Reports')
@section('content')
<div class="page-hd">
  <h1>Pilot Reports</h1>
  <p>Northamptonshire eVoucher Food Support Platform — Performance Analytics</p>
</div>
<!-- KPI Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-sterling-sign"></i></div>
    <div class="stat-label">Total Donated</div>
    <div class="stat-value">£{{ number_format($totalDonated ?? 0, 2) }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-ticket"></i></div>
    <div class="stat-label">Vouchers Issued</div>
    <div class="stat-value">{{ $totalVouchers ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-check-double"></i></div>
    <div class="stat-label">Redemptions</div>
    <div class="stat-value">{{ $totalRedemptions ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-basket-shopping"></i></div>
    <div class="stat-label">Food Items Listed</div>
    <div class="stat-value">{{ $totalListings ?? 0 }}</div>
  </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
  <!-- Participation -->
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-users text-green-600"></i> Platform Participation</div></div>
    <div class="card-body">
      <div class="space-y-4">
        <div>
          <div class="flex justify-between mb-1">
            <span style="font-size:13px;font-weight:600;color:#334155">Recipients</span>
            <span style="font-size:13px;font-weight:700;color:#16a34a">{{ $totalRecipients ?? 0 }}</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:{{ min(100, ($totalRecipients ?? 0) * 10) }}%"></div></div>
        </div>
        <div>
          <div class="flex justify-between mb-1">
            <span style="font-size:13px;font-weight:600;color:#334155">Local Shops</span>
            <span style="font-size:13px;font-weight:700;color:#3b82f6">{{ $totalShops ?? 0 }}</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:{{ min(100, ($totalShops ?? 0) * 20) }}%;background:#3b82f6"></div></div>
        </div>
        <div>
          <div class="flex justify-between mb-1">
            <span style="font-size:13px;font-weight:600;color:#334155">Donor Organisations</span>
            <span style="font-size:13px;font-weight:700;color:#a855f7">{{ $totalDonors ?? 0 }}</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:{{ min(100, ($totalDonors ?? 0) * 20) }}%;background:#a855f7"></div></div>
        </div>
        <div>
          <div class="flex justify-between mb-1">
            <span style="font-size:13px;font-weight:600;color:#334155">Redemption Rate</span>
            <span style="font-size:13px;font-weight:700;color:#f97316">
              {{ $totalVouchers > 0 ? round(($totalRedemptions / $totalVouchers) * 100) : 0 }}%
            </span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:{{ $totalVouchers > 0 ? min(100, round(($totalRedemptions / $totalVouchers) * 100)) : 0 }}%;background:#f97316"></div></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Chart -->
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-chart-doughnut text-blue-500"></i> Voucher Status Distribution</div></div>
    <div class="card-body" style="display:flex;align-items:center;justify-content:center;min-height:200px">
      <canvas id="statusChart" width="200" height="200"></canvas>
    </div>
  </div>
</div>
<!-- Monthly Donations Table -->
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-table text-slate-500"></i> Monthly Donation Summary</div>
    <a href="{{ route('admin.reports.index') }}?export=csv" class="btn btn-secondary btn-sm">
      <i class="fas fa-download"></i> Export CSV
    </a>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Month</th><th>Donations</th><th>Amount</th><th>Vouchers Issued</th><th>Redemptions</th></tr></thead>
      <tbody>
        @forelse($monthlyData ?? [] as $row)
        <tr>
          <td style="font-weight:600">{{ $row['month'] }}</td>
          <td>{{ $row['donations'] }}</td>
          <td style="font-weight:700;color:#16a34a">£{{ number_format($row['amount'], 2) }}</td>
          <td>{{ $row['vouchers'] }}</td>
          <td>{{ $row['redemptions'] }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center py-6 text-slate-400">No data available yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
@section('scripts')
<script>
const ctx2 = document.getElementById('statusChart');
if(ctx2){
  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ['Active','Redeemed','Expired','Cancelled'],
      datasets: [{
        data: [{{ $voucherStats['active'] ?? 0 }}, {{ $voucherStats['redeemed'] ?? 0 }}, {{ $voucherStats['expired'] ?? 0 }}, {{ $voucherStats['cancelled'] ?? 0 }}],
        backgroundColor: ['#16a34a','#3b82f6','#ef4444','#94a3b8'],
        borderWidth: 0, hoverOffset: 4
      }]
    },
    options: {
      responsive: false,
      plugins: {
        legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 }, usePointStyle: true, padding: 16 } }
      },
      cutout: '65%'
    }
  });
}
</script>
@endsection
