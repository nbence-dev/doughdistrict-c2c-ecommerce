<?php
// Standalone page — no sidebar. Any logged-in user (buyer) can see this.
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Seller Onboarding — DoughDistrict</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  theme: { extend: {
    colors: {
      "primary": "#6f4627", "primary-container": "#8b5e3c", "primary-fixed": "#ffdcc5", "primary-fixed-dim": "#f4bb92",
      "on-primary": "#ffffff", "on-primary-container": "#ffe3d1",
      "secondary": "#924c00", "secondary-fixed-dim": "#ffb781",
      "tertiary": "#495523", "tertiary-fixed": "#dbe9a9", "tertiary-fixed-dim": "#bfcd8f",
      "on-tertiary-fixed-variant": "#404b1b",
      "surface": "#fbf9f1", "surface-container": "#f0eee6", "surface-container-low": "#f5f4ec",
      "surface-container-high": "#eae8e0", "surface-container-lowest": "#ffffff",
      "on-surface": "#1b1c17", "on-surface-variant": "#51443c",
      "outline": "#83746b", "outline-variant": "#d5c3b8",
      "error": "#ba1a1a", "error-container": "#ffdad6",
    },
    fontFamily: { "headline": ["Plus Jakarta Sans"], "label": ["Be Vietnam Pro"] }
  }}
}
</script>
<style>
  body { font-family: 'Be Vietnam Pro', sans-serif; }
  h1,h2,h3,h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<!-- Header -->
