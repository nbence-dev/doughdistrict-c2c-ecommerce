<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<style>
.ship-card     { background: #fff; border-radius: 1rem; box-shadow: 0 4px 16px rgba(48,49,44,.06); }
.form-card     { background: var(--dd-surface-low); border-radius: 1rem; }
.dim-input     { text-align: center; background: #fff; border: none; border-radius: .75rem; padding: .75rem 1rem; width: 100%; color: var(--dd-on-surface); }
.dim-input:focus { outline: none; box-shadow: 0 0 0 2px rgba(111,70,39,.25); }
.tracking-placeholder { border: 1.5px dashed var(--dd-outline-var); border-radius: .75rem; background: var(--dd-surface-low); }
</style>

<main class="container py-5">

    <!-- Breadcrumb + Header -->
    <div class="mb-5">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>seller/orders" style="color: var(--dd-outline);">Orders</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>" style="color: var(--dd-outline);">
                        Order #<?= (int) $order['id'] ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" style="color: var(--dd-on-surface-var);">Ship</li>
            </ol>
        </nav>
        <h1 class="font-headline fw-bold mb-0" style="color: var(--dd-primary);">
            Ship Order #<?= (int) $order['id'] ?>
        </h1>
    </div>

    <div class="row g-4 align-items-start">

        <!-- Left: Order Summary -->
        <div class="col-lg-5">

            <div class="ship-card p-4 mb-4 position-relative overflow-hidden">
                <span class="material-symbols-outlined position-absolute top-0 end-0 m-3 select-none"
                      style="font-size: 4rem; color: var(--dd-surface-low); transform: rotate(12deg);">receipt_long</span>

                <h3 class="fw-bold mb-4 position-relative" style="color: var(--dd-on-surface);">Order Summary</h3>

                <div class="mb-3">
                    <p class="text-uppercase fw-bold mb-1" style="font-size: .65rem; letter-spacing: .12em; color: var(--dd-outline);">Buyer</p>
                    <p class="fw-semibold mb-0"><?= htmlspecialchars($buyerUser['name']) ?></p>
                </div>

                <div class="mb-4">
                    <p class="text-uppercase fw-bold mb-1" style="font-size: .65rem; letter-spacing: .12em; color: var(--dd-outline);">Shipping Address</p>
                    <div class="d-flex gap-2">
                        <span class="material-symbols-outlined flex-shrink-0 mt-1" style="color: var(--dd-secondary); font-size: 1.1rem;">location_on</span>
                        <p class="mb-0 small" style="color: var(--dd-on-surface-var); line-height: 1.7;">
                            <?= htmlspecialchars($order['shipping_street']) ?><br>
                            <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_province']) ?><br>
                            <?= htmlspecialchars($order['shipping_postal_code']) ?>
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <p class="text-uppercase fw-bold mb-3" style="font-size: .65rem; letter-spacing: .12em; color: var(--dd-outline);">Items</p>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="fw-semibold small mb-0"><?= htmlspecialchars($item['product_name']) ?></p>
                                <p class="mb-0" style="font-size: .75rem; color: var(--dd-outline);">
                                    Qty: <?= (int) $item['quantity'] ?>
                                </p>
                            </div>
                            <span class="fw-bold small" style="color: var(--dd-secondary);">
                                R&nbsp;<?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top">
                        <span class="fw-bold" style="color: var(--dd-on-surface);">Total</span>
                        <span class="fw-bold fs-5" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Info notice -->
            <div class="rounded-4 p-4 d-flex gap-3" style="background: rgba(73,85,35,.08); border: 1px solid rgba(73,85,35,.12);">
                <span class="material-symbols-outlined flex-shrink-0" style="color: var(--dd-secondary);">info</span>
                <div>
                    <p class="fw-bold small mb-1" style="color: var(--dd-secondary);">Fulfillment Notice</p>
                    <p class="mb-0" style="font-size: .8rem; color: var(--dd-on-surface-var); line-height: 1.6;">
                        Ensure all baked goods are cooled and securely packaged before handover. The courier will collect from your registered address.
                    </p>
                </div>
            </div>

        </div>

        <!-- Right: Shipment Form -->
        <div class="col-lg-7">
            <div class="form-card p-4 p-lg-5">
                <h3 class="fw-bold mb-4" style="color: var(--dd-on-surface);">Shipment Details</h3>

                <form method="POST" action="<?= BASE_URL ?>seller/orders/ship?id=<?= (int) $order['id'] ?>">

                    <!-- Parcel Description -->
                    <div class="mb-4">
                        <label for="parcel_description" class="form-label fw-semibold small d-flex align-items-center gap-2" style="color: var(--dd-on-surface-var);">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">inventory_2</span>
                            Parcel Description
                        </label>
                        <input type="text" id="parcel_description" name="parcel_description"
                               class="form-control" style="border-radius: .75rem; border: none; background: #fff;"
                               placeholder="e.g. Sourdough loaves, croissants"
                               value="Baked goods" required>
                    </div>

                    <!-- Weight + Collection Date -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label for="weight_kg" class="form-label fw-semibold small d-flex align-items-center gap-2" style="color: var(--dd-on-surface-var);">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">weight</span>
                                Weight (kg)
                            </label>
                            <input type="number" id="weight_kg" name="weight_kg" step="0.1" min="0.1"
                                   class="form-control" style="border-radius: .75rem; border: none; background: #fff;"
                                   placeholder="0.0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small d-flex align-items-center gap-2" style="color: var(--dd-on-surface-var);">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">storefront</span>
                                Collection From
                            </label>
                            <input type="text" class="form-control" style="border-radius: .75rem; border: none; background: #fff;"
                                   value="<?= htmlspecialchars($sellerProfile['city'] . ', ' . $sellerProfile['zone']) ?>" disabled>
                        </div>
                    </div>

                    <!-- Dimensions -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small d-flex align-items-center gap-2 mb-3" style="color: var(--dd-on-surface-var);">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">straighten</span>
                            Parcel Dimensions (cm)
                        </label>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="position-relative">
                                    <input type="number" name="length_cm" step="0.1" min="0.1"
                                           class="dim-input" placeholder="L" required>
                                    <span class="position-absolute end-0 top-0 mt-2 me-2 text-uppercase fw-bold"
                                          style="font-size: .6rem; color: var(--dd-outline);">Length</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="position-relative">
                                    <input type="number" name="width_cm" step="0.1" min="0.1"
                                           class="dim-input" placeholder="W" required>
                                    <span class="position-absolute end-0 top-0 mt-2 me-2 text-uppercase fw-bold"
                                          style="font-size: .6rem; color: var(--dd-outline);">Width</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="position-relative">
                                    <input type="number" name="height_cm" step="0.1" min="0.1"
                                           class="dim-input" placeholder="H" required>
                                    <span class="position-absolute end-0 top-0 mt-2 me-2 text-uppercase fw-bold"
                                          style="font-size: .6rem; color: var(--dd-outline);">Height</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking placeholder -->
                    <div class="tracking-placeholder p-3 mb-4 d-flex align-items-center gap-3" style="opacity: .6;">
                        <span class="material-symbols-outlined" style="color: var(--dd-outline);">qr_code_2</span>
                        <div>
                            <p class="text-uppercase fw-bold mb-0" style="font-size: .65rem; letter-spacing: .12em; color: var(--dd-outline);">Tracking Reference</p>
                            <p class="small mb-0 fst-italic" style="color: var(--dd-on-surface-var);">Generated upon creation…</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex align-items-center gap-3 pt-2">
                        <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>"
                           class="fw-bold text-decoration-none px-3" style="color: var(--dd-primary);">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-dd-primary flex-fill py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">local_shipping</span>
                            Create Shipment
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</main>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
