@extends('layouts.dashboard')
@section('page-title', 'My Cart')
@section('title', 'My Cart')
@section('content')

<div class="page-hd">
    <h1>My Cart</h1>
    <p>Review your selected food items and redeem them with your voucher</p>
</div>

@if(session('success'))
<div class="alert alert-success mb-4"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
@endif
@if(session('info'))
<div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 mb-4"><i class="fas fa-info-circle mr-2"></i>{{ session('info') }}</div>
@endif
@if(isset($removed) && $removed > 0)
<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 mb-4">
    <i class="fas fa-exclamation-triangle mr-2"></i>{{ $removed }} item(s) were removed from your cart because they are no longer available.
</div>
@endif

@if($listings->isEmpty())
<div class="card">
    <div class="card-body text-center" style="padding:48px">
        <div style="font-size:48px;margin-bottom:16px">🛒</div>
        <div style="font-size:18px;font-weight:700;color:#334155;margin-bottom:8px">Your cart is empty</div>
        <div style="font-size:14px;color:#94a3b8;margin-bottom:24px">Browse available food items and add them to your cart</div>
        <a href="{{ route('food.index') }}" class="btn btn-primary">
            <i class="fas fa-search mr-2"></i> Browse Food
        </a>
    </div>
</div>
@else
<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">

    <!-- Cart Items -->
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <div style="font-size:15px;font-weight:700;color:#0f172a">{{ $listings->count() }} item(s) in your cart</div>
            <form method="POST" action="{{ route('recipient.cart.clear') }}" onsubmit="return confirm('Clear all items from your cart?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="font-size:13px;color:#dc2626;background:none;border:none;cursor:pointer;font-weight:600">
                    <i class="fas fa-trash mr-1"></i> Clear Cart
                </button>
            </form>
        </div>

        @foreach($listings as $listing)
        <div class="card mb-3" style="border:1px solid #e2e8f0">
            <div class="card-body" style="padding:16px;display:flex;gap:16px;align-items:flex-start">
                @if($listing->image_url)
                <img src="{{ $listing->image_url }}" alt="{{ $listing->item_name }}"
                    style="width:80px;height:80px;object-fit:cover;border-radius:8px;flex-shrink:0">
                @else
                <div style="width:80px;height:80px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:28px;flex-shrink:0">🍎</div>
                @endif

                <div style="flex:1;min-width:0">
                    <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px">{{ $listing->item_name }}</div>
                    <div style="font-size:13px;color:#64748b;margin-bottom:6px">
                        <i class="fas fa-store mr-1"></i>
                        {{ $listing->shop->shopProfile->shop_name ?? $listing->shop->name ?? 'Unknown Shop' }}
                        @if($listing->shop->shopProfile->town ?? null)
                        &nbsp;·&nbsp;<i class="fas fa-map-marker-alt mr-1"></i>{{ $listing->shop->shopProfile->town }}
                        @endif
                    </div>
                    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                        <span style="font-size:16px;font-weight:800;color:#c2410c">
                            £{{ number_format($listing->discounted_price ?? $listing->voucher_value, 2) }}
                        </span>
                        @if($listing->original_price)
                        <span style="font-size:12px;color:#94a3b8;text-decoration:line-through">
                            £{{ number_format($listing->original_price, 2) }}
                        </span>
                        @endif
                        <span class="badge badge-orange"><i class="fas fa-tag mr-1"></i>Discounted</span>
                        <span style="font-size:12px;color:#64748b">
                            <i class="fas fa-clock mr-1"></i>Expires {{ \Carbon\Carbon::parse($listing->expiry_date)->format('d M Y') }}
                        </span>
                    </div>
                    @if($listing->voucher_value > 0)
                    <div style="font-size:12px;color:#16a34a;margin-top:4px;font-weight:600">
                        <i class="fas fa-ticket mr-1"></i>Voucher covers: £{{ number_format($listing->voucher_value, 2) }}
                    </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('recipient.cart.remove', $listing) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Remove from cart"
                        style="background:#fee2e2;border:none;border-radius:6px;padding:6px 10px;cursor:pointer;color:#dc2626;font-size:13px">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        <a href="{{ route('food.index') }}" style="font-size:13px;color:#16a34a;font-weight:600;text-decoration:none">
            <i class="fas fa-plus mr-1"></i> Add more items
        </a>
    </div>

    <!-- Order Summary & Checkout -->
    <div>
        <div class="card" style="border:1px solid #e2e8f0;position:sticky;top:20px">
            <div class="card-body" style="padding:20px">
                <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:16px">Order Summary</div>

                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:13px;color:#64748b">Items ({{ $listings->count() }})</span>
                    <span style="font-size:13px;font-weight:600;color:#0f172a">£{{ number_format($totalVoucherCost, 2) }}</span>
                </div>
                <div style="height:1px;background:#e2e8f0;margin:12px 0"></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:16px">
                    <span style="font-size:14px;font-weight:700;color:#0f172a">Total Voucher Cost</span>
                    <span style="font-size:16px;font-weight:800;color:#c2410c">£{{ number_format($totalVoucherCost, 2) }}</span>
                </div>

                @if($user_vouchers->isEmpty())
                <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:#92400e">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    You have no active vouchers. Contact your support worker or admin to receive a voucher.
                </div>
                <a href="{{ route('recipient.vouchers.index') }}" class="btn btn-secondary w-full" style="text-align:center;justify-content:center">
                    View My Vouchers
                </a>
                @else
                <form method="POST" action="{{ route('recipient.cart.checkout') }}">
                    @csrf
                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;text-transform:uppercase">Select Voucher</label>
                        @foreach($user_vouchers as $voucher)
                        <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;margin-bottom:8px;transition:border-color 0.2s"
                            onclick="this.style.borderColor='#16a34a'">
                            <input type="radio" name="voucher_id" value="{{ $voucher->id }}" required
                                style="margin-top:2px;accent-color:#16a34a">
                            <div style="flex:1">
                                <div style="font-size:13px;font-weight:700;color:#0f172a;font-family:monospace">{{ $voucher->code }}</div>
                                <div style="font-size:12px;color:#16a34a;font-weight:600">Balance: £{{ number_format($voucher->remaining_value, 2) }}</div>
                                <div style="font-size:11px;color:#94a3b8">Expires {{ \Carbon\Carbon::parse($voucher->expiry_date)->format('d M Y') }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    @if($totalVoucherBalance < $totalVoucherCost)
                    <div style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;margin-bottom:14px;font-size:12px;color:#92400e">
                        <i class="fas fa-info-circle mr-1"></i>
                        Your voucher balance (£{{ number_format($totalVoucherBalance, 2) }}) is less than the total cost.
                        You may need to pay the difference at the shop(s).
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-full" style="justify-content:center;padding:12px;font-size:15px"
                        onclick="return confirm('Confirm redemption of {{ $listings->count() }} item(s)?')">
                        <i class="fas fa-check-circle mr-2"></i> Confirm & Redeem All
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@endsection
