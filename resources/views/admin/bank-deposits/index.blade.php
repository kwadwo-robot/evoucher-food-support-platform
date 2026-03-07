@extends('layouts.dashboard')
@section('title','Bank Deposits')
@section('page-title','Bank Deposits Verification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Bank Deposits</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference or organisation" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 w-full">Filter</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">Reference</th>
                    <th class="px-6 py-3 text-left">Organisation</th>
                    <th class="px-6 py-3 text-left">Amount</th>
                    <th class="px-6 py-3 text-left">Bank</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3 font-semibold">{{ $deposit->reference }}</td>
                        <td class="px-6 py-3">{{ $deposit->organisation->name }}</td>
                        <td class="px-6 py-3 font-semibold">£{{ number_format($deposit->amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm">{{ $deposit->bank_name }}</td>
                        <td class="px-6 py-3">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                @if($deposit->status === 'verified') bg-green-100 text-green-800
                                @elseif($deposit->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($deposit->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.bank-deposits.show', $deposit) }}" class="text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No deposits found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $deposits->links() }}
    </div>
</div>
@endsection
