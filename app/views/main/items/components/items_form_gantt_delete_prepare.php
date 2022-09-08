<?php

if (!defined('KERUY_CRM')) {
    exit;
}

if (strstr(\K::$fw->app_redirect_to, 'ganttreport')) {
    /*$check_query = db_query("select id from app_entities where parent_id='" . \K::$fw->current_entity_id . "'");
    $check = db_fetch_array($check_query);*/

    $check = \K::model()->db_fetch_one('app_entities', [
        'parent_id = ?',
        \K::$fw->current_entity_id
    ], [], 'id');

    if (\Models\Main\Users\Users::has_access('delete') and !$check) {
        $extra_button = '<button id="gantt_delete_item_btn" type="button" class="btn btn-default" onclick="gantt_delete()"><i class="fa fa-trash-o"></i></button>';
    }
}