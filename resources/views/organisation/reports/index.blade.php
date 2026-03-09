@extends('layouts.dashboard')
@section('title','Reports')
@section('page-title','Reports')
@section('content')

<div class="page-hd">
  <h1>Reports</h1>
  <p>View and export your fund loads and bank deposit reports.</p>
</div>

<!-- Financial Summary Section -->
<div class="mb-8">
  <h2 class="text-2xl font-bold mb-4">Financial Summary</h2>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Received -->
    <div class="stat-card border-l-4 border-green-500">
      <div class="stat-icon mb-3" style="background:#f0fdf4;color:#16a34a"><i class="fas fa-arrow-down"></i></div>
      <div class="stat-label">Total Received</div>
      <div class="stat-value">£{{ number_format($totalReceived ?? 0, 2) }}</div>
      <div style="font-size:12px;color:#64748b;margin-top:8px">
        Funds Loaded: £{{ number_format($totalFundsLoaded, 2) }}<br>
        Bank Deposits: £{{ number_format($totalBankDeposits, 2) }}
      </div>
    </div>
    
    <!-- Total Spent -->
    <div class="stat-card border-l-4 border-orange-500">
      <div class="stat-icon mb-3" style="background:#fef3c7;color:#f97316"><i class="fas fa-arrow-up"></i></div>
      <div class="stat-label">Total Spent</div>
      <div class="stat-value">£{{ number_format($totalSpent ?? 0, 2) }}</div>
      <div style="font-size:12px;color:#64748b;margin-top:8px">
        {{ $redemptionCount ?? 0 }} Voucher Redemptions
      </div>
    </div>
    
    <!-- Total Balance -->
    <div class="stat-card border-l-4 border-blue-500">
      <div class="stat-icon mb-3" style="background:#eff6ff;color:#3b82f6"><i class="fas fa-wallet"></i></div>
      <div class="stat-label">Total Balance</div>
      <div class="stat-value">£{{ number_format($totalBalance ?? 0, 2) }}</div>
      <div style="font-size:12px;color:#64748b;margin-top:8px">
        Remaining Funds
      </div>
    </div>
    
    <!-- Redemption Rate -->
    <div class="stat-card border-l-4 border-purple-500">
      <div class="stat-icon mb-3" style="background:#fdf4ff;color:#a855f7"><i class="fas fa-percent"></i></div>
      <div class="stat-label">Redemption Rate</div>
      <div class="stat-value">{{ $redemptionRate ?? 0 }}%</div>
      <div style="font-size:12px;color:#64748b;margin-top:8px">
        Vouchers Redeemed
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div style="text-decoration:none;display:block;border:2px dashed #16a34a;border-radius:8px;padding:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div class="stat-icon" style="background:#dcfce7;color:#15803d">
      <i class="fas fa-wallet"></i>
    </div>
    <div class="stat-label">Total Funds Loaded</div>
    <div class="stat-value">£{{ number_format($totalFundsLoaded, 2) }}</div>
    <div class="stat-change" style="color:#15803d">{{ $fundLoadsCount }} transactions</div>
  </div>
  
  <div style="text-decoration:none;display:block;border:2px dashed #1d4ed8;border-radius:8px;padding:16px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8">
      <i class="fas fa-bank"></i>
    </div>
    <div class="stat-label">Bank Deposits Verified</div>
    <div class="stat-value">£{{ number_format($totalBankDeposits, 2) }}</div>
    <div class="stat-change" style="color:#1d4ed8">{{ $bankDepositsCount }} deposits</div>
  </div>
  
  <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.fund-loads-pdf' : 'school.reports.fund-loads-pdf') }}" style="text-decoration:none;display:block;border:2px dashed #7e22ce;border-radius:8px;padding:16px;background:#fff;hover:shadow-lg;transition:all 0.3s ease;cursor:pointer" onmouseover="this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
    <div class="stat-icon" style="background:#f3e8ff;color:#7e22ce">
      <i class="fas fa-file-pdf"></i>
    </div>
    <div class="stat-label">PDF Reports</div>
    <div class="stat-value">{{ ($fundLoads->count() > 0 || $bankDeposits->count() > 0) ? 2 : 0 }}</div>
    <div class="stat-change" style="color:#7e22ce">{{ ($fundLoads->count() > 0 || $bankDeposits->count() > 0) ? 'Available for download' : 'No data available' }}</div>
  </a>
  
  <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.fund-loads-excel' : 'school.reports.fund-loads-excel') }}" style="text-decoration:none;display:block;border:2px dashed #c2410c;border-radius:8px;padding:16px;background:#fff;hover:shadow-lg;transition:all 0.3s ease;cursor:pointer" onmouseover="this.style.boxShadow='0 10px 15px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
    <div class="stat-icon" style="background:#ffedd5;color:#c2410c">
      <i class="fas fa-file-excel"></i>
    </div>
    <div class="stat-label">Excel Reports</div>
    <div class="stat-value">{{ ($fundLoads->count() > 0 || $bankDeposits->count() > 0) ? 2 : 0 }}</div>
    <div class="stat-change" style="color:#c2410c">{{ ($fundLoads->count() > 0 || $bankDeposits->count() > 0) ? 'Available for download' : 'No data available' }}</div>
  </a>
