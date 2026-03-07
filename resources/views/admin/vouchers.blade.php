@extends('layouts.dashboard')
@section('title','Vouchers')
@section('page-title','Voucher Management')
@section('content')
<div class="page-hd">
  <h1>Voucher Management</h1>
  <p>Issue, track, and manage all vouchers for recipients</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <!-- Issue Voucher Form -->
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-plus-circle text-green-600"></i> Issue New Voucher</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.vouchers.issue') }}">
        @csrf
        <div class="mb-4">
          <label class="form-label">Recipient *</label>
          <select name="recipient_id" class="form-select" required>
            <option value="">Select recipient...</option>
            @foreach($recipients ?? [] as $r)
            <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->email }})</option>
            @endforeach
          </select>
        </div>
        <div class="mb-4">
          <label class="form-label">Voucher Value (£) *</label>
          <input type="number" name="amount" min="1" max="500" step="0.01" placeholder="e.g. 15.00" class="form-input" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Expiry Date *</label>
          <input type="date" name="expires_at" class="form-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Notes (optional)</label>
          <textarea name="notes" rows="2" placeholder="Any notes about this voucher..." class="form-textarea"></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full">
          <i class="fas fa-ticket"></i> Issue Voucher
        </button>
      </form>
    </div>
  </div>
  <!-- Voucher List -->
  <div class="card lg:col-span-2">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-list text-blue-500"></i> All Vouchers</div>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead><tr><th>Code</th><th>Recipient</th><th>Value</th><th>Remaining</th><th>Expires</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($vouchers as $v)
          <tr>
            <td><code style="background:#f0fdf4;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;color:#16a34a;letter-spacing:.05em">{{ $v->code }}</code></td>
            <td>
              <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $v->recipient->name ?? '—' }}</div>
              <div style="font-size:11px;color:#94a3b8">{{ $v->recipient->email ?? '' }}</div>
            </td>
            <td style="font-weight:700;color:#0f172a">£{{ number_format($v->amount, 2) }}</td>
            <td style="font-weight:600;color:#16a34a">£{{ number_format($v->remaining_value ?? $v->amount, 2) }}</td>
            <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($v->expires_at)->format('d M Y') }}</td>
            <td>
              @if($v->status === 'active')<span class="badge badge-green">Active</span>
              @elseif($v->status === 'redeemed')<span class="badge badge-blue">Redeemed</span>
              @elseif($v->status === 'expired')<span class="badge badge-red">Expired</span>
              @elseif($v->status === 'partially_used')<span class="badge badge-yellow">Partial</span>
              @elseif($v->status === 'cancelled')<span class="badge badge-gray">Cancelled</span>
              @else<span class="badge badge-gray">{{ ucfirst($v->status) }}</span>@endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-ticket"></i></div><h3>No vouchers yet</h3><p>Issue a voucher using the form on the left</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
