@extends('layouts.dashboard')

@section('title', 'Bank Deposit Notifications - eVoucher Platform')

@section('content')
<div class="page-header mb-6">
  <h1>Bank Deposit Notifications</h1>
  <p>View all your submitted bank deposit notifications and their verification status.</p>
</div>

<div class="card">
  <div class="card-hd">
    <div class="card-title">
      <i class="fas fa-university"></i> Your Bank Deposits
    </div>
    <a href="{{ route($role === 'vcfse' ? 'vcfse.bank-deposit-notification.create' : 'school.bank-deposit-notification.create') }}" class="btn btn-primary" style="font-size:12px;padding:6px 14px">
      <i class="fas fa-plus"></i> New Deposit
    </a>
  </div>
  <div class="card-body">
    @if($bankDeposits->count() > 0)
      <table class="data-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Amount</th>
            <th>Bank</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bankDeposits as $deposit)
            <tr>
              <td><strong>{{ $deposit->reference }}</strong></td>
              <td>£{{ number_format($deposit->amount, 2) }}</td>
              <td>{{ $deposit->bank_name }}</td>
              <td>
                @if($deposit->status === 'pending')
                  <span class="badge badge-yellow">Pending</span>
                @elseif($deposit->status === 'verified')
                  <span class="badge badge-green">Verified</span>
                @elseif($deposit->status === 'rejected')
                  <span class="badge badge-red">Rejected</span>
                @endif
              </td>
              <td>{{ $deposit->created_at->format('d M Y H:i') }}</td>
              <td>
                <a href="{{ route($role === 'vcfse' ? 'vcfse.bank-deposit-notification.show' : 'school.bank-deposit-notification.show', $deposit) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                  View
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      
      <div class="mt-4">
        {{ $bankDeposits->links() }}
      </div>
    @else
      <div class="text-center py-12">
        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Bank Deposits Yet</h3>
        <p class="text-gray-500 mb-6">You haven't submitted any bank deposits yet.</p>
        <a href="{{ route($role === 'vcfse' ? 'vcfse.bank-deposit-notification.create' : 'school.bank-deposit-notification.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Submit Bank Deposit
        </a>
      </div>
    @endif
  </div>
</div>

<style>
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #16a34a;
  color: white;
}

.btn-primary:hover {
  background: #15803d;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}
</style>
@endsection
