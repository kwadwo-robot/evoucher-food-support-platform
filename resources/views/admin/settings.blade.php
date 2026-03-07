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

    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:24px 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9">
      <i class="fas fa-credit-card text-purple-500 mr-2"></i>Stripe Settings
    </h3>
    <div class="mb-4">
      <label class="form-label">Stripe Publishable Key</label>
      <input type="text" name="stripe_key" class="form-input" value="{{ $settings['stripe_key']->value ?? '' }}" placeholder="pk_live_...">
    </div>
    <div class="mb-6">
      <label class="form-label">Minimum Donation Amount (£)</label>
      <input type="number" name="min_donation" class="form-input" value="{{ $settings['min_donation']->value ?? 10 }}" min="1" step="0.01" style="max-width:160px">
    </div>

    <button type="submit" class="btn btn-primary">Save Settings</button>
  </form>
</div>
@endsection
