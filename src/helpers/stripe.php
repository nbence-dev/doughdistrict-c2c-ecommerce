<?php

require_once dirname(ROOT_PATH) . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

function stripe_connect_oauth_url($seller_profile_id)
{
    return 'https://connect.stripe.com/oauth/authorize?' . http_build_query([
        'response_type'  => 'code',
        'client_id'      => $_ENV['STRIPE_CONNECT_CLIENT_ID'],
        'scope'          => 'read_write',
        'redirect_uri'   => BASE_FULL_URL . '/seller/stripe/callback',
        'state'          => $seller_profile_id,
    ]);
}

function stripe_create_payment_intent($amount_cents, $currency)
{
    return \Stripe\PaymentIntent::create([
        'amount' => $amount_cents,
        'currency' => $currency,
        'payment_method_types' => ['card'],
    ]);
}
?>