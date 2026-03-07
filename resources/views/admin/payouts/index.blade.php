@extends('layouts.dashboard')
@section('title','Shop Payouts')
@section('page-title','Shop Payouts')
@section('content')
<div class="page-hd">
  <h1>Shop Payouts</h1>
  <p>Review and process bank transfer payments to local shops for redeemed food items</p>
</div>

@if(session('success'))
<div class="alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid mb-6" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;display:grid">
  <div class="stat-card">
    <div class="stat-label">Pending</div>
    <div class="stat-value text-yellow-600">{{ $stats['pending'] }}</div>
    <div class="stat-sub">Awaiting review</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Approved</div>
    <div class="stat-value text-blue-600">{{ $stats['approved'] }}</div>
    <div class="stat-sub">Ready to pay</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Paid</div>
    <div class="stat-value text-green-600">{{ $stats['paid'] }}</div>
    <div class="stat-sub">Completed</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Pending Amount</div>
    <div class="stat-value text-orange-600">£{{ number_format($stats['total_pending_amount'], 2) }}</div>
    <div class="stat-sub">To be paid</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Paid Out</div>
    <div class="stat-value text-green-600">£{{ number_format($stats['total_paid_amount'], 2) }}</div>
    <div class="stat-sub">All time</div>
  </div>
</div>

{{-- Filter Tabs --}}
<div class="card">
  <div class="card-hd" style="flex-wrap:wrap;gap:8px">
    <div class="card-title"><i class="fas fa-money-bill-transfer text-green-600"></i> Payout Requests</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      @foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','paid'=>'Paid','rejected'=>'Rejected'] as $val=>$label)
      <a href="{{ route('admin.payouts.index', ['status'=>$val]) }}"
         class="btn btn-sm {{ $status===$val ? 'btn-primary' : 'btn-secondary' }}">
        {{ $label }}
        @if($val !== 'all' && isset($stats[$val]))
          <span class="badge badge-gray ml-1">{{ $stats[$val] }}</span>
        @endif
      </a>
      @endforeach
    </div>
  </div>

  @if($payouts->isEmpty())
    <div class="empty-state" style="padding:50px 0">
      <div class="empty-icon"><i class="fas fa-money-bill-wave"></i></div>
      <h3>No Payout Requests</h3>
      <p>No payout requests match the selected filter.</p>
    </div>
  @else
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Ref</th>
          <th>Shop</th>
          <th>Items</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payouts as $p)
        <tr>
          <td><code style="font-size:12px">PR-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</code></td>
          <td>
            <div style="font-weight:600">{{ $p->shop->shopProfile->shop_name ?? $p->shop->name ?? 'Unknown Shop' }}</div>
            <div style="font-size:12px;color:#6b7280">{{ $p->shop->email ?? '' }}</div>
          </td>
          <td>{{ $p->redemption_count }}</td>
          <td style="font-weight:700;color:#16a34a">£{{ number_format($p->total_amount, 2) }}</td>
          <td>
            @if($p->status === 'pending')
              <span class="badge badge-yellow">Pending</span>
            @elseif($p->status === 'approved')
              <span class="badge badge-blue">Approved</span>
            @elseif($p->status === 'paid')
              <span class="badge badge-green">Paid</span>
            @elseif($p->status === 'rejected')
              <span class="badge badge-red">Rejected</span>
            @endif
          </td>
          <td style="font-size:13px">{{ $p->created_at->format('d M Y') }}</td>
          <td>
            <a href="{{ route('admin.payouts.show', $p->id) }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-eye"></i> View
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="padding:16px">
    {{ $payouts->appends(['status'=>$status])->links() }}
  </div>
  @endif
</div>
@endsection
