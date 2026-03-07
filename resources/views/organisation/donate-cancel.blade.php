@extends('layouts.app')
@section('title', 'Donation Cancelled')
@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-times-circle text-gray-400 text-4xl"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Donation Cancelled</h1>
    <p class="text-gray-500 mb-8">Your donation was not processed. No payment has been taken.</p>
    <a href="{{ route(auth()->user()->getDashboardRoute()) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
        Back to Dashboard
    </a>
</div>
@endsection
