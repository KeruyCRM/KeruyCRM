<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

if (!\K::$fw->GET['entities_id']) {
    \Helpers\Urls::redirect_to('main/entities');
}   