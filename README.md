# Laladia — Laravel eCommerce Platform

A production-ready, full-featured eCommerce platform built on **Laravel 12**. Ships a customer-facing storefront, a public JSON API, a full admin dashboard, and a rich event-driven backend — all in one repository.

---

## Table of Contents

1. [Tech Stack](#tech-stack)
2. [Feature Overview](#feature-overview)
3. [Architecture](#architecture)
4. [Project Structure](#project-structure)
5. [Database Schema](#database-schema)
6. [Requirements](#requirements)
7. [Local Setup](#local-setup)
8. [Running the App](#running-the-app)
9. [Environment Reference](#environment-reference)
10. [Routes Overview](#routes-overview)
11. [Domain Flow](#domain-flow)
12. [Events & Notifications](#events--notifications)
13. [Admin Panel](#admin-panel)
14. [Testing & Quality](#testing--quality)
15. [Deployment](#deployment)
16. [Troubleshooting](#troubleshooting)
17. [License](#license)

---

## Tech Stack

### Backend

| Package                             | Version | Purpose                          |
| ----------------------------------- | ------- | -------------------------------- |
| Laravel Framework                   | `^12.0` | Core framework                   |
| PHP                                 | `^8.2`  | Runtime                          |
| Laravel Sanctum                     | `^4.3`  | API token authentication         |
| Spatie Laravel Permission           | `^7.2`  | Role-based access control (RBAC) |
| Spatie Laravel Activity Log         | `^4.12` | Admin audit trail                |
| Mews Purifier                       | `^3.4`  | HTML sanitization                |
| Laravel Notification Channels (FCM) | `^6.0`  | Firebase push notifications      |

### Frontend

| Tool               | Version | Purpose                    |
| ------------------ | ------- | -------------------------- |
| Vite               | `^7`    | Asset bundler              |
| Tailwind CSS       | `^4`    | Utility-first styling      |
| Axios              | latest  | HTTP client for JS modules |
| Vanilla JS modules | —       | Cart, auth, filter, search |

### Infrastructure (default `.env.example`)

| Concern | Default Driver |
| ------- | -------------- |
| Session | `database`     |
| Queue   | `database`     |
| Cache   | `database`     |
| Mail    | `log` (local)  |

---

## Feature Overview

### Storefront

- Product catalog with variants, tier pricing, and combo bundles
- Category and slug-based navigation
- Full-text product search
- Custom sales landing pages with direct checkout
- Guest and authenticated cart with automatic merge after login
- Coupon code validation with real-time pricing preview
- Zone-based shipping rate calculation
- Checkout → Order pipeline with immutable line-item snapshots
- Order success/failure pages

### Customer Account

- Dashboard with order history
- Order detail view
- Profile management
- Referral/commission tracking

### API (`/api/v1/*`)

- Full RESTful JSON API for all storefront actions
- Sanctum token authentication
- Guest cart support via session
- Checkout preview endpoint for authoritative total calculation

### Admin Dashboard

- Manage products, variants, tier prices, combos, certifications
- Category management
- Order management with status pipeline
- Customer management
- Coupon creation and usage reports
- Shipping zone configuration
- Landing page builder
- Hero banner management
- Webhook configuration
- Notification dispatch controls
- Transaction / payment records
- Role and permission management (RBAC)
- Activity log / audit trail

### Notifications

- SMS (configurable provider)
- Email (Laravel Mail)
- WhatsApp
- Firebase Cloud Messaging (FCM) push notifications

### Integrations

- Courier/shipment tracking
- Webhook system for third-party integrations
- Event dispatching for customer registration, order events, shipment updates

---

## Architecture

The application follows a **Domain-Driven Design (DDD)** structure. Each feature lives in its own domain under `app/Domains/`, with a consistent internal layout:

```
Domain/
  Controllers/
  Models/
  Services/
  Resources/       # API response serializers
  Requests/        # Form request validation
  Repositories/    # Optional repository layer
```

Cross-cutting concerns (base classes, infrastructure clients) live outside the domains:

```
app/Core/           # BaseController, BaseRepository, BaseService
app/Infrastructure/ # Courier, SMS, WhatsApp, Webhook, FCM clients
app/Events/         # Application-level events
app/Listeners/      # Event listeners
app/Jobs/           # Queued jobs
```

---

## Project Structure

```text
BionicProject/
├── app/
│   ├── Console/                # Artisan commands
│   ├── Core/                   # Base classes
│   ├── Domains/                # 19 feature domains (see below)
│   ├── Events/                 # OrderCreated, CustomerRegistered, etc.
│   ├── Http/                   # Shared middleware, global controllers
│   ├── Infrastructure/         # External service clients
│   ├── Jobs/                   # Queue jobs (4)
│   ├── Listeners/              # Event listeners (16)
│   ├── Mail/                   # Mailable classes
│   ├── Models/                 # Eloquent models
│   ├── Notifications/          # Notification classes
│   ├── Policies/               # Authorization policies
│   ├── Providers/              # Service providers
│   └── Helpers/                # format.php, flash.php
├── config/                     # 19 configuration files
├── database/
│   ├── factories/
│   ├── migrations/             # 38 migrations
│   └── seeders/
├── resources/
│   ├── css/                    # Tailwind entry
│   ├── js/                     # app.js (storefront), admin.js (dashboard)
│   └── views/                  # Blade templates
│       ├── admin/
│       ├── store/
│       ├── auth/
│       ├── customer/
│       ├── landing/
│       └── emails/
├── routes/
│   ├── web.php                 # Storefront + admin Blade routes
│   ├── api.php                 # API entrypoint (/api/v1/*)
│   ├── public.php              # Unauthenticated API routes
│   └── admin.php               # Admin-only API routes
└── tests/
```

### Domains

| Domain          | Responsibility                                                       |
| --------------- | -------------------------------------------------------------------- |
| `Auth`          | Registration, login, password reset, Sanctum token management        |
| `Cart`          | Guest/auth cart, item CRUD, combo support, cart merge                |
| `Category`      | Product categorization and catalog hierarchy                         |
| `Product`       | Catalog, variants, tier pricing, related products, recommendations   |
| `Order`         | Order lifecycle, status pipeline, line-item snapshots                |
| `Checkout`      | Cart → Order pipeline, pricing preview, coupon & shipping resolution |
| `Shipping`      | Zone-based shipping rates                                            |
| `Coupon`        | Discount codes, validation, usage tracking, expiry                   |
| `Customer`      | Dashboard, profile, order history, referral system                   |
| `Landing`       | Custom landing pages with embedded checkout                          |
| `Admin`         | Administrative controllers for all entities                          |
| `Notification`  | Multi-channel dispatch (SMS, Email, WhatsApp, FCM)                   |
| `Certification` | Product compliance certifications                                    |
| `Courier`       | Shipment tracking and third-party courier integration                |
| `Webhook`       | Outbound webhook management                                          |
| `ActivityLog`   | Admin audit log                                                      |
| `Marketing`     | Referrals and commissions                                            |
| `Intelligence`  | Product recommendations and search                                   |
| `Store`         | Core catalog operations shared across domains                        |

---

## Database Schema

38 migrations covering the following groups:

**Auth & Access**
`users`, `personal_access_tokens`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

**Catalog**
`categories`, `products`, `product_variants`, `product_tier_prices`, `product_relations`, `combos`, `combo_items`, `certifications`, `certification_product`

**Commerce**
`carts`, `cart_items`, `orders`, `order_items`, `order_addresses`, `order_notes`, `order_transactions`, `coupons`, `coupon_usages`, `shipping_zones`

**Marketing & Content**
`hero_banners`, `landing_pages`, `landing_page_items`, `media_videos`, `social_proofs`, `commissions`

**Operations**
`courier_shipments`, `device_tokens`, `webhooks`, `notifications`, `activity_log`, `settings`

**Infrastructure**
`jobs`, `cache`, `sessions`

---

## Requirements

- PHP 8.2+
- Composer 2+
- Node.js 18+
- NPM 9+
- SQLite, MySQL, or PostgreSQL

---

## Local Setup

### 1. Clone

```bash
git clone <your-repo-url> laladia
cd laladia
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database connection, mail driver, and any third-party credentials (SMS, WhatsApp, FCM).

### 4. Run migrations (and optional seeders)

```bash
php artisan migrate
# php artisan db:seed   # if seeders are available
```

### 5. Build frontend assets

```bash
npm run build
```

---

## Running the App

### Option A — Separate terminals

```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan queue:listen --tries=1 --timeout=0

# Terminal 3 (hot reload)
npm run dev
```

### Option B — Combined (recommended for local dev)

```bash
composer run dev
```

Starts the Laravel server, queue listener, log stream (`pail`), and Vite dev server concurrently.

---

## Environment Reference

Key variables in `.env.example`:

```dotenv
APP_ENV=local
APP_DEBUG=true

# Database (SQLite default, change to mysql/pgsql as needed)
DB_CONNECTION=sqlite

# Drivers
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Mail (logs locally, configure SMTP for production)
MAIL_MAILER=log

# Admin contacts
ADMIN_PHONE=
ADMIN_EMAIL=

# Third-party (fill in for notifications)
# SMS provider credentials
# WhatsApp credentials
# Firebase (FCM) credentials
```

> **Note:** The `jobs`, `cache`, and `sessions` tables are created by migrations — no extra setup needed for the database queue/session/cache drivers.

---

## Routes Overview

### Storefront — Web (`routes/web.php`)

| Method | URI                       | Description               |
| ------ | ------------------------- | ------------------------- |
| GET    | `/`                       | Home page                 |
| GET    | `/products`               | Product listing           |
| GET    | `/product/{slug}`         | Product detail            |
| GET    | `/category/{slug}`        | Category listing          |
| GET    | `/combos`                 | Combo listing             |
| GET    | `/combos/{slug}`          | Combo detail              |
| GET    | `/cart`                   | Cart page                 |
| GET    | `/checkout`               | Checkout page             |
| GET    | `/order-success/{order}`  | Order success             |
| GET    | `/order-failed`           | Order failed              |
| GET    | `/login`                  | Login page                |
| GET    | `/register`               | Register page             |
| GET    | `/forgot-password`        | Password reset request    |
| GET    | `/password/reset/{token}` | Password reset form       |
| GET    | `/account/dashboard`      | Customer dashboard (auth) |
| GET    | `/account/orders`         | Order list (auth)         |
| GET    | `/account/orders/{order}` | Order detail (auth)       |
| GET    | `/account/profile`        | Profile (auth)            |
| `*`    | `/admin/*`                | Admin Blade pages         |

### Public API (`/api/v1/*` — `routes/public.php`)

**Auth**

| Method | URI         | Auth  |
| ------ | ----------- | ----- |
| POST   | `/register` | —     |
| POST   | `/login`    | —     |
| POST   | `/logout`   | Token |
| GET    | `/me`       | Token |

**Products**

| Method | URI                              | Auth |
| ------ | -------------------------------- | ---- |
| GET    | `/products`                      | —    |
| GET    | `/products/{slug}`               | —    |
| GET    | `/products/{id}/recommendations` | —    |
| GET    | `/products/search`               | —    |

**Cart**

| Method | URI               | Auth |
| ------ | ----------------- | ---- |
| GET    | `/cart`           | —    |
| POST   | `/cart/add`       | —    |
| POST   | `/cart/add-combo` | —    |
| POST   | `/cart/update`    | —    |
| POST   | `/cart/remove`    | —    |
| DELETE | `/cart/clear`     | —    |

**Checkout**

| Method | URI                 | Auth |
| ------ | ------------------- | ---- |
| POST   | `/checkout/preview` | —    |
| POST   | `/checkout`         | —    |

**Misc**

| Method | URI                        |
| ------ | -------------------------- |
| POST   | `/coupon/validate`         |
| GET    | `/shipping-zones`          |
| GET    | `/landing/{slug}`          |
| POST   | `/landing/{slug}/preview`  |
| POST   | `/landing/{slug}/checkout` |

---

## Domain Flow

```
[Product Catalog]
      │
      ▼
[Cart] ──────────────── guest or authenticated
      │                  cart merge on login
      ▼
[Checkout Preview] ──── server-side totals
      │                  coupon + tier discounts
      │                  zone-based shipping
      ▼
[Order Created] ─────── immutable line snapshots
      │                  coupon usage recorded
      │
      ├──► [Events dispatched]
      │         │
      │         ├──► SMS listener
      │         ├──► Email listener
      │         ├──► WhatsApp listener
      │         ├──► Referral commission listener
      │         └──► Webhook listener
      │
      └──► [Admin order management]
                │
                └──► Courier / shipment tracking
```

---

## Events & Notifications

### Events

| Event                   | Triggered By          |
| ----------------------- | --------------------- |
| `OrderCreated`          | Successful checkout   |
| `OrderPaymentUpdated`   | Payment status change |
| `OrderStatusChanged`    | Admin status update   |
| `ShipmentStatusUpdated` | Courier callback      |
| `CustomerRegistered`    | New registration      |
| `CouponExpired`         | Coupon expiry         |

### Listeners (16 total)

Each event fans out to multiple listeners — dispatching notifications (SMS, Email, WhatsApp, FCM), creating commission records, posting to webhooks, and notifying admins.

Queue workers **must** be running in staging/production environments:

```bash
php artisan queue:work --tries=3
```

---

## Admin Panel

Accessible at `/admin/*` (Blade-rendered, protected by role/permission middleware).

| Section        | Capability                                   |
| -------------- | -------------------------------------------- |
| Products       | CRUD, variants, tier pricing, certifications |
| Combos         | Bundle management                            |
| Categories     | Tree management                              |
| Orders         | Status pipeline, notes, transaction records  |
| Customers      | Customer list, detail, commission records    |
| Coupons        | Create/expire, usage stats                   |
| Shipping Zones | Zone CRUD with rates                         |
| Landing Pages  | Page builder with items                      |
| Hero Banners   | Storefront banner management                 |
| Webhooks       | Endpoint config, event binding               |
| Notifications  | Manual dispatch to segments                  |
| Transactions   | Payment records                              |
| Access Control | Roles, permissions (Spatie RBAC)             |
| Activity Log   | Full audit trail of admin actions            |
| Settings       | System-level settings                        |

---

## Testing & Quality

```bash
# PHPUnit test suite
php artisan test

# Laravel Pint (code style)
./vendor/bin/pint

# Vite build check
npm run build
```

---

## Deployment

### Checklist

```bash
# Environment
APP_ENV=production
APP_DEBUG=false

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage symlink
php artisan storage:link

# Run migrations
php artisan migrate --force

# Build frontend
npm run build
```

### Queue Workers

Use Supervisor or systemd to keep workers alive:

```bash
php artisan queue:work --tries=3 --timeout=60
```

### Recommended Production Upgrades

- Switch `QUEUE_CONNECTION` to `redis` for better throughput
- Switch `CACHE_STORE` to `redis`
- Configure a real mail provider (Mailgun, SES, Postmark)
- Fill in SMS / WhatsApp / FCM credentials in `.env`
- Set up Supervisor for queue workers

---

## Troubleshooting

**Autoload / class not found**

```bash
composer dump-autoload
```

**Frontend not reflecting changes**

```bash
npm run dev   # hot reload
# or
npm run build
```

**Queue jobs not processing**

```bash
php artisan queue:listen
```

**Session or cart behavior inconsistent**

```bash
# Verify SESSION_DRIVER and DB migrations, then clear caches
php artisan optimize:clear
```

**Permission denied errors**

```bash
chmod -R 775 storage bootstrap/cache
```

---

## License

This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
