<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_sections_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $obj = \K::model()->db_find('app_listing_sections', \K::$fw->GET['id']);

        if (!isset(\K::$fw->GET['id'])) {
            $obj['sort_order'] = \Models\Main\Listing_types::get_sections_next_order(\K::$fw->GET['listing_types_id']);
        }

        \K::$fw->obj = $obj;

        \K::$fw->listing_types_info = \K::model()->db_find("app_listing_types", \K::$fw->GET['listing_types_id']);

        $entity_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        $typeNotIn = ['fieldtype_section'];

        //include fieldtype_parent_item_id only for sub entities
        if ($entity_info['parent_id'] == 0) {
            $typeNotIn[] = 'fieldtype_parent_item_id';
        }

        $fields_sql_query = \K::model()->quoteToString($typeNotIn);

        $reserved_fields_types = array_merge(
            \Models\Main\Fields_types::get_reserved_data_types(),
            \Models\Main\Fields_types::get_users_types()
        );
        $reserved_fields_types_list = \K::model()->quoteToString($reserved_fields_types);

        $choices = [];
        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name, if(f.type in (" . $reserved_fields_types_list . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in (" . $fields_sql_query . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by tab_sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            $choices[$v['id']] = strip_tags(
                    \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name'])
                ) . ' (#' . $v['id'] . ')';
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_sections_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}