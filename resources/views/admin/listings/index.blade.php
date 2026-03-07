@extends('layouts.dashboard')
@section('page-title', 'Food Listings')
@section('title', 'Food Listings')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Food Listings</h1>
        <p class="text-gray-500 text-sm mt-1">All listings from local shops</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('admin.listings.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Search -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-2">Search</label>
            <input type="text" name="search" placeholder="Item name..." value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        
        <!-- Shop Filter -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-2">Shop</label>
            <select name="shop_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">All Shops</option>
                @foreach($shops as $shop)
                <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Listing Type Filter -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-2">Type</label>
            <select name="listing_type" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="all" {{ request('listing_type') == 'all' ? 'selected' : '' }}>All Types</option>
                <option value="free" {{ request('listing_type') == 'free' ? 'selected' : '' }}>Free</option>
                <option value="discounted" {{ request('listing_type') == 'discounted' ? 'selected' : '' }}>Discounted</option>
                <option value="surplus" {{ request('listing_type') == 'surplus' ? 'selected' : '' }}>Surplus</option>
            </select>
        </div>
        
        <!-- Status Filter -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Removed</option>
            </select>
        </div>
        
        <!-- Sort -->
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-2">Sort</label>
            <select name="sort" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="expiring" {{ request('sort') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
            </select>
        </div>
        
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">Filter</button>
            <a href="{{ route('admin.listings.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 text-center">Reset</a>
        </div>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Item</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Shop</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Value</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Expiry</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($listings as $listing)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($listing->image_url)
                            <img src="{{ $listing->image_url }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                            @else
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-apple-alt text-green-400 text-sm"></i>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $listing->item_name }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ $listing->quantity }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $listing->shopProfile->shop_name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        @if($listing->listing_type === 'discounted')
                        <span class="badge badge-orange" style="font-size:10px"><i class="fas fa-tag mr-1"></i>Discounted</span>
                        @elseif($listing->listing_type === 'surplus')
                        <span class="badge badge-purple" style="font-size:10px"><i class="fas fa-boxes-stacked mr-1"></i>Surplus</span>
                        @else
                        <span class="badge badge-green" style="font-size:10px"><i class="fas fa-gift mr-1"></i>Free</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold text-green-600">£{{ number_format($listing->voucher_value, 2) }}</td>
                    <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $listing->expiry_date->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $listing->status === 'available' ? 'badge-active' : ($listing->status === 'reserved' ? 'badge-pending' : 'badge-redeemed') }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded" onclick="return confirm('Remove this listing?')">Remove</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No listings found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($listings->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $listings->links() }}</div>
    @endif
</div>
@endsection
