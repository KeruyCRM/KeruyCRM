<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_access extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $typeIn = \K::model()->quoteToString(
            ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_created_by', 'fieldtype_date_updated']
        );

        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name, if(f.type in (" . $typeIn . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in (?) and f.entities_id = ? and f.forms_tabs_id = t.id order by tab_sort_order, t.name, f.sort_order, f.name",
            [
                'fieldtype_action',
                \K::$fw->GET['entities_id']
            ],
            'app_fields,app_forms_tabs'
        );

        $fields_list = [];
        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            $fields_list[$v['id']] = [
                'name' => \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']),
                'type' => $v['type']
            ];
        }

        \K::$fw->fields_list = $fields_list;

        \K::$fw->access_choices_default = [
            'yes' => \K::$fw->TEXT_YES,
            'view' => \K::$fw->TEXT_VIEW_ONLY,
            'hide' => \K::$fw->TEXT_HIDE
        ];

        \K::$fw->access_choices_internal = ['yes' => \K::$fw->TEXT_YES, 'hide' => \K::$fw->TEXT_HIDE];

        \K::$fw->groups_query = \K::model()->db_fetch(
            'app_access_groups', [],
            ['order' => 'sort_order,name'],
            'id,name'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_access.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function set_access()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->POST['ui_accordion_active'])) {
            if (isset(\K::$fw->POST['access'])) {
                \K::model()->begin();

                foreach (\K::$fw->POST['access'] as $access_groups_id => $fields) {
                    foreach ($fields as $id => $access) {
                        if (in_array($access, ['view', 'hide'])) {
                            $sql_data = ['access_schema' => $access];
                            $sql_data['entities_id'] = \K::$fw->GET['entities_id'];
                            $sql_data['access_groups_id'] = $access_groups_id;
                            $sql_data['fields_id'] = $id;

                            \K::model()->db_perform(
                                'app_fields_access',
                                $sql_data,
                                [
                                    'entities_id = ? and access_groups_id = ? and fields_id = ?',
                                    \K::$fw->GET['entities_id'],
                                    $access_groups_id,
                                    $id
                                ]
                            );
                            /*$acess_info_query = db_query(
                                "select access_schema from app_fields_access where entities_id='" . db_input(
                                    $_GET['entities_id']
                                ) . "' and access_groups_id='" . db_input(
                                    $access_groups_id
                                ) . "' and fields_id='" . db_input($id) . "'"
                            );

                            if ($acess_info = db_fetch_array($acess_info_query)) {
                                db_perform(
                                    'app_fields_access',
                                    $sql_data,
                                    'update',
                                    "entities_id='" . db_input(
                                        $_GET['entities_id']
                                    ) . "' and access_groups_id='" . db_input(
                                        $access_groups_id
                                    ) . "'  and fields_id='" . db_input($id) . "'"
                                );
                            } else {
                                $sql_data['entities_id'] = $_GET['entities_id'];
                                $sql_data['access_groups_id'] = $access_groups_id;
                                $sql_data['fields_id'] = $id;
                                db_perform('app_fields_access', $sql_data);
                            }*/
                        } else {
                            /*db_query(
                                "delete from app_fields_access where entities_id='" . db_input(
                                    $_GET['entities_id']
                                ) . "' and access_groups_id='" . db_input(
                                    $access_groups_id
                                ) . "'  and fields_id='" . db_input($id) . "'"
                            );*/

                            \K::model()->db_delete('app_fields_access', [
                                'entities_id = ? and access_groups_id = ? and fields_id = ?',
                                \K::$fw->GET['entities_id'],
                                $access_groups_id,
                                $id
                            ]);
                        }
                    }
                }

                \K::model()->commit();

                \K::flash()->addMessage(\K::$fw->TEXT_ACCESS_UPDATED, 'success');
            }

            \Helpers\Urls::redirect_to(
                'main/entities/fields_access',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&ui_accordion_active=' . \K::$fw->POST['ui_accordion_active']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}