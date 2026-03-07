@extends('layouts.dashboard')
@section('title', $listing->item_name)
@section('page-title', $listing->item_name)
@section('content')
<div class="mb-4">
  <a href="{{ route('recipient.food.browse') }}" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Browse
  </a>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Main Details -->
  <div class="lg:col-span-2">
    <div class="card mb-5">
      @if($listing->image_url)
      <img src="{{ $listing->image_url }}" style="width:100%;height:260px;object-fit:cover" alt="{{ $listing->item_name }}">
      @else
      <div style="width:100%;height:200px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:64px">🥖</div>
      @endif
      <div class="card-body">
        <div class="flex items-start justify-between mb-3">
          <h1 style="font-size:22px;font-weight:800;color:#0f172a">{{ $listing->item_name }}</h1>
          <div style="display:flex;gap:8px">
            <span class="badge badge-green" style="font-size:12px;padding:5px 12px">{{ $listing->quantity }} available</span>
            @if($listing->listing_type === 'discounted' && $listing->original_price > 0)
            <span class="badge badge-orange" style="font-size:12px;padding:5px 12px"><i class="fas fa-tag mr-1"></i>{{ round((1 - $listing->discounted_price / $listing->original_price) * 100) }}% off</span>
            @endif
          </div>
        </div>
        @if($listing->description)
        <p style="font-size:14px;color:#64748b;line-height:1.7;margin-bottom:16px">{{ $listing->description }}</p>
        @endif
        <div class="grid grid-cols-2 gap-4">
          <div style="padding:12px;background:#f8fafc;border-radius:10px">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Expiry Date</div>
            <div style="font-size:14px;font-weight:700;color:#ef4444"><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($listing->expiry_date)->format('d M Y') }}</div>
          </div>
          <div style="padding:12px;background:#f8fafc;border-radius:10px">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Shop</div>
            <div style="font-size:14px;font-weight:700;color:#0f172a">{{ $listing->shopUser->name ?? 'Local Shop' }}</div>
          </div>
          <div style="padding:12px;background:#f8fafc;border-radius:10px">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Collection Address</div>
            <div style="font-size:13px;font-weight:600;color:#334155">{{ $listing->collection_address }}</div>
          </div>
          <div style="padding:12px;background:#f8fafc;border-radius:10px">
            <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Collection Time</div>
            <div style="font-size:13px;font-weight:600;color:#334155">{{ $listing->collection_time ?? 'Contact shop' }}</div>
          </div>
        </div>
        @if($listing->collection_instructions)
        <div style="margin-top:16px;padding:14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px">
          <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:4px"><i class="fas fa-info-circle mr-1"></i> Collection Instructions</div>
          <div style="font-size:13px;color:#78350f">{{ $listing->collection_instructions }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
  <!-- Redemption Panel -->
  <div>
    @if($user_vouchers && count($user_vouchers) > 0)
    <div class="card" style="border:2px solid #16a34a">
      <div class="card-hd" style="background:#f0fdf4">
        <div class="card-title" style="color:#15803d"><i class="fas fa-ticket"></i> Redeem with Voucher</div>
      </div>
      <div class="card-body" x-data="voucherPicker({{ $listing->voucher_value ?? 0 }})">
        <form method="POST" action="{{ route('recipient.food.redeem', $listing->id) }}">
          @csrf
          <div class="mb-4">
            <label class="form-label">Select Your Voucher</label>
            @foreach($user_vouchers as $voucher)
            <label style="display:flex;align-items:center;gap:12px;padding:12px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;margin-bottom:8px;transition:border .15s"
              :style="selectedId == {{ $voucher->id }} ? 'border-color:#16a34a;background:#f0fdf4' : ''">
              <input type="radio" name="voucher_id" value="{{ $voucher->id }}"
                x-on:change="selectVoucher({{ $voucher->id }}, {{ $voucher->remaining_value ?? $voucher->amount }})"
                required style="accent-color:#16a34a">
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:8px">
                  <code style="font-size:13px;font-weight:800;color:#16a34a;letter-spacing:.08em">{{ $voucher->code }}</code>
                  @if($voucher->status === 'partially_used')
                  <span class="badge badge-yellow" style="font-size:10px">Partial</span>
                  @endif
                </div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Balance: <strong style="color:#0f172a">£{{ number_format($voucher->remaining_value ?? $voucher->amount, 2) }}</strong></div>
              </div>
            </label>
            @endforeach
          </div>

          {{-- Dynamic cost breakdown --}}
          @if($listing->voucher_value > 0)
          <div x-show="selectedId !== null" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-bottom:16px">
            <div style="font-size:12px;font-weight:700;color:#15803d;margin-bottom:10px"><i class="fas fa-calculator mr-1"></i> Payment Breakdown</div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
              <span style="font-size:13px;color:#64748b">Item cost</span>
              <span style="font-size:13px;font-weight:700;color:#0f172a">£{{ number_format($listing->voucher_value, 2) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
              <span style="font-size:13px;color:#64748b">From your voucher</span>
              <span style="font-size:13px;font-weight:700;color:#16a34a">− £<span x-text="voucherPays">0.00</span></span>
            </div>
            <div style="height:1px;background:#bbf7d0;margin:8px 0"></div>
            <div style="display:flex;justify-content:space-between">
              <span style="font-size:13px;font-weight:700;color:#0f172a">You pay at shop</span>
              <span style="font-size:14px;font-weight:800;" :style="owedAtShop > 0 ? 'color:#dc2626' : 'color:#16a34a'">£<span x-text="owedAtShop">0.00</span></span>
            </div>
            <div x-show="owedAtShop > 0" style="margin-top:10px;padding:8px 12px;background:#fef9c3;border-radius:8px;font-size:12px;color:#92400e">
              <i class="fas fa-info-circle mr-1"></i> Your voucher balance is less than the item cost. You will need to pay the outstanding amount at the shop.
            </div>
          </div>
          <div x-show="selectedId === null" style="padding:10px 14px;background:#f0fdf4;border-radius:8px;margin-bottom:16px;font-size:13px;color:#15803d;font-weight:600">
            <i class="fas fa-tag mr-1"></i> This item costs £{{ number_format($listing->voucher_value, 2) }} from your voucher
          </div>
          @else
          <div style="padding:10px 14px;background:#f0fdf4;border-radius:8px;margin-bottom:16px;font-size:13px;color:#15803d;font-weight:600">
            <i class="fas fa-gift mr-1"></i> This item is free — no voucher value deducted
          </div>
          @endif

          <button type="submit" class="btn btn-primary w-full" style="justify-content:center;padding:12px">
            <i class="fas fa-check-circle"></i> Confirm Redemption
          </button>
        </form>
      </div>

<script>
function voucherPicker(itemCost) {
  return {
    selectedId: null,
    selectedBalance: 0,
    itemCost: parseFloat(itemCost) || 0,
    get voucherPays() {
      return Math.min(this.selectedBalance, this.itemCost).toFixed(2);
    },
    get owedAtShop() {
      return Math.max(0, this.itemCost - this.selectedBalance).toFixed(2);
    },
    selectVoucher(id, balance) {
      this.selectedId = id;
      this.selectedBalance = parseFloat(balance) || 0;
    }
  };
}
</script>
    </div>
    @else
    <div class="card" style="border:2px dashed #e2e8f0">
      <div class="card-body text-center" style="padding:32px">
        <div style="font-size:36px;margin-bottom:12px">🎫</div>
        <div style="font-size:15px;font-weight:700;color:#334155;margin-bottom:6px">No Active Voucher</div>
        <div style="font-size:13px;color:#94a3b8;margin-bottom:16px">You need an active voucher to redeem this item</div>
        <a href="{{ route('recipient.vouchers') }}" class="btn btn-secondary">View My Vouchers</a>
      </div>
    </div>
    @endif
    <!-- Item Info Card -->
    <div class="card mt-4">
      <div class="card-body" style="padding:16px">
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Item Summary</div>
        <div class="flex justify-between mb-2">
          <span style="font-size:13px;color:#64748b">Quantity</span>
          <span style="font-size:13px;font-weight:600;color:#0f172a">{{ $listing->quantity }}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span style="font-size:13px;color:#64748b">Voucher Cost</span>
          <span style="font-size:13px;font-weight:700;color:#16a34a">{{ $listing->voucher_value > 0 ? '£'.number_format($listing->voucher_value,2) : 'Free' }}</span>
        </div>
        @if($listing->listing_type === 'discounted')
        <div class="flex justify-between mb-2">
          <span style="font-size:13px;color:#64748b">Original Price</span>
          <span style="font-size:13px;font-weight:600;color:#94a3b8"><del>£{{ number_format($listing->original_price,2) }}</del></span>
        </div>
        @endif
        <div class="flex justify-between">
          <span style="font-size:13px;color:#64748b">Status</span>
          <span class="badge badge-green">{{ ucfirst($listing->status) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
