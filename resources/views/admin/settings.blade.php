@extends('layouts.dashboard')
@section('title','Platform Settings')
@section('page-title','Settings')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Platform Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Configure eVoucher platform settings</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
    <i class="fas fa-check-circle text-green-600"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<form method="POST" action="{{ route('admin.settings.save') }}" class="space-y-6" style="max-width:720px">
    @csrf

    {{-- General Settings --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="fas fa-cog text-green-600"></i>
            <h2 class="text-sm font-semibold text-gray-900">General Settings</h2>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Platform Name</label>
                <input type="text" name="platform_name"
                    value="{{ $settings['platform_name']->value ?? 'eVoucher Food Support Platform' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilot Area</label>
                <input type="text" name="pilot_area"
                    value="{{ $settings['pilot_area']->value ?? 'Northamptonshire' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Voucher Expiry (days)</label>
                    <input type="number" name="voucher_expiry_days"
                        value="{{ $settings['voucher_expiry_days']->value ?? 30 }}"
                        min="1" max="365"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Voucher Value (£)</label>
                    <input type="number" name="max_voucher_value"
                        value="{{ $settings['max_voucher_value']->value ?? 50 }}"
                        min="1" step="0.01"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
                </div>
            </div>
        </div>
    </div>

    {{-- Email Settings --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="fas fa-envelope text-blue-500"></i>
            <h2 class="text-sm font-semibold text-gray-900">Email Settings</h2>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                <input type="email" name="support_email"
                    value="{{ $settings['support_email']->value ?? 'support@evoucher.org' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notification Email</label>
                <input type="email" name="admin_email"
                    value="{{ $settings['admin_email']->value ?? 'admin@evoucher.org' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
        </div>
    </div>

    {{-- Stripe / Payment Settings --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <i class="fas fa-credit-card text-purple-500"></i>
            <h2 class="text-sm font-semibold text-gray-900">Stripe Payment Settings</h2>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-1"></i>
                Enter your Stripe keys from the
                <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="text-purple-600 underline font-medium">Stripe Dashboard</a>.
                Use <strong>test keys</strong> (<code>pk_test_</code> / <code>sk_test_</code>) for testing and
                <strong>live keys</strong> (<code>pk_live_</code> / <code>sk_live_</code>) for production.
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Stripe Publishable Key
                    <span class="text-gray-400 font-normal">(used in the browser)</span>
                </label>
                <input type="text" name="stripe_key"
                    value="{{ $settings['stripe_key']->value ?? '' }}"
                    placeholder="pk_live_... or pk_test_..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Stripe Secret Key
                    <span class="text-gray-400 font-normal">(kept on the server)</span>
                </label>
                <div class="relative">
                    <input type="password" name="stripe_secret" id="stripe_secret_input"
                        value="{{ $settings['stripe_secret']->value ?? '' }}"
                        placeholder="sk_live_... or sk_test_..."
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none font-mono">
                    <button type="button" onclick="toggleStripeSecret()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        title="Show/Hide">
                        <i class="fas fa-eye" id="stripe_secret_eye"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1"><i class="fas fa-lock mr-1"></i>This key is stored securely and never exposed to the browser.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Donation Amount (£)</label>
                <input type="number" name="min_donation"
                    value="{{ $settings['min_donation']->value ?? 10 }}"
                    min="1" step="0.01"
                    class="w-48 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 pb-6">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors">
            <i class="fas fa-save mr-2"></i>Save Settings
        </button>
        <span class="text-xs text-gray-400">Changes take effect immediately.</span>
    </div>
</form>

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
