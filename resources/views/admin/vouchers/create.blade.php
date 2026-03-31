@extends('layouts.dashboard')
@section('title', 'Issue Voucher')

@section('content')
<div class="page-header">
    <div class="page-title">Issue New Voucher</div>
    <div class="page-desc">Create and assign a voucher to an approved recipient</div>
</div>

<div style="max-width:900px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-ticket" style="color:#16a34a;margin-right:8px;"></i>Issue New Voucher</span>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <div style="display:flex;gap:12px;border-bottom:2px solid #e2e8f0;margin-bottom:24px;">
                <button type="button" class="tab-btn active" data-tab="registered" style="padding:12px 20px;border:none;background:none;cursor:pointer;font-weight:500;color:#1e293b;border-bottom:3px solid #16a34a;margin-bottom:-2px;">
                    <i class="fas fa-user-check" style="margin-right:8px;"></i>Issue to Registered Recipient
                </button>
                <button type="button" class="tab-btn" data-tab="manual" style="padding:12px 20px;border:none;background:none;cursor:pointer;font-weight:500;color:#64748b;border-bottom:3px solid transparent;margin-bottom:-2px;">
                    <i class="fas fa-user-plus" style="margin-right:8px;"></i>Issue to New Recipient
                </button>
            </div>

            <!-- Registered Recipient Tab -->
            <div id="registered-tab" class="tab-content" style="display:block;">
                <form method="POST" action="{{ route('admin.vouchers.store') }}">
                    @csrf
                    <input type="hidden" name="issue_type" value="registered">
                    <div style="display:grid;gap:18px;">
                        <div>
                            <label class="form-label">Recipient <span style="color:#ef4444;">*</span></label>
                            <div style="display:flex;gap:8px;margin-bottom:8px;">
                                <input type="text" id="recipient-search" class="form-input" placeholder="Search recipient by name or email..." style="flex:1;">
                                <button type="button" id="search-btn" class="btn btn-primary" style="padding:8px 16px;white-space:nowrap;" title="Search recipients">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <button type="button" id="clear-search" class="btn btn-secondary" style="padding:8px 16px;" title="Clear search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <select name="recipient_id" id="recipient-select" class="form-select" required>
                                <option value="">— Select Recipient —</option>
                                @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}" data-name="{{ strtolower($recipient->name) }}" data-email="{{ strtolower($recipient->email) }}" {{ old('recipient_id') == $recipient->id ? 'selected' : '' }}>
                                    {{ $recipient->name }} ({{ $recipient->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('recipient_id') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                            <div>
                                <label class="form-label">Voucher Amount (&pound;) <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="value" id="admin-voucher-value" value="{{ old('value', '10.00') }}" step="0.01" min="0.01" max="500" class="form-input" required>
                                @error('value') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Number of Vouchers <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="quantity" id="admin-voucher-quantity" value="{{ old('quantity', 1) }}" step="1" min="1" max="100" class="form-input" required>
                                @error('quantity') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div style="padding:12px;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;">
                            <p style="font-size:14px;color:#1e40af;margin:0;"><i class="fas fa-info-circle" style="margin-right:8px;"></i> <strong>Total Cost:</strong> <span id="admin-total-cost">&pound;10.00</span> (&pound;<span id="admin-per-voucher-cost">10.00</span> per voucher &times; <span id="admin-quantity-display">1</span> vouchers)</p>
                        </div>
                        <div>
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', now()->addDays(30)->format('Y-m-d')) }}" class="form-input">
                            <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Leave blank for no expiry. Default is 30 days from today.</div>
                        </div>
                        <div>
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" rows="3" class="form-textarea" placeholder="Internal notes about this voucher...">{{ old('notes') }}</textarea>
                        </div>
                        <div style="display:flex;gap:12px;padding-top:4px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Issue Voucher
                            </button>
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Manual Recipient Tab -->
            <div id="manual-tab" class="tab-content" style="display:none;">
                <form method="POST" action="{{ route('admin.vouchers.store') }}">
                    @csrf
                    <input type="hidden" name="issue_type" value="manual">
                    <div style="display:grid;gap:18px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                            <div>
                                <label class="form-label">First Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-input" required>
                                @error('first_name') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Last Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-input" required>
                                @error('last_name') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                            @error('email') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input">
                            @error('phone') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                            <div>
                                <label class="form-label">County <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="county" value="{{ old('county') }}" class="form-input" required>
                                @error('county') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Post Code <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="post_code" value="{{ old('post_code') }}" class="form-input" required>
                                @error('post_code') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Category <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="category" value="{{ old('category') }}" class="form-input" placeholder="e.g., Individual, Family, Senior" required>
                            @error('category') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="form-label">Organization <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="organization" value="{{ old('organization') }}" class="form-input" required>
                            @error('organization') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                            <div>
                                <label class="form-label">Voucher Amount (&pound;) <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="manual_amount" id="manual-voucher-value" value="{{ old('manual_amount', '10.00') }}" step="0.01" min="0.01" max="500" class="form-input" required>
                                @error('manual_amount') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="form-label">Number of Vouchers <span style="color:#ef4444;">*</span></label>
                                <input type="number" name="manual_quantity" id="manual-voucher-quantity" value="{{ old('manual_quantity', 1) }}" step="1" min="1" max="100" class="form-input" required>
                                @error('manual_quantity') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div style="padding:12px;background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;">
                            <p style="font-size:14px;color:#1e40af;margin:0;"><i class="fas fa-info-circle" style="margin-right:8px;"></i> <strong>Total Cost:</strong> <span id="manual-total-cost">&pound;10.00</span> (&pound;<span id="manual-per-voucher-cost">10.00</span> per voucher &times; <span id="manual-quantity-display">1</span> vouchers)</p>
                        </div>
                        <div>
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', now()->addDays(30)->format('Y-m-d')) }}" class="form-input">
                            <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Leave blank for no expiry. Default is 30 days from today.</div>
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="2" class="form-textarea" placeholder="Description of the voucher...">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Conditions</label>
                            <textarea name="conditions" rows="2" class="form-textarea" placeholder="Any conditions or restrictions...">{{ old('conditions') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Internal Notes (optional)</label>
                            <textarea name="internal_notes" rows="3" class="form-textarea" placeholder="Internal notes about this voucher...">{{ old('internal_notes') }}</textarea>
                        </div>
                        <div style="display:flex;gap:12px;padding-top:4px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Issue Voucher
                            </button>
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.style.color = '#64748b';
            b.style.borderBottomColor = 'transparent';
        });
        
        // Show selected tab
        document.getElementById(tabName + '-tab').style.display = 'block';
        
        // Add active class to clicked button
        this.style.color = '#1e293b';
        this.style.borderBottomColor = '#16a34a';
    });
});

