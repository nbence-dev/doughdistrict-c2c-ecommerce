# DoughDistrict

DoughDistrict is a consumer-to-consumer (C2C) e-commerce platform for home-baked goods in South Africa. Home bakers list their products for sale, buyers browse and purchase via Stripe, and sellers fulfil orders with courier integration through The Courier Guy. Built as a university MVP deliverable (Eduvos ITECA3-B12).

## Features

**Buyer**
- Browse, search, and filter products by category
- View product detail pages with images, pricing, and seller information
- Session-based shopping cart (add, update quantity, remove items)
- Checkout with Stripe payment (per-seller orders)
- View order history and tracking numbers
- Leave star ratings and reviews on delivered products

**Seller**
- Self-service shop onboarding (any buyer can upgrade their account)
- Product CRUD with image upload to Cloudflare R2
- Connect a Stripe account to receive payments
- View and manage incoming orders, update order status
- Trigger shipments via The Courier Guy API

**Admin**
- Manage all users: activate/deactivate, promote/demote roles
- Moderate product listings (approve, reject, set pending)
- Manage product categories (create, edit, delete with product guard)

## Tech Stack

| Layer | Technology |
|---|---|
| **Frontend** | HTML5, Bootstrap 5, jQuery 3 |
| **Backend** | PHP 8.4, procedural + light OOP (no framework) |
| **Database** | MySQL 8 |
| **Object Storage** | Cloudflare R2 (S3-compatible, via AWS SDK) |
| **Payments** | Stripe Connect (sellers link their own accounts) |
| **Courier** | The Courier Guy REST API |
| **Infrastructure** | Docker, docker-compose, Ubuntu Server VM |
| **Tunnels** | Cloudflare Named Tunnels |
| **CI/CD** | GitHub Actions |

## Project Structure

```
doughdistrict-c2c-ecommerce/
├── public/                  ← web root (Apache document root)
│   ├── index.php            ← front controller / router
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js
│       └── images/
├── src/
│   ├── config/
│   │   ├── db.php           ← PDO connection
│   │   └── constants.php    ← BASE_URL, APP_NAME, ROOT_PATH
│   ├── helpers/
│   │   ├── auth.php         ← require_login(), require_role(), current_user()
│   │   ├── flash.php        ← one-time session messages
│   │   ├── r2.php           ← Cloudflare R2 upload helper (AWS SDK)
│   │   └── stripe.php       ← Stripe SDK wrapper (stub — Phase 5)
│   ├── models/              ← PDO data-access classes
│   ├── controllers/         ← request handlers (one file per domain)
│   └── views/
│       ├── layouts/         ← shared header.php + footer.php
│       ├── auth/            ← login, register
│       ├── admin/           ← admin panel (layout, users, products, categories)
│       ├── seller/          ← onboarding, dashboard, product CRUD, orders, Stripe
│       └── buyer/           ← browse, product detail, cart, checkout, orders, reviews
├── sql/
│   ├── schema.sql           ← full database schema
│   └── seed.sql             ← admin user, categories, sample data
├── docker-compose.yml       ← base services (app, db, phpmyadmin, tunnel)
├── docker-compose.prod.yml  ← prod override (tunnel always-on, no profile gate)
├── Dockerfile               ← PHP 8.4 + Apache image
└── .env.example             ← required environment variables
```

## Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose

### Environment Setup

```bash
cp .env.example .env
```

