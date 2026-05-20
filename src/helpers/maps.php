<?php

function validate_za_address(string $street, string $city, string $province, string $postal_code, string $local_area = ''): bool
{
    // Basic sanity check: SA postal codes are 4 digits and province must be known
    $valid_provinces = ['GP','WC','EC','KZN','LP','MP','NW','NC','FS'];
    $basic_valid = preg_match('/^\d{4}$/', $postal_code)
                && in_array(strtoupper($province), $valid_provinces, true)
                && strlen(trim($street)) >= 3
                && strlen(trim($city)) >= 2;

    $key = getenv('ADDRESS_API_KEY');
    if (!$key) return $basic_valid;

    $parts = array_filter([$street, $local_area, $city, "$province $postal_code"]);
    $payload = json_encode([
        'address' => [
            'regionCode'   => 'ZA',
            'addressLines' => [implode(', ', $parts)],
        ],
    ]);

    $ch = curl_init('https://addressvalidation.googleapis.com/v1:validateAddress?key=' . $key);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 5,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return $basic_valid; // fall back to basic check on network error

    $data = json_decode($response, true);

    if (!isset($data['result'])) return $basic_valid; // API error — fall back

    // Accept if Google can geocode the address to any location on the map
    if (isset($data['result']['geocode']['location'])) return true;

    // Google couldn't place it at all — fall back to basic sanity check
    return $basic_valid;
}
