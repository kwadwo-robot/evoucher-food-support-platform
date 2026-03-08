@extends('layouts.dashboard')
@section('title', 'Issue Voucher')
@section('page-title', 'Issue New Voucher')
@section('content')

<div class="page-content">
  <div class="flex flex-col lg:flex-row gap-6">
    <!-- Main Form -->
    <div class="flex-1 min-w-0">
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-ticket text-blue-600"></i> Issue New Voucher</div>
        </div>
        <div class="card-body">
          <form action="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.store') : route('school.vouchers.store') }}" method="POST">
            @csrf

            <!-- Recipient Selection -->
            <div class="mb-5">
              <label class="form-label">Select Recipient <span class="text-red-600">*</span></label>
              <select name="recipient_id" id="recipient-select" class="form-input @error('recipient_id') border-red-500 @enderror">
                <option value="">-- Select a Recipient --</option>
                @if(auth()->user()->role === 'vcfse')
                  <optgroup label="Individuals">
                    @forelse($recipients['individuals'] ?? [] as $individual)
                      <option value="{{ $individual->id }}" {{ old('recipient_id') == $individual->id ? 'selected' : '' }}>
                        {{ $individual->name }} ({{ $individual->email }})
                      </option>
                    @empty
                      <option value="" disabled>No individuals available</option>
                    @endforelse
                  </optgroup>
                  <optgroup label="Schools/Care">
                    @forelse($recipients['schools'] ?? [] as $school)
                      <option value="{{ $school->id }}" {{ old('recipient_id') == $school->id ? 'selected' : '' }}>
                        {{ $school->name }} ({{ $school->email }})
                      </option>
                    @empty
                      <option value="" disabled>No schools/care organizations available</option>
                    @endforelse
                  </optgroup>
                @else
                  <optgroup label="Individuals">
                    @forelse($recipients['individuals'] ?? [] as $individual)
                      <option value="{{ $individual->id }}" {{ old('recipient_id') == $individual->id ? 'selected' : '' }}>
                        {{ $individual->name }} ({{ $individual->email }})
                      </option>
                    @empty
                      <option value="" disabled>No individuals available</option>
                    @endforelse
                  </optgroup>
                  <optgroup label="VCFSE Groups">
                    @forelse($recipients['vcfse'] ?? [] as $vcfse)
                      <option value="{{ $vcfse->id }}" {{ old('recipient_id') == $vcfse->id ? 'selected' : '' }}>
                        {{ $vcfse->name }} ({{ $vcfse->email }})
                      </option>
                    @empty
                      <option value="" disabled>No VCFSE groups available</option>
                    @endforelse
                  </optgroup>
                @endif
              </select>

              @error('recipient_id')
                <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
              @enderror
              <p class="text-gray-500 text-sm mt-1"><i class="fas fa-info-circle"></i> Select the recipient who will receive this voucher</p>
            </div>
            <!-- Voucher Value -->
            <div class="mb-5">
              <label class="form-label">Voucher Value (£) <span class="text-red-600">*</span></label>
              <div class="relative">
                <input type="number" name="value" id="voucher-value" class="form-input @error('value') border-red-500 @enderror" placeholder="0.00" min="0.01" step="0.01" value="{{ old('value') }}">
              </div>
              @error('value')
                <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
              @enderror
              <p class="text-gray-500 text-sm mt-1"><i class="fas fa-wallet"></i> Available balance: <strong>£{{ number_format($walletBalance, 2) }}</strong></p>
            </div>
            <!-- Expiry Period -->
            <div class="mb-5">
              <label class="form-label">Expiry Period (Days) <span class="text-red-600">*</span></label>
              <select name="expiry_days" id="expiry-days" class="form-input @error('expiry_days') border-red-500 @enderror">
                <option value="">Select expiry period...</option>
                <option value="7" {{ old('expiry_days') == 7 ? 'selected' : '' }}>7 days</option>
                <option value="14" {{ old('expiry_days') == 14 ? 'selected' : '' }}>14 days</option>
                <option value="30" {{ old('expiry_days') == 30 ? 'selected' : '' }}>30 days</option>
                <option value="60" {{ old('expiry_days') == 60 ? 'selected' : '' }}>60 days</option>
                <option value="90" {{ old('expiry_days') == 90 ? 'selected' : '' }}>90 days</option>
                <option value="180" {{ old('expiry_days') == 180 ? 'selected' : '' }}>180 days</option>
                <option value="365" {{ old('expiry_days') == 365 ? 'selected' : '' }}>1 year</option>
              </select>
              @error('expiry_days')
                <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
              @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
              <label class="form-label">Notes (Optional)</label>
              <textarea name="notes" class="form-input @error('notes') border-red-500 @enderror" 
                        placeholder="Add any notes about this voucher..." rows="3">{{ old('notes') }}</textarea>
              @error('notes')
                <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
              @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
              <button type="submit" id="submit-btn" class="btn btn-primary">
                <i class="fas fa-check"></i> Issue Voucher
              </button>
              <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.dashboard') : route('school.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Info Card -->
    <div class="flex-1 min-w-0">
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-info-circle text-blue-600"></i> How It Works</div>
        </div>
        <div class="card-body text-sm space-y-4">
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-1"></i> Enter Details</div>
            <p class="text-gray-600">Select a recipient, enter the voucher value, and choose an expiry period.</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-2"></i> Wallet Deduction</div>
            <p class="text-gray-600">The voucher value will be deducted from your wallet balance immediately.</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-3"></i> Recipient Notified</div>
            <p class="text-gray-600">The recipient will receive a notification with the voucher code.</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-4"></i> Use Voucher</div>
            <p class="text-gray-600">The recipient can use the voucher to purchase food items.</p>
          </div>
        </div>
      </div>

      <!-- Current Wallet Balance -->
      <div class="card mt-4">
        <div class="card-body">
          <div class="text-center">
            <div class="text-gray-600 text-sm mb-2">Current Wallet Balance</div>
            <div class="text-3xl font-bold text-green-600">£{{ number_format($walletBalance, 2) }}</div>
            <div class="text-green-600 text-sm mt-2"><i class="fas fa-check-circle"></i> Ready to issue vouchers</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Simple form - no complex validation needed
</script>

@endsection
