<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>

<?php $flash = get_flash(); ?>

<style>
.profile-card { background: #fff; border-radius: 1rem; box-shadow: 0 4px 16px rgba(48,49,44,.06); }
.section-label { font-size: .65rem; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; color: var(--dd-outline); }
</style>

<main class="container py-5">

    <header class="mb-5">
        <h1 class="font-headline fw-bold mb-1" style="color: var(--dd-primary);">Shop Profile</h1>
        <p class="mb-0" style="color: var(--dd-on-surface-var);">Update your shop details and collection address.</p>
    </header>

    <?php if ($flash): ?>
    <?php $err = in_array($flash['type'], ['danger','error']); ?>
    <div class="mb-4 px-4 py-3 rounded-4 d-flex align-items-center gap-2
        <?= $err ? 'bg-danger bg-opacity-10 text-danger' : '' ?>"
        style="<?= !$err ? 'background: var(--dd-surface-low); color: var(--dd-on-surface)' : '' ?>">
        <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
        <span class="small fw-semibold"><?= htmlspecialchars($flash['message']) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>seller/profile">
        <div class="row g-4">

            <!-- Left: Shop Info -->
            <div class="col-lg-6">
                <div class="profile-card p-4 h-100">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="material-symbols-outlined" style="color: var(--dd-primary);">storefront</span>
                        <h2 class="fw-bold mb-0 fs-6" style="color: var(--dd-on-surface);">Shop Info</h2>
                    </div>

                    <div class="mb-3">
                        <label for="shop_name" class="form-label section-label">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name"
                               class="form-control" style="border-radius: .75rem;"
                               value="<?= htmlspecialchars($sellerProfile['shop_name']) ?>" required>
                    </div>

                    <div>
                        <label for="bio" class="form-label section-label">Bio</label>
                        <textarea id="bio" name="bio" rows="5"
                                  class="form-control" style="border-radius: .75rem; resize: none;"
                                  required><?= htmlspecialchars($sellerProfile['bio']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Collection Address -->
            <div class="col-lg-6">
                <div class="profile-card p-4 h-100">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="material-symbols-outlined" style="color: var(--dd-primary);">local_shipping</span>
                        <h2 class="fw-bold mb-0 fs-6" style="color: var(--dd-on-surface);">Collection Address</h2>
                    </div>

                    <div class="mb-3">
                        <label for="street_address" class="form-label section-label">Street Address</label>
                        <input type="text" id="street_address" name="street_address"
                               class="form-control" style="border-radius: .75rem;"
                               placeholder="e.g. 12 Bakers Lane"
                               value="<?= htmlspecialchars($sellerProfile['street_address'] ?? '') ?>">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="local_area" class="form-label section-label">Suburb</label>
                            <input type="text" id="local_area" name="local_area"
                                   class="form-control" style="border-radius: .75rem;"
                                   placeholder="e.g. Sandton"
                                   value="<?= htmlspecialchars($sellerProfile['local_area'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <label for="city" class="form-label section-label">City</label>
                            <input type="text" id="city" name="city"
                                   class="form-control" style="border-radius: .75rem;"
                                   placeholder="e.g. Johannesburg"
                                   value="<?= htmlspecialchars($sellerProfile['city'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label for="zone" class="form-label section-label">Province</label>
                            <select id="zone" name="zone" class="form-select" style="border-radius: .75rem;">
                                <option value="">Select province</option>
                                <?php foreach (['Gauteng','Western Cape','Eastern Cape','KwaZulu-Natal','Limpopo','Mpumalanga','North West','Northern Cape','Free State'] as $p): ?>
                                    <option value="<?= $p ?>" <?= ($sellerProfile['zone'] ?? '') === $p ? 'selected' : '' ?>>
                                        <?= $p ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="postal_code" class="form-label section-label">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code"
                                   class="form-control" style="border-radius: .75rem;"
                                   placeholder="e.g. 2196" maxlength="10"
                                   value="<?= htmlspecialchars($sellerProfile['postal_code'] ?? '') ?>">
                        </div>
                    </div>

                    <div>
                        <label for="mobile_number" class="form-label section-label">Mobile Number <span class="text-muted fw-normal">(for courier)</span></label>
                        <input type="tel" id="mobile_number" name="mobile_number"
                               class="form-control" style="border-radius: .75rem;"
                               placeholder="e.g. 0821234567"
                               value="<?= htmlspecialchars($sellerProfile['mobile_number'] ?? '') ?>">
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4 d-flex gap-3">
            <button type="submit" class="btn btn-dd-primary px-5">Save Changes</button>
            <a href="<?= BASE_URL ?>seller/dashboard" class="btn" style="border: 1px solid var(--dd-outline-var); color: var(--dd-primary);">Cancel</a>
        </div>

    </form>

</main>

<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>
