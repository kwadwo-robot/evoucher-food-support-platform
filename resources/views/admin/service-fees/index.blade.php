@extends('layouts.dashboard')

@section('title', 'Service Fees Management')
@section('page-title', 'Service Fees Management')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
        <h1 style="font-size: 32px; font-weight: 700; color: #0f172a; margin: 0;">Service Fees Management</h1>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.service-fees.settings') }}" style="background: #3b82f6; color: white; border: none; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fas fa-cog"></i> Settings
            </a>
            <div style="position: relative; display: inline-block;">
                <button onclick="toggleExportMenu()" style="background: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; cursor: pointer;">
                    <i class="fas fa-download"></i> Export <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                </button>
                <div id="exportMenu" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 180px; z-index: 1000; margin-top: 4px;">
                    <a href="{{ route('admin.service-fees.export', ['format' => 'csv']) }}" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; color: #0f172a; text-decoration: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #f1f5f9;">
                        <i class="fas fa-file-csv" style="color: #10b981;"></i> Export CSV
                    </a>
                    <a href="{{ route('admin.service-fees.export', ['format' => 'excel']) }}" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; color: #0f172a; text-decoration: none; font-size: 14px; font-weight: 500; border-bottom: 1px solid #f1f5f9;">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.service-fees.export', ['format' => 'pdf']) }}" style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; color: #0f172a; text-decoration: none; font-size: 14px; font-weight: 500;">
                        <i class="fas fa-file-pdf" style="color: #dc2626;"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #15803d; padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 15px;">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #15803d; cursor: pointer; font-size: 20px;">×</button>
    </div>
@endif

<!-- Statistics Cards - 4 Column Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <!-- Total Collected -->
    <div class="stat-card" style="border-left: 4px solid #10b981; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Collected</div>
                <div style="font-size: 28px; font-weight: 700; color: #10b981; margin-top: 8px;">£{{ number_format($stats['total_collected'], 2) }}</div>
                <div style="font-size: 14px; color: #94a3b8; margin-top: 6px;">From {{ $stats['collected_transactions'] }} transactions</div>
            </div>
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Total Pending -->
    <div class="stat-card" style="border-left: 4px solid #f59e0b; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Pending</div>
                <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-top: 8px;">£{{ number_format($stats['total_pending'], 2) }}</div>
                <div style="font-size: 14px; color: #94a3b8; margin-top: 6px;">From {{ $stats['pending_transactions'] }} transactions</div>
            </div>
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <!-- Current Fee % -->
    <div class="stat-card" style="border-left: 4px solid #3b82f6; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Current Fee %</div>
                <div style="font-size: 28px; font-weight: 700; color: #3b82f6; margin-top: 8px;">{{ number_format($stats['current_percentage'], 2) }}%</div>
                <div style="font-size: 14px; color: #94a3b8; margin-top: 6px;"><a href="{{ route('admin.service-fees.settings') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Change</a></div>
            </div>
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="stat-card" style="border-left: 4px solid #8b5cf6; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Transactions</div>
                <div style="font-size: 28px; font-weight: 700; color: #8b5cf6; margin-top: 8px;">{{ $stats['total_transactions'] }}</div>
                <div style="font-size: 14px; color: #94a3b8; margin-top: 6px;">All service fees</div>
            </div>
            <div class="stat-icon" style="background: #faf5ff; color: #8b5cf6; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 16px;">
    <div class="card-body" style="padding: 16px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.service-fees.index') }}" style="background: {{ !request('status') ? '#3b82f6' : '#f1f5f9' }}; color: {{ !request('status') ? 'white' : '#0f172a' }}; border: {{ !request('status') ? 'none' : '1px solid #e2e8f0' }}; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fas fa-list"></i> All Transactions
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'pending']) }}" style="background: {{ request('status') === 'pending' ? '#f59e0b' : '#f1f5f9' }}; color: {{ request('status') === 'pending' ? 'white' : '#0f172a' }}; border: {{ request('status') === 'pending' ? 'none' : '1px solid #e2e8f0' }}; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                <i class="fas fa-clock"></i> Pending
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'collected']) }}" style="background: {{ request('status') === 'collected' ? '#10b981' : '#f1f5f9' }}; color: {{ request('status') === 'collected' ? 'white' : '#0f172a' }}; border: {{ request('status') === 'collected' ? 'none' : '1px solid #e2e8f0' }}; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
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
                <th style="font-size: 14px;">ID</th>
                <th style="font-size: 14px;">Shop</th>
                <th style="text-align: right; font-size: 14px;">Total Amount</th>
                <th style="text-align: right; font-size: 14px;">Fee %</th>
                <th style="text-align: right; font-size: 14px;">Fee Amount</th>
                <th style="text-align: right; font-size: 14px;">After Fee</th>
                <th style="font-size: 14px;">Status</th>
                <th style="font-size: 14px;">Date</th>
                <th style="text-align: center; font-size: 14px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td style="font-size: 14px;">#{{ $transaction->id }}</td>
                    <td style="font-size: 14px;">
                        @if($transaction->shop)
                            {{ $transaction->shop->name }}
                        @else
                            <span style="color: #94a3b8;">N/A</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-size: 14px;">£{{ number_format($transaction->total_amount, 2) }}</td>
                    <td style="text-align: right; font-size: 14px;">{{ number_format($transaction->service_fee_percentage, 2) }}%</td>
                    <td style="text-align: right; color: #dc2626; font-weight: 600; font-size: 14px;">£{{ number_format($transaction->service_fee_amount, 2) }}</td>
                    <td style="text-align: right; color: #10b981; font-weight: 600; font-size: 14px;">£{{ number_format($transaction->amount_after_fee, 2) }}</td>
                    <td style="font-size: 14px;">
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
                    <td style="font-size: 14px;">{{ $transaction->created_at->format('M d, Y') }}</td>
                    <td style="text-align: center;">
                        <a href="{{ route('admin.service-fees.show', $transaction->id) }}" style="background: #f1f5f9; color: #0f172a; border: 1px solid #e2e8f0; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8; font-size: 16px;">
                        <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.5;"></i>
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

<script>
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('exportMenu');
    const button = event.target.closest('button');
    if (!button && menu.style.display === 'block') {
        menu.style.display = 'none';
    }
});
</script>
@endsection
