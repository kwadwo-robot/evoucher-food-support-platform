@extends('layouts.dashboard')
@section('title','Load Funds')
@section('page-title','Load Funds')
@section('content')
<div class="page-hd">
  <h1>Load Funds</h1>
  <p>Add funds to your wallet using Stripe (Visa/Mastercard)</p>
</div>

<!-- Wallet Balance -->
<div class="card mb-6" style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);color:#fff;border:none">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div>
        <div style="font-size:13px;opacity:0.9">Current Wallet Balance</div>
        <div style="font-size:32px;font-weight:800;margin-top:8px">£{{ number_format($walletBalance, 2) }}</div>
      </div>
      <div style="font-size:48px;opacity:0.3"><i class="fas fa-wallet"></i></div>
    </div>
  </div>
</div>

<!-- Load Funds Form -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-credit-card text-blue-500"></i> Load Funds via Stripe</div>
    </div>
    <div class="card-body">
      <form id="payment-form" style="display:flex;flex-direction:column;gap:16px">
        <div>
          <label class="form-label">Amount (£)</label>
          <div style="display:flex;gap:8px;margin-bottom:12px">
            <button type="button" class="btn btn-secondary btn-sm" onclick="setAmount(10)">£10</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="setAmount(25)">£25</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="setAmount(50)">£50</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="setAmount(100)">£100</button>
          </div>
          <input type="number" id="amount" name="amount" min="1" step="0.01" required 
                 class="form-input" placeholder="Enter amount in pounds">
        </div>

        <div>
          <label class="form-label">Card Details</label>
          <div id="card-element" style="padding:12px;border:1px solid #e2e8f0;border-radius:9px;background:#fff"></div>
          <div id="card-errors" style="color:#dc2626;font-size:13px;margin-top:8px"></div>
        </div>

        <div>
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ $user->email }}" readonly class="form-input" style="background:#f8fafc">
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="width:100%">
          <i class="fas fa-lock mr-2"></i>Load Funds Securely
        </button>

        <div style="font-size:11px;color:#94a3b8;text-align:center">
          <i class="fas fa-shield-alt mr-1"></i>Secure payment powered by Stripe
        </div>
      </form>
    </div>
  </div>

  <!-- Bank Deposits Info -->
  <div class="card">
    <div class="card-hd">
      <div class="card-title"><i class="fas fa-bank text-green-600"></i> Bank Deposits</div>
    </div>
    <div class="card-body">
      @if($bankDeposits->count() > 0)
        <div style="margin-bottom:16px">
          <div style="font-size:12px;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:8px">Verified Deposits</div>
          @foreach($bankDeposits as $deposit)
          <div style="padding:12px;background:#f0fdf4;border-radius:8px;margin-bottom:8px;border-left:3px solid #16a34a">
            <div style="font-weight:600;color:#0f172a">£{{ number_format($deposit->amount, 2) }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
              <i class="fas fa-check-circle text-green-600 mr-1"></i>Verified on {{ $deposit->verified_at->format('d M Y') }}
            </div>
          </div>
          @endforeach
        </div>
      @else
        <div style="padding:16px;background:#fef9c3;border-radius:8px;border-left:3px solid #ca8a04">
          <div style="font-weight:600;color:#a16207;margin-bottom:4px">
            <i class="fas fa-info-circle mr-2"></i>No Verified Bank Deposits
          </div>
          <div style="font-size:12px;color:#a16207;line-height:1.5">
            To enable fund loads, please deposit money in the BAKUP CIC bank account first. Once verified, you can load funds here.
          </div>
        </div>
      @endif

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid #e2e8f0">
        <div style="font-size:12px;color:#94a3b8;text-transform:uppercase;font-weight:600;margin-bottom:8px">Bank Details</div>
        <div style="background:#f8fafc;padding:12px;border-radius:8px;font-size:12px;font-family:monospace">
          <div><strong>Bank:</strong> BAKUP CIC</div>
          <div><strong>Account:</strong> ••••••••••••1234</div>
          <div><strong>Sort Code:</strong> 20-00-00</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Loads -->
<div class="card mt-6">
  <div class="card-hd">
    <div class="card-title"><i class="fas fa-history text-purple-500"></i> Recent Fund Loads</div>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead>
        <tr>
          <th>Amount</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bankDeposits as $deposit)
        <tr>
          <td style="font-weight:600">£{{ number_format($deposit->amount, 2) }}</td>
          <td>{{ $deposit->created_at->format('d M Y H:i') }}</td>
          <td><span class="badge badge-green">Verified</span></td>
        </tr>
        @empty
        <tr>
          <td colspan="3" style="text-align:center;color:#94a3b8;padding:24px">No fund loads yet</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ config("services.stripe.public") }}');
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

cardElement.addEventListener('change', (event) => {
  const displayError = document.getElementById('card-errors');
  displayError.textContent = event.error ? event.error.message : '';
});

function setAmount(amt) {
  document.getElementById('amount').value = amt;
}

document.getElementById('payment-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const amount = parseFloat(document.getElementById('amount').value);
  if (!amount || amount < 1) {
    alert('Please enter a valid amount');
    return;
  }

  document.getElementById('submit-btn').disabled = true;
  document.getElementById('submit-btn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

  try {
    // Create payment intent
    const rolePrefix = '{{ $user->role === "school_care" ? "school" : "vcfse" }}';
    const response = await fetch('/' + rolePrefix + '/fund-load/create-intent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ amount })
    });

    const data = await response.json();
    if (data.error) throw new Error(data.error);

    // Confirm payment
    const result = await stripe.confirmCardPayment(data.clientSecret, {
      payment_method: {
        card: cardElement,
        billing_details: { email: '{{ $user->email }}' }
      }
    });

    if (result.error) {
      document.getElementById('card-errors').textContent = result.error.message;
    } else if (result.paymentIntent.status === 'succeeded') {
      // Confirm on backend
      const confirmResponse = await fetch('/' + rolePrefix + '/fund-load/confirm', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          payment_intent_id: result.paymentIntent.id,
          amount
        })
      });

      const confirmData = await confirmResponse.json();
      if (confirmData.success) {
        alert('Funds loaded successfully! £' + amount + ' has been added to your wallet.');
        location.reload();
      } else {
        alert('Error: ' + confirmData.error);
      }
    }
  } catch (error) {
    document.getElementById('card-errors').textContent = error.message;
  } finally {
    document.getElementById('submit-btn').disabled = false;
    document.getElementById('submit-btn').innerHTML = '<i class="fas fa-lock mr-2"></i>Load Funds Securely';
  }
});
</script>
@endsection
