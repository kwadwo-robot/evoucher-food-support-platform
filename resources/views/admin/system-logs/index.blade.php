@extends('layouts.dashboard')
@section('title','System Logs')
@section('page-title','System Logs & Activity')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">System Logs</h1>
        <a href="{{ route('admin.logs.export') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-download mr-2"></i>Export CSV
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-4 gap-4">
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
            <div class="col-span-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Entity</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-left">IP Address</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-3">{{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}</td>
                        <td class="px-6 py-3 text-sm">{{ Str::limit($log->description, 50) }}</td>
                        <td class="px-6 py-3 text-sm">{{ $log->ip_address }}</td>
                        <td class="px-6 py-3 text-sm">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No logs found</td>
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
