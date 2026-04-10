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

## Phase 1 — Auth + RBAC

**Goal:** Register, login, logout. Role-based route guards. Seed an admin user.

### Tasks
1. `src/config/db.php` — PDO connection using `.env` values
2. `src/config/constants.php` — app-wide constants (BASE_URL, etc.)
3. `src/helpers/auth.php` — `require_login()`, `require_role($role)`, `current_user()`, `is_logged_in()`
4. `src/helpers/flash.php` — one-time session messages (success/error)
5. `src/models/User.php` — `findByEmail()`, `create()`, `findById()`
6. `src/controllers/auth_controller.php` — handles `POST /register`, `POST /login`, `GET /logout`
7. `src/views/auth/register.php` — Bootstrap form
8. `src/views/auth/login.php` — Bootstrap form
9. `src/views/layouts/header.php` + `footer.php` — shared nav, Bootstrap CDN links
10. `public/index.php` — front controller / router
11. `sql/seed.sql` — insert default admin user + seed categories

**Done when:** A user can register as buyer, log in, and log out. Admin user exists via seed.

---

## Phase 2 — Admin Panel (RBAC)

**Goal:** Separate admin UI. Manage users, products, categories. Satisfies uni RBAC requirement.

### Tasks
1. `src/views/admin/layout.php` — admin-specific nav/sidebar (Bootstrap)
2. `src/views/admin/dashboard.php` — stats: total users, products, orders
3. `src/views/admin/users.php` — list all users, change role, deactivate/activate
4. `src/views/admin/products.php` — list all products, toggle active/inactive, delete
5. `src/views/admin/categories.php` — CRUD for product categories
6. `src/controllers/admin_controller.php` — handles all admin actions
7. `src/models/` — extend User, Product models with admin queries

**Done when:** Admin can log in, see dashboard, manage users (promote/demote/deactivate), manage categories, and deactivate any product.

---

## Phase 3 — Seller: Shop Setup + Product Listings

**Goal:** Any buyer can activate a seller account. Sellers can list, edit, and delete products.

### Tasks
1. `src/controllers/seller_controller.php`
2. `src/models/SellerProfile.php` — `create()`, `findByUserId()`, `findById()`
3. `src/models/Product.php` — `create()`, `update()`, `delete()`, `findBySeller()`
4. `src/views/seller/onboarding.php` — shop name + bio form (sets `role = 'seller'`)
5. `src/views/seller/dashboard.php` — overview of listings and recent orders
6. `src/views/seller/products/index.php` — list own products
7. `src/views/seller/products/create.php` — new product form (incl. image upload)
8. `src/views/seller/products/edit.php` — edit product form
9. **Cloudflare R2 upload** — `src/helpers/r2.php` using AWS SDK (S3-compatible). Upload returns CDN URL stored in `products.image_url`.
10. `src/views/seller/stripe_connect.php` — link to Stripe Connect OAuth flow

**Done when:** Seller can activate account, create/edit/delete products with R2 image uploads, and initiate Stripe Connect.

---

## Phase 4 — Buyer: Browse, Search, Cart

**Goal:** Buyers can browse products, filter by category, search, view product detail, and manage a session cart.

### Tasks
1. `src/models/Product.php` — `getAll()`, `search($query)`, `filterByCategory()`, `findById()`
2. `src/models/Category.php` — `getAll()`
3. `src/views/buyer/browse.php` — product grid with search bar + category filter sidebar
4. `src/views/buyer/product_detail.php` — image, description, price, stock, add-to-cart button, reviews section
5. `src/views/buyer/cart.php` — session cart: list items, update qty, remove, subtotal
6. `src/controllers/cart_controller.php` — `add`, `update`, `remove`, `clear` cart actions (modifies `$_SESSION['cart']`)

**Done when:** Buyer can browse, search, filter, view a product, add to cart, and adjust cart.

---

