<?php

define('TEXT_MODULE_SMARTYSTREETS_TITLE', 'smartyStreets');
define('TEXT_MODULE_SMARTYSTREETS_TYPE_SINGLE_ADDRESS_US', 'Single Address Validation (US only)');
define(
    'TEXT_MODULE_SMARTYSTREETS_RULES_INFO',
    'You will need to map fields manually. Example:<br>
address1: [1]<br>
address2: [2]<br>
locality: [3]<br>
administrative_area: [4]<br>
postal_code: [5]<br>
Where 1-5 are fields ID of input fields in your form.'
);
define('TEXT_MODULE_SMARTYSTREETS_AUTOVERIFY', 'Auto Verify');
define(
    'TEXT_MODULE_SMARTYSTREETS_AUTOVERIFY_INFO',
    'Verify the address as soon as enough of it has been typed by the user. This will only happen once per address before the form is submitted to avoid annoying the user.'
);
