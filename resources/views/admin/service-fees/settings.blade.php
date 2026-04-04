@extends('layouts.dashboard')

@section('title', 'Service Fee Settings')
@section('page-title', 'Service Fee Settings')

@section('content')
<div class="page-hd">
    <h1 style="font-size: 28px; font-weight: 700; color: #0f172a; margin: 0;">Service Fee Settings</h1>
</div>

@if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #15803d; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fas fa-check-circle" style="margin-right: 8px;"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #15803d; cursor: pointer; font-size: 18px;">×</button>
    </div>
@endif

@if(session('error'))
    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <span><i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>{{ session('error') }}</span>
        <button onclick="this.parentElement.style.display='none';" style="background: none; border: none; color: #b91c1c; cursor: pointer; font-size: 18px;">×</button>
    </div>
@endif

@if($errors->any())
    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
        <strong><i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>Validation Errors:</strong>
        <ul style="margin: 8px 0 0 0; padding-left: 24px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.service-fees.update-percentage') }}" method="POST">
                @csrf

                <div style="margin-bottom: 24px;">
                    <label for="service_fee_percentage" style="display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">
                        <i class="fas fa-percent" style="margin-right: 6px; color: #3b82f6;"></i>Service Fee Percentage (%)
                    </label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input 
                            type="number" 
                            id="service_fee_percentage" 
                            name="service_fee_percentage" 
                            value="{{ $currentPercentage }}"
                            min="0"
                            max="100"
                            step="0.01"
                            style="flex: 1; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 600;"
                            required
                            onchange="updateCalculation()"
                            oninput="updateCalculation()"
                        >
                        <span style="font-size: 16px; font-weight: 700; color: #0f172a;">%</span>
                    </div>
                    <small style="display: block; font-size: 12px; color: #94a3b8; margin-top: 8px;">
                        This percentage will be deducted from all new payout requests as a service fee.
                    </small>
                </div>

                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                    <h5 style="font-size: 13px; font-weight: 700; color: #1e40af; margin: 0 0 12px 0;">
                        <i class="fas fa-calculator" style="margin-right: 6px;"></i>Example Calculation
                    </h5>
                    <p style="font-size: 12px; color: #1e40af; margin: 0 0 12px 0;">
                        If a shop requests a payout of £100 with a <span id="examplePercentage" style="font-weight: 700;">{{ $currentPercentage }}</span>% service fee:
                    </p>
                    <ul style="font-size: 12px; color: #1e40af; margin: 0; padding-left: 20px; line-height: 1.8;">
                        <li>Total Amount: £100.00</li>
                        <li>Service Fee (<span id="examplePercentage2" style="font-weight: 700;">{{ $currentPercentage }}</span>%): <span style="color: #dc2626; font-weight: 700;">£<span id="exampleFee">{{ number_format(100 * ($currentPercentage / 100), 2) }}</span></span></li>
                        <li>Amount Paid to Shop: <span style="color: #10b981; font-weight: 700;">£<span id="exampleAfterFee">{{ number_format(100 - (100 * ($currentPercentage / 100)), 2) }}</span></span></li>
                    </ul>
                </div>

                <div style="display: flex; gap: 12px; margin-bottom: 24px;">
                    <button 
                        type="submit" 
                        style="flex: 1; background: #3b82f6; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;"
                    >
                        <i class="fas fa-save"></i>Save Changes
                    </button>
                    <a 
                        href="{{ route('admin.service-fees.index') }}" 
                        style="flex: 1; background: #f1f5f9; color: #0f172a; border: 1px solid #e2e8f0; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;"
                    >
                        <i class="fas fa-times"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div style="background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 16px; margin-top: 16px;">
        <h5 style="font-size: 13px; font-weight: 700; color: #a16207; margin: 0 0 8px 0;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>Important Notice
        </h5>
        <p style="font-size: 12px; color: #a16207; margin: 0;">
            Changing the service fee percentage will only affect <strong>new payout requests</strong>. 
            Existing pending or approved payouts will retain their original fee percentage.
        </p>
    </div>
</div>

<script>
function updateCalculation() {
    const percentage = parseFloat(document.getElementById('service_fee_percentage').value) || 0;
    const totalAmount = 100;
    const fee = (totalAmount * percentage / 100).toFixed(2);
    const afterFee = (totalAmount - fee).toFixed(2);
    
    document.getElementById('examplePercentage').textContent = percentage.toFixed(2);
    document.getElementById('examplePercentage2').textContent = percentage.toFixed(2);
    document.getElementById('exampleFee').textContent = fee;
    document.getElementById('exampleAfterFee').textContent = afterFee;
}
</script>
@endsection
