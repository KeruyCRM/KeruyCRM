<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Rules_form extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();

        if (isset(\K::$fw->GET['id'])) {
            $obj = \K::model()->db_find('app_access_rules', \K::$fw->GET['id']);
        } else {
            //TODO check double use code in db_find and db_show_columns
            $obj = \K::model()->db_show_columns('app_access_rules');
        }

        \K::$fw->obj = $obj;
    }

    public function index()
    {
        $fields_id = \K::$fw->GET['fields_id'];

        $field_info = \K::model()->db_find('app_fields', $fields_id);

        $cfg = new \Models\Main\Fields_types_cfg($field_info['configuration']);

        $choices = [];
        $tree = ($cfg->get('use_global_list') > 0 ? \Models\Main\Global_lists::get_choices_tree(
            $cfg->get('use_global_list')
        ) : \Models\Main\Fields_choices::get_tree($fields_id));
        foreach ($tree as $v) {
            $choices[$v['id']] = $v['name'];
        }

        \K::$fw->choices = $choices;

        $users_groups = [];
        $groups_query = \K::model()->db_query_exec(
            'select ag.id, ag.name from app_access_groups ag where ag.id in (select ea.access_groups_id from app_entities_access ea where ea.entities_id = ? and length(ea.access_schema) > 0) order by ag.sort_order, ag.name',
            \K::$fw->GET['entities_id'],
            'app_access_groups,app_entities_access'
        );

        //while ($v = db_fetch_array($groups_query)) {
        foreach ($groups_query as $v) {
            $users_groups[$v['id']] = $v['name'];
        }

        \K::$fw->users_groups = $users_groups;

        $fields_view_only_access = [];

        //TODO Really use 2 table?
        $fields_query = \K::model()->db_query_exec(
            'select f.id, f.name, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (' . \Models\Main\Fields_types::get_reserverd_types_list(
            ) . ') and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name',
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields_view_only_access[$fields['id']] = $fields['name'];
        }

        \K::$fw->fields_view_only_access = $fields_view_only_access;

        $access_schema = [
            'update' => \K::$fw->TEXT_UPDATE_ACCESS,
            'delete' => \K::$fw->TEXT_DELETE_ACCESS,
            'export' => \K::$fw->TEXT_EXPORT_ACCESS,
        ];

        //extra access available in extension
        if (\Helpers\App::is_ext_installed()) {
            $access_schema += [
                'copy' => \K::$fw->TEXT_COPY_RECORDS,
                'move' => \K::$fw->TEXT_MOVE_RECORDS,
            ];
        }

        \K::$fw->access_schema = $access_schema;

        $comments_access_schema = [
            'false' => '',
            'no' => \K::$fw->TEXT_NO,
            'view_create_update_delete' => \K::$fw->TEXT_YES,
            'view_create' => \K::$fw->TEXT_CREATE_ONLY_ACCESS,
            'view' => \K::$fw->TEXT_VIEW_ONLY_ACCESS
        ];

        \K::$fw->comments_access_schema = $comments_access_schema;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'rules_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}