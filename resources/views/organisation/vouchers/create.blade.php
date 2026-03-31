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
          <!-- Tabs -->
          <div class="flex gap-4 mb-6 border-b border-gray-200">
            <button type="button" class="tab-button active px-4 py-2 border-b-2 border-blue-600 text-blue-600 font-semibold" data-tab="registered">
              <i class="fas fa-user-check mr-2"></i> Issue to Registered Recipient
            </button>
            <button type="button" class="tab-button px-4 py-2 border-b-2 border-transparent text-gray-600 font-semibold hover:text-gray-900" data-tab="manual">
              <i class="fas fa-user-plus mr-2"></i> Issue to New Recipient
            </button>
          </div>

          <!-- Tab 1: Registered Recipient -->
          <div id="registered-tab" class="tab-content">
            <form action="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.store') : route('school.vouchers.store') }}" method="POST">
              @csrf
              <input type="hidden" name="issue_type" value="registered">

              <!-- Recipient Selection -->
              <div class="mb-5">
                <label class="form-label">Select Recipient <span class="text-red-600">*</span></label>
                <div class="flex gap-2 mb-3">
                  <input type="text" id="recipient-search" class="form-input flex-1" placeholder="Search recipient by name or email...">
                  <button type="button" id="search-btn" class="btn btn-secondary"><i class="fas fa-search"></i> Search</button>
                </div>
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
              <!-- Voucher Value and Quantity -->
              <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                  <label class="form-label">Voucher Value (£) <span class="text-red-600">*</span></label>
                  <div class="relative">
                    <input type="number" name="value" id="voucher-value" class="form-input @error('value') border-red-500 @enderror" placeholder="0.00" min="0.01" step="0.01" value="{{ old('value') }}">
                  </div>
                  @error('value')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label class="form-label">Number of Vouchers <span class="text-red-600">*</span></label>
                  <div class="relative">
                    <input type="number" name="quantity" id="voucher-quantity" class="form-input @error('quantity') border-red-500 @enderror" placeholder="1" min="1" step="1" value="{{ old('quantity', 1) }}">
                  </div>
                  @error('quantity')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div class="mb-5 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-700 text-sm"><i class="fas fa-info-circle mr-2"></i> <strong>Total Cost:</strong> <span id="total-cost">£0.00</span> (£<span id="per-voucher-cost">0.00</span> per voucher × <span id="quantity-display">1</span> vouchers)</p>
              </div>
              <p class="text-gray-500 text-sm mb-5"><i class="fas fa-wallet"></i> Available balance: <strong>£{{ number_format($walletBalance, 2) }}</strong></p>
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
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-check"></i> Issue Voucher
                </button>
                <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.dashboard') : route('school.dashboard') }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i> Cancel
                </a>
              </div>
            </form>
          </div>

          <!-- Tab 2: Manual Recipient -->
          <div id="manual-tab" class="tab-content hidden">
            <form action="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.store') : route('school.vouchers.store') }}" method="POST">
              @csrf
              <input type="hidden" name="issue_type" value="manual">
              <input type="hidden" name="new_recipient_name" value="1">

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-700 text-sm"><i class="fas fa-info-circle mr-2"></i> Create a new recipient account and issue them a voucher. They will receive an email with login credentials and voucher details.</p>
              </div>

              <!-- First Name -->
              <div class="mb-5">
                <label class="form-label">First Name <span class="text-red-600">*</span></label>
                <input type="text" name="first_name" class="form-input @error('first_name') border-red-500 @enderror" placeholder="John" value="{{ old('first_name') }}">
                @error('first_name')
                  <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
              </div>

              <!-- Last Name -->
              <div class="mb-5">
                <label class="form-label">Last Name <span class="text-red-600">*</span></label>
                <input type="text" name="last_name" class="form-input @error('last_name') border-red-500 @enderror" placeholder="Doe" value="{{ old('last_name') }}">
                @error('last_name')
                  <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
              </div>

              <!-- Email -->
              <div class="mb-5">
                <label class="form-label">Email Address <span class="text-red-600">*</span></label>
                <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" placeholder="john.doe@example.com" value="{{ old('email') }}">
                @error('email')
                  <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
              </div>

              <!-- Phone -->
              <div class="mb-5">
                <label class="form-label">Phone (Optional)</label>
                <input type="tel" name="phone" class="form-input @error('phone') border-red-500 @enderror" placeholder="+44 123 456 7890" value="{{ old('phone') }}">
                @error('phone')
                  <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
              </div>

              <!-- Postcode -->
              <div class="mb-5">
                <label class="form-label">Postcode <span class="text-red-600">*</span></label>
                <input type="text" name="postcode" class="form-input @error('postcode') border-red-500 @enderror" placeholder="NN1 1AA" value="{{ old('postcode') }}">
                @error('postcode')
                  <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
              </div>

              <!-- Voucher Value and Quantity -->
              <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                  <label class="form-label">Voucher Value (£) <span class="text-red-600">*</span></label>
                  <div class="relative">
                    <input type="number" name="value" id="manual-voucher-value" class="form-input @error('value') border-red-500 @enderror" placeholder="0.00" min="0.01" step="0.01" value="{{ old('value') }}">
                  </div>
                  @error('value')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label class="form-label">Number of Vouchers <span class="text-red-600">*</span></label>
                  <div class="relative">
                    <input type="number" name="quantity" id="manual-voucher-quantity" class="form-input @error('quantity') border-red-500 @enderror" placeholder="1" min="1" step="1" value="{{ old('quantity', 1) }}">
                  </div>
                  @error('quantity')
                    <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div class="mb-5 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-700 text-sm"><i class="fas fa-info-circle mr-2"></i> <strong>Total Cost:</strong> <span id="manual-total-cost">£0.00</span> (£<span id="manual-per-voucher-cost">0.00</span> per voucher × <span id="manual-quantity-display">1</span> vouchers)</p>
              </div>
              <p class="text-gray-500 text-sm mb-5"><i class="fas fa-wallet"></i> Available balance: <strong>£{{ number_format($walletBalance, 2) }}</strong></p>

              <!-- Expiry Period -->
              <div class="mb-5">
                <label class="form-label">Expiry Period (Days) <span class="text-red-600">*</span></label>
                <select name="expiry_days" class="form-input @error('expiry_days') border-red-500 @enderror">
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
                <button type="submit" class="btn btn-primary">
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
            <p class="text-gray-600">Select a recipient or create a new one, enter the voucher value, and choose an expiry period.</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-2"></i> Wallet Deduction</div>
            <p class="text-gray-600">The voucher value will be deducted from your wallet balance immediately.</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-3"></i> Recipient Notified</div>
            <p class="text-gray-600">The recipient will receive a notification with the voucher code and login credentials (if new).</p>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-1"><i class="fas fa-4"></i> Use Voucher</div>
            <p class="text-gray-600">The recipient can use the voucher to purchase food items from local shops.</p>
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
document.querySelectorAll('.tab-button').forEach(button => {
  button.addEventListener('click', function() {
    const tabName = this.getAttribute('data-tab');
    
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('border-blue-600', 'text-blue-600');
      btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active state to clicked button
    this.classList.remove('border-transparent', 'text-gray-600');
    this.classList.add('border-blue-600', 'text-blue-600');
  });
});

