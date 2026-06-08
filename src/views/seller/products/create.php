<?php
$pageTitle  = 'Add New Product';
include __DIR__ . '/../layout.php';

$flash      = get_flash();
$categories = $categories ?? [];
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center gap-3 px-8 py-4 border-b border-outline-variant/10">
  <a href="<?= BASE_URL ?>seller/products" class="p-2 hover:bg-surface-container-low rounded-lg transition-colors text-on-surface-variant">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">New Product</h2>
    <p class="text-sm text-on-surface-variant">Add a new handcrafted creation to your shop</p>
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

  <form method="POST"
        action="<?= BASE_URL ?>seller/products/create"
        enctype="multipart/form-data"
        class="space-y-8" data-validate>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

      <!-- Left: core details -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Core info card -->
        <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)]">
          <div class="space-y-6">

            <!-- Product Name -->
            <div class="flex flex-col gap-2">
              <label for="name" class="font-headline font-bold text-on-surface">Product Name</label>
              <input type="text" id="name" name="name" required
                     value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                     placeholder="e.g. Traditional Karoo Sourdough"
                     class="bg-surface-container-low border-none rounded-xl p-4 text-on-surface placeholder:text-outline/50 focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
            </div>

            <!-- Category & Price -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col gap-2">
                <label for="category_id" class="font-headline font-bold text-on-surface">Category</label>
                <select id="category_id" name="category_id" required
                        class="bg-surface-container-low border-none rounded-xl p-4 text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all appearance-none cursor-pointer">
                  <option value="">— Select a category —</option>
                  <?php foreach ($categories as $cat): ?>
                  <option value="<?= (int)$cat['id'] ?>"
                    <?= (isset($_POST['category_id']) && (int)$_POST['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="flex flex-col gap-2">
                <label for="price" class="font-headline font-bold text-on-surface">Price (ZAR)</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary font-bold">R</span>
                  <input type="number" id="price" name="price" required
                         min="0.01" step="0.01"
                         value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                         placeholder="0.00"
                         class="bg-surface-container-low border-none rounded-xl p-4 pl-10 w-full text-on-surface placeholder:text-outline/50 focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-2">
              <label for="description" class="font-headline font-bold text-on-surface">Description</label>
              <textarea id="description" name="description" rows="5" required
                        placeholder="Share the story behind this product, its ingredients, and what makes it special..."
                        class="bg-surface-container-low border-none rounded-xl p-4 text-on-surface placeholder:text-outline/50 focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all resize-none"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

          </div>
        </section>

        <!-- Package Dimensions (moved here so it's visible alongside product info) -->
        <section class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)] space-y-5">
          <div>
            <h3 class="font-headline font-bold text-on-surface mb-1">Package Dimensions</h3>
            <p class="text-xs text-on-surface-variant">Used to calculate the shipping cost shown to buyers and to book courier collection.</p>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div class="flex flex-col gap-1">
              <label class="text-xs font-bold text-outline uppercase tracking-wider">Length (cm)</label>
              <input type="number" name="length_cm" min="0.1" step="0.1" required data-label="Length"
                     value="<?= htmlspecialchars($_POST['length_cm'] ?? '') ?>"
                     placeholder="30"
                     class="bg-surface-container-low border-none rounded-xl p-3 text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-xs font-bold text-outline uppercase tracking-wider">Width (cm)</label>
              <input type="number" name="width_cm" min="0.1" step="0.1" required data-label="Width"
                     value="<?= htmlspecialchars($_POST['width_cm'] ?? '') ?>"
                     placeholder="20"
                     class="bg-surface-container-low border-none rounded-xl p-3 text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-xs font-bold text-outline uppercase tracking-wider">Height (cm)</label>
              <input type="number" name="height_cm" min="0.1" step="0.1" required data-label="Height"
                     value="<?= htmlspecialchars($_POST['height_cm'] ?? '') ?>"
                     placeholder="10"
                     class="bg-surface-container-low border-none rounded-xl p-3 text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-xs font-bold text-outline uppercase tracking-wider">Weight (kg)</label>
            <input type="number" name="weight_kg" min="0.01" step="0.001" required data-label="Weight"
                   value="<?= htmlspecialchars($_POST['weight_kg'] ?? '') ?>"
                   placeholder="0.500"
                   class="bg-surface-container-low border-none rounded-xl p-3 w-full text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
          </div>
        </section>

      </div>

      <!-- Right: image + stock -->
      <div class="space-y-6">

        <!-- Image upload with preview -->
        <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)] border-2 border-dashed border-outline-variant/30 hover:border-primary/40 transition-colors">
          <label for="image" id="image-label" class="flex flex-col items-center justify-center text-center cursor-pointer min-h-56 group overflow-hidden rounded-xl">
            <img id="image-preview" src="" alt="Preview" class="hidden w-full object-cover rounded-xl" style="max-height:224px;"/>
            <div id="image-placeholder" class="flex flex-col items-center py-10">
              <div class="bg-surface-container-low w-16 h-16 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-primary text-3xl">add_a_photo</span>
              </div>
              <h3 class="font-headline font-bold text-on-surface mb-1">Upload Product Image</h3>
              <p class="text-on-surface-variant text-xs px-4">JPG, PNG, or WebP · Max 10 MB</p>
            </div>
          </label>
          <input type="file" id="image" name="image" accept="image/*" required class="sr-only"/>
          <p id="file-name" class="text-center text-xs text-outline mt-2 font-medium"></p>
        </section>

        <!-- Stock -->
        <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_12px_32px_rgba(48,49,44,0.06)] space-y-6">
          <div class="flex flex-col gap-2">
            <label for="stock_qty" class="font-headline font-bold text-on-surface">Stock Quantity</label>
            <input type="number" id="stock_qty" name="stock_qty"
                   min="0" value="<?= htmlspecialchars($_POST['stock_qty'] ?? '1') ?>"
                   class="bg-surface-container-low border-none rounded-xl p-4 text-on-surface focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary/40 transition-all"/>
          </div>

          <div class="p-4 rounded-2xl bg-tertiary-fixed/30 border border-tertiary/10">
            <p class="text-xs text-on-tertiary-fixed-variant leading-relaxed">
              <span class="font-bold text-tertiary">Note:</span> New products require admin approval before appearing in the marketplace. This usually takes under 24 hours.
            </p>
          </div>
        </section>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
          <button type="submit"
                  class="w-full py-5 bg-primary text-on-primary font-headline font-bold text-lg rounded-2xl hover:opacity-90 transition-all flex items-center justify-center gap-2 shadow-lg active:scale-[0.98]">
            <span class="material-symbols-outlined" style="font-size:1.4rem;">check_circle</span>
            Save &amp; List Product
          </button>
          <a href="<?= BASE_URL ?>seller/products"
             class="w-full py-4 text-center bg-transparent border-2 border-outline-variant/40 text-primary font-headline font-bold rounded-xl hover:bg-surface-container-low transition-all">
            Cancel
          </a>
        </div>

      </div>
    </div><!-- /grid -->

  </form>
</div><!-- /p-8 -->

</main>
<script>
document.getElementById('image').addEventListener('change', function () {
    var file = this.files[0];
    var preview = document.getElementById('image-preview');
    var placeholder = document.getElementById('image-placeholder');
    var fileName = document.getElementById('file-name');
    if (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
        fileName.textContent = file.name;
    }
});
</script>
<script src="<?= asset('js/validation.js') ?>"></script>
</body>
</html>
