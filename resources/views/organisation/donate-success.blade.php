@extends('layouts.app')
@section('title', 'Donation Successful')
@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-check-circle text-green-500 text-4xl"></i>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-3">Thank You!</h1>
    <p class="text-gray-500 mb-2">Your donation has been received successfully.</p>
    @if($donation)
    <p class="text-2xl font-bold text-green-600 mb-6">£{{ number_format($donation->amount, 2) }}</p>
    @endif
    <p class="text-sm text-gray-500 mb-8">Your contribution will help fund food vouchers for families in Northamptonshire. The admin team will allocate vouchers based on current need.</p>
    <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
        Back to Dashboard
    </a>
</div>
@endsection
