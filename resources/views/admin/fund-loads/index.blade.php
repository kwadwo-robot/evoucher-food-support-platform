@extends('layouts.dashboard')
@section('title','Load Funds to Organisations')
@section('page-title','Load Funds')
@section('content')
<div class="page-hd">
  <h1>Load Funds to Organisations</h1>
  <p>Allocate funds directly to VCFSE and School/Care organisation wallets. These funds can be used to back voucher allocations.</p>
</div>

@if(session('success'))
  <div class="alert-success mb-5"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert-error mb-5"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
  <div class="stat-card">
    <div class="flex items-center gap-4">
      <div class="stat-icon" style="background:#dcfce7;color:#16a34a"><i class="fas fa-wallet"></i></div>
      <div>
        <div class="stat-label">Total Funds Loaded</div>
        <div class="stat-value">£{{ number_format($totalLoaded, 2) }}</div>
        <div class="stat-change" style="color:#64748b">All time</div>
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="flex items-center gap-4">
      <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8"><i class="fas fa-building"></i></div>
      <div>
        <div class="stat-label">Current Wallet Balances</div>
        <div class="stat-value">£{{ number_format($totalWalletBalance, 2) }}</div>
        <div class="stat-change" style="color:#64748b">Across all orgs</div>
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="flex items-center gap-4">
      <div class="stat-icon" style="background:#f3e8ff;color:#7e22ce"><i class="fas fa-users"></i></div>
      <div>
        <div class="stat-label">Active Organisations</div>
        <div class="stat-value">{{ $totalOrgs }}</div>
        <div class="stat-change" style="color:#64748b">VCFSE + School/Care</div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <!-- Load Funds Form -->
  <div class="lg:col-span-1">
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-plus-circle" style="color:#16a34a"></i> Load Funds</div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.fund-loads.store') }}">
          @csrf
          <div class="mb-4">
            <label class="form-label">Select Organisation <span style="color:#ef4444">*</span></label>
            <select name="organisation_user_id" class="form-select" required>
              <option value="">— Choose Organisation —</option>
              @foreach($organisations as $org)
              <option value="{{ $org->id }}" {{ old('organisation_user_id') == $org->id ? 'selected' : '' }}>
                {{ $org->organisationProfile->org_name ?? $org->name }}
                ({{ $org->role === 'vcfse' ? 'VCFSE' : 'School/Care' }})
                — Balance: £{{ number_format($org->organisationProfile->wallet_balance ?? 0, 2) }}
              </option>
              @endforeach
            </select>
            @error('organisation_user_id')
              <div style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-4">
            <label class="form-label">Amount (£) <span style="color:#ef4444">*</span></label>
            <div style="position:relative">
              <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;font-weight:600">£</span>
              <input type="number" name="amount" value="{{ old('amount') }}" min="1" max="10000" step="0.01"
                class="form-input" style="padding-left:28px !important" placeholder="0.00" required>
            </div>
            @error('amount')
              <div style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-5">
            <label class="form-label">Notes (optional)</label>
            <textarea name="notes" rows="3" class="form-textarea" placeholder="Reason for fund load, grant reference, etc.">{{ old('notes') }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary w-full justify-center">
            <i class="fas fa-paper-plane"></i> Load Funds
          </button>
        </form>
      </div>
    </div>

    <!-- Organisation Wallet Balances -->
    <div class="card mt-5">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-wallet" style="color:#1d4ed8"></i> Organisation Wallets</div>
      </div>
      <div class="card-body" style="padding:0">
        @forelse($organisations as $org)
        @php $profile = $org->organisationProfile; @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f8fafc">
          <div>
            <div style="font-size:13px;font-weight:600;color:#0f172a">{{ $profile->org_name ?? $org->name }}</div>
            <div style="font-size:11px;color:#94a3b8">{{ $org->role === 'vcfse' ? 'VCFSE' : 'School/Care' }} · {{ $org->email }}</div>
          </div>
          <div style="text-align:right">
            <div style="font-size:15px;font-weight:800;color:{{ ($profile->wallet_balance ?? 0) > 0 ? '#16a34a' : '#94a3b8' }}">
              £{{ number_format($profile->wallet_balance ?? 0, 2) }}
            </div>
            @if(($profile->wallet_balance ?? 0) == 0)
            <div style="font-size:10px;color:#ef4444">No funds</div>
            @endif
          </div>
        </div>
        @empty
        <div class="empty-state" style="padding:24px">
          <div class="empty-icon" style="font-size:28px"><i class="fas fa-building"></i></div>
          <p>No approved organisations yet.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Fund Load History -->
  <div class="lg:col-span-2">
    <div class="card">
      <div class="card-hd">
        <div class="card-title"><i class="fas fa-history" style="color:#7e22ce"></i> Fund Load History</div>
        <span class="badge badge-purple">{{ $fundLoads->total() }} records</span>
      </div>
      @if($fundLoads->isEmpty())
        <div class="empty-state">
          <div class="empty-icon"><i class="fas fa-inbox"></i></div>
          <h3>No Fund Loads Yet</h3>
          <p>Use the form on the left to load funds to an organisation.</p>
        </div>
      @else
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Organisation</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Loaded By</th>
              <th>Date</th>
              <th>Notes</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fundLoads as $load)
            <tr>
              <td>
                <span style="font-family:monospace;font-size:12px;background:#f1f5f9;padding:2px 8px;border-radius:6px;color:#334155">
                  {{ $load->reference }}
                </span>
              </td>
              <td>
                <div style="font-weight:600;color:#0f172a;font-size:13px">
                  {{ $load->organisation->organisationProfile->org_name ?? $load->organisation->name ?? 'Unknown' }}
                </div>
                <div style="font-size:11px;color:#94a3b8">{{ $load->organisation->email ?? '' }}</div>
              </td>
              <td>
                @if($load->organisation->role === 'vcfse')
                  <span class="badge badge-blue">VCFSE</span>
                @else
                  <span class="badge badge-purple">School/Care</span>
                @endif
              </td>
              <td>
                <span style="font-size:15px;font-weight:800;color:#16a34a">£{{ number_format($load->amount, 2) }}</span>
              </td>
              <td style="font-size:12px;color:#64748b">{{ $load->admin->name ?? 'Admin' }}</td>
              <td style="font-size:12px;color:#64748b;white-space:nowrap">
                {{ $load->created_at->format('d M Y') }}<br>
                <span style="color:#94a3b8">{{ $load->created_at->format('H:i') }}</span>
              </td>
              <td style="font-size:12px;color:#64748b;max-width:180px">
                {{ $load->notes ? Str::limit($load->notes, 60) : '—' }}
              </td>
              <td>
                <form method="POST" action="{{ route('admin.fund-loads.destroy', $load->id) }}"
                  onsubmit="return confirm('Reverse this fund load? The wallet balance will be adjusted.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-undo"></i> Reverse
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($fundLoads->hasPages())
      <div style="padding:16px 20px;border-top:1px solid #f8fafc">
        {{ $fundLoads->links() }}
      </div>
      @endif
      @endif
    </div>
  </div>
</div>
@endsection