Edit `.env` and fill in the required values (see [Environment Variables](#environment-variables)).

### Running Locally

```bash
docker compose up -d
```

The app will be available at `http://localhost` (port 80).  
phpMyAdmin is available at `http://localhost:8080`.

The database schema and seed data are applied automatically on first start via `docker-entrypoint-initdb.d`.

To start with the Cloudflare dev tunnel active:

```bash
docker compose --profile tunnel up -d
```

## Ports

| Service | Port | Notes |
|---|---|---|
| PHP App (HTTP) | 80 | Main web interface |
| phpMyAdmin | 8080 | Database GUI |
| MySQL | not exposed | Internal network only (`dough-internal`) |

## Database

Key tables in `sql/schema.sql`:

| Table | Purpose |
|---|---|
| `users` | All users. `role ENUM('admin','seller','buyer')`. `is_active` for admin deactivation. |
| `seller_profiles` | Shop name, bio, Stripe Connect account ID, onboarding status |
| `addresses` | Buyer shipping addresses with label and default flag |
| `categories` | Admin-managed product categories with auto-generated slugs |
| `products` | Seller listings. `image_url` stores Cloudflare R2 CDN URL. Status: pending/active/rejected. |
| `orders` | One row per seller per checkout. Shipping address snapshotted at purchase time. |
| `order_items` | Line items. Product name and unit price snapshotted at purchase time. |
| `reviews` | One review per buyer per product per order. Rating 1–5 + comment. |

## Environment Variables

| Variable | Description | Example |
|---|---|---|
| `DB_HOST` | MySQL service name | `dough-db` |
| `DB_PORT` | MySQL port | `3306` |
| `DB_NAME` | Database name | `doughdistrict` |
| `DB_USER` | MySQL user | `dough` |
| `DB_PASS` | MySQL password | `secret` |
| `DB_ROOT_PASS` | MySQL root password | `rootsecret` |
| `R2_ACCOUNT_ID` | Cloudflare account ID | `abc123...` |
| `R2_ACCESS_KEY_ID` | R2 access key | — |
| `R2_SECRET_ACCESS_KEY` | R2 secret key | — |
| `R2_BUCKET` | R2 bucket name | `doughdistrict` |
| `R2_CDN_URL` | Public CDN base URL for R2 | `https://pub-xxx.r2.dev` |
| `STRIPE_SECRET_KEY` | Stripe secret key | `sk_test_...` |
| `STRIPE_PUBLISHABLE_KEY` | Stripe publishable key | `pk_test_...` |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook signing secret | `whsec_...` |
| `COURIER_GUY_API_KEY` | The Courier Guy API key | — |
| `APP_URL` | Public app URL | `https://doughdistrict.co.za` |

> `STRIPE_*` and `COURIER_GUY_API_KEY` are not yet in `.env.example` — add them before starting Phase 5 (Checkout).

## Deployment

The app runs on an Ubuntu Server VM behind named Cloudflare Tunnels:

| Environment | Domain | Tunnel | How to start |
|---|---|---|---|
| Production | `doughdistrict.co.za`, `www.doughdistrict.co.za` | `doughdistrict-prod` | Always-on via `docker-compose.prod.yml` override |
| Dev | `dev.doughdistrict.co.za` | `doughdistrict-dev` | Manual: `docker compose --profile tunnel up -d` |

**CI/CD** (GitHub Actions) deploys via SSH with:
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build --remove-orphans
```

Tunnel credentials (`cloudflared/config.yml` + `cloudflared/creds.json`) are gitignored. Copy them to the server once via `scp` — they persist across deploys.

## Roles & Permissions

| Role | Capabilities |
|---|---|
| `buyer` | Browse catalogue, search, filter by category, cart, checkout, view order history, leave reviews |
| `seller` | All buyer capabilities + manage shop profile, list products (with R2 image upload), fulfil orders, trigger shipments via Courier Guy |
| `admin` | Manage all users (role/status), moderate product listings, manage categories — **cannot buy or sell** |

**Role transition rules:**
- Any `buyer` can self-upgrade to `seller` via the shop onboarding form
- Admin can promote `buyer → admin` or demote `admin → buyer`
- The `seller` role is locked — admins cannot reassign it
- Admins must use a separate account to buy or sell

## Build Phases

| Phase | Scope | Status |
|---|---|---|
| 0 | Folder structure, Docker, schema.sql | Done |
| 1 | Auth + RBAC (register, login, logout, role guards) | Done |
| 2 | Admin panel (user management, categories, product moderation) | Done |
| 3 | Seller: shop onboarding, product CRUD, R2 upload, Stripe Connect stub | Done |
| 4 | Buyer: browse, search, category filter, product detail, session cart | Done |
| 5 | Checkout + Stripe payment, order creation | Pending |
| 6 | Order management (buyer history, seller fulfilment, status updates) | Pending |
| 7 | The Courier Guy API integration (shipment creation, tracking) | Pending |
| 8 | Reviews (post-delivery, per product per order) | Pending |
| 9 | Polish, mobile check, seed data, Cloudflare tunnel verification | Pending |
