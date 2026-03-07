@extends('layouts.dashboard')
@section('title', __('app.recipient_redemption_history'))
@section('page-title', __('app.recipient_redemption_history'))
@section('content')
<div class="page-hd">
  <h1>{{ __('app.recipient_redemption_history') }}</h1>
  <p>{{ __('app.recipient_all_redeemed') }}</p>
</div>

<!-- Export Buttons -->
@if(count($redemptions) > 0)
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <a href="{{ route('recipient.reports.export-pdf') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#ef4444;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s" title="{{ __('app.recipient_export_pdf') }}">
    <i class="fas fa-file-pdf"></i> {{ __('app.recipient_export_pdf') }}
  </a>
  <a href="{{ route('recipient.reports.export-excel') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;background:#10b981;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s" title="{{ __('app.recipient_export_excel') }}">
    <i class="fas fa-file-excel"></i> {{ __('app.recipient_export_excel') }}
  </a>
</div>
@endif
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-history text-blue-500"></i> {{ __('app.recipient_history') }} <span class="badge badge-gray ml-2">{{ count($redemptions) }}</span></div>
  </div>
  @if(count($redemptions) > 0)
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>{{ __('app.recipient_food_item') }}</th><th>{{ __('app.recipient_shop') }}</th><th>{{ __('app.recipient_voucher_used') }}</th><th>{{ __('app.recipient_date') }}</th><th>{{ __('app.recipient_status') }}</th></tr></thead>
      <tbody>
        @foreach($redemptions as $r)
        <tr>
          <td style="font-weight:600;color:#0f172a">{{ $r->foodListing->item_name ?? '—' }}</td>
          <td style="font-size:12.5px;color:#64748b">{{ $r->foodListing->shopUser->name ?? '—' }}</td>
          <td><code style="background:#f0fdf4;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $r->voucher->code ?? '—' }}</code></td>
          <td style="font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
          <td>
            @if($r->status === 'pending')<span class="badge badge-yellow">{{ __('app.recipient_pending') }}</span>
            @elseif($r->status === 'confirmed')<span class="badge badge-green">{{ __('app.recipient_confirmed') }}</span>
            @elseif($r->status === 'cancelled')<span class="badge badge-red">{{ __('app.recipient_cancelled') }}</span>
            @else<span class="badge badge-gray">{{ ucfirst($r->status) }}</span>@endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="empty-state" style="padding:64px 24px">
    <div class="empty-icon"><i class="fas fa-history"></i></div>
    <h3>{{ __('app.recipient_no_redemptions') }}</h3>
    <p>{{ __('app.recipient_browse_redeem') }}</p>
    <a href="{{ route('recipient.food.browse') }}" class="btn btn-primary mt-4"><i class="fas fa-basket-shopping"></i> {{ __('app.browse_food') }}</a>
  </div>
  @endif
</div>
@endsection
