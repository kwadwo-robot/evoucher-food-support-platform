@extends('layouts.dashboard')
@section('title','Voucher Details')
@section('page-title','Voucher Details')
@section('content')
<div class="flex items-center gap-3 mb-6">
  <a href="{{ route('recipient.vouchers') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
  <div>
    <h1 class="text-2xl font-bold text-gray-900">Voucher Details</h1>
    <p class="text-gray-500 text-sm mt-1">{{ $voucher->code }}</p>
  </div>
</div>

<!-- Voucher Card -->
<div class="card mb-6" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none">
  <div class="flex items-start justify-between mb-4">
    <div>
      <div style="font-size:11px;font-weight:700;letter-spacing:.1em;opacity:.7;text-transform:uppercase">eVoucher</div>
      <div style="font-size:28px;font-weight:900;margin-top:4px">£{{ number_format($voucher->remaining_value, 2) }}</div>
      <div style="font-size:12px;opacity:.7">of £{{ number_format($voucher->value, 2) }} original value</div>
    </div>
    <div style="text-align:right">
      @if($voucher->status === 'active')
        <span style="background:rgba(255,255,255,.2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">Active</span>
      @elseif($voucher->status === 'redeemed')
        <span style="background:rgba(0,0,0,.2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">Redeemed</span>
      @else
        <span style="background:rgba(0,0,0,.2);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">{{ ucfirst($voucher->status) }}</span>
      @endif
    </div>
  </div>
  <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:12px 16px;text-align:center;margin-bottom:16px">
    <code style="font-size:20px;font-weight:900;letter-spacing:.15em">{{ $voucher->code }}</code>
  </div>
  <div class="flex justify-between text-xs" style="opacity:.75">
    <span><i class="fas fa-calendar mr-1"></i>Issued {{ $voucher->created_at->format('d M Y') }}</span>
    <span><i class="fas fa-clock mr-1"></i>Expires {{ $voucher->expiry_date->format('d M Y') }}</span>
  </div>
</div>

@if($voucher->status === 'active')
<div class="mb-6">
  <a href="{{ route('recipient.food.browse') }}" class="btn btn-primary w-full text-center" style="display:block">
    <i class="fas fa-basket-shopping mr-2"></i>Browse Food Items to Redeem
  </a>
</div>
@endif

<!-- Redemption History -->
@if($voucher->redemptions->count() > 0)
<div class="card">
  <div class="card-hd mb-4">
    <div class="card-title"><i class="fas fa-history text-blue-500"></i> Redemption History</div>
  </div>
  <div class="space-y-3">
    @foreach($voucher->redemptions as $r)
    <div style="padding:12px 16px;background:#f8fafc;border-radius:10px;display:flex;align-items:center;gap:12px">
      <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0">
        <i class="fas fa-check"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $r->foodListing->item_name ?? 'Food Item' }}</div>
        <div style="font-size:11.5px;color:#94a3b8">{{ \Carbon\Carbon::parse($r->redeemed_at)->format('d M Y, H:i') }}</div>
      </div>
      <div style="font-size:14px;font-weight:700;color:#16a34a">-£{{ number_format($r->amount_used, 2) }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

@if($voucher->notes)
<div class="card mt-4">
  <div class="card-title mb-2"><i class="fas fa-sticky-note text-yellow-500"></i> Notes</div>
  <p style="font-size:13px;color:#64748b">{{ $voucher->notes }}</p>
</div>
@endif
@endsection
