<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<style>
.status-badge { font-size: .7rem; font-weight: 700; padding: .25rem .75rem; border-radius: 999px; display: inline-block; }
.status-paid       { background: #e4e3db; color: #51443c; }
.status-processing { background: #ffdcc4; color: #703800; }
.status-shipped    { background: #ffdcc5; color: #653d1e; }
.status-delivered  { background: #dbe9a9; color: #404b1b; }
.order-row:hover   { background: var(--dd-surface-low); }
</style>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
        <div>
            <h1 class="font-headline fw-bold mb-1" style="color: var(--dd-on-surface);">My Orders</h1>
            <p class="mb-0" style="color: var(--dd-on-surface-var);">Manage incoming orders and customer shipments.</p>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <span class="material-symbols-outlined d-block mb-3" style="font-size: 3rem; color: var(--dd-outline);">shopping_bag</span>
            <p style="color: var(--dd-on-surface-var);">No orders yet — share your shop to get started!</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background: var(--dd-surface-low);">
                        <tr>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Order #</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Buyer</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Date</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Total</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Status</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <?php
                                $statusClass = match($o['status']) {
                                    'processing' => 'status-processing',
                                    'shipped'    => 'status-shipped',
                                    'delivered'  => 'status-delivered',
                                    default      => 'status-paid',
                                };
                            ?>
                            <tr class="order-row border-bottom">
                                <td class="px-4 py-4 fw-bold" style="color: var(--dd-primary);">#<?= (int) $o['id'] ?></td>
                                <td class="px-4 py-4 fw-semibold"><?= htmlspecialchars($o['name']) ?></td>
                                <td class="px-4 py-4" style="color: var(--dd-on-surface-var);"><?= htmlspecialchars(date('d M Y', strtotime($o['created_at']))) ?></td>
                                <td class="px-4 py-4 fw-bold" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($o['total_amount'], 2) ?></td>
                                <td class="px-4 py-4"><span class="status-badge <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($o['status'])) ?></span></td>
                                <td class="px-4 py-4">
                                    <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $o['id'] ?>" class="fw-bold text-decoration-none" style="color: var(--dd-primary);">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
