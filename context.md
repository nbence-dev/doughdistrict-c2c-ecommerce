# DoughDistrict — Project Context

> This file records decisions made during the planning interview (2026-04-10).
> It exists so future Claude sessions have full context without re-interviewing.

## University Deliverable Summary

- **Type:** C2C e-commerce platform (mandatory — B2C/B2B not permitted)
- **Required:** Main website + separate admin website with RBAC
- **RBAC requirement:** Create, display, update, delete different user types
- **Hosting:** Must be live online — localhost submissions penalised 50%
- **Stack:** HTML, CSS, JS (jQuery OK), PHP, MySQL, Bootstrap permitted
- **Diagrams needed** (separate from code): CRC cards, EERD, Context Diagram, DFD, Use Case Diagram, DB Schema

## Confirmed Architecture Decisions

### Auth
- PHP `$_SESSION` only. No JWT. No cookies beyond the session cookie.
- `session_start()` in every page that needs auth state.
- `require_login()` and `require_role($role)` helpers in `src/helpers/auth.php`.

### Roles (RBAC)
- Three roles: `admin`, `seller`, `buyer`
- Stored in `users.role ENUM`
- `is_seller` boolean removed — use `role = 'seller'` check instead
- Any `buyer` can self-upgrade to `seller` by completing shop onboarding
- Admin can change any user's role or deactivate them

### Payments
- **Stripe Connect** — sellers connect their own Stripe accounts
- Platform takes **no fee** for MVP (no `application_fee_amount`)
- Flow: buyer hits checkout → backend creates PaymentIntent → Stripe.js confirms → webhook (or redirect) creates order in DB
- One `orders` row per seller in the cart (cart may span multiple sellers)

### Images
- **Cloudflare R2** (S3-compatible object storage)
- PHP uploads via AWS SDK (S3 client pointed at R2 endpoint)
- Store CDN URL in `products.image_url VARCHAR(1000)`
- Config in `src/helpers/r2.php`

### Cart
- PHP `$_SESSION['cart']` — array keyed by `product_id`
- No DB writes until checkout
- Cleared after successful payment

### Courier
- **The Courier Guy** REST API
- Seller manually triggers shipment from the order detail page
- API call creates waybill, returns tracking number
- Tracking number stored in `orders.tracking_number`
- Buyer sees tracking number on their order detail page

### Catalogue
- Categories (seeded by admin, browseable by all)
- Keyword search on product name + description
- Both used on the buyer browse page

### Hosting
- Ubuntu Server VM (VirtualBox)
- Docker + docker-compose (`restart: always` already configured)
- Cloudflare Quick Tunnel (`trycloudflare`) for public HTTPS URL
- GitHub Actions CI/CD already configured

## Schema Changes Made at Planning (2026-04-10)

The original schema from a previous session had these bugs — all fixed in `sql/schema.sql`:

| Bug | Fix |
|---|---|
| No `role` column | Added `role ENUM('admin','seller','buyer') DEFAULT 'buyer'` |
| `is_seller BOOLEAN` — redundant with role | Removed |
| `orders.address_id NOT NULL` + FK `ON DELETE SET NULL` | Removed FK; replaced with explicit snapshot columns on `orders` |
| `order_items.product_id NOT NULL` + FK `ON DELETE SET NULL` | Made `product_id NULL`; added `product_name` snapshot column |
| `reviews` table missing | Added with `UNIQUE(product_id, buyer_id, order_id)` constraint |
| `image_url VARCHAR(255)` | Extended to `VARCHAR(1000)` for R2 CDN URLs |
| No `is_active` on users | Added for admin deactivation |
| No `stripe_onboarding_complete` on seller_profiles | Added |
| No `is_default` or `label` on addresses | Added |

## What Is NOT in Scope for MVP

- Platform fee (Stripe `application_fee_amount`) — skip for now
- Email notifications (order confirmations, shipping updates)
- Wishlists / saved products
- Promotions / discount codes
- Seller analytics dashboard beyond basic order list
- Multi-language / currency beyond ZAR
- Mobile app
- Admin approval of new sellers
