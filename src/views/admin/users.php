<?php
$pageTitle = 'Manage Users';
include __DIR__ . '/layout.php';

// $users is populated by admin_controller.php
// Each row: id, name, email, role, is_active, created_at
$users = $users ?? [];

function role_badge(string $role): string {
    $map = [
        'admin'  => 'badge-role-admin',
        'seller' => 'badge-role-seller',
        'buyer'  => 'badge-role-buyer',
    ];
    $cls = $map[$role] ?? 'badge-role-buyer';
    return '<span class="badge-role ' . $cls . '">' . htmlspecialchars(ucfirst($role)) . '</span>';
}

function status_badge(int $isActive): string {
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

<!-- ── Page Header ── -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
    <div>
        <h2 class="page-heading mb-1">Manage Users</h2>
        <p class="page-subheading mb-0">Oversee the community of bakers and buyers within DoughDistrict.</p>
    </div>
    <button class="btn btn-dd d-flex align-items-center gap-2 px-4 py-2">
        <span class="material-symbols-outlined" style="font-size:1rem">add</span> Invite New User
    </button>
</div>

<!-- ── Filter Chips ── -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <button class="filter-chip chip-active">All</button>
    <button class="filter-chip">Sellers</button>
    <button class="filter-chip">Buyers</button>
    <button class="filter-chip">Admins</button>
    <button class="filter-chip">Inactive</button>
</div>

<!-- ── Users Table ── -->
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
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=f5f4ec&color=6f4627&bold=true&size=64"
                                 alt="<?= htmlspecialchars($u['name']) ?>"
                                 class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                            <span class="fw-semibold" style="color:var(--dd-on-surface)">
                                <?= htmlspecialchars($u['name']) ?>
                            </span>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell" style="color:var(--dd-on-surface-var)"><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= role_badge($u['role']) ?></td>
                    <td class="d-none d-sm-table-cell"><?= status_badge((int)$u['is_active']) ?></td>
                    <td class="d-none d-lg-table-cell" style="color:var(--dd-outline);white-space:nowrap">
                        <?= date('d M Y', strtotime($u['created_at'])) ?>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end gap-1">
                            <!-- Change role -->
                            <div class="dropdown">
                                <button class="action-btn action-btn-edit dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        style="border-radius:.45rem"
                                        title="Change role">
                                    <span class="material-symbols-outlined">manage_accounts</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:.8125rem">
                                    <li><span class="dropdown-item-text text-muted" style="font-size:.675rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em">Set Role</span></li>
                                    <li>
                                        <form method="POST" action="<?= BASE_URL ?>admin/users/role">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                            <input type="hidden" name="role" value="admin">
                                            <button class="dropdown-item">Admin</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="<?= BASE_URL ?>admin/users/role">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                            <input type="hidden" name="role" value="seller">
                                            <button class="dropdown-item">Seller</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="<?= BASE_URL ?>admin/users/role">
                                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                            <input type="hidden" name="role" value="buyer">
                                            <button class="dropdown-item">Buyer</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <!-- Toggle active/inactive -->
                            <form method="POST" action="<?= BASE_URL ?>admin/users/toggle">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
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
        Showing <?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?>
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
