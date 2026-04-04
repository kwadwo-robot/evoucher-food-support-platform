@extends('layouts.dashboard')

@section('title', 'Service Fees Management')
@section('page-title', 'Service Fees Management')

@section('content')
<div class="page-hd">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0;">Service Fees Management</h1>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.service-fees.settings') }}" class="tb-btn" style="background: #3b82f6; color: white; border: none;">
                <i class="fas fa-cog" style="margin-right: 6px;"></i> Settings
            </a>
            <a href="{{ route('admin.service-fees.export') }}" class="tb-btn" style="background: #10b981; color: white; border: none;">
                <i class="fas fa-download" style="margin-right: 6px;"></i> Export CSV
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #15803d; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #15803d; cursor: pointer; font-size: 18px;">×</button>
    </div>
@endif

<!-- Statistics Cards - 4 Column Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <!-- Total Collected -->
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="stat-label">Total Collected</div>
                <div class="stat-value" style="color: #10b981;">£{{ number_format($stats['total_collected'], 2) }}</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">From {{ $stats['collected_transactions'] }} transactions</div>
            </div>
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Total Pending -->
    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="stat-label">Total Pending</div>
                <div class="stat-value" style="color: #f59e0b;">£{{ number_format($stats['total_pending'], 2) }}</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">From {{ $stats['pending_transactions'] }} transactions</div>
            </div>
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <!-- Current Fee % -->
    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="stat-label">Current Fee %</div>
                <div class="stat-value" style="color: #3b82f6;">{{ number_format($stats['current_percentage'], 2) }}%</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;"><a href="{{ route('admin.service-fees.settings') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Change</a></div>
            </div>
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="stat-label">Total Transactions</div>
                <div class="stat-value" style="color: #8b5cf6;">{{ $stats['total_transactions'] }}</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">All service fees</div>
            </div>
            <div class="stat-icon" style="background: #faf5ff; color: #8b5cf6;">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 16px;">
    <div class="card-body" style="padding: 16px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.service-fees.index') }}" class="tb-btn" style="{{ !request('status') ? 'background: #3b82f6; color: white; border: none;' : '' }}">
                <i class="fas fa-list"></i> All Transactions
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'pending']) }}" class="tb-btn" style="{{ request('status') === 'pending' ? 'background: #f59e0b; color: white; border: none;' : '' }}">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'collected']) }}" class="tb-btn" style="{{ request('status') === 'collected' ? 'background: #10b981; color: white; border: none;' : '' }}">
                <i class="fas fa-check"></i> Collected
            </a>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop</th>
                <th style="text-align: right;">Total Amount</th>
                <th style="text-align: right;">Fee %</th>
                <th style="text-align: right;">Fee Amount</th>
                <th style="text-align: right;">After Fee</th>
                <th>Status</th>
                <th>Date</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>#{{ $transaction->id }}</td>
                    <td>
                        @if($transaction->shop)
                            {{ $transaction->shop->name }}
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td style="text-align: right;">£{{ number_format($transaction->total_amount, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($transaction->service_fee_percentage, 2) }}%</td>
                    <td style="text-align: right; color: #dc2626; font-weight: 600;">£{{ number_format($transaction->service_fee_amount, 2) }}</td>
                    <td style="text-align: right; color: #10b981; font-weight: 600;">£{{ number_format($transaction->amount_after_fee, 2) }}</td>
                    <td>
                        <span class="badge 
                            @if($transaction->status === 'collected')
                                badge-green
                            @elseif($transaction->status === 'pending')
                                badge-yellow
                            @else
                                badge-red
                            @endif
                        ">
                            @if($transaction->status === 'collected')
                                <i class="fas fa-check-circle"></i>
                            @elseif($transaction->status === 'pending')
                                <i class="fas fa-hourglass-half"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                    <td style="text-align: center;">
                        <a href="{{ route('admin.service-fees.show', $transaction->id) }}" class="tb-btn" style="font-size: 12px;">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 32px; color: #94a3b8;">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
                        No service fee transactions yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($transactions->hasPages())
    <div style="margin-top: 24px;">
        {{ $transactions->links() }}
    </div>
@endif
@endsection