// Admin (Registered Recipient) Quantity and Cost Calculation
const adminVoucherValueInput = document.getElementById('admin-voucher-value');
const adminVoucherQuantityInput = document.getElementById('admin-voucher-quantity');
const adminTotalCostDisplay = document.getElementById('admin-total-cost');
const adminPerVoucherCostDisplay = document.getElementById('admin-per-voucher-cost');
const adminQuantityDisplaySpan = document.getElementById('admin-quantity-display');

function updateAdminTotalCost() {
  const value = parseFloat(adminVoucherValueInput.value) || 0;
  const quantity = parseInt(adminVoucherQuantityInput.value) || 1;
  const perVoucherCost = (value / quantity).toFixed(2);
  
  adminTotalCostDisplay.textContent = '£' + value.toFixed(2);
  adminPerVoucherCostDisplay.textContent = perVoucherCost;
  adminQuantityDisplaySpan.textContent = quantity;
}

adminVoucherValueInput.addEventListener('input', updateAdminTotalCost);
adminVoucherQuantityInput.addEventListener('input', updateAdminTotalCost);

// Manual (New Recipient) Quantity and Cost Calculation
const manualVoucherValueInput = document.getElementById('manual-voucher-value');
const manualVoucherQuantityInput = document.getElementById('manual-voucher-quantity');
const manualTotalCostDisplay = document.getElementById('manual-total-cost');
const manualPerVoucherCostDisplay = document.getElementById('manual-per-voucher-cost');
const manualQuantityDisplaySpan = document.getElementById('manual-quantity-display');

function updateManualTotalCost() {
  const value = parseFloat(manualVoucherValueInput.value) || 0;
  const quantity = parseInt(manualVoucherQuantityInput.value) || 1;
  const perVoucherCost = (value / quantity).toFixed(2);
  
  manualTotalCostDisplay.textContent = '£' + value.toFixed(2);
  manualPerVoucherCostDisplay.textContent = perVoucherCost;
  manualQuantityDisplaySpan.textContent = quantity;
}

manualVoucherValueInput.addEventListener('input', updateManualTotalCost);
manualVoucherQuantityInput.addEventListener('input', updateManualTotalCost);

// Recipient Search Functionality
const recipientSearchInput = document.getElementById('recipient-search');
const recipientSelect = document.getElementById('recipient-select');
const searchBtn = document.getElementById('search-btn');
const clearSearchBtn = document.getElementById('clear-search');
const allOptions = Array.from(recipientSelect.options);

function filterRecipients(searchTerm) {
  const lowerSearchTerm = searchTerm.toLowerCase();
  let visibleCount = 0;
  
  allOptions.forEach((option, index) => {
    if (index === 0) return; // Skip the "Select Recipient" option
    
    const name = option.getAttribute('data-name') || '';
    const email = option.getAttribute('data-email') || '';
    const matches = name.includes(lowerSearchTerm) || email.includes(lowerSearchTerm);
    
    option.style.display = matches ? 'block' : 'none';
    if (matches) visibleCount++;
  });
  
  // If no results, show a message
  if (visibleCount === 0) {
    recipientSelect.title = 'No recipients found';
  } else {
    recipientSelect.title = '';
  }
}

// Search button click handler
searchBtn.addEventListener('click', function(e) {
  e.preventDefault();
  filterRecipients(recipientSearchInput.value);
  recipientSelect.focus();
});

// Real-time search as user types
recipientSearchInput.addEventListener('input', function() {
  filterRecipients(this.value);
});

// Allow Enter key to trigger search
recipientSearchInput.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    searchBtn.click();
  }
});

clearSearchBtn.addEventListener('click', function(e) {
  e.preventDefault();
  recipientSearchInput.value = '';
  filterRecipients('');
  recipientSearchInput.focus();
});

// Initialize on page load
updateAdminTotalCost();
updateManualTotalCost();
</script>

@endsection
