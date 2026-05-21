<?php
$pageTitle = 'Ship Order';
include __DIR__ . '/../layout.php';

$flash = get_flash();
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center gap-3 px-6 lg:px-8 py-4 border-b border-outline-variant/10">
  <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>"
     class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant flex-shrink-0">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <nav class="flex items-center gap-1 text-outline text-xs mb-0.5">
      <a href="<?= BASE_URL ?>seller/orders" class="hover:text-primary transition-colors">Orders</a>
      <span class="material-symbols-outlined text-[10px]">chevron_right</span>
      <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>" class="hover:text-primary transition-colors">Order #<?= (int) $order['id'] ?></a>
      <span class="material-symbols-outlined text-[10px]">chevron_right</span>
      <span class="text-primary font-medium">Ship</span>
    </nav>
    <h2 class="font-headline font-bold text-xl text-on-surface tracking-tight">Ship Order #<?= (int) $order['id'] ?></h2>
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

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    <!-- Left: Order Summary -->
    <div class="lg:col-span-2 space-y-5">

      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10 relative overflow-hidden">
        <span class="material-symbols-outlined absolute -right-2 -top-2 text-[5rem] text-surface-container/80 select-none pointer-events-none">receipt_long</span>

        <h3 class="font-headline font-bold text-on-surface mb-5 relative">Order Summary</h3>

        <div class="mb-4">
          <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Buyer</p>
          <p class="font-semibold text-on-surface"><?= htmlspecialchars($buyerUser['name']) ?></p>
        </div>

        <div class="mb-5">
          <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Shipping Address</p>
          <div class="flex gap-2">
            <span class="material-symbols-outlined text-secondary flex-shrink-0 mt-0.5" style="font-size:1.1rem;">location_on</span>
            <p class="text-sm text-on-surface-variant leading-relaxed">
              <?= htmlspecialchars($order['shipping_street']) ?><br>
              <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_province']) ?><br>
              <?= htmlspecialchars($order['shipping_postal_code']) ?>
            </p>
          </div>
        </div>

        <div class="pt-4 border-t border-outline-variant/10">
          <p class="text-xs font-bold text-outline uppercase tracking-wider mb-3">Items</p>
          <div class="space-y-3">
            <?php foreach ($items as $item): ?>
            <div class="flex justify-between items-center">
              <div>
                <p class="font-semibold text-sm text-on-surface"><?= htmlspecialchars($item['product_name']) ?></p>
                <p class="text-xs text-outline">Qty: <?= (int) $item['quantity'] ?></p>
              </div>
              <span class="font-bold text-sm text-secondary">R <?= number_format($item['unit_price'] * $item['quantity'], 2) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="flex justify-between pt-3 mt-3 border-t border-outline-variant/10">
            <span class="font-bold text-on-surface">Total</span>
            <span class="font-bold text-secondary">R <?= number_format($order['total_amount'], 2) ?></span>
          </div>
        </div>
      </div>

      <!-- Fulfillment notice -->
      <div class="bg-tertiary-container/10 rounded-2xl p-5 border border-tertiary/10 flex items-start gap-3">
        <span class="material-symbols-outlined text-secondary flex-shrink-0 mt-0.5">info</span>
        <div>
          <p class="font-bold text-sm text-secondary mb-1">Fulfillment Notice</p>
          <p class="text-xs text-on-surface-variant leading-relaxed">
            Ensure all baked goods are cooled and securely packaged before handover. The courier will collect from your registered address.
          </p>
        </div>
      </div>

    </div>

    <!-- Right: Shipment Form -->
    <div class="lg:col-span-3">
      <div class="bg-surface-container-lowest rounded-2xl p-6 lg:p-8 shadow-sm ring-1 ring-outline-variant/10">
        <h3 class="font-headline font-bold text-on-surface mb-6">Shipment Details</h3>

        <form method="POST" action="<?= BASE_URL ?>seller/orders/ship?id=<?= (int) $order['id'] ?>" class="space-y-5" data-validate>

          <!-- Parcel Description -->
          <div>
            <label for="parcel_description" class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
              <span class="material-symbols-outlined" style="font-size:1rem;">inventory_2</span>
              Parcel Description
            </label>
            <input type="text" id="parcel_description" name="parcel_description"
                   value="Baked goods" required
                   placeholder="e.g. Sourdough loaves, croissants"
                   class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
          </div>

          <!-- Collection from + dimensions summary (read-only, pulled from product listings) -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                <span class="material-symbols-outlined" style="font-size:1rem;">storefront</span>
                Collection From
              </label>
              <input type="text" disabled
                     value="<?= htmlspecialchars(($sellerProfile['city'] ?? '') . ', ' . ($sellerProfile['zone'] ?? '')) ?>"
                     class="w-full bg-surface-container border-0 rounded-xl px-4 py-3 text-outline cursor-not-allowed"/>
            </div>
            <div>
              <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                <span class="material-symbols-outlined" style="font-size:1rem;">weight</span>
                Total Weight
              </label>
              <input type="text" disabled
                     value="<?= number_format($parcelDimensions['weight_kg'], 3) ?> kg"
                     class="w-full bg-surface-container border-0 rounded-xl px-4 py-3 text-outline cursor-not-allowed"/>
            </div>
          </div>
          <div>
            <label class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
              <span class="material-symbols-outlined" style="font-size:1rem;">straighten</span>
              Parcel Dimensions (from product listings)
            </label>
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Length</label>
                <input type="text" disabled value="<?= number_format($parcelDimensions['length_cm'], 1) ?> cm"
                       class="w-full bg-surface-container border-0 rounded-xl px-3 py-3 text-outline text-center cursor-not-allowed"/>
              </div>
              <div>
                <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Width</label>
                <input type="text" disabled value="<?= number_format($parcelDimensions['width_cm'], 1) ?> cm"
                       class="w-full bg-surface-container border-0 rounded-xl px-3 py-3 text-outline text-center cursor-not-allowed"/>
              </div>
              <div>
                <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Height</label>
                <input type="text" disabled value="<?= number_format($parcelDimensions['height_cm'], 1) ?> cm"
                       class="w-full bg-surface-container border-0 rounded-xl px-3 py-3 text-outline text-center cursor-not-allowed"/>
              </div>
            </div>
            <p class="text-[10px] text-outline mt-2">Dimensions sourced from your product listings. Update them on the product edit page if needed.</p>
          </div>

          <!-- Tracking placeholder -->
          <div class="flex items-center gap-3 p-4 border border-dashed border-outline-variant/40 rounded-xl opacity-60">
            <span class="material-symbols-outlined text-outline">qr_code_2</span>
            <div>
              <p class="text-xs font-bold text-outline uppercase tracking-wider">Tracking Reference</p>
              <p class="text-xs italic text-on-surface-variant">Generated upon creation…</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-3 pt-2">
            <a href="<?= BASE_URL ?>seller/orders/detail?id=<?= (int) $order['id'] ?>"
               class="px-5 py-3 font-bold text-primary hover:text-primary/70 transition-colors text-sm">
              Cancel
            </a>
            <button type="submit"
                    class="flex-1 py-3.5 flex items-center justify-center gap-2 bg-primary text-on-primary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-[0.98] shadow-md">
              <span class="material-symbols-outlined">local_shipping</span>
              Create Shipment
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>

</div>

</main>
<script src="<?= JS_URL ?>validation.js"></script>
</body>
</html>
