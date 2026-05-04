<?php
/**
 * Admin layout — include at the TOP of every admin view.
 * Expects: $pageTitle (string), current_user() available via auth.php
 * Opens: <div id="admin-main"><div class="admin-content">
 * Each view MUST close those divs and include the closing scripts.
 */
$pageTitle = $pageTitle ?? 'Admin';
$currentUser = current_user();
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

function admin_nav_link(string $path, string $icon, string $label, string $currentPath): string
{
    $active = ($currentPath === $path) ? 'active' : '';
    $url = BASE_URL . $path;
    return <<<HTML
    <a href="{$url}" class="nav-link {$active}">
        <span class="material-symbols-outlined">{$icon}</span> {$label}
    </a>
    HTML;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — DoughDistrict Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Be+Vietnam+Pro:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ── Design tokens ───────────────────────────── */
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
        }

        /* ── Base ────────────────────────────────────── */
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

        /* ── Sidebar ─────────────────────────────────── */
        #admin-sidebar {
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

        #admin-sidebar .brand {
            padding: 0 1.5rem 2rem;
        }

        #admin-sidebar .brand h1 {
            color: #fef3c7;
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        #admin-sidebar .brand p {
            color: #78716c;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            margin: 0;
            font-weight: 600;
        }

        #admin-sidebar .nav-link {
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

        #admin-sidebar .nav-link:hover {
            color: #f5f5f4;
            background-color: rgba(68, 64, 60, 0.45);
        }

        #admin-sidebar .nav-link.active {
            background-color: #292524;
            color: #fcd34d;
        }

        #admin-sidebar .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #292524;
            padding-top: 0.75rem;
        }

        #admin-sidebar .admin-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1.25rem 0;
        }

        #admin-sidebar .admin-user img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #3d3835;
            flex-shrink: 0;
        }

        #admin-sidebar .admin-user .admin-name {
            color: #fef9f0;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.2;
        }

        #admin-sidebar .admin-user .admin-role {
            color: #78716c;
            font-size: 0.675rem;
            line-height: 1;
        }

        /* ── Topbar ──────────────────────────────────── */
        #admin-topbar {
            position: fixed;
            top: 0;
            left: 256px;
            right: 0;
            height: 64px;
            background-color: rgba(251, 249, 241, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(213, 195, 184, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 1039;
        }

        #admin-topbar .search-wrap {
            position: relative;
            width: 300px;
        }

        #admin-topbar .search-wrap .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dd-outline);
            font-size: 1rem;
            pointer-events: none;
        }

        #admin-topbar .search-wrap input {
            background-color: var(--dd-surface-low);
            border: none;
            border-radius: 2rem;
            padding: 0.4rem 1rem 0.4rem 2.4rem;
            font-size: 0.875rem;
            color: var(--dd-on-surface);
            width: 100%;
        }

        #admin-topbar .search-wrap input:focus {
            outline: none;
            box-shadow: 0 0 0 1px rgba(111, 70, 39, 0.25);
            background-color: var(--dd-surface-card);
        }

        #admin-topbar .icon-btn {
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

        #admin-topbar .icon-btn:hover {
            background-color: var(--dd-surface-low);
        }

        #admin-topbar .topbar-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--dd-on-surface);
            line-height: 1.2;
        }

        #admin-topbar .topbar-role {
            font-size: 0.625rem;
            color: var(--dd-outline);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ── Main canvas ─────────────────────────────── */
        #admin-main {
            margin-left: 256px;
            padding-top: 64px;
            min-height: 100vh;
        }

        .admin-content {
            padding: 2.5rem 2rem;
            max-width: 1360px;
        }

        /* ── Reusable components ─────────────────────── */
        .btn-dd {
            background: var(--dd-primary-grad);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-dd:hover {
            color: #fff;
            box-shadow: 0 4px 16px rgba(111, 70, 39, 0.3);
            transform: translateY(-1px);
        }

        .filter-chip {
            border-radius: 2rem;
            padding: 0.35rem 1.1rem;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .filter-chip.chip-active {
            background-color: var(--dd-secondary);
            color: #fff;
        }

        .filter-chip:not(.chip-active) {
            background-color: #dbe9a9;
            color: #404b1b;
        }

        .filter-chip:not(.chip-active):hover {
            background-color: #bfcd8f;
        }

        /* Role & status badges */
        .badge-role {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 2rem;
            padding: 0.25rem 0.7rem;
        }

        .badge-role-admin {
            background-color: #ffdcc5;
            color: #4a2010;
        }

        .badge-role-seller {
            background-color: #dbe9a9;
            color: #1e2d00;
        }

        .badge-role-buyer {
            background-color: #f0eee6;
            color: #51443c;
        }

        .badge-status {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 2rem;
            padding: 0.25rem 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge-active {
            background-color: #dbe9a9;
            color: #1e2d00;
        }

        .badge-inactive {
            background-color: #ffdad6;
            color: #93000a;
        }

        .badge-pending {
            background-color: #ec945996;
            color: #6f4627;
        }

        .badge-approved {
            background-color: #dbe9a9;
            color: #1e2d00;
        }

        .badge-rejected {
            background-color: #ffdad6;
            color: #93000a;
        }

        /* Admin table */
        .table-dd thead th {
            background-color: var(--dd-surface-low);
            font-size: 0.675rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--dd-outline);
            border-bottom: 1px solid rgba(213, 195, 184, 0.3);
            padding: 0.875rem 1.25rem;
            white-space: nowrap;
        }

        .table-dd tbody td {
            vertical-align: middle;
            padding: 0.875rem 1.25rem;
            border-color: rgba(213, 195, 184, 0.2);
            font-size: 0.875rem;
        }

        .table-dd tbody tr:hover td {
            background-color: rgba(245, 244, 236, 0.7);
        }

        /* Action buttons */
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 0.45rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--dd-surface-low);
            cursor: pointer;
            transition: all 0.15s;
            color: var(--dd-on-surface-var);
            text-decoration: none;
        }

        .action-btn .material-symbols-outlined {
            font-size: 1rem;
        }

        .action-btn-view:hover {
            background-color: var(--dd-primary);
            color: #fff;
        }

        .action-btn-approve:hover {
            background-color: var(--dd-tertiary);
            color: #fff;
        }

        .action-btn-edit:hover {
            background-color: var(--dd-primary);
            color: #fff;
        }

        .action-btn-reject:hover,
        .action-btn-delete:hover,
        .action-btn-ban:hover {
            background-color: var(--dd-error);
            color: #fff;
        }

        .action-btn-restore:hover {
            background-color: var(--dd-secondary);
            color: #fff;
        }

        /* Stat cards */
        .stat-card {
            background-color: var(--dd-surface-low);
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dd-primary);
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(48, 49, 44, 0.06);
        }

        .stat-label {
            font-size: 0.675rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--dd-outline);
        }

        .stat-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--dd-on-surface);
            line-height: 1;
        }

        /* Page header */
        .page-heading {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--dd-primary);
            letter-spacing: -0.01em;
        }

        .page-subheading {
            color: var(--dd-on-surface-var);
            font-size: 0.9rem;
        }

        /* Card wrapper for tables */
        .table-card {
            background-color: var(--dd-surface-card);
            border: 1px solid rgba(213, 195, 184, 0.2);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(48, 49, 44, 0.04);
        }

        /* Visible horizontal scrollbar on tables */
        .table-responsive::-webkit-scrollbar { height: 6px; }
        .table-responsive::-webkit-scrollbar-track { background: var(--dd-surface-low); }
        .table-responsive::-webkit-scrollbar-thumb { background: var(--dd-outline-var); border-radius: 3px; }
        .table-responsive { scrollbar-width: thin; scrollbar-color: var(--dd-outline-var) var(--dd-surface-low); }
        .scroll-hint {
            display: none;
            font-size: 0.7rem;
            color: var(--dd-outline);
            text-align: right;
            margin-bottom: 0.25rem;
        }
        @media (max-width: 767.98px) { .scroll-hint { display: block; } }

        /* ── Sidebar toggle (mobile) ── */
        #sidebar-toggle {
            display: none;
        }

        #sidebar-overlay {
            display: none;
        }

        /* ── Mobile breakpoint ── */
        @media (max-width: 991.98px) {
            #admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            body.sidebar-open #admin-sidebar {
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

            #admin-topbar {
                left: 0;
                padding: 0 1rem;
            }

            #admin-topbar .search-wrap {
                display: none;
            }

            #sidebar-toggle {
                display: inline-flex;
            }

            #admin-main {
                margin-left: 0;
            }

            .admin-content {
                padding: 1.5rem 1rem;
            }

            .page-heading {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 575.98px) {

            #admin-topbar .topbar-name,
            #admin-topbar .topbar-role,
            #admin-topbar .vr {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- ════════ Admin Sidebar ════════ -->
    <aside id="admin-sidebar">
        <div class="brand">
            <h1>DoughDistrict</h1>
            <p>The Artisan Ledger</p>
        </div>

        <nav>
            <!-- <?= admin_nav_link('admin/dashboard', 'dashboard', 'Dashboard', $currentPath) ?> -->
            <?= admin_nav_link('admin/users', 'group', 'Users', $currentPath) ?>
            <?= admin_nav_link('admin/products', 'inventory_2', 'Products', $currentPath) ?>
            <?= admin_nav_link('admin/categories', 'category', 'Categories', $currentPath) ?>
            <div style="height:1px;background:rgba(255,255,255,0.07);margin:.5rem .75rem;"></div>
            <?= admin_nav_link('browse', 'explore', 'Browse Shop', $currentPath) ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>logout" class="nav-link">
                <span class="material-symbols-outlined">logout</span> Sign Out
            </a>
            <div class="admin-user">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['name'] ?? 'Admin') ?>&background=6f4627&color=fff&bold=true&size=64"
                    alt="Admin avatar">
                <div>
                    <div class="admin-name"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <div id="sidebar-overlay"></div>

    <!-- ════════ Admin Topbar ════════ -->
    <header id="admin-topbar">
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <button class="icon-btn" id="sidebar-toggle" title="Menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
        <div class="search-wrap">
            <span class="material-symbols-outlined search-icon">search</span>
            <input type="text" placeholder="Search the ledger…">
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="icon-btn" title="Notifications">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <button class="icon-btn" title="Help">
                <span class="material-symbols-outlined">help</span>
            </button>
            <div class="vr mx-1" style="height:24px;opacity:0.25;"></div>
            <div class="text-end">
                <div class="topbar-name"><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></div>
                <div class="topbar-role">Administrator</div>
            </div>
        </div>
    </header>
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function () {
            document.body.classList.toggle('sidebar-open');
        });
        document.getElementById('sidebar-overlay').addEventListener('click', function () {
            document.body.classList.remove('sidebar-open');
        });
    </script>

    <!-- ════════ Main Canvas ════════ -->
    <div id="admin-main">
        <div class="admin-content">