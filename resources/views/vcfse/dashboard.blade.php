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
  <p>{{ __('app.vcfse_organisation_description') }}</p>
</div>

<!-- Wallet Balance Banner -->
@if(isset($walletBalance) && $walletBalance > 0)
<div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;color:#fff">
  <div    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.available_wallet_balance') }}</div>
    <div style="font-size:32px;font-weight:900">£{{ number_format($walletBalance, 2) }}</div>
    <div style="font-size:12.5px;opacity:.75;margin-top:4px">{{ __('app.funds_loaded_by_admin') }}</div>
  </div>
  <div style="font-size:48px;opacity:.3"><i class="fas fa-wallet"></i></div>
</div>
@endif

<!-- Fund Load Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-bottom:24px">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#f0fdf4;color:#16a34a;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-sterling-sign"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.total_loaded') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">£{{ number_format($totalLoaded ?? 0, 2) }}</div>
  </div>
  
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#eff6ff;color:#3b82f6;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-receipt"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.fund_loads') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">{{ $fundLoadCount ?? 0 }}</div>
  </div>
  
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#fef3c7;color:#d97706;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-credit-card"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.amount_paid') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">£{{ number_format($foodClaimsPaid ?? 0, 2) }}</div>
  </div>
</div>

<!-- Food Claims Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-bottom:24px">
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#fdf4ff;color:#a855f7;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-shopping-bag"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.food_claims') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">{{ $foodClaimsCounted ?? 0 }}</div>
  </div>
  
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#dcfce7;color:#16a34a;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-check-circle"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.redeemed') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">{{ $foodClaimsRedeemed ?? 0 }}</div>
  </div>
  
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:20px">
    <div style="display:flex;align-items:center;margin-bottom:12px">
      <div style="background:#fef2f2;color:#dc2626;width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px"><i class="fas fa-hourglass-half"></i></div>
    </div>
    <div style="font-size:12px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">{{ __('app.pending') }}</div>
    <div style="font-size:24px;font-weight:700;color:#0f172a">{{ ($foodClaimsCounted - $foodClaimsRedeemed) ?? 0 }}</div>
  </div>
</div>

<!-- Main Content Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:24px">
  <!-- Load Funds Card -->
  <div style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);border:none;color:#fff;border-radius:8px;overflow:hidden">
    <div style="padding:28px">
      <div style="font-size:32px;margin-bottom:12px">💰</div>
      <div style="font-size:18px;font-weight:800;margin-bottom:8px">{{ __('app.load_funds') }}</div>
      <div style="font-size:13.5px;opacity:.75;line-height:1.7;margin-bottom:20px">
        {{ __('app.load_funds_description_vcfse') }}
      </div>
      <a href="{{ route('vcfse.fund-load') }}" class="btn" style="background:#fff;color:#16a34a;width:100%;justify-content:center;font-weight:600;display:flex;align-items:center;text-decoration:none;padding:10px 16px;border-radius:6px;border:none;cursor:pointer">
        <i class="fas fa-wallet"></i> Load Funds Now
      </a>
    </div>
  </div>
  
  <!-- Organisation Profile -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f8fafc">
      <div style="font-size:14px;font-weight:700;color:#0f172a"><i class="fas fa-building text-blue-500"></i> {{ __('app.organisation_profile') }}</div>
    </div>
    <div style="padding:20px">
      <div style="margin-bottom:16px">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">{{ __('app.organisation_name') }}</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->org_name ?? auth()->user()->name }}</div>
      </div>
      <div style="margin-bottom:16px">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">{{ __('app.type') }}</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">VCFSE Organisation</div>
      </div>
      @if($profile && $profile->charity_number)
      <div style="margin-bottom:16px">
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">{{ __('app.charity_number') }}</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->charity_number }}</div>
      </div>
      @endif
      @if($profile && $profile->contact_name)
      <div>
        <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">{{ __('app.contact_person') }}</div>
        <div style="font-size:14px;font-weight:600;color:#0f172a">{{ $profile->contact_name }}</div>
      </div>
      @endif
    </div>
  </div>
  
  <!-- Recent Fund Loads -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f8fafc;display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:14px;font-weight:700;color:#0f172a"><i class="fas fa-wallet text-green-600"></i> {{ __('app.recent_fund_loads') }}</div>
      <a href="{{ route('vcfse.reports') }}" style="font-size:12px;padding:6px 12px;background:#f1f5f9;color:#0f172a;border-radius:4px;text-decoration:none;border:none;cursor:pointer">All</a>
    </div>
    <div>
      @forelse($recentTransactions ?? [] as $t)
      <div style="padding:14px 20px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:14px;font-weight:700;color:#16a34a">£{{ number_format($t->amount, 2) }}</div>
          <div style="font-size:11.5px;color:#94a3b8">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}</div>
        </div>
        <span style="background:#dcfce7;color:#16a34a;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600">Loaded</span>
      </div>
      @empty
      <div style="padding:32px 24px;text-align:center">
        <div style="font-size:28px;margin-bottom:8px"><i class="fas fa-wallet"></i></div>
        <div style="font-size:13px;color:#94a3b8">{{ __('app.no_fund_loads_yet') }}</div>
      </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Recent Food Claims Section (Full Width) -->
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden">
  <div style="padding:16px 20px;border-bottom:1px solid #f8fafc;display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:14px;font-weight:700;color:#0f172a"><i class="fas fa-shopping-bag text-blue-600"></i> {{ __('app.recent_food_claims') }}</div>
    <a href="{{ route('vcfse.reports') }}" style="font-size:12px;padding:6px 12px;background:#f1f5f9;color:#0f172a;border-radius:4px;text-decoration:none;border:none;cursor:pointer">All Claims</a>
  </div>
  <div>
    @forelse($recentFoodClaims ?? [] as $claim)
    <div style="padding:14px 20px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between">
      <div style="flex:1">
        <div style="font-size:14px;font-weight:700;color:#3b82f6">{{ $claim->foodListing->item_name ?? 'Unknown Item' }}</div>
        <div style="font-size:11.5px;color:#94a3b8">{{ \Carbon\Carbon::parse($claim->created_at)->format('d M Y H:i') }}</div>
      </div>
      <div style="margin-left:12px">
        @if($claim->status === 'confirmed')
          @if($claim->redeemed_at)
            <span style="background:#dcfce7;color:#16a34a;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600">Redeemed</span>
          @else
            <span style="background:#fef3c7;color:#d97706;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600">Pending</span>
          @endif
        @else
          <span style="background:#f3f4f6;color:#6b7280;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600">{{ ucfirst($claim->status) }}</span>
        @endif
      </div>
      @if($claim->amount_used > 0)
      <div style="margin-left:12px;text-align:right">
        <div style="font-size:13px;font-weight:600;color:#059669">£{{ number_format($claim->amount_used, 2) }}</div>
      </div>
      @endif
    </div>
    @empty
    <div style="padding:32px 24px;text-align:center">
      <div style="font-size:28px;margin-bottom:8px"><i class="fas fa-shopping-bag"></i></div>
        <div style="font-size:13px;color:#94a3b8">{{ __('app.no_food_claims_yet') }}</div>
    </div>
    @endforelse
  </div>
</div>

@endsection
