# eVoucher Food Support Platform

**Northamptonshire Pilot — Built for BAKUP CIC**

A community food support platform connecting local shops, recipients, and organisations to reduce food waste and food poverty across Northamptonshire.

---

## What It Does

Local shops list near-expiry food items. Approved recipients receive digital vouchers by email and through the web app, then redeem them against available food listings. VCFSE groups, schools, and care organisations fund the voucher programme through the platform.

---

## User Roles

| Role | Access |
|------|--------|
| **Super Admin** | Full platform control, system configuration |
| **Admin** | User management, vouchers, reports, fund loads, shop payouts |
| **Local Shop** | List food items, verify vouchers, request payouts |
| **Recipient** | Browse food, redeem vouchers, view history |
| **VCFSE** | Fund vouchers, browse surplus food listings |
| **School / Care** | Fund vouchers, browse surplus food listings |

---

## Tech Stack

- **Backend:** Laravel 10 (PHP 8.2+)
- **Database:** MySQL 8+
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js
- **Payments:** Stripe (pending API key configuration)
- **Email:** Laravel Mail (SMTP configurable)
- **QR Codes:** endroid/qr-code

---

## Local Setup

### Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js 18+ and npm

### Installation

```bash
git clone https://github.com/Niche-Business/evoucher-food-support-platform.git
cd evoucher-food-support-platform
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# Edit .env with your DB credentials
php artisan migrate
php artisan db:seed
php artisan serve
```

Visit http://localhost:8000

---

## Test Credentials (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@evoucher.org | password123 |
| Admin | admin@evoucher.org | password123 |
| Local Shop | shop@evoucher.org | password123 |
| Recipient | recipient@evoucher.org | password123 |
| VCFSE | vcfse@evoucher.org | password123 |
| School/Care | school@evoucher.org | password123 |

---

## Key Features

- **Voucher System** — email delivery, QR code, anti-double-redemption logic
- **Food Listings** — three types: Free, Food to Go (discounted), Free Surplus (VCFSE only)
- **Shop Payout System** — shops submit bank details and payout requests; admin marks as paid with BACS reference
- **Fund Load System** — admin loads wallet funds to VCFSE/School organisations
- **Multi-language** — English and Arabic (extensible)
- **Mobile-first** — responsive design throughout

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes following [Conventional Commits](https://www.conventionalcommits.org/)
4. Push and open a Pull Request against `main`

---

## Pilot Area

**Northamptonshire, United Kingdom** — structured to scale to other UK regions.

---

## Licence

Built for **BAKUP CIC** — Community Interest Company.
Laravel framework is open-sourced under the [MIT licence](https://opensource.org/licenses/MIT).
