<?php
$pageTitle = 'Manage Categories';
include __DIR__ . '/layout.php';

// $categories populated by admin_controller.php
// Each row: id, name, slug, product_count
$categories = $categories ?? [];
$totalProducts = $totalProducts ?? 0;
$flash = get_flash();
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
<div class="mb-4">
    <h2 class="page-heading mb-1">Manage Categories</h2>
    <p class="page-subheading mb-0">Organise the bakery catalogue for buyers and sellers.</p>
</div>

<!-- ── Quick Create + Stats ── -->
<div class="row g-4 mb-5">
    <!-- Quick Create form -->
    <div class="col-lg-8">
        <div class="table-card p-4">
            <p class="mb-3 text-uppercase fw-bold"
                style="font-size:.7rem;letter-spacing:.14em;color:var(--dd-tertiary)">Quick Create</p>
            <form method="POST" action="<?= BASE_URL ?>admin/categories/create"
                class="d-flex flex-column flex-md-row gap-3 align-items-md-end">
                <div class="flex-grow-1 min-width-0" style="min-width:0">
                    <label class="form-label small fw-semibold" style="color:var(--dd-outline)">Category Name</label>
                    <input type="text" name="name" id="quickCreateName" class="form-control" placeholder="e.g. Artisanal Sourdough" required
                        style="background:var(--dd-surface-low);border:none;border-radius:.5rem;font-size:.875rem;color:var(--dd-on-surface)">
                    <div id="quickCreateNameError" class="text-danger mt-1" style="font-size:.8rem;display:none"></div>
                </div>
                <div class="flex-shrink-0" style="min-width:0;max-width:160px">
                    <label class="form-label small fw-semibold" style="color:var(--dd-outline)">Slug <span
                            class="text-muted">(auto)</span></label>
                    <input type="text" name="slug" id="quickCreateSlug" class="form-control" placeholder="auto-generated" readonly
                        style="background:var(--dd-surface-low);border:none;border-radius:.5rem;font-size:.875rem;color:var(--dd-on-surface);opacity:.65;cursor:default;overflow:hidden;text-overflow:ellipsis">
                </div>
                <button type="submit" class="btn btn-dd d-flex align-items-center gap-2 px-4 py-2 text-nowrap">
                    <span class="material-symbols-outlined" style="font-size:1rem">add</span> Save Category
                </button>
            </form>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="col-lg-4">
        <div class="row g-3">
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">grid_view</span>
                    </div>
                    <div>
                        <div class="stat-label">Categories</div>
                        <div class="stat-value"><?= count($categories) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    <div>
                        <div class="stat-label">Products</div>
                        <div class="stat-value"><?= $totalProducts ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Categories Table ── -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0 fw-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:var(--dd-primary)">
        Active Ledger
    </h3>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-dd mb-0">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th class="d-none d-md-table-cell">Slug</th>
                    <th class="d-none d-sm-table-cell">Products</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5" style="color:var(--dd-outline)">
                            <span class="material-symbols-outlined d-block mb-2" style="font-size:2.5rem">category</span>
                            No categories yet. Create one above.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <!-- Name + icon -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                                        style="width:38px;height:38px;background:rgba(253,160,85,0.15);color:var(--dd-secondary)">
                                        <span class="material-symbols-outlined" style="font-size:1.15rem">bakery_dining</span>
                                    </div>
                                    <span class="fw-bold" style="color:var(--dd-on-surface)">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </span>
                                </div>
                            </td>
                            <!-- Slug -->
                            <td class="d-none d-md-table-cell">
                                <code class="small px-2 py-1 rounded"
                                    style="background:var(--dd-surface-low);color:var(--dd-on-surface-var);font-size:.8rem">
                                                                    <?= htmlspecialchars($cat['slug'] ?? strtolower(str_replace(' ', '-', $cat['name']))) ?>
                                                                </code>
                            </td>
                            <!-- Product count -->
                            <td class="d-none d-sm-table-cell">
                                <span class="badge-status badge-approved">
                                    <?= (int) ($cat['product_count'] ?? 0) ?> Items
                                </span>
                            </td>
                            <!-- Actions -->
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Edit: opens modal -->
                                    <button type="button" class="action-btn action-btn-edit" title="Edit" data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal" data-id="<?= (int) $cat['id'] ?>"
                                        data-name="<?= htmlspecialchars($cat['name']) ?>"
                                        data-slug="<?= htmlspecialchars($cat['slug'] ?? '') ?>">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <!-- Delete -->
                                    <button type="button" class="action-btn action-btn-delete" title="Delete"
                                        data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"
                                        data-id="<?= (int) $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Edit Category Modal ── -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius:.75rem;background:var(--dd-surface-card)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:var(--dd-primary)">
                    Edit Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>admin/categories/update">
                <div class="modal-body py-3">
                    <input type="hidden" name="category_id" id="editCategoryId">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color:var(--dd-outline)">Category
                            Name</label>
                        <input type="text" name="name" id="editCategoryName" class="form-control" required
                            style="background:var(--dd-surface-low);border:none;border-radius:.5rem">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-semibold" style="color:var(--dd-outline)">Slug <span class="text-muted">(auto)</span></label>
                        <input type="text" name="slug" id="editCategorySlug" class="form-control" readonly
                            style="background:var(--dd-surface-low);border:none;border-radius:.5rem;opacity:.65;cursor:default">
                        <div id="editCategoryNameError" class="text-danger mt-1" style="font-size:.8rem;display:none"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm px-4" data-bs-dismiss="modal"
                        style="border-radius:2rem;color:var(--dd-outline);border:1px solid rgba(213,195,184,.4)">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-dd btn-sm px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Delete Category Modal ── -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius:.75rem;background:var(--dd-surface-card)">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:var(--dd-primary)">
                    Delete Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-0 small" style="color:var(--dd-on-surface-var)">
                    Are you sure you want to delete <strong id="deleteCategoryName"></strong>?
                    This cannot be undone.
                </p>
            </div>
            <form method="POST" action="<?= BASE_URL ?>admin/categories/delete">
                <input type="hidden" name="category_id" id="deleteCategoryId">
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm px-4" data-bs-dismiss="modal"
                        style="border-radius:2rem;color:var(--dd-outline);border:1px solid rgba(213,195,184,.4)">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold text-white"
                        style="border-radius:2rem;background:#c0392b;border:none">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div><!-- /.admin-content -->
