<?php

define('TEXT_MODULE_STRIPE_SUBSCRIPTION_TITLE', 'Stripe Subscription');
define('TEXT_MODULE_STRIPE_SUBSCRIPTION_PUBLISHABLE_KEY', 'Publishable key');
define('TEXT_MODULE_STRIPE_SUBSCRIPTION_SECRET_KEY', 'Secret key');
define(
    'TEXT_MODULE_STRIPE_SUBSCRIPTION_SECRET_KEY_INFO',
    'See your keys here: <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">dashboard.stripe.com/account/apikeys</a>'
);
define('TEXT_MODULE_STRIPE_SUBSCRIPTION_PLAN', 'Field ID where Plan ID stored');
define(
    'TEXT_MODULE_STRIPE_SUBSCRIPTION_PLAN_INFO',
    'Enter input field ID where Plan ID will be stored. You can create several records with different plans.<br>After you create the pricing plan, record the plan ID so it can be used in subsequent steps. It\'s displayed on the pricing plan page.'
);
define(
    'TEXT_MODULE_STRIPE_SUBSCRIPTION_LANGUAGE_INFO',
    '<a href="https://support.stripe.com/questions/supported-languages-for-stripe-checkout" target="_blank">See supported languages</a>'
);
define('TEXT_MODULE_STRIPE_SUBSCRIPTION_ENDPOINT_SECRET_KEY', 'Endpoint signing secret');
define(
    'TEXT_MODULE_STRIPE_SUBSCRIPTION_ENDPOINT_SECRET_KEY_INFO',
    'Go to <a href="https://dashboard.stripe.com/test/webhooks" target="_blank">Webhooks</a> page and add Endpoint with next url: <br>%s<br>In Event type dropdown select "checkout.session.completed"'
);

define('TEXT_MODULE_STRIPE_SUBSCRIPTION_SUBSCRIPTION_ID', 'Subscription ID');
define('TEXT_MODULE_STRIPE_SUBSCRIPTION_STATUS', 'Subscription Status');