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
- Admin can change user roles, but with strict transition rules:
  - `buyer → admin` (promote) and `admin → buyer` (demote) are the only allowed transitions
  - `seller` role is locked — cannot be reassigned by admin
  - Rationale: admins are not permitted to shop or sell; they need a separate account for that
- Admin can deactivate any user regardless of role

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
- **Named Cloudflare Tunnels** (replaced quick tunnel):
  - `doughdistrict-prod` → `doughdistrict.co.za` + `www.doughdistrict.co.za` (always-on via `docker-compose.prod.yml` override)
  - `doughdistrict-dev` → `dev.doughdistrict.co.za` (manual, profile-gated: `docker compose --profile tunnel up -d`)
- `cloudflared/config.yml` and `cloudflared/creds.json` are gitignored — must be copied to the server manually via `scp` once; they persist between deploys
- GitHub Actions CI/CD deploys via: `docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build --remove-orphans`

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

## Phase 4 — Buyer: Browse, Search, Cart (completed 2026-04-21)

### What was built
- **browse_controller.php:** Loads active products via `Product::getBrowse($search, $category_id)`. Passes `$products`, `$categories`, `$search`, `$selected_category` to browse view.
- **cart_controller.php:** Session cart — `add`, `update`, `remove` POST actions + `GET` view data builder (`$cart_items`, `$total`). Seller-owns-product guard added (seller cannot add their own product to cart).
- **Product.php:** Added `getBrowse()` (active products with search + category filter), `findActive()` (single active product — updated to include `u.id AS seller_user_id` for the ownership guard).
- **Views:** `browse.php` (product grid, search bar, category filter), `product_detail.php` (image, description, price, stock, add-to-cart form), `cart.php` (item list with qty stepper + remove, order summary sidebar, empty state).
- **errors/404.php:** Branded 404 page — router default case now renders this instead of a plain echo.
- **header.php:** Added Material Symbols Outlined font (required for icons across all views).

### Key decisions
- Cart stored in `$_SESSION['cart']` as `[$product_id => $qty]`. No DB writes until checkout.
- Qty stepper in cart uses JavaScript to intercept +/- clicks and submit the update form — avoids a full page reload just to change quantity.
- Seller ownership check compares `$_SESSION['user_id']` against `$product['seller_user_id']` (joined `u.id` in `findActive()`).
- 404 view lives in `src/views/errors/` — a new subdirectory added for error pages.
- Shipping not calculated on cart page — shown as "Calculated at checkout" (Phase 5 concern).

### What is NOT yet done in Phase 4
- "Proceed to Checkout" button links to `/checkout` — Phase 5 not yet built (returns 404 currently).

---

## Phase 3 — Seller (completed ~2026-04-20)

### What was built
- **seller_controller.php:** Onboarding (buyer → seller upgrade), product CRUD (create/edit/delete with R2 image upload via AWS SDK), Stripe Connect route stubs (TODO).
- **SellerProfile.php:** `create()`, `findByUserId()`, `findById()`, `nameExists()`.
- **Product.php:** `create()`, `update()`, `delete()`, `findBySeller()`, `findById()`.
- **r2.php:** AWS SDK S3-compatible upload helper. Returns CDN URL stored in `products.image_url`.
- **Views:** `onboarding.php`, `dashboard.php`, `layout.php` (seller sidebar), `products/index.php`, `products/create.php`, `products/edit.php`, `stripe_connect.php`.

### What is NOT yet done in Phase 3
- `src/helpers/stripe.php` is an empty file — Stripe PHP SDK not yet wired up.
- Stripe Connect OAuth (`seller/stripe/connect` + `seller/stripe/callback`) are controller stubs with TODO comments. Needs live/test Stripe keys in `.env`.
- `seller/orders/` views (`index.php`, `detail.php`, `ship.php`) are empty — deferred to Phase 6/7.

---

## Phase 2 — Admin Panel (completed 2026-04-15)

### What was built
- **Layout:** Fixed sidebar + frosted-glass topbar, mobile slide-in overlay, full design token system in `src/views/admin/layout.php`. All admin pages share this layout via `include`.
- **Users page:** Table with role/status badges, activate/deactivate toggle, role change dropdown (buyer↔admin only; seller locked). Server-side pagination + filtering (?filter=all|admin|seller|buyer|inactive). Self-demotion blocked in controller.
- **Products page:** Table with seller/category/price/status. Status dropdown (approve/pending/reject). Same server-side pagination + filtering (?filter=all|pending|active|rejected).
- **Categories page:** Quick-create form with live slug generation and client-side duplicate check. Edit modal (inline). Delete modal with confirmation. Deletion blocked server-side if category has products.
- **Dashboard:** Route exists but `dashboard.php` is empty — left for later polish.

### Key decisions
- Pagination: 15 rows/page, `LIMIT ? OFFSET ?` in model. Count query runs separately (not SQL_CALC_FOUND_ROWS).
- Filtering: query param `?filter=X` drives a `WHERE` clause in the model. Filter chips are `<a>` links, not JS buttons — no client-side row hiding.
- Category slug: generated server-side in `Category::slugify()` on every create/update. The slug field in the form is display-only (readonly).
- `dashboard.php` nav link is commented out in the sidebar until the page is built.

## What Is NOT in Scope for MVP

- Platform fee (Stripe `application_fee_amount`) — skip for now
- Email notifications (order confirmations, shipping updates)
- Wishlists / saved products
- Promotions / discount codes
- Seller analytics dashboard beyond basic order list
- Multi-language / currency beyond ZAR
- Mobile app
- Admin approval of new sellers
