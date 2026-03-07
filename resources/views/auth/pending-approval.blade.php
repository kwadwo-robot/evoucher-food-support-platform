@extends('layouts.app')
@section('title', 'Awaiting Approval')
@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-clock text-amber-500 text-4xl"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Awaiting Approval</h1>
    <p class="text-gray-500 mb-2">Your account is pending review by our admin team.</p>
    <p class="text-gray-500 mb-8">You will receive an email notification once your account has been approved. This usually takes 1-2 business days.</p>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 text-left">
        <h3 class="font-semibold text-amber-800 mb-2"><i class="fas fa-info-circle mr-2"></i>What happens next?</h3>
        <ul class="text-sm text-amber-700 space-y-1">
            <li><i class="fas fa-check mr-2 text-amber-500"></i>Admin reviews your registration details</li>
            <li><i class="fas fa-check mr-2 text-amber-500"></i>You receive an approval email</li>
            <li><i class="fas fa-check mr-2 text-amber-500"></i>You can then log in and access your dashboard</li>
        </ul>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">Sign out</button>
    </form>
</div>
@endsection
