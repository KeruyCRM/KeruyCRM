<?php

switch ($module_info['module']) {
    case 'cryptopro':
        require('plugins/ext/digital_signature_modules/cryptopro/components/signature_form.php');
        break;
}

