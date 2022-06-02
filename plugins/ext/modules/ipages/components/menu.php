<?php
/**
 *add info pages
 */

$menu = ipages::build_menu();

if (count($menu)) {
    if (!isset($app_plugin_menu['menu'])) {
        $app_plugin_menu['menu'] = [];
    }

    $app_plugin_menu['menu'] = array_merge($app_plugin_menu['menu'], $menu);
}


//
if ((in_array($app_user['id'], explode(',', CFG_IPAGES_ACCESS_TO_USERS)) and strlen(CFG_IPAGES_ACCESS_TO_USERS)) or
    (in_array($app_user['group_id'], explode(',', CFG_IPAGES_ACCESS_TO_USERS_GROUP)) and strlen(
            CFG_IPAGES_ACCESS_TO_USERS_GROUP
        ))) {
    $app_plugin_menu['menu'][] = [
        'title' => TEXT_EXT_MENU_IPAGES,
        'url' => url_for('ext/ipages/configuration'),
        'class' => 'fa-info-circle'
    ];
}
