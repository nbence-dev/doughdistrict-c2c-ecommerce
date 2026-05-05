<?php
$pageTitle = 'Order Detail';
include __DIR__ . '/../layout.php';

$flash  = get_flash();
$order  = $data['order'];
$items  = $data['items'];

$statusColors = [
    'paid'       => 'bg-surface-variant text-on-surface-variant',
    'processing' => 'bg-secondary-container text-on-secondary-container',
    'shipped'    => 'bg-primary-container text-on-primary-container',
    'delivered'  => 'bg-tertiary text-on-tertiary',
];
$statusCls = $statusColors[$order['status']] ?? 'bg-surface-variant text-on-surface-variant';
$statuses  = ['paid', 'processing', 'shipped', 'delivered'];
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center gap-3 px-6 lg:px-8 py-4 border-b border-outline-variant/10">
  <a href="<?= BASE_URL ?>seller/orders"
     class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant flex-shrink-0">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div class="flex-1 min-w-0">
    <nav class="flex items-center gap-1 text-outline text-xs mb-0.5">
      <a href="<?= BASE_URL ?>seller/orders" class="hover:text-primary transition-colors">Orders</a>
      <span class="material-symbols-outlined text-[10px]">chevron_right</span>
      <span class="text-primary font-medium">Order #<?= (int) $order['id'] ?></span>
    </nav>
    <div class="flex items-center gap-3 flex-wrap">
      <h2 class="font-headline font-bold text-xl text-on-surface tracking-tight">Order #<?= (int) $order['id'] ?></h2>
      <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide <?= $statusCls ?>">
        <?= ucfirst(htmlspecialchars($order['status'])) ?>
      </span>
    </div>
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

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Items + Address -->
    <div class="lg:col-span-2 space-y-6">

      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <h3 class="font-headline font-bold text-on-surface mb-5">Order Items</h3>
        <div class="space-y-1">
          <?php foreach ($items as $item): ?>
          <div class="flex justify-between items-start py-3 border-b border-surface-container last:border-0">
            <div>
              <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($item['product_name']) ?></p>
              <p class="text-xs text-outline mt-0.5">
                Qty: <?= (int) $item['quantity'] ?> &nbsp;·&nbsp; @ R <?= number_format($item['unit_price'], 2) ?> each
              </p>
            </div>
            <span class="font-bold text-secondary text-sm ml-4 flex-shrink-0">
              R <?= number_format($item['unit_price'] * $item['quantity'], 2) ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Totals -->
        <div class="mt-4 pt-4 border-t border-outline-variant/10 space-y-2">
          <?php if (!empty($order['shipping_cost'])): ?>
          <div class="flex justify-between text-sm text-on-surface-variant">
            <span>Items subtotal</span>
            <span>R <?= number_format($order['total_amount'], 2) ?></span>
          </div>
          <div class="flex justify-between text-sm text-on-surface-variant">
            <span>Shipping (Shiplogic)</span>
            <span>R <?= number_format($order['shipping_cost'], 2) ?></span>
          </div>
          <div class="flex justify-between pt-2 border-t border-outline-variant/10">
            <span class="font-headline font-bold text-on-surface">Total</span>
            <span class="font-headline font-bold text-secondary text-xl">R <?= number_format($order['total_amount'] + $order['shipping_cost'], 2) ?></span>
          </div>
          <?php else: ?>
          <div class="flex justify-between">
            <span class="font-headline font-bold text-on-surface">Total</span>
            <span class="font-headline font-bold text-secondary text-xl">R <?= number_format($order['total_amount'], 2) ?></span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Shipping Address -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <div class="flex items-center gap-2 mb-4">
          <span class="material-symbols-outlined text-primary">local_shipping</span>
          <h3 class="font-headline font-bold text-on-surface">Shipping Address</h3>
        </div>
        <p class="font-bold text-on-surface mb-1"><?= htmlspecialchars($order['shipping_name']) ?></p>
        <p class="text-on-surface-variant text-sm leading-relaxed">
          <?= htmlspecialchars($order['shipping_street']) ?><br>
          <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_province']) ?><br>
          <?= htmlspecialchars($order['shipping_postal_code']) ?>
        </p>
      </div>

    </div>

    <!-- Right: Fulfillment Panel -->
    <div>
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10 lg:sticky" style="top: 88px;">
        <h3 class="font-headline font-bold text-on-surface mb-5">Fulfillment</h3>

        <form method="POST" action="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>">
          <label class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Update Status</label>
          <div class="relative mb-3">
            <select name="status"
                    class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface appearance-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all">
              <?php foreach ($statuses as $s): ?>
              <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-3 text-outline pointer-events-none">expand_more</span>
          </div>
          <button type="submit"
                  class="w-full py-3 bg-primary text-on-primary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95">
            Save Status
          </button>
        </form>

        <?php if (in_array($order['status'], ['paid', 'processing'])): ?>
        <div class="h-px bg-outline-variant/10 my-4"></div>
        <a href="<?= BASE_URL ?>seller/orders/ship?id=<?= (int) $order['id'] ?>"
           class="w-full py-3 flex items-center justify-center gap-2 bg-secondary text-on-secondary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95">
          <span class="material-symbols-outlined text-lg">local_shipping</span>
          Ship This Order
        </a>
        <?php elseif (!empty($order['tracking_reference'])): ?>
        <div class="h-px bg-outline-variant/10 my-4"></div>
        <div class="bg-surface-container-low rounded-xl p-4">
          <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Tracking Reference</p>
          <p class="font-bold text-primary font-mono"><?= htmlspecialchars($order['tracking_reference']) ?></p>
        </div>
        <?php if (!empty($order['estimated_collection'])): ?>
        <div class="bg-surface-container-low rounded-xl p-4 mt-3">
          <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Driver Pickup</p>
          <p class="font-bold text-on-surface"><?= (new DateTime($order['estimated_collection']))->format('d M Y, H:i') ?></p>
        </div>
        <?php endif; ?>
        <div class="h-px bg-outline-variant/10 my-4"></div>
        <a href="https://sandbox.shiplogic.com/track?S&ref=<?= urlencode($order['tracking_reference']) ?>" target="_blank" rel="noopener"
           class="w-full py-3 flex items-center justify-center gap-2 bg-primary text-on-primary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95">
          <span class="material-symbols-outlined text-lg">travel_explore</span>
          Track Shipment
        </a>
        <?php if (!empty($order['shiplogic_shipment_id'])): ?>
        <div class="h-px bg-outline-variant/10 my-4"></div>
        <a href="<?= BASE_URL ?>seller/orders/waybill?id=<?= (int) $order['id'] ?>" target="_blank" rel="noopener"
           class="w-full py-3 flex items-center justify-center gap-2 bg-tertiary text-on-tertiary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95">
          <span class="material-symbols-outlined text-lg">print</span>
          Print Waybill
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <div class="h-px bg-outline-variant/10 my-4"></div>
        <a href="<?= BASE_URL ?>seller/orders"
           class="w-full py-3 flex items-center justify-center border border-outline-variant/30 text-primary rounded-xl font-headline font-semibold text-sm hover:bg-surface-container-low transition-all">
          ← Back to Orders
        </a>
      </div>
    </div>

  </div>

</div>

</main>
</body>
</html>
