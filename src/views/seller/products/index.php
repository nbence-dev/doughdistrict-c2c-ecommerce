<?php
$pageTitle = 'My Products';
include __DIR__ . '/../layout.php';

$flash    = get_flash();
$products = $products ?? [];

function product_status_pill(string $status): string
{
    return match($status) {
        'active'   => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-tertiary text-on-tertiary">Active</span>',
        'pending'  => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-secondary-container text-on-secondary-container">Pending</span>',
        'rejected' => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-error-container text-error">Rejected</span>',
        default    => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-surface-variant text-on-surface-variant">Unknown</span>',
    };
}
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex justify-between items-center px-8 py-4 border-b border-outline-variant/10">
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">My Products</h2>
    <p class="text-sm text-on-surface-variant">Manage your artisanal listings and stock</p>
  </div>
  <a href="<?= BASE_URL ?>seller/products/create"
     class="bg-primary text-on-primary px-5 py-2.5 rounded-xl font-headline font-semibold text-sm flex items-center gap-2 hover:opacity-90 transition-all active:scale-95 shadow-sm">
    <span class="material-symbols-outlined text-lg">add</span> Add New Product
  </a>
</header>

<div class="p-8 space-y-8">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger','error']); ?>
  <div class="px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <?php if (empty($products)): ?>
  <!-- Empty state -->
  <div class="flex flex-col items-center justify-center p-20 border-2 border-dashed border-outline-variant/30 rounded-3xl">
    <span class="material-symbols-outlined text-5xl mb-4 text-primary/40">bakery_dining</span>
    <p class="font-headline font-semibold text-xl text-on-surface mb-2">Ready to share your creations?</p>
    <p class="text-on-surface-variant text-sm text-center max-w-sm mb-8">
      List your first handcrafted bake. Once approved by our team it will appear in the marketplace.
    </p>
    <a href="<?= BASE_URL ?>seller/products/create"
       class="px-8 py-3 bg-primary text-on-primary rounded-xl font-headline font-semibold hover:opacity-90 transition-all">
      Add your first product
    </a>
  </div>

  <?php else: ?>
  <!-- Product table -->
  <p class="text-xs text-outline text-right mb-1 sm:hidden flex items-center justify-end gap-1">
    <span class="material-symbols-outlined text-sm">swipe</span> Scroll to see more
  </p>
  <div class="bg-surface-container-lowest rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)] overflow-x-auto">
    <table class="w-full text-left border-collapse" style="min-width:640px;">
      <thead>
        <tr class="bg-surface-container-low border-none">
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider">Product</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider">Category</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-right">Price</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-center">Stock</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-center">Status</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($products as $product): ?>
        <tr class="hover:bg-surface/30 transition-colors">
          <td class="px-6 py-5">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-xl overflow-hidden bg-surface-container flex-shrink-0">
                <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="w-full h-full object-cover"/>
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                  <span class="material-symbols-outlined text-outline">bakery_dining</span>
                </div>
                <?php endif; ?>
              </div>
              <div>
                <h4 class="font-headline font-bold text-on-surface"><?= htmlspecialchars($product['name']) ?></h4>
                <p class="text-xs text-outline font-medium">ID #<?= (int)$product['id'] ?></p>
              </div>
            </div>
          </td>
          <td class="px-6 py-5">
            <span class="inline-block px-2.5 py-1 rounded-lg bg-surface-container text-on-surface-variant font-medium text-xs max-w-[140px] truncate" title="<?= htmlspecialchars($product['category_name']) ?>">
              <?= htmlspecialchars($product['category_name']) ?>
            </span>
          </td>
          <td class="px-6 py-5 text-right font-bold text-primary">
            <span class="text-secondary font-semibold text-xs align-top mr-0.5">R</span><?= number_format($product['price'], 2) ?>
          </td>
          <td class="px-6 py-5 text-center text-on-surface-variant text-sm font-medium">
            <?= (int)$product['stock_qty'] ?>
          </td>
          <td class="px-6 py-5 text-center">
            <?= product_status_pill($product['status']) ?>
          </td>
          <td class="px-6 py-5 text-right">
            <div class="flex justify-end gap-2">
              <a href="<?= BASE_URL ?>seller/products/edit?id=<?= (int)$product['id'] ?>"
                 class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant inline-flex" title="Edit">
                <span class="material-symbols-outlined text-xl">edit</span>
              </a>
              <form method="POST" action="<?= BASE_URL ?>seller/products/delete"
                    onsubmit="return confirm('Delete \'<?= htmlspecialchars(addslashes($product['name'])) ?>\'? This cannot be undone.')">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>"/>
                <button type="submit"
                        class="p-2 hover:bg-error-container hover:text-error rounded-lg transition-colors text-on-surface-variant" title="Delete">
                  <span class="material-symbols-outlined text-xl">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Footer hint -->
  <p class="text-center text-xs text-outline font-label">
    New products are reviewed by our team before going live. Approval usually takes under 24 hours.
  </p>

</div><!-- /p-8 -->

</main>
</body>
</html>
