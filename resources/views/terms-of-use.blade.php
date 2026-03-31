@extends('layouts.app')
@section('content')
<style>
/* Footer */
.footer{background:linear-gradient(135deg,#1a5fa0 0%,#1a5fa0 100%);padding:60px 40px 32px;border-top:1px solid rgba(255,255,255,.07);width:100vw;position:relative;left:50%;right:50%;margin-left:-50vw;margin-right:-50vw}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;max-width:1100px;margin:0 auto 40px;padding:0 40px}
.footer-logo{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.footer-logo-img{width:36px;height:36px;object-fit:contain}
.footer-logo-text{font-size:16px;font-weight:800;color:#fff}
.footer-tagline{font-size:13.5px;color:rgba(255,255,255,.6);line-height:1.7;max-width:280px}
.footer-col-title{font-size:12px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px}
.footer-link{display:block;font-size:13.5px;color:rgba(255,255,255,.6);text-decoration:none;margin-bottom:10px;transition:color .15s}
.footer-link:hover{color:#4ade80}
.footer-bottom{border-top:1px solid rgba(255,255,255,.1);padding-top:24px;padding-left:40px;padding-right:40px;max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.footer-copy{font-size:12.5px;color:rgba(255,255,255,.4)}
.footer-badges{display:flex;gap:12px;align-items:center}
.footer-badge{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px}
</style>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Terms of Use</h1>
    <p class="text-gray-600 mb-8">Effective Date: March 2026</p>

    <div class="space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
            <p class="text-gray-700">By registering or using the BAK UP E-Voucher App, you agree to these Terms of Use.</p>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Eligibility</h2>
            
            <h3 class="text-lg font-bold text-gray-900 mb-3">Individuals</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Must be referred or meet eligibility criteria</li>
                <li>Must provide accurate information</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-4">Organisations / Schools</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Must be registered and approved by BAK UP CIC</li>
                <li>Must comply with safeguarding and referral standards</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-4">Shop Owners / Farmers</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Must operate a legitimate business</li>
                <li>Must agree to fair pricing, and free food donations to Charitable organisations and ethical practices</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Account Responsibilities</h2>
            <p class="text-gray-700 mb-3">Users must:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Provide accurate and up-to-date information</li>
                <li>Keep login credentials secure</li>
                <li>Notify us of any unauthorised use</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Use of E-Vouchers</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Vouchers are non-transferable unless authorised</li>
                <li>Cannot be exchanged for cash</li>
                <li>Must be used within the validity period</li>
                <li>Can only be used with approved vendors</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Responsibilities by User Type</h2>
            
            <h3 class="text-lg font-bold text-gray-900 mb-3">5.1 Individuals / Beneficiaries</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Use vouchers for intended purposes only</li>
                <li>Do not attempt fraud or misuse</li>
                <li>Engage respectfully with vendors</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-4">5.2 Charitable Organisations / Schools</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Ensure accurate referrals</li>
                <li>Maintain confidentiality</li>
                <li>Safeguard vulnerable individuals</li>
                <li>Monitor appropriate use of support</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-4">5.3 Shop Owners / Local Farmers</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Accept valid vouchers</li>
                <li>Provide agreed goods/services</li>
                <li>Maintain fair pricing</li>
                <li>Submit accurate redemption claims</li>
                <li>Comply with food safety and trading regulations</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Payments and Reimbursements</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Vendors will be reimbursed based on verified transactions</li>
                <li>BAK UP CIC reserves the right to audit claims</li>
                <li>Fraudulent claims will result in suspension and possible legal action</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Prohibited Activities</h2>
            <p class="text-gray-700 mb-3">Users must not:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Commit fraud or misuse vouchers</li>
                <li>Provide false information</li>
                <li>Exploit vulnerable individuals</li>
                <li>Resell voucher-funded goods</li>
                <li>Attempt to hack or disrupt the system</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Suspension and Termination</h2>
            <p class="text-gray-700 mb-3">We may suspend or terminate accounts if:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Terms are breached</li>
                <li>Fraud is suspected</li>
                <li>Safeguarding concerns arise</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Limitation of Liability</h2>
            <p class="text-gray-700 mb-3">BAK UP CIC is not liable for:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Loss arising from misuse of vouchers</li>
                <li>Vendor disputes (though we will mediate where possible)</li>
                <li>Service interruptions beyond our control</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Intellectual Property</h2>
            <p class="text-gray-700">All platform content belongs to BAK UP CIC and may not be copied or reproduced without permission.</p>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Changes to Terms</h2>
            <p class="text-gray-700">We may update these Terms periodically. Users will be notified of significant changes.</p>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Governing Law</h2>
            <p class="text-gray-700">These Terms are governed by the laws of England and Wales.</p>
        </section>
    </div>
</div>
</div>
<footer class="footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo">
        <img src="{{ asset("images/logo.png") }}" alt="eVoucher Logo" class="footer-logo-img">
        <div class="footer-logo-text">eVoucher</div>
      </div>
      <div class="footer-tagline">{{ __("app.footer_tagline") }}</div>
    </div>
    <div>
      <div class="footer-col-title">{{ __("app.platform") }}</div>
      <a href="{{ url("/food") }}" class="footer-link">{{ __("app.browse_food") }}</a>
      <a href="{{ url("/shops") }}" class="footer-link">{{ __("app.shops") }}</a>
      <a href="{{ route("login") }}" class="footer-link">{{ __("app.sign_in") }}</a>
    </div>
    <div>
      <div class="footer-col-title">{{ __("app.company") }}</div>
      <a href="#" class="footer-link">{{ __("app.about_us") }}</a>
      <a href="#" class="footer-link">{{ __("app.contact") }}</a>
      <a href="#" class="footer-link">{{ __("app.volunteer") }}</a>
    </div>
    <div>
      <div class="footer-col-title">{{ __("app.legal") }}</div>
      <a href="/privacy-policy" class="footer-link">{{ __("app.privacy_policy") }}</a>
      <a href="/terms-of-use" class="footer-link">{{ __("app.terms_of_use") }}</a>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-copy">{{ __("app.footer_copy", ["year" => date("Y")]) }}</div>
    <div class="footer-badges">
      <div class="footer-badge">Northamptonshire Pilot</div>
      <div class="footer-badge">BAKUP CIC</div>
    </div>
  </div>
</footer>
@endsection
