@extends('layouts.dashboard')
@section('title','System Logs')
@section('page-title','System Logs & Activity')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">System Logs & Activity Tracking</h1>
        <a href="{{ route('admin.logs.export') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Activities</div>
            <div class="text-2xl font-bold text-blue-600">{{ $logs->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Logins</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\SystemLog::where('action', 'login')->whereHas('user', function($q) { $q->whereIn('role', ['recipient', 'vcfse', 'school_care']); })->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Vouchers Redeemed</div>
            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\SystemLog::where('action', 'redeem')->whereHas('user', function($q) { $q->whereIn('role', ['recipient', 'vcfse', 'school_care']); })->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Food Listings Created</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\SystemLog::where('action', 'create')->where('entity_type', 'FoodListing')->whereHas('user', function($q) { $q->whereIn('role', ['recipient', 'vcfse', 'school_care']); })->count() }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Filters</h2>
        <form method="GET" class="grid grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">User Role</label>
                <select name="user_role" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Roles</option>
                    <option value="recipient" @selected(request('user_role') === 'recipient')>Recipient</option>
                    <option value="vcfse" @selected(request('user_role') === 'vcfse')>VCFSE</option>
                    <option value="school_care" @selected(request('user_role') === 'school_care')>School/Care</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Entity Type</label>
                <select name="entity_type" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Types</option>
                    @foreach($entityTypes as $type)
                        <option value="{{ $type }}" @selected(request('entity_type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="col-span-5">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Filter</button>
                <a href="{{ route('admin.logs.index') }}" class="ml-2 bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 inline-block">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left">Entity</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-left">IP Address</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3 font-semibold">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-3">
                            @if($log->user)
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $log->user->role)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm">{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</td>
                        <td class="px-6 py-3 text-sm">{{ Str::limit($log->description, 60) }}</td>
                        <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $log->ip_address }}</td>
                        <td class="px-6 py-3 text-sm">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-3 text-sm">
                            <a href="{{ route('admin.logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 font-semibold">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No activity logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection
