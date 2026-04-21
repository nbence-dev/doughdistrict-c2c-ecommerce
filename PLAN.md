# DoughDistrict — Implementation Plan

## Decisions Locked In

| Concern | Decision |
|---|---|
| Auth | PHP sessions (no JWT) |
| Roles | `admin`, `seller`, `buyer` — stored in `users.role` |
| Seller activation | Self-service — any buyer can upgrade their own account |
| Stripe | Connect (sellers link their own accounts) — no platform fee for MVP |
| Images | Cloudflare R2 — store CDN URL in `products.image_url` |
| Cart | PHP `$_SESSION` — no DB until checkout |
| Catalogue | Categories + keyword search |
| Courier | The Courier Guy API — seller manually triggers shipment after payment |
| Hosting | Ubuntu VM + Cloudflare Quick Tunnel (trycloudflare) |
| CI/CD | GitHub Actions (already set up) |

---

## Phase 0 — Foundation (COMPLETE)
- [x] Folder structure created
- [x] Docker + docker-compose configured
- [x] `sql/schema.sql` written and reviewed

**Schema fixes applied (2026-04-10):**
- Added `role ENUM('admin','seller','buyer')` and `is_active` to `users`
- Removed `is_seller` boolean (redundant — use `role = 'seller'`)
- Replaced `shipping_address_snapshot TEXT` with explicit snapshot columns on `orders`
- Removed `address_id FK` from `orders` (snapshot approach is cleaner and survives deletion)
- Added `product_name` snapshot to `order_items`
- Fixed `ON DELETE SET NULL` contradictions on NOT NULL columns
- Added `reviews` table (was missing)
- Added `stripe_onboarding_complete`, `is_default` address flag, address `label`
- Expanded `image_url` to VARCHAR(1000) for R2 URLs

---

## Phase 1 — Auth + RBAC (COMPLETE)
- [x] `src/config/db.php` — PDO connection
- [x] `src/config/constants.php` — BASE_URL, APP_NAME, ROOT_PATH
- [x] `src/helpers/auth.php` — `require_login()`, `require_role()`, `current_user()`
- [x] `src/helpers/flash.php` — one-time session messages
- [x] `src/models/User.php` — `findByEmail()`, `create()`, `findById()`, `setRole()`
- [x] `src/controllers/auth_controller.php` — register, login, logout
- [x] `src/views/auth/register.php` + `login.php`
- [x] `src/views/layouts/header.php` + `footer.php`
- [x] `public/index.php` — front controller / router
- [x] `sql/seed.sql` — admin user + seed categories

---

## Phase 2 — Admin Panel (COMPLETE)
- [x] `src/views/admin/layout.php` — fixed sidebar + frosted-glass topbar
- [x] `src/views/admin/users.php` — role badges, activate/deactivate, role change (buyer↔admin only), server-side pagination + filtering
- [x] `src/views/admin/products.php` — product moderation (approve/pending/reject), pagination + filtering
- [x] `src/views/admin/categories.php` — CRUD with live slug generation, edit modal, delete with product-guard
- [x] `src/controllers/admin_controller.php` — all admin actions
- [x] `src/models/Category.php`, `User.php`, `Product.php` extended with admin queries
- [ ] `src/views/admin/dashboard.php` — route exists but view is empty (deferred to Phase 9 polish)

---

## Phase 3 — Seller: Shop Setup + Product Listings (COMPLETE — one stub remaining)
- [x] `src/controllers/seller_controller.php` — onboarding, product CRUD, R2 upload, Stripe Connect stubs
- [x] `src/models/SellerProfile.php` — `create()`, `findByUserId()`, `findById()`, `nameExists()`
- [x] `src/models/Product.php` — `create()`, `update()`, `delete()`, `findBySeller()`, `findById()`
- [x] `src/views/seller/onboarding.php` — shop name + bio (upgrades buyer → seller)
- [x] `src/views/seller/dashboard.php` — listings overview
- [x] `src/views/seller/layout.php` — seller-specific sidebar/nav
- [x] `src/views/seller/products/index.php` + `create.php` + `edit.php`
- [x] `src/helpers/r2.php` — AWS SDK S3-compatible upload to Cloudflare R2
- [x] `src/views/seller/stripe_connect.php` — Stripe Connect UI page
- [ ] `src/helpers/stripe.php` — **EMPTY** — Stripe SDK wrapper not yet implemented
- [ ] Stripe Connect OAuth flow — controller stubs exist but TODO; needs `STRIPE_SECRET_KEY` wired up