// Recipient Search Functionality
const searchInput = document.getElementById('recipient-search');
const searchBtn = document.getElementById('search-btn');
const recipientSelect = document.getElementById('recipient-select');

function filterRecipients() {
  const searchTerm = searchInput.value.toLowerCase().trim();
  const options = recipientSelect.querySelectorAll('option');
  let hasMatches = false;
  
  options.forEach(option => {
    if (option.value === '') {
      option.style.display = 'block';
      return;
    }
    
    const text = option.textContent.toLowerCase();
    if (searchTerm === '' || text.includes(searchTerm)) {
      option.style.display = 'block';
      hasMatches = true;
    } else {
      option.style.display = 'none';
    }
  });
  
  // Also filter optgroups
  const optgroups = recipientSelect.querySelectorAll('optgroup');
  optgroups.forEach(optgroup => {
    const visibleOptions = Array.from(optgroup.querySelectorAll('option')).filter(opt => opt.style.display !== 'none');
    optgroup.style.display = visibleOptions.length > 0 ? 'block' : 'none';
  });
}

searchInput.addEventListener('input', filterRecipients);
searchBtn.addEventListener('click', function(e) {
  e.preventDefault();
  filterRecipients();
  recipientSelect.focus();
});

searchInput.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    filterRecipients();
    recipientSelect.focus();
  }
});

