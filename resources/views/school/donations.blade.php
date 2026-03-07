@extends('layouts.dashboard')
@section('title','Donation History')
@section('page-title','Donation History')
@section('content')
<div class="page-hd">
  <h1>Donation History</h1>
  <p>All donations made by your organisation</p>
</div>
<div class="grid grid-cols-3 gap-4 mb-6">
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#16a34a">£{{ number_format($totalDonated ?? 0, 2) }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Total Donated</div>
  </div>
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#3b82f6">{{ $donationCount ?? 0 }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Donations</div>
  </div>
  <div class="stat-card text-center">
    <div style="font-size:24px;font-weight:800;color:#a855f7">{{ $vouchersFunded ?? 0 }}</div>
    <div style="font-size:11.5px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Vouchers Funded</div>
  </div>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-receipt text-green-600"></i> All Donations</div>
    <a href="{{ route('school.donate') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Donation</a>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Amount</th><th>Reference</th><th>Message</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($donations ?? [] as $d)
        <tr>
          <td style="font-size:16px;font-weight:800;color:#16a34a">£{{ number_format($d->amount, 2) }}</td>
          <td><code style="background:#f1f5f9;padding:2px 8px;border-radius:5px;font-size:11px">{{ $d->stripe_payment_id ?? 'N/A' }}</code></td>
          <td style="font-size:12.5px;color:#64748b">{{ Str::limit($d->message ?? '—', 40) }}</td>
          <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($d->created_at)->format('d M Y H:i') }}</td>
          <td>
            @if($d->status === 'completed')<span class="badge badge-green">Completed</span>
            @elseif($d->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @else<span class="badge badge-gray">{{ ucfirst($d->status) }}</span>@endif
          </td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="fas fa-receipt"></i></div><h3>No donations yet</h3><p><a href="{{ route('school.donate') }}" style="color:#16a34a;font-weight:600">Make your first donation</a></p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
