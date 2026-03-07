
## UI Redesign — SaaS Professional Dashboard
- [ ] Set up SaaS design system with Tailwind CDN, Inter font, Chart.js, and custom CSS variables
- [ ] Build new sidebar layout component for all authenticated dashboards
- [ ] Redesign landing page with hero, features, stats, and CTA sections
- [ ] Redesign login and register pages with split-panel SaaS style
- [ ] Redesign Admin/Super Admin dashboard with sidebar, stat cards, charts, and data tables
- [ ] Redesign Admin Users, Vouchers, Food Listings, Reports pages
- [ ] Redesign Local Shop dashboard with sidebar, stat cards, and listing management
- [ ] Redesign Recipient dashboard with voucher card, food grid, and redemption history
- [ ] Redesign VCFSE dashboard with donation stats and history
- [ ] Redesign School/Care dashboard with donation stats and history
- [ ] Redesign donation page with Stripe card element
- [ ] Redesign food browse page (public) with card grid and filters
- [ ] Ensure mobile-first responsive design across all pages

## Bug Fixes Found During Testing (Phase 2)
- [ ] Fix admin donations page — controller calls view('admin.donations') but view is at admin/payments/index.blade.php
- [ ] Fix admin dashboard stats keys mismatch (controller sends different keys vs what view expects)
- [ ] Test and fix admin vouchers page (create/list/show)
- [ ] Test and fix admin users page (view/approve/deactivate)
- [ ] Test and fix admin food listings page
- [ ] Test and fix admin reports page
- [ ] Test and fix shop dashboard
- [ ] Test and fix shop listings (create/edit/delete)
- [ ] Test and fix shop redemptions page
- [ ] Test and fix recipient dashboard
- [ ] Test and fix recipient food browse
- [ ] Test and fix recipient vouchers page
- [ ] Test and fix recipient history page
- [ ] Test and fix VCFSE dashboard and donate/donations pages
- [ ] Test and fix School/Care dashboard and donate/donations pages
- [ ] Fix blank registration form for non-recipient roles (known issue)

## Sprint 2 — Bug Fixes & New Features

- [ ] Fix shop dashboard 404 error
- [ ] Build voucher verification page: manual code entry + QR code scanner
- [ ] Show full voucher details (recipient name, food item, value, expiry) before confirming
- [ ] Add Accept / Reject buttons on verification page
- [ ] Mark redemption as collected only after shop explicitly accepts
- [ ] Fix empty confirm redemption page

## Sprint 3 — Shop Payout System

- [x] Create shop_bank_details table (account name, sort code, account number, bank name)
- [x] Create shop_payout_requests table (shop, amount, status, admin notes, paid_at)
- [x] Add bank details form to shop profile settings
- [x] Add payout request page for shops (shows redeemed items with amounts owed)
- [x] Add payout request submission (shop selects unpaid redemptions and requests payout)
- [x] Build admin payout management panel (list all payout requests, mark as paid)
- [x] Admin can add payment reference and notes when marking as paid
- [x] Update shop redemption history to show payout status per item
- [x] Show total amount owed and total paid on shop dashboard
- [ ] Email notification to shop when payout is marked as paid by admin
