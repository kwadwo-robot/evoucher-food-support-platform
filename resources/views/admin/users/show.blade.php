@extends('layouts.dashboard')
@section('title', 'User Details')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                <span class="text-green-700 font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-500">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 text-xs rounded-full capitalize
                        {{ $user->role === 'recipient' ? 'bg-green-100 text-green-700' :
                           ($user->role === 'local_shop' ? 'bg-blue-100 text-blue-700' :
                           (in_array($user->role, ['admin','super_admin']) ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700')) }}">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                    <span class="{{ $user->is_active ? 'badge-active' : 'badge-expired' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500 text-xs">Joined</p>
                <p class="font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Approved</p>
                <p class="font-medium text-gray-900">{{ $user->is_approved ? 'Yes' : 'No' }}</p>
            </div>
        </div>

        @if($user->recipientProfile)
        <div class="border-t border-gray-100 mt-4 pt-4">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Recipient Profile</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Full Name</p><p class="font-medium">{{ $user->recipientProfile->full_name }}</p></div>
                <div><p class="text-xs text-gray-500">Phone</p><p class="font-medium">{{ $user->recipientProfile->phone ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Postcode</p><p class="font-medium">{{ $user->recipientProfile->postcode ?? 'N/A' }}</p></div>
            </div>
        </div>
        @endif

        @if($user->shopProfile)
        <div class="border-t border-gray-100 mt-4 pt-4">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Shop Profile</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Shop Name</p><p class="font-medium">{{ $user->shopProfile->shop_name }}</p></div>
                <div><p class="text-xs text-gray-500">Phone</p><p class="font-medium">{{ $user->shopProfile->phone ?? 'N/A' }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-500">Address</p><p class="font-medium">{{ $user->shopProfile->address ?? 'N/A' }}</p></div>
            </div>
        </div>
        @endif

        @if($user->organisationProfile)
        <div class="border-t border-gray-100 mt-4 pt-4">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Organisation Profile</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Org Name</p><p class="font-medium">{{ $user->organisationProfile->org_name }}</p></div>
                <div><p class="text-xs text-gray-500">Type</p><p class="font-medium capitalize">{{ $user->organisationProfile->org_type }}</p></div>
                <div><p class="text-xs text-gray-500">Charity No</p><p class="font-medium">{{ $user->organisationProfile->charity_number ?? 'N/A' }}</p></div>
            </div>
        </div>
        @endif
    </div>

    <div class="flex gap-3">
        @if(!$user->is_approved && !$user->isAdmin() && $user->role !== 'recipient')
        <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="flex-1">
            @csrf @method('PATCH')
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Approve Account
            </button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="flex-1">
            @csrf @method('PATCH')
            <button type="submit" class="w-full {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} font-semibold py-2.5 rounded-lg transition-colors">
                {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
            </button>
        </form>
    </div>
</div>
@endsection
