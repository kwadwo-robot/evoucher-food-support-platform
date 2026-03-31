@extends('layouts.dashboard')
@section('title', 'Redeems')
@section('page-title', 'Manage Redeems')
@section('content')
<div class="page-hd">
  <h1>Redeems</h1>
    <p>Track and manage all food item redeems</p>
</div>

<!-- Filters -->
<div class="card mb-6">
  <div class="card-body">
    <form method="GET" class="flex gap-3 flex-wrap items-end">
      <div class="flex-1 min-w-64">
        <label class="form-label">Search by recipient or item</label>
        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}" class="form-input">
      </div>
      <div class="flex-1 min-w-48">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
          <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
      <a href="{{ route('admin.redemptions.index') }}" class="btn btn-secondary">Clear</a>
    </form>
  </div>
</div>

<!-- Redemptions Table -->
<div class="card">
  <div class="card-body">
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Item</th>
            <th>Recipient</th>
            <th>Voucher Code</th>
            <th>Amount Owed</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($redemptions as $redemption)
          <tr>
            <td>
              <div style="font-weight:600;color:#0f172a">{{ $redemption->foodListing->item_name ?? 'N/A' }}</div>
              <div style="font-size:12px;color:#64748b">{{ $redemption->foodListing->shopUser->name ?? 'N/A' }}</div>
            </td>
            <td style="font-size:12px">{{ $redemption->recipient->name ?? 'N/A' }}</td>
            <td>
              @if($redemption->voucher)
                <code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:11px;font-weight:700;color:#16a34a">{{ $redemption->voucher->code }}</code>
              @else
                <span style="color:#94a3b8">—</span>
              @endif
            </td>
            <td style="font-weight:600">£{{ number_format($redemption->amount_owed, 2) }}</td>
            <td>
              @if($redemption->status === 'pending')<span class="badge badge-yellow">Pending</span>
              @elseif($redemption->status === 'confirmed')<span class="badge badge-blue">Confirmed</span>
              @elseif($redemption->status === 'completed')<span class="badge badge-green">Completed</span>
              @else<span class="badge badge-gray">{{ ucfirst($redemption->status) }}</span>@endif
            </td>
            <td style="font-size:12px;color:#64748b">{{ $redemption->created_at->format('d M Y') }}</td>
            <td>
              <a href="{{ route('admin.users.show', $redemption->recipient) }}" class="btn btn-sm btn-secondary">View</a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-8 text-slate-400">No redeems found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-6">
      {{ $redemptions->links() }}
    </div>
  </div>
</div>
@endsection
