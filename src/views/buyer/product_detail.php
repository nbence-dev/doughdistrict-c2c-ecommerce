<?php $pageTitle = $product ? htmlspecialchars($product['name']) : 'Product'; include __DIR__ . '/layout.php'; ?>

<style>
    .product-img {
        border-radius: 1.25rem;
        width: 100%;
        max-height: 520px;
        object-fit: cover;
    }

    .qty-input {
        width: 72px;
        text-align: center;
        border-radius: 0.5rem;
        border: 1px solid var(--dd-outline-var);
    }

    .badge-category {
        background: var(--dd-surface-low);
        color: var(--dd-outline);
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.35em 0.85em;
        border-radius: 2rem;
    }

    .info-tile {
        background: var(--dd-surface-low);
        border-radius: 0.75rem;
        padding: 0.9rem 1rem;
        font-size: 0.85rem;
        color: var(--dd-on-surface-var);
    }
</style>

<div class="container py-5">

    <?php if (!$product): ?>
        <div class="alert alert-warning">Product not found or no longer available.</div>
    <?php else: ?>

        <!-- Back button + Breadcrumb -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= BASE_URL ?>browse" class="btn btn-sm px-3 py-1 d-flex align-items-center gap-1"
               style="background:var(--dd-surface-low);color:var(--dd-primary);border-radius:.625rem;font-weight:600;font-size:.8125rem;text-decoration:none;">
                <span class="material-symbols-outlined" style="font-size:1rem;">arrow_back</span> Browse
            </a>
            <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0" style="color: var(--dd-outline);">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>browse" style="color: var(--dd-outline);">Browse</a>
                </li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>browse?category=<?= htmlspecialchars($product['category_slug']) ?>"
                        style="color: var(--dd-outline);"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active" style="color: var(--dd-on-surface);">
                    <?= htmlspecialchars($product['name']) ?></li>
            </ol>
            </nav>
        </div>

        <div class="row g-5 align-items-start">

            <!-- Left: Image -->
            <div class="col-lg-6">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" class="product-img shadow-sm"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        loading="lazy">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center shadow-sm"
                        style="height: 420px; border-radius: 1.25rem; background: var(--dd-surface-low); font-size: 5rem;">
                        🥐
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Details -->
            <div class="col-lg-6">
                <span class="badge-category mb-3 d-inline-block"><?= htmlspecialchars($product['category_name']) ?></span>

                <h1 class="mb-2" style="font-size: 2.2rem; line-height: 1.2;">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <p class="mb-1">
                    <a href="#" class="text-decoration-none fw-medium" style="color: var(--dd-primary);">
                        <?= htmlspecialchars($product['shop_name']) ?>
                    </a>
                </p>
                <p class="small mb-4" style="color: var(--dd-outline);">
                    by <?= htmlspecialchars($product['seller_name']) ?>
                </p>

                <div class="d-flex align-items-center gap-3 mb-1">
                    <span style="font-size: 2rem; font-weight: 700; color: var(--dd-secondary);">
                        R <?= number_format($product['price'], 2) ?>
                    </span>
                    <span class="badge" style="background: var(--dd-surface-low); color: var(--dd-secondary); font-size: .7rem;">
                        <?php if (!empty($product['shipping_cost'])): ?>
                            + R <?= number_format($product['shipping_cost'], 2) ?> shipping
                        <?php else: ?>
                            Shipping TBD
                        <?php endif; ?>
                    </span>
                </div>
                <p class="small mb-4" style="color: var(--dd-outline);">
                    <?= (int) $product['stock_qty'] ?> in stock
                </p>

                <p class="mb-5" style="color: var(--dd-on-surface-var); line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <!-- Add to Cart -->
                <?php $viewerRole = current_user()['role'] ?? 'buyer'; ?>
                <?php if ($viewerRole === 'admin'): ?>
                <div class="alert alert-light border rounded-3 py-2 small mb-4" style="color: var(--dd-outline);">
                    <span class="material-symbols-outlined align-middle" style="font-size:1rem;">info</span>
                    Admins can browse products but cannot purchase.
                </div>
                <?php else: ?>
                <form method="POST" action="<?= BASE_URL ?>cart/add" data-validate>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <label class="fw-medium small" for="qty">Quantity</label>
                        <input type="number" id="qty" name="qty" value="1" min="1" max="<?= (int) $product['stock_qty'] ?>"
                            class="form-control qty-input">
                    </div>
                    <button type="submit" class="btn btn-dd-primary w-100 py-3 fs-5">
                        Add to Cart
                    </button>
                </form>

                <!-- Out-of-stock toast -->
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
                    <div id="stock-toast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body fw-semibold" id="stock-toast-msg"></div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <script>
                (function () {
                    var input = document.getElementById('qty');
                    var max   = parseInt(input.getAttribute('max'), 10);
                    function showToast() {
                        document.getElementById('stock-toast-msg').textContent =
                            'Only ' + max + ' in stock — quantity adjusted.';
                        bootstrap.Toast.getOrCreateInstance(
                            document.getElementById('stock-toast'), { delay: 3000 }
                        ).show();
                    }
                    input.addEventListener('input', function () {
                        if (parseInt(this.value, 10) > max) { this.value = max; showToast(); }
                        if (parseInt(this.value, 10) < 1)   { this.value = 1; }
                    });
                    input.addEventListener('change', function () {
                        if (parseInt(this.value, 10) > max) { this.value = max; showToast(); }
                        if (parseInt(this.value, 10) < 1)   { this.value = 1; }
                    });
                })();
                </script>
                <?php endif; ?>

                <!-- Info tiles -->
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="info-tile">🚚 Delivered via The Courier Guy</div>
                    </div>
                    <div class="col-6">
                        <div class="info-tile">🏡 Home-baked with care</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Reviews ───────────────────────────────────────────────── -->
        <section class="mt-5 pt-4" style="border-top: 1px solid var(--dd-outline-var);">
            <div class="row g-4">

                <!-- Left col: rating summary + review form -->
                <div class="col-lg-4">

                    <!-- Rating summary card -->
                    <div class="p-4 rounded-4 mb-4 shadow-sm" style="background: var(--dd-surface-low);">
                        <h3 class="fw-bold mb-3" style="font-size: 1.05rem; color: var(--dd-on-surface);">Customer Ratings</h3>
                        <?php if ($reviewCount > 0): ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span style="font-size: 3rem; font-weight: 700; color: var(--dd-on-surface); line-height: 1;">
                                <?= number_format($avgRating, 1) ?>
                            </span>
                            <div>
                                <div class="d-flex gap-1 mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="material-symbols-outlined"
                                          style="font-size:1.1rem;color:var(--dd-secondary);font-variation-settings:'FILL' <?= $i <= round($avgRating) ? '1' : '0' ?>;">star</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="small" style="color: var(--dd-outline);">
                                    Based on <?= $reviewCount ?> review<?= $reviewCount !== 1 ? 's' : '' ?>
                                </span>
                            </div>
                        </div>
                        <?php else: ?>
                        <p class="small mb-0" style="color: var(--dd-outline);">No reviews yet. Be the first!</p>
                        <?php endif; ?>
                    </div>

                    <!-- Review form (eligible buyers only) -->
                    <?php if (!empty($eligibleOrderIds)): ?>
                        <?php include __DIR__ . '/review_form.php'; ?>
                    <?php endif; ?>

                </div>

                <!-- Right col: review cards -->
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-4" style="font-size: 1.05rem; color: var(--dd-on-surface);">
                        Reviews
                        <?php if ($reviewCount > 0): ?>
                        <span class="fw-normal small ms-1" style="color: var(--dd-outline);">(<?= $reviewCount ?>)</span>
                        <?php endif; ?>
                    </h3>

                    <?php if (empty($reviews)): ?>
                    <p style="color: var(--dd-outline);">No reviews yet for this product.</p>
                    <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($reviews as $r): ?>
                        <?php
                            $nameParts = explode(' ', trim($r['reviewer_name']));
                            $initials  = strtoupper(
                                substr($nameParts[0], 0, 1) .
                                (count($nameParts) > 1 ? substr($nameParts[1], 0, 1) : '')
                            );
                        ?>
                        <div class="p-4 rounded-4" style="background: var(--dd-surface-low);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width:44px;height:44px;background:var(--dd-surface);color:var(--dd-primary);font-size:.8rem;">
                                        <?= htmlspecialchars($initials) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small" style="color: var(--dd-on-surface);">
                                            <?= htmlspecialchars($r['reviewer_name']) ?>
                                        </div>
                                        <div class="small" style="color: var(--dd-outline);">
                                            Verified Buyer &middot; <?= date('M j, Y', strtotime($r['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="material-symbols-outlined"
                                          style="font-size:.9rem;color:var(--dd-secondary);font-variation-settings:'FILL' <?= $i <= (int) $r['rating'] ? '1' : '0' ?>;">star</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <?php if (!empty($r['comment'])): ?>
                            <p class="mb-0 small" style="color: var(--dd-on-surface-var); line-height: 1.65;">
                                "<?= htmlspecialchars($r['comment']) ?>"
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>

    <?php endif; ?>

</div>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= JS_URL ?>validation.js"></script>
</body>
</html>