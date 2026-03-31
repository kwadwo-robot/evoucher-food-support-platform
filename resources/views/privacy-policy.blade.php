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
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
    <p class="text-gray-600 mb-8">Effective Date: March 2026</p>

    <div class="space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Introduction</h2>
            <p class="text-gray-700 mb-4">BAK UP CIC ("we", "our", "us") is committed to protecting and respecting your privacy in accordance with the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018.</p>
            <p class="text-gray-700">This Privacy Policy explains how we collect, use, store, and protect personal data when you use the BAK UP E-Voucher App.</p>
        </section>

        <section>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Organisation Details</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li><strong>Organisation:</strong> BAK UP CIC</li>
                <li><strong>Registered Address:</strong> Enterprise Centre Warth Park Raunds, NN9 6GR</li>
                <li><strong>Contact Email:</strong> admin@bakupcic.co.uk</li>
                <li><strong>ICO Registration Number:</strong> ZB394154</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Who This Policy Applies To</h2>
            <p class="text-gray-700 mb-3">This policy applies to:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Individuals / Beneficiaries receiving support</li>
                <li>Charitable organisations and referral partners</li>
                <li>Schools and educational institutions</li>
                <li>Shop owners, retailers, and local farmers participating in the scheme</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Personal Data We Collect</h2>
            
            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-4">Individuals / Beneficiaries</h3>
            <p class="text-gray-700 mb-2">We may collect:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Full name</li>
                <li>Address / postcode</li>
                <li>Contact details (phone/email)</li>
                <li>Household composition</li>
                <li>Financial / vulnerability information</li>
                <li>Referral details</li>
                <li>Voucher usage history</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-6">Charitable Organisations / Schools</h3>
            <p class="text-gray-700 mb-2">We may collect:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Organisation name and registration details</li>
                <li>Contact persons</li>
                <li>Email and phone numbers</li>
                <li>Referral records and case notes</li>
                <li>Safeguarding-related information (where applicable)</li>
            </ul>

            <h3 class="text-lg font-bold text-gray-900 mb-3 mt-6">Shop Owners / Local Farmers</h3>
            <p class="text-gray-700 mb-2">We may collect:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Business name and address</li>
                <li>Owner/operator details</li>
                <li>Bank/payment details</li>
                <li>Transaction records</li>
                <li>Product/service categories</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">How We Use Your Data</h2>
            <p class="text-gray-700 mb-3">We use personal data to:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Deliver and manage the E-Voucher scheme</li>
                <li>Verify eligibility and prevent fraud</li>
                <li>Process referrals and allocate support</li>
                <li>Facilitate transactions between users and vendors</li>
                <li>Monitor impact and outcomes</li>
                <li>Improve services and user experience</li>
                <li>Meet legal and safeguarding obligations</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Lawful Basis for Processing</h2>
            <p class="text-gray-700 mb-3">We process data under:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Consent (Article 6(1)(a))</li>
                <li>Contractual necessity (Article 6(1)(b))</li>
                <li>Legal obligation (Article 6(1)(c))</li>
                <li>Legitimate interests (Article 6(1)(f))</li>
                <li>Substantial public interest (for sensitive data, Article 9)</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Data Sharing</h2>
            <p class="text-gray-700 mb-3">We may share data with:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Local authorities and funders</li>
                <li>Partner charities and referral organisations</li>
                <li>Schools and safeguarding bodies</li>
                <li>Payment processors</li>
                <li>Retailers and participating vendors</li>
            </ul>
            <p class="text-gray-700 mt-4"><strong>We will never sell personal data.</strong></p>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Data Retention</h2>
            <p class="text-gray-700 mb-3">We retain data:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Only for as long as necessary</li>
                <li>Typically, between 1–20 years, depending on:
                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                        <li>Funding requirements</li>
                        <li>Legal obligations</li>
                        <li>Safeguarding considerations</li>
                    </ul>
                </li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Data Security</h2>
            <p class="text-gray-700 mb-3">We implement:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Encrypted systems and secure servers</li>
                <li>Access controls and role-based permissions</li>
                <li>Regular security reviews</li>
                <li>Staff training on data protection</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Rights</h2>
            <p class="text-gray-700 mb-3">Under UK GDPR, you have the right to:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Access your data</li>
                <li>Rectify inaccurate data</li>
                <li>Request erasure ("right to be forgotten")</li>
                <li>Restrict processing</li>
                <li>Data portability</li>
                <li>Object to processing</li>
            </ul>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Safeguarding and Sensitive Data</h2>
            <p class="text-gray-700 mb-3">We may process sensitive data (e.g. health, vulnerability) to:</p>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Provide appropriate support</li>
                <li>Ensure safeguarding</li>
                <li>Prevent harm</li>
            </ul>
            <p class="text-gray-700 mt-3">All such data is handled with strict confidentiality.</p>
        </section>

        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Complaints</h2>
            <p class="text-gray-700 mb-3">You can complain to:</p>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="font-bold text-gray-900">Information Commissioner's Office (ICO)</p>
                <p class="text-gray-700">Website: <a href="https://ico.org.uk" class="text-blue-600 hover:text-blue-800 underline" target="_blank">https://ico.org.uk</a></p>
            </div>
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