// Quantity and Cost Calculation
const voucherValueInput = document.getElementById('voucher-value');
const voucherQuantityInput = document.getElementById('voucher-quantity');
const totalCostDisplay = document.getElementById('total-cost');
const perVoucherCostDisplay = document.getElementById('per-voucher-cost');
const quantityDisplaySpan = document.getElementById('quantity-display');

function updateTotalCost() {
  const value = parseFloat(voucherValueInput.value) || 0;
  const quantity = parseInt(voucherQuantityInput.value) || 1;
  const perVoucher = quantity > 0 ? (value / quantity).toFixed(2) : '0.00';
  const totalCost = (value).toFixed(2);
  
  totalCostDisplay.textContent = '£' + totalCost;
  perVoucherCostDisplay.textContent = perVoucher;
  quantityDisplaySpan.textContent = quantity;
}

voucherValueInput.addEventListener('input', updateTotalCost);
voucherQuantityInput.addEventListener('input', updateTotalCost);

// Initialize on page load
updateTotalCost();

// Manual Tab Quantity and Cost Calculation
const manualVoucherValueInput = document.getElementById('manual-voucher-value');
const manualVoucherQuantityInput = document.getElementById('manual-voucher-quantity');
const manualTotalCostDisplay = document.getElementById('manual-total-cost');
const manualPerVoucherCostDisplay = document.getElementById('manual-per-voucher-cost');
const manualQuantityDisplaySpan = document.getElementById('manual-quantity-display');

function updateManualTotalCost() {
  const value = parseFloat(manualVoucherValueInput.value) || 0;
  const quantity = parseInt(manualVoucherQuantityInput.value) || 1;
  const perVoucher = quantity > 0 ? (value / quantity).toFixed(2) : '0.00';
  
  manualTotalCostDisplay.textContent = '£' + value.toFixed(2);
  manualPerVoucherCostDisplay.textContent = perVoucher;
  manualQuantityDisplaySpan.textContent = quantity;
}

if (manualVoucherValueInput) {
  manualVoucherValueInput.addEventListener('input', updateManualTotalCost);
}
if (manualVoucherQuantityInput) {
  manualVoucherQuantityInput.addEventListener('input', updateManualTotalCost);
}

// Initialize manual tab on page load
if (manualVoucherValueInput) {
  updateManualTotalCost();
}

// Prevent hidden form submission and ensure only active form is submitted
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const tabContent = this.closest('.tab-content');
    // Only allow submission if the form's tab is visible (not hidden)
    if (tabContent && (tabContent.classList.contains('hidden') || window.getComputedStyle(tabContent).display === 'none')) {
      e.preventDefault();
      console.log('Form submission prevented: tab is hidden or not displayed');
      return false;
    }
  });
});

// Also ensure tab buttons properly switch tabs
document.querySelectorAll('.tab-button').forEach(button => {
  button.addEventListener('click', function() {
    const tabName = this.getAttribute('data-tab');
    console.log('Tab button clicked:', tabName);
    
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.add('hidden');
      tab.style.display = 'none';
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('border-blue-600', 'text-blue-600');
      btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
      selectedTab.classList.remove('hidden');
      selectedTab.style.display = 'block';
      console.log('Tab shown:', tabName + '-tab');
    }
    
    // Add active state to clicked button
    this.classList.remove('border-transparent', 'text-gray-600');
    this.classList.add('border-blue-600', 'text-blue-600');
  });
});

// Initialize: show the first tab by default
window.addEventListener('DOMContentLoaded', function() {
  const firstButton = document.querySelector('.tab-button');
  if (firstButton) {
    firstButton.click();
  }
});
</script>

@endsection
