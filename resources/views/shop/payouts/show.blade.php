@extends('layouts.dashboard')
@section('title','Payout Request')
@section('page-title','Payout Request Detail')
@section('content')
<div class="page-hd">
  <div>
    <a href="{{ route('shop.payouts.index') }}" class="btn btn-secondary btn-sm mb-2">
      <i class="fas fa-arrow-left mr-1"></i> Back to Payouts
    </a>
    <h1>Payout Request — PR-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</h1>
    <p>Submitted {{ $payout->created_at->format('d M Y, H:i') }}</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px" class="responsive-grid">
  {{-- Redemptions included --}}
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-basket-shopping text-green-600"></i> Items Included</div>
      <span class="badge badge-gray">{{ $payout->redemptions->count() }} items</span>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Food Item</th>
            <th>Collected</th>
            <th style="text-align:right">Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payout->redemptions as $r)
          @php $amount = $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0); @endphp
          <tr>
            <td>{{ $r->foodListing->item_name ?? 'Unknown Item' }}</td>
            <td style="font-size:13px">{{ $r->redeemed_at ? $r->redeemed_at->format('d M Y') : '—' }}</td>
            <td style="text-align:right;font-weight:600;color:#16a34a">£{{ number_format($amount, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:#f0fdf4">
            <td colspan="2" style="font-weight:700;padding:10px 12px">Total</td>
            <td style="text-align:right;font-weight:700;color:#16a34a;padding:10px 12px">£{{ number_format($payout->total_amount, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- Status & Details --}}
  <div>
    <div class="card mb-4">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-info-circle text-blue-600"></i> Status</div>
      </div>
      <div class="card-body">
        <div style="margin-bottom:12px">
          @if($payout->status === 'pending')
            <span class="badge badge-yellow" style="font-size:14px;padding:6px 14px">Pending Review</span>
          @elseif($payout->status === 'approved')
            <span class="badge badge-blue" style="font-size:14px;padding:6px 14px">Approved</span>
          @elseif($payout->status === 'paid')
            <span class="badge badge-green" style="font-size:14px;padding:6px 14px">Paid</span>
          @elseif($payout->status === 'rejected')
            <span class="badge badge-red" style="font-size:14px;padding:6px 14px">Rejected</span>
          @endif
        </div>

        @if($payout->status === 'paid')
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;font-size:13px">
          <div style="font-weight:600;margin-bottom:4px"><i class="fas fa-check-circle text-green-600 mr-1"></i> Payment Confirmed</div>
          @if($payout->payment_reference)
          <div>Reference: <strong>{{ $payout->payment_reference }}</strong></div>
          @endif
          @if($payout->paid_at)
          <div>Paid on: {{ $payout->paid_at->format('d M Y, H:i') }}</div>
          @endif
        </div>
        @elseif($payout->status === 'rejected')
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px;font-size:13px">
          <div style="font-weight:600;margin-bottom:4px"><i class="fas fa-times-circle text-red-600 mr-1"></i> Request Rejected</div>
          @if($payout->admin_notes)
          <div>Reason: {{ $payout->admin_notes }}</div>
          @endif
        </div>
        @elseif($payout->status === 'pending')
        <p style="font-size:13px;color:#6b7280">Your payout request is under review. The admin will process your bank transfer shortly.</p>
        @elseif($payout->status === 'approved')
        <p style="font-size:13px;color:#6b7280">Your request has been approved. Bank transfer is being processed.</p>
        @endif

        @if($payout->admin_notes && $payout->status !== 'rejected')
        <div style="margin-top:12px;font-size:13px;color:#6b7280">
          <strong>Admin Notes:</strong> {{ $payout->admin_notes }}
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-university text-slate-600"></i> Summary</div>
      </div>
      <div class="card-body" style="font-size:13px">
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span class="text-muted">Request ID</span>
          <strong>PR-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span class="text-muted">Items</span>
          <strong>{{ $payout->redemption_count }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span class="text-muted">Total Amount</span>
          <strong style="color:#16a34a">£{{ number_format($payout->total_amount, 2) }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span class="text-muted">Submitted</span>
          <strong>{{ $payout->created_at->format('d M Y') }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.responsive-grid { grid-template-columns: 2fr 1fr; }
@media(max-width:768px){ .responsive-grid { grid-template-columns: 1fr !important; } }
</style>
@endsection
