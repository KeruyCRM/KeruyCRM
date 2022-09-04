<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Delete extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        $msg = '';

        if (\K::$fw->current_entity_id == 1 and \K::$fw->GET['id'] == \K::$fw->app_logged_users_id) {
            $msg = \K::$fw->TEXT_ERROR_USER_DELETE;
        }

        if (!\Models\Main\Users\Users::has_access('delete')) {
            $msg = \K::$fw->TEXT_NO_ACCESS;
        }

        if ($msg) {
            echo \Helpers\App::ajax_modal_template(\K::$fw->TEXT_WARNING, $msg);
        } else {
            $item_info = \K::model()->db_find('app_entity_' . (int)\K::$fw->GET['entity_id'], \K::$fw->GET['id']);
            $heading_field_id = \Models\Main\Fields::get_heading_id(\K::$fw->GET['entity_id']);
            $name = ($heading_field_id > 0 ? \Models\Main\Items\Items::get_heading_field_value(
                $heading_field_id,
                $item_info
            ) : $item_info['id']);

            \K::$fw->heading = \K::$fw->TEXT_HEADING_DELETE;
            \K::$fw->content = sprintf(\K::$fw->TEXT_DEFAULT_DELETE_CONFIRMATION, $name);
            \K::$fw->button_title = \K::$fw->TEXT_BUTTON_DELETE;

            if (\Models\Main\Entities::has_subentities(\K::$fw->current_entity_id)) {
                $show_delete_confirm = false;
                //$entities_query = db_query("select id from app_entities where parent_id='" . \K::$fw->current_entity_id . "'");

                $entities_query = \K::model()->db_fetch('app_entities', [
                    'parent_id = ?',
                    \K::$fw->current_entity_id
                ], [], 'id');

                //while ($entities = db_fetch_array($entities_query)) {
                foreach ($entities_query as $entities) {
                    $entities = $entities->cast();

                    //$items_query = db_query("select id from app_entity_" . $entities['id'] . " limit 1");

                    //TODO Review logic
                    $items = \K::model()->db_fetch_one('app_entity_' . (int)$entities['id'], [], [], 'id');

                    if ($items) {
                        $show_delete_confirm = true;
                        break;
                    }
                }

                if ($show_delete_confirm) {
                    \K::$fw->content .= '<div style="margin-top: 15px;" class="alert alert-warning">' . sprintf(
                            \K::$fw->TEXT_WARNING_ITEM_HAS_SUB_ITEM,
                            \K::$fw->app_entities_cache[\K::$fw->current_entity_id]['name']
                        ) . '</div><div class="single-checkbox"><label>' . \Helpers\Html::input_checkbox_tag(
                            'delete_confirm',
                            1,
                            ['class' => 'required']
                        ) . ' ' . \K::$fw->TEXT_CONFIRM_DELETE . '</label></div>';
                }
            }

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'delete.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        }
    }
}