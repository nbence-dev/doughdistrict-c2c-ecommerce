<?php
$pageTitle = 'My Orders';
include __DIR__ . '/../layout.php';

$flash  = get_flash();
$orders = $orders ?? [];

function order_status_pill(string $status): string
{
    return match ($status) {
        'processing' => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-secondary-container text-on-secondary-container">Processing</span>',
        'shipped'    => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-primary-container text-on-primary-container">Shipped</span>',
        'delivered'  => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-tertiary text-on-tertiary">Delivered</span>',
        default      => '<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide bg-surface-variant text-on-surface-variant">Paid</span>',
    };
}
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center px-6 lg:px-8 py-4 border-b border-outline-variant/10">
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">My Orders</h2>
    <p class="text-sm text-on-surface-variant">Manage incoming orders and customer shipments</p>
  </div>
</header>

<div class="p-6 lg:p-8">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger', 'error']); ?>
  <div class="mb-6 px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <?php if (empty($orders)): ?>
  <div class="flex flex-col items-center justify-center p-16 border-2 border-dashed border-outline-variant/30 rounded-3xl">
    <span class="material-symbols-outlined text-5xl mb-4 text-primary/40">receipt_long</span>
    <p class="font-headline font-semibold text-xl text-on-surface mb-2">No orders yet</p>
    <p class="text-on-surface-variant text-sm text-center max-w-sm">
      Share your shop link to start receiving orders from buyers.
    </p>
  </div>

  <?php else: ?>
  <p class="text-xs text-outline text-right mb-1 flex items-center justify-end gap-1 sm:hidden">
    <span class="material-symbols-outlined text-sm">swipe</span> Scroll to see more
  </p>
  <div class="bg-surface-container-lowest rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)] overflow-x-auto">
    <table class="w-full text-left border-collapse" style="min-width:560px;">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider">Order</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider hidden sm:table-cell">Buyer</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider hidden md:table-cell">Date</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider hidden lg:table-cell">Pickup</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-right">Total</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-center">Status</th>
          <th class="px-6 py-5 font-headline font-bold text-on-surface-variant text-xs uppercase tracking-wider text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container-low">
        <?php foreach ($orders as $o): ?>
        <tr class="hover:bg-surface/30 transition-colors">
          <td class="px-6 py-5">
            <span class="font-headline font-bold text-primary">#<?= (int) $o['id'] ?></span>
            <p class="text-xs text-outline mt-0.5 sm:hidden"><?= htmlspecialchars($o['name']) ?></p>
          </td>
          <td class="px-6 py-5 hidden sm:table-cell text-on-surface font-medium text-sm"><?= htmlspecialchars($o['name']) ?></td>
          <td class="px-6 py-5 hidden md:table-cell text-on-surface-variant text-sm"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td class="px-6 py-5 hidden lg:table-cell text-sm">
            <?php if (!empty($o['estimated_collection'])): ?>
              <?php
                $pickup = new DateTime($o['estimated_collection']);
                $now    = new DateTime();
                $isPast = $pickup < $now;
              ?>
              <span class="inline-flex items-center gap-1 <?= $isPast ? 'text-on-surface-variant' : 'text-primary font-bold' ?>">
                <span class="material-symbols-outlined text-sm"><?= $isPast ? 'check_circle' : 'schedule' ?></span>
                <?= $pickup->format('d M Y') ?>
              </span>
            <?php else: ?>
              <span class="text-outline">—</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-5 text-right font-bold text-secondary text-sm">
            R <?= number_format($o['total_amount'], 2) ?>
          </td>
          <td class="px-6 py-5 text-center"><?= order_status_pill($o['status']) ?></td>
          <td class="px-6 py-5 text-right">
            <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $o['id'] ?>"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-primary hover:text-on-primary transition-colors text-primary text-xs font-bold">
              View <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>

</main>
</body>
</html>
