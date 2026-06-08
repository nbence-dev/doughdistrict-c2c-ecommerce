<?php $pageTitle = 'Cart'; include __DIR__ . '/layout.php'; ?>

<style>
    .cart-item-img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .qty-stepper input[type=number] {
        width: 56px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 700;
        color: var(--dd-on-surface);
        -moz-appearance: textfield;
    }
    .qty-stepper input[type=number]::-webkit-inner-spin-button,
    .qty-stepper input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
    .qty-stepper { background: var(--dd-surface-low); border-radius: 999px; display: inline-flex; align-items: center; padding: 4px 8px; }
    .qty-stepper button { background: none; border: none; color: var(--dd-primary); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .qty-stepper button:hover { background: var(--dd-outline-var); }
    .cart-summary { background: var(--dd-surface-low); border: 1px solid var(--dd-outline-var); border-radius: 1rem; padding: 2rem; }
    .remove-btn { background: none; border: none; color: var(--dd-outline); font-size: .875rem; cursor: pointer; display: flex; align-items: center; gap: 6px; }
    .remove-btn:hover { color: #ba1a1a; }
    .freshness-box { background: rgba(73, 85, 35, 0.08); border-radius: .5rem; padding: 1rem; display: flex; gap: .75rem; align-items: flex-start; }
</style>

<main class="container py-5 mt-4">

    <header class="mb-5">
        <h1 class="fw-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">Your Harvest Basket</h1>
        <p style="color: var(--dd-on-surface-var);">
            <?php if (!empty($cart_items)): ?>
                Supporting <?= count(array_unique(array_column(array_column($cart_items, 'product'), 'shop_name'))) ?> local maker(s).
            <?php else: ?>
                Your basket is empty.
            <?php endif; ?>
        </p>
    </header>

    <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <span class="material-symbols-outlined" style="font-size:64px; color:var(--dd-outline-var);">shopping_basket</span>
            <p class="mt-3" style="color:var(--dd-on-surface-var);">Nothing here yet — go find something delicious.</p>
            <a href="<?= BASE_URL ?>browse" class="btn btn-dd-primary mt-2">Browse the Bakery</a>
        </div>
    <?php else: ?>

        <div class="row g-5 align-items-start">

            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): $p = $item['product']; ?>
                <div class="d-flex flex-column flex-md-row gap-4 bg-white rounded-4 shadow-sm p-4 mb-4">

                    <?php if (!empty($p['image_url'])): ?>
                        <img src="<?= htmlspecialchars($p['image_url']) ?>"
                             alt="<?= htmlspecialchars($p['name']) ?>"
                             class="cart-item-img rounded-3"
                             loading="lazy">
                    <?php else: ?>
                        <div class="cart-item-img rounded-3 d-flex align-items-center justify-content-center"
                             style="background:var(--dd-surface-low);">
                            <span class="material-symbols-outlined" style="font-size:48px;color:var(--dd-outline-var);">bakery_dining</span>
                        </div>
                    <?php endif; ?>

                    <div class="flex-grow-1 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="h5 fw-bold mb-1"><?= htmlspecialchars($p['name']) ?></h3>
                                    <span class="d-flex align-items-center gap-1 small" style="color:var(--dd-secondary);">
                                        <span class="material-symbols-outlined" style="font-size:16px;">storefront</span>
                                        <?= htmlspecialchars($p['shop_name']) ?>
                                    </span>
                                </div>
                                <span class="fw-bold fs-5" style="color:var(--dd-secondary);">
                                    R <?= number_format($p['price'], 2) ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">

                            <!-- Quantity stepper -->
                            <form method="POST" action="<?= BASE_URL ?>cart/update" class="d-inline" data-validate>
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <div class="qty-stepper">
                                    <button type="button" class="qty-minus">
                                        <span class="material-symbols-outlined" style="font-size:18px;">remove</span>
                                    </button>
                                    <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$p['stock_qty'] ?>">
                                    <button type="button" class="qty-plus">
                                        <span class="material-symbols-outlined" style="font-size:18px;">add</span>
                                    </button>
                                </div>
                            </form>

                            <!-- Remove -->
                            <form method="POST" action="<?= BASE_URL ?>cart/remove" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="remove-btn">
                                    <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
                                    Remove
                                </button>
                            </form>
                        </div>

                        <div class="mt-2 text-end small" style="color:var(--dd-on-surface-var);">
                            Line total: <strong>R <?= number_format($item['subtotal'], 2) ?></strong>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="cart-summary position-sticky" style="top: 5rem;">
                    <h2 class="h5 fw-bold mb-4">Order Summary</h2>

                    <div class="d-flex justify-content-between mb-2" style="color:var(--dd-on-surface-var);">
                        <span>Subtotal</span>
                        <span class="fw-semibold" style="color:var(--dd-on-surface);">R <?= number_format($total, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="color:var(--dd-on-surface-var);">
                        <span>Shipping</span>
                        <span class="fst-italic small" style="color:var(--dd-outline);">Calculated at checkout</span>
                    </div>

                    <hr style="border-color: var(--dd-outline-var);">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-4" style="color:var(--dd-secondary);">R <?= number_format($total, 2) ?></span>
                    </div>

                    <a href="<?= BASE_URL ?>checkout" class="btn btn-dd-primary w-100 mb-3 py-3 fw-bold">
                        Proceed to Checkout
                    </a>
                    <a href="<?= BASE_URL ?>browse" class="btn w-100 py-3 fw-bold"
                       style="border:1px solid var(--dd-primary); color:var(--dd-primary); background:transparent;">
                        Continue Shopping
                    </a>

                    <div class="freshness-box mt-4">
                        <span class="material-symbols-outlined" style="color:#495523; font-variation-settings:'FILL' 1;">eco</span>
                        <p class="mb-0 small" style="color:#495523;">
                            <strong>Freshness Guarantee:</strong> All items are baked-to-order and dispatched within 24 hours of cooling.
                        </p>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-2 mt-4 small"
                         style="color:var(--dd-outline); text-transform:uppercase; letter-spacing:.08em;">
                        <span class="material-symbols-outlined" style="font-size:18px;">verified_user</span>
                        Secure Checkout · Stripe
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>

</main>

<script>
// Qty stepper: +/- buttons auto-submit the parent form
document.querySelectorAll('.qty-stepper').forEach(function(stepper) {
    var input = stepper.querySelector('input[type=number]');
    var form  = stepper.closest('form');

    stepper.querySelector('.qty-minus').addEventListener('click', function() {
        var v = parseInt(input.value) - 1;
        if (v < 1) v = 1;
        input.value = v;
        form.submit();
    });

    stepper.querySelector('.qty-plus').addEventListener('click', function() {
        var next = parseInt(input.value) + 1;
        if (next > parseInt(input.getAttribute('max'), 10)) return;
        input.value = next;
        form.submit();
    });
});
</script>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= JS_URL ?>validation.js"></script>
</body>
</html>