<header class="w-full sticky top-0 bg-gradient-to-b from-[#fbf9f1] to-[#f5f4ec] z-40">
  <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
    <a href="<?= BASE_URL ?>" class="text-xl font-bold text-primary font-headline tracking-tight">DoughDistrict</a>
    <a href="<?= BASE_URL ?>logout" class="text-sm text-on-surface-variant hover:text-primary transition-colors">Log out</a>
  </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-12 md:py-20 flex flex-col items-center">

  <!-- Flash message -->
  <?php if ($flash): ?>
  <?php $isError = in_array($flash['type'], ['danger', 'error']); ?>
  <div class="w-full max-w-2xl mb-6 px-5 py-4 rounded-2xl flex items-center gap-3
    <?= $isError ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined text-lg"><?= $isError ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <!-- Page header -->
  <div class="w-full max-w-6xl mb-12">
    <h1 class="text-3xl font-headline font-extrabold text-primary tracking-tight">Create Your Shop</h1>
    <p class="text-on-surface-variant mt-2">Fill in your shop details and collection address to get started.</p>
  </div>

  <!-- Two-column layout -->
  <div class="grid grid-cols-1 md:grid-cols-12 gap-12 w-full max-w-6xl">

    <!-- Left: editorial content -->
    <div class="md:col-span-5 flex flex-col justify-start">
      <div class="relative mb-8">
        <div class="absolute -top-4 -left-4 w-24 h-24 bg-tertiary-fixed-dim/20 rounded-full blur-3xl"></div>
        <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800"
             alt="Artisan baker dusting flour"
             class="rounded-3xl shadow-lg relative z-10 w-full aspect-[4/5] object-cover"/>
      </div>
      <h3 class="text-2xl font-headline font-bold text-primary mb-4">Tell us your story</h3>
      <p class="text-on-surface-variant leading-relaxed mb-6">
        Every loaf has a journey. Every maker has a hearth. Your shop profile is where customers first connect with the passion behind your craft.
      </p>
      <div class="flex items-center gap-3 text-tertiary font-medium">
        <span class="material-symbols-outlined">auto_awesome</span>
        <span class="text-sm">Highlight your local South African roots.</span>
      </div>
    </div>

    <!-- Right: form -->
    <div class="md:col-span-7">
      <div class="bg-surface-container-lowest p-8 md:p-12 rounded-[2rem] shadow-[0px_12px_32px_rgba(48,49,44,0.06)]">
        <form method="POST" action="<?= BASE_URL ?>seller/onboard" class="space-y-8"
              id="onboarding-form" data-validate
              data-maps-key="<?= htmlspecialchars(getenv('ADDRESS_API_KEY') ?: '') ?>">

          <!-- Shop Name -->
          <div class="space-y-2">
            <label for="shop_name" class="block text-sm font-label font-semibold text-on-surface ml-1">Shop Name</label>
            <input type="text" id="shop_name" name="shop_name"
                   placeholder="e.g. The Sourdough Sanctuary"
                   value="<?= htmlspecialchars($_POST['shop_name'] ?? '') ?>"
                   required
                   class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
            <p class="text-[11px] text-outline px-1">This is how your shop will appear to the community.</p>
          </div>

          <!-- Bio -->
          <div class="space-y-2">
            <label for="bio" class="block text-sm font-label font-semibold text-on-surface ml-1">Shop Bio</label>
            <textarea id="bio" name="bio" rows="6"
                      placeholder="Tell us about your baking style, your ingredients, and why you love what you do..."
                      required
                      class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60 resize-none"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
            <p class="text-[11px] text-outline px-1">Keep it authentic and inviting.</p>
          </div>

          <!-- Collection Address -->
          <div class="pt-2">
            <div class="flex items-center gap-2 mb-5">
              <span class="material-symbols-outlined text-primary">local_shipping</span>
              <h3 class="text-sm font-headline font-bold text-on-surface uppercase tracking-widest">Collection Address</h3>
            </div>
            <div class="space-y-4">

              <!-- Street Address -->
              <div class="space-y-2">
                <label for="street_address" class="block text-sm font-label font-semibold text-on-surface ml-1">Street Address</label>
                <input type="text" id="street_address" name="street_address"
                       placeholder="e.g. 12 Bakers Lane"
                       value="<?= htmlspecialchars($_POST['street_address'] ?? '') ?>"
                       required
                       class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
              </div>

              <!-- Local Area + City -->
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label for="local_area" class="block text-sm font-label font-semibold text-on-surface ml-1">Suburb / Local Area</label>
                  <input type="text" id="local_area" name="local_area"
                         placeholder="e.g. Sandton"
                         value="<?= htmlspecialchars($_POST['local_area'] ?? '') ?>"
                         required
                         class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
                </div>
                <div class="space-y-2">
                  <label for="city" class="block text-sm font-label font-semibold text-on-surface ml-1">City</label>
                  <input type="text" id="city" name="city"
                         placeholder="e.g. Johannesburg"
                         value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
                         required
                         class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
                </div>
              </div>

              <!-- Province + Postal Code -->
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label for="zone" class="block text-sm font-label font-semibold text-on-surface ml-1">Province</label>
                  <select id="zone" name="zone" required
                          class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface">
                    <option value="" disabled <?= empty($_POST['zone']) ? 'selected' : '' ?>>Select province</option>
                    <?php
                    $provinces = ['EC'=>'Eastern Cape','FS'=>'Free State','GP'=>'Gauteng','KZN'=>'KwaZulu-Natal','LP'=>'Limpopo','MP'=>'Mpumalanga','NW'=>'North West','NC'=>'Northern Cape','WC'=>'Western Cape'];
                    foreach ($provinces as $code => $label): ?>
                      <option value="<?= $code ?>" <?= ($_POST['zone'] ?? '') === $code ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="space-y-2">
                  <label for="postal_code" class="block text-sm font-label font-semibold text-on-surface ml-1">Postal Code</label>
                  <input type="text" id="postal_code" name="postal_code"
                         placeholder="e.g. 2196"
                         value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>"
                         required maxlength="10" data-rule="postal"
                         class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
                </div>
              </div>

              <!-- Mobile Number -->
              <div class="space-y-2">
                <label for="mobile_number" class="block text-sm font-label font-semibold text-on-surface ml-1">Mobile Number <span class="text-outline font-normal">(for courier collection)</span></label>
                <input type="tel" id="mobile_number" name="mobile_number"
                       placeholder="e.g. 0821234567"
                       value="<?= htmlspecialchars($_POST['mobile_number'] ?? '') ?>"
                       required
                       class="w-full px-6 py-4 rounded-xl bg-surface-container-low border-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all text-on-surface placeholder:text-outline/60"/>
              </div>

            </div>
          </div>

          <!-- Info box -->
          <div class="p-4 rounded-2xl bg-tertiary-fixed/30 border border-tertiary/10 flex items-start gap-4">
            <div class="p-2 bg-white rounded-lg shadow-sm">
              <span class="material-symbols-outlined text-tertiary">info</span>
            </div>
            <div>
              <h4 class="text-sm font-bold text-tertiary">What happens next?</h4>
              <p class="text-xs text-on-tertiary-fixed-variant leading-tight mt-1">
                After submitting, your account is upgraded to Seller. You can then list products, connect your Stripe account to receive payments, and ship orders via The Courier Guy.
              </p>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-4">
            <button type="submit"
                    class="w-full py-4 px-8 bg-gradient-to-br from-primary to-primary-container text-on-primary font-headline font-bold text-lg rounded-xl shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
              Create My Shop
              <span class="material-symbols-outlined">arrow_forward</span>
            </button>
            <a href="<?= BASE_URL ?>browse"
               class="w-full mt-4 py-3 text-primary font-label font-semibold text-sm hover:underline decoration-secondary-fixed-dim underline-offset-4 flex items-center justify-center">
              Cancel, go back to browsing
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>

  <footer class="mt-20 text-center">
    <p class="text-outline text-xs font-label uppercase tracking-[0.2em]">DoughDistrict • Handcrafted in South Africa</p>
  </footer>
