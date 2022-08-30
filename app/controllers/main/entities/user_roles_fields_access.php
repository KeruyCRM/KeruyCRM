<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class User_roles_fields_access extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->entity_info = \K::model()->db_find('app_entities', \K::$fw->GET['role_entities_id']);

        \K::$fw->user_roles_info = \K::model()->db_find('app_user_roles', \K::$fw->GET['role_id']);

        \K::$fw->access_choices_default = [
            'yes' => \K::$fw->TEXT_YES,
            'view' => \K::$fw->TEXT_VIEW_ONLY,
            'hide' => \K::$fw->TEXT_HIDE
        ];

        \K::$fw->access_choices_internal = ['yes' => \K::$fw->TEXT_YES, 'hide' => \K::$fw->TEXT_HIDE];

        $fields_list = [];
        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name,if(f.type in (" . \K::model()->quoteToString(
                ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_created_by']
            ) . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in (" . \K::model(
            )->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']
            ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by tab_sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['role_entities_id'],
            'app_fields,app_forms_tabs'
        );

        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            $fields_list[$v['id']] = [
                'name' => \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']),
                'type' => $v['type']
            ];
        }

        \K::$fw->fields_list = $fields_list;

        /*$access_schema_info_query = db_query(
            "select * from app_user_roles_access where user_roles_id='" . _get::int(
                'role_id'
            ) . "' and entities_id='" . \K::$fw->GET['role_entities_id'] . "' and fields_id='" . _get::int(
                'fields_id'
            ) . "'"
        );*/
        $access_schema = [];

        $access_schema_info = \K::model()->db_fetch_one('app_user_roles_access', [
            'user_roles_id = ? and entities_id = ? and fields_id = ?',
            \K::$fw->GET['role_id'],
            \K::$fw->GET['role_entities_id'],
            \K::$fw->GET['fields_id']
        ]);

        if ($access_schema_info and strlen($access_schema_info['fields_access'])) {
            $access_schema = json_decode($access_schema_info['fields_access'], true);
        }

        \K::$fw->access_schema = $access_schema;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_roles_fields_access.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}