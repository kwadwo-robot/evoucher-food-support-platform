@extends('layouts.dashboard')
@section('title','Payout Request')
@section('page-title','Payout Request Detail')
@section('content')
<div class="page-hd">
  <div>
    <a href="{{ route('admin.payouts.index') }}" class="btn btn-secondary btn-sm mb-2">
      <i class="fas fa-arrow-left mr-1"></i> Back to Payouts
    </a>
    <h1>Payout Request — PR-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}</h1>
    <p>Submitted {{ $payout->created_at->format('d M Y, H:i') }} by {{ $payout->shop->shopProfile->shop_name ?? $payout->shop->name }}</p>
  </div>
</div>

@if(session('success'))
<div class="alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px" class="responsive-grid">

  {{-- Left: Redemptions + Bank Details --}}
  <div>
    {{-- Bank Details --}}
    <div class="card mb-4">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-university text-blue-600"></i> Bank Details for Transfer</div>
      </div>
      <div class="card-body">
        @if($bankDetails)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;font-size:14px">
          <div>
            <div class="text-muted" style="font-size:12px;margin-bottom:2px">Account Holder</div>
            <div style="font-weight:600">{{ $bankDetails->account_holder_name }}</div>
          </div>
          <div>
            <div class="text-muted" style="font-size:12px;margin-bottom:2px">Bank Name</div>
            <div style="font-weight:600">{{ $bankDetails->bank_name }}</div>
          </div>
          <div>
            <div class="text-muted" style="font-size:12px;margin-bottom:2px">Sort Code</div>
            <div style="font-weight:600;font-family:monospace">{{ $bankDetails->sort_code }}</div>
          </div>
          <div>
            <div class="text-muted" style="font-size:12px;margin-bottom:2px">Account Number</div>
            <div style="font-weight:600;font-family:monospace">{{ $bankDetails->account_number }}</div>
          </div>
          @if($bankDetails->bank_reference)
          <div>
            <div class="text-muted" style="font-size:12px;margin-bottom:2px">Payment Reference</div>
            <div style="font-weight:600">{{ $bankDetails->bank_reference }}</div>
          </div>
          @endif
        </div>
        @else
        <div class="alert-error" style="font-size:13px">
          <i class="fas fa-exclamation-triangle mr-1"></i> This shop has not saved bank details yet. Contact them before processing.
        </div>
        @endif
      </div>
    </div>

    {{-- Redemptions --}}
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-basket-shopping text-green-600"></i> Redeemed Items</div>
        <span class="badge badge-gray">{{ $payout->redemptions->count() }} items</span>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Food Item</th>
              <th>Collected</th>
              <th style="text-align:right">Voucher Value</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payout->redemptions as $r)
            @php $amount = $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0); @endphp
            <tr>
              <td>{{ $r->foodListing->item_name ?? 'Unknown Item' }}</td>
              <td style="font-size:13px">{{ $r->redeemed_at ? $r->redeemed_at->format('d M Y, H:i') : '—' }}</td>
              <td style="text-align:right;font-weight:600;color:#16a34a">£{{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background:#f0fdf4">
              <td colspan="2" style="font-weight:700;padding:10px 12px">Total to Pay</td>
              <td style="text-align:right;font-weight:700;color:#16a34a;padding:10px 12px;font-size:16px">
                £{{ number_format($payout->total_amount, 2) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Right: Status & Actions --}}
  <div>
    {{-- Current Status --}}
    <div class="card mb-4">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-info-circle text-blue-600"></i> Current Status</div>
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
        @if($payout->paid_at)
        <div style="font-size:13px;color:#6b7280">Paid on: {{ $payout->paid_at->format('d M Y, H:i') }}</div>
        @endif
        @if($payout->payment_reference)
        <div style="font-size:13px;margin-top:4px">Payment Ref: <code>{{ $payout->payment_reference }}</code></div>
        @endif
        @if($payout->admin_notes)
        <div style="font-size:13px;margin-top:8px;padding:8px;background:#f9fafb;border-radius:6px">
          <strong>Notes:</strong> {{ $payout->admin_notes }}
        </div>
        @endif
        @if($payout->processedBy)
        <div style="font-size:12px;color:#9ca3af;margin-top:8px">
          Processed by: {{ $payout->processedBy->name }}
        </div>
        @endif
      </div>
    </div>

    {{-- Actions --}}
    @if(in_array($payout->status, ['pending', 'approved']))
    <div class="card mb-4">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-cogs text-slate-600"></i> Actions</div>
      </div>
      <div class="card-body">

        {{-- Approve --}}
        @if($payout->status === 'pending')
        <form method="POST" action="{{ route('admin.payouts.approve', $payout->id) }}" class="mb-3">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-primary w-full"
            onclick="return confirm('Approve this payout request?')">
            <i class="fas fa-thumbs-up mr-2"></i>Approve Request
          </button>
          <p style="font-size:12px;color:#6b7280;margin-top:6px;text-align:center">
            Approve to confirm you will process the bank transfer
          </p>
        </form>
        @endif

        {{-- Mark as Paid --}}
        <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout->id) }}" class="mb-3">
          @csrf
          <div class="form-group">
            <label class="form-label">Bank Transfer Reference *</label>
            <input type="text" name="payment_reference" class="form-control @error('payment_reference') is-invalid @enderror"
              placeholder="e.g. BACS-2026-0001" required>
            @error('payment_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Admin Notes <small class="text-muted">(optional)</small></label>
            <textarea name="admin_notes" class="form-control" rows="2"
              placeholder="Any notes about this payment..."></textarea>
          </div>
          <button type="submit" class="btn w-full" style="background:#16a34a;color:#fff"
            onclick="return confirm('Mark this payout as PAID? This confirms the bank transfer has been completed.')">
            <i class="fas fa-check-double mr-2"></i>Mark as Paid — £{{ number_format($payout->total_amount, 2) }}
          </button>
        </form>

        <hr style="border-color:#e5e7eb;margin:16px 0">

        {{-- Reject --}}
        <details>
          <summary style="cursor:pointer;font-size:13px;color:#dc2626;font-weight:600;padding:4px 0">
            <i class="fas fa-times-circle mr-1"></i> Reject Request
          </summary>
          <form method="POST" action="{{ route('admin.payouts.reject', $payout->id) }}" class="mt-3">
            @csrf
            <div class="form-group">
              <label class="form-label">Reason for Rejection *</label>
              <textarea name="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror"
                rows="3" placeholder="Explain why this payout is being rejected..." required></textarea>
              @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn w-full" style="background:#dc2626;color:#fff"
              onclick="return confirm('Reject this payout? The shop will need to submit a new request.')">
              <i class="fas fa-times mr-2"></i>Reject Request
            </button>
          </form>
        </details>
      </div>
    </div>
    @endif

    {{-- Shop Info --}}
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-store text-slate-600"></i> Shop Info</div>
      </div>
      <div class="card-body" style="font-size:13px">
        <div style="margin-bottom:8px">
          <div class="text-muted" style="font-size:12px">Shop Name</div>
          <div style="font-weight:600">{{ $payout->shop->shopProfile->shop_name ?? $payout->shop->name }}</div>
        </div>
        <div style="margin-bottom:8px">
          <div class="text-muted" style="font-size:12px">Email</div>
          <div>{{ $payout->shop->email }}</div>
        </div>
        @if($payout->shop->shopProfile->address ?? null)
        <div>
          <div class="text-muted" style="font-size:12px">Address</div>
          <div>{{ $payout->shop->shopProfile->address }}, {{ $payout->shop->shopProfile->postcode }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
.responsive-grid { grid-template-columns: 2fr 1fr; }
@media(max-width:768px){ .responsive-grid { grid-template-columns: 1fr !important; } }
</style>
@endsection
