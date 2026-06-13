<?php
// Stripe wrapper. The secret key is set once here for the whole request.
require_once dirname(ROOT_PATH) . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

// Builds the Stripe Connect onboarding link a seller clicks to link their own
// account. 'state' carries our seller_profile_id round-trip so the callback
// knows which profile to attach the returned account to, and redirect_uri is
// built from the live host so it works on prod and dev without hardcoding.
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

// A cart can span several sellers, which means several PaymentIntents confirmed
// with the same card in one go. Stripe won't let a PaymentMethod be reused
// across PaymentIntents unless it's attached to a Customer, so each checkout
// gets a Customer that every intent shares. Returns the customer id.
function stripe_create_customer($email, $name)
{
    return \Stripe\Customer::create([
        'email' => $email,
        'name'  => $name,
    ])->id;
}

// Creates the PaymentIntent that the checkout page confirms with the card.
// Amount is in the currency's smallest unit (cents), so callers multiply rands
// by 100 before passing it in. The customer is attached and setup_future_usage
// is set so the first confirmation saves the card to the customer, letting the
// remaining sellers' intents reuse it without a "PaymentMethod used before" error.
function stripe_create_payment_intent($amount_cents, $currency, $customer_id)
{
    return \Stripe\PaymentIntent::create([
        'amount' => $amount_cents,
        'currency' => $currency,
        'payment_method_types' => ['card'],
        'customer' => $customer_id,
        'setup_future_usage' => 'off_session',
    ]);
}
?>