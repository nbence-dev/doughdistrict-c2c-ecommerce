<?php
/**
 * @var int[] $order_ids  IDs of orders created in this checkout session
 */
$pageTitle = 'Order Confirmed';
include __DIR__ . '/layout.php'; ?>

<style>
    .confirm-hero {
        text-align: center;
        padding: 3rem 0 2rem;
    }
    .confirm-check {
        width: 5.5rem;
        height: 5.5rem;
        border-radius: 50%;
        background: rgba(73, 85, 35, 0.1);
        color: #495523;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        animation: confirm-pulse 2s infinite;
    }
    .confirm-check .material-symbols-outlined {
        font-size: 3rem;
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    @keyframes confirm-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(73, 85, 35, 0.35); }
        70%  { box-shadow: 0 0 0 18px rgba(73, 85, 35, 0); }
        100% { box-shadow: 0 0 0 0 rgba(73, 85, 35, 0); }
    }
    .confirm-card {
        background: #fff;
        border: 1px solid var(--dd-outline-var);
        border-radius: 1rem;
        padding: 2rem;
    }
    .order-id-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--dd-surface-low);
        border: 1px solid var(--dd-outline-var);
        border-radius: 999px;
        padding: .5rem 1.25rem;
        font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.0625rem;
        color: var(--dd-primary);
    }
    .action-card {
        background: var(--dd-surface-low);
        border-radius: 1rem;
        padding: 1.5rem;
    }
    .next-step-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

<main class="container py-5 mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <!-- Hero -->
            <div class="confirm-hero">
                <div class="confirm-check mx-auto">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <h1 class="fw-bold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif; font-size: clamp(2rem, 5vw, 2.75rem); color: var(--dd-primary);">
                    Order Confirmed!
                </h1>
                <p class="mb-0" style="color: var(--dd-on-surface-var); max-width: 38ch; margin: 0 auto;">
                    Your order is being prepared with care by your local makers.
                </p>
            </div>

            <!-- Order IDs -->
            <div class="confirm-card mb-4">
                <p class="fw-bold mb-3" style="color: var(--dd-on-surface-var); text-transform: uppercase; letter-spacing: .06em; font-size: .75rem;">
                    Order <?= count($order_ids) > 1 ? 'Numbers' : 'Number' ?>
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($order_ids as $oid): ?>
                        <span class="order-id-pill">
                            <span class="material-symbols-outlined" style="font-size: 1rem; color: var(--dd-secondary);">receipt_long</span>
                            #DD-<?= str_pad((int) $oid, 4, '0', STR_PAD_LEFT) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- What Happens Next -->
            <div class="confirm-card mb-4">
                <p class="fw-bold mb-4" style="color: var(--dd-on-surface-var); text-transform: uppercase; letter-spacing: .06em; font-size: .75rem;">
                    What Happens Next
                </p>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="next-step-icon" style="background: rgba(146, 76, 0, 0.1);">
                            <span class="material-symbols-outlined" style="font-size: 1.125rem; color: var(--dd-secondary);">kitchen</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">Bakers are preparing your order</div>
                            <div class="small mt-1" style="color: var(--dd-on-surface-var);">Your local makers have been notified and are getting to work.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-start">
                        <div class="next-step-icon" style="background: rgba(146, 76, 0, 0.1);">
                            <span class="material-symbols-outlined" style="font-size: 1.125rem; color: var(--dd-secondary);">local_shipping</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">Shipment will be arranged</div>
                            <div class="small mt-1" style="color: var(--dd-on-surface-var);">Once packed, the seller will dispatch via The Courier Guy. A tracking number will appear in your order history.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 align-items-start">
                        <div class="next-step-icon" style="background: rgba(73, 85, 35, 0.1);">
                            <span class="material-symbols-outlined" style="font-size: 1.125rem; color: #495523;">home</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">Delivered fresh to your door</div>
                            <div class="small mt-1" style="color: var(--dd-on-surface-var);">Freshly baked goods, straight from the maker to you.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-card">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <a href="<?= BASE_URL ?>orders"
                           class="btn btn-dd-primary w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <span class="material-symbols-outlined" style="font-size: 1.125rem;">receipt_long</span>
                            View My Orders
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="<?= BASE_URL ?>browse"
                           class="btn w-100 py-3 fw-semibold d-flex align-items-center justify-content-center gap-2"
                           style="border: 1.5px solid var(--dd-outline-var); border-radius: 2rem; color: var(--dd-primary); background: #fff;">
                            <span class="material-symbols-outlined" style="font-size: 1.125rem;">storefront</span>
                            Continue Browsing
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Quote -->
            <p class="text-center small fst-italic mt-5 mb-2" style="color: var(--dd-on-surface-var);">
                "Every loaf tells a story of heritage and patience. Thank you for supporting your local artisans."
            </p>
            <div class="text-center">
                <span class="material-symbols-outlined" style="color: var(--dd-outline-var); font-size: 1.375rem;">eco</span>
                <span class="material-symbols-outlined" style="color: var(--dd-outline-var); font-size: 1.375rem;">volunteer_activism</span>
                <span class="material-symbols-outlined" style="color: var(--dd-outline-var); font-size: 1.375rem;">bakery_dining</span>
            </div>

        </div>
    </div>
</main>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
