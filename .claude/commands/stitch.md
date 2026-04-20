# Stitch → PHP View Builder

You are implementing a DoughDistrict PHP view from a Stitch MCP design. Follow every step in order without skipping.

## Project context (always apply)
- Bootstrap 5 only — never use Tailwind classes in output files
- CSS variables already defined in `header.php`: `--dd-primary`, `--dd-secondary`, `--dd-surface`, `--dd-surface-low`, `--dd-on-surface`, `--dd-on-surface-var`, `--dd-outline`, `--dd-outline-var`
- Every view must start with `<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>` and end with `<?php require_once ROOT_PATH . '/views/layouts/footer.php'; ?>`
- Escape all output with `htmlspecialchars()`. Use `number_format($p['price'], 2)` for prices.
- Stitch Tailwind color tokens map to Bootstrap + CSS variables as follows:
  - `text-primary` / `bg-primary` → `style="color: var(--dd-primary)"` / `style="background: var(--dd-primary)"`
  - `text-secondary` → `style="color: var(--dd-secondary)"`
  - `bg-surface-container-low` → `style="background: var(--dd-surface-low)"`
  - `text-on-surface-variant` / `text-outline` → `style="color: var(--dd-on-surface-var)"` / `style="color: var(--dd-outline)"`
  - `rounded-xl` / `rounded-2xl` → `rounded-4` (Bootstrap)
  - `shadow-[...]` → `shadow-sm`
  - `font-headline` → leave as-is (mapped in header.php `<style>`)
  - `btn-dd-primary` is the project's primary button class (already defined in header.php)

## Step 1 — Identify the controller and views needed

From the user's message and current conversation context, determine:
- Which controller was just completed (e.g., `browse_controller.php`, `cart_controller.php`)
- Which PHP view files need to be created (check `src/views/buyer/` stubs — a stub is a file with 1–3 lines)
- What PHP variables the controller exposes (read the controller file)

## Step 2 — Pull matching screens from Stitch

Use the Stitch MCP tools in this order:

1. Call `mcp__stitch__list_projects` — find the project titled "DoughDistrict Marketplace" (id: `4850655813952717171`)
2. Call `mcp__stitch__list_screens` with `projectId: "4850655813952717171"`
3. Match screen titles to the views needed. Prefer Desktop variants. Also fetch Mobile if it exists.
4. Call `mcp__stitch__get_screen` for each matched screen to get the `htmlCode.downloadUrl`
5. Use `curl` via Bash to download each HTML file to `/tmp/`: `curl -sL "<url>" -o /tmp/<screen_name>.html`
6. Read each downloaded file

## Step 3 — Convert to Bootstrap 5 PHP view

For each view file:

1. Strip the entire `<head>` block (fonts, Tailwind script, tailwind.config) — the project's `header.php` already loads fonts and Bootstrap
2. Strip the `<nav>` / `<header>` top bar — `header.php` already renders the navbar
3. Strip any `<footer>` — `footer.php` already renders it
4. Strip the mobile `<nav>` bottom bar if present
5. Keep all structural HTML (layout, grid, cards, forms)
6. Convert Tailwind utility classes to Bootstrap 5 equivalents using the mapping above
7. Add a `<style>` block at the top of the view for any component-specific styles that have no Bootstrap equivalent (hover effects, custom card styles, etc.)
8. Replace all static/hardcoded content with PHP variables from the controller:
   - Product lists → `<?php foreach ($products as $p): ?>` loops
   - Single product → `$product['field']`
   - Categories → `<?php foreach ($categories as $cat): ?>`
   - Form actions → `<?= BASE_URL ?>route/subroute`
   - Links → `<?= BASE_URL ?>product?id=<?= $p['id'] ?>`
9. Add empty-state handling (`<?php if (empty($...)): ?>`)
10. Wrap output in `<?php require_once ROOT_PATH . '/views/layouts/header.php'; ?>` ... `footer.php`

## Step 4 — Wire up routes

Check `public/index.php`. For each new view, ensure a `case` block exists:
```php
case 'route-name':
    require_login(); // or require_role('buyer') / require_role('seller')
    require_once ROOT_PATH . '/controllers/relevant_controller.php';
    require_once ROOT_PATH . '/views/buyer/view_name.php';
    break;
```
Add any missing routes.

## Step 5 — Verify

After writing all files, briefly list:
- Files created/updated
- Variables each view expects from its controller
- Any routes added to `index.php`
- Anything not yet wired (e.g., form POSTs to a controller not yet built) — flag these explicitly so the user knows what's still pending
