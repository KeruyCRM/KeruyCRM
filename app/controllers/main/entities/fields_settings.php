<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_settings extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        //\K::$fw->fields_info = db_query("select * from app_fields where id='" . db_input(\K::$fw->GET['fields_id']) . "'");

        \K::$fw->fields_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['fields_id']
        ], [], 'id,type,name,configuration');

        if (!\K::$fw->fields_info) {
            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        }
    }

    public function index()
    {
        //default field configuration
        \K::$fw->cfg = \Models\Main\Fields_types::parse_configuration(\K::$fw->fields_info['configuration']);

        \K::$fw->exclude_cfg_keys = [];

        \K::$fw->fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where (is_heading = 0 or is_heading is null) and f.type not in (" . \K::model(
            )->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']
            ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->cfg['entity_id'],
            'app_fields,app_forms_tabs'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_settings.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $fields_configuration = \K::$fw->POST['fields_configuration'];

            switch (\K::$fw->fields_info['type']) {
                case 'fieldtype_related_records':
                    if (isset(\K::$fw->POST['fields_in_listing'])) {
                        $fields_configuration['fields_in_listing'] = implode(',', \K::$fw->POST['fields_in_listing']);
                    } else {
                        $fields_configuration['fields_in_listing'] = '';
                    }

                    if (isset(\K::$fw->POST['fields_in_popup'])) {
                        $fields_configuration['fields_in_popup'] = implode(',', \K::$fw->POST['fields_in_popup']);
                    } else {
                        $fields_configuration['fields_in_popup'] = '';
                    }

                    $fields_configuration['create_related_comment'] = \K::$fw->POST['create_related_comment'];
                    $fields_configuration['create_related_comment_text'] = \K::$fw->POST['create_related_comment_text'];
                    $fields_configuration['delete_related_comment'] = \K::$fw->POST['delete_related_comment'];
                    $fields_configuration['delete_related_comment_text'] = \K::$fw->POST['delete_related_comment_text'];
                    $fields_configuration['create_related_comment_to'] = \K::$fw->POST['create_related_comment_to'];
                    $fields_configuration['create_related_comment_to_text'] = \K::$fw->POST['create_related_comment_to_text'];
                    $fields_configuration['delete_related_comment_to'] = \K::$fw->POST['delete_related_comment_to'];
                    $fields_configuration['delete_related_comment_to_text'] = \K::$fw->POST['delete_related_comment_to_text'];
                    break;

                case 'fieldtype_entity':

                    if (isset(\K::$fw->POST['fields_in_popup'])) {
                        $fields_configuration['fields_in_popup'] = implode(',', \K::$fw->POST['fields_in_popup']);
                    } else {
                        $fields_configuration['fields_in_popup'] = '';
                    }
                    break;
            }

            /*db_query(
                "update app_fields set configuration='" . db_input(
                    fields_types::prepare_configuration($fields_configuration)
                ) . "' where id='" . db_input(\K::$fw->fields_info['id']) . "'"
            );*/

            $sql_data = ['configuration' => \Models\Main\Fields_types::prepare_configuration($fields_configuration)];

            \K::model()->db_update('app_fields', $sql_data, [
                'id = ?',
                \K::$fw->fields_info['id']
            ]);

            \K::flash()->addMessage(\K::$fw->TEXT_CONFIGURATION_UPDATED, 'success');

            \Helpers\Urls::redirect_to('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}