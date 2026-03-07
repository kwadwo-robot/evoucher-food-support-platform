@extends('layouts.dashboard')
@section('title','System Log Details')
@section('page-title','System Log Details')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.logs.index') }}" class="text-green-600 hover:text-green-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Logs
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Log Details</h1>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">ID</label>
                <p class="text-lg">{{ $log->id }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">User</label>
                <p class="text-lg">{{ $log->user?->name ?? 'System' }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Action</label>
                <p class="text-lg"><span class="bg-blue-100 text-blue-800 px-3 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Entity Type</label>
                <p class="text-lg">{{ $log->entity_type }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Entity ID</label>
                <p class="text-lg">{{ $log->entity_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">IP Address</label>
                <p class="text-lg">{{ $log->ip_address }}</p>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Description</label>
                <p class="text-lg">{{ $log->description }}</p>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Timestamp</label>
                <p class="text-lg">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
