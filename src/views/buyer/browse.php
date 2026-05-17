<?php include __DIR__ . '/layout.php'; ?>

<style>
    .dd-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .dd-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(48, 49, 44, 0.08) !important;
    }

    .dd-card img {
        height: 220px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .dd-card:hover img {
        transform: scale(1.04);
    }

    .dd-search {
        background: var(--dd-surface-low);
        border: none;
        border-radius: 0.75rem;
    }

    .dd-search:focus {
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(111, 70, 39, 0.2);
    }

    .dd-select {
        background: var(--dd-surface-low);
        border: none;
        border-radius: 0.75rem;
    }

    .dd-select:focus {
        box-shadow: 0 0 0 2px rgba(111, 70, 39, 0.2);
    }

    .price-tag {
        color: var(--dd-secondary);
        font-weight: 700;
        font-size: 1.05rem;
    }

    .shop-label {
        color: var(--dd-outline);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .img-placeholder {
        height: 220px;
        background: var(--dd-surface-low);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }
</style>

<div class="container py-5">

    <!-- Search & Filter Bar -->
    <form method="GET" action="<?= BASE_URL ?>browse" class="row g-3 mb-5 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"
                    style="background: var(--dd-surface-low); border: none; border-radius: 0.75rem 0 0 0.75rem; color: var(--dd-outline);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z" />
                    </svg>
                </span>
                <input type="text" name="q" class="form-control dd-search"
                    style="border-left: none; border-radius: 0 0.75rem 0.75rem 0;"
                    placeholder="Search for sourdough, croissants, rusks..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select dd-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($category_id == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dd-primary w-100">Search</button>
        </div>
        <?php if ($search || $category_id): ?>
            <div class="col-md-1">
                <a href="<?= BASE_URL ?>browse" class="btn btn-outline-secondary w-100" title="Clear filters">✕</a>
            </div>
        <?php endif; ?>
    </form>

    <!-- Result count -->
    <p class="small mb-4" style="color: var(--dd-outline);">
        <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> found
        <?php if ($search): ?>
            for <strong>"<?= htmlspecialchars($search) ?>"</strong>
        <?php endif; ?>
        <?php if ($category_id): ?>
            <?php $selCat = array_filter($categories, fn($c) => $c['id'] == $category_id); ?>
            <?php if ($selCat): ?>
                in <strong><?= htmlspecialchars(array_values($selCat)[0]['name']) ?></strong>
            <?php endif; ?>
        <?php endif; ?>
    </p>

    <!-- Product Grid -->
    <?php if (empty($products)): ?>
        <div class="text-center py-5" style="color: var(--dd-outline);">
            <p class="fs-5 mb-2">No products found.</p>
            <a href="<?= BASE_URL ?>browse" class="small">Clear filters</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($products as $p): ?>
                <div class="col">
                    <div class="card dd-card h-100 shadow-sm">
                        <?php if (!empty($p['image_url'])): ?>
                            <div class="overflow-hidden">
                                <img src="<?= htmlspecialchars($p['image_url']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($p['name']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="img-placeholder">🥐</div>
                        <?php endif; ?>

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="card-title mb-0 me-2"><?= htmlspecialchars($p['name']) ?></h5>
                                <span class="price-tag text-nowrap">R<?= number_format($p['price'], 2) ?></span>
                            </div>
                            <p class="shop-label mb-2"><?= htmlspecialchars($p['shop_name']) ?></p>
                            <?php if (!empty($p['avg_rating'])): ?>
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <span class="material-symbols-outlined"
                                      style="font-size:.9rem;color:var(--dd-secondary);font-variation-settings:'FILL' 1;">star</span>
                                <span class="small fw-semibold" style="color:var(--dd-secondary);"><?= number_format($p['avg_rating'], 1) ?></span>
                                <span class="small" style="color:var(--dd-outline);">(<?= (int) $p['review_count'] ?>)</span>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                <span class="badge" style="background: var(--dd-surface-low); color: var(--dd-secondary); font-size: .65rem;">
                                    <?php if (!empty($p['shipping_cost'])): ?>
                                        Shipping R <?= number_format($p['shipping_cost'], 2) ?>
                                    <?php else: ?>
                                        Shipping TBD
                                    <?php endif; ?>
                                </span>
                                <?php if ((int) $p['stock_qty'] === 0): ?>
                                    <span class="badge" style="background: #f5e6e6; color: #8b1a1a; font-size: .65rem;">Out of stock</span>
                                <?php elseif ((int) $p['stock_qty'] <= 5): ?>
                                    <span class="badge" style="background: #fff3e0; color: #7c4700; font-size: .65rem;">Only <?= (int) $p['stock_qty'] ?> left!</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--dd-surface-low); color: var(--dd-outline); font-size: .65rem;"><?= (int) $p['stock_qty'] ?> in stock</span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text small mb-4 flex-grow-1"
                                style="color: var(--dd-on-surface-var); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($p['description']) ?>
                            </p>
                            <?php if ((int) $p['stock_qty'] === 0): ?>
                            <span class="btn w-100 mt-auto disabled" style="background: var(--dd-surface-low); color: var(--dd-outline); cursor: not-allowed;">
                                Out of Stock
                            </span>
                            <?php else: ?>
                            <a href="<?= BASE_URL ?>product?id=<?= $p['id'] ?>" class="btn btn-dd-primary w-100 mt-auto">
                                View Product
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>