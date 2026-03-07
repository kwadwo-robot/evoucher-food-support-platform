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
      <form method="POST" action="{{ route('vcfse.donate.process') }}" id="donationForm">
        @csrf
        <div class="mb-5">
          <label class="form-label">Select Amount</label>
          <div class="grid grid-cols-3 gap-3 mb-3">
            @foreach([10, 25, 50, 100, 250, 500] as $amt)
            <label style="cursor:pointer">
              <input type="radio" name="preset_amount" value="{{ $amt }}" style="display:none" onchange="document.getElementById('custom_amount').value='{{ $amt }}'">
              <div class="amount-btn" style="padding:12px;border:2px solid #e2e8f0;border-radius:10px;text-align:center;font-size:15px;font-weight:700;color:#334155;transition:all .15s;cursor:pointer" onclick="this.parentElement.querySelector('input').checked=true;document.querySelectorAll('.amount-btn').forEach(b=>b.style.borderColor='#e2e8f0');this.style.borderColor='#16a34a';this.style.color='#16a34a';this.style.background='#f0fdf4'">
                £{{ $amt }}
              </div>
            </label>
            @endforeach
          </div>
        </div>
        <div class="mb-5">
          <label class="form-label">Or Enter Custom Amount (£)</label>
          <input type="number" name="amount" id="custom_amount" min="1" max="10000" step="0.01" placeholder="Enter amount..." class="form-input" required>
        </div>
        <div class="mb-5">
          <label class="form-label">Donation Message (optional)</label>
          <textarea name="message" rows="3" placeholder="Add a message with your donation..." class="form-textarea"></textarea>
        </div>
        <div style="padding:16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:20px">
          <div style="font-size:13px;color:#15803d;line-height:1.7">
            <strong>How your donation helps:</strong> Every £10 donated funds approximately one food voucher for a family in need. Your contribution directly supports food security in Northamptonshire.
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:15px">
          <i class="fas fa-lock"></i> Proceed to Secure Payment
        </button>
      </form>
    </div>
  </div>
  <!-- Why Donate -->
  <div>
    <div class="card mb-4" style="background:linear-gradient(135deg,#0f172a,#1e293b);border:none;color:#fff">
      <div class="card-body" style="padding:24px">
        <div style="font-size:28px;margin-bottom:12px">💚</div>
        <div style="font-size:16px;font-weight:800;margin-bottom:8px">Why Donate?</div>
        <div style="font-size:13px;opacity:.75;line-height:1.8">
          <div class="mb-2">✅ Reduce food waste in local shops</div>
          <div class="mb-2">✅ Support families in food poverty</div>
          <div class="mb-2">✅ Fund vouchers for recipients</div>
          <div class="mb-2">✅ Strengthen community resilience</div>
          <div>✅ Pilot project for Northamptonshire</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body" style="padding:20px">
        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:12px">Donation Impact</div>
        <div class="mb-3">
          <div class="flex justify-between mb-1">
            <span style="font-size:12.5px;color:#64748b">£10 funds</span>
            <span style="font-size:12.5px;font-weight:600;color:#16a34a">1 food voucher</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:20%"></div></div>
        </div>
        <div class="mb-3">
          <div class="flex justify-between mb-1">
            <span style="font-size:12.5px;color:#64748b">£50 funds</span>
            <span style="font-size:12.5px;font-weight:600;color:#16a34a">5 food vouchers</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:50%"></div></div>
        </div>
        <div>
          <div class="flex justify-between mb-1">
            <span style="font-size:12.5px;color:#64748b">£100 funds</span>
            <span style="font-size:12.5px;font-weight:600;color:#16a34a">10 food vouchers</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:100%"></div></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
