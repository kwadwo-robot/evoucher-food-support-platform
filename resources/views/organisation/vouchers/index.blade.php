@extends('layouts.dashboard')
@section('title', 'Issued Vouchers')
@section('page-title', 'Voucher Management')
@section('topbar-actions')
<a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.create') : route('school.vouchers.create') }}" class="btn btn-primary btn-sm">
  <i class="fas fa-plus"></i> Issue Voucher
</a>
@endsection
@section('content')

<div class="page-content">
  <!-- Stats -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
      <div class="stat-icon mb-3" style="background:#dbeafe;color:#3b82f6;"><i class="fas fa-ticket"></i></div>
      <div class="stat-label">Total Issued</div>
      <div class="stat-value">{{ $totalIssued }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon mb-3" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
      <div class="stat-label">Active</div>
      <div class="stat-value">{{ $activeCount }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon mb-3" style="background:#fef3c7;color:#d97706;"><i class="fas fa-hourglass-half"></i></div>
      <div class="stat-label">Pending Redeem</div>
      <div class="stat-value">{{ $pendingRedemption }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon mb-3" style="background:#fee2e2;color:#ef4444;"><i class="fas fa-ban"></i></div>
      <div class="stat-label">Cancelled</div>
      <div class="stat-value">{{ $cancelledCount }}</div>
    </div>
  </div>

  <!-- Vouchers Table -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-list text-blue-600"></i> Issued Vouchers</div>
      <div class="flex gap-2">
        <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.reports.vouchers-excel') : route('school.reports.vouchers-excel') }}" class="btn btn-sm" style="background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;">
          <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.reports.vouchers-pdf') : route('school.reports.vouchers-pdf') }}" class="btn btn-sm" style="background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;">
          <i class="fas fa-file-pdf"></i> Export PDF
        </a>
      </div>
    </div>
    <div class="card-body p-0">
      @if($totalIssued > 0)
        <table class="data-table">
          <thead>
            <tr>
              <th>Voucher Code</th>
              <th>Recipient</th>
              <th>Value</th>
              <th>Remaining</th>
              <th>Status</th>
              <th>Issued Date</th>
              <th>Expiry Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vouchers as $voucher)
            <tr>
              <td>
                <span class="font-mono font-bold text-blue-600">{{ $voucher->code }}</span>
              </td>
              <td>
                <div class="font-semibold">{{ $voucher->recipient->name ?? 'N/A' }}</div>
                <div class="text-xs text-gray-500">{{ $voucher->recipient->email ?? 'N/A' }}</div>
              </td>
              <td class="font-semibold">£{{ number_format($voucher->value, 2) }}</td>
              <td class="font-semibold text-green-600">£{{ number_format($voucher->remaining_value, 2) }}</td>
              <td>
                @if($voucher->status === 'active')
                  @if($voucher->expiry_date >= now())
                    <span class="badge badge-green">Active</span>
                  @else
                    <span class="badge badge-red">Expired</span>
                  @endif
                @elseif($voucher->status === 'cancelled')
                  <span class="badge badge-gray">Cancelled</span>
                @else
                  <span class="badge badge-blue">{{ ucfirst($voucher->status) }}</span>
                @endif
              </td>
              <td>{{ $voucher->created_at->format('d M Y H:i') }}</td>
              <td>
                @if($voucher->expiry_date >= now())
                  <span class="text-green-600">{{ $voucher->expiry_date->format('d M Y') }}</span>
                @else
                  <span class="text-red-600">{{ $voucher->expiry_date->format('d M Y') }}</span>
                @endif
              </td>
              <td>
                <div class="flex gap-2">
                  <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.show', $voucher) : route('school.vouchers.show', $voucher) }}" class="btn btn-sm" style="background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;padding:5px 10px;">
                    <i class="fas fa-eye"></i>
                  </a>
                  @if($voucher->status === 'active')
                    <form action="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.cancel', $voucher) : route('school.vouchers.cancel', $voucher) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this voucher?');">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#ef4444;border:1px solid #fecaca;padding:5px 10px;">
                        <i class="fas fa-times"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
          {{ $vouchers->links() }}
        </div>
      @else
        <div style="padding:48px 24px;text-align:center;">
          <div style="font-size:32px;margin-bottom:12px;color:#94a3b8;"><i class="fas fa-ticket"></i></div>
          <div style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:4px;">No vouchers issued yet</div>
          <div style="font-size:13px;color:#94a3b8;margin-bottom:16px;">Start by issuing your first voucher to a recipient</div>
          <a href="{{ auth()->user()->role === 'vcfse' ? route('vcfse.vouchers.create') : route('school.vouchers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Issue First Voucher
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection
