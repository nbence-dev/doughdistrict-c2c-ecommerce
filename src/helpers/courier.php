<?php

function shiplogic_request($method, $endpoint, $body = null)
{
    $url = rtrim($_ENV['SHIPLOGIC_API_URL'], '/') . '/' . ltrim($endpoint, '/');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_ENV['SHIPLOGIC_API_KEY'],
        'Content-Type: application/json',
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Shiplogic request failed: cURL error');
    }

    $data = json_decode($response, true);

    if ($status >= 400) {
        $message = $data['message'] ?? $data['error'] ?? 'Unknown error';
        throw new RuntimeException('Shiplogic error (' . $status . '): ' . $message);
    }

    return $data;
}

function shiplogic_create_shipment($order, $seller_profile, $seller_user, $buyer_user, $parcel)
{
    $body = [
        'collection_address' => [
            'type'           => 'business',
            'company'        => $seller_profile['shop_name'],
            'street_address' => $seller_profile['street_address'],
            'local_area'     => $seller_profile['local_area'],
            'city'           => $seller_profile['city'],
            'zone'           => $seller_profile['zone'],
            'country'        => 'ZA',
            'code'           => $seller_profile['postal_code'],
        ],
        'collection_contact' => [
            'name'          => $seller_user['name'],
            'mobile_number' => $seller_profile['mobile_number'] ?? '',
            'email'         => $seller_user['email'],
        ],
        'delivery_address' => [
            'type'           => 'residential',
            'street_address' => $order['shipping_street'],
            'city'           => $order['shipping_city'],
            'zone'           => $order['shipping_province'],
            'country'        => 'ZA',
            'code'           => $order['shipping_postal_code'],
        ],
        'delivery_contact' => [
            'name'          => $order['shipping_name'],
            'mobile_number' => '',
            'email'         => $buyer_user['email'],
        ],
        'parcels' => [
            [
                'parcel_description'   => $parcel['description'],
                'submitted_length_cm'  => (float) $parcel['length_cm'],
                'submitted_width_cm'   => (float) $parcel['width_cm'],
                'submitted_height_cm'  => (float) $parcel['height_cm'],
                'submitted_weight_kg'  => (float) $parcel['weight_kg'],
            ],
        ],
        'service_level_code'  => 'ECO',
        'customer_reference'  => 'Order #' . $order['id'],
        'mute_notifications'  => false,
    ];

    return shiplogic_request('POST', '/shipments', $body);
}

function shiplogic_get_shipment($tracking_reference)
{
    return shiplogic_request('GET', '/shipments?tracking_reference=' . urlencode($tracking_reference));
}

function shiplogic_get_rate($sellerProfile, $parcel)
{
    $body = [
        'collection_address' => [
            'type'           => 'business',
            'company'        => $sellerProfile['shop_name'],
            'street_address' => $sellerProfile['street_address'],
            'local_area'     => $sellerProfile['local_area'],
            'city'           => $sellerProfile['city'],
            'zone'           => $sellerProfile['zone'],
            'country'        => 'ZA',
            'code'           => $sellerProfile['postal_code'],
        ],
        'delivery_address' => [
            'type'           => 'residential',
            'street_address' => '1 Sandton Drive',
            'local_area'     => 'Sandton',
            'city'           => 'Johannesburg',
            'zone'           => 'Gauteng',
            'country'        => 'ZA',
            'code'           => '2196',
        ],
        'parcels' => [[
            'parcel_description'  => $parcel['description'] ?? 'Baked goods',
            'submitted_length_cm' => (float) $parcel['length_cm'],
            'submitted_width_cm'  => (float) $parcel['width_cm'],
            'submitted_height_cm' => (float) $parcel['height_cm'],
            'submitted_weight_kg' => (float) $parcel['weight_kg'],
        ]],
        'service_level_code' => 'ECO',
    ];

    $data = shiplogic_request('POST', '/rates', $body);

    // Response may be an array of rate objects, or a wrapper with a 'rates' key
    $rates = $data['rates'] ?? (isset($data[0]) ? $data : []);
    foreach ($rates as $r) {
        if (($r['service_level_code'] ?? '') === 'ECO') {
            return (float) $r['rate'];
        }
    }
    return isset($rates[0]['rate']) ? (float) $rates[0]['rate'] : 0.0;
}
