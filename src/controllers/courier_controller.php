<?php
/**
 * courier_controller.php
 *
 * Handles the seller "Ship Order" flow:
 *   GET  seller/orders/ship?id=X  — show parcel dimension form
 *   POST seller/orders/ship?id=X  — submit to Shiplogic, store tracking reference
 *
 * $path, $pdo, $sellerProfile are injected by index.php before require_once.
 */

require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/SellerProfile.php';
require_once ROOT_PATH . '/helpers/courier.php';

$user = current_user();
$orderModel = new Order($pdo);
$userModel = new User($pdo);
$sellerProfileModel = new SellerProfile($pdo);
$sellerProfile = $sellerProfileModel->findByUserId($user['id']);

if (!$sellerProfile) {
    set_flash('Seller profile not found.', 'danger');
    header('Location: ' . BASE_URL . 'seller/dashboard');
    exit();
}

// ── 1. Load and validate the order ───────────────────────────────────────────
// We use findById() which returns ['order' => ..., 'items' => [...]]

$order_id = (int) ($_GET['id'] ?? 0);
$data = $orderModel->findById($order_id);
$order = $data['order'];
$items = $data['items'];

// Order must exist and belong to this seller's profile
if (!$order || (int) $order['seller_id'] !== (int) $sellerProfile['id']) {
    set_flash('Order not found.', 'danger');
    header('Location: ' . BASE_URL . 'seller/orders');
    exit();
}

// Prevent shipping an order that is already shipped, delivered, or cancelled
if (in_array($order['status'], ['shipped', 'delivered', 'cancelled'])) {
    set_flash('This order has already been shipped.', 'warning');
    header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $order_id);
    exit();
}

// ── 2. Guard: seller must have a complete collection address ──────────────────
// Shiplogic requires all address fields — if any are empty, redirect to profile.

$addressComplete = !empty($sellerProfile['street_address'])
    && !empty($sellerProfile['local_area'])
    && !empty($sellerProfile['city'])
    && !empty($sellerProfile['zone'])
    && !empty($sellerProfile['postal_code'])
    && !empty($sellerProfile['mobile_number']);

if (!$addressComplete) {
    set_flash('Please complete your collection address in your shop profile before shipping.', 'warning');
    header('Location: ' . BASE_URL . 'seller/profile');
    exit();
}

// ── 3. Load buyer and seller user rows ───────────────────────────────────────
// Shiplogic needs email addresses for both collection_contact and delivery_contact.

$buyerUser = $userModel->find($order['buyer_id']);
$sellerUser = $userModel->find($user['id']);

// ── 4. Handle POST — submit shipment to Shiplogic ────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $parcel = [
        'description' => trim($_POST['parcel_description'] ?? 'Baked goods'),
        'length_cm' => (float) ($_POST['length_cm'] ?? 0),
        'width_cm' => (float) ($_POST['width_cm'] ?? 0),
        'height_cm' => (float) ($_POST['height_cm'] ?? 0),
        'weight_kg' => (float) ($_POST['weight_kg'] ?? 0),
    ];

    // All dimensions must be positive numbers
    if (
        $parcel['length_cm'] <= 0 || $parcel['width_cm'] <= 0
        || $parcel['height_cm'] <= 0 || $parcel['weight_kg'] <= 0
    ) {
        set_flash('Please enter valid parcel dimensions and weight.', 'danger');
        header('Location: ' . BASE_URL . 'seller/orders/ship?id=' . $order_id);
        exit();
    }

    try {
        // Call the Shiplogic API — on success returns a shipment object with
        // 'id' (integer) and 'custom_tracking_reference' / 'short_tracking_reference'
        $shipment = shiplogic_create_shipment($order, $sellerProfile, $sellerUser, $buyerUser, $parcel);

        $trackingRef = $shipment['custom_tracking_reference'] ?? $shipment['short_tracking_reference'];

        // Shiplogic returns estimated_collection as an ISO datetime — store as MySQL DATETIME
        $estimatedCollection = null;
        if (!empty($shipment['estimated_collection'])) {
            $dt = DateTime::createFromFormat(DateTime::ATOM, $shipment['estimated_collection'])
               ?: DateTime::createFromFormat('Y-m-d\TH:i:s.uZ', $shipment['estimated_collection'])
               ?: new DateTime($shipment['estimated_collection']);
            $estimatedCollection = $dt ? $dt->format('Y-m-d H:i:s') : null;
        }

        $orderModel->storeTracking($order_id, $shipment['id'], $trackingRef, $estimatedCollection);

        set_flash('Shipment booked! Tracking reference: ' . $trackingRef, 'success');
        header('Location: ' . BASE_URL . 'seller/orders/detail?id=' . $order_id);
        exit();

    } catch (RuntimeException $e) {
        set_flash('Shiplogic error: ' . $e->getMessage(), 'danger');
        header('Location: ' . BASE_URL . 'seller/orders/ship?id=' . $order_id);
        exit();
    }
}

// ── 5. GET — fall through to the ship view ───────────────────────────────────
// View receives: $order, $items, $sellerProfile, $buyerUser
