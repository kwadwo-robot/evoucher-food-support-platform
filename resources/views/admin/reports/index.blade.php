@extends('layouts.dashboard')
@section('page-title', 'Reports')
@section('title', 'Reports')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
        <p class="text-gray-500 text-sm mt-1">Pilot performance &mdash; Northamptonshire</p>
    </div>
    <a href="{{ route('admin.reports.export') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fas fa-download mr-1"></i> Export CSV
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-green-600 mb-1">£{{ number_format($data['total_donations'], 2) }}</div>
        <div class="text-xs text-gray-500">Total Donated</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $data['total_vouchers_issued'] }}</div>
        <div class="text-xs text-gray-500">Vouchers Issued</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-orange-600 mb-1">{{ $data['total_redemptions'] }}</div>
        <div class="text-xs text-gray-500">Redemptions</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-purple-600 mb-1">{{ $data['food_redeemed'] }}</div>
        <div class="text-xs text-gray-500">Food Items Redeemed</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-teal-600 mb-1">{{ $data['total_recipients'] }}</div>
        <div class="text-xs text-gray-500">Recipients</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $data['total_shops'] }}</div>
        <div class="text-xs text-gray-500">Local Shops</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-pink-600 mb-1">{{ $data['total_donors'] }}</div>
        <div class="text-xs text-gray-500">Donor Organisations</div>
    </div>
    <div class="stat-card text-center">
        <div class="text-3xl font-bold text-gray-600 mb-1">{{ $data['total_food_listed'] }}</div>
        <div class="text-xs text-gray-500">Food Items Listed</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="font-semibold text-gray-900 mb-4">Monthly Donations</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2 text-xs font-semibold text-gray-500">Month</th>
                    <th class="text-right px-4 py-2 text-xs font-semibold text-gray-500">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($monthly_donations as $row)
                <tr>
                    <td class="px-4 py-2 text-gray-700">{{ date('F Y', mktime(0,0,0,$row->month,1,$row->year)) }}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-600">£{{ number_format($row->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">No donation data yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
