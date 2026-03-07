@extends('layouts.dashboard')
@section('title','Redemptions')
@section('page-title','Voucher Redemptions')
@section('content')
<div class="page-hd">
  <div>
    <h1>Redemption History</h1>
    <p>All voucher redemptions for your food listings</p>
  </div>
  <a href="{{ route('shop.payouts.index') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-money-bill-transfer mr-1"></i> Request Payout
  </a>
</div>

@if(session('success'))
<div class="alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-check-double text-green-600"></i> Redemptions <span class="badge badge-gray ml-2">{{ count($redemptions) }}</span></div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Food Item</th>
          <th>Recipient</th>
          <th>Voucher Code</th>
          <th>Amount Owed</th>
          <th>Date</th>
          <th>Status</th>
          <th>Payout</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($redemptions as $r)
        @php
          $payoutAmount = $r->amount_used > 0 ? $r->amount_used : ($r->foodListing->voucher_value ?? 0);
        @endphp
        <tr>
          <td style="font-weight:600;color:#0f172a">{{ $r->foodListing->item_name ?? '—' }}</td>
          <td>
            <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $r->recipient->name ?? '—' }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $r->recipient->email ?? '' }}</div>
          </td>
          <td><code style="background:#f0fdf4;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $r->voucher->code ?? '—' }}</code></td>
          <td>
            <span style="font-size:14px;font-weight:700;color:#16a34a">£{{ number_format($payoutAmount, 2) }}</span>
          </td>
          <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
          <td>
            @if($r->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @elseif($r->status === 'collected')<span class="badge badge-green">Collected</span>
            @elseif($r->status === 'confirmed')<span class="badge badge-green">Confirmed</span>
            @elseif($r->status === 'cancelled')<span class="badge badge-red">Cancelled</span>
            @else<span class="badge badge-gray">{{ ucfirst($r->status) }}</span>@endif
          </td>
          <td>
            @if($r->status === 'collected' || $r->status === 'confirmed')
              @if($r->payout_request_id)
                @php $pr = $r->payoutRequest; @endphp
                @if($pr && $pr->status === 'paid')
                  <span class="badge badge-green" title="Paid — Ref: {{ $pr->payment_reference }}">
                    <i class="fas fa-check-circle mr-1"></i>Paid
                  </span>
                @elseif($pr && $pr->status === 'approved')
                  <span class="badge badge-blue">Processing</span>
                @elseif($pr && $pr->status === 'pending')
                  <span class="badge badge-yellow">In Request</span>
                @elseif($pr && $pr->status === 'rejected')
                  <span class="badge badge-red">Rejected</span>
                @else
                  <span class="badge badge-gray">In Request</span>
                @endif
              @else
                <span class="badge badge-gray" title="Not yet included in a payout request">Unpaid</span>
              @endif
            @else
              <span style="font-size:12px;color:#94a3b8">—</span>
            @endif
          </td>
          <td>
            @if($r->status === 'pending')
              @if(($r->amount_owed_at_shop ?? 0) > 0 && !$r->payment_collected)
                <button type="button"
                  onclick="openPaymentModal({{ $r->id }}, '{{ addslashes($r->foodListing->item_name ?? 'Item') }}', {{ number_format($r->amount_owed_at_shop, 2, '.', '') }})"
                  class="btn btn-primary btn-sm">
                  <i class="fas fa-pound-sign"></i> Collect Payment
                </button>
              @else
                <form method="POST" action="{{ route('shop.redemptions.confirm', $r->id) }}" style="display:inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Confirm</button>
                </form>
              @endif
            @else
            <span style="font-size:12px;color:#94a3b8">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fas fa-check-double"></i></div><h3>No redemptions yet</h3><p>Redemptions will appear here when recipients use vouchers on your listings</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Payment Confirmation Modal --}}
<div id="paymentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:16px;padding:28px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
      <div style="width:44px;height:44px;background:#fef9c3;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">💷</div>
      <div>
        <div style="font-size:16px;font-weight:800;color:#0f172a">Collect Payment</div>
        <div style="font-size:13px;color:#64748b" id="modalItemName">Item name</div>
      </div>
    </div>
    <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:20px;">
      <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:4px"><i class="fas fa-exclamation-triangle mr-1"></i> Payment Required</div>
      <div style="font-size:13px;color:#78350f">The recipient's voucher does not fully cover this item. Please collect <strong id="modalAmount">£0.00</strong> from the recipient before confirming collection.</div>
    </div>
    <form id="paymentForm" method="POST" action="">
      @csrf @method('PATCH')
      <input type="hidden" name="payment_confirmed" value="1">
      <div class="mb-4">
        <label class="form-label">Payment Method Received <span style="color:#ef4444">*</span></label>
        <select name="payment_method" class="form-select" required>
          <option value="">-- Select payment method --</option>
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="contactless">Contactless</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="button" onclick="closePaymentModal()" class="btn btn-secondary" style="flex:1;justify-content:center">Cancel</button>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center"><i class="fas fa-check-circle"></i> Confirm &amp; Collected</button>
      </div>
    </form>
  </div>
</div>

<script>
function openPaymentModal(id, itemName, amount) {
  document.getElementById('modalItemName').textContent = itemName;
  document.getElementById('modalAmount').textContent = '£' + parseFloat(amount).toFixed(2);
  document.getElementById('paymentForm').action = '/shop/redemptions/' + id + '/confirm';
  document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
  document.getElementById('paymentModal').style.display = 'none';
}
document.getElementById('paymentModal').addEventListener('click', function(e) {
  if (e.target === this) closePaymentModal();
});
</script>
@endsection
