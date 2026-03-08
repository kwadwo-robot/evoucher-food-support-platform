@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary mb-3">← Back to Donations</a>
            <h1 class="h3">Donation #{{ $donation->id }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Donation Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Donor Email</label>
                            <p class="h6">{{ $donation->donor_email ?? $donation->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Amount</label>
                            <p class="h6">£{{ number_format($donation->amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Status</label>
                            <p>
                                @if($donation->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($donation->status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($donation->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($donation->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Currency</label>
                            <p class="h6">{{ $donation->currency ?? 'GBP' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted">Date Created</label>
                            <p class="h6">{{ $donation->created_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted">Stripe Payment ID</label>
                            <p class="h6 text-break">{{ $donation->stripe_payment_id ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted">Payment Intent ID</label>
                            <p class="h6 text-break">{{ $donation->payment_intent_id ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted">Payment Method ID</label>
                            <p class="h6 text-break">{{ $donation->payment_method_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($donation->notes)
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <pre class="mb-0">{{ json_encode(json_decode($donation->notes), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Donation ID</small>
                        <p class="h6">#{{ $donation->id }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Amount</small>
                        <p class="h5 text-success">£{{ number_format($donation->amount, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p>
                            @if($donation->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($donation->status === 'processing')
                                <span class="badge bg-info">Processing</span>
                            @elseif($donation->status === 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($donation->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Date</small>
                        <p class="h6">{{ $donation->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
