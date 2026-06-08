<?php
/** @var string $path injected by index.php before require_once */
require_once ROOT_PATH . '/helpers/maps.php';
require_once ROOT_PATH . '/helpers/stripe.php';
require_once ROOT_PATH . '/helpers/emails.php';
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Address.php';
require_once ROOT_PATH . '/models/SellerProfile.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/User.php';

// Checkout happens in three routes:
//   checkout          - show the page and set up a Stripe PaymentIntent per seller
//   checkout/confirm  - after payment, verify it and write the orders
//   order/confirmation- the success page
// A single cart can contain products from several sellers, so it's split into one
// order (and one payment) per seller rather than charging everything together.
if ($path === 'checkout') {
    if (empty($_SESSION['cart'])) {
        header('Location: ' . BASE_URL . 'cart');
        exit();
    }
    $productModel = new Product($pdo);
    $sellerModel  = new SellerProfile($pdo);
    $addressModel = new Address($pdo);

    // Group cart items by seller; track max shipping cost per seller
    $seller_groups = [];
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $productModel->findActive($product_id);
        if (!$product) continue;

        $sid = $product['seller_id'];
        if (!isset($seller_groups[$sid])) {
            $seller = $sellerModel->findById($sid);
            $seller_groups[$sid] = [
                'seller'   => $seller,
                'items'    => [],
                'subtotal' => 0,
                'shipping' => 0,
            ];
        }
        $seller_groups[$sid]['items'][] = [
            'product'    => $product,
            'quantity'   => $quantity,
            'line_total' => $product['price'] * $quantity,
        ];
        $seller_groups[$sid]['subtotal'] += $product['price'] * $quantity;
        // Everything from one seller ships as a single parcel, so instead of
        // summing per-item shipping we charge the single highest rate in the group.
        $seller_groups[$sid]['shipping'] = max(
            $seller_groups[$sid]['shipping'],
            (float) ($product['shipping_cost'] ?? 0)
        );
    }

    if (empty($seller_groups)) {
        set_flash("Your cart has no active products.", 'warning');
        header('Location: ' . BASE_URL . 'cart');
        exit();
    }

    // Create a PaymentIntent per seller (items + shipping)
    $client_secrets = [];
    $_SESSION['pending_payment_intents'] = [];

    foreach ($seller_groups as $sid => $group) {
        $seller = $group['seller'];

        // Can't take money for a seller who hasn't linked a Stripe account, so
        // block the whole checkout until they finish onboarding.
        if (empty($seller['stripe_account_id']) || !$seller['stripe_onboarding_complete']) {
            set_flash('A seller in your cart has not completed Stripe setup.', 'danger');
            header('Location: ' . BASE_URL . 'cart');
            exit();
        }

        try {
            // Stripe wants the amount as an integer in the smallest unit, so the
            // rand total (items + shipping) is rounded and multiplied by 100.
            $amount_cents = (int) round(($group['subtotal'] + $group['shipping']) * 100);
            $intent = stripe_create_payment_intent($amount_cents, 'usd');
            $client_secrets[] = $intent->client_secret;
            // Remember each intent id + its shipping cost so checkout/confirm can
            // re-check payment and reuse the same shipping figure on the order.
            $_SESSION['pending_payment_intents'][$sid] = [
                'id'       => $intent->id,
                'shipping' => $group['shipping'],
            ];
        } catch (Exception $e) {
            set_flash('Failed to initialize payment: ' . $e->getMessage(), 'danger');
            header('Location: ' . BASE_URL . 'cart');
            exit();
        }
    }

    $addresses   = $addressModel->findByUser(current_user()['id']);
    $grand_total = array_sum(array_map(fn($g) => $g['subtotal'] + $g['shipping'], $seller_groups));

} elseif ($path === 'checkout/confirm') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['pending_payment_intents'])) {
        header('Location: ' . BASE_URL . 'checkout');
        exit();
    }
    $addressModel = new Address($pdo);
    $productModel = new Product($pdo);
    $orderModel   = new Order($pdo);
    $sellerModel  = new SellerProfile($pdo);

    // Resolve shipping address: either an existing saved one (by id) or a brand
    // new address typed into the form. A saved address is re-checked to make sure
    // it actually belongs to this buyer, so nobody can ship to someone else's id.
    $address_id = (int) ($_POST['address_id'] ?? 0);

    if ($address_id > 0) {
        $address = $addressModel->find($address_id);
        if (!$address || (int) $address['user_id'] !== (int) current_user()['id']) {
            set_flash('Invalid shipping address selected.', 'danger');
            header('Location: ' . BASE_URL . 'checkout');
            exit();
        }
    } else {
        $label       = trim($_POST['label'] ?? 'Home');
        $street      = trim($_POST['street'] ?? '');
        $local_area  = trim($_POST['local_area'] ?? '');
        $city        = trim($_POST['city'] ?? '');
        $province    = trim($_POST['province'] ?? '');
        $postal_code = trim($_POST['postal_code'] ?? '');

        if (empty($street) || empty($city) || empty($province) || empty($postal_code)) {
            set_flash('Please fill in all address fields.', 'danger');
            header('Location: ' . BASE_URL . 'checkout');
            exit();
        }

        if (!validate_za_address($street, $city, $province, $postal_code, $local_area)) {
            set_flash('The address you entered could not be verified. Please use a valid South African address.', 'danger');
            header('Location: ' . BASE_URL . 'checkout');
            exit();
        }

        $new_id  = $addressModel->create(current_user()['id'], $label, $street, $local_area, $city, $province, $postal_code);
        $address = $addressModel->find($new_id);
    }

    $shipping = [
        'name'        => current_user()['name'],
        'street'      => $address['street'],
        'city'        => $address['city'],
        'province'    => $address['province'],
        'postal_code' => $address['postal_code'],
    ];

    // Re-build seller groups from session cart
    $seller_groups = [];
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $productModel->findActive($product_id);
        if (!$product) continue;
        $sid = $product['seller_id'];
        if (!isset($seller_groups[$sid])) $seller_groups[$sid] = [];
        $seller_groups[$sid][] = ['product' => $product, 'quantity' => $quantity];
    }

    $order_ids = [];
    $userModel = new User($pdo);
    $buyer     = current_user();

    try {
        // Walk each seller's payment intent. We re-fetch it from Stripe rather
        // than trusting the browser, and only write an order once Stripe itself
        // reports the charge 'succeeded'. This is what stops a faked POST from
        // creating a paid order without real money changing hands.
        foreach ($_SESSION['pending_payment_intents'] as $seller_id => $pi) {
            $intent   = \Stripe\PaymentIntent::retrieve($pi['id']);
            $shipping_cost = (float) ($pi['shipping'] ?? 0);

            if ($intent->status !== 'succeeded') {
                set_flash('Payment not completed. Please try again.', 'danger');
                header('Location: ' . BASE_URL . 'checkout');
                exit();
            }

            $items = $seller_groups[$seller_id] ?? [];
            $items_total = array_sum(array_map(fn($i) => $i['product']['price'] * $i['quantity'], $items));

            $order_id = $orderModel->create(
                $buyer['id'],
                $seller_id,
                $items_total,
                $intent->id,
                $shipping,
                $shipping_cost
            );

            $order_items = array_map(fn($i) => [
                'product_id'   => $i['product']['id'],
                'product_name' => $i['product']['name'],
                'unit_price'   => $i['product']['price'],
                'quantity'     => $i['quantity'],
            ], $items);

            $orderModel->createItems($order_id, $order_items);

            // Now that the order is recorded, take the purchased quantities out of
            // stock so the same units can't be oversold on the next checkout.
            foreach ($items as $item) {
                $pdo->prepare('UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?')
                    ->execute([$item['quantity'], $item['product']['id']]);
            }

            // Notify seller of new order
            $sellerProfile = $sellerModel->findById($seller_id);
            $sellerUser    = $sellerProfile ? $userModel->find($sellerProfile['user_id']) : null;
            if ($sellerUser) {
                email_new_order(
                    $sellerUser['email'],
                    $sellerUser['name'],
                    $order_id,
                    $buyer['name'],
                    $order_items,
                    $items_total + $shipping_cost
                );
            }

            $order_ids[] = $order_id;
        }
    } catch (Exception $e) {
        set_flash('Order creation failed: ' . $e->getMessage(), 'danger');
        header('Location: ' . BASE_URL . 'checkout');
        exit();
    }

    // Notify buyer their order is confirmed
    email_order_confirmed($buyer['email'], $buyer['name'], $order_ids);

    // Clear the cart and pending intents so a refresh can't re-submit, and hand
    // the new order ids to the confirmation page via the session.
    unset($_SESSION['cart'], $_SESSION['pending_payment_intents']);
    $_SESSION['confirmed_order_ids'] = $order_ids;
    header('Location: ' . BASE_URL . 'order/confirmation');
    exit();

} elseif ($path === 'order/confirmation') {
    if (empty($_SESSION['confirmed_order_ids'])) {
        header('Location: ' . BASE_URL);
        exit();
    }
    $order_ids = $_SESSION['confirmed_order_ids'];
    unset($_SESSION['confirmed_order_ids']);
}
?>
