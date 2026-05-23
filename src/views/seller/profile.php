<?php
$pageTitle = 'Shop Profile';
include __DIR__ . '/layout.php';

$flash = get_flash();
?>

<!-- Top bar -->
<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md flex items-center px-6 lg:px-8 py-4 border-b border-outline-variant/10">
  <div>
    <h2 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Shop Profile</h2>
    <p class="text-sm text-on-surface-variant">Update your shop details and collection address</p>
  </div>
</header>

<div class="p-6 lg:p-8 max-w-5xl">

  <!-- Flash -->
  <?php if ($flash): ?>
  <?php $err = in_array($flash['type'], ['danger', 'error']); ?>
  <div class="mb-6 px-5 py-4 rounded-2xl flex items-center gap-3 <?= $err ? 'bg-error-container text-error' : 'bg-tertiary-fixed text-on-tertiary-fixed-variant' ?>">
    <span class="material-symbols-outlined"><?= $err ? 'error' : 'check_circle' ?></span>
    <p class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></p>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>seller/profile"
        id="profile-form" data-validate
        data-maps-key="<?= htmlspecialchars(getenv('ADDRESS_API_KEY') ?: '') ?>">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Shop Info -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <div class="flex items-center gap-2 mb-5">
          <span class="material-symbols-outlined text-primary">storefront</span>
          <h3 class="font-headline font-bold text-on-surface">Shop Info</h3>
        </div>
        <div class="space-y-4">
          <div>
            <label for="shop_name" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Shop Name</label>
            <input type="text" id="shop_name" name="shop_name" required
                   value="<?= htmlspecialchars($sellerProfile['shop_name']) ?>"
                   class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
          </div>
          <div>
            <label for="bio" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Bio</label>
            <textarea id="bio" name="bio" rows="5" required
                      class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all resize-none"><?= htmlspecialchars($sellerProfile['bio']) ?></textarea>
          </div>
        </div>
      </div>

      <!-- Collection Address -->
      <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm ring-1 ring-outline-variant/10">
        <div class="flex items-center gap-2 mb-5">
          <span class="material-symbols-outlined text-primary">local_shipping</span>
          <h3 class="font-headline font-bold text-on-surface">Collection Address</h3>
        </div>
        <div class="space-y-4">
          <div>
            <label for="street_address" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Street Address</label>
            <input type="text" id="street_address" name="street_address" required
                   placeholder="e.g. 12 Bakers Lane"
                   value="<?= htmlspecialchars($sellerProfile['street_address'] ?? '') ?>"
                   class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="local_area" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Suburb</label>
              <input type="text" id="local_area" name="local_area" required
                     placeholder="e.g. Sandton"
                     value="<?= htmlspecialchars($sellerProfile['local_area'] ?? '') ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
            <div>
              <label for="city" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">City</label>
              <input type="text" id="city" name="city" required
                     placeholder="e.g. Johannesburg"
                     value="<?= htmlspecialchars($sellerProfile['city'] ?? '') ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-1">
              <label for="zone" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Province</label>
              <div class="relative">
                <select id="zone" name="zone" required
                        class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface appearance-none bg-none focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all">
                  <option value="">Select province</option>
                  <?php
                  $provinces = [
                      'EC' => 'Eastern Cape',
                      'FS' => 'Free State',
                      'GP' => 'Gauteng',
                      'KZN' => 'KwaZulu-Natal',
                      'LP' => 'Limpopo',
                      'MP' => 'Mpumalanga',
                      'NW' => 'North West',
                      'NC' => 'Northern Cape',
                      'WC' => 'Western Cape',
                  ];
                  foreach ($provinces as $code => $label): ?>
                  <option value="<?= $code ?>" <?= ($sellerProfile['zone'] ?? '') === $code ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-3 text-outline pointer-events-none">expand_more</span>
              </div>
            </div>
            <div>
              <label for="postal_code" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">Postal Code</label>
              <input type="text" id="postal_code" name="postal_code" required
                     placeholder="e.g. 2196" maxlength="10" data-rule="postal"
                     value="<?= htmlspecialchars($sellerProfile['postal_code'] ?? '') ?>"
                     class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
            </div>
          </div>
          <div>
            <label for="mobile_number" class="block text-xs font-bold text-outline uppercase tracking-wider mb-2">
              Mobile Number <span class="normal-case font-normal text-outline">(for courier)</span>
            </label>
            <input type="tel" id="mobile_number" name="mobile_number" required
                   placeholder="e.g. 0821234567"
                   value="<?= htmlspecialchars($sellerProfile['mobile_number'] ?? '') ?>"
                   class="w-full bg-surface-container-low border-0 rounded-xl px-4 py-3 text-on-surface focus:ring-1 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all"/>
          </div>
        </div>
      </div>

    </div>

    <!-- Actions -->
    <div class="mt-6 flex items-center gap-4">
      <button type="submit"
              class="px-8 py-3 bg-primary text-on-primary rounded-xl font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95 shadow-sm">
        Save Changes
      </button>
      <a href="<?= BASE_URL ?>seller/dashboard"
         class="px-6 py-3 border border-outline-variant/40 text-primary rounded-xl font-headline font-semibold text-sm hover:bg-surface-container-low transition-all">
        Cancel
      </a>
    </div>

  </form>

</div>

</main>

<script>
function initProfileAutocomplete() {
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
    const key = document.getElementById('profile-form')?.dataset.mapsKey;
    if (!key) return;
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=initProfileAutocomplete';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();
</script>

<script src="<?= JS_URL ?>validation.js"></script>
</body>
</html>
