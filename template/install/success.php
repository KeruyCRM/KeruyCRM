<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= $TEXT_SUCCESS ?></h3>

<p><?= $TEXT_INSTALLATION_SUCCESS ?></p>

<input type="button" value="<?= $TEXT_BUTTON_LOGIN ?>" class="btn btn-primary" onClick="location.href='<?= $locationAdmin ?>'">