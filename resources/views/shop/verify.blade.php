@extends('layouts.dashboard')

@section('title', 'Verify Voucher')

@section('content')
{{-- Page header --}}
<div style="margin-bottom:24px;">
    <h1 style="font-size:22px;font-weight:700;color:#0f172a;margin:0 0 4px;">Verify Voucher</h1>
    <p style="color:#64748b;font-size:14px;margin:0;">Scan a QR code or enter a voucher code manually to verify and accept a customer's voucher.</p>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">&#10003; {{ session('success') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info" style="margin-bottom:20px;">i {{ session('info') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error" style="margin-bottom:20px;">
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
@endif

<div style="display:grid;grid-template-columns:1fr;gap:20px;align-items:start;" class="grid-responsive">

    {{-- LEFT: QR Scanner + Manual Entry --}}
    <div>
        {{-- QR Scanner Card --}}
        <div class="card" style="margin-bottom:20px;" x-data="qrScanner()" x-init="init()">
            <div class="card-hd" style="padding:16px 20px;">
                <span class="card-title">Scan QR Code</span>
                <button class="btn btn-secondary btn-sm" @click="toggleScanner()" x-text="scanning ? 'Stop Scanner' : 'Start Scanner'">Start Scanner</button>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <div id="qr-reader" style="width:100%;border-radius:10px;overflow:hidden;background:#f8fafc;min-height:200px;display:flex;align-items:center;justify-content:center;" x-show="scanning">
                    <p style="color:#94a3b8;font-size:13px;">Initialising camera...</p>
                </div>
                <div x-show="!scanning" style="text-align:center;padding:30px 0;color:#94a3b8;">
                    <div style="font-size:48px;margin-bottom:8px;">&#128247;</div>
                    <p style="font-size:13px;margin:0;">Click <strong>Start Scanner</strong> to activate your camera and scan a voucher QR code.</p>
                </div>
                <div x-show="scannedCode" style="margin-top:12px;" x-cloak>
                    <div class="alert alert-success">QR Code detected: <strong x-text="scannedCode"></strong></div>
                    <a :href="'/shop/verify?code=' + scannedCode" class="btn btn-primary" style="width:100%;justify-content:center;">Look Up This Voucher</a>
                </div>
            </div>
        </div>

        {{-- Manual Entry Card --}}
        <div class="card">
            <div class="card-hd" style="padding:16px 20px;">
                <span class="card-title">Enter Voucher Code Manually</span>
            </div>
            <div class="card-body" style="padding:16px 20px;">
                <form method="POST" action="{{ route('shop.verify.lookup') }}">
                    @csrf
                    <label class="form-label">Voucher Code</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="text"
                               name="code"
                               class="form-input"
                               placeholder="e.g. EV-ABCD1234"
                               value="{{ old('code', $code ?? '') }}"
                               style="flex:1;text-transform:uppercase;letter-spacing:2px;font-weight:700;font-size:16px;padding:12px 14px;border:2px solid #e2e8f0;border-radius:6px;min-height:44px;"
                               autocomplete="off"
                               autofocus>
                        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Look Up</button>
                    </div>
                    <p style="font-size:12px;color:#94a3b8;margin:8px 0 0;">Enter the code exactly as shown on the recipient's voucher (e.g. EV-ABCD1234).</p>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Voucher Details & Accept/Reject --}}
    <div>
        @if($code && !$voucher && !$error)
            <div class="card">
                <div class="card-body" style="text-align:center;padding:40px 20px;color:#94a3b8;">
                    <div style="font-size:48px;margin-bottom:8px;">&#128269;</div>
                    <p>Searching for voucher...</p>
                </div>
            </div>
        @elseif($error && !$voucher)
            <div class="card">
                <div class="card-body" style="padding:20px;">
                    <div class="alert alert-error">{{ $error }}</div>
                    <p style="font-size:13px;color:#64748b;">Please check the voucher code and try again, or ask the recipient to show their voucher details.</p>
                </div>
            </div>
        @elseif($voucher)
            {{-- Voucher Details --}}
            <div class="card" style="margin-bottom:16px;">
                <div class="card-hd" style="padding:16px 20px;">
                    <span class="card-title">Voucher Details</span>
                    @php
                        $statusColour = match($voucher->status) {
                            'active','partially_used' => '#15803d',
                            'redeemed' => '#b45309',
                            'expired'  => '#b91c1c',
                            'cancelled'=> '#6b7280',
                            default    => '#374151',
                        };
                        $statusBg = match($voucher->status) {
                            'active','partially_used' => '#dcfce7',
                            'redeemed' => '#fef9c3',
                            'expired'  => '#fee2e2',
                            'cancelled'=> '#f1f5f9',
                            default    => '#f8fafc',
                        };
                    @endphp
                    <span style="background:{{ $statusBg }};color:{{ $statusColour }};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;">
                        {{ strtoupper(str_replace('_',' ',$voucher->status)) }}
                    </span>
                </div>
                <div class="card-body" style="padding:16px 20px;">
                    <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:8px 0;color:#64748b;font-weight:600;width:40%;">Voucher Code</td>
                            <td style="padding:8px 0;font-weight:700;font-family:monospace;font-size:15px;color:#0f172a;letter-spacing:1px;">{{ $voucher->code }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:8px 0;color:#64748b;font-weight:600;">Recipient</td>
                            <td style="padding:8px 0;font-weight:600;color:#0f172a;">
                                @if($voucher->recipient && $voucher->recipient->recipientProfile)
                                    {{ $voucher->recipient->recipientProfile->full_name }}
                                @elseif($voucher->recipient)
                                    {{ $voucher->recipient->name }}
                                @else
                                    <span style="color:#94a3b8;">Unknown</span>
                                @endif
                            </td>
                        </tr>

                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:8px 0;color:#64748b;font-weight:600;">Balance</td>
                            <td style="padding:8px 0;font-weight:700;color:#0f172a;font-size:16px;">&#163;{{ number_format($voucher->remaining_value, 2) }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:8px 0;color:#64748b;font-weight:600;">Expiry Date</td>
                            <td style="padding:8px 0;color:#0f172a;">
                                {{ $voucher->expiry_date->format('d M Y') }}
                                @if($voucher->expiry_date < now())
                                    <span style="color:#b91c1c;font-size:12px;font-weight:600;"> (EXPIRED)</span>
                                @elseif($voucher->expiry_date <= now()->addDays(7))
                                    <span style="color:#b45309;font-size:12px;font-weight:600;"> (Expires soon)</span>
                                @endif
                            </td>
                        </tr>
                        @if($voucher->notes)

                        @endif
                    </table>
                </div>
            </div>

            {{-- Redemption History --}}
            @if($redemptionHistory->isNotEmpty())
            <div class="card" style="margin-bottom:16px;">
                <div class="card-hd" style="padding:16px 20px;">
                    <span class="card-title">Redemption History</span>
                </div>
                <div class="card-body" style="padding:16px 20px;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                            <tr>
                                <th style="padding:10px;text-align:left;color:#64748b;font-weight:600;">Food Item</th>
                                <th style="padding:10px;text-align:left;color:#64748b;font-weight:600;">Amount Used</th>
                                <th style="padding:10px;text-align:left;color:#64748b;font-weight:600;">Status</th>
                                <th style="padding:10px;text-align:left;color:#64748b;font-weight:600;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redemptionHistory as $redemption)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:10px;color:#0f172a;font-weight:600;">{{ $redemption->foodListing?->item_name ?? 'N/A' }}</td>
                                <td style="padding:10px;color:#16a34a;font-weight:700;">£{{ number_format($redemption->amount_used, 2) }}</td>
                                <td style="padding:10px;">
                                    @php
                                        $statusColor = match($redemption->status) {
                                            'collected' => '#15803d',
                                            'confirmed' => '#0284c7',
                                            'pending' => '#b45309',
                                            default => '#6b7280',
                                        };
                                        $statusBg = match($redemption->status) {
                                            'collected' => '#dcfce7',
                                            'confirmed' => '#cffafe',
                                            'pending' => '#fef9c3',
                                            default => '#f1f5f9',
                                        };
                                    @endphp
                                    <span style="background:{{ $statusBg }};color:{{ $statusColor }};padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase;">
                                        {{ strtoupper(str_replace('_', ' ', $redemption->status)) }}
                                    </span>
                                </td>
                                <td style="padding:10px;color:#64748b;">{{ $redemption->redeemed_at?->format('d M Y H:i') ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($error)
                {{-- Voucher found but has an issue --}}
                <div class="card">
                    <div class="card-body" style="padding:20px;">
                        <div class="alert alert-error">{{ $error }}</div>
                        <p style="font-size:13px;color:#64748b;margin:0;">This voucher cannot be accepted. Please inform the recipient to contact the platform administrator.</p>
                    </div>
                </div>
            @elseif(in_array($voucher->status, ['active','partially_used']) && $voucher->remaining_value > 0)
                {{-- Accept / Reject Form --}}
                <div class="card"
                     x-data="{
                         selectedId: null,
                         paymentMethod: '',
                         voucherBalance: {{ (float)$voucher->remaining_value }},
                         listings: {{ $foodListings->map(fn($l) => ['id'=>$l->id,'name'=>$l->item_name,'value'=>(float)$l->voucher_value])->values()->toJson() }},
                         get selected() { return this.listings.find(l => l.id === this.selectedId) || null; },
                         get voucherCovers() { return this.selected ? Math.min(this.voucherBalance, this.selected.value) : 0; },
                         get owedAtShop() { return this.selected ? Math.max(0, this.selected.value - this.voucherBalance) : 0; }
                     }">
                    <div class="card-hd" style="padding:16px 20px;">
                        <span class="card-title">Select Food Item to Redeem</span>
                    </div>
                    <div class="card-body" style="padding:16px 20px;">
                        @if($foodListings->isEmpty())
                            <div class="alert alert-warning">
                                You have no available food listings at the moment. Please
                                <a href="{{ route('shop.listings.create') }}" style="color:#a16207;font-weight:600;">add a food listing</a> first.
                            </div>
                        @else
                            <form method="POST" action="{{ route('shop.verify.accept') }}" id="acceptForm">
                                @csrf
                                <input type="hidden" name="code" value="{{ $voucher->code }}">
                                <label class="form-label">Choose the food item this voucher is being used for:</label>
                                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
                                    @foreach($foodListings as $listing)
                                    <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:2px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color .15s;"
                                           :style="selectedId === {{ $listing->id }} ? 'border-color:#16a34a;background:#f0fdf4;' : ''"
                                           @click="selectedId = {{ $listing->id }}">
                                        <input type="radio" name="food_listing_id" value="{{ $listing->id }}" required
                                               style="width:18px;height:18px;accent-color:#16a34a;"
                                               @change="selectedId = {{ $listing->id }}">
                                        <div style="flex:1;">
                                            <div style="font-weight:700;font-size:13.5px;color:#0f172a;">{{ $listing->item_name }}</div>
                                            <div style="font-size:12px;color:#64748b;">
                                                Qty: {{ $listing->quantity }} &nbsp;&middot;&nbsp;
                                                Expires: {{ $listing->expiry_date->format('d M Y') }} &nbsp;&middot;&nbsp;
                                                @if($listing->voucher_value > 0)
                                                    Cost: <strong style="color:#16a34a;">&#163;{{ number_format($listing->voucher_value, 2) }}</strong>
                                                @else
                                                    <strong style="color:#16a34a;">Free</strong>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>

                                {{-- Payment summary shown when a listing is selected --}}
                                <template x-if="selected !== null">
                                    <div>
                                        {{-- Cost breakdown --}}
                                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:12px;">
                                            <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Payment Summary</div>
                                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                                                <span style="color:#64748b;">Item Cost</span>
                                                <span style="font-weight:600;" x-text="'&#163;' + (selected ? selected.value.toFixed(2) : '0.00')"></span>
                                            </div>
                                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                                                <span style="color:#64748b;">Voucher Covers</span>
                                                <span style="font-weight:600;color:#16a34a;" x-text="'&#163;' + voucherCovers.toFixed(2)"></span>
                                            </div>
                                            <div style="border-top:1px solid #e2e8f0;margin:8px 0;"></div>
                                            <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
                                                <span>Customer Pays at Shop</span>
                                                <span x-text="'&#163;' + owedAtShop.toFixed(2)"
                                                      :style="owedAtShop > 0 ? 'color:#b45309;' : 'color:#16a34a;'"></span>
                                            </div>
                                        </div>

                                        {{-- Payment method required only if customer owes money --}}
                                        <template x-if="owedAtShop > 0">
                                            <div style="margin-bottom:12px;">
                                                <label class="form-label">Payment Method Received <span style="color:#ef4444;">*</span></label>
                                                <select name="payment_method" class="form-select" x-model="paymentMethod" required>
                                                    <option value="">-- Select payment method --</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="card">Card</option>
                                                    <option value="contactless">Contactless</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                </select>
                                                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Confirm you have collected the outstanding amount from the customer before accepting.</p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div style="display:flex;gap:10px;">
                                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:12px;">
                                        Accept &amp; Mark as Collected
                                    </button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('shop.verify.reject') }}" style="margin-top:10px;">
                                @csrf
                                <input type="hidden" name="code" value="{{ $voucher->code }}">
                                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;padding:12px;"
                                        onclick="return confirm('Are you sure you want to reject this voucher? No changes will be made.')">
                                    Reject Voucher
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Direct Voucher Redemption --}}
                <div class="card" style="margin-top:20px;">
                    <div class="card-hd" style="padding:16px 20px;background:#f0fdf4;border-bottom:2px solid #dcfce7;">
                        <span class="card-title" style="color:#15803d;">Or Redeem Voucher Directly</span>
                        <p style="font-size:12px;color:#64748b;margin:4px 0 0;">Redeem voucher without selecting a specific food item</p>
                    </div>
                    <div class="card-body" style="padding:16px 20px;">
                        <form method="POST" action="{{ route('shop.verify.accept-direct') }}" id="directRedeemForm"
                              x-data="{
                                  voucherBalance: {{ (float)$voucher->remaining_value }},
                                  redemptionAmount: 0,
                                  topUpAmount: 0,
                                  paymentMethod: '',
                                  get totalAmount() { return this.redemptionAmount + this.topUpAmount; },
                                  get isValid() { return this.redemptionAmount > 0 && this.redemptionAmount <= this.voucherBalance; }
                              }">
                            @csrf
                            <input type="hidden" name="code" value="{{ $voucher->code }}">

                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:16px;">
                                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Voucher Details</div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;">
                                    <span style="color:#64748b;">Available Balance</span>
                                    <span style="font-weight:600;color:#16a34a;" x-text="'£' + {{ (float)$voucher->remaining_value }}.toFixed(2)"></span>
                                </div>
                            </div>

                            <div style="margin-bottom:16px;">
                                <label class="form-label">Amount to Redeem from Voucher <span style="color:#ef4444;">*</span></label>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="font-weight:600;font-size:16px;">£</span>
                                    <input type="number" name="redemption_amount" class="form-control" 
                                           placeholder="0.00" step="0.01" min="0.01" max="{{ $voucher->remaining_value }}"
                                           x-model.number="redemptionAmount" required
                                           style="font-size:16px;padding:12px 14px;">
                                </div>
                                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Enter the amount from the voucher to use (max: £{{ number_format($voucher->remaining_value, 2) }})</p>
                            </div>

                            <div style="background:#fef9c3;border:1px solid #fcd34d;border-radius:10px;padding:12px;margin-bottom:16px;" x-show="topUpAmount > 0">
                                <div style="font-size:12px;font-weight:700;color:#b45309;margin-bottom:10px;">Customer Top-up Payment</div>
                                <div style="margin-bottom:12px;">
                                    <label class="form-label">Top-up Amount</label>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span style="font-weight:600;font-size:16px;">£</span>
                                        <input type="number" name="top_up_amount" class="form-control" 
                                               placeholder="0.00" step="0.01" min="0" 
                                               x-model.number="topUpAmount"
                                               style="font-size:16px;padding:12px 14px;">
                                    </div>
                                    <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Additional amount customer pays in cash or other method</p>
                                </div>

                                <div style="margin-bottom:12px;">
                                    <label class="form-label">Payment Method Received <span style="color:#ef4444;">*</span></label>
                                    <select name="payment_method" class="form-select" x-model="paymentMethod"
                                            :required="topUpAmount > 0">
                                        <option value="">-- Select payment method --</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="contactless">Contactless</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>

                            <div style="background:#f0fdf4;border:1px solid #dcfce7;border-radius:10px;padding:14px;margin-bottom:16px;">
                                <div style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Summary</div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;">
                                    <span style="color:#64748b;">Voucher Used</span>
                                    <span style="font-weight:600;color:#16a34a;" x-text="'£' + (redemptionAmount || 0).toFixed(2)"></span>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px;" x-show="topUpAmount > 0">
                                    <span style="color:#64748b;">Top-up Collected</span>
                                    <span style="font-weight:600;color:#b45309;" x-text="'£' + (topUpAmount || 0).toFixed(2)"></span>
                                </div>
                                <div style="border-top:1px solid #dcfce7;margin:8px 0;"></div>
                                <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
                                    <span>Total Transaction</span>
                                    <span x-text="'£' + (totalAmount || 0).toFixed(2)" style="color:#16a34a;"></span>
                                </div>
                            </div>

                            <div style="display:flex;gap:10px;">
                                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;padding:12px;" :disabled="!isValid">
                                    Redeem Voucher Directly
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body" style="padding:20px;">
                        <div class="alert alert-warning">This voucher cannot be accepted &mdash; its current status is <strong>{{ strtoupper(str_replace('_',' ',$voucher->status)) }}</strong>.</div>
                    </div>
                </div>
            @endif
        @else
            {{-- Default empty state --}}
            <div class="card">
                <div class="card-body" style="text-align:center;padding:50px 20px;color:#94a3b8;">
                    <div style="font-size:56px;margin-bottom:12px;">&#127915;</div>
                    <h3 style="font-size:16px;font-weight:700;color:#374151;margin:0 0 6px;">No Voucher Loaded</h3>
                    <p style="font-size:13px;margin:0;">Scan a QR code or enter a voucher code on the left to see the voucher details here.</p>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- QR Scanner JS (using html5-qrcode CDN) --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner() {
    return {
        scanning: false,
        scannedCode: null,
        scanner: null,

        init() {},

        toggleScanner() {
            if (this.scanning) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        startScanner() {
            this.scanning = true;
            this.scannedCode = null;
            this.$nextTick(() => {
                this.scanner = new Html5Qrcode("qr-reader");
                this.scanner.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        let code = decodedText;
                        const urlMatch = decodedText.match(/code=([A-Z0-9\-]+)/i);
                        if (urlMatch) code = urlMatch[1];
                        this.scannedCode = code.toUpperCase();
                        this.stopScanner();
                    },
                    (error) => {}
                ).catch(err => {
                    this.scanning = false;
                    alert('Camera access denied or not available. Please use manual entry.');
                });
            });
        },

        stopScanner() {
            this.scanning = false;
            if (this.scanner) {
                this.scanner.stop().catch(() => {});
                this.scanner = null;
            }
        }
    };
}
</script>
<style>
@media (min-width: 768px) {
    .grid-responsive { grid-template-columns: 1fr 1fr; }
}
</style>
@endsection
