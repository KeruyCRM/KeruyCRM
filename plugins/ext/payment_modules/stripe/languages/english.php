<?php

define('TEXT_MODULE_STRIPE_TITLE', 'Stripe');
define('TEXT_MODULE_STRIPE_PUBLISHABLE_KEY', 'Publishable key');
define('TEXT_MODULE_STRIPE_SECRET_KEY', 'Secret key');
define(
    'TEXT_MODULE_STRIPE_SECRET_KEY_INFO',
    'See your keys here: <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">dashboard.stripe.com/account/apikeys</a>'
);
define('TEXT_MODULE_STRIPE_CURRENCY', 'Currency');
define(
    'TEXT_MODULE_STRIPE_CURRENCY_INFO',
    'See supported currencies here: <a href="https://stripe.com/docs/currencies" target="_blank">stripe.com/docs/currencies</a>'
);
define(
    'TEXT_MODULE_STRIPE_LANGUAGE_INFO',
    '<a href="https://support.stripe.com/questions/supported-languages-for-stripe-checkout" target="_blank">See supported languages</a>'
);
define('TEXT_MODULE_STRIPE_ENDPOINT_SECRET_KEY', 'Endpoint signing secret');
define(
    'TEXT_MODULE_STRIPE_ENDPOINT_SECRET_KEY_INFO',
    'Go to <a href="https://dashboard.stripe.com/test/webhooks" target="_blank">Webhooks</a> page and add Endpoint with next url: <br>%s<br>In Event type dropdown select "checkout.session.completed"'
);