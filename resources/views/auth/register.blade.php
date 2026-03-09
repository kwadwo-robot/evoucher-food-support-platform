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
.reg-wrap{max-width:720px;margin:0 auto}
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
.form-textarea{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#0f172a;background:#fff;outline:none;transition:border .15s;font-family:'Inter',sans-serif;resize:vertical;min-height:80px}
.form-textarea:focus{border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.1)}
.password-wrapper{position:relative;display:flex;align-items:center}
.password-toggle{position:absolute;right:12px;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px;padding:4px 8px;transition:color .15s}
.password-toggle:hover{color:#64748b}
.btn-submit{width:100%;padding:13px;background:#16a34a;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:6px}
.btn-submit:hover{background:#15803d;transform:translateY(-1px)}
.section-divider{height:1px;background:#f1f5f9;margin:4px 0 16px}
.section-title{font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px}
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
    <a href="/" class="reg-logo" style="text-decoration:none">
      <img src="{{ asset('images/logo.png') }}" alt="eVoucher" style="width:44px;height:44px;object-fit:contain">
      <div style="display:flex;flex-direction:column;line-height:1.2">
        <div style="font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em">BAKUP CIC</div>
        <div class="reg-logo-text">eVoucher</div>
      </div>
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
        <div class="role-tab-name">Local Food Shop</div>
        <div class="role-tab-desc">I want to list food</div>
      </div>
      <div class="role-tab" :class="role === 'vcfse' ? 'active' : ''" @click="setRole('vcfse')">
        <div class="role-tab-icon">🏛️</div>
        <div class="role-tab-name">VCFSE Organisation</div>
        <div class="role-tab-desc">We want to support</div>
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
          <div class="password-wrapper">
            <input type="password" name="password" id="password" class="form-input" placeholder="Min. 8 characters" required style="width:100%">
            <button type="button" class="password-toggle" onclick="togglePassword('password')">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        <div>
          <label class="form-label">Confirm Password *</label>
          <div class="password-wrapper">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" placeholder="Repeat password" required style="width:100%">
            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
              <i class="fas fa-eye"></i>
            </button>
          </div>
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
        <div class="mb-4">
          <label class="form-label">Postcode</label>
          <input type="text" name="postcode" value="{{ old('postcode') }}" class="form-input" placeholder="e.g. NN9 6GR" style="max-width:200px">
        </div>
      </div>

      <!-- Local Food Shop Fields -->
      <div x-show="role === 'local_shop'" x-cloak>
        <div class="section-divider"></div>
        <div class="section-title"><i class="fas fa-store mr-1" style="color:#16a34a"></i> Shop Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Shop Name *</label>
            <input type="text" name="shop_name" value="{{ old('shop_name') }}" class="form-input" placeholder="Your shop name">
          </div>
          <div>
            <label class="form-label">Shop Category *</label>
            <select name="shop_category" class="form-select">
              <option value="">-- Select a category --</option>
              <option value="grocery" {{ old('shop_category') === 'grocery' ? 'selected' : '' }}>Grocery / Supermarket</option>
              <option value="butcher" {{ old('shop_category') === 'butcher' ? 'selected' : '' }}>Butcher</option>
              <option value="bakery" {{ old('shop_category') === 'bakery' ? 'selected' : '' }}>Bakery</option>
              <option value="greengrocer" {{ old('shop_category') === 'greengrocer' ? 'selected' : '' }}>Greengrocer / Farm Shop</option>
              <option value="deli" {{ old('shop_category') === 'deli' ? 'selected' : '' }}>Deli / Convenience Store</option>
              <option value="ethnic_food" {{ old('shop_category') === 'ethnic_food' ? 'selected' : '' }}>Ethnic / World Food Shop</option>
              <option value="health_food" {{ old('shop_category') === 'health_food' ? 'selected' : '' }}>Health Food Store</option>
              <option value="fishmonger" {{ old('shop_category') === 'fishmonger' ? 'selected' : '' }}>Fishmonger</option>
              <option value="market_stall" {{ old('shop_category') === 'market_stall' ? 'selected' : '' }}>Market Stall</option>
              <option value="other" {{ old('shop_category') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Shop Address *</label>
          <input type="text" name="shop_address" value="{{ old('shop_address') }}" class="form-input" placeholder="Full shop address (street and building)">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Town / City *</label>
            <input type="text" name="shop_town" value="{{ old('shop_town') }}" class="form-input" placeholder="e.g. Northampton">
          </div>
          <div>
            <label class="form-label">Postcode *</label>
            <input type="text" name="shop_postcode" value="{{ old('shop_postcode') }}" class="form-input" placeholder="e.g. NN1 1AA">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Business Registration Number</label>
          <input type="text" name="business_reg" value="{{ old('business_reg') }}" class="form-input" placeholder="Optional">
        </div>
        <div class="mb-4">
          <label class="form-label">Opening Hours</label>
          <input type="text" name="opening_hours" value="{{ old('opening_hours') }}" class="form-input" placeholder="e.g. Mon–Fri 9am–6pm, Sat 9am–4pm">
        </div>
        <div class="mb-4">
          <label class="form-label">Shop Description</label>
          <textarea name="shop_description" class="form-textarea" placeholder="Brief description of your shop and the products you offer">{{ old('shop_description') }}</textarea>
        </div>
      </div>

      <!-- VCFSE Organisation Fields -->
      <div x-show="role === 'vcfse'" x-cloak>
        <div class="section-divider"></div>
        <div class="section-title"><i class="fas fa-building mr-1" style="color:#16a34a"></i> VCFSE Organisation Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Organisation Name *</label>
            <input type="text" name="org_name" id="vcfse_org_name" value="{{ old('role') === 'vcfse' ? old('org_name') : '' }}" class="form-input" placeholder="Organisation name">
          </div>
          <div>
            <label class="form-label">Charity / Registration Number</label>
            <input type="text" name="charity_number" value="{{ old('role') === 'vcfse' ? old('charity_number') : '' }}" class="form-input" placeholder="e.g. 1234567">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Contact Person *</label>
          <input type="text" name="contact_name" value="{{ old('role') === 'vcfse' ? old('contact_name') : '' }}" class="form-input" placeholder="Primary contact full name">
        </div>
        <div class="mb-4">
          <label class="form-label">Organisation Address *</label>
          <input type="text" name="org_address" value="{{ old('role') === 'vcfse' ? old('org_address') : '' }}" class="form-input" placeholder="Full organisation address">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Town / City *</label>
            <input type="text" name="org_town" value="{{ old('role') === 'vcfse' ? old('org_town') : '' }}" class="form-input" placeholder="e.g. Northampton">
          </div>
          <div>
            <label class="form-label">Postcode *</label>
            <input type="text" name="org_postcode" value="{{ old('role') === 'vcfse' ? old('org_postcode') : '' }}" class="form-input" placeholder="e.g. NN1 1AA">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Website</label>
          <input type="url" name="org_website" value="{{ old('role') === 'vcfse' ? old('org_website') : '' }}" class="form-input" placeholder="https://www.yourorganisation.org.uk">
        </div>
      </div>

      <!-- School / Care Organisation Fields -->
      <div x-show="role === 'school_care'" x-cloak>
        <div class="section-divider"></div>
        <div class="section-title"><i class="fas fa-school mr-1" style="color:#16a34a"></i> School / Care Organisation Details</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Organisation Name *</label>
            <input type="text" name="school_org_name" id="school_org_name" value="{{ old('role') === 'school_care' ? old('school_org_name') : '' }}" class="form-input" placeholder="School or care organisation name">
          </div>
          <div>
            <label class="form-label">Organisation Type *</label>
            <select name="org_type" class="form-select">
              <option value="">-- Select type --</option>
              <option value="school" {{ old('org_type') === 'school' ? 'selected' : '' }}>School</option>
              <option value="care_home" {{ old('org_type') === 'care_home' ? 'selected' : '' }}>Care Home</option>
              <option value="ngo" {{ old('org_type') === 'ngo' ? 'selected' : '' }}>NGO</option>
              <option value="other" {{ old('org_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Contact Person *</label>
          <input type="text" name="school_contact_name" value="{{ old('role') === 'school_care' ? old('school_contact_name') : '' }}" class="form-input" placeholder="Primary contact full name">
        </div>
        <div class="mb-4">
          <label class="form-label">Organisation Address *</label>
          <input type="text" name="school_address" value="{{ old('role') === 'school_care' ? old('school_address') : '' }}" class="form-input" placeholder="Full organisation address">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Town / City *</label>
            <input type="text" name="school_town" value="{{ old('role') === 'school_care' ? old('school_town') : '' }}" class="form-input" placeholder="e.g. Northampton">
          </div>
          <div>
            <label class="form-label">Postcode *</label>
            <input type="text" name="school_postcode" value="{{ old('role') === 'school_care' ? old('school_postcode') : '' }}" class="form-input" placeholder="e.g. NN1 1AA">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Website</label>
          <input type="url" name="school_website" value="{{ old('role') === 'school_care' ? old('school_website') : '' }}" class="form-input" placeholder="https://www.yourschool.sch.uk">
        </div>
        <div class="mb-4">
          <label class="form-label">Registration / Ofsted Number</label>
          <input type="text" name="school_reg_number" value="{{ old('role') === 'school_care' ? old('school_reg_number') : '' }}" class="form-input" placeholder="Optional — school URN, Ofsted, or care registration number">
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
<script>
function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const button = event.target.closest('.password-toggle');
  if (field.type === 'password') {
    field.type = 'text';
    button.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    field.type = 'password';
    button.innerHTML = '<i class="fas fa-eye"></i>';
  }
}
</script>
</body>
</html>
