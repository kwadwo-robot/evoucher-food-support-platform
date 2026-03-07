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
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Item</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Shop</th>
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
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No listings found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($listings->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">{{ $listings->links() }}</div>
    @endif
</div>
@endsection
