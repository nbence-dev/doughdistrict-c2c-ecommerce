# DoughDistrict — CLAUDE.md

## What this project is
A school project MVP e-commerce platform for home-baked goods in South Africa.
Sellers list products, buyers browse and purchase, payments via Stripe, delivery via a South African courier API.

## Guiding principles
- Keep it simple. This is a school MVP, not a production SaaS.
- Do not add features, abstractions, or complexity beyond what is explicitly asked.
- No frameworks. Vanilla PHP, no Composer packages unless absolutely necessary.
- Ask before creating new files or making structural changes.

## Forced tech stack
- **Frontend:** HTML5, Bootstrap 5, jQuery 3
- **Backend:** PHP 8 (procedural or light OOP — no framework)
- **Database:** MySQL 8
- **Infrastructure:** Docker, hosted on Ubuntu Server (VirtualBox VM), CI/CD via GitHub Actions

## External integrations
- **Stripe** — payments (Stripe.js on frontend, Stripe PHP SDK on backend)
- **Courier** — South African courier with a developer API (TBD: The Courier Guy or similar)

## Platform model
C2C — any registered user can buy. A user can also activate a seller account (connect Stripe, set up shop). The platform takes a fee on each sale.

## Data models (MVP tables)
| Table            | Purpose                                                        |
|------------------|----------------------------------------------------------------|
| users            | all users; `is_seller` flag activates seller features          |
| seller_profiles  | shop name, bio, Stripe Connect account ID — one per seller     |
| addresses        | shipping addresses linked to users                             |
| categories       | product categories                                             |
| products         | seller listings                                                |
| orders           | buyer orders, holds Stripe payment ID + courier tracking info  |
| order_items      | line items per order (snapshot of price at time of purchase)   |
| reviews          | buyer reviews per product                                      |

## Folder structure
```
DoughDistrict/
├── public/                  ← web root
│   ├── index.php
│   ├── assets/css|js|images
│   └── uploads/
├── src/
│   ├── config/              ← database.php, constants.php
│   ├── models/              ← one file per table
│   ├── controllers/         ← request logic
│   ├── views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── buyer/
│   │   └── seller/
│   └── helpers/
├── docker/
├── sql/schema.sql
├── .env.example
└── CLAUDE.md
```

## Build phases
- **Phase 0** — Folder structure, Docker setup, schema.sql
- **Phase 1** — Auth (register/login/logout, roles)
- **Phase 2** — Seller: create/edit/delete product listings
- **Phase 3** — Buyer: browse, search, product detail, cart (session-based)
- **Phase 4** — Checkout + Stripe payment
- **Phase 5** — Orders management (buyer history, seller fulfilment)
- **Phase 6** — Courier API integration (create shipment, tracking)
- **Phase 7** — Reviews
- **Phase 8** — CI/CD pipeline (GitHub Actions → Docker on Ubuntu Server)

## Current phase
Phase 0 — nothing built yet. Next step: create folder structure and sql/schema.sql.

## Rules for Claude
- Do not scaffold an entire phase unprompted. Work one task at a time.
- Do not install Composer packages without asking first.
- Do not rewrite files that weren't part of the current task.
- Prefer editing existing files over creating new ones.
- When in doubt, propose and wait for confirmation.
