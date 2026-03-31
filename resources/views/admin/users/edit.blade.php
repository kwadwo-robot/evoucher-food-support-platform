@extends('layouts.dashboard')
@section('title', 'Edit User')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- User Information -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">User Information</h2>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('phone') border-red-500 @enderror">
                    @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Recipient Profile -->
            @if($user->recipientProfile)
            <div class="border-t border-gray-100 pt-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recipient Profile</h2>
                
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->recipientProfile->address) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('address') border-red-500 @enderror">
                    @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="postcode" class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                    <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $user->recipientProfile->postcode) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('postcode') border-red-500 @enderror">
                    @error('postcode')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.show', $user) }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-center">
                    Cancel
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
