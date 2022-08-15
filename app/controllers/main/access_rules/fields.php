<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Fields extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();
    }

    public function index()
    {
        \K::$fw->form_fields_query = \K::model()->db_query_exec(
            'select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_access_rules_fields r, app_fields f where r.fields_id = f.id and r.entities_id = ?',
            \K::$fw->GET['entities_id'],
            'app_access_rules_fields,app_fields'
        );

        \K::$fw->entities_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);

        if (\K::$fw->entities_info['parent_id'] != 0) {
            \K::$fw->parent_entities_info = \K::model()->db_find('app_entities', \K::$fw->entities_info['parent_id']);

            /*$reports_info_query = db_query(
                "select * from app_reports where entities_id='" . db_input(
                    \K::$fw->parent_entities_info['id']
                ) . "' and reports_type='hide_add_button_rules" . \K::$fw->GET['entities_id'] . "'"
            );*/

            \K::$fw->reports_info = \K::model()->db_fetch_one('app_reports', [
                'entities_id = ? and reports_type = ?',
                \K::$fw->parent_entities_info['id'],
                'hide_add_button_rules' . \K::$fw->GET['entities_id']
            ]);

            if (!\K::$fw->reports_info) {
                $sql_data = [
                    'name' => '',
                    'entities_id' => \K::$fw->parent_entities_info['id'],
                    'reports_type' => 'hide_add_button_rules' . \K::$fw->GET['entities_id'],
                    'in_menu' => 0,
                    'in_dashboard' => 0,
                    'created_by' => 0,
                ];

                $mapper = \K::model()->db_perform('app_reports', $sql_data);
                $reports_id = \K::model()->db_insert_id($mapper);

                \K::$fw->reports_info = \K::model()->db_find('app_reports', $reports_id);
            }

            \K::$fw->db_count = \K::model()->db_count('app_reports_filters', \K::$fw->reports_info['id'], 'reports_id');

            \K::$fw->filters_query = \K::model()->db_query(
                'select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id',
                \K::$fw->reports_info['id'],
                'app_reports_filters,app_fields'
            );
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'entities_id' => \K::$fw->GET['entities_id'],
                'fields_id' => \K::$fw->POST['fields_id'],
            ];

            if (isset(\K::$fw->GET['id'])) {
                $access_rules_fields_info = \K::model()->db_find('app_access_rules_fields', \K::$fw->GET['id']);

                \K::model()->begin();

                if ($access_rules_fields_info['fields_id'] != \K::$fw->POST['fields_id']) {
                    \K::model()->db_delete_row('app_access_rules', \K::$fw->GET['entities_id'], 'entities_id');
                }

                \K::model()->db_update('app_access_rules_fields', $sql_data, ['id = ?', \K::$fw->GET['id']]);

                \K::model()->commit();
            } else {
                \K::model()->db_perform('app_access_rules_fields', $sql_data);
            }

            \Helpers\Urls::redirect_to('main/access_rules/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            if (isset(\K::$fw->GET['id'])) {
                \K::model()->begin();

                \K::model()->db_delete_row('app_access_rules_fields', \K::$fw->GET['id']);
                \K::model()->db_delete_row('app_access_rules', \K::$fw->GET['entities_id'], 'entities_id');

                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to('main/access_rules/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}