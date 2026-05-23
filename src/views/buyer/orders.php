<?php $pageTitle = 'My Orders'; include __DIR__ . '/layout.php'; ?>

<style>
.status-badge { font-size: .72rem; font-weight: 700; padding: .25rem .8rem; border-radius: 999px; display: inline-block; }
.status-paid       { background: #e4e3db; color: #51443c; }
.status-processing { background: #ffdcc4; color: #703800; }
.status-shipped    { background: #ffdcc5; color: #653d1e; }
.status-delivered  { background: #dbe9a9; color: #404b1b; }
.order-row:hover   { background: var(--dd-surface-low); }
</style>

<main class="container py-5">

    <header class="mb-5">
        <h1 class="font-headline fw-bold" style="color: var(--dd-primary);">Your Orders</h1>
        <p class="mb-0" style="color: var(--dd-on-surface-var);">A history of every order you've placed on DoughDistrict.</p>
    </header>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <span class="material-symbols-outlined d-block mb-3" style="font-size: 3rem; color: var(--dd-outline);">receipt_long</span>
            <p style="color: var(--dd-on-surface-var);">You haven't placed any orders yet.</p>
            <a href="<?= BASE_URL ?>browse" class="btn btn-dd-primary mt-2 px-4">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead style="background: var(--dd-surface-low);">
                        <tr>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Order #</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Date</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Seller</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Status</th>
                            <th class="px-4 py-3 fw-bold text-uppercase" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Total</th>
                            <th class="px-4 py-3 fw-bold text-uppercase text-end" style="font-size:.7rem; letter-spacing:.12em; color: var(--dd-outline);">Action</th>
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
                                <td class="px-4 py-4 fw-bold" style="color: var(--dd-on-surface);">#<?= (int) $o['id'] ?></td>
                                <td class="px-4 py-4" style="color: var(--dd-on-surface-var);"><?= htmlspecialchars(date('d M Y', strtotime($o['created_at']))) ?></td>
                                <td class="px-4 py-4 fw-semibold" style="color: var(--dd-primary);"><?= htmlspecialchars($o['shop_name']) ?></td>
                                <td class="px-4 py-4"><span class="status-badge <?= $statusClass ?>"><?= ucfirst(htmlspecialchars($o['status'])) ?></span></td>
                                <td class="px-4 py-4 fw-bold" style="color: var(--dd-secondary);">R&nbsp;<?= number_format($o['total_amount'], 2) ?></td>
                                <td class="px-4 py-4 text-end">
                                    <a href="<?= BASE_URL ?>orders/detail?id=<?= (int) $o['id'] ?>" class="fw-bold text-decoration-none" style="color: var(--dd-primary);">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
