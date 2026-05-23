<?php
/**
 * Seller layout — dark sidebar matching admin style.
 * Include at the TOP of every seller view (except onboarding).
 * Expects: $pageTitle (string), $sellerProfile (array), current_user() available.
 * Opens: <main id="seller-main"> — each view MUST close it + </body></html>
 */
$pageTitle    = $pageTitle ?? 'Seller Portal';
$sellerProfile = $sellerProfile ?? [];
$currentUser  = current_user();
$currentPath  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

function seller_nav_link(string $path, string $icon, string $label, string $currentPath): string
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

$shopName = htmlspecialchars($sellerProfile['shop_name'] ?? 'My Shop');
$userName = htmlspecialchars($currentUser['name'] ?? '');
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($pageTitle) ?> — DoughDistrict</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "primary": "#6f4627", "primary-container": "#8b5e3c", "primary-fixed": "#ffdcc5", "primary-fixed-dim": "#f4bb92",
        "on-primary": "#ffffff", "on-primary-container": "#ffe3d1", "on-primary-fixed": "#301400", "on-primary-fixed-variant": "#653d1e",
        "secondary": "#924c00", "secondary-container": "#fda055", "secondary-fixed": "#ffdcc4", "secondary-fixed-dim": "#ffb781",
        "on-secondary": "#ffffff", "on-secondary-container": "#703800", "on-secondary-fixed": "#2f1400", "on-secondary-fixed-variant": "#6f3800",
        "tertiary": "#495523", "tertiary-container": "#616d39", "tertiary-fixed": "#dbe9a9", "tertiary-fixed-dim": "#bfcd8f",
        "on-tertiary": "#ffffff", "on-tertiary-container": "#e0eeae", "on-tertiary-fixed": "#171e00", "on-tertiary-fixed-variant": "#404b1b",
        "surface": "#fbf9f1", "surface-bright": "#fbf9f1", "surface-dim": "#dcdad2",
        "surface-container": "#f0eee6", "surface-container-low": "#f5f4ec", "surface-container-high": "#eae8e0",
        "surface-container-highest": "#e4e3db", "surface-container-lowest": "#ffffff",
        "surface-variant": "#e4e3db", "surface-tint": "#805533",
        "on-surface": "#1b1c17", "on-surface-variant": "#51443c",
        "outline": "#83746b", "outline-variant": "#d5c3b8",
        "background": "#fbf9f1", "on-background": "#1b1c17",
        "error": "#ba1a1a", "error-container": "#ffdad6", "on-error": "#ffffff", "on-error-container": "#93000a",
        "inverse-surface": "#30312c", "inverse-on-surface": "#f3f1e9", "inverse-primary": "#f4bb92",
      },
      borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
      fontFamily: { "headline": ["Plus Jakarta Sans"], "body": ["Be Vietnam Pro"], "label": ["Be Vietnam Pro"] }
    }
  }
}
</script>
<style>
  body { font-family: 'Be Vietnam Pro', sans-serif; background-color: #fbf9f1; color: #1b1c17; }
  h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: -4px; font-size: 1.2em; }

  /* ── Sidebar ─────────────────────────────────────────────── */
  #seller-sidebar {
      width: 256px;
      min-height: 100vh;
      background-color: #1c1917;
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      padding: 1.5rem 0;
      z-index: 50;
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
  }

  #seller-sidebar .brand {
      padding: 0 1.5rem 2rem;
  }

  #seller-sidebar .brand h1 {
      color: #fef3c7;
      font-size: 1.125rem;
      font-weight: 700;
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
  }

  #seller-sidebar .brand p {
      color: #78716c;
      font-size: 0.625rem;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      margin: 0;
      font-weight: 600;
  }

  #seller-sidebar .nav-link {
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

  #seller-sidebar .nav-link:hover {
      color: #f5f5f4;
      background-color: rgba(68, 64, 60, 0.45);
  }

  #seller-sidebar .nav-link.active {
      background-color: #292524;
      color: #fcd34d;
  }

  #seller-sidebar .sidebar-footer {
      margin-top: auto;
      border-top: 1px solid #292524;
      padding-top: 0.75rem;
  }

  #seller-sidebar .sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.625rem 1.25rem 0;
  }

  #seller-sidebar .sidebar-user img {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #3d3835;
      flex-shrink: 0;
  }

  #seller-sidebar .sidebar-user .user-name {
      color: #fef9f0;
      font-size: 0.8125rem;
      font-weight: 600;
      line-height: 1.2;
  }

  #seller-sidebar .sidebar-user .user-role {
      color: #78716c;
      font-size: 0.675rem;
      line-height: 1;
  }

  /* ── New Listing CTA ─────────────────────────────────────── */
  #seller-sidebar .new-listing-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin: 0 0.75rem 0.75rem;
      padding: 0.625rem 1rem;
      background: linear-gradient(135deg, #6f4627 0%, #8b5e3c 100%);
      color: #fff;
      border-radius: 0.5rem;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 700;
      font-size: 0.8rem;
      text-decoration: none;
      transition: all 0.2s;
  }

  #seller-sidebar .new-listing-btn:hover {
      box-shadow: 0 4px 16px rgba(111, 70, 39, 0.35);
      transform: translateY(-1px);
      color: #fff;
  }

  /* ── Mobile topbar ───────────────────────────────────────── */
  #seller-topbar {
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
      z-index: 39;
  }

  #seller-topbar .topbar-brand {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 700;
      color: #6f4627;
      font-size: 1rem;
      flex: 1;
  }

  #seller-topbar .icon-btn {
      background: none;
      border: none;
      color: #6f4627;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.15s;
  }

  #seller-topbar .icon-btn:hover { background-color: #f5f4ec; }

  /* ── Sidebar overlay ─────────────────────────────────────── */
  #sidebar-overlay { display: none; }

  /* ── Main canvas ─────────────────────────────────────────── */
  #seller-main {
      margin-left: 256px;
      min-height: 100vh;
  }

  /* ── Mobile breakpoint ───────────────────────────────────── */
  @media (max-width: 991.98px) {
      #seller-sidebar {
          transform: translateX(-100%);
          transition: transform 0.25s ease;
      }

      body.sidebar-open #seller-sidebar {
          transform: translateX(0);
      }

      #sidebar-overlay {
          position: fixed;
          inset: 0;
          background: rgba(0, 0, 0, 0.45);
          z-index: 40;
      }

      body.sidebar-open #sidebar-overlay {
          display: block;
      }

      #seller-topbar { display: flex; }

      #seller-main {
          margin-left: 0;
          padding-top: 56px;
      }

      /* Push sticky content headers below mobile topbar */
      #seller-main header.sticky { top: 56px !important; }
  }