</div>

<!-- Fund Loads Report Section -->
<div class="card mb-6">
  <div class="card-hd">
    <div class="card-title">
      <i class="fas fa-wallet"></i> Fund Loads Report
    </div>
    <div class="flex gap-2">
      <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.fund-loads-excel' : 'school.reports.fund-loads-excel') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-download"></i> Excel
      </a>
      <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.fund-loads-pdf' : 'school.reports.fund-loads-pdf') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-download"></i> PDF
      </a>
    </div>
  </div>
  <div class="card-body">
    @if($fundLoads->isEmpty())
      <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <h3>No Fund Loads Yet</h3>
        <p>You haven't loaded any funds yet. Start by loading funds to your wallet.</p>
      </div>
    @else
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Reference</th>
              <th>Loaded By</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            @foreach($fundLoads as $load)
            <tr>
              <td>{{ $load->created_at->format('d M Y H:i') }}</td>
              <td><strong>£{{ number_format($load->amount, 2) }}</strong></td>
              <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px">{{ $load->reference }}</code></td>
              <td>{{ $load->admin->name ?? 'System' }}</td>
              <td>{{ $load->notes ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<!-- Bank Deposits Report Section -->
<div class="card" id="bank-deposits">
  <div class="card-hd">
    <div class="card-title">
      <i class="fas fa-bank"></i> Bank Deposits Report
    </div>
    <div class="flex gap-2">
      <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.bank-deposits-excel' : 'school.reports.bank-deposits-excel') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-download"></i> Excel
      </a>
      <a href="{{ route($role === 'vcfse' ? 'vcfse.reports.bank-deposits-pdf' : 'school.reports.bank-deposits-pdf') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-download"></i> PDF
      </a>
    </div>
  </div>
  <div class="card-body">
    @if($bankDeposits->isEmpty())
      <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <h3>No Bank Deposits Yet</h3>
        <p>You haven't submitted any bank deposits yet. Submit a bank deposit to enable fund loads.</p>
      </div>
    @else
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount</th>
              <th>Reference</th>
              <th>Status</th>
              <th>Verified On</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bankDeposits as $deposit)
            <tr>
              <td>{{ $deposit->created_at->format('d M Y H:i') }}</td>
              <td><strong>£{{ number_format($deposit->amount, 2) }}</strong></td>
              <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:11px">{{ $deposit->reference }}</code></td>
              <td>
                @if($deposit->status === 'verified')
                  <span class="badge badge-green"><i class="fas fa-check"></i> Verified</span>
                @elseif($deposit->status === 'pending')
                  <span class="badge badge-yellow"><i class="fas fa-clock"></i> Pending</span>
                @else
                  <span class="badge badge-red"><i class="fas fa-times"></i> Rejected</span>
                @endif
              </td>
              <td>{{ $deposit->verified_at ? $deposit->verified_at->format('d M Y') : '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<style>
.flex { display: flex; }
.gap-2 { gap: 8px; }
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
@media (min-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 1024px) {
  .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}
.mb-6 { margin-bottom: 24px; }
.mb-4 { margin-bottom: 16px; }
</style>

@endsection
