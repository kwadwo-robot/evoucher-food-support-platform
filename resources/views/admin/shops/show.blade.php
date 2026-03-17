@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.shops.index') }}" class="text-green-600 hover:text-green-700 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Shops
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $shop->name }}</h1>
            <p class="mt-2 text-gray-600">{{ $shop->email }}</p>
        </div>

        <!-- Shop Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Name</label>
                        <p class="text-gray-900">{{ $shop->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <p class="text-gray-900">{{ $shop->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Phone</label>
                        <p class="text-gray-900">{{ $shop->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Joined</label>
                        <p class="text-gray-900">{{ $shop->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Actions</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Approval Status</label>
                        <div class="mt-2">
                            @if($shop->is_approved)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Active Status</label>
                        <div class="mt-2">
                            @if($shop->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="space-y-2">
                            @if(!$shop->is_approved)
                                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                        Approve Shop
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.shops.toggle', $shop) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                                    {{ $shop->is_active ? 'Deactivate' : 'Activate' }} Shop
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shop Profile -->
        @if($shop->shopProfile)
            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shop Profile</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Address</label>
                        <p class="text-gray-900">{{ $shop->shopProfile->address ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Postcode</label>
                        <p class="text-gray-900">{{ $shop->shopProfile->postcode ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Bank Account</label>
                        <p class="text-gray-900">{{ $shop->shopProfile->bank_account_number ? '****' . substr($shop->shopProfile->bank_account_number, -4) : 'Not provided' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
