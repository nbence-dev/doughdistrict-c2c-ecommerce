<?php
require_once ROOT_PATH . '/models/SellerProfile.php';
require_once ROOT_PATH . '/models/Product.php';
require_once ROOT_PATH . '/models/Category.php';
require_once ROOT_PATH . '/helpers/r2.php';

// ── Onboarding ────────────────────────────────────────────────────────────────
// Available to any logged-in user — a buyer becomes a seller on submit.

if ($path === 'seller/onboard') {
    $sellerProfileModel = new SellerProfile($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Already a seller — no need to onboard again
        if ($sellerProfileModel->findByUserId(current_user()['id'])) {
            set_flash("You are already registered as a seller.", 'info');
            header('Location: ' . BASE_URL . 'seller/dashboard');
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shop_name'], $_POST['bio'])) {
        $shop_name     = trim($_POST['shop_name']);
        $bio           = trim($_POST['bio']);
        $street        = trim($_POST['street_address'] ?? '');
        $local_area    = trim($_POST['local_area'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $zone          = trim($_POST['zone'] ?? '');
        $postal_code   = trim($_POST['postal_code'] ?? '');
        $mobile_number = trim($_POST['mobile_number'] ?? '');

        if (empty($shop_name) || empty($bio) || empty($street) || empty($local_area) || empty($city) || empty($zone) || empty($postal_code) || empty($mobile_number)) {
            set_flash("All fields are required.", 'danger');
            header('Location: ' . BASE_URL . 'seller/onboard');
            exit();
        }

        if ($sellerProfileModel->nameExists($shop_name)) {
            set_flash("Shop name '" . htmlspecialchars($shop_name) . "' is already taken. Please choose another.", 'danger');
            header('Location: ' . BASE_URL . 'seller/onboard');
            exit();
        }

        try {
            $sellerProfileModel->create(current_user()['id'], $shop_name, $bio);
            $profile = $sellerProfileModel->findByUserId(current_user()['id']);
            $sellerProfileModel->updateAddress($profile['id'], $street, $local_area, $city, $zone, $postal_code, $mobile_number);

            $userModel = new User($pdo);
            $userModel->setRole(current_user()['id'], 'seller');

            set_flash("Seller profile created. Welcome to DoughDistrict!", 'success');
            header('Location: ' . BASE_URL . 'seller/dashboard');
            exit();
        } catch (PDOException $e) {
            set_flash("An error occurred while creating your seller profile. Please try again.", 'danger');
            header('Location: ' . BASE_URL . 'seller/onboard');
            exit();
        }
    }

    // ── All other seller routes ───────────────────────────────────────────────────
// Require a seller profile. If missing, push back to onboarding.

} else {
    $sellerProfileModel = new SellerProfile($pdo);
    $sellerProfile = $sellerProfileModel->findByUserId(current_user()['id']);

    if (!$sellerProfile) {
        set_flash("Please complete your seller profile first.", 'warning');
        header('Location: ' . BASE_URL . 'seller/onboard');
        exit();
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    if ($path === 'seller/dashboard') {
        $productModel = new Product($pdo);
        $products = $productModel->findBySeller($sellerProfile['id']);

        // ── Shop profile (edit shop name / bio) ───────────────────────────────────

    } elseif ($path === 'seller/profile') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shop_name'], $_POST['bio'])) {
            $shop_name     = trim($_POST['shop_name']);
            $bio           = trim($_POST['bio']);
            $street        = trim($_POST['street_address'] ?? '');
            $local_area    = trim($_POST['local_area'] ?? '');
            $city          = trim($_POST['city'] ?? '');
            $zone          = trim($_POST['zone'] ?? '');
            $postal_code   = trim($_POST['postal_code'] ?? '');
            $mobile_number = trim($_POST['mobile_number'] ?? '');

            if (empty($shop_name) || empty($bio)) {
                set_flash("Shop name and bio cannot be empty.", 'danger');
            } elseif ($sellerProfileModel->nameExists($shop_name, $sellerProfile['id'])) {
                set_flash("That shop name is already taken.", 'danger');
            } else {
                $sellerProfileModel->update($sellerProfile['id'], $shop_name, $bio);
                if (!empty($street) && !empty($city) && !empty($zone) && !empty($postal_code)) {
                    $sellerProfileModel->updateAddress($sellerProfile['id'], $street, $local_area, $city, $zone, $postal_code, $mobile_number);
                }
                set_flash("Shop profile updated.", 'success');
                header('Location: ' . BASE_URL . 'seller/profile');
                exit();
            }
        }

        // ── Products list ─────────────────────────────────────────────────────────

    } elseif ($path === 'seller/products') {
        $productModel = new Product($pdo);
        $products = $productModel->findBySeller($sellerProfile['id']);

        // ── Create product ────────────────────────────────────────────────────────

    } elseif ($path === 'seller/products/create') {
        $categoryModel = new Category($pdo);
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock_qty = (int) ($_POST['stock_qty'] ?? 0);
            $category_id = (int) ($_POST['category_id'] ?? 0);

            $errors = [];
            if ($name === '')
                $errors[] = "Product name is required.";
            if ($description === '')
                $errors[] = "Description is required.";
            if ($price <= 0)
                $errors[] = "Price must be greater than zero.";
            if ($stock_qty < 0)
                $errors[] = "Stock quantity cannot be negative.";
            if ($category_id <= 0)
                $errors[] = "Please select a category.";
            if (empty($_FILES['image']['name']))
                $errors[] = "A product image is required.";

            if (empty($errors)) {
                try {
                    $image_url = upload_to_r2($_FILES['image']);
                    $productModel = new Product($pdo);
                    $productModel->create($sellerProfile['id'], $category_id, $name, $description, $price, $stock_qty, $image_url);
                    set_flash("Product listed successfully. It is pending admin approval before going live.", 'success');
                    header('Location: ' . BASE_URL . 'seller/products');
                    exit();
                } catch (RuntimeException $e) {
                    set_flash($e->getMessage(), 'danger');
                } catch (PDOException $e) {
                    set_flash("Failed to save product. Please try again.", 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    set_flash($error, 'danger');
                }
            }
        }

        // ── Edit product ──────────────────────────────────────────────────────────

    } elseif ($path === 'seller/products/edit') {
        $productId = (int) ($_GET['id'] ?? 0);
        $productModel = new Product($pdo);
        $product = $productModel->find($productId);

        // Ownership check — prevent editing another seller's product
        if (!$product || (int) $product['seller_id'] !== (int) $sellerProfile['id']) {
            set_flash("Product not found.", 'danger');
            header('Location: ' . BASE_URL . 'seller/products');
            exit();
        }

        $categoryModel = new Category($pdo);
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock_qty = (int) ($_POST['stock_qty'] ?? 0);
            $category_id = (int) ($_POST['category_id'] ?? 0);

            $errors = [];
            if ($name === '')
                $errors[] = "Product name is required.";
            if ($description === '')
                $errors[] = "Description is required.";
            if ($price <= 0)
                $errors[] = "Price must be greater than zero.";
            if ($stock_qty < 0)
                $errors[] = "Stock quantity cannot be negative.";
            if ($category_id <= 0)
                $errors[] = "Please select a category.";

            if (empty($errors)) {
                try {
                    // Only upload a new image if the seller chose one
                    $image_url = $product['image_url'];
                    if (!empty($_FILES['image']['name'])) {
                        $image_url = upload_to_r2($_FILES['image']);
                    }

                    $productModel->update($productId, $category_id, $name, $description, $price, $stock_qty, $image_url);
                    set_flash("Product updated successfully.", 'success');
                    header('Location: ' . BASE_URL . 'seller/products');
                    exit();
                } catch (RuntimeException $e) {
                    set_flash($e->getMessage(), 'danger');
                } catch (PDOException $e) {
                    set_flash("Failed to update product. Please try again.", 'danger');
                }
            } else {
                foreach ($errors as $error) {
                    set_flash($error, 'danger');
                }
            }
        }

        // ── Delete product ────────────────────────────────────────────────────────

    } elseif ($path === 'seller/products/delete') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
            header('Location: ' . BASE_URL . 'seller/products');
            exit();
        }

        $productId = (int) $_POST['product_id'];
        $productModel = new Product($pdo);
        $product = $productModel->find($productId);

        // Ownership check
        if (!$product || (int) $product['seller_id'] !== (int) $sellerProfile['id']) {
            set_flash("Product not found.", 'danger');
            header('Location: ' . BASE_URL . 'seller/products');
            exit();
        }

        try {
            if (!empty($product['image_url'])) {
                delete_from_r2($product['image_url']);
            }
            $productModel->delete($productId);
            set_flash("Product deleted.", 'success');
        } catch (PDOException $e) {
            set_flash("Failed to delete product. Please try again.", 'danger');
        }

        header('Location: ' . BASE_URL . 'seller/products');
        exit();

        // ── Stripe Connect — Phase 3 stub ─────────────────────────────────────────

    } elseif ($path === 'seller/stripe/connect') {
        // TODO Phase 3: redirect to Stripe Connect OAuth URL
        require_once ROOT_PATH . '/helpers/stripe.php';
        header('Location: ' . stripe_connect_oauth_url($sellerProfile['id']));
        exit();


    } elseif ($path === 'seller/stripe/callback') {
        // TODO Phase 3: exchange OAuth code for Stripe account ID, store in seller_profiles
        require_once ROOT_PATH . '/helpers/stripe.php';
        if (isset($_GET['error'])) {
            set_flash('Stripe connection cancelled.', 'warning');
            header('Location: ' . BASE_URL . 'seller/dashboard');
            exit();
        }
        $code = $_GET['code'] ?? '';
        $state = (int) ($_GET['state'] ?? 0);

        if ($state !== (int) $sellerProfile['id'] || $code === '') {
            set_flash('Invalid Stripe callback', 'danger');
            header('Location: ' . BASE_URL . 'seller/dashboard');
            exit();
        }
        try {
            $response = \Stripe\OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
            $sellerProfileModel->setStripeAccount($sellerProfile['id'], $response->stripe_user_id);
            $sellerProfileModel->setStripeOnboardingComplete($sellerProfile['id'], true);
            set_flash('Stripe account connected successfully!', 'success');
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            set_flash('Failed to connect to Stripe: ' . $e->getMessage(), 'danger');


        }
        header('Location: ' . BASE_URL . 'seller/dashboard');
        exit();
    }
}
?>