@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 mb-4">Donations Management</h1>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Donations</h6>
                    <h3>{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Completed</h6>
                    <h3 class="text-success">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Processing</h6>
                    <h3 class="text-info">{{ $stats['processing'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Failed</h6>
                    <h3 class="text-danger">{{ $stats['failed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Amount</h6>
                    <h3>£{{ number_format($stats['total_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.donations.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Donations Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                ID
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort_by' => 'donor_email', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                Email
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort_by' => 'amount', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                Amount
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                Status
                            </a>
                        </th>
                        <th>Payment ID</th>
                        <th>
                            <a href="{{ route('admin.donations.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc'])) }}">
                                Date
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $donation)
                        <tr>
                            <td>#{{ $donation->id }}</td>
                            <td>{{ $donation->donor_email ?? $donation->email ?? 'N/A' }}</td>
                            <td>£{{ number_format($donation->amount, 2) }}</td>
                            <td>
                                @if($donation->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($donation->status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($donation->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($donation->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ substr($donation->stripe_payment_id ?? 'N/A', 0, 20) }}...</small>
                            </td>
                            <td>{{ $donation->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.donations.show', $donation) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No donations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $donations->links() }}
    </div>
</div>

<style>
    .card {
        border: 1px solid #e0e0e0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .table a {
        color: #0066cc;
        text-decoration: none;
    }
    
    .table a:hover {
        text-decoration: underline;
    }
</style>
@endsection
