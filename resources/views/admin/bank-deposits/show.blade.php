@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-8">Bank Deposit Details</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Reference</label>
                <p class="text-lg font-semibold">{{ $deposit->reference }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Amount</label>
                <p class="text-lg font-semibold text-green-600">£{{ number_format($deposit->amount, 2) }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Organisation</label>
                <p class="text-lg">{{ $deposit->organisation->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Status</label>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($deposit->status === 'verified') bg-green-100 text-green-800
                    @elseif($deposit->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($deposit->status) }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Bank Name</label>
                <p class="text-lg">{{ $deposit->bank_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Account Holder</label>
                <p class="text-lg">{{ $deposit->bank_account_holder }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Sort Code</label>
                <p class="text-lg font-mono">{{ $deposit->sort_code }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Account Number</label>
                <p class="text-lg font-mono">{{ $deposit->account_number }}</p>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-600">Submitted</label>
                <p class="text-lg">{{ $deposit->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if($deposit->status === 'pending')
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Verification</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <form action="{{ route('admin.bank-deposits.verify', $deposit) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 w-full">
                        <i class="fas fa-check mr-2"></i>Verify Deposit
                    </button>
                </form>

                <form action="{{ route('admin.bank-deposits.reject', $deposit) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Rejection Reason</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg" required></textarea>
                    </div>
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 w-full" onclick="return confirm('Reject this deposit?')">
                        <i class="fas fa-times mr-2"></i>Reject Deposit
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
