<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

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

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small" style="color: var(--dd-outline);">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>browse" style="color: var(--dd-outline);">Browse</a>
                </li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>browse?category=<?= $product['category_id'] ?>"
                        style="color: var(--dd-outline);"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active" style="color: var(--dd-on-surface);">
                    <?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row g-5 align-items-start">

            <!-- Left: Image -->
            <div class="col-lg-6">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" class="product-img shadow-sm"
                        alt="<?= htmlspecialchars($product['name']) ?>">
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
                    <span class="badge"
                        style="background: var(--dd-surface-low); color: var(--dd-secondary); font-size: .7rem;">Free Delivery</span>
                </div>
                <p class="small mb-4" style="color: var(--dd-outline);">
                    <?= (int) $product['stock_qty'] ?> in stock
                </p>

                <p class="mb-5" style="color: var(--dd-on-surface-var); line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <!-- Add to Cart -->
                <form method="POST" action="<?= BASE_URL ?>cart/add">
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

    <?php endif; ?>

</div>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>