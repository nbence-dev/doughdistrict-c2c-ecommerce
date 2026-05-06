# DoughDistrict

DoughDistrict is a consumer-to-consumer (C2C) e-commerce platform for home-baked goods in South Africa. Home bakers list their products for sale, buyers browse and purchase via Stripe, and sellers fulfil orders with courier integration through The Courier Guy (Shiplogic). Built as a university MVP deliverable (Eduvos ITECA3-B12).

## Features

**Buyer**
- Browse, search, and filter products by category
- Stock badges with urgency indicators — "Only X left!" or "Out of stock"
- View product detail pages with images, pricing, and seller information
- Session-based shopping cart (add, update quantity, remove)
- Checkout with Stripe card payment — one PaymentIntent per seller
- Save and select shipping addresses at checkout
- View order history with status badges
- View order detail with line items and shipping address snapshot
- Track shipments via Shiplogic tracking link with pre-filled reference
- Transactional email notifications: order confirmed, shipped, and delivered

**Seller**
- Self-service shop onboarding — any buyer can upgrade their account
- Shop profile management (name, bio, collection address)
- Product CRUD with image upload to Cloudflare R2
- Connect a Stripe account via OAuth to receive payments directly
- View and manage incoming orders, update fulfilment status
- Orders sorted by upcoming driver pickup date (soonest first)
- Book shipments via The Courier Guy (Shiplogic API) with parcel dimensions
- Tracking reference, estimated driver pickup date, and Print Waybill stored after booking
- Email notification sent to seller on every new order received

**Admin**
- Manage all users: activate/deactivate, promote buyer→admin or demote admin→buyer
- Moderate product listings (approve, reject, set pending)
- Manage product categories (create, edit, delete with product guard)

## Tech Stack

| Layer | Technology |
|---|---|
| **Frontend** | HTML5, Bootstrap 5, jQuery 3, Material Symbols |
| **Backend** | PHP 8, procedural + light OOP (no framework) |
| **Database** | MySQL 8 |
| **Object Storage** | Cloudflare R2 (S3-compatible, via AWS SDK for PHP) |
| **Payments** | Stripe Connect (destination charges — no platform fee) |
| **Courier** | The Courier Guy via Shiplogic REST API |
| **Email** | Resend (transactional email via PHP SDK) |
| **Infrastructure** | Docker, docker-compose, Ubuntu Server VM |
| **Tunnels** | Cloudflare Named Tunnels |
| **CI/CD** | GitHub Actions (SSH deploy) |

## Project Structure

```
doughdistrict-c2c-ecommerce/
├── public/                  ← web root (Apache document root)
│   ├── index.php            ← front controller / router
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js
│       └── js/checkout.js   ← Stripe.js card element logic
├── src/
│   ├── config/
│   │   ├── db.php           ← PDO connection (reads from env)
│   │   └── constants.php    ← BASE_URL, APP_NAME, ROOT_PATH
│   ├── helpers/
│   │   ├── auth.php         ← require_login(), require_role(), current_user()
│   │   ├── flash.php        ← one-time session messages
│   │   ├── r2.php           ← Cloudflare R2 upload via AWS SDK
│   │   ├── stripe.php       ← Stripe SDK boot + PaymentIntent helpers
│   │   ├── courier.php      ← Shiplogic API wrapper (create shipment, label URL, rates)
│   │   ├── mailer.php       ← send_email() wrapper using Resend PHP SDK
│   │   └── emails.php       ← branded HTML email templates (order confirmed, shipped, delivered, new order)
│   ├── models/              ← PDO data-access classes (one per table)
│   │   ├── User.php
│   │   ├── SellerProfile.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── Address.php
│   │   └── Review.php
│   ├── controllers/         ← request handlers (one file per domain)
│   └── views/
│       ├── layouts/         ← header.php + footer.php (Bootstrap 5, shared nav)
│       ├── auth/            ← login, register
│       ├── admin/           ← admin panel layout, users, products, categories
│       ├── seller/          ← onboarding, dashboard, profile, products, orders, ship
│       ├── buyer/           ← browse, product detail, cart, checkout, orders, reviews
│       └── errors/          ← 404
├── sql/
│   ├── schema.sql           ← full database schema
│   └── seed.sql             ← admin user, categories, sample data
├── docker-compose.yml       ← base services (app, db, phpmyadmin, tunnel)
├── docker-compose.prod.yml  ← prod override (tunnel always-on)
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

Edit `.env` and fill in all required values (see [Environment Variables](#environment-variables)).

### Running Locally

```bash
docker compose up -d
```

The app will be available at `http://localhost` (port 80).
phpMyAdmin is available at `http://localhost:8080`.

The database schema and seed data are applied automatically on first start via `docker-entrypoint-initdb.d`. The seed file creates the default admin account and product categories.

To start with the Cloudflare dev tunnel:

```bash
docker compose --profile tunnel up -d
```

