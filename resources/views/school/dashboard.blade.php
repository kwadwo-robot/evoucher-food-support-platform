@extends('layouts.dashboard')
@section('title','School Dashboard')
@section('page-title','Organisation Dashboard')
@section('topbar-actions')
<a href="{{ route('school.donate') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-hand-holding-heart"></i> Make Donation
</a>
@endsection
@section('content')
<div class="page-hd">
  <h1>{{ $profile->org_name ?? auth()->user()->name }}</h1>
  <p>School / Care Organisation — Supporting the eVoucher Food Programme</p>
</div>
<!-- Wallet Balance Banner -->
@if(isset($walletBalance) && $walletBalance > 0)
<div style="background:linear-gradient(135deg,#1d4ed8,#1e40af);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;color:#fff">
  <div>
    <div style="font-size:12px;font-weight:700;opacity:.75;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Available Wallet Balance</div>
    <div style="font-size:32px;font-weight:900">£{{ number_format($walletBalance, 2) }}</div>
    <div style="font-size:12.5px;opacity:.75;margin-top:4px">Funds loaded by admin — available for voucher allocation</div>
  </div>
  <div style="font-size:48px;opacity:.3"><i class="fas fa-wallet"></i></div>
</div>
@endif
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
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="card" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:none;color:#fff">
    <div class="card-body" style="padding:28px">
      <div style="font-size:32px;margin-bottom:12px">🏫</div>
      <div style="font-size:18px;font-weight:800;margin-bottom:8px">School & Care Support</div>
      <div style="font-size:13.5px;opacity:.75;line-height:1.7;margin-bottom:20px">
        Your organisation's contributions help fund food vouchers for vulnerable families and individuals in Northamptonshire. Together we can make a real difference.
      </div>
      <a href="{{ route('school.donate') }}" class="btn" style="background:#16a34a;color:#fff">
        <i class="fas fa-hand-holding-heart"></i> Donate Now
      </a>
    </div>
  </div>
  <div class="card">
    <div class="card-hd"><div class="card-title"><i class="fas fa-building text-blue-500"></i> Organisation Details</div></div>
    <div class="card-body">
      <div class="mb-3">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Organisation Name</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->org_name ?? auth()->user()->name }}</div>
      </div>
      <div class="mb-3">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Type</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->org_type ?? 'School / Care Organisation' }}</div>
      </div>
      @if($profile && $profile->contact_name)
      <div>
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">Contact Person</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->contact_name }}</div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
