@extends('layouts.dashboard')

@section('title', 'Shop Details')
@section('page-title', 'Shop Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('admin.shops.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Shops
    </a>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $shop->shop_name }}</h1>
                <p class="text-gray-600 mt-2">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($shop->is_verified) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                        {{ $shop->is_verified ? '✓ Verified' : '⚠ Unverified' }}
                    </span>
                    <span class="ml-2 px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($shop->user->is_active) bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                        {{ $shop->user->is_active ? '● Active' : '● Suspended' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Shop Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-600 text-sm">Email</p>
                        <p class="text-gray-900">{{ $shop->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Phone</p>
                        <p class="text-gray-900">{{ $shop->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Address</p>
                        <p class="text-gray-900">{{ $shop->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Town</p>
                        <p class="text-gray-900">{{ $shop->town ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Postcode</p>
                        <p class="text-gray-900">{{ $shop->postcode ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Category</p>
                        <p class="text-gray-900">{{ $shop->category ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Additional Details</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-600 text-sm">Joined Date</p>
                        <p class="text-gray-900">{{ $shop->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Last Updated</p>
                        <p class="text-gray-900">{{ $shop->updated_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($shop->opening_hours)
                        <div>
                            <p class="text-gray-600 text-sm">Opening Hours</p>
                            <p class="text-gray-900">{{ $shop->opening_hours }}</p>
                        </div>
                    @endif
                    @if($shop->description)
                        <div>
                            <p class="text-gray-600 text-sm">Description</p>
                            <p class="text-gray-900">{{ $shop->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.shops.edit', $shop) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-edit mr-2"></i> Edit Shop
                </a>

                @if($shop->user->is_active)
                    <form action="{{ route('admin.shops.suspend', $shop) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to suspend this shop?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-pause mr-2"></i> Suspend Shop
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.shops.reactivate', $shop) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reactivate this shop?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Reactivate Shop
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.shops.destroy', $shop) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this shop? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i> Delete Shop
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
