<?php
$pageTitle = 'Product Moderation';
include __DIR__ . '/layout.php';

// $products populated by admin_controller.php
// Each row: id, name, image_url, created_at, seller_name, category_name, price, status
$products = $products ?? [];

function product_status_badge(string $status): string {
    $map = [
        'pending'  => ['badge-pending',  'pending'],
        'active'   => ['badge-approved', 'Active'],
        'rejected' => ['badge-rejected', 'Rejected'],
    ];
    [$cls, $label] = $map[$status] ?? ['badge-pending', ucfirst($status)];
    return '<span class="badge-status ' . $cls . '">' . htmlspecialchars($label) . '</span>';
}
?>

<!-- ── Page Header ── -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <h2 class="page-heading mb-1">Product Moderation</h2>
        <p class="page-subheading mb-0">Review artisan listings and maintain DoughDistrict's marketplace quality.</p>
    </div>
    <!-- Filter chips -->
    <div class="d-flex flex-wrap gap-2">
        <button class="filter-chip chip-active">All</button>
        <button class="filter-chip">Pending Review</button>
        <button class="filter-chip">Active</button>
        <button class="filter-chip">Rejected</button>
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
                <tr>
                    <!-- Product details -->
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= htmlspecialchars($p['image_url']) ?>"
                                 alt="<?= htmlspecialchars($p['name']) ?>"
                                 class="rounded-2" style="width:64px;height:64px;object-fit:cover;flex-shrink:0;">
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
                                 alt="<?= htmlspecialchars($p['seller_name'] ?? '') ?>"
                                 class="rounded-circle" style="width:28px;height:28px;object-fit:cover;flex-shrink:0;">
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
                        <div class="fw-bold mb-1" style="color:var(--dd-secondary);font-family:'Plus Jakarta Sans',sans-serif">
                            R <?= number_format((float)$p['price'], 2) ?>
                        </div>
                        <?= product_status_badge($p['status'] ?? 'pending') ?>
                    </td>
                    <!-- Actions -->
                    <td>
                        <div class="d-flex justify-content-end gap-1">
                            <!-- View -->
                            <a href="<?= BASE_URL ?>browse/product/<?= (int)$p['id'] ?>"
                               class="action-btn action-btn-view" title="View listing">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>

                            <?php if (($p['status'] ?? '') === 'pending'): ?>
                            <!-- Approve -->
                            <form method="POST" action="<?= BASE_URL ?>admin/products/approve">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="action-btn action-btn-approve" title="Approve">
                                    <span class="material-symbols-outlined">check</span>
                                </button>
                            </form>
                            <!-- Reject -->
                            <form method="POST" action="<?= BASE_URL ?>admin/products/reject">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="action-btn action-btn-reject" title="Reject">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </form>

                            <?php elseif (($p['status'] ?? '') === 'active'): ?>
                            <!-- Reject/remove active listing -->
                            <form method="POST" action="<?= BASE_URL ?>admin/products/reject">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="action-btn action-btn-ban" title="Remove listing">
                                    <span class="material-symbols-outlined">block</span>
                                </button>
                            </form>

                            <?php elseif (($p['status'] ?? '') === 'rejected'): ?>
                            <!-- Restore -->
                            <form method="POST" action="<?= BASE_URL ?>admin/products/approve">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="action-btn action-btn-restore" title="Restore">
                                    <span class="material-symbols-outlined">refresh</span>
                                </button>
                            </form>
                            <?php endif; ?>
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
<div class="d-flex justify-content-between align-items-center mt-4">
    <p class="mb-0 small" style="color:var(--dd-outline)">
        Showing <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?>
    </p>
    <nav>
        <ul class="pagination pagination-sm mb-0" style="gap:.25rem">
            <li class="page-item disabled">
                <a class="page-link rounded" href="#" style="border-color:rgba(213,195,184,.3)">
                    <span class="material-symbols-outlined" style="font-size:.9rem">chevron_left</span>
                </a>
            </li>
            <li class="page-item active">
                <a class="page-link rounded" href="#"
                   style="background:var(--dd-primary);border-color:var(--dd-primary)">1</a>
            </li>
            <li class="page-item">
                <a class="page-link rounded" href="#" style="border-color:rgba(213,195,184,.3);color:var(--dd-primary)">
                    <span class="material-symbols-outlined" style="font-size:.9rem">chevron_right</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

</div><!-- /.admin-content -->
</div><!-- /#admin-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
