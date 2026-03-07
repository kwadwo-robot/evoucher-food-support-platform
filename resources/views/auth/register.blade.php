<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — eVoucher Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#f1f5f9;min-height:100vh;padding:40px 20px}
.reg-wrap{max-width:680px;margin:0 auto}
.reg-header{text-align:center;margin-bottom:32px}
.reg-logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:20px;text-decoration:none}
.reg-logo-icon{width:44px;height:44px;background:#16a34a;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px}
.reg-logo-text{font-size:18px;font-weight:900;color:#0f172a}
.role-tabs{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:24px}
.role-tab{padding:14px 12px;border:2px solid #e2e8f0;border-radius:12px;cursor:pointer;transition:all .15s;text-align:center;background:#fff}
.role-tab.active{border-color:#16a34a;background:#f0fdf4}
.role-tab-icon{font-size:20px;margin-bottom:6px}
.role-tab-name{font-size:13px;font-weight:700;color:#0f172a}
.role-tab-desc{font-size:11px;color:#94a3b8;margin-top:2px}
.card{background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:28px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.form-label{display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:5px}
.form-input{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;outline:none;transition:border .15s;font-family:'Inter',sans-serif}
.form-input:focus{border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.1)}
.form-select{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;outline:none;transition:border .15s;font-family:'Inter',sans-serif}
.form-select:focus{border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.1)}
.btn-submit{width:100%;padding:13px;background:#16a34a;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:6px}
.btn-submit:hover{background:#15803d;transform:translateY(-1px)}
[x-cloak]{display:none!important}
</style>
</head>
<body>
<div class="reg-wrap" x-data="{
  role: '{{ old('role', 'recipient') }}',
  setRole(r) {
    this.role = r;
    document.getElementById('role_input').value = r;
  }
}" x-init="document.getElementById('role_input').value = role">
  <div class="reg-header">
    <a href="/" class="reg-logo">
      <div class="reg-logo-icon"><i class="fas fa-leaf"></i></div>
      <div class="reg-logo-text">eVoucher Platform</div>
    </a>
    <h1 style="font-size:26px;font-weight:900;color:#0f172a;margin-bottom:6px">Create Your Account</h1>
    <p style="font-size:14px;color:#94a3b8">Join the Northamptonshire eVoucher Food Support Programme</p>
  </div>

  @if($errors->any())
  <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#b91c1c">
    <i class="fas fa-exclamation-circle mr-2"></i>
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
  </div>
  @endif

  <!-- Role Selector -->
  <div style="margin-bottom:20px">
    <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;text-align:center">I am registering as a...</div>
    <div class="role-tabs">
      <div class="role-tab" :class="role === 'recipient' ? 'active' : ''" @click="setRole('recipient')">
        <div class="role-tab-icon">👤</div>
        <div class="role-tab-name">Recipient</div>
        <div class="role-tab-desc">I need food support</div>
      </div>
      <div class="role-tab" :class="role === 'local_shop' ? 'active' : ''" @click="setRole('local_shop')">
        <div class="role-tab-icon">🏪</div>
        <div class="role-tab-name">Local Shop</div>
        <div class="role-tab-desc">I want to list food</div>
      </div>
      <div class="role-tab" :class="role === 'vcfse' ? 'active' : ''" @click="setRole('vcfse')">
        <div class="role-tab-icon">🏛️</div>
        <div class="role-tab-name">VCFSE Organisation</div>
        <div class="role-tab-desc">We want to donate</div>
      </div>
      <div class="role-tab" :class="role === 'school_care' ? 'active' : ''" @click="setRole('school_care')">
        <div class="role-tab-icon">🏫</div>
        <div class="role-tab-name">School / Care Org</div>
        <div class="role-tab-desc">We want to support</div>
      </div>
    </div>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('register') }}">
      @csrf
      {{-- Plain hidden input updated by JS on role change --}}
      <input type="hidden" id="role_input" name="role" value="{{ old('role', 'recipient') }}">

      <!-- Common Fields -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="form-label">Full Name *</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="Your full name" required>
        </div>
        <div>
          <label class="form-label">Email Address *</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="you@example.com" required>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
        </div>
        <div>
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Phone Number</label>
        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="e.g. 07700 900000">
      </div>

      <!-- Recipient Fields -->
      <div x-show="role === 'recipient'" x-cloak>
        <div style="padding:12px 16px;background:#f0fdf4;border-radius:10px;margin-bottom:16px;font-size:13px;color:#15803d;display:flex;align-items:center;gap:8px">
          <i class="fas fa-info-circle"></i> Your account will be reviewed and approved by an admin before you can access vouchers.
        </div>
        <div class="mb-4">
          <label class="form-label">Address</label>
          <input type="text" name="address" value="{{ old('address') }}" class="form-input" placeholder="Your home address">
        </div>
      </div>

      <!-- Shop Fields -->
      <div x-show="role === 'local_shop'" x-cloak>
        <div style="height:1px;background:#f1f5f9;margin:4px 0 16px"></div>
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Shop Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Shop Name *</label>
            <input type="text" name="shop_name" value="{{ old('shop_name') }}" class="form-input" placeholder="Your shop name">
          </div>
          <div>
            <label class="form-label">Business Registration</label>
            <input type="text" name="business_reg" value="{{ old('business_reg') }}" class="form-input" placeholder="Optional">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Shop Address *</label>
          <input type="text" name="shop_address" value="{{ old('shop_address') }}" class="form-input" placeholder="Full shop address">
        </div>
      </div>

      <!-- VCFSE Fields -->
      <div x-show="role === 'vcfse'" x-cloak>
        <div style="height:1px;background:#f1f5f9;margin:4px 0 16px"></div>
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Organisation Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Organisation Name *</label>
            <input type="text" name="org_name" id="vcfse_org_name" value="{{ old('role') === 'vcfse' ? old('org_name') : '' }}" class="form-input" placeholder="Organisation name">
          </div>
          <div>
            <label class="form-label">Charity Number</label>
            <input type="text" name="charity_number" value="{{ old('role') === 'vcfse' ? old('charity_number') : '' }}" class="form-input" placeholder="e.g. 1234567">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Contact Person</label>
          <input type="text" name="contact_name" value="{{ old('role') === 'vcfse' ? old('contact_name') : '' }}" class="form-input" placeholder="Primary contact name">
        </div>
      </div>

      <!-- School Fields -->
      <div x-show="role === 'school_care'" x-cloak>
        <div style="height:1px;background:#f1f5f9;margin:4px 0 16px"></div>
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Organisation Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Organisation Name *</label>
            <input type="text" name="school_org_name" id="school_org_name" value="{{ old('role') === 'school_care' ? old('school_org_name') : '' }}" class="form-input" placeholder="School or care org name">
          </div>
          <div>
            <label class="form-label">Organisation Type</label>
            <select name="org_type" class="form-select">
              <option value="school" {{ old('org_type') === 'school' ? 'selected' : '' }}>School</option>
              <option value="care_home" {{ old('org_type') === 'care_home' ? 'selected' : '' }}>Care Home</option>
              <option value="ngo" {{ old('org_type') === 'ngo' ? 'selected' : '' }}>NGO</option>
              <option value="other" {{ old('org_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Contact Person</label>
          <input type="text" name="school_contact_name" value="{{ old('role') === 'school_care' ? old('school_contact_name') : '' }}" class="form-input" placeholder="Primary contact name">
        </div>
      </div>

      <button type="submit" class="btn-submit">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
    </form>
  </div>

  <div style="text-align:center;margin-top:20px;font-size:13.5px;color:#94a3b8">
    Already have an account? <a href="{{ route('login') }}" style="color:#16a34a;font-weight:700;text-decoration:none">Sign in here</a>
  </div>
  <div style="text-align:center;margin-top:10px">
    <a href="/" style="font-size:12.5px;color:#94a3b8;text-decoration:none"><i class="fas fa-arrow-left mr-1"></i> Back to Home</a>
  </div>
</div>
</body>
</html>
