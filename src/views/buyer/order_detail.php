<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<style>
.status-badge { font-size: .72rem; font-weight: 700; padding: .3rem .9rem; border-radius: 999px; display: inline-block; }
.status-paid       { background: #e4e3db; color: #51443c; }
.status-processing { background: #ffdcc4; color: #703800; }
.status-shipped    { background: #ffdcc5; color: #653d1e; }
.status-delivered  { background: #dbe9a9; color: #404b1b; }
.detail-card { background: #fff; border-radius: 1rem; box-shadow: 0 4px 16px rgba(48,49,44,.06); }
</style>

<?php
$order = $data['order'];
$items = $data['items'];
$statusClass = match($order['status']) {
    'processing' => 'status-processing',
    'shipped'    => 'status-shipped',
    'delivered'  => 'status-delivered',
    default      => 'status-paid',
};
?>

<main class="container py-5">

    <!-- Breadcrumb + Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>orders" style="color: var(--dd-outline);">Orders</a></li>
                <li class="breadcrumb-item active" style="color: var(--dd-on-surface-var);">Order #<?= (int) $order['id'] ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3 flex-wrap mt-2">
            <h1 class="font-headline fw-bold mb-0" style="color: var(--dd-primary);">Order #<?= (int) $order['id'] ?></h1>
            <span class="status-badge <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span>
        </div>
        <p class="mt-1 mb-0" style="color: var(--dd-on-surface-var);">
            Placed on <?= htmlspecialchars(date('d M Y', strtotime($order['created_at']))) ?> &mdash; <?= htmlspecialchars($order['shop_name']) ?>
        </p>
    </div>

    <div class="row g-4">

        <!-- Left: Items + Shipping -->
        <div class="col-lg-8">

            <!-- Order Items -->
            <div class="detail-card p-4 mb-4">
                <h2 class="h5 fw-bold mb-4" style="color: var(--dd-primary);">Order Items</h2>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($item['product_name']) ?></p>
                            <p class="small mb-0" style="color: var(--dd-on-surface-var);">
                                Qty: <?= (int) $item['quantity'] ?> &nbsp;&middot;&nbsp; @ R&nbsp;<?= number_format($item['unit_price'], 2) ?> each
                            </p>
                        </div>
                        <div class="text-end">
                            <p class="fw-bold mb-0" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!empty($order['shipping_cost'])): ?>
                <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top">
                    <span class="small" style="color: var(--dd-on-surface-var);">Items subtotal</span>
                    <span class="small" style="color: var(--dd-on-surface-var);">R&nbsp;<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2">
                    <span class="small" style="color: var(--dd-on-surface-var);">Shipping</span>
                    <span class="small" style="color: var(--dd-on-surface-var);">R&nbsp;<?= number_format($order['shipping_cost'], 2) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 mt-1">
                    <span class="fw-bold fs-5" style="color: var(--dd-on-surface);">Total</span>
                    <span class="fw-bold fs-4" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($order['total_amount'] + $order['shipping_cost'], 2) ?></span>
                </div>
                <?php else: ?>
                <div class="d-flex justify-content-between align-items-center pt-3 mt-2">
                    <span class="fw-bold fs-5" style="color: var(--dd-on-surface);">Total</span>
                    <span class="fw-bold fs-4" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Shipping Address -->
            <div class="detail-card p-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--dd-primary);">
                    <span class="material-symbols-outlined">local_shipping</span> Shipping Address
                </h3>
                <p class="fw-bold mb-1"><?= htmlspecialchars($order['shipping_name']) ?></p>
                <p class="mb-0" style="color: var(--dd-on-surface-var); line-height: 1.7;">
                    <?= htmlspecialchars($order['shipping_street']) ?><br>
                    <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_province']) ?><br>
                    <?= htmlspecialchars($order['shipping_postal_code']) ?>
                </p>
            </div>

        </div>

        <!-- Right: Sidebar -->
        <div class="col-lg-4">
            <div class="detail-card p-4">
                <h3 class="h6 fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--dd-primary);">
                    <span class="material-symbols-outlined">storefront</span> Fulfilled by
                </h3>
                <p class="fw-semibold mb-4"><?= htmlspecialchars($order['shop_name']) ?></p>
                <a href="<?= BASE_URL ?>orders" class="btn w-100" style="border: 1px solid var(--dd-outline-var); color: var(--dd-primary);">
                    &larr; Back to Orders
                </a>
            </div>
        </div>

    </div>
</main>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