After changing `.env`, restart the containers to pick up new variables:

```bash
docker compose restart
```

## Ports

| Service | Port | Notes |
|---|---|---|
| PHP App (HTTP) | 80 | Main web interface |
| phpMyAdmin | 8080 | Database GUI |
| MySQL | — | Internal network only (`dough-internal`) |

## Database

Key tables in `sql/schema.sql`:

| Table | Purpose |
|---|---|
| `users` | All users. `role ENUM('admin','seller','buyer')`. `is_active` for deactivation. |
| `seller_profiles` | Shop name, bio, collection address, Stripe Connect account ID and onboarding status |
| `addresses` | Buyer shipping addresses with label and default flag |
| `categories` | Admin-managed product categories |
| `products` | Seller listings. `image_url` stores R2 CDN URL. Status: `pending/active/rejected`. Includes dimension and shipping cost fields. |
| `orders` | One row per seller per checkout. Shipping address snapshotted at purchase. Stores Stripe PaymentIntent ID, Shiplogic shipment ID, tracking reference, and estimated driver collection date. |
| `order_items` | Line items. Product name and unit price snapshotted at purchase time. |
| `reviews` | One review per buyer per product per order. Rating 1–5 + comment. |

## Environment Variables

| Variable | Description |
|---|---|
| `DB_HOST` | MySQL service hostname (`dough-db` in Docker) |
| `DB_PORT` | MySQL port (`3306`) |
| `DB_NAME` | Database name |
| `DB_USER` | MySQL user |
| `DB_PASS` | MySQL user password |
| `DB_ROOT_PASS` | MySQL root password |
| `R2_ACCOUNT_ID` | Cloudflare account ID |
| `R2_ACCESS_KEY_ID` | R2 access key ID |
| `R2_SECRET_ACCESS_KEY` | R2 secret access key |
| `R2_BUCKET` | R2 bucket name |
| `R2_CDN_URL` | Public CDN base URL for the R2 bucket |
| `STRIPE_SECRET_KEY` | Stripe secret key (`sk_test_...` for sandbox) |
| `STRIPE_PUBLISHABLE_KEY` | Stripe publishable key (`pk_test_...` for sandbox) |
| `STRIPE_CONNECT_CLIENT_ID` | Stripe Connect OAuth client ID (`ca_...`) |
| `SHIPLOGIC_API_KEY` | Shiplogic (The Courier Guy) API key |
| `SHIPLOGIC_API_URL` | Shiplogic base URL (`https://api.shiplogic.com`) |
| `RESEND_API_KEY` | Resend transactional email API key |
| `MAIL_FROM` | From address for outgoing emails (e.g. `orders@doughdistrict.co.za`) |

## Deployment

The app runs on an Ubuntu Server VM behind named Cloudflare Tunnels:

| Environment | Domain | Tunnel | How to start |
|---|---|---|---|
| Production | `doughdistrict.co.za` | `doughdistrict-prod` | Always-on via `docker-compose.prod.yml` override |
| Dev | `dev.doughdistrict.co.za` | `doughdistrict-dev` | Manual: `docker compose --profile tunnel up -d` |

**CI/CD** deploys via GitHub Actions over SSH:
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build --remove-orphans
```

Tunnel credentials (`cloudflared/config.yml` and `cloudflared/creds.json`) are gitignored. Copy them to the server once via `scp` — they persist across deploys.

## Roles & Permissions

| Role | Capabilities |
|---|---|
| `buyer` | Browse, search, filter, cart, checkout, order history, reviews |
| `seller` | All buyer capabilities + shop profile, product CRUD, order management, Stripe Connect, courier shipment booking |
| `admin` | User management, product moderation, category management — cannot buy or sell |

**Role transition rules:**
- Any `buyer` can self-upgrade to `seller` via the shop onboarding form
- Admin can promote `buyer → admin` or demote `admin → buyer`
- The `seller` role is locked — admins cannot reassign it
- Admins must use a separate account to buy or sell

## Build Phases

| Phase | Scope | Status |
|---|---|---|
| 0 | Folder structure, Docker, schema.sql | Done |
| 1 | Auth + RBAC (register, login, logout, role guards, seed admin) | Done |
| 2 | Admin panel (user management, categories, product moderation) | Done |
| 3 | Seller: shop onboarding, product CRUD, R2 image upload, Stripe Connect OAuth | Done |
| 4 | Buyer: browse, search, category filter, product detail, session cart | Done |
| 5 | Checkout + Stripe payment (destination charges), order creation | Done |
| 6 | Order management (buyer history, seller fulfilment, status updates) | Done |
| 7 | Shiplogic (The Courier Guy) integration — shipment booking, tracking, waybill | Done |
| 8 | Reviews (post-delivery, per product per order, rating 1–5) | Pending |
| 9 | Email notifications (Resend) — order confirmed, shipped, delivered, new order | Done |
| 10 | Admin dashboard stats, polish, seed data verification | Pending |