---

## Phase 4 — Buyer: Browse, Search, Cart (COMPLETE)

**Goal:** Buyers can browse products, filter by category, search, view product detail, and manage a session cart.

- [x] `src/models/Product.php` — added `getBrowse($search, $category_id)` and `findActive($id)` (includes `seller_user_id` for ownership guard)
- [x] `src/models/Category.php` — `getAll()` used for filter sidebar
- [x] `src/controllers/browse_controller.php` — search + category filter via query params
- [x] `src/views/buyer/browse.php` — product grid, search bar, category filter
- [x] `src/views/buyer/product_detail.php` — image, description, price, stock, add-to-cart form
- [x] `src/views/buyer/cart.php` — item list with qty stepper, remove, order summary, empty state
- [x] `src/controllers/cart_controller.php` — `add`, `update`, `remove` actions + GET view builder. Seller ownership guard prevents seller adding their own product.
- [x] `src/views/errors/404.php` — branded 404 page; router default case updated
- [x] `src/views/layouts/header.php` — Material Symbols Outlined font added globally
- [x] `public/index.php` — all cart routes wired correctly

---

## Phase 5 — Checkout + Stripe Payment (**NEXT**)

**Goal:** Buyer completes purchase. Stripe Payment Intent created. Order written to DB on success.

**What exists (all empty):** `checkout_controller.php`, `checkout.php`, `order_confirmation.php`, `Order.php`, `stripe.php`

### Tasks
1. `src/helpers/stripe.php` — Stripe PHP SDK wrapper (`\Stripe\Stripe::setApiKey`) — **also completes the Phase 3 Stripe Connect stub**
2. `src/models/Order.php` — `create()`, `createItems()`, `findByBuyer()`
3. `src/views/buyer/checkout.php` — address selection/entry form, order summary, Stripe card element
4. `src/controllers/checkout_controller.php`
   - `GET /checkout` — render page, create Stripe PaymentIntent, return `client_secret`
   - `POST /checkout/confirm` — webhook or redirect handler: create `orders` + `order_items`, decrement stock
5. `public/assets/js/checkout.js` — Stripe.js card element, confirm payment, redirect on success
6. `src/views/buyer/order_confirmation.php` — thank-you page with order number

**Note:** Cart may span multiple sellers. Each seller's items = one `orders` row. Loop at checkout.

**Done when:** Buyer can pay via Stripe, order is created in DB, cart is cleared, confirmation shown.

---

## Phase 6 — Order Management

**Goal:** Buyers see order history. Sellers see their incoming orders and update status.

**What exists (all empty):** `order_controller.php`, `orders.php`, `order_detail.php`, `seller/orders/index.php`, `seller/orders/detail.php`
Router routes for `seller/orders` and `seller/orders/detail` exist but are commented out.

### Tasks
1. `src/controllers/order_controller.php` — fetch orders for buyer and seller
2. Extend `src/models/Order.php` — `findBySeller()`, `updateStatus()`
3. `src/views/buyer/orders.php` — list buyer's orders with status badge
4. `src/views/buyer/order_detail.php` — items, address, tracking number (if shipped)
5. `src/views/seller/orders/index.php` — list orders for this seller with status filter
6. `src/views/seller/orders/detail.php` — order items, buyer address, mark as processing
7. Uncomment + wire up seller order routes in `public/index.php`

**Done when:** Buyers see history, sellers see and update their order statuses.

---

## Phase 7 — The Courier Guy Integration

**Goal:** Seller triggers shipment via The Courier Guy API. Tracking number stored. Buyer can view it.

**What exists (all empty):** `courier_controller.php`, `seller/orders/ship.php`

### Tasks
1. `src/helpers/courier.php` — wraps The Courier Guy REST API
   - `createShipment($order)` → returns waybill/tracking number
   - `getTracking($trackingNumber)` → returns status string
2. `src/views/seller/orders/ship.php` — confirm shipment details form, POST triggers API call
3. `src/controllers/courier_controller.php` — calls `createShipment()`, saves tracking number, updates order to `shipped`
4. Add tracking number display to buyer's `order_detail.php`
5. `.env` — add `COURIER_GUY_API_KEY`

