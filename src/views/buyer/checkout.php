<?php
/**
 * @var string[]  $client_secrets  Stripe PaymentIntent client secrets, one per seller
 * @var array[]   $seller_groups   Cart items grouped by seller
 * @var array[]   $addresses       Buyer's saved shipping addresses
 * @var float     $grand_total     Sum of all seller subtotals
 */
include __DIR__ . '/layout.php'; ?>

<style>
    .checkout-section-card {
        background: #fff;
        border: 1px solid var(--dd-outline-var);
        border-radius: 1rem;
        padding: 1.75rem;
    }
    .step-badge {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8125rem;
        flex-shrink: 0;
    }
    .address-radio-card {
        border: 1.5px solid var(--dd-outline-var);
        border-radius: .75rem;
        padding: .875rem 1rem;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        width: 100%;
        margin-bottom: .75rem;
    }
    .address-radio-card:has(input:checked) {
        border-color: var(--dd-primary);
        background: rgba(111, 70, 39, 0.04);
    }
    .address-radio-card input[type="radio"] {
        margin-top: .2rem;
        flex-shrink: 0;
        accent-color: var(--dd-primary);
    }
    .dd-field {
        background: var(--dd-surface-low);
        border: none;
        border-radius: .75rem;
        padding: .75rem 1rem;
        width: 100%;
        color: var(--dd-on-surface);
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: .9375rem;
        transition: box-shadow .15s;
    }
    .dd-field:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(111, 70, 39, 0.25);
        background: #fff;
    }
    .dd-label {
        display: block;
        font-size: .8125rem;
        font-weight: 600;
        color: var(--dd-on-surface-var);
        margin-bottom: .375rem;
    }
    .stripe-card-wrapper {
        background: var(--dd-surface-low);
        border-radius: .75rem;
        padding: .875rem 1rem;
    }
    .order-summary-card {
        background: #fff;
        border: 1px solid var(--dd-outline-var);
        border-radius: 1rem;
        padding: 1.75rem;
    }
    .item-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: .5rem;
        flex-shrink: 0;
    }
    .item-thumb-placeholder {
        width: 60px;
        height: 60px;
        border-radius: .5rem;
        background: var(--dd-surface-low);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .seller-divider {
        font-size: .8125rem;
        font-weight: 600;
        color: var(--dd-on-surface-var);
        display: flex;
        align-items: center;
        gap: .375rem;
        margin-bottom: .875rem;
    }
    .seller-divider .material-symbols-outlined { font-size: 1rem; }
    .pay-btn { border-radius: .75rem; padding: .875rem; font-size: 1rem; font-weight: 700; }
</style>

<main class="container py-5 mt-2">

    <div class="mb-5">
        <h1 class="fw-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">Checkout</h1>
        <p style="color: var(--dd-on-surface-var);">Complete your order from South Africa's finest makers.</p>
    </div>

    <form id="checkout-form"
          action="<?= BASE_URL ?>checkout/confirm"
          method="POST"
          data-pk="<?= htmlspecialchars(getenv('STRIPE_PUBLISHABLE_KEY') ?: '') ?>"
          data-secrets="<?= htmlspecialchars(json_encode($client_secrets)) ?>"
          data-maps-key="<?= htmlspecialchars(getenv('ADDRESS_API_KEY') ?: '') ?>">

        <div class="row g-4 g-lg-5">

            <!-- ── Left: Shipping + Payment ──────────────────────────────── -->
            <div class="col-lg-7">

                <!-- Step 1: Shipping Address -->
                <div class="checkout-section-card mb-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="step-badge" style="background: var(--dd-secondary); color: #fff;">1</span>
                        <h2 class="h5 fw-bold mb-0">Shipping Address</h2>
                    </div>

                    <?php if (!empty($addresses)): ?>

                        <?php $firstAddr = true; foreach ($addresses as $addr): ?>
                            <label class="address-radio-card">
                                <input type="radio" name="address_id"
                                       value="<?= (int) $addr['id'] ?>"
                                       <?= $firstAddr ? 'checked' : '' ?>>
                                <div>
                                    <div class="fw-semibold small"><?= htmlspecialchars($addr['label']) ?></div>
                                    <div class="small mt-1" style="color: var(--dd-on-surface-var);">
                                        <?= htmlspecialchars($addr['street']) ?><?= !empty($addr['local_area']) ? ', ' . htmlspecialchars($addr['local_area']) : '' ?>,
                                        <?= htmlspecialchars($addr['city']) ?>,
                                        <?= htmlspecialchars($addr['province']) ?>
                                        <?= htmlspecialchars($addr['postal_code']) ?>
                                    </div>
                                </div>
                            </label>
                        <?php $firstAddr = false; endforeach; ?>

                        <label class="address-radio-card mb-0">
                            <input type="radio" name="address_id" value="0" id="new-address-toggle">
                            <span class="fw-semibold small">Use a new address</span>
                        </label>

                        <!-- New address form: hidden until radio selected -->
                        <div id="new-address-fields" class="d-none mt-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="dd-label" for="label">Address Label</label>
                                    <input type="text" name="label" id="label" class="dd-field" placeholder="Home, Work…" value="Home">
                                </div>
                                <div class="col-12">
                                    <label class="dd-label" for="street">Street Address</label>
                                    <input type="text" name="street" id="street" class="dd-field" placeholder="2 Protea Heights">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="local_area">Suburb</label>
                                    <input type="text" name="local_area" id="local_area" class="dd-field" placeholder="Bellairs">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="city">City</label>
                                    <input type="text" name="city" id="city" class="dd-field" placeholder="Johannesburg">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="province">Province</label>
                                    <select name="province" id="province" class="dd-field">
                                        <option value="" disabled selected>Select Province</option>
                                        <option value="EC">Eastern Cape</option>
                                        <option value="FS">Free State</option>
                                        <option value="GP">Gauteng</option>
                                        <option value="KZN">KwaZulu-Natal</option>
                                        <option value="LP">Limpopo</option>
                                        <option value="MP">Mpumalanga</option>
                                        <option value="NW">North West</option>
                                        <option value="NC">Northern Cape</option>
                                        <option value="WC">Western Cape</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="postal_code">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code" class="dd-field" placeholder="2188">
                                </div>
                            </div>
                        </div>

                    <?php else: ?>

                        <!-- No saved addresses: show form directly -->
                        <input type="hidden" name="address_id" value="0">
                        <div id="new-address-fields">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="dd-label" for="label">Address Label</label>
                                    <input type="text" name="label" id="label" class="dd-field" placeholder="Home, Work…" value="Home">
                                </div>
                                <div class="col-12">
                                    <label class="dd-label" for="street">Street Address</label>
                                    <input type="text" name="street" id="street" class="dd-field" placeholder="2 Protea Heights">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="local_area">Suburb</label>
                                    <input type="text" name="local_area" id="local_area" class="dd-field" placeholder="Bellairs">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="city">City</label>
                                    <input type="text" name="city" id="city" class="dd-field" placeholder="Johannesburg">
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="province">Province</label>
                                    <select name="province" id="province" class="dd-field">
                                        <option value="" disabled selected>Select Province</option>
                                        <option value="EC">Eastern Cape</option>
                                        <option value="FS">Free State</option>
                                        <option value="GP">Gauteng</option>
                                        <option value="KZN">KwaZulu-Natal</option>
                                        <option value="LP">Limpopo</option>
                                        <option value="MP">Mpumalanga</option>
                                        <option value="NW">North West</option>
                                        <option value="NC">Northern Cape</option>
                                        <option value="WC">Western Cape</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="dd-label" for="postal_code">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code" class="dd-field" placeholder="2188">
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>

                <!-- Step 2: Payment -->
                <div class="checkout-section-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="step-badge" style="background: var(--dd-surface-low); color: var(--dd-on-surface-var);">2</span>
                        <h2 class="h5 fw-bold mb-0">Payment</h2>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small fw-semibold">Credit or Debit Card</span>
                        <span class="material-symbols-outlined" style="color: var(--dd-outline); font-size: 1.375rem;">credit_card</span>
                    </div>

                    <div class="stripe-card-wrapper">
                        <div id="card-element"></div>
                    </div>
                    <div id="card-errors" class="text-danger small mt-2" role="alert"></div>

                    <p class="small fst-italic mt-3 mb-0" style="color: var(--dd-outline);">
                        Payments are processed securely by Stripe. We never store your card details.
                    </p>
                </div>

            </div>

            <!-- ── Right: Order Summary (sticky) ─────────────────────────── -->
            <div class="col-lg-5">
                <div class="sticky-top" style="top: 76px;">

                    <div class="order-summary-card mb-4">
                        <h3 class="fw-bold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif; font-size: 1.25rem;">Order Summary</h3>

                        <?php
                        $items_subtotal   = array_sum(array_column($seller_groups, 'subtotal'));
                        $shipping_subtotal = array_sum(array_column($seller_groups, 'shipping'));
                        ?>
                        <?php foreach ($seller_groups as $group): ?>
                            <div class="mb-4">
                                <div class="seller-divider">
                                    <span class="material-symbols-outlined">storefront</span>
                                    <?= htmlspecialchars($group['seller']['shop_name']) ?>
                                </div>

                                <?php foreach ($group['items'] as $item): ?>
                                    <div class="d-flex gap-3 mb-3 align-items-start">
                                        <?php if (!empty($item['product']['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($item['product']['image_url']) ?>"
                                                 alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                                 class="item-thumb">
                                        <?php else: ?>
                                            <div class="item-thumb-placeholder">
                                                <span class="material-symbols-outlined" style="color: var(--dd-outline); font-size: 1.25rem;">bakery_dining</span>
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between gap-2">
                                                <span class="fw-semibold small lh-sm"><?= htmlspecialchars($item['product']['name']) ?></span>
                                                <span class="small fw-bold text-nowrap" style="color: var(--dd-secondary);">
                                                    R&nbsp;<?= number_format($item['line_total'], 2) ?>
                                                </span>
                                            </div>
                                            <div class="small mt-1" style="color: var(--dd-on-surface-var);">
                                                Qty: <?= (int) $item['quantity'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ($group['shipping'] > 0): ?>
                                <div class="d-flex justify-content-between small pb-3 border-bottom" style="color: var(--dd-on-surface-var); border-color: var(--dd-outline-var) !important;">
                                    <span class="d-flex align-items-center gap-1">
                                        <span class="material-symbols-outlined" style="font-size: .875rem;">local_shipping</span>
                                        Shipping (ECO)
                                    </span>
                                    <span>R&nbsp;<?= number_format($group['shipping'], 2) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="border-top pt-3" style="border-color: var(--dd-outline-var) !important;">
                            <div class="d-flex justify-content-between small mb-2" style="color: var(--dd-on-surface-var);">
                                <span>Items</span>
                                <span>R&nbsp;<?= number_format($items_subtotal, 2) ?></span>
                            </div>
                            <?php if ($shipping_subtotal > 0): ?>
                            <div class="d-flex justify-content-between small mb-2" style="color: var(--dd-on-surface-var);">
                                <span>Shipping</span>
                                <span>R&nbsp;<?= number_format($shipping_subtotal, 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span style="color: var(--dd-primary);">R&nbsp;<?= number_format($grand_total, 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="pay-btn"
                            class="btn btn-dd-primary w-100 d-flex align-items-center justify-content-center gap-2 pay-btn">
                        <span class="material-symbols-outlined">lock</span>
                        Pay R&nbsp;<?= number_format($grand_total, 2) ?>
                    </button>

                    <p class="text-center small mt-3 mb-0" style="color: var(--dd-outline);">
                        <span class="material-symbols-outlined" style="font-size: .875rem; vertical-align: middle;">verified_user</span>
                        SSL Encrypted · Powered by Stripe
                    </p>

                </div>
            </div>

        </div>
    </form>

</main>

<script src="https://js.stripe.com/v3/"></script>
<script src="<?= JS_URL ?>checkout.js"></script>

<script>
function initAddressAutocomplete() {
    const streetInput = document.getElementById('street');
    if (!streetInput) return;

    const provinceMap = {
        'Gauteng': 'GP', 'Western Cape': 'WC', 'Eastern Cape': 'EC',
        'KwaZulu-Natal': 'KZN', 'Limpopo': 'LP', 'Mpumalanga': 'MP',
        'North West': 'NW', 'Northern Cape': 'NC', 'Free State': 'FS'
    };

    let autocomplete = null;

    function attachAutocomplete() {
        if (autocomplete) return;
        autocomplete = new google.maps.places.Autocomplete(streetInput, {
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
            const provinceEl = document.getElementById('province');
            const postalEl   = document.getElementById('postal_code');

            if (suburbEl)   suburbEl.value   = suburb;
            if (cityEl)     cityEl.value     = city;
            if (postalEl)   postalEl.value   = postalCode;
            if (provinceEl && provinceMap[province]) provinceEl.value = provinceMap[province];
        });
    }

    // If field is already visible (no saved addresses), attach now.
    // Otherwise defer to first focus so positioning is correct when the
    // hidden new-address section is revealed.
    if (streetInput.offsetParent !== null) {
        attachAutocomplete();
    } else {
        streetInput.addEventListener('focus', attachAutocomplete, { once: true });
    }
}
</script>
<script>
(function () {
    const key = document.getElementById('checkout-form')?.dataset.mapsKey;
    if (!key) return;
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=initAddressAutocomplete';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();
</script>

</div><!-- /#buyer-main -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