</main>

<script>
function initOnboardingAutocomplete() {
    const streetInput = document.getElementById('street_address');
    if (!streetInput) return;

    const provinceMap = {
        'Gauteng': 'GP', 'Western Cape': 'WC', 'Eastern Cape': 'EC',
        'KwaZulu-Natal': 'KZN', 'Limpopo': 'LP', 'Mpumalanga': 'MP',
        'North West': 'NW', 'Northern Cape': 'NC', 'Free State': 'FS'
    };

    const autocomplete = new google.maps.places.Autocomplete(streetInput, {
        componentRestrictions: { country: 'za' },
        fields: ['address_components'],
        types: ['address']
    });

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.address_components) return;

        let streetNumber = '', route = '', suburb = '', city = '', province = '', postalCode = '';
        for (const c of place.address_components) {
            if (c.types.includes('street_number'))               streetNumber = c.long_name;
            if (c.types.includes('route'))                       route        = c.long_name;
            if (c.types.includes('sublocality_level_1') || c.types.includes('sublocality') || (c.types.includes('neighborhood') && !suburb)) suburb = c.long_name;
            if (c.types.includes('locality'))                    city         = c.long_name;
            if (c.types.includes('administrative_area_level_1')) province     = c.long_name;
            if (c.types.includes('postal_code'))                 postalCode   = c.long_name;
        }

        streetInput.value = streetNumber ? streetNumber + ' ' + route : route;

        const suburbEl   = document.getElementById('local_area');
        const cityEl     = document.getElementById('city');
        const provinceEl = document.getElementById('zone');
        const postalEl   = document.getElementById('postal_code');

        if (suburbEl && suburb)  suburbEl.value   = suburb;
        if (cityEl)              cityEl.value     = city;
        if (postalEl)            postalEl.value   = postalCode;
        if (provinceEl && provinceMap[province]) provinceEl.value = provinceMap[province];
    });
}
</script>
<script>
(function () {
    const key = document.getElementById('onboarding-form')?.dataset.mapsKey;
    if (!key) return;
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=initOnboardingAutocomplete';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();
</script>

<script src="<?= asset('js/validation.js') ?>"></script>
</body>
</html>