</div><!-- /#admin-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Existing category names for duplicate checking (id + lowercased name)
    const existingCategories = <?= json_encode(array_map(fn($c) => ['id' => (int)$c['id'], 'name' => strtolower(trim($c['name']))], $categories)) ?>;

    function slugify(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-');
    }

    // ── Quick Create: live slug + duplicate check ──
    const quickNameInput = document.getElementById('quickCreateName');
    const quickSlugInput = document.getElementById('quickCreateSlug');
    const quickNameError = document.getElementById('quickCreateNameError');

    quickNameInput.addEventListener('input', function () {
        quickSlugInput.value = slugify(this.value);

        const val = this.value.trim().toLowerCase();
        const duplicate = val && existingCategories.some(c => c.name === val);
        quickNameError.textContent = duplicate ? 'A category with this name already exists.' : '';
        quickNameError.style.display = duplicate ? '' : 'none';
    });

    quickNameInput.closest('form').addEventListener('submit', function (e) {
        const val = quickNameInput.value.trim().toLowerCase();
        if (val && existingCategories.some(c => c.name === val)) {
            e.preventDefault();
            quickNameError.textContent = 'A category with this name already exists.';
            quickNameError.style.display = '';
            quickNameInput.focus();
        }
    });

    // ── Edit Modal: populate fields, live slug + duplicate check ──
    const editNameInput = document.getElementById('editCategoryName');
    const editSlugInput = document.getElementById('editCategorySlug');
    const editNameError = document.getElementById('editCategoryNameError');

    document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('editCategoryId').value = btn.dataset.id;
        editNameInput.value = btn.dataset.name;
        editSlugInput.value = btn.dataset.slug;
        editNameError.style.display = 'none';
    });

    editNameInput.addEventListener('input', function () {
        editSlugInput.value = slugify(this.value);

        const val = this.value.trim().toLowerCase();
        const currentId = parseInt(document.getElementById('editCategoryId').value);
        const duplicate = val && existingCategories.some(c => c.name === val && c.id !== currentId);
        editNameError.textContent = duplicate ? 'A category with this name already exists.' : '';
        editNameError.style.display = duplicate ? '' : 'none';
    });

    editNameInput.closest('form').addEventListener('submit', function (e) {
        const val = editNameInput.value.trim().toLowerCase();
        const currentId = parseInt(document.getElementById('editCategoryId').value);
        if (val && existingCategories.some(c => c.name === val && c.id !== currentId)) {
            e.preventDefault();
            editNameError.textContent = 'A category with this name already exists.';
            editNameError.style.display = '';
            editNameInput.focus();
        }
    });

    // ── Delete Modal: populate fields ──
    document.getElementById('deleteCategoryModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('deleteCategoryId').value = btn.dataset.id;
        document.getElementById('deleteCategoryName').textContent = btn.dataset.name;
    });
</script>
</body>

</html>