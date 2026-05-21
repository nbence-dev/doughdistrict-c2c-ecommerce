<?php
$pageTitle = 'Manage Users';
include __DIR__ . '/layout.php';

// $users is populated by admin_controller.php
// Each row: id, name, email, role, is_active, created_at
$users = $users ?? [];
$flash = get_flash();

function role_badge(string $role): string
{
    $map = [
        'admin' => 'badge-role-admin',
        'seller' => 'badge-role-seller',
        'buyer' => 'badge-role-buyer',
    ];
    $cls = $map[$role] ?? 'badge-role-buyer';
    return '<span class="badge-role ' . $cls . '">' . htmlspecialchars(ucfirst($role)) . '</span>';
}

function status_badge(int $isActive): string
{
    if ($isActive) {
        return '<span class="badge-status badge-active">
                    <span class="material-symbols-outlined" style="font-size:.7rem">circle</span> Active
                </span>';
    }
    return '<span class="badge-status badge-inactive">
                <span class="material-symbols-outlined" style="font-size:.7rem">block</span> Inactive
            </span>';
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
        <h2 class="page-heading mb-1">Manage Users</h2>
        <p class="page-subheading mb-0">Oversee the community of bakers and buyers within DoughDistrict.</p>
    </div>
    <button class="btn btn-dd d-flex align-items-center gap-2 px-4 py-2"
            data-bs-toggle="modal" data-bs-target="#inviteModal">
        <span class="material-symbols-outlined" style="font-size:1rem">add</span> Invite New Admin
    </button>
</div>

<!-- ── Filter Chips ── -->
<?php $f = $filter ?? 'all'; ?>
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="?filter=all"      class="filter-chip <?= $f === 'all'      ? 'chip-active' : '' ?>">All</a>
    <a href="?filter=seller"   class="filter-chip <?= $f === 'seller'   ? 'chip-active' : '' ?>">Sellers</a>
    <a href="?filter=buyer"    class="filter-chip <?= $f === 'buyer'    ? 'chip-active' : '' ?>">Buyers</a>
    <a href="?filter=admin"    class="filter-chip <?= $f === 'admin'    ? 'chip-active' : '' ?>">Admins</a>
    <a href="?filter=inactive" class="filter-chip <?= $f === 'inactive' ? 'chip-active' : '' ?>">Inactive</a>
</div>

<!-- ── Users Table ── -->
<p class="scroll-hint">← Scroll to see more →</p>
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-dd mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th>Role</th>
                    <th class="d-none d-sm-table-cell">Status</th>
                    <th class="d-none d-lg-table-cell">Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:var(--dd-outline)">
                            <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem">group</span>
                            No users found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr data-role="<?= htmlspecialchars($u['role']) ?>" data-active="<?= (int) $u['is_active'] ?>">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=f5f4ec&color=6f4627&bold=true&size=64"
                                        alt="<?= htmlspecialchars($u['name']) ?>" class="rounded-circle"
                                        style="width:36px;height:36px;object-fit:cover;">
                                    <span class="fw-semibold" style="color:var(--dd-on-surface)">
                                        <?= htmlspecialchars($u['name']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell" style="color:var(--dd-on-surface-var)">
                                <?= htmlspecialchars($u['email']) ?>
                            </td>
                            <td><?= role_badge($u['role']) ?></td>
                            <td class="d-none d-sm-table-cell"><?= status_badge((int) $u['is_active']) ?></td>
                            <td class="d-none d-lg-table-cell" style="color:var(--dd-outline);white-space:nowrap">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Change role (buyer→admin only; sellers and admins locked) -->
                                    <?php if ($u['role'] === 'buyer'): ?>
                                        <div class="dropdown">
                                            <button class="action-btn action-btn-edit dropdown-toggle" data-bs-toggle="dropdown"
                                                style="border-radius:.45rem" title="Change role">
                                                <span class="material-symbols-outlined">manage_accounts</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                                style="font-size:.8125rem">
                                                <li><span class="dropdown-item-text text-muted"
                                                        style="font-size:.675rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em">Set
                                                        Role</span></li>
                                                <li>
                                                    <form method="POST" action="<?= BASE_URL ?>admin/users/role">
                                                        <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                                        <input type="hidden" name="role" value="admin">
                                                        <button class="dropdown-item">Make Admin</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php endif; /* admins and sellers: no role button */ ?>
                                    <!-- Toggle active/inactive (cannot self-deactivate) -->
                                    <?php if ($u['id'] !== current_user()['id']): ?>
                                    <form method="POST" action="<?= BASE_URL ?>admin/users/toggle">
                                        <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                        <?php if ($u['is_active']): ?>
                                            <button type="submit" class="action-btn action-btn-ban" title="Deactivate">
                                                <span class="material-symbols-outlined">block</span>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="action-btn action-btn-restore" title="Reactivate">
                                                <span class="material-symbols-outlined">check_circle</span>
                                            </button>
                                        <?php endif; ?>
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
<?php
$page       = $page       ?? 1;
$totalPages = $totalPages ?? 1;
$perPage    = $perPage    ?? PHP_INT_MAX;
$total      = $total      ?? count($users);
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
        Showing <span id="userCount">
            <?php if ($total === 0): ?>
                0 users
            <?php elseif ($totalPages <= 1): ?>
                <?= $total ?> user<?= $total !== 1 ? 's' : '' ?>
            <?php else: ?>
                <?= $showFrom ?>–<?= $showTo ?> of <?= $total ?> user<?= $total !== 1 ? 's' : '' ?>
            <?php endif; ?>
        </span>
    </p>
    <?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination pagination-sm mb-0" style="gap:.25rem">
            <!-- Prev -->
            <?php $fp = ($f !== 'all') ? 'filter=' . urlencode($f) . '&' : ''; ?>
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link rounded" href="?<?= $fp ?>page=<?= max(1, $page - 1) ?>"
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
                        <a class="page-link rounded" href="?<?= $fp ?>page=<?= $pn ?>"
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
                <a class="page-link rounded" href="?<?= $fp ?>page=<?= min($totalPages, $page + 1) ?>"
                    style="border-color:rgba(213,195,184,.3);color:var(--dd-primary)">
                    <span class="material-symbols-outlined" style="font-size:.9rem">chevron_right</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- ── Invite Admin Modal ── -->
<div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="inviteModalLabel" style="color:var(--dd-on-surface)">
                    Invite New Admin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>admin/users/invite" data-validate>
                <div class="modal-body pt-2">
                    <p class="small mb-4" style="color:var(--dd-outline)">
                        A temporary password will be generated and emailed to this person.
                        They will be prompted to change it on first login.
                    </p>
                    <div class="mb-3">
                        <label for="invite_name" class="form-label small fw-semibold" style="color:var(--dd-on-surface-var)">Full Name</label>
                        <input type="text" id="invite_name" name="name" class="form-control"
                            placeholder="Jane Dough" required autocomplete="off">
                    </div>
                    <div class="mb-1">
                        <label for="invite_email" class="form-label small fw-semibold" style="color:var(--dd-on-surface-var)">Email Address</label>
                        <input type="email" id="invite_email" name="email" class="form-control"
                            placeholder="admin@example.com" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dd btn-sm px-4">Send Invite</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div><!-- /.admin-content -->
</div><!-- /#admin-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= JS_URL ?>validation.js"></script>
</body>

</html>