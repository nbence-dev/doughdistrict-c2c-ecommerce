<?php
/**
 * Buyer layout — dark sidebar matching admin style.
 * Include at the TOP of every buyer view.
 * Opens: <div id="buyer-main"> — each view MUST close it + add scripts + </body></html>
 */
$pageTitle = $pageTitle ?? 'DoughDistrict';
$currentUser = current_user();
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

function buyer_nav_link(string $path, string $icon, string $label, string $currentPath): string
{
    $active = ($currentPath === $path) ? 'active' : '';
    if ($active === '' && $path !== '' && strpos($currentPath, $path) === 0) {
        $active = 'active';
    }
    $url = BASE_URL . $path;
    return <<<HTML
    <a href="{$url}" class="nav-link {$active}">
        <span class="material-symbols-outlined">{$icon}</span> {$label}
    </a>
    HTML;
}

$_flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — DoughDistrict</title>
    <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Be+Vietnam+Pro:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ── Design tokens ─────────────────────────────────── */
        :root {
            --dd-primary: #6f4627;
            --dd-primary-grad: linear-gradient(135deg, #6f4627 0%, #8b5e3c 100%);
            --dd-secondary: #924c00;
            --dd-tertiary: #495523;
            --dd-error: #ba1a1a;
            --dd-surface: #fbf9f1;
            --dd-surface-low: #f5f4ec;
            --dd-surface-cont: #f0eee6;
            --dd-surface-card: #ffffff;
            --dd-on-surface: #1b1c17;
            --dd-on-surface-var: #51443c;
            --dd-outline: #83746b;
            --dd-outline-var: #d5c3b8;
            --dd-on-primary: #fff;
        }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: var(--dd-surface);
            color: var(--dd-on-surface);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: -4px;
            font-size: 1.2em;
        }

        /* ── Sidebar ────────────────────────────────────────── */
        #buyer-sidebar {
            width: 256px;
            min-height: 100vh;
            background-color: #1c1917;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
            z-index: 1040;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        #buyer-sidebar .brand {
            padding: 0 1.5rem 2rem;
        }

        #buyer-sidebar .brand h1 {
            color: #fef3c7;
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #buyer-sidebar .brand p {
            color: #78716c;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            margin: 0;
            font-weight: 600;
        }

        #buyer-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            margin: 0.1rem 0.5rem;
            border-radius: 0.5rem;
            color: #a8a29e;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        #buyer-sidebar .nav-link:hover {
            color: #f5f5f4;
            background-color: rgba(68, 64, 60, 0.45);
        }

        #buyer-sidebar .nav-link.active {
            background-color: #292524;
            color: #fcd34d;
        }

        #buyer-sidebar .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #292524;
            padding-top: 0.75rem;
        }

        #buyer-sidebar .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1.25rem 0;
        }

        #buyer-sidebar .sidebar-user img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #3d3835;
            flex-shrink: 0;
        }

        #buyer-sidebar .sidebar-user .user-name {
            color: #fef9f0;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.2;
        }

        #buyer-sidebar .sidebar-user .user-role {
            color: #78716c;
            font-size: 0.675rem;
            line-height: 1;
        }

        /* ── Mobile topbar ──────────────────────────────────── */
        #buyer-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background-color: rgba(251, 249, 241, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(213, 195, 184, 0.35);
            align-items: center;
            padding: 0 1rem;
            gap: 0.75rem;
            z-index: 1039;
        }

        #buyer-topbar .topbar-brand {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--dd-primary);
            font-size: 1rem;
            flex: 1;
            text-decoration: none;
        }

        #buyer-topbar .icon-btn {
            background: none;
            border: none;
            color: var(--dd-primary);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s;
        }

        #buyer-topbar .icon-btn:hover {
            background-color: var(--dd-surface-low);
        }

        /* ── Sidebar overlay ────────────────────────────────── */
        #sidebar-overlay {
            display: none;
        }

        /* ── Main canvas ────────────────────────────────────── */
        #buyer-main {
            margin-left: 256px;
            min-height: 100vh;
        }

        /* ── Reusable ───────────────────────────────────────── */
        .btn-dd-primary {
            background: var(--dd-primary-grad);
            color: #fff;
            border: none;
            border-radius: 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-dd-primary:hover {
            box-shadow: 0 6px 20px rgba(111, 70, 39, 0.3);
            transform: translateY(-1px);
            color: #fff;
        }

        /* ── Horizontal scroll tables ───────────────────────── */
        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .table-scroll::-webkit-scrollbar {
            height: 5px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: var(--dd-surface-low);
            border-radius: 3px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: var(--dd-outline-var);
            border-radius: 3px;
        }

        /* ── Mobile breakpoint ──────────────────────────────── */
        @media (max-width: 991.98px) {
            #buyer-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            body.sidebar-open #buyer-sidebar {
                transform: translateX(0);
            }

            #sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 1039;
            }

            body.sidebar-open #sidebar-overlay {
                display: block;
            }

            #buyer-topbar {
                display: flex;
            }

            #buyer-main {
                margin-left: 0;
                padding-top: 56px;
            }
        }
    </style>
