<?php

require_once __DIR__ . '/mailer.php';

function email_base_url(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'doughdistrict.co.za';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . $host . '/';
}

function email_wrap(string $content): string
{
    $year = date('Y');
    return '
    <!DOCTYPE html>
    <html>
    <body style="margin:0;padding:0;background-color:#fbf9f1;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fbf9f1">
      <tr>
        <td align="center" bgcolor="#fbf9f1" style="padding:40px 16px;">
          <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:560px;">
            <tr>
              <td bgcolor="#6f4627" style="padding:24px 32px;border-radius:16px 16px 0 0;">
                <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:700;font-family:Arial,sans-serif;">&#x1F950; DoughDistrict</h1>
              </td>
            </tr>
            <tr>
              <td bgcolor="#ffffff" style="padding:32px;font-family:Arial,sans-serif;font-size:15px;color:#1b1c17;line-height:1.6;">
                ' . $content . '
              </td>
            </tr>
            <tr>
              <td bgcolor="#f5f4ec" style="padding:16px 32px;text-align:center;border-radius:0 0 16px 16px;">
                <p style="margin:0;font-size:12px;color:#83746b;font-family:Arial,sans-serif;">&copy; ' . $year . ' DoughDistrict &mdash; Baked with heart in South Africa.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    </body>
    </html>';
}

