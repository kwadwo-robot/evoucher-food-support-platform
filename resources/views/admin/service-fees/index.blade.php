@extends('layouts.dashboard')

@section('title', 'Service Fees Management')
@section('page-title', 'Service Fees Management')

@section('content')
<div class="page-hd">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Service Fees Management</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.service-fees.settings') }}" class="btn btn-info btn-sm">
                ⚙️ Settings
            </a>
            <a href="{{ route('admin.service-fees.export') }}" class="btn btn-success btn-sm">
                📥 Export CSV
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Collected -->
    <div class="col-md-3 mb-3">
        <div class="card border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small font-weight-bold">Total Collected</p>
                        <p class="text-success h4 font-weight-bold mt-2">£{{ number_format($stats['total_collected'], 2) }}</p>
                        <p class="text-muted small mt-1">From {{ $stats['collected_transactions'] }} transactions</p>
                    </div>
                    <div class="text-success" style="font-size: 2rem;">💰</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pending -->
    <div class="col-md-3 mb-3">
        <div class="card border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small font-weight-bold">Total Pending</p>
                        <p class="text-warning h4 font-weight-bold mt-2">£{{ number_format($stats['total_pending'], 2) }}</p>
                        <p class="text-muted small mt-1">From {{ $stats['pending_transactions'] }} transactions</p>
                    </div>
                    <div class="text-warning" style="font-size: 2rem;">⏳</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Fee % -->
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small font-weight-bold">Current Fee %</p>
                        <p class="text-primary h4 font-weight-bold mt-2">{{ number_format($stats['current_percentage'], 2) }}%</p>
                        <p class="text-muted small mt-1"><a href="{{ route('admin.service-fees.settings') }}" class="text-primary">Change</a></p>
                    </div>
                    <div class="text-primary" style="font-size: 2rem;">⚙️</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="col-md-3 mb-3">
        <div class="card border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small font-weight-bold">Total Transactions</p>
                        <p class="text-info h4 font-weight-bold mt-2">{{ $stats['total_transactions'] }}</p>
                        <p class="text-muted small mt-1">All service fees</p>
                    </div>
                    <div class="text-info" style="font-size: 2rem;">📊</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.service-fees.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                All Transactions
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'pending']) }}" class="btn {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm">
                Pending
            </a>
            <a href="{{ route('admin.service-fees.index', ['status' => 'collected']) }}" class="btn {{ request('status') === 'collected' ? 'btn-success' : 'btn-outline-success' }} btn-sm">
                Collected
            </a>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Shop</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Fee %</th>
                    <th class="text-right">Fee Amount</th>
                    <th class="text-right">After Fee</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Action</th>
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
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="text-right">£{{ number_format($transaction->total_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($transaction->service_fee_percentage, 2) }}%</td>
                        <td class="text-right text-danger font-weight-bold">£{{ number_format($transaction->service_fee_amount, 2) }}</td>
                        <td class="text-right text-success font-weight-bold">£{{ number_format($transaction->amount_after_fee, 2) }}</td>
                        <td>
                            <span class="badge 
                                @if($transaction->status === 'collected')
                                    bg-success
                                @elseif($transaction->status === 'pending')
                                    bg-warning
                                @else
                                    bg-danger
                                @endif
                            ">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.service-fees.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No service fee transactions yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($transactions->hasPages())
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
@endif
@endsection
