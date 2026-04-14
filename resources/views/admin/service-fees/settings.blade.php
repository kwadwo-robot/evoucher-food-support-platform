@extends('layouts.dashboard')

@section('title', 'Service Fee Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Service Fee Settings</h1>
        <p class="text-gray-600 mt-2">Manage the service fee percentage applied to shop payouts</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Settings Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-8">
                <form method="POST" action="{{ route('admin.service-fees.update-percentage') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="service_fee_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Fee Percentage
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="service_fee_percentage" 
                                name="service_fee_percentage"
                                value="{{ $currentSetting->service_fee_percentage ?? 10.00 }}"
                                step="0.01"
                                min="0"
                                max="100"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-lg"
                                placeholder="10.00"
                            >
                            <span class="absolute right-4 top-3 text-2xl text-gray-400">%</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-2">
                            This percentage will be deducted from all shop payouts. For example, if set to 10%, a £100 payout will result in £90 paid to the shop and £10 collected as service fee.
                        </p>
                    </div>

                    <!-- Fee Preview -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-3">Fee Calculation Preview</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-700">Example Payout Amount:</span>
                                <span class="font-medium">£100.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-700">Service Fee (<span id="preview-percentage">10</span>%):</span>
                                <span class="font-medium text-green-600">£<span id="preview-fee">10.00</span></span>
                            </div>
                            <div class="border-t border-blue-200 pt-2 mt-2 flex justify-between">
                                <span class="text-gray-700 font-semibold">Amount to Shop:</span>
                                <span class="font-bold text-blue-600">£<span id="preview-amount">90.00</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                        <h3 class="font-semibold text-yellow-900 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Important Notice
                        </h3>
                        <p class="text-yellow-800 text-sm">
                            Changing the service fee percentage will only affect new payout requests. Existing payout requests will retain their original fee percentage.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                        <a href="{{ route('admin.service-fees.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium text-center transition">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Total Collected</p>
                        <p class="text-2xl font-bold text-green-600">£{{ number_format($stats['total_collected'], 2) }}</p>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm">Total Transactions</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_transactions'] }}</p>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-gray-600 text-sm">Average Fee per Transaction</p>
                        <p class="text-2xl font-bold text-purple-600">£{{ number_format($stats['average_fee_per_transaction'], 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.service-fees.index') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>View All Transactions
                    </a>
                    <a href="{{ route('admin.payouts.index') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-money-bill mr-2"></i>View Payouts
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time fee preview calculation
    const percentageInput = document.getElementById('service_fee_percentage');
    const previewPercentage = document.getElementById('preview-percentage');
    const previewFee = document.getElementById('preview-fee');
    const previewAmount = document.getElementById('preview-amount');

    function updatePreview() {
        const percentage = parseFloat(percentageInput.value) || 0;
        const exampleAmount = 100;
        const fee = (exampleAmount * percentage) / 100;
        const amount = exampleAmount - fee;

        previewPercentage.textContent = percentage.toFixed(2);
        previewFee.textContent = fee.toFixed(2);
        previewAmount.textContent = amount.toFixed(2);
    }

    percentageInput.addEventListener('input', updatePreview);
    updatePreview();
</script>
@endsection
