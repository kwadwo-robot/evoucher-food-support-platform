@extends('layouts.dashboard')
@section('title','Platform Settings')
@section('page-title','Settings')
@section('content')
<div class="page-hd">
  <h1>Platform Settings</h1>
  <p>Configure eVoucher platform settings</p>
</div>

@if(session('success'))
<div class="alert-success mb-4">{{ session('success') }}</div>
@endif

<div class="card" style="max-width:680px">
  <form method="POST" action="{{ route('admin.settings.save') }}">
    @csrf

    {{-- General Settings --}}
    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
      <i class="fas fa-cog text-green-600 mr-2"></i>General Settings
    </h3>
    <div class="mb-4">
      <label class="form-label">Platform Name</label>
      <input type="text" name="platform_name" class="form-input" value="{{ $settings['platform_name']->value ?? 'eVoucher Food Support Platform' }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Pilot Area</label>
      <input type="text" name="pilot_area" class="form-input" value="{{ $settings['pilot_area']->value ?? 'Northamptonshire' }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Default Voucher Expiry (days)</label>
      <input type="number" name="voucher_expiry_days" class="form-input" value="{{ $settings['voucher_expiry_days']->value ?? 30 }}" min="1" max="365" style="max-width:160px">
    </div>
    <div class="mb-4">
      <label class="form-label">Max Voucher Value (£)</label>
      <input type="number" name="max_voucher_value" class="form-input" value="{{ $settings['max_voucher_value']->value ?? 50 }}" min="1" step="0.01" style="max-width:160px">
    </div>

    {{-- Email Settings --}}
    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:24px 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
      <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Settings
    </h3>
    <div class="mb-4">
      <label class="form-label">Support Email</label>
      <input type="email" name="support_email" class="form-input" value="{{ $settings['support_email']->value ?? 'support@evoucher.org' }}">
    </div>
    <div class="mb-4">
      <label class="form-label">Admin Notification Email</label>
      <input type="email" name="admin_email" class="form-input" value="{{ $settings['admin_email']->value ?? 'admin@evoucher.org' }}">
    </div>

    {{-- Stripe / Payment Settings --}}
    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:24px 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
      <i class="fas fa-credit-card text-purple-500 mr-2"></i>Stripe Payment Settings
    </h3>
    <div class="mb-2" style="background:#fefce8;border:1px solid #fde047;border-radius:8px;padding:10px 14px;font-size:13px;color:#713f12;">
      <i class="fas fa-info-circle mr-1"></i>
      Enter your Stripe keys from the <a href="https://dashboard.stripe.com/apikeys" target="_blank" style="color:#7c3aed;text-decoration:underline;">Stripe Dashboard</a>.
      Use <strong>test keys</strong> (starting with <code>pk_test_</code> / <code>sk_test_</code>) for testing, and <strong>live keys</strong> (starting with <code>pk_live_</code> / <code>sk_live_</code>) for production.
    </div>
    <div class="mb-4 mt-3">
      <label class="form-label">Stripe Publishable Key <span style="color:#6b7280;font-weight:400;">(used in the browser)</span></label>
      <input type="text" name="stripe_key" class="form-input" value="{{ $settings['stripe_key']->value ?? '' }}" placeholder="pk_live_... or pk_test_...">
    </div>
    <div class="mb-4">
      <label class="form-label">Stripe Secret Key <span style="color:#6b7280;font-weight:400;">(kept on the server)</span></label>
      <div style="position:relative;">
        <input type="password" name="stripe_secret" id="stripe_secret_input" class="form-input" value="{{ $settings['stripe_secret']->value ?? '' }}" placeholder="sk_live_... or sk_test_..." autocomplete="new-password" style="padding-right:44px;">
        <button type="button" onclick="toggleStripeSecret()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7280;" title="Show/Hide">
          <i class="fas fa-eye" id="stripe_secret_eye"></i>
        </button>
      </div>
      <p style="font-size:12px;color:#6b7280;margin-top:4px;"><i class="fas fa-lock mr-1"></i>This key is stored securely and never exposed to the browser.</p>
    </div>
    <div class="mb-6">
      <label class="form-label">Minimum Donation Amount (£)</label>
      <input type="number" name="min_donation" class="form-input" value="{{ $settings['min_donation']->value ?? 10 }}" min="1" step="0.01" style="max-width:160px">
    </div>

    <button type="submit" class="btn btn-primary">Save Settings</button>
  </form>
</div>

<script>
function toggleStripeSecret() {
    var input = document.getElementById('stripe_secret_input');
    var eye = document.getElementById('stripe_secret_eye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>
@endsection
