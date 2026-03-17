@extends('layouts.dashboard')
@section('title','Add Food Listing')
@section('page-title','Add Food Listing')
@section('content')
<div class="page-hd">
  <h1>Add New Food Listing</h1>
  <p>Choose the listing type and fill in the details below</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{
  listingType: '{{ old('listing_type', 'free') }}',
  get isDiscounted() { return this.listingType === 'discounted'; },
  get isSurplus()    { return this.listingType === 'surplus'; },
  get isFree()       { return this.listingType === 'free'; }
}">
  <div class="card lg:col-span-2">
    <div class="card-hd"><div class="card-title"><i class="fas fa-plus-circle text-green-600"></i> Listing Details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('shop.listings.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ── Listing Type Selector ──────────────────────────────────────── --}}
        <div class="mb-5">
          <label class="form-label">Listing Type *</label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-1">

            {{-- Free --}}
            <label :class="listingType==='free' ? 'ring-2 ring-green-500 bg-green-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="free" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">🎁</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Free</span>
                <span class="badge badge-green ml-auto" style="font-size:10px">Schools/Care & VCFSE</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Visible to Schools/Care and VCFSE groups only. Recipients cannot see this.</p>
            </label>

            {{-- Food to Go (Discounted) --}}
            <label :class="listingType==='discounted' ? 'ring-2 ring-orange-500 bg-orange-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="discounted" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">🏷️</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Food to Go</span>
                <span class="badge badge-orange ml-auto" style="font-size:10px">All Users</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Sell near-expiry food at a discount. Visible to Recipients, Schools/Care, and VCFSE groups.</p>
            </label>

            {{-- Free Surplus --}}
            <label :class="listingType==='surplus' ? 'ring-2 ring-purple-500 bg-purple-50' : 'bg-white hover:bg-slate-50'"
                   class="flex flex-col gap-1 p-4 rounded-xl border border-slate-200 cursor-pointer transition-all">
              <input type="radio" name="listing_type" value="surplus" x-model="listingType" class="sr-only">
              <div class="flex items-center gap-2">
                <span class="text-xl">📦</span>
                <span style="font-size:13px;font-weight:700;color:#0f172a">Free Surplus</span>
                <span class="badge badge-purple ml-auto" style="font-size:10px">Schools/Care & VCFSE</span>
              </div>
              <p style="font-size:11.5px;color:#64748b;line-height:1.5">Visible to Schools/Care and VCFSE groups only. Recipients cannot see this.</p>
            </label>

          </div>
        </div>

        {{-- ── Discounted Price Fields (shown only for Food to Go) ─────────── --}}
        <div x-show="isDiscounted" x-cloak class="mb-4 p-4 rounded-xl" style="background:#fff7ed;border:1px solid #fed7aa">
          <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-tag text-orange-500"></i>
            <span style="font-size:13px;font-weight:700;color:#c2410c">Food to Go Pricing</span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Original Price (£) *</label>
              <input type="number" name="original_price" value="{{ old('original_price') }}" min="0.01" step="0.01" placeholder="e.g. 3.50" class="form-input">
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">The normal retail price before discount</p>
            </div>
            <div>
              <label class="form-label">Discounted Price (£) *</label>
              <input type="number" name="discounted_price" value="{{ old('discounted_price') }}" min="0.01" step="0.01" placeholder="e.g. 1.00" class="form-input">
              <p style="font-size:11px;color:#94a3b8;margin-top:4px">What the recipient pays at your shop</p>
            </div>
          </div>
        </div>

        {{-- ── Voucher Value (hidden for surplus, shown for free/discounted) ── --}}
        <div x-show="!isSurplus" x-cloak class="mb-4">
          <label class="form-label">Voucher Redeem Value (£)</label>
          <input type="number" name="voucher_value" value="{{ old('voucher_value', 0) }}" min="0" step="0.01" placeholder="0.00 = free" class="form-input">
          <p style="font-size:11px;color:#94a3b8;margin-top:4px" x-show="isDiscounted">Amount deducted from the recipient's voucher. The rest they pay at your shop.</p>
          <p style="font-size:11px;color:#94a3b8;margin-top:4px" x-show="isFree">Set to £0.00 for completely free items.</p>
        </div>
        {{-- Hidden voucher_value for surplus --}}
        <input x-show="isSurplus" type="hidden" name="voucher_value" value="0">

        {{-- ── Core Fields ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Item Name *</label>
            <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="e.g. Wholemeal Bread" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Quantity *</label>
            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="form-input" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Description</label>
          <textarea name="description" rows="3" placeholder="Brief description of the item..." class="form-textarea">{{ old('description') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Expiry Date *</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="form-input" required>
          </div>
          <div>
            <label class="form-label">Food Image</label>
            <input type="file" name="image" accept="image/*" class="form-input" style="padding:6px">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Collection Address *</label>
          <input type="text" name="collection_address" value="{{ old('collection_address', $shopProfile?->address ?? '') }}" placeholder="Full address for collection" class="form-input" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="form-label">Collection Time</label>
            <input type="text" name="collection_time" value="{{ old('collection_time') }}" placeholder="e.g. Mon-Fri 9am-5pm" class="form-input">
          </div>
        </div>
        <div class="mb-5">
          <label class="form-label">Collection Instructions</label>
          <textarea name="collection_instructions" rows="2" placeholder="Any special instructions for collection..." class="form-textarea">{{ old('collection_instructions') }}</textarea>
        </div>
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Create Listing
          </button>
          <a href="{{ route('shop.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Tips Sidebar ──────────────────────────────────────────────────────── --}}
  <div>
    <div class="card mb-4">
      <div class="card-hd"><div class="card-title"><i class="fas fa-lightbulb text-yellow-500"></i> Listing Types</div></div>
      <div class="card-body" style="padding:16px">
        <div style="font-size:13px;color:#64748b;line-height:1.7">
          <div class="mb-3 p-3 rounded-lg" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div style="font-weight:700;color:#15803d;margin-bottom:2px">🎁 Free</div>
            <div style="font-size:12px">Visible to <strong>Schools/Care</strong> and <strong>VCFSE groups</strong> only. Recipients cannot see this.</div>
          </div>
          <div class="mb-3 p-3 rounded-lg" style="background:#fff7ed;border:1px solid #fed7aa">
            <div style="font-weight:700;color:#c2410c;margin-bottom:2px">🏷️ Food to Go</div>
            <div style="font-size:12px">Visible to <strong>Recipients</strong>, <strong>Schools/Care</strong>, and <strong>VCFSE groups</strong>. They pay the discounted price at your shop (voucher covers part or all).</div>
          </div>
          <div class="p-3 rounded-lg" style="background:#faf5ff;border:1px solid #e9d5ff">
            <div style="font-weight:700;color:#7e22ce;margin-bottom:2px">📦 Free Surplus</div>
            <div style="font-size:12px">Visible to <strong>Schools/Care</strong> and <strong>VCFSE groups</strong> only. Recipients cannot see this.</div>
          </div>
        </div>
      </div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-color:#bbf7d0">
      <div class="card-body" style="padding:16px;text-align:center">
        <div style="font-size:28px;margin-bottom:8px">🌱</div>
        <div style="font-size:13px;font-weight:700;color:#15803d;margin-bottom:4px">Reduce Food Waste</div>
        <div style="font-size:12px;color:#16a34a">Every listing helps a family in Northamptonshire and reduces food waste in our community.</div>
      </div>
    </div>
  </div>
</div>
@endsection
