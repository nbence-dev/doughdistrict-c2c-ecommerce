<?php
require_once ROOT_PATH . '/models/Product.php';
if ($path === 'cart') {
    // loop over $_SESSION['cart'] and display cart items using Product->findActive()
    $cart_items = [];
    $total = 0;

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $productModel = new Product($pdo);
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = $productModel->findActive($product_id);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
                $total += $product['price'] * $quantity;
            }
        }

    }





}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'cart/add') {
    // Handle adding item to cart
    if (isset($_POST['product_id']) && isset($_POST['qty'])) {

        $productModel = new Product($pdo);
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['qty']);

        $stock = $productModel->find($product_id)['stock_qty'];
        $existing_qty = $_SESSION['cart'][$product_id] ?? 0;

        $isActive = $productModel->findActive($product_id);
        if ($isActive) {
            if ($isActive['seller_user_id'] === $_SESSION['user_id']) {
                set_flash("You cannot add your own product to the cart.", 'danger');
                header('Location: ' . BASE_URL . 'product?id=' . $product_id);
                exit();
            }
            if ($existing_qty + $quantity > $stock) {
                set_flash("Cannot order more than currently available.", 'danger');
                header('Location: ' . BASE_URL . 'product?id=' . $product_id);
                exit();
            }

            if ($product_id > 0 && $quantity > 0) {
                // Add to cart logic
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
                header('Location: ' . BASE_URL . 'cart');
                exit();
            }
        } else {
            set_flash("Product not found or inactive.", 'danger');
            header('Location: ' . BASE_URL . 'cart');
            exit();
        }

    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'cart/remove') {
    // Handle removing item from cart
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            header('Location: ' . BASE_URL . 'cart');
            exit();
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === 'cart/update') {
    // Handle updating item quantity in cart


    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $productModel = new Product($pdo);
        $stock = $productModel->find($product_id)['stock_qty'];
        if ($stock < $quantity) {
            set_flash("Cannot order more than there is available.", "danger");
            header("Location: " . BASE_URL . "cart");
            exit();
        }
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header('Location: ' . BASE_URL . 'cart');
        exit();
    }
}
?>