## Phase 5 — Checkout + Stripe Payment

**Goal:** Buyer completes purchase. Stripe Payment Intent created. Order written to DB on success.

### Tasks
1. `src/views/buyer/checkout.php` — address selection/form, order summary, Stripe card element
2. `src/controllers/checkout_controller.php`
   - `GET /checkout` — render page, create Stripe PaymentIntent, return `client_secret`
   - `POST /checkout/confirm` — Stripe webhook or redirect handler, create `orders` + `order_items`, decrement stock
3. `src/models/Order.php` — `create()`, `createItems()`, `findByBuyer()`
4. `src/helpers/stripe.php` — Stripe PHP SDK wrapper (`\Stripe\Stripe::setApiKey`)
5. `public/assets/js/checkout.js` — Stripe.js card element, confirm payment, redirect on success
6. `src/views/buyer/order_confirmation.php` — thank-you page with order number

**Note:** Cart may span multiple sellers. Each seller's items = one `orders` row. Loop at checkout.

**Done when:** Buyer can pay via Stripe, order is created in DB, cart is cleared, confirmation shown.

---

## Phase 6 — Order Management

**Goal:** Buyers see order history. Sellers see their incoming orders and update status.

### Tasks
1. `src/views/buyer/orders.php` — list buyer's orders with status badge
2. `src/views/buyer/order_detail.php` — items, address, tracking number (if shipped)
3. `src/views/seller/orders/index.php` — list orders for this seller with status filter
4. `src/views/seller/orders/detail.php` — order items, buyer address, mark as processing
5. `src/controllers/order_controller.php` — fetch and update orders
6. Extend `src/models/Order.php` — `findBySeller()`, `updateStatus()`

**Done when:** Buyers see history, sellers see and update their order statuses.

---

## Phase 7 — The Courier Guy Integration

**Goal:** Seller triggers shipment via The Courier Guy API. Tracking number stored. Buyer can view it.

### Tasks
1. `src/helpers/courier.php` — wraps The Courier Guy REST API
   - `createShipment($order)` → returns waybill/tracking number
   - `getTracking($trackingNumber)` → returns status string
2. `src/views/seller/orders/ship.php` — confirm shipment details form, POST triggers API call
3. `src/controllers/courier_controller.php` — calls `createShipment()`, saves tracking number, updates order status to `shipped`
4. Add tracking number display to buyer's `order_detail.php`
5. `.env` — add `COURIER_GUY_API_KEY`

**API reference:** The Courier Guy developer portal (request API key separately).

**Done when:** Seller can create a shipment via button, order updates to `shipped`, buyer sees tracking number.

---

## Phase 8 — Reviews

**Goal:** Buyers who received an order can leave a star rating + comment on the product.

### Tasks
1. `src/models/Review.php` — `create()`, `findByProduct()`, `hasReviewed($buyerId, $orderId, $productId)`
2. `src/views/buyer/order_detail.php` — add "Leave a Review" button per item (only if status = delivered and not yet reviewed)
3. `src/views/buyer/review_form.php` — star rating (1–5) + comment textarea
4. `src/controllers/review_controller.php` — validate, insert, redirect
5. Product detail page — show average rating + review list

**Done when:** Delivered buyers can leave one review per product per order. Reviews show on product page.

---

## Phase 9 — Polish + Hosting

**Goal:** Finalize UI, run end-to-end smoke test, confirm Cloudflare tunnel is stable for submission.

### Tasks
1. Consistent Bootstrap theme across all views (navbar, cards, badges, tables)
2. Mobile responsiveness check (Bootstrap grid review)
3. `.env.example` — document all required env vars
4. `sql/seed.sql` — insert demo admin, test buyer, test seller, sample categories + products
5. Confirm `docker-compose up` works cleanly from cold start
6. Confirm Cloudflare Quick Tunnel exposes the app publicly
7. Final GitHub Actions CI check passes

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
