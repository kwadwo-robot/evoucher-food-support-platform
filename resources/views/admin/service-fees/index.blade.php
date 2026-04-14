@extends('layouts.admin')

@section('title', 'Service Fees')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Service Fees</h1>
                <p class="text-gray-600 mt-2">Monitor and manage service fee transactions</p>
            </div>
            <div class="space-x-3">
                <a href="{{ route('admin.service-fees.settings') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
                <form method="GET" action="{{ route('admin.service-fees.export') }}" class="inline-block">
                    @if($startDate)
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                    @endif
                    @if($endDate)
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                    @endif
                    <button type="submit" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-download mr-2"></i>Export CSV
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Collected</p>
                        <p class="text-2xl font-bold text-green-600">£{{ number_format($stats['total_collected'], 2) }}</p>
                    </div>
                    <div class="text-4xl text-green-100">
                        <i class="fas fa-pound-sign"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Transactions</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_transactions'] }}</p>
                    </div>
                    <div class="text-4xl text-blue-100">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Average Fee</p>
                        <p class="text-2xl font-bold text-purple-600">£{{ number_format($stats['average_fee_per_transaction'], 2) }}</p>
                    </div>
                    <div class="text-4xl text-purple-100">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Current Rate</p>
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['current_percentage'], 2) }}%</p>
                    </div>
                    <div class="text-4xl text-orange-100">
                        <i class="fas fa-percent"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.service-fees.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Shop</label>
                <select name="shop_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Shops</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ $shopId == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('admin.service-fees.index') }}" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-center">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Service Fees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Shop</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Payout Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fee %</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fee Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Amount After Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($serviceFees as $fee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">#{{ $fee->id }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $fee->shopUser->name }}</div>
                            <div class="text-gray-600 text-xs">{{ $fee->shopUser->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">£{{ number_format($fee->payout_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($fee->service_fee_percentage, 2) }}%</td>
                        <td class="px-6 py-4 text-sm font-medium text-green-600">£{{ number_format($fee->service_fee_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-blue-600">£{{ number_format($fee->amount_after_fee, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($fee->status === 'collected') bg-green-100 text-green-800
                                @elseif($fee->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($fee->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $fee->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.service-fees.show', $fee->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-3 block opacity-50"></i>
                            No service fee transactions found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $serviceFees->links() }}
    </div>
</div>
@endsection
