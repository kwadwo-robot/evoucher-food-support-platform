@extends('layouts.dashboard')
@section('title', 'Voucher Details - ' . $voucher->code)
@section('page-title', 'Voucher Details')
@section('content')

<div class="page-content">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
      <div class="card mb-6">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-ticket text-blue-600"></i> Voucher Information</div>
          <span class="badge @if($voucher->status === 'active' && $voucher->expiry_date >= now()) badge-green @elseif($voucher->status === 'cancelled') badge-gray @else badge-red @endif">
            {{ $voucher->status === 'active' && $voucher->expiry_date >= now() ? 'Active' : ($voucher->status === 'cancelled' ? 'Revokeled' : 'Expired') }}
          </span>
        </div>
        <div class="card-body">
          <div class="grid grid-cols-2 gap-6">
            <!-- Voucher Code -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Voucher Code</div>
              <div class="font-mono text-lg font-bold text-blue-600 bg-blue-50 p-3 rounded-lg">{{ $voucher->code }}</div>
            </div>

            <!-- Status -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</div>
              <div class="text-lg font-semibold">
                @if($voucher->status === 'active')
                  @if($voucher->expiry_date >= now())
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Active</span>
                  @else
                    <span class="text-red-600"><i class="fas fa-times-circle"></i> Expired</span>
                  @endif
                @elseif($voucher->status === 'cancelled')
                  <span class="text-gray-600"><i class="fas fa-ban"></i> Revokeled</span>
                @else
                  <span class="text-blue-600"><i class="fas fa-info-circle"></i> {{ ucfirst($voucher->status) }}</span>
                @endif
              </div>
            </div>

            <!-- Original Value -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Original Value</div>
              <div class="text-2xl font-bold text-gray-900">£{{ number_format($voucher->value, 2) }}</div>
            </div>

            <!-- Remaining Value -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Remaining Value</div>
              <div class="text-2xl font-bold text-green-600">£{{ number_format($voucher->remaining_value, 2) }}</div>
            </div>

            <!-- Issued Date -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Issued Date</div>
              <div class="text-lg font-semibold">{{ $voucher->created_at->format('d M Y H:i') }}</div>
            </div>

            <!-- Expiry Date -->
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Expiry Date</div>
              <div class="text-lg font-semibold @if($voucher->expiry_date >= now()) text-green-600 @else text-red-600 @endif">
                {{ $voucher->expiry_date->format('d M Y') }}
              </div>
            </div>
          </div>

          @if($voucher->notes)
          <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Notes</div>
            <p class="text-gray-700">{{ $voucher->notes }}</p>
          </div>
          @endif
        </div>
      </div>

      <!-- Recipient Information -->
      <div class="card mb-6">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-user text-purple-600"></i> Recipient Information</div>
        </div>
        <div class="card-body">
          <div class="flex items-center gap-4 mb-4">
            <div style="width:48px;height:48px;background:#fdf4ff;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#a855f7;font-size:20px;flex-shrink:0;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <div class="font-semibold text-gray-900">{{ $voucher->recipient->name }}</div>
              <div class="text-sm text-gray-500">{{ $voucher->recipient->email }}</div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Role</div>
              <div class="text-sm font-semibold">{{ ucfirst($voucher->recipient->role) }}</div>
            </div>
            <div>
              <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Status</div>
              <div class="text-sm font-semibold">
                @if($voucher->recipient->is_active)
                  <span class="text-green-600"><i class="fas fa-check-circle"></i> Active</span>
                @else
                  <span class="text-red-600"><i class="fas fa-times-circle"></i> Inactive</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Redemptions -->
      @if($voucher->redemptions->count() > 0)
      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-history text-orange-600"></i> Redemption History</div>
        </div>
        <div class="card-body p-0">
          <table class="data-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Food Item</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($voucher->redemptions as $redemption)
              <tr>
                <td>{{ $redemption->created_at->format('d M Y H:i') }}</td>
                <td>{{ $redemption->foodListing->item_name ?? 'N/A' }}</td>
                <td>£{{ number_format($redemption->amount_used, 2) }}</td>
                <td>
                  @if($redemption->status === 'confirmed')
                    <span class="badge badge-green">Confirmed</span>
                  @else
                    <span class="badge badge-yellow">{{ ucfirst($redemption->status) }}</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>

    <!-- Sidebar -->
    <div>
      <!-- Summary Card -->
      <div class="card bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200 mb-4">
        <div class="card-body">
          <div class="text-sm text-blue-700 font-semibold mb-3">Voucher Summary</div>
          
          <div class="space-y-3">
            <div class="flex justify-between items-center pb-3 border-b border-blue-200">
              <span class="text-sm text-blue-700">Original Value</span>
              <span class="font-bold text-blue-900">£{{ number_format($voucher->value, 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center pb-3 border-b border-blue-200">
              <span class="text-sm text-blue-700">Redeemed</span>
              <span class="font-bold text-orange-600">£{{ number_format($voucher->value - $voucher->remaining_value, 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="text-sm text-blue-700">Remaining</span>
              <span class="font-bold text-green-600">£{{ number_format($voucher->remaining_value, 2) }}</span>
            </div>
          </div>

          @if($voucher->remaining_value == 0)
          <div class="mt-4 p-3 bg-green-100 border border-green-300 rounded-lg">
            <p class="text-xs text-green-800"><i class="fas fa-check-circle"></i> Fully redeemed</p>
          </div>
          @endif
        </div>
      </div>

      <!-- Actions -->
      @if($voucher->status === 'active')
      <div class="card">
        <div class="card-body">
          <form action="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.revoke', $voucher) : route('school.vouchers.revoke', $voucher) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this voucher? The remaining balance will be refunded to your wallet.');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger w-full">
              <i class="fas fa-ban"></i> Revoke Voucher
            </button>
          </form>
        </div>
      </div>
      @endif

      <!-- Back Button -->
      <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.index') : route('school.vouchers.index') }}" class="btn btn-secondary w-full mt-4">
        <i class="fas fa-arrow-left"></i> Back to Vouchers
      </a>
    </div>
  </div>
</div>

@endsection