</head>

<body>

    <!-- ════════ Sidebar ════════ -->
    <aside id="buyer-sidebar">
        <div class="brand">
            <h1>DoughDistrict</h1>
            <p>Marketplace</p>
        </div>

        <nav>
            <?= buyer_nav_link('browse', 'explore', 'Browse', $currentPath) ?>
            <?= buyer_nav_link('cart', 'shopping_cart', 'Cart', $currentPath) ?>
            <?= buyer_nav_link('orders', 'receipt_long', 'My Orders', $currentPath) ?>
            <div style="height:1px;background:rgba(255,255,255,0.07);margin:.5rem .75rem;"></div>
            <?php if ($currentUser && $currentUser['role'] === 'seller'): ?>
                <?= buyer_nav_link('seller/dashboard', 'storefront', 'Seller Portal', $currentPath) ?>
            <?php elseif ($currentUser && $currentUser['role'] === 'admin'): ?>
                <?= buyer_nav_link('admin/users', 'admin_panel_settings', 'Admin Panel', $currentPath) ?>
            <?php else: ?>
                <?= buyer_nav_link('seller/onboard', 'add_business', 'Become a Seller', $currentPath) ?>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>logout" class="nav-link">
                <span class="material-symbols-outlined">logout</span> Sign Out
            </a>
            <?php if ($currentUser): ?>
                <a href="<?= BASE_URL ?>account/profile" class="sidebar-user text-decoration-none">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['name'] ?? 'User') ?>&background=6f4627&color=fff&bold=true&size=64"
                        alt="Avatar">
                    <div>
                        <div class="user-name"><?= htmlspecialchars($currentUser['name'] ?? '') ?></div>
                        <div class="user-role"><?= ucfirst($currentUser['role'] ?? 'Buyer') ?></div>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <div id="sidebar-overlay"></div>

    <!-- ════════ Mobile topbar ════════ -->
    <header id="buyer-topbar">
        <button class="icon-btn" id="sidebar-toggle" aria-label="Open menu">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="<?= BASE_URL ?>browse" class="topbar-brand">🥐 DoughDistrict</a>
        <a href="<?= BASE_URL ?>cart" class="icon-btn" title="Cart">
            <span class="material-symbols-outlined">shopping_cart</span>
        </a>
    </header>

    <script>
        (function () {
            var toggle = document.getElementById('sidebar-toggle');
            var overlay = document.getElementById('sidebar-overlay');
            if (toggle) toggle.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-open');
            });
            if (overlay) overlay.addEventListener('click', function () {
                document.body.classList.remove('sidebar-open');
            });
        })();
    </script>

    <!-- ════════ Main Canvas ════════ -->
    <div id="buyer-main">
        <?php if ($_flash): ?>
            <div class="container pt-3">
                <div class="alert alert-<?= htmlspecialchars($_flash['type']) ?> alert-dismissible fade show py-2 small mb-0"
                    role="alert">
                    <?= htmlspecialchars($_flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                        style="top:50%;transform:translateY(-50%);"></button>
                </div>
            </div>
        <?php endif; ?>