// ── 1. Buyer: order confirmed ─────────────────────────────────────────────────
function email_order_confirmed(string $buyer_email, string $buyer_name, array $order_ids): void
{
    $base = email_base_url();
    $ids = implode(', ', array_map(fn($id) => '#' . $id, $order_ids));
    $html = email_wrap('
      <h2 style="color:#6f4627;margin-top:0;">Your order is confirmed!</h2>
      <p style="color:#51443c;">Hi ' . htmlspecialchars($buyer_name) . ',</p>
      <p style="color:#51443c;">Thank you for your order. Your payment was successful and your seller has been notified.</p>
      <div style="background:#f5f4ec;border-radius:10px;padding:16px 20px;margin:24px 0;">
        <p style="margin:0;font-size:13px;color:#83746b;">Order reference</p>
        <p style="margin:4px 0 0;font-size:20px;font-weight:700;color:#6f4627;">' . $ids . '</p>
      </div>
      <a href="' . $base . 'orders" style="display:inline-block;background:#6f4627;color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;">View My Orders</a>
    ');
    send_email($buyer_email, $buyer_name, 'Order confirmed — DoughDistrict', $html);
}

// ── 2. Buyer: order shipped ───────────────────────────────────────────────────
function email_order_shipped(string $buyer_email, string $buyer_name, int $order_id, string $tracking_ref): void
{
    $base = email_base_url();
    $tracking_url = 'https://sandbox.shiplogic.com/track?S&ref=' . urlencode($tracking_ref);
    $html = email_wrap('
      <h2 style="color:#6f4627;margin-top:0;">Your order is on its way!</h2>
      <p style="color:#51443c;">Hi ' . htmlspecialchars($buyer_name) . ',</p>
      <p style="color:#51443c;">Your order <strong>#' . (int) $order_id . '</strong> has been handed to The Courier Guy and is on its way to you.</p>
      <div style="background:#f5f4ec;border-radius:10px;padding:16px 20px;margin:24px 0;">
        <p style="margin:0;font-size:13px;color:#83746b;">Tracking reference</p>
        <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#6f4627;letter-spacing:.05em;">' . htmlspecialchars($tracking_ref) . '</p>
      </div>
      <a href="' . $tracking_url . '" style="display:inline-block;background:#6f4627;color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;margin-bottom:16px;">Track My Order</a>
      <p style="color:#83746b;font-size:13px;margin-top:16px;">Or copy this link: ' . $tracking_url . '</p>
    ');
    send_email($buyer_email, $buyer_name, 'Your DoughDistrict order is on its way — ' . $tracking_ref, $html);
}

// ── 3. Buyer: order delivered ─────────────────────────────────────────────────
function email_order_delivered(string $buyer_email, string $buyer_name, int $order_id, array $items = []): void
{
    $base = email_base_url();

    $review_buttons = '';
    foreach ($items as $item) {
        if (!empty($item['product_id'])) {
            $url = $base . 'product?id=' . (int) $item['product_id'];
            $review_buttons .= '<a href="' . $url . '" style="display:inline-block;background:#924c00;color:#fff;padding:10px 22px;border-radius:999px;text-decoration:none;font-weight:700;margin:4px 4px 4px 0;font-size:14px;">Review: ' . htmlspecialchars($item['product_name']) . '</a> ';
        }
    }
    if (!$review_buttons) {
        $review_buttons = '<a href="' . $base . 'orders/detail?id=' . $order_id . '" style="display:inline-block;background:#924c00;color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;">Leave a Review</a>';
    }

    $html = email_wrap('
      <h2 style="color:#6f4627;margin-top:0;">Your order has been delivered!</h2>
      <p style="color:#51443c;">Hi ' . htmlspecialchars($buyer_name) . ',</p>
      <p style="color:#51443c;">Order <strong>#' . (int) $order_id . '</strong> has been marked as delivered. We hope you love what you received!</p>
      <p style="color:#51443c;">If you enjoyed your order, please take a moment to leave a review — it means a lot to the home baker who made it.</p>
      <div style="margin-top:16px;">' . $review_buttons . '</div>
    ');
    send_email($buyer_email, $buyer_name, 'Your DoughDistrict order has arrived — how was it?', $html);
}

// ── 4. Seller: new order received ─────────────────────────────────────────────
function email_new_order(string $seller_email, string $seller_name, int $order_id, string $buyer_name, array $items, float $total): void
{
    $base = email_base_url();
    $order_url = $base . 'seller/orders/detail?id=' . $order_id;

    $rows = '';
    foreach ($items as $item) {
        $rows .= '<tr>
          <td style="padding:8px 0;color:#1b1c17;border-bottom:1px solid #f5f4ec;">' . htmlspecialchars($item['product_name']) . ' &times; ' . (int) $item['quantity'] . '</td>
          <td style="padding:8px 0;color:#924c00;font-weight:700;text-align:right;border-bottom:1px solid #f5f4ec;">R ' . number_format($item['unit_price'] * $item['quantity'], 2) . '</td>
        </tr>';
    }

    $html = email_wrap('
      <h2 style="color:#6f4627;margin-top:0;">You have a new order!</h2>
      <p style="color:#51443c;">Hi ' . htmlspecialchars($seller_name) . ',</p>
      <p style="color:#51443c;"><strong>' . htmlspecialchars($buyer_name) . '</strong> just placed order <strong>#' . (int) $order_id . '</strong>.</p>
      <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        ' . $rows . '
        <tr>
          <td style="padding:12px 0 0;font-weight:700;color:#1b1c17;">Total</td>
          <td style="padding:12px 0 0;font-weight:700;color:#924c00;text-align:right;font-size:18px;">R ' . number_format($total, 2) . '</td>
        </tr>
      </table>
      <a href="' . $order_url . '" style="display:inline-block;background:#6f4627;color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;margin-top:8px;">View Order</a>
    ');
    send_email($seller_email, $seller_name, 'New order #' . $order_id . ' — DoughDistrict', $html);
}

// ── 5. Admin invite ───────────────────────────────────────────────────────────
function email_admin_invite(string $to_email, string $to_name, string $temp_password): void
{
    $base       = email_base_url();
    $configured = getenv('MAIL_FROM') ?: ($_ENV['MAIL_FROM'] ?? '');
    preg_match('/@([a-zA-Z0-9.\-]+)/', $configured, $m);
    $domain = $m[1] ?? 'doughdistrict.co.za';
    $from   = 'DoughDistrict <no-reply@' . $domain . '>';

    $html = email_wrap('
      <h2 style="color:#6f4627;margin-top:0;">You\'ve been invited to DoughDistrict</h2>
      <p style="color:#51443c;">Hi ' . htmlspecialchars($to_name) . ',</p>
      <p style="color:#51443c;">An admin account has been created for you on DoughDistrict. Use the temporary password below to sign in — you\'ll be asked to set a new password immediately.</p>
      <div style="background:#f5f4ec;border-radius:10px;padding:16px 20px;margin:24px 0;">
        <p style="margin:0;font-size:13px;color:#83746b;">Temporary password</p>
        <p style="margin:4px 0 0;font-size:22px;font-weight:700;color:#6f4627;letter-spacing:.1em;">' . htmlspecialchars($temp_password) . '</p>
      </div>
      <a href="' . $base . 'login" style="display:inline-block;background:#6f4627;color:#fff;padding:12px 28px;border-radius:999px;text-decoration:none;font-weight:700;">Sign In</a>
      <p style="color:#83746b;font-size:12px;margin-top:24px;">If you were not expecting this invitation, you can safely ignore this email.</p>
    ');

    send_email($to_email, $to_name, 'Your DoughDistrict admin invitation', $html, $from);
}
