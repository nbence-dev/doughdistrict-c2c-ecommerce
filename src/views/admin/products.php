<?php
$pageTitle = 'Product Moderation';
include __DIR__ . '/layout.php';

// $products populated by admin_controller.php
// Each row: id, name, image_url, created_at, seller_name, category_name, price, status
$products = $products ?? [];
$flash = get_flash();

function product_status_badge(string $status): string
{
    $map = [
        'pending' => ['badge-pending', 'pending'],
        'active' => ['badge-approved', 'Active'],
        'rejected' => ['badge-rejected', 'Rejected'],
    ];
    [$cls, $label] = $map[$status] ?? ['badge-pending', ucfirst($status)];
    return '<span class="badge-status ' . $cls . '">' . htmlspecialchars($label) . '</span>';
}
?>
<?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show mb-4 py-2 small"
        role="alert">
        <?= htmlspecialchars($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"
            style="top: 50%; transform: translateY(-50%);"></button>
    </div>
<?php endif; ?>
<!-- ── Page Header ── -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <h2 class="page-heading mb-1">Product Moderation</h2>
        <p class="page-subheading mb-0">Review artisan listings and maintain DoughDistrict's marketplace quality.</p>
    </div>
    <!-- Filter chips -->
    <div class="d-flex flex-wrap gap-2">
        <button class="filter-chip chip-active" data-filter="all">All</button>
        <button class="filter-chip" data-filter="pending">Pending Review</button>
        <button class="filter-chip" data-filter="active">Active</button>
        <button class="filter-chip" data-filter="rejected">Rejected</button>
    </div>
</div>

<!-- ── Products Table ── -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-dd mb-0">
            <thead>
                <tr>
                    <th>Product &amp; Listing Details</th>
                    <th class="d-none d-md-table-cell">Maker</th>
                    <th class="d-none d-lg-table-cell">Category</th>
                    <th class="d-none d-sm-table-cell">Price &amp; Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--dd-outline)">
                            <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem">inventory_2</span>
                            No products to moderate.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr data-status="<?= htmlspecialchars($p['status'] ?? 'pending') ?>">
                            <!-- Product details -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($p['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($p['image_url']) ?>"
                                            alt="<?= htmlspecialchars($p['name']) ?>" class="rounded-2"
                                            style="width:64px;height:64px;object-fit:cover;flex-shrink:0;">
                                    <?php else: ?>
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width:64px;height:64px;background:var(--dd-surface-cont);color:var(--dd-outline)">
                                            <span class="material-symbols-outlined">bakery_dining</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold" style="color:var(--dd-on-surface)">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </div>
                                        <div class="small" style="color:var(--dd-outline)">
                                            Listed: <?= date('d M Y', strtotime($p['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Maker -->
                            <td class="d-none d-md-table-cell">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['seller_name'] ?? 'Seller') ?>&background=f5f4ec&color=6f4627&bold=true&size=48"
                                        alt="<?= htmlspecialchars($p['seller_name'] ?? '') ?>" class="rounded-circle"
                                        style="width:28px;height:28px;object-fit:cover;flex-shrink:0;">
                                    <span class="small fw-semibold" style="color:var(--dd-on-surface-var)">
                                        <?= htmlspecialchars($p['seller_name'] ?? '—') ?>
                                    </span>
                                </div>
                            </td>
                            <!-- Category -->
                            <td class="d-none d-lg-table-cell small" style="color:var(--dd-on-surface-var)">
                                <?= htmlspecialchars($p['category_name'] ?? '—') ?>
                            </td>
                            <!-- Price & Status -->
                            <td class="d-none d-sm-table-cell">
                                <div class="fw-bold mb-1"
                                    style="color:var(--dd-secondary);font-family:'Plus Jakarta Sans',sans-serif">
                                    R <?= number_format((float) $p['price'], 2) ?>
                                </div>
                                <?= product_status_badge($p['status'] ?? 'pending') ?>
                            </td>
                            <!-- Actions -->
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- View -->
                                    <a href="<?= BASE_URL ?>browse/product/<?= (int) $p['id'] ?>"
                                        class="action-btn action-btn-view" title="View listing">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <!-- Status dropdown -->
                                    <div class="dropdown">
                                        <button class="action-btn action-btn-edit dropdown-toggle" data-bs-toggle="dropdown"
                                            style="border-radius:.45rem" title="Set status">
                                            <span class="material-symbols-outlined">rule</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                            style="font-size:.8125rem">
                                            <li><span class="dropdown-item-text text-muted"
                                                    style="font-size:.675rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em">Set
                                                    Status</span></li>
                                            <li>
                                                <form method="POST" action="<?= BASE_URL ?>admin/products/status">
                                                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                                    <input type="hidden" name="status" value="active">
                                                    <button class="dropdown-item">Approve</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="<?= BASE_URL ?>admin/products/status">
                                                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                                    <input type="hidden" name="status" value="pending">
                                                    <button class="dropdown-item">Pending</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="<?= BASE_URL ?>admin/products/status">
                                                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="dropdown-item text-danger">Reject</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Pagination ── -->
<?php
$page       = $page       ?? 1;
$totalPages = $totalPages ?? 1;
$perPage    = $perPage    ?? PHP_INT_MAX;
$total      = $total      ?? count($products);
$showFrom   = $total > 0 ? ($page - 1) * $perPage + 1 : 0;
$showTo     = min($page * $perPage, $total);

// Build page-number window (max 7 entries, with '...' gaps)
if ($totalPages <= 7) {
    $pageWindow = range(1, $totalPages);
} elseif ($page <= 4) {
    $pageWindow = [1, 2, 3, 4, 5, '...', $totalPages];
} elseif ($page >= $totalPages - 3) {
    $pageWindow = [1, '...', $totalPages - 4, $totalPages - 3, $totalPages - 2, $totalPages - 1, $totalPages];
} else {
    $pageWindow = [1, '...', $page - 1, $page, $page + 1, '...', $totalPages];
}
?>
<div class="d-flex justify-content-between align-items-center mt-4">
    <p class="mb-0 small" style="color:var(--dd-outline)">
        Showing <span id="productCount">
            <?php if ($total === 0): ?>
                0 products
            <?php elseif ($totalPages <= 1): ?>
                <?= $total ?> product<?= $total !== 1 ? 's' : '' ?>
            <?php else: ?>
                <?= $showFrom ?>–<?= $showTo ?> of <?= $total ?> product<?= $total !== 1 ? 's' : '' ?>
            <?php endif; ?>
        </span>
    </p>
    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination pagination-sm mb-0" style="gap:.25rem">
            <!-- Prev -->
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link rounded" href="?page=<?= max(1, $page - 1) ?>"
                    style="border-color:rgba(213,195,184,.3)">
                    <span class="material-symbols-outlined" style="font-size:.9rem">chevron_left</span>
                </a>
            </li>
            <!-- Page numbers -->
            <?php foreach ($pageWindow as $pn): ?>
                <?php if ($pn === '...'): ?>
                    <li class="page-item disabled">
                        <span class="page-link" style="border-color:rgba(213,195,184,.3)">…</span>
                    </li>
                <?php else: ?>
                    <li class="page-item <?= $pn === $page ? 'active' : '' ?>">
                        <a class="page-link rounded" href="?page=<?= $pn ?>"
                            style="<?= $pn === $page
                                ? 'background:var(--dd-primary);border-color:var(--dd-primary)'
                                : 'border-color:rgba(213,195,184,.3);color:var(--dd-primary)' ?>">
                            <?= $pn ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <!-- Next -->
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link rounded" href="?page=<?= min($totalPages, $page + 1) ?>"
                    style="border-color:rgba(213,195,184,.3);color:var(--dd-primary)">
                    <span class="material-symbols-outlined" style="font-size:.9rem">chevron_right</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

</div><!-- /.admin-content -->
</div><!-- /#admin-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const chips = document.querySelectorAll('.filter-chip[data-filter]');
    const rows = document.querySelectorAll('tbody tr[data-status]');

    chips.forEach(chip => {
        chip.addEventListener('click', function () {
            chips.forEach(c => c.classList.remove('chip-active'));
            this.classList.add('chip-active');

            const filter = this.dataset.filter;
            let visible = 0;

            rows.forEach(row => {
                const show = filter === 'all' || row.dataset.status === filter;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('productCount').textContent =
                visible + ' product' + (visible !== 1 ? 's' : '');
        });
    });
</script>
</body>

</html>