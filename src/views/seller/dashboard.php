<?php
$pageTitle = 'Dashboard';
include __DIR__ . '/layout.php';

$flash    = get_flash();
$products = $products ?? [];
$total    = count($products);
$pending  = count(array_filter($products, fn($p) => $p['status'] === 'pending'));
$active   = count(array_filter($products, fn($p) => $p['status'] === 'active'));

function status_pill(string $status): string
{
    return match($status) {
        'active'   => '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-tertiary text-on-tertiary">Active</span>',
        'pending'  => '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-secondary-container text-on-secondary-container">Pending</span>',
        'rejected' => '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-error-container text-error">Rejected</span>',
        default    => '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-surface-container text-outline">Unknown</span>',
    };
}
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex justify-between items-center px-8 py-4 border-b border-outline-variant/10">
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Baker's Dashboard</h2>
    <p class="text-sm text-on-surface-variant">Welcome back, <?= htmlspecialchars(current_user()['name']) ?>.</p>
  </div>
  <a href="<?= BASE_URL ?>seller/products/create"
     class="bg-primary text-on-primary px-5 py-2.5 rounded-xl font-headline font-semibold text-sm flex items-center gap-2 hover:opacity-90 transition-all active:scale-95 shadow-sm">
    <span class="material-symbols-outlined text-lg">add</span> Add Product
  </a>
</header>

<div class="p-8 space-y-10">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger','error']); ?>
  <div class="px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <!-- Stat cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_12px_32px_rgba(48,49,44,0.06)] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <span class="material-symbols-outlined text-9xl">bakery_dining</span>
      </div>
      <p class="text-tertiary font-label font-medium mb-1">Total Listings</p>
      <div class="flex items-baseline gap-2">
        <h2 class="text-4xl font-headline font-extrabold text-on-surface"><?= $total ?></h2>
      </div>
      <div class="mt-6 h-1 w-full bg-surface-container rounded-full">
        <div class="h-1 bg-tertiary rounded-full" style="width: <?= $total > 0 ? min(100, ($active / $total) * 100) : 0 ?>%"></div>
      </div>
    </div>

    <div class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_12px_32px_rgba(48,49,44,0.06)] relative overflow-hidden group">
      <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
        <span class="material-symbols-outlined text-9xl">pending_actions</span>
      </div>
      <p class="text-secondary font-label font-medium mb-1">Pending Approval</p>
      <div class="flex items-baseline gap-2">
        <h2 class="text-4xl font-headline font-extrabold text-on-surface"><?= $pending ?></h2>
        <span class="text-secondary text-sm font-label">awaiting review</span>
      </div>
      <div class="mt-6 h-1 w-full bg-surface-container rounded-full">
        <div class="h-1 bg-secondary rounded-full" style="width: <?= $total > 0 ? min(100, ($pending / $total) * 100) : 0 ?>%"></div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-primary to-primary-container text-on-primary p-8 rounded-3xl shadow-xl relative overflow-hidden">
      <div class="absolute -right-4 -top-4 opacity-10">
        <span class="material-symbols-outlined text-9xl">payments</span>
      </div>
      <?php $stripeLinked = !empty($sellerProfile['stripe_account_id']); ?>
      <p class="text-on-primary-container font-label font-medium mb-1">Stripe Payments</p>
      <div class="flex items-baseline gap-2 mt-1">
        <h2 class="text-2xl font-headline font-extrabold text-on-primary">
          <?= $stripeLinked ? 'Connected' : 'Not connected' ?>
        </h2>
      </div>
      <a href="<?= BASE_URL ?>seller/stripe/connect"
         class="mt-6 inline-flex items-center gap-1 text-on-primary-container/80 text-sm font-label hover:text-on-primary transition-colors underline-offset-2">
        <?= $stripeLinked ? 'Manage Stripe →' : 'Set up payments →' ?>
      </a>
    </div>
  </div>

  <!-- Recent products -->
  <div>
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-headline text-xl font-bold text-on-surface">My Listings</h3>
      <a href="<?= BASE_URL ?>seller/products" class="text-secondary font-label font-semibold text-sm flex items-center gap-1 hover:underline underline-offset-4">
        View all <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </a>
    </div>

    <?php if (empty($products)): ?>
    <div class="flex flex-col items-center justify-center p-16 border-2 border-dashed border-outline-variant/30 rounded-3xl">
      <span class="material-symbols-outlined text-5xl mb-4 text-primary/40">bakery_dining</span>
      <p class="font-headline font-semibold text-lg text-on-surface">No products yet</p>
      <p class="text-on-surface-variant text-sm text-center max-w-sm mb-6">
        List your first handcrafted creation to start selling on DoughDistrict.
      </p>
      <a href="<?= BASE_URL ?>seller/products/create"
         class="px-6 py-3 bg-primary text-on-primary rounded-xl font-headline font-semibold text-sm hover:opacity-90 transition-all">
        Add your first product
      </a>
    </div>

    <?php else: ?>
    <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm border border-outline-variant/10">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low/50">
            <th class="px-6 py-4 font-headline font-bold text-xs uppercase tracking-widest text-outline">Product</th>
            <th class="px-6 py-4 font-headline font-bold text-xs uppercase tracking-widest text-outline">Category</th>
            <th class="px-6 py-4 font-headline font-bold text-xs uppercase tracking-widest text-outline text-right">Price</th>
            <th class="px-6 py-4 font-headline font-bold text-xs uppercase tracking-widest text-outline text-center">Status</th>
            <th class="px-6 py-4 font-headline font-bold text-xs uppercase tracking-widest text-outline text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container">
          <?php foreach (array_slice($products, 0, 5) as $product): ?>
          <tr class="hover:bg-surface-container-low/30 transition-colors">
            <td class="px-6 py-5">
              <div class="flex items-center gap-4">
                <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="w-12 h-12 rounded-xl object-cover bg-surface-container"/>
                <?php else: ?>
                <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center">
                  <span class="material-symbols-outlined text-outline">bakery_dining</span>
                </div>
                <?php endif; ?>
                <span class="font-headline font-bold text-on-surface"><?= htmlspecialchars($product['name']) ?></span>
              </div>
            </td>
            <td class="px-6 py-5 text-on-surface-variant text-sm"><?= htmlspecialchars($product['category_name']) ?></td>
            <td class="px-6 py-5 text-right font-bold text-primary">
              <span class="text-secondary font-semibold text-xs align-top mr-0.5">R</span><?= number_format($product['price'], 2) ?>
            </td>
            <td class="px-6 py-5 text-center"><?= status_pill($product['status']) ?></td>
            <td class="px-6 py-5 text-right">
              <a href="<?= BASE_URL ?>seller/products/edit?id=<?= (int)$product['id'] ?>"
                 class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant inline-flex" title="Edit">
                <span class="material-symbols-outlined text-xl">edit</span>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div><!-- /p-8 -->

</main>
</body>
</html>
