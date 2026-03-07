@extends('layouts.dashboard')
@section('title','VCFSE Dashboard')
@section('page-title','Organisation Dashboard')
@section('topbar-actions')
<a href="{{ route('vcfse.fund-load') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-wallet"></i> Load Funds
</a>
@endsection
@section('content')
<div class="page-hd">
  <h1>{{ $profile->org_name ?? auth()->user()->name }}</h1>
  <p>VCFSE Organisation — Support the eVoucher Food Support Programme</p>
</div>
<!-- Wallet Balance Banner -->
@if(isset($walletBalance) && $walletBalance > 0)
<div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;color:#fff">
  <div>
    <div style="font-size:12px;font-weight:700;opacity:.75;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Available Wallet Balance</div>
    <div style="font-size:32px;font-weight:900">£{{ number_format($walletBalance, 2) }}</div>
    <div style="font-size:12.5px;opacity:.75;margin-top:4px">Funds loaded by admin — available for voucher allocation</div>
  </div>
  <div style="font-size:48px;opacity:.3"><i class="fas fa-wallet"></i></div>
</div>
@endif
<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-sterling-sign"></i></div>
    <div class="stat-label">Total Donated</div>
    <div class="stat-value">£{{ number_format($totalDonated ?? 0, 2) }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-receipt"></i></div>
    <div class="stat-label">Donations Made</div>
    <div class="stat-value">{{ $donationCount ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-ticket"></i></div>
    <div class="stat-label">Vouchers Funded</div>
    <div class="stat-value">{{ $vouchersFunded ?? 0 }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon mb-3" style="background:#fef9c3;color:#ca8a04"><i class="fas fa-users"></i></div>
    <div class="stat-label">People Helped</div>
    <div class="stat-value">{{ $peopleHelped ?? 0 }}</div>
  </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Fund Load Card -->
  <div class="card" style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);border:none;color:#fff">
    <div class="card-body" style="padding:28px">
      <div style="font-size:32px;margin-bottom:12px">💰</div>
      <div style="font-size:18px;font-weight:800;margin-bottom:8px">Load Funds</div>
      <div style="font-size:13.5px;opacity:.75;line-height:1.7;margin-bottom:20px">
        Load funds to your wallet using Stripe. Your funds will be used to allocate food vouchers to recipients in Northamptonshire.
      </div>
      <a href="{{ route('vcfse.fund-load') }}" class="btn" style="background:#fff;color:#16a34a;width:100%;justify-content:center;font-weight:600">
        <i class="fas fa-wallet"></i> Load Funds Now
      </a>
    </div>
  </div>
  <!-- Organisation Profile -->
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-building text-blue-500"></i> Organisation Profile</div></div>
    <div class="card-body">
      <div class="mb-3">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Organisation Name</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->org_name ?? auth()->user()->name }}</div>
      </div>
      <div class="mb-3">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Type</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">VCFSE Organisation</div>
      </div>
      @if($profile && $profile->charity_number)
      <div class="mb-3">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Charity Number</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->charity_number }}</div>
      </div>
      @endif
      @if($profile && $profile->contact_name)
      <div>
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Contact Person</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->contact_name }}</div>
      </div>
      @endif
    </div>
  </div>
  <!-- Recent Donations -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-receipt text-green-600"></i> Recent Donations</div>
      <a href="{{ route('vcfse.donations') }}" class="btn btn-secondary btn-sm">All</a>
    </div>
    <div>
      @forelse($recentDonations ?? [] as $d)
      <div style="padding:14px 20px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:14px;font-weight:700;color:#16a34a">£{{ number_format($d->amount, 2) }}</div>
          <div style="font-size:11.5px;color:#94a3b8">{{ \Carbon\Carbon::parse($d->created_at)->format('d M Y') }}</div>
        </div>
        @if($d->status === 'completed')<span class="badge badge-green">Completed</span>
        @elseif($d->status === 'pending')<span class="badge badge-yellow">Pending</span>
        @else<span class="badge badge-gray">{{ ucfirst($d->status) }}</span>@endif
      </div>
      @empty
      <div class="empty-state" style="padding:32px 24px"><div class="empty-icon" style="font-size:28px"><i class="fas fa-receipt"></i></div><h3 style="font-size:13px">No donations yet</h3></div>
      @endforelse
    </div>
  </div>
</div>
@endsection