</style>
</head>
<body>

<!-- ════════ Sidebar ════════ -->
<aside id="seller-sidebar">
  <div class="brand">
    <h1>DoughDistrict</h1>
    <p><?= $shopName ?></p>
  </div>

  <nav style="flex:1;">
    <?= seller_nav_link('seller/dashboard', 'dashboard', 'Dashboard', $currentPath) ?>
    <?= seller_nav_link('seller/products', 'storefront', 'My Products', $currentPath) ?>
    <?= seller_nav_link('seller/orders', 'receipt_long', 'Orders', $currentPath) ?>
    <?= seller_nav_link('seller/stripe', 'payments', 'Payments', $currentPath) ?>
    <?= seller_nav_link('seller/profile', 'settings', 'Shop Profile', $currentPath) ?>
    <div style="height:1px;background:rgba(255,255,255,0.07);margin:.5rem .75rem;"></div>
    <?= seller_nav_link('browse', 'explore', 'Browse Shop', $currentPath) ?>
  </nav>

  <a href="<?= BASE_URL ?>seller/products/create" class="new-listing-btn">
    <span class="material-symbols-outlined" style="font-size:1rem;">add</span> New Listing
  </a>

  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>logout" class="nav-link">
      <span class="material-symbols-outlined">logout</span> Sign Out
    </a>
    <a href="<?= BASE_URL ?>account/profile" class="sidebar-user text-decoration-none">
      <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['name'] ?? 'Seller') ?>&background=6f4627&color=fff&bold=true&size=64"
           alt="Avatar">
      <div>
        <div class="user-name"><?= $userName ?></div>
        <div class="user-role">Seller</div>
      </div>
    </a>
  </div>
</aside>

<div id="sidebar-overlay"></div>

<!-- ════════ Mobile topbar ════════ -->
<header id="seller-topbar">
  <button class="icon-btn" id="sidebar-toggle" aria-label="Open menu">
    <span class="material-symbols-outlined">menu</span>
  </button>
  <span class="topbar-brand">DoughDistrict</span>
  <a href="<?= BASE_URL ?>seller/products/create" class="icon-btn" title="New Listing">
    <span class="material-symbols-outlined">add</span>
  </a>
</header>

<script>
(function () {
  var toggle  = document.getElementById('sidebar-toggle');
  var sidebar = document.getElementById('seller-sidebar');
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
<main id="seller-main">
