<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?><h3 class="page-title"><?php
    echo TEXT_HEADING_CHECK_VERSION ?></h3>
<p><?php
    echo TEXT_VERSION_INFO . ' ' . PROJECT_VERSION . ' ' . PROJECT_VERSION_DEV ?></p>

<p><?php
    echo(defined('PLUGIN_EXT_VERSION') ? TEXT_HEADING_EXTENSION . ' ' . PLUGIN_EXT_VERSION : '') ?></p>