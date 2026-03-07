@extends('layouts.dashboard')
@section('title','Make a Donation')
@section('page-title','Make a Donation')
@section('content')
<div class="page-hd">
  <h1>Make a Donation</h1>
  <p>Support the eVoucher Food Support Programme in Northamptonshire</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="card lg:col-span-2">
    <div class="card-hd"><div class="card-title"><i class="fas fa-hand-holding-heart text-green-600"></i> Donation Details</div></div>
    <div class="card-body">
      @if(!config('services.stripe.key') || config('services.stripe.key') === 'your_stripe_key')
      <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Stripe payment processing is not yet configured. Please contact the platform administrator.</div>
      @endif
      <form method="POST" action="{{ route('school.donate.process') }}">
        @csrf
        <div class="mb-5">
          <label class="form-label">Select Amount</label>
          <div class="grid grid-cols-3 gap-3 mb-3">
            @foreach([10, 25, 50, 100, 250, 500] as $amt)
            <div style="padding:12px;border:2px solid #e2e8f0;border-radius:10px;text-align:center;font-size:15px;font-weight:700;color:#334155;cursor:pointer;transition:all .15s"
                 onclick="document.getElementById('amount').value='{{ $amt }}';document.querySelectorAll('.amt-opt').forEach(e=>e.style.borderColor='#e2e8f0');this.style.borderColor='#16a34a';this.style.color='#16a34a';this.style.background='#f0fdf4'"
                 class="amt-opt">£{{ $amt }}</div>
            @endforeach
          </div>
        </div>
        <div class="mb-5">
          <label class="form-label">Or Enter Custom Amount (£)</label>
          <input type="number" name="amount" id="amount" min="1" max="10000" step="0.01" placeholder="Enter amount..." class="form-input" required>
        </div>
        <div class="mb-5">
          <label class="form-label">Donation Message (optional)</label>
          <textarea name="message" rows="3" placeholder="Add a message with your donation..." class="form-textarea"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:15px">
          <i class="fas fa-lock"></i> Proceed to Secure Payment
        </button>
      </form>
    </div>
  </div>
  <div class="card" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:none;color:#fff;align-self:start">
    <div class="card-body" style="padding:24px">
      <div style="font-size:28px;margin-bottom:12px">🏫</div>
      <div style="font-size:16px;font-weight:800;margin-bottom:8px">School & Care Support</div>
      <div style="font-size:13px;opacity:.75;line-height:1.8">
        <div class="mb-2">✅ Support vulnerable families</div>
        <div class="mb-2">✅ Reduce food waste locally</div>
        <div class="mb-2">✅ Fund community food vouchers</div>
        <div class="mb-2">✅ Strengthen Northamptonshire</div>
        <div>✅ Transparent donation tracking</div>
      </div>
    </div>
  </div>
</div>
@endsection
