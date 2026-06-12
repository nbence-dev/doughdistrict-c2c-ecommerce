<?php
$pageTitle  = 'Edit Product';
include __DIR__ . '/../layout.php';

$flash      = get_flash();
$categories = $categories ?? [];
// $product is set by the controller; ownership already verified
$p = $product;
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center gap-3 px-8 py-4 border-b border-outline-variant/10">
  <a href="<?= BASE_URL ?>seller/products" class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <nav class="flex items-center gap-2 text-outline text-xs mb-1">
      <a href="<?= BASE_URL ?>seller/products" class="hover:text-primary transition-colors">My Products</a>
      <span class="material-symbols-outlined text-[10px]">chevron_right</span>
      <span class="text-primary font-medium">Edit</span>
    </nav>
    <h2 class="font-headline font-extrabold text-2xl text-primary tracking-tight">
      <?= htmlspecialchars($p['name']) ?>
    </h2>
  </div>
</header>

<div class="p-8 max-w-5xl">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger','error']); ?>
  <div class="mb-6 px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-12 gap-8">

    <!-- Left: image panel -->
    <div class="col-span-12 lg:col-span-4 space-y-6">
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <h3 class="font-headline text-sm font-bold text-primary mb-4">Product Image</h3>

        <!-- Current image preview -->
        <div class="relative group aspect-square rounded-xl overflow-hidden bg-surface-container mb-4">
          <?php if (!empty($p['image_url'])): ?>
          <img src="<?= htmlspecialchars($p['image_url']) ?>"
               alt="<?= htmlspecialchars($p['name']) ?>"
               class="w-full h-full object-cover" id="image-preview"/>
          <?php else: ?>
          <div class="w-full h-full flex flex-col items-center justify-center text-outline" id="image-preview">
            <span class="material-symbols-outlined text-4xl">image</span>
            <p class="text-xs mt-2">No image yet</p>
          </div>
          <?php endif; ?>
          <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <span class="bg-surface-container-lowest/90 px-4 py-2 rounded-full text-xs font-bold text-primary flex items-center gap-2 shadow-lg">
              <span class="material-symbols-outlined text-sm">photo_camera</span> Change Image
            </span>
          </div>
        </div>

        <!-- Upload new image (optional) -->
        <label for="image" class="border-2 border-dashed border-outline-variant/40 rounded-xl p-5 text-center hover:bg-surface-container-low transition-colors cursor-pointer flex flex-col items-center">
          <span class="material-symbols-outlined text-primary-container mb-2">cloud_upload</span>
          <p class="text-xs font-medium text-on-surface">Drop new image here or click</p>
          <p class="text-[10px] text-outline mt-1">JPG, PNG, WebP · Max 10 MB</p>
        </label>
        <input type="file" id="image" name="image" accept="image/*" class="sr-only"
               onchange="previewImage(this)"/>
        <p id="file-name" class="text-center text-xs text-outline mt-1 font-medium"></p>
      </div>

      <!-- Baker's tip -->
      <div class="bg-tertiary-container/10 rounded-2xl p-5 border border-tertiary/10 flex items-start gap-3">
        <span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">info</span>
        <div>
          <h4 class="text-sm font-bold text-tertiary mb-1">Baker's Tip</h4>
          <p class="text-xs text-on-tertiary-fixed-variant leading-relaxed">
            Natural lighting makes your bread look more appetizing. Try taking photos near a morning window for that golden, crusty detail.
          </p>
        </div>
      </div>
    </div>

    <!-- Right: form -->
    <form method="POST"
          action="<?= BASE_URL ?>seller/products/edit?id=<?= (int)$p['id'] ?>"
          enctype="multipart/form-data" data-validate
          class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-2xl p-8 shadow-sm ring-1 ring-outline-variant/10">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Product Name -->
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">Product Name</label>
          <input type="text" name="name" required
                 value="<?= htmlspecialchars($_POST['name'] ?? $p['name']) ?>"
                 class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
        </div>

        <!-- Description -->
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">Description</label>
          <textarea name="description" rows="4" required
                    class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all resize-none"><?= htmlspecialchars($_POST['description'] ?? $p['description']) ?></textarea>
        </div>

        <!-- Category -->
        <div>
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">Category</label>
          <div class="relative">
            <select name="category_id" required
                    class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface appearance-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all">
              <?php foreach ($categories as $cat): ?>
              <?php $sel = (int)($_POST['category_id'] ?? $p['category_id']) === (int)$cat['id']; ?>
              <option value="<?= (int)$cat['id'] ?>" <?= $sel ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-3 text-outline pointer-events-none">expand_more</span>
          </div>
        </div>

        <!-- Price -->
        <div>
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">Price (ZAR)</label>
          <div class="relative">
            <span class="absolute left-4 top-3 text-secondary font-bold">R</span>
            <input type="number" name="price" required min="0.01" step="0.01"
                   value="<?= htmlspecialchars($_POST['price'] ?? number_format($p['price'], 2)) ?>"
                   class="w-full bg-surface-container-low border-0 rounded-xl pl-10 pr-4 py-3 text-on-surface font-bold focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
          </div>
        </div>

        <!-- Stock Quantity -->
        <div>
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-2">Stock Quantity</label>
          <input type="number" name="stock_qty" min="0"
                 value="<?= htmlspecialchars($_POST['stock_qty'] ?? $p['stock_qty']) ?>"
                 class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
        </div>

        <!-- Package Dimensions -->
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-primary uppercase tracking-wider mb-3">Package Dimensions &amp; Weight</label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
              <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Length (cm)</label>
              <input type="number" name="length_cm" min="0.1" step="0.1" required data-label="Length"
                     value="<?= htmlspecialchars($_POST['length_cm'] ?? $p['length_cm']) ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-3 py-2 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
            <div>
              <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Width (cm)</label>
              <input type="number" name="width_cm" min="0.1" step="0.1" required data-label="Width"
                     value="<?= htmlspecialchars($_POST['width_cm'] ?? $p['width_cm']) ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-3 py-2 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
            <div>
              <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Height (cm)</label>
              <input type="number" name="height_cm" min="0.1" step="0.1" required data-label="Height"
                     value="<?= htmlspecialchars($_POST['height_cm'] ?? $p['height_cm']) ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-3 py-2 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
            <div>
              <label class="block text-[10px] font-semibold text-outline uppercase mb-1">Weight (kg)</label>
              <input type="number" name="weight_kg" min="0.01" step="0.001" required data-label="Weight"
                     value="<?= htmlspecialchars($_POST['weight_kg'] ?? $p['weight_kg']) ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-3 py-2 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
          </div>
          <?php if (!empty($p['shipping_cost'])): ?>
          <p class="text-xs text-outline mt-2">
            Current estimated shipping: <span class="font-bold text-secondary">R <?= number_format($p['shipping_cost'], 2) ?></span> — recalculated on save.
          </p>
          <?php endif; ?>
        </div>

        <!-- Current status chip -->
        <div class="flex items-end pb-3">
          <?php
          $statusColors = [
            'active'   => 'bg-tertiary-container/20 text-tertiary border-tertiary/10',
            'pending'  => 'bg-secondary-container/20 text-secondary border-secondary/10',
            'rejected' => 'bg-error-container/40 text-error border-error/10',
          ];
          $cls = $statusColors[$p['status']] ?? 'bg-surface-container text-outline border-outline/10';
          ?>
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border <?= $cls ?>">
            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
            Status: <?= htmlspecialchars(ucfirst($p['status'])) ?>
          </span>
        </div>

      </div>

      <!-- Actions -->
      <div class="mt-10 pt-8 border-t border-outline-variant/10 flex flex-col sm:flex-row items-center justify-end gap-4">
        <a href="<?= BASE_URL ?>seller/products"
           class="order-2 sm:order-1 px-8 py-3 text-sm font-bold text-primary hover:bg-surface-container transition-all rounded-xl active:scale-95">
          Cancel
        </a>
        <button type="submit"
                class="order-1 sm:order-2 w-full sm:w-auto px-10 py-3 bg-primary-container text-on-primary-container rounded-xl font-headline font-extrabold text-sm hover:opacity-90 transition-all shadow-md active:scale-95">
          Save Changes
        </button>
      </div>

    </form>

  </div><!-- /grid -->

</div><!-- /p-8 -->

<script>
function previewImage(input) {
  const nameEl = document.getElementById('file-name');
  nameEl.textContent = input.files[0]?.name ?? '';
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const preview = document.getElementById('image-preview');
      if (preview.tagName === 'IMG') {
        preview.src = e.target.result;
      } else {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'w-full h-full object-cover';
        img.id = 'image-preview';
        preview.replaceWith(img);
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

</main>
<script src="<?= asset('js/validation.js') ?>"></script>
</body>
</html>
