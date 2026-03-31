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
                <div class="col-span-2"><p class="text-xs text-gray-500">Address</p><p class="font-medium">{{ $user->recipientProfile->address ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Postcode</p><p class="font-medium">{{ $user->recipientProfile->postcode ?? 'N/A' }}</p></div>
            </div>
        </div>
        @endif

        @if($user->shopProfile)
        <div class="border-t border-gray-100 mt-4 pt-4">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">Local Food Shop Profile</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Shop Name</p><p class="font-medium">{{ $user->shopProfile->shop_name }}</p></div>
                <div><p class="text-xs text-gray-500">Category</p><p class="font-medium capitalize">{{ $user->shopProfile->category ? str_replace('_', ' ', $user->shopProfile->category) : 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Phone</p><p class="font-medium">{{ $user->shopProfile->phone ?? 'N/A' }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-500">Address</p><p class="font-medium">{{ $user->shopProfile->address ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Town / City</p><p class="font-medium">{{ $user->shopProfile->town ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Postcode</p><p class="font-medium">{{ $user->shopProfile->postcode ?? 'N/A' }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-500">Opening Hours</p><p class="font-medium">{{ $user->shopProfile->opening_hours ?? 'N/A' }}</p></div>
                <div class="col-span-2"><p class="text-xs text-gray-500">Description</p><p class="font-medium">{{ $user->shopProfile->description ?? 'N/A' }}</p></div>
            </div>
        </div>
        @endif

        @if($user->organisationProfile)
        <div class="border-t border-gray-100 mt-4 pt-4">
            <h3 class="font-semibold text-gray-700 mb-3 text-sm">
                @if($user->role === 'school_care') School / Care Organisation Profile
                @else VCFSE Organisation Profile
                @endif
            </h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Organisation Name</p><p class="font-medium">{{ $user->organisationProfile->org_name }}</p></div>
                <div><p class="text-xs text-gray-500">Type</p><p class="font-medium capitalize">{{ str_replace('_', ' ', $user->organisationProfile->org_type) }}</p></div>
                <div><p class="text-xs text-gray-500">Contact Person</p><p class="font-medium">{{ $user->organisationProfile->contact_person ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Phone</p><p class="font-medium">{{ $user->organisationProfile->phone ?? 'N/A' }}</p></div>
                @if($user->role === 'vcfse')
                <div><p class="text-xs text-gray-500">Charity / Reg Number</p><p class="font-medium">{{ $user->organisationProfile->charity_number ?? 'N/A' }}</p></div>
                @else
                <div><p class="text-xs text-gray-500">Reg / Ofsted Number</p><p class="font-medium">{{ $user->organisationProfile->charity_number ?? 'N/A' }}</p></div>
                @endif
                <div class="col-span-2"><p class="text-xs text-gray-500">Address</p><p class="font-medium">{{ $user->organisationProfile->address ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Postcode</p><p class="font-medium">{{ $user->organisationProfile->postcode ?? 'N/A' }}</p></div>
                <div><p class="text-xs text-gray-500">Website</p>
                    @if($user->organisationProfile->website)
                        <a href="{{ $user->organisationProfile->website }}" target="_blank" class="font-medium text-green-600 hover:underline">{{ $user->organisationProfile->website }}</a>
                    @else
                        <p class="font-medium">N/A</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="flex gap-3 mb-4">
        <a href="{{ route('admin.users.edit', $user) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-center">
            <i class="fas fa-edit mr-2"></i>Edit User
        </a>
        <button type="button" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 rounded-lg transition-colors" onclick="document.getElementById('resetPasswordModal').classList.remove('hidden')">
            <i class="fas fa-key mr-2"></i>Reset Password
        </button>
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

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Reset Password</h2>
            <p class="text-gray-600 mb-4">Enter a new password for {{ $user->name }}</p>
            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
