@extends('layouts.dashboard')
@section('title','Redemption History')
@section('page-title','Redemption History')
@section('content')
<div class="page-hd">
  <h1>Redemption History</h1>
  <p>All food items you have redeemed using your vouchers</p>
</div>
<div class="card">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-history text-blue-500"></i> History <span class="badge badge-gray ml-2">{{ count($redemptions) }}</span></div>
  </div>
  @if(count($redemptions) > 0)
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Food Item</th><th>Shop</th><th>Voucher Used</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($redemptions as $r)
        <tr>
          <td style="font-weight:600;color:#0f172a">{{ $r->foodListing->item_name ?? '—' }}</td>
          <td style="font-size:12.5px;color:#64748b">{{ $r->foodListing->shopUser->name ?? '—' }}</td>
          <td><code style="background:#f0fdf4;padding:2px 8px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $r->voucher->code ?? '—' }}</code></td>
          <td style="font-
size:12px;color:#64748b">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
          <td>
            @if($r->status === 'pending')<span class="badge badge-yellow">Pending</span>
            @elseif($r->status === 'confirmed')<span class="badge badge-green">Confirmed</span>
            @elseif($r->status === 'cancelled')<span class="badge badge-red">Cancelled</span>
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
    <h3>No redemptions yet</h3>
    <p>Browse available food and redeem your voucher to get started</p>
    <a href="{{ route('recipient.food.browse') }}" class="btn btn-primary mt-4"><i class="fas fa-basket-shopping"></i> Browse Food</a>
  </div>
  @endif
</div>
@endsection
