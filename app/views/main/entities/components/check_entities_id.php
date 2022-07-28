<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

if (!$_GET['entities_id']) {
    redirect_to('entities/');
}   