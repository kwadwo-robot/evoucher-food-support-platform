@extends('layouts.dashboard')
@section('title','Edit Listing')
@section('page-title','Edit Listing')
@section('content')
<div class="page-hd">
  <h1>Edit Food Listing</h1>
  <p>Update the details for "{{ $listing->item_name }}"</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{
  listingType: '{{ old('listing_type', $listing->listing_type ?? 'free') }}',
  get isDiscounted() { return this.listingType === 'discounted'; },
  get isSurplus()    { return this.listingType === 'surplus'; },
  get isFree()       { return this.listingType === 'free'; }
}">
  <div class="card lg:col-span-2">
    <div class="card-hd"><div class="card-title"><i class="fas fa-edit text-green-600"></i> Edit Details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('shop.listings.update', $listing->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- ── Listing Type Selector ──────────────────────────────────────── --}}
        <div class="mb-5">
          <label class="form-label">Listing Type *</label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-1">
            <label :class="listingType==='free' ? 'ring-2 ring-green-500 bg-green-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="free" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">🎁</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Free</span>
                <span class="badge badge-green ml-auto" style="font-size:10px">Free</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Recipients and VCFSE groups can redeem for free.</p>
            </label>
            <label :class="listingType==='discounted' ? 'ring-2 ring-orange-500 bg-orange-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="discounted" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">🏷️</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Food to Go</span>
                <span class="badge badge-orange ml-auto" style="font-size:10px">Discounted</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Sell at a discount. Recipients only.</p>
            </label>
            <label :class="listingType==='surplus' ? 'ring-2 ring-purple-500 bg-purple-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="surplus" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">📦</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Free Surplus</span>
                <span class="badge badge-purple ml-auto" style="font-size:10px">VCFSE Only</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Bulk surplus for VCFSE collection only.</p>
            </label>
          </div>
        </div>

        {{-- ── Discounted Price Fields ─────────────────────────────────────── --}}
        <div x-show="isDiscounted" x-cloak class="mb-4 p-4 rounded-xl" style="background:#fff7ed;border:1px solid #fed7aa">
          <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-tag text-orange-500"></i>
            <span style="font-size:13px;font-weight:700;color:#c2410c">Food to Go Pricing</span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Original Price (£) *</label>
              <input type="number" name="original_price" value="{{ old('original_price', $listing->original_price) }}" min="0.01" step="0.01" placeholder="e.g. 3.50" class="form-input">
            </div>
            <div>
              <label class="form-label">Discounted Price (£) *</label>
              <input type="number" name="discounted_price" value="{{ old('discounted_price', $listing->discounted_price) }}" min="0.01" step="0.01" placeholder="e.g. 1.00" class="form-input">
            </div>
          </div>
        </div>

        {{-- ── Voucher Value ───────────────────────────────────────────────── --}}
        <div x-show="!isSurplus" x-cloak class="mb-4">
          <label class="form-label">Voucher Redemption Value (£)</label>
          <input type="number" name="voucher_value" value="{{ old('voucher_value', $listing->voucher_value) }}" min="0" step="0.01" class="form-input">
        </div>
        <input x-show="isSurplus" type="hidden" name="voucher_value" value="0">

        {{-- ── Core Fields ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Item Name *</label>
            <input type="text" name="item_name" value="{{ old('item_name', $listing->item_name) }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Quantity *</label>
            <input type="number" name="quantity" value="{{ old('quantity', $listing->quantity) }}" min="1" class="form-input" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Description</label>
          <textarea name="description" rows="3" class="form-textarea">{{ old('description', $listing->description) }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Expiry Date *</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', \Carbon\Carbon::parse($listing->expiry_date)->format('Y-m-d')) }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="available" {{ $listing->status === 'available' ? 'selected' : '' }}>Available</option>
              <option value="reserved"  {{ $listing->status === 'reserved'  ? 'selected' : '' }}>Reserved</option>
              <option value="collected" {{ $listing->status === 'collected' ? 'selected' : '' }}>Collected</option>
              <option value="expired"   {{ $listing->status === 'expired'   ? 'selected' : '' }}>Expired</option>
            </select>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Collection Address *</label>
          <input type="text" name="collection_address" value="{{ old('collection_address', $listing->collection_address) }}" class="form-input" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Collection Time</label>
            <input type="text" name="collection_time" value="{{ old('collection_time', $listing->collection_time) }}" class="form-input">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Collection Instructions</label>
          <textarea name="collection_instructions" rows="2" class="form-textarea">{{ old('collection_instructions', $listing->collection_instructions) }}</textarea>
        </div>
        <div class="mb-5">
          <label class="form-label">Update Image (optional)</label>
          @if($listing->image_url)
          <div class="mb-2"><img src="{{ $listing->image_url }}" style="height:80px;border-radius:8px;object-fit:cover"></div>
          @endif
          <input type="file" name="image" accept="image/*" class="form-input" style="padding:6px">
        </div>
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
          <a href="{{ route('shop.listings.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Listing Type Guide ──────────────────────────────────────────────── --}}
  <div>
    <div class="card">
      <div class="card-hd"><div class="card-title"><i class="fas fa-info-circle text-blue-500"></i> Listing Types</div></div>
      <div class="card-body" style="padding:16px">
        <div style="font-size:13px;color:#64748b;line-height:1.7">
          <div class="mb-3 p-3 rounded-lg" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div style="font-weight:700;color:#15803d;margin-bottom:2px">🎁 Free</div>
            <div style="font-size:12px">Visible to <strong>Recipients</strong> and <strong>VCFSE</strong>.</div>
          </div>
          <div class="mb-3 p-3 rounded-lg" style="background:#fff7ed;border:1px solid #fed7aa">
            <div style="font-weight:700;color:#c2410c;margin-bottom:2px">🏷️ Food to Go</div>
            <div style="font-size:12px">Visible to <strong>Recipients only</strong>. Discounted price.</div>
          </div>
          <div class="p-3 rounded-lg" style="background:#faf5ff;border:1px solid #e9d5ff">
            <div style="font-weight:700;color:#7e22ce;margin-bottom:2px">📦 Free Surplus</div>
            <div style="font-size:12px">Visible to <strong>VCFSE only</strong>. Bulk collection.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
