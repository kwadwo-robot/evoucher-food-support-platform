@extends('layouts.app')
@section('title', 'Make a Donation')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Make a Donation</h1>
            <p class="text-gray-500 text-sm">Fund food vouchers via Stripe</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700"><i class="fas fa-lock mr-2 text-green-500"></i>Payments are securely processed by Stripe. Your card details are never stored on our servers.</p>
        </div>

        <form method="POST" action="{{ auth()->user()->isVcfse() ? route('vcfse.donate.initiate') : route('school.donate.initiate') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Donation Amount</label>
                    <div class="grid grid-cols-4 gap-2 mb-3" x-data="{ custom: false, amount: 50 }">
                        @foreach([10, 25, 50, 100] as $preset)
                        <button type="button" onclick="document.getElementById('amount').value='{{ $preset }}'; document.querySelectorAll('.preset-btn').forEach(b=>b.classList.remove('ring-2','ring-green-500')); this.classList.add('ring-2','ring-green-500')"
                            class="preset-btn py-2 text-sm font-semibold border border-gray-200 rounded-lg hover:border-green-400 transition-colors {{ $preset === 50 ? 'ring-2 ring-green-500' : '' }}">
                            £{{ $preset }}
                        </button>
                        @endforeach
                    </div>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', 50) }}" min="5" max="10000" step="0.01" required
                        placeholder="Or enter custom amount"
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('amount') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Minimum £5</p>
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Any notes about this donation..."
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i>
                    Proceed to Secure Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
