@extends('layouts.dashboard')
@section('title', 'Issue Voucher')

@section('content')
<div class="page-header">
    <div class="page-title">Issue New Voucher</div>
    <div class="page-desc">Create and assign a voucher to an approved recipient</div>
</div>

<div style="max-width:600px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-ticket" style="color:#16a34a;margin-right:8px;"></i>Voucher Details</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vouchers.store') }}">
                @csrf
                <div style="display:grid;gap:18px;">
                    <div>
                        <label class="form-label">Recipient <span style="color:#ef4444;">*</span></label>
                        <select name="recipient_user_id" class="form-select" required>
                            <option value="">— Select Recipient —</option>
                            @foreach($recipients as $recipient)
                            <option value="{{ $recipient->id }}" {{ old('recipient_user_id') == $recipient->id ? 'selected' : '' }}>
                                {{ $recipient->name }} ({{ $recipient->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('recipient_id') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Voucher Amount (&pound;) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="value" value="{{ old('value', '10.00') }}" step="0.01" min="1" max="500" class="form-input" required>
                        @error('amount') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', now()->addDays(30)->format('Y-m-d')) }}" class="form-input">
                        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">Leave blank for no expiry. Default is 30 days from today.</div>
                    </div>
                    <div>
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="form-textarea" placeholder="Internal notes about this voucher...">{{ old('notes') }}</textarea>
                    </div>
                    <div style="display:flex;gap:12px;padding-top:4px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Issue Voucher
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
