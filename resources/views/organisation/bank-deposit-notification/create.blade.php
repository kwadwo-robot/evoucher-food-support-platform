@extends('layouts.dashboard')

@section('title', 'Submit Bank Deposit - eVoucher Platform')

@section('content')
<div class="page-header mb-6">
  <h1>Bank Deposit Notification</h1>
  <p>Submit your bank deposit details for admin verification and fund loading.</p>
</div>

<div class="card">
  <div class="card-body">
    <form action="{{ route($role === 'vcfse' ? 'vcfse.bank-deposit-notification.store' : 'school.bank-deposit-notification.store') }}" method="POST" class="max-w-2xl">
      @csrf

      <!-- Deposit Amount -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Deposit Amount (£) <span class="text-red-500">*</span></label>
        <input type="number" name="amount" step="0.01" min="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('amount') border-red-500 @enderror" placeholder="e.g., 5000" value="{{ old('amount') }}" required>
        @error('amount')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Bank Reference/Transaction ID -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Reference/Transaction ID <span class="text-red-500">*</span></label>
        <input type="text" name="reference" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('reference') border-red-500 @enderror" placeholder="e.g., TRF-20260307-001" value="{{ old('reference') }}" required>
        <p class="text-gray-500 text-xs mt-1">Unique identifier for tracking this transfer</p>
        @error('reference')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Bank Details Section -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Bank Details</h3>

        <!-- Bank Name -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Name <span class="text-red-500">*</span></label>
          <input type="text" name="bank_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('bank_name') border-red-500 @enderror" placeholder="e.g., Barclays, HSBC, Lloyds" value="{{ old('bank_name') }}" required>
          @error('bank_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Account Holder Name -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Account Holder Name <span class="text-red-500">*</span></label>
          <input type="text" name="bank_account_holder" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('bank_account_holder') border-red-500 @enderror" placeholder="e.g., Northampton Community Trust" value="{{ old('bank_account_holder') }}" required>
          @error('bank_account_holder')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Sort Code -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Sort Code <span class="text-red-500">*</span></label>
          <input type="text" name="sort_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('sort_code') border-red-500 @enderror" placeholder="XX-XX-XX" pattern="\d{2}-\d{2}-\d{2}" value="{{ old('sort_code') }}" required>
          <p class="text-gray-500 text-xs mt-1">Format: XX-XX-XX (e.g., 20-17-75)</p>
          @error('sort_code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Account Number -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Account Number <span class="text-red-500">*</span></label>
          <input type="text" name="account_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('account_number') border-red-500 @enderror" placeholder="8 digits" pattern="\d{8}" maxlength="8" value="{{ old('account_number') }}" required>
          <p class="text-gray-500 text-xs mt-1">8 digit account number</p>
          @error('account_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Notes -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Notes</label>
        <textarea name="notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('notes') border-red-500 @enderror" placeholder="Add any additional information about this deposit...">{{ old('notes') }}</textarea>
        @error('notes')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Submit Button -->
      <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-check"></i> Submit Bank Deposit Notification
        </button>
        <a href="{{ route($role === 'vcfse' ? 'vcfse.dashboard' : 'school.dashboard') }}" class="btn btn-secondary">
          <i class="fas fa-times"></i> Cancel
        </a>
      </div>
    </form>
  </div>
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

.btn-primary {
  background: #16a34a;
  color: white;
}

.btn-primary:hover {
  background: #15803d;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

.btn-secondary {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
  background: #e2e8f0;
}
</style>
@endsection
