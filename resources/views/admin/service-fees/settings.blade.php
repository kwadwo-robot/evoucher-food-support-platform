@extends('layouts.dashboard')

@section('title', 'Service Fee Settings')
@section('page-title', 'Service Fee Settings')

@section('content')
<div class="page-hd">
    <h1 class="text-3xl font-bold text-gray-900">Service Fee Settings</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.service-fees.update-percentage') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="service_fee_percentage" class="form-label fw-bold">
                            Service Fee Percentage (%)
                        </label>
                        <div class="input-group">
                            <input 
                                type="number" 
                                id="service_fee_percentage" 
                                name="service_fee_percentage" 
                                value="{{ $currentPercentage }}"
                                min="0"
                                max="100"
                                step="0.01"
                                class="form-control form-control-lg @error('service_fee_percentage') is-invalid @enderror"
                                required
                                onchange="updateCalculation()"
                                oninput="updateCalculation()"
                            >
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            This percentage will be deducted from all new payout requests as a service fee.
                        </small>
                        @error('service_fee_percentage')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">📊 Example Calculation</h5>
                        <p class="mb-2">
                            If a shop requests a payout of £100 with a <span id="examplePercentage" class="fw-bold">{{ $currentPercentage }}</span>% service fee:
                        </p>
                        <ul class="mb-0">
                            <li>• Total Amount: £100.00</li>
                            <li>• Service Fee (<span id="examplePercentage2" class="fw-bold">{{ $currentPercentage }}</span>%): £<span id="exampleFee" class="fw-bold">{{ number_format(100 * ($currentPercentage / 100), 2) }}</span></li>
                            <li>• Amount Paid to Shop: £<span id="exampleAfterFee" class="fw-bold text-success">{{ number_format(100 - (100 * ($currentPercentage / 100)), 2) }}</span></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button 
                            type="submit" 
                            class="btn btn-primary btn-lg"
                        >
                            Save Changes
                        </button>
                        <a 
                            href="{{ route('admin.service-fees.index') }}" 
                            class="btn btn-secondary btn-lg"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-warning mt-4">
            <h5 class="alert-heading">⚠️ Important Notice</h5>
            <p class="mb-0">
                Changing the service fee percentage will only affect <strong>new payout requests</strong>. 
                Existing pending or approved payouts will retain their original fee percentage.
            </p>
        </div>
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
