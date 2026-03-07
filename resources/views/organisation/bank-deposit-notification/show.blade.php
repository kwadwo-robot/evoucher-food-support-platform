@extends('layouts.dashboard')

@section('title', 'Bank Deposit Details - eVoucher Platform')

@section('content')
<div class="page-header mb-6">
  <h1>Bank Deposit Details</h1>
  <p>Reference: <strong>{{ $bankDeposit->reference }}</strong></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
  <!-- Status Card -->
  <div class="card">
    <div class="card-body text-center">
      <div class="text-4xl mb-2">
        @if($bankDeposit->status === 'pending')
          ⏳
        @elseif($bankDeposit->status === 'verified')
          ✅
        @elseif($bankDeposit->status === 'rejected')
          ❌
        @endif
      </div>
      <h3 class="font-semibold text-gray-800">Status</h3>
      <p class="text-2xl font-bold">
        @if($bankDeposit->status === 'pending')
          <span class="text-yellow-600">Pending</span>
        @elseif($bankDeposit->status === 'verified')
          <span class="text-green-600">Verified</span>
        @elseif($bankDeposit->status === 'rejected')
          <span class="text-red-600">Rejected</span>
        @endif
      </p>
    </div>
  </div>

  <!-- Amount Card -->
  <div class="card">
    <div class="card-body text-center">
      <div class="text-4xl mb-2">💷</div>
      <h3 class="font-semibold text-gray-800">Amount</h3>
      <p class="text-2xl font-bold text-green-600">£{{ number_format($bankDeposit->amount, 2) }}</p>
    </div>
  </div>

  <!-- Submitted Date Card -->
  <div class="card">
    <div class="card-body text-center">
      <div class="text-4xl mb-2">📅</div>
      <h3 class="font-semibold text-gray-800">Submitted</h3>
      <p class="text-lg">{{ $bankDeposit->created_at->format('d M Y') }}</p>
      <p class="text-sm text-gray-500">{{ $bankDeposit->created_at->format('H:i') }}</p>
    </div>
  </div>
</div>

<!-- Deposit Details -->
<div class="card mb-6">
  <div class="card-hd">
    <div class="card-title">
      <i class="fas fa-info-circle"></i> Deposit Information
    </div>
  </div>
  <div class="card-body">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Reference ID</h4>
        <p class="text-gray-600 font-mono">{{ $bankDeposit->reference }}</p>
      </div>
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Amount</h4>
        <p class="text-gray-600">£{{ number_format($bankDeposit->amount, 2) }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Bank Details -->
<div class="card mb-6">
  <div class="card-hd">
    <div class="card-title">
      <i class="fas fa-university"></i> Bank Details
    </div>
  </div>
  <div class="card-body">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Bank Name</h4>
        <p class="text-gray-600">{{ $bankDeposit->bank_name }}</p>
      </div>
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Account Holder</h4>
        <p class="text-gray-600">{{ $bankDeposit->bank_account_holder }}</p>
      </div>
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Sort Code</h4>
        <p class="text-gray-600 font-mono">{{ $bankDeposit->sort_code }}</p>
      </div>
      <div>
        <h4 class="font-semibold text-gray-700 mb-2">Account Number</h4>
        <p class="text-gray-600 font-mono">{{ str_repeat('*', 4) }}{{ substr($bankDeposit->account_number, -4) }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Notes -->
@if($bankDeposit->notes)
  <div class="card mb-6">
    <div class="card-hd">
      <div class="card-title">
        <i class="fas fa-sticky-note"></i> Notes
      </div>
    </div>
    <div class="card-body">
      <p class="text-gray-600">{{ $bankDeposit->notes }}</p>
    </div>
  </div>
@endif

<!-- Verification Details -->
@if($bankDeposit->status === 'verified')
  <div class="card mb-6 border-l-4 border-green-500">
    <div class="card-hd">
      <div class="card-title">
        <i class="fas fa-check-circle text-green-600"></i> Verification Details
      </div>
    </div>
    <div class="card-body">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 class="font-semibold text-gray-700 mb-2">Verified By</h4>
          <p class="text-gray-600">{{ $bankDeposit->verifiedBy->name ?? 'System' }}</p>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700 mb-2">Verified On</h4>
          <p class="text-gray-600">{{ $bankDeposit->verified_at->format('d M Y H:i') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif

<!-- Back Button -->
<div class="flex gap-3">
  <a href="{{ route($role === 'vcfse' ? 'vcfse.bank-deposit-notification.index' : 'school.bank-deposit-notification.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>

<style>
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
  .grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1024px) {
  .grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

.gap-6 {
  gap: 1.5rem;
}

.gap-3 {
  gap: 0.75rem;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

.text-center {
  text-align: center;
}

.font-semibold {
  font-weight: 600;
}

.font-bold {
  font-weight: 700;
}

.text-gray-700 {
  color: #374151;
}

.text-gray-600 {
  color: #4b5563;
}

.text-gray-500 {
  color: #6b7280;
}

.text-green-600 {
  color: #16a34a;
}

.text-yellow-600 {
  color: #ca8a04;
}

.text-red-600 {
  color: #dc2626;
}

.text-2xl {
  font-size: 1.5rem;
}

.text-4xl {
  font-size: 2.25rem;
}

.text-lg {
  font-size: 1.125rem;
}

.text-sm {
  font-size: 0.875rem;
}

.font-mono {
  font-family: monospace;
}

.border-l-4 {
  border-left-width: 4px;
}

.border-green-500 {
  border-color: #22c55e;
}
</style>
@endsection