**API reference:** The Courier Guy developer portal (request API key separately).

**Done when:** Seller can create a shipment via button, order updates to `shipped`, buyer sees tracking number.

---

## Phase 8 — Reviews

**Goal:** Buyers who received an order can leave a star rating + comment on the product.

**What exists (all empty):** `review_controller.php`, `Review.php`, `review_form.php`

### Tasks
1. `src/models/Review.php` — `create()`, `findByProduct()`, `hasReviewed($buyerId, $orderId, $productId)`
2. `src/views/buyer/order_detail.php` — "Leave a Review" button per item (only if status = delivered, not yet reviewed)
3. `src/views/buyer/review_form.php` — star rating (1–5) + comment textarea
4. `src/controllers/review_controller.php` — validate, insert, redirect
5. `src/views/buyer/product_detail.php` — show average rating + review list

**Done when:** Delivered buyers can leave one review per product per order. Reviews show on product page.

---

## Phase 9 — Polish + Hosting

**Goal:** Finalize UI, run end-to-end smoke test, confirm Cloudflare tunnel is stable for submission.

### Tasks
1. `src/views/admin/dashboard.php` — fill in stats (total users, products, orders)
2. Consistent Bootstrap theme across all views (navbar, cards, badges, tables)
3. Mobile responsiveness check (Bootstrap grid review)
4. `.env.example` — document all required env vars
5. `sql/seed.sql` — verify demo admin, test buyer, test seller, sample categories + products are complete
6. Confirm `docker-compose up` works cleanly from cold start
7. Confirm named Cloudflare tunnel (`doughdistrict-prod`) exposes the app on `doughdistrict.co.za`
8. Final GitHub Actions CI check passes

---

## Estimated Timeline (as of 2026-04-21)

| Phase | Scope | Est. Sessions | Target Date | Status |
|---|---|---|---|---|
| 4 | Browse, search, cart | — | — | **DONE** |
| 5 | Checkout + Stripe payment | 2–3 sessions | 2026-04-25 | Next |
| 6 | Order management (buyer + seller views) | 1–2 sessions | 2026-04-27 | Pending |
| 7 | The Courier Guy integration | 1–2 sessions | 2026-04-29 | Pending |
| 8 | Reviews | 1 session | 2026-04-30 | Pending |
| 9 | Polish, seed data, hosting smoke test | 1 session | 2026-05-02 | Pending |

**Remaining work:** ~7–10 focused sessions. Realistically 1.5–2 weeks of part-time work.

**Critical path:** Phase 4 → 5 → 6 → 7 → 8 → 9 (strictly sequential — each phase depends on the last).

**Highest-risk item:** Stripe Connect OAuth (live credentials needed, callback URL must match). Start setting up Stripe test keys before Phase 5 begins.

---

## File Creation Order (sequential dependencies)

```
config/db.php
config/constants.php
helpers/auth.php
helpers/flash.php
           ↓
views/layouts/ (header, footer)
models/User.php
controllers/auth_controller.php
views/auth/ (login, register)
public/index.php (router)
           ↓
controllers/admin_controller.php
views/admin/
           ↓
models/SellerProfile.php, Product.php, Category.php
helpers/r2.php, stripe.php
controllers/seller_controller.php
views/seller/
           ↓
controllers/cart_controller.php
views/buyer/browse, cart, product_detail
           ↓
helpers/stripe.php
controllers/checkout_controller.php
views/buyer/checkout, confirmation
models/Order.php
           ↓
controllers/order_controller.php
views/buyer/orders, seller/orders
           ↓
helpers/courier.php
controllers/courier_controller.php
           ↓
models/Review.php
controllers/review_controller.php
views/buyer/review_form
```

---

## Environment Variables Required

```
DB_HOST=db
DB_PORT=3306
DB_NAME=doughdistrict
DB_USER=dough
DB_PASS=secret

STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

R2_ACCOUNT_ID=...
R2_ACCESS_KEY_ID=...
R2_SECRET_ACCESS_KEY=...
R2_BUCKET=doughdistrict-images
R2_CDN_URL=https://...r2.dev

COURIER_GUY_API_KEY=...

APP_URL=https://<your-tunnel>.trycloudflare.com
```
