# DoughDistrict — CLAUDE.md

## What this project is
A university C2C e-commerce MVP for home-baked goods in South Africa.
Sellers list products, buyers browse and purchase, payments via Stripe Connect, delivery via The Courier Guy API.
Includes a separate admin website with RBAC (required by university deliverable).

## Guiding principles
- Keep it simple. This is a school MVP, not a production SaaS.
- Do not add features, abstractions, or complexity beyond what is explicitly asked.
- No frameworks. Vanilla PHP, no Composer packages unless absolutely necessary.
- Ask before creating new files or making structural changes.
- Work one task at a time. Do not scaffold an entire phase unprompted.

## Tech stack
- **Frontend:** HTML5, Bootstrap 5, jQuery 3
- **Backend:** PHP 8 (procedural or light OOP — no framework)
- **Database:** MySQL 8
- **Images:** Cloudflare R2 (S3-compatible). AWS SDK used for upload. CDN URL stored in DB.
- **Payments:** Stripe Connect (sellers link their own accounts). No platform fee for MVP.
- **Courier:** The Courier Guy REST API. Seller manually triggers shipment.
- **Infrastructure:** Docker + docker-compose, Ubuntu Server VM, Cloudflare Quick Tunnel, GitHub Actions CI/CD

## Roles (RBAC)
| Role | Capabilities |
|---|---|
| `buyer` | Browse, search, cart, checkout, view orders, leave reviews |
| `seller` | All buyer capabilities + manage shop profile, list products, fulfil orders, trigger shipments |
| `admin` | Full access: manage all users (role/status), moderate products, manage categories |

- Role stored in `users.role ENUM('admin','seller','buyer')`
- Any buyer can self-upgrade to seller (shop onboarding form)
- Admin can promote/demote/deactivate any user
- **Role transition rules (enforced in controller + view):**
  - `buyer` → `admin` only (promote)
  - `admin` → `buyer` only (demote)
  - `seller` role is locked — no admin reassignment permitted
  - Admins are not permitted to buy or sell; they must use a separate account

## Auth
- PHP `$_SESSION` — no JWT
- Helpers: `require_login()`, `require_role($role)`, `current_user()` in `src/helpers/auth.php`

## Data models
| Table | Purpose |
|---|---|
| `users` | All users. `role` ENUM controls access. `is_active` for admin deactivation. |
| `seller_profiles` | Shop name, bio, Stripe Connect account ID, onboarding status |
| `addresses` | Buyer shipping addresses (label, default flag) |
| `categories` | Product categories (admin-managed) |
| `products` | Seller listings. `image_url` = Cloudflare R2 CDN URL. |
| `orders` | One row per seller per checkout. Shipping address snapshot columns. |
| `order_items` | Line items. Snapshots `product_name` + `unit_price` at purchase time. |
| `reviews` | One review per buyer per product per order. Rating 1–5 + comment. |

## Folder structure
```
DoughDistrict/
├── public/                     ← web root (Apache/Nginx document root)
│   ├── index.php               ← front controller / router
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js
│       └── images/
├── src/
│   ├── config/
│   │   ├── db.php              ← PDO connection
│   │   └── constants.php       ← BASE_URL, APP_NAME, etc.
│   ├── helpers/
│   │   ├── auth.php            ← require_login(), require_role(), current_user()
│   │   ├── flash.php           ← one-time session messages
│   │   ├── r2.php              ← Cloudflare R2 upload helper
│   │   └── stripe.php          ← Stripe SDK wrapper
│   ├── models/
│   │   ├── User.php
│   │   ├── SellerProfile.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   └── Review.php
│   ├── controllers/
│   │   ├── auth_controller.php
│   │   ├── admin_controller.php
│   │   ├── seller_controller.php
│   │   ├── cart_controller.php
│   │   ├── checkout_controller.php
│   │   ├── order_controller.php
│   │   ├── courier_controller.php
│   │   └── review_controller.php
│   └── views/
│       ├── layouts/
│       │   ├── header.php
│       │   └── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── admin/
│       │   ├── layout.php      ← admin-specific nav/sidebar
│       │   ├── dashboard.php
│       │   ├── users.php
│       │   ├── products.php
│       │   └── categories.php
│       ├── seller/
│       │   ├── onboarding.php
│       │   ├── dashboard.php
│       │   ├── stripe_connect.php
│       │   ├── products/
│       │   │   ├── index.php
│       │   │   ├── create.php
│       │   │   └── edit.php
│       │   └── orders/
│       │       ├── index.php
│       │       ├── detail.php
│       │       └── ship.php
│       └── buyer/
│           ├── browse.php
│           ├── product_detail.php
│           ├── cart.php
│           ├── checkout.php
│           ├── order_confirmation.php
│           ├── orders.php
│           ├── order_detail.php
│           └── review_form.php
├── docker/
├── sql/
│   ├── schema.sql              ← reviewed and finalised 2026-04-10
│   └── seed.sql                ← admin user, categories, sample data
├── .env
├── .env.example
├── CLAUDE.md
├── PLAN.md                     ← full phase-by-phase plan
└── context.md                  ← decisions and rationale from planning session
```

## Build phases
| Phase | Scope | Status |
|---|---|---|
| 0 | Folder structure, Docker, schema.sql | Done |
| 1 | Auth + RBAC (register, login, logout, role guards, seed admin) | **Next** |
| 2 | Admin panel (user management, categories, product moderation) | Pending |
| 3 | Seller: shop onboarding, product CRUD, R2 image upload, Stripe Connect | Pending |
| 4 | Buyer: browse, search, category filter, product detail, session cart | Pending |
| 5 | Checkout + Stripe payment, order creation | Pending |
| 6 | Order management (buyer history, seller fulfilment, status updates) | Pending |
| 7 | The Courier Guy API integration (shipment creation, tracking) | Pending |
| 8 | Reviews (post-delivery, per product per order) | Pending |
| 9 | Polish, mobile check, seed data, Cloudflare tunnel verification | Pending |

## Current phase
**Phase 1 — Auth + RBAC.**
Start with `src/config/db.php`, then `src/helpers/auth.php`, then the User model, then auth controller and views.
See `PLAN.md` for the detailed task list.

## Rules for Claude
- Do not scaffold an entire phase unprompted. Work one task at a time.
- Do not install Composer packages without asking first.
- Do not rewrite files that weren't part of the current task.
- Prefer editing existing files over creating new ones.
- When in doubt, propose and wait for confirmation.
- No Agents.md needed — this project is built step-by-step by the student with Claude assisting.
- Do not add a `Co-Authored-By` trailer to git commits.
