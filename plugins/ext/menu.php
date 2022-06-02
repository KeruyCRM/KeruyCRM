<?php

if (defined('CFG_PLUGIN_EXT_INSTALLED')) {
    require('plugins/ext/ext_menu.php');
} else {
    $app_plugin_menu['extension'][] = ['title' => TEXT_EXT_INSTALLATION, 'url' => url_for('ext/ext/')];
}