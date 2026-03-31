@extends('layouts.dashboard')
@section('title', __('app.recipient_dashboard'))
@section('page-title', __('app.recipient_dashboard'))
@section('content')
<div class="page-hd">
  <h1>{{ __('app.recipient_dashboard') }}</h1>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body text-center">
          <h3 class="card-title">{{ $totalVouchers }}</h3>
          <p class="card-text">{{ __('app.total_vouchers') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body text-center">
          <h3 class="card-title">{{ $totalRedeemed }}</h3>
          <p class="card-text">{{ __('app.redeemed') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body text-center">
          <h3 class="card-title">{{ $totalAvailable }}</h3>
          <p class="card-text">{{ __('app.items_available') }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Broadcast Messages Section -->
  @php
    $broadcasts = \App\Models\Broadcast::where(function($query) {
      $query->whereJsonContains('recipient_user_ids', auth()->id())
            ->orWhereJsonContains('recipient_user_ids', (string)auth()->id());
    })
      ->where('status', 'sent')
      ->orderByDesc('created_at')
      ->limit(5)
      ->get();
  @endphp
  @if($broadcasts->count() > 0)
  <div class="card mb-6">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-bell text-blue-500"></i> Broadcast Messages</div>
    </div>
    <div style="padding:16px">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($broadcasts as $broadcast)
        <a href="{{ route('recipient.broadcasts.show', $broadcast->id) }}" style="text-decoration:none">
          <div class="broadcast-card" style="border-left:4px solid #3b82f6;padding:12px;background:#f0f9ff;border-radius:6px;cursor:pointer;transition:all 0.2s">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px">
              <div style="font-size:13px;font-weight:700;color:#1e40af;flex:1">{{ Str::limit($broadcast->title, 40) }}</div>
            </div>
            <div style="font-size:12px;color:#475569;margin-bottom:6px;line-height:1.4">{{ Str::limit($broadcast->message, 60) }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $broadcast->created_at->format('d M Y, H:i') }}</div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  <!-- Rest of the dashboard content -->
</div>
@endsection
