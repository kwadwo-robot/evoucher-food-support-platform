@extends('layouts.dashboard')

@section('title', 'Local Shops')
@section('page-title', 'Shop Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Local Shops</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($shops->isEmpty())
        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded">
            <p>No shops found.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shop Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Town</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shops as $shop)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $shop->shop_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shop->user->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shop->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $shop->town ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($shop->is_verified) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                                    @if($shop->is_verified)
                                        <i class="fas fa-check mr-1"></i> Verified
                                    @else
                                        <i class="fas fa-clock mr-1"></i> Unverified
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($shop->user->is_active) bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                                    @if($shop->user->is_active)
                                        <i class="fas fa-circle mr-1"></i> Active
                                    @else
                                        <i class="fas fa-ban mr-1"></i> Suspended
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.shops.show', $shop) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                <a href="{{ route('admin.shops.edit', $shop) }}" class="text-green-600 hover:text-green-900 inline-flex items-center">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $shops->links() }}
        </div>
    @endif
</div>
@endsection
