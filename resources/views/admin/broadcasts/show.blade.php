@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Broadcast Details</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Title</p>
                <p class="text-lg font-semibold text-gray-900">{{ $broadcast->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $broadcast->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($broadcast->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600">Sent At</p>
                <p class="text-lg font-semibold text-gray-900">{{ $broadcast->sent_at ? $broadcast->sent_at->format('M d, Y H:i') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Recipients</p>
                <p class="text-lg font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">Message</p>
            <div class="bg-gray-50 rounded p-4 mt-2">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $broadcast->message }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Delivery Status</h2>
        
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded p-4">
                <p class="text-sm text-gray-600">Total Sent</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['sent'] ?? 0 }}</p>
            </div>
            <div class="bg-red-50 rounded p-4">
                <p class="text-sm text-gray-600">Failed</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] ?? 0 }}</p>
            </div>
            <div class="bg-yellow-50 rounded p-4">
                <p class="text-sm text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-purple-50 rounded p-4">
                <p class="text-sm text-gray-600">Read</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['read'] ?? 0 }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Email</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Sent At</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900">Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $delivery->email }}</td>
                        <td class="py-3 px-4">
                            <span class="inline-block px-2 py-1 rounded text-sm font-semibold {{ $delivery->status === 'sent' ? 'bg-green-100 text-green-800' : ($delivery->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">{{ $delivery->sent_at ? $delivery->sent_at->format('M d, Y H:i') : 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm text-red-600">{{ $delivery->error_message ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">No delivery records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.broadcasts.index') }}" class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Back to Broadcasts</a>
    </div>
</div>
@endsection
