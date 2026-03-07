@extends('layouts.dashboard')
@section('title', __('app.recipient_my_vouchers'))
@section('page-title', __('app.recipient_my_vouchers'))
@section('content')
<div class="page-hd">
  <h1>{{ __('app.recipient_my_vouchers') }}</h1>
  <p>{{ __('app.recipient_all_vouchers') }}</p>
</div>
@if(count($vouchers) > 0)
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
  @foreach($vouchers as $v)
  <div class="{{ $v->status === 'active' || $v->status === 'partially_used' ? 'voucher-card' : 'card' }}" style="{{ $v->status !== 'active' && $v->status !== 'partially_used' ? 'opacity:.7' : '' }}">
    @if($v->status === 'active' || $v->status === 'partially_used')
    <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:16px;position:relative;z-index:1">
      <div>
        <div style="font-size:11px;font-weight:700;opacity:.7;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px">
          {{ $v->status === 'partially_used' ? __('app.recipient_partially_used') : __('app.recipient_active_voucher') }}
        </div>
        <div style="font-size:32px;font-weight:900;line-height:1">£{{ number_format($v->remaining_value ?? $v->amount, 2) }}</div>
        <div style="font-size:12px;opacity:.7;margin-top:3px">{{ __('app.recipient_of_value', ['amount' => number_format($v->amount, 2)]) }}</div>
      </div>
      <code style="background:rgba(255,255,255,.15);padding:6px 14px;border-radius:8px;font-size:13px;font-weight:800;letter-spacing:.1em">{{ $v->code }}</code>
    </div>
    <div style="padding-top:14px;border-top:1px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1">
      <span style="font-size:12px;opacity:.8"><i class="fas fa-calendar mr-1"></i> {{ __('app.recipient_expires', ['date' => \Carbon\Carbon::parse($v->expires_at)->format('d M Y')]) }}</span>
      <a href="{{ route('recipient.food.browse') }}" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:#fff">
        <i class="fas fa-basket-shopping"></i> {{ __('app.browse_food') }}
      </a>
    </div>
    @else
    <div class="card-body">
      <div class="flex items-center justify-between mb-3">
        <code style="background:#f1f5f9;padding:5px 12px;border-radius:7px;font-size:13px;font-weight:700;color:#64748b;letter-spacing:.08em">{{ $v->code }}</code>
        @if($v->status === 'redeemed')<span class="badge badge-blue">{{ __('app.recipient_confirmed') }}</span>
        @elseif($v->status === 'expired')<span class="badge badge-red">{{ __('app.nav_logout') }}</span>
        @elseif($v->status === 'cancelled')<span class="badge badge-gray">{{ __('app.recipient_cancelled') }}</span>
        @else<span class="badge badge-gray">{{ ucfirst($v->status) }}</span>@endif
      </div>
      <div class="flex justify-between">
        <span style="font-size:13px;color:#64748b">{{ __('app.recipient_of_value', ['amount' => '']) }}</span>
        <span style="font-size:14px;font-weight:700;color:#94a3b8">£{{ number_format($v->amount, 2) }}</span>
      </div>
      <div class="flex justify-between mt-1">
        <span style="font-size:12px;color:#94a3b8">{{ __('app.recipient_expires') }}</span>
        <span style="font-size:12px;color:#94a3b8">{{ \Carbon\Carbon::parse($v->expires_at)->format('d M Y') }}</span>
      </div>
    </div>
    @endif
  </div>
  @endforeach
</div>
@else
<div class="card">
  <div class="empty-state" style="padding:64px 24px">
    <div class="empty-icon"><i class="fas fa-ticket"></i></div>
    <h3>{{ __('app.recipient_no_vouchers_yet') }}</h3>
    <p>{{ __('app.recipient_vouchers_appear') }}</p>
  </div>
</div>
@endif
@endsection
