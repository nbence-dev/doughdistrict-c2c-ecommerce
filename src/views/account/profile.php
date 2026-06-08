<?php
$pageTitle = 'My Profile';
$user = current_user();
if ($user['role'] === 'admin') {
    include __DIR__ . '/../admin/layout.php';
} else {
    include __DIR__ . '/../buyer/layout.php';
}
?>

<style>
    .profile-card {
        background: #fff;
        border: 1px solid var(--dd-outline-var);
        border-radius: 1rem;
        padding: 1.75rem;
    }
    .dd-field {
        background: var(--dd-surface-low);
        border: none;
        border-radius: .75rem;
        padding: .75rem 1rem;
        width: 100%;
        color: var(--dd-on-surface);
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: .9375rem;
        transition: box-shadow .15s;
    }
    .dd-field:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(111, 70, 39, 0.25);
        background: #fff;
    }
    .dd-label {
        display: block;
        font-size: .8125rem;
        font-weight: 600;
        color: var(--dd-on-surface-var);
        margin-bottom: .375rem;
    }
    .pw-toggle {
        position: absolute;
        right: .875rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        color: var(--dd-outline);
        cursor: pointer;
        line-height: 1;
    }
    .pw-toggle:hover { color: var(--dd-primary); }
</style>

<main class="container py-5 mt-2">

    <div class="mb-5 d-flex align-items-center gap-4">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name'] ?? 'User') ?>&background=6f4627&color=fff&bold=true&size=128"
             alt="Your avatar"
             style="width:72px;height:72px;border-radius:50%;flex-shrink:0;">
        <div>
            <h1 class="fw-bold mb-0" style="font-family:'Plus Jakarta Sans',sans-serif;">
                <?= htmlspecialchars($user['name']) ?>
            </h1>
            <p class="mb-0 small" style="color:var(--dd-on-surface-var);">
                <?= ucfirst($user['role']) ?> · <?= htmlspecialchars($user['email']) ?>
            </p>
        </div>
    </div>

    <div class="row g-4">

        <!-- ── Edit Profile ────────────────────────────── -->
        <div class="col-lg-6">
            <div class="profile-card h-100">
                <h2 class="h5 fw-bold mb-4">Edit Profile</h2>

                <form method="POST" action="<?= BASE_URL ?>account/profile" novalidate data-validate>
                    <input type="hidden" name="update_profile" value="1">

                    <div class="mb-3">
                        <label for="name" class="dd-label">Full Name</label>
                        <input type="text" id="name" name="name" class="dd-field"
                               value="<?= htmlspecialchars($user['name']) ?>"
                               required minlength="2" autocomplete="name">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="dd-label">Email Address</label>
                        <input type="email" id="email" name="email" class="dd-field"
                               value="<?= htmlspecialchars($user['email']) ?>"
                               required autocomplete="email">
                    </div>

                    <button type="submit" class="btn btn-dd-primary px-4 py-2">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- ── Change Password ────────────────────────── -->
        <div class="col-lg-6">
            <div class="profile-card h-100">
                <h2 class="h5 fw-bold mb-4">Change Password</h2>

                <form method="POST" action="<?= BASE_URL ?>account/profile" novalidate data-validate>
                    <input type="hidden" name="update_password" value="1">

                    <div class="mb-3">
                        <label for="current_password" class="dd-label">Current Password</label>
                        <div class="position-relative">
                            <input type="password" id="current_password" name="current_password"
                                   class="dd-field pe-5" required autocomplete="current-password"
                                   placeholder="Your current password">
                            <button type="button" class="pw-toggle"
                                    onclick="togglePw('current_password', this)" aria-label="Show/hide">
                                <?= eyeIcon() ?>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="dd-label">New Password</label>
                        <div class="position-relative">
                            <input type="password" id="new_password" name="new_password"
                                   class="dd-field pe-5" required minlength="8"
                                   autocomplete="new-password" placeholder="At least 8 characters">
                            <button type="button" class="pw-toggle"
                                    onclick="togglePw('new_password', this)" aria-label="Show/hide">
                                <?= eyeIcon() ?>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="dd-label">Confirm New Password</label>
                        <div class="position-relative">
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="dd-field pe-5" required
                                   data-match="new_password" data-match-msg="Passwords do not match."
                                   autocomplete="new-password" placeholder="••••••••">
                            <button type="button" class="pw-toggle"
                                    onclick="togglePw('confirm_password', this)" aria-label="Show/hide">
                                <?= eyeIcon() ?>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dd-primary px-4 py-2">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

    </div>

</main>

<?php function eyeIcon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';
} ?>

<script src="<?= JS_URL ?>validation.js"></script>
<script>
const eyeSvg = '<?= addslashes(eyeIcon()) ?>';
const eyeSlashSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709z"/><path d="M13.646 14.354l-12-12 .708-.708 12 12-.707.707z"/></svg>';

function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    const hidden = input.type === 'password';
    input.type = hidden ? 'text' : 'password';
    btn.innerHTML = hidden ? eyeSlashSvg : eyeSvg;
}
</script>

<?php if ($user['role'] === 'admin'): ?>
</div><!-- /.admin-content -->
</div><!-- /#admin-main -->
<?php else: ?>
</div><!-- /#buyer-main -->
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
