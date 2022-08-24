<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');//FIX
        }
    }

    public function index()
    {
        $entity_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        $typeNotIn = ['fieldtype_section'];

        //include fieldtype_parent_item_id only for sub entities
        if ($entity_info['parent_id'] == 0) {
            //$fields_sql_query .= " and f.type not in (" . \K::model()->quote('fieldtype_parent_item_id') . ")";
            $typeNotIn[] = 'fieldtype_parent_item_id';
        }

        $fields_sql_query = " and f.type not in (" . \K::model()->quoteToString($typeNotIn) . ")";

        \K::$fw->fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.listing_status = 1 " . $fields_sql_query . " and f.entities_id = ? and f.forms_tabs_id = t.id order by f.listing_sort_order",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        $typeNotIn[] = 'fieldtype_mapbbcode';
        $fields_sql_query = " and f.type not in (" . \K::model()->quoteToString($typeNotIn) . ")";

        \K::$fw->fields_query2 = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.listing_status = 0 " . $fields_sql_query . " and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);

        //select allowed fields for heading
        $choices = [];
        $choices[''] = '';

        $typeIn = \K::model()->quoteToString([
            'fieldtype_id',
            'fieldtype_date_added',
            'fieldtype_created_by',
            'fieldtype_parent_item_id'
        ]);

        $typeNotIn = \K::model()->quoteToString([
            'fieldtype_action',
            'fieldtype_parent_item_id',
            'fieldtype_mapbbcode',
            'fieldtype_section',
            'fieldtype_input_numeric_comments',
            'fieldtype_input_url',
            'fieldtype_attachments',
            'fieldtype_input_file',
            'fieldtype_image',
            'fieldtype_image_ajax',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_formula',
            'fieldtype_related_records',
            'fieldtype_user_status',
            'fieldtype_user_accessgroups',
            'fieldtype_user_language',
            'fieldtype_user_skin',
            'fieldtype_user_photo'
        ]);

        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name,if(f.type in (" . $typeIn . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in (" . $typeNotIn . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by tab_sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET["entities_id"],
            'app_fields,app_forms_tabs'
        );

        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            $choices[$v['id']] = \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']);
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}