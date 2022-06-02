<?php

define('TEXT_MODULE_2CHECKOUT_TITLE', '2Checkout');
define('TEXT_MODULE_2CHECKOUT_SID', 'Your 2Checkout account number');
define(
    'TEXT_MODULE_2CHECKOUT_SID_INFO',
    'Enter live account number or <a href="https://sandbox.2checkout.com/sandbox" target="_blank">Create Sandbox Account</a><br>In 2checkout account settings set Header Redirect (Your URL)<br>Set Approved URL: ' . url_for_file(
        'api/ipn.php?module_id='
    ) . '%s' . '<br>This is Url for your customers to be sent to on a successful purchase.'
);
define('TEXT_MODULE_2CHECKOUT_SECRET_WORD', 'Secret Word');
define('TEXT_MODULE_2CHECKOUT_SECRET_WORD_INFO', 'Secret word for the 2CheckOut MD5 hash facility');