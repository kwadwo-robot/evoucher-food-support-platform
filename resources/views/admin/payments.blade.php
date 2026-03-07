@extends('layouts.dashboard')
@section('title','Payments')
@section('page-title','Payment Tracking')
@section('content')
<div class="page-hd">
  <h1>Donation Payments</h1>
  <p>Track all donations from VCFSE organisations, schools, and care organisations</p>
</div>
<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-sterling-sign"></i></div>
    <div class="stat-label">Total Received</div>
    <div class="stat-value">£{{ number_format($totalDonated ?? 0, 2) }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-check-circle"></i></div>
    <div class="stat-label">Completed</div>
    <div class="stat-value">{{ $completedCount ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-clock"></i></div>
    <div class="stat-label">Pending</div>
    <div class="stat-value">{{ $pendingPayments ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-building"></i></div>
    <div class="stat-label">Donors</div>
    <div class="stat-value">{{ $donorCount ?? 0 }}</div>
  </div>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-credit-card text-green-600"></i> All Donations</div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Donor</th><th>Organisation</th><th>Amount</th><th>Reference</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($payments ?? [] as $p)
        <tr>
          <td>
            <div style="font-weight:600;color:#0f172a;font-size:13px">{{ $p->donor->name ?? '—' }}</div>
            <div style="font-size:11.5px;color:#94a3b8">{{ $p->donor->email ?? '' }}</div>
          </td>
          <td style="font-size:12.5px;color:#64748b">{{ $p->donor->role === 'vcfse' ? 'VCFSE' : 'School/Care' }}</td>
          <td style="font-weight:700;color:#0f172a;font-size:14px">£{{ number_format($p->amount, 2) }}</td>
          <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:11px">{{ $p->stripe_payment_id ?? $p->reference ?? 'N/A' }}</code></td>
          <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}</td>
          <td>
            @if($p->status === 'completed')<span class="badge badge-green">Completed</span>
            @elseif($p->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @elseif($p->status === 'failed')<span class="badge badge-red">Failed</span>
            @else<span class="badge badge-gray">{{ ucfirst($p->status) }}</span>@endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-credit-card"></i></div><h3>No payments yet</h3><p>Donations will appear here once organisations start contributing</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
