@extends('layouts.dashboard')

@section('title', 'Edit Shop')
@section('page-title', 'Shop Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('admin.shops.show', $shop) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Shop Details
    </a>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Shop: {{ $shop->shop_name }}</h1>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.shops.update', $shop) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shop Name -->
                <div>
                    <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-2">Shop Name *</label>
                    <input type="text" id="shop_name" name="shop_name" value="{{ old('shop_name', $shop->shop_name) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('shop_name') border-red-500 @enderror"
                        required>
                    @error('shop_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <input type="text" id="category" name="category" value="{{ old('category', $shop->category) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-500 @enderror"
                        required>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $shop->phone) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Town -->
                <div>
                    <label for="town" class="block text-sm font-medium text-gray-700 mb-2">Town *</label>
                    <input type="text" id="town" name="town" value="{{ old('town', $shop->town) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('town') border-red-500 @enderror"
                        required>
                    @error('town')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Postcode -->
                <div>
                    <label for="postcode" class="block text-sm font-medium text-gray-700 mb-2">Postcode *</label>
                    <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $shop->postcode) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postcode') border-red-500 @enderror"
                        required>
                    @error('postcode')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opening Hours -->
                <div>
                    <label for="opening_hours" class="block text-sm font-medium text-gray-700 mb-2">Opening Hours</label>
                    <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours', $shop->opening_hours) }}" 
                        placeholder="e.g., Mon-Fri 9am-5pm, Sat 10am-4pm"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('opening_hours') border-red-500 @enderror">
                    @error('opening_hours')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address (Full Width) -->
            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                    required>{{ old('address', $shop->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description (Full Width) -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Enter shop description..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $shop->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex gap-3">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
                <a href="{{ route('admin.shops.show', $shop) }}" class="inline-flex items-center px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
