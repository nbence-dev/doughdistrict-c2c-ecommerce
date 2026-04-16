<?php
/**
 * Seller layout — include at the TOP of every seller view (except onboarding).
 * Expects: $pageTitle (string), $sellerProfile (array), current_user() available.
 * Opens: <main class="flex-1 ml-64 ..."> — each view MUST close it.
 */
$pageTitle    = $pageTitle ?? 'Seller Portal';
$sellerProfile = $sellerProfile ?? [];
$currentPath  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

function seller_nav_link(string $path, string $icon, string $label, string $currentPath): string
{
    $active = ($currentPath === $path);
    $url    = BASE_URL . $path;
    $base   = 'flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 text-sm';
    $cls    = $active
        ? "$base bg-surface-container-lowest text-primary font-bold shadow-sm"
        : "$base text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest/50";
    return "<a href=\"{$url}\" class=\"{$cls}\"><span class=\"material-symbols-outlined\">{$icon}</span>{$label}</a>";
}

$shopName = htmlspecialchars($sellerProfile['shop_name'] ?? 'My Shop');
$userName = htmlspecialchars(current_user()['name'] ?? '');
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
  body { font-family: 'Be Vietnam Pro', sans-serif; }
  h1,h2,h3,h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .golden-glow:hover { background: linear-gradient(135deg, #6f4627 0%, #8b5e3c 100%); }
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen flex">

<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-surface-container-low flex flex-col py-6 gap-1 z-40 border-r border-outline-variant/20">
  <div class="px-6 mb-8">
    <h1 class="font-headline text-xl font-black text-primary tracking-tight">DoughDistrict</h1>
    <p class="text-xs text-on-surface-variant font-medium mt-0.5"><?= $shopName ?></p>
  </div>

  <nav class="flex-1 px-3 space-y-1">
    <?= seller_nav_link('seller/dashboard', 'dashboard', 'Dashboard', $currentPath) ?>
    <?= seller_nav_link('seller/products', 'storefront', 'My Products', $currentPath) ?>
    <?= seller_nav_link('seller/orders', 'receipt_long', 'Orders', $currentPath) ?>
    <?= seller_nav_link('seller/stripe/connect', 'payments', 'Payments', $currentPath) ?>
    <?= seller_nav_link('seller/profile', 'settings', 'Shop Profile', $currentPath) ?>
  </nav>

  <div class="px-4 mt-auto space-y-3">
    <a href="<?= BASE_URL ?>seller/products/create"
       class="w-full py-3 px-4 bg-primary text-on-primary rounded-xl font-headline font-bold text-sm flex items-center justify-center gap-2 golden-glow transition-all active:scale-95 shadow-sm">
      <span class="material-symbols-outlined text-sm">add</span> New Listing
    </a>
    <div class="border-t border-outline-variant/20 pt-3">
      <div class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-surface-container-lowest/50 transition-colors">
        <span class="material-symbols-outlined text-on-surface-variant">account_circle</span>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-bold text-on-surface truncate"><?= $userName ?></p>
          <p class="text-[10px] text-outline">Seller</p>
        </div>
        <a href="<?= BASE_URL ?>logout" class="text-[10px] text-outline hover:text-error transition-colors">Logout</a>
      </div>
    </div>
  </div>
</aside>

<!-- Main content — views close this -->
<main class="flex-1 ml-64 min-h-screen flex flex-col">
