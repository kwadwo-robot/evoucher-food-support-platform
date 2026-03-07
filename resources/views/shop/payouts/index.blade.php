@extends('layouts.dashboard')
@section('title','My Payouts')
@section('page-title','My Payouts')
@section('content')
<div class="page-hd">
  <h1>My Payouts</h1>
  <p>Track payments owed to you for redeemed food items and submit payout requests</p>
</div>

@if(session('success'))
<div class="alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif

{{-- Summary Cards --}}
<div class="stats-grid mb-6" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;display:grid">
  <div class="stat-card">
    <div class="stat-label">Amount Owed</div>
    <div class="stat-value text-green-600">£{{ number_format($unpaidTotal, 2) }}</div>
    <div class="stat-sub">{{ $unpaidRedemptions->count() }} collected item(s)</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Pending Requests</div>
    <div class="stat-value text-yellow-600">£{{ number_format($totalPending, 2) }}</div>
    <div class="stat-sub">Awaiting admin payment</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Paid Out</div>
    <div class="stat-value text-blue-600">£{{ number_format($totalPaid, 2) }}</div>
    <div class="stat-sub">All time</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px" class="responsive-grid">

  {{-- Bank Details Form --}}
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-university text-blue-600"></i> Bank Details</div>
    </div>
    <div class="card-body">
      @if($bankDetails)
      <div class="alert-success mb-4" style="font-size:13px">
        <i class="fas fa-check-circle mr-1"></i> Bank details saved. You can update them below.
      </div>
      @else
      <div class="alert-error mb-4" style="font-size:13px">
        <i class="fas fa-exclamation-triangle mr-1"></i> Please save your bank details before requesting a payout.
      </div>
      @endif

      <form method="POST" action="{{ route('shop.payouts.bank-details') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Account Holder Name *</label>
          <input type="text" name="account_holder_name" class="form-control @error('account_holder_name') is-invalid @enderror"
            value="{{ old('account_holder_name', $bankDetails->account_holder_name ?? '') }}" required placeholder="Full name on bank account">
          @error('account_holder_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">Bank Name *</label>
          <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
            value="{{ old('bank_name', $bankDetails->bank_name ?? '') }}" required placeholder="e.g. Barclays, HSBC, Lloyds">
          @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group">
            <label class="form-label">Sort Code * <small class="text-muted">(12-34-56)</small></label>
            <input type="text" name="sort_code" class="form-control @error('sort_code') is-invalid @enderror"
              value="{{ old('sort_code', $bankDetails->sort_code ?? '') }}" required placeholder="12-34-56" maxlength="8">
            @error('sort_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Account Number * <small class="text-muted">(8 digits)</small></label>
            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
              value="{{ old('account_number', $bankDetails->account_number ?? '') }}" required placeholder="12345678" maxlength="8">
            @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Payment Reference <small class="text-muted">(optional)</small></label>
          <input type="text" name="bank_reference" class="form-control"
            value="{{ old('bank_reference', $bankDetails->bank_reference ?? '') }}" placeholder="e.g. your shop name or invoice ref">
        </div>
        <button type="submit" class="btn btn-primary w-full">
          <i class="fas fa-save mr-2"></i>{{ $bankDetails ? 'Update Bank Details' : 'Save Bank Details' }}
        </button>
      </form>
    </div>
  </div>

  {{-- Request Payout --}}
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-money-bill-transfer text-green-600"></i> Request Payout</div>
    </div>
    <div class="card-body">
      @if($unpaidRedemptions->isEmpty())
        <div class="empty-state" style="padding:30px 0">
          <div class="empty-icon"><i class="fas fa-check-double"></i></div>
          <h3>No Unpaid Redemptions</h3>
          <p>All your collected redemptions have been included in a payout request.</p>
        </div>
      @else
        <p style="font-size:13px;color:#6b7280;margin-bottom:16px">
          The following collected items have not yet been included in a payout request.
          Click <strong>Request Payout</strong> to submit them all to the admin for payment.
        </p>
        <div style="overflow-x:auto;margin-bottom:16px">
          <table class="data-table" style="font-size:13px">
            <thead>
              <tr>
                <th>Item</th>
                <th>Collected</th>
                <th style="text-align:right">Amount</th>
              </tr>
            </thead>
            <tbody>
              @foreach($unpaidRedemptions as $r)
              @php $amount = $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0); @endphp
              <tr>
                <td>{{ $r->foodListing->item_name ?? 'Unknown Item' }}</td>
                <td>{{ $r->redeemed_at ? $r->redeemed_at->format('d M Y') : '—' }}</td>
                <td style="text-align:right;font-weight:600;color:#16a34a">£{{ number_format($amount, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr style="background:#f0fdf4">
                <td colspan="2" style="font-weight:700;padding:10px 12px">Total to Request</td>
                <td style="text-align:right;font-weight:700;color:#16a34a;padding:10px 12px">£{{ number_format($unpaidTotal, 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        @if($bankDetails)
        <form method="POST" action="{{ route('shop.payouts.request') }}">
          @csrf
          <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px">
            <div style="font-weight:600;margin-bottom:4px"><i class="fas fa-university mr-1 text-green-700"></i> Payment will be sent to:</div>
            <div>{{ $bankDetails->account_holder_name }} — {{ $bankDetails->bank_name }}</div>
            <div>Sort Code: {{ $bankDetails->sort_code }} | Account: ****{{ substr($bankDetails->account_number, -4) }}</div>
          </div>
          <button type="submit" class="btn btn-primary w-full" onclick="return confirm('Submit payout request for £{{ number_format($unpaidTotal, 2) }}?')">
            <i class="fas fa-paper-plane mr-2"></i>Request Payout — £{{ number_format($unpaidTotal, 2) }}
          </button>
        </form>
        @else
        <div class="alert-error" style="font-size:13px">
          <i class="fas fa-exclamation-triangle mr-1"></i> Please save your bank details first (on the left) before requesting a payout.
        </div>
        @endif
      @endif
    </div>
  </div>
</div>

{{-- Payout History --}}
<div class="card mt-6">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-history text-slate-600"></i> Payout History</div>
    <span class="badge badge-gray">{{ $payoutRequests->count() }} requests</span>
  </div>
  @if($payoutRequests->isEmpty())
    <div class="empty-state" style="padding:40px 0">
      <div class="empty-icon"><i class="fas fa-money-bill-wave"></i></div>
      <h3>No Payout Requests Yet</h3>
      <p>Once you submit a payout request, it will appear here with its status.</p>
    </div>
  @else
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Items</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Payment Ref</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payoutRequests as $p)
        <tr>
          <td><code style="font-size:12px">PR-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</code></td>
          <td>{{ $p->redemption_count }} item(s)</td>
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
          <td>
            @if($p->payment_reference)
              <code style="font-size:12px">{{ $p->payment_reference }}</code>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td style="font-size:13px">
            {{ $p->paid_at ? $p->paid_at->format('d M Y') : $p->created_at->format('d M Y') }}
          </td>
          <td>
            <a href="{{ route('shop.payouts.show', $p->id) }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-eye"></i> View
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

<style>
.responsive-grid { grid-template-columns: 1fr 1fr; }
@media(max-width:768px){ .responsive-grid { grid-template-columns: 1fr !important; } }
</style>
@endsection
