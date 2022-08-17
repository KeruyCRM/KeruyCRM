<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_choices extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->field_info = \K::model()->db_find('app_fields', \K::$fw->GET['fields_id']);

        $cfg = new \Models\Main\Fields_types_cfg(\K::$fw->field_info['configuration']);

        if ($cfg->get('use_global_list') > 0) {
            \Helpers\Urls::redirect_to('main/global_lists/choices', 'lists_id=' . $cfg->get('use_global_list'));
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'fields_id' => \K::$fw->POST['fields_id'],
                'parent_id' => \K::$fw->POST['parent_id'] ?? 0,
                'name' => \K::$fw->POST['name'],
                'users' => (isset(\K::$fw->POST['users']) ? implode(',', \K::$fw->POST['users']) : ''),
                'is_default' => (\K::$fw->POST['is_default'] ?? 0),
                'is_active' => (\K::$fw->POST['is_active'] ?? 0),
                'bg_color' => \K::$fw->POST['bg_color'],
                'sort_order' => \K::$fw->POST['sort_order'],
                'value' => (isset(\K::$fw->POST['value']) ? str_replace(',', '.', \K::$fw->POST['value']) : ''),
            ];

            \K::model()->begin();

            if (isset(\K::$fw->POST['is_default'])) {
                /*db_query(
                    "update app_fields_choices set is_default = 0 where fields_id = '" . db_input(\K::$fw->POST['fields_id']) . "'"
                );*/

                \K::model()->db_update(
                    'app_fields_choices',
                    ['is_default' => 0],
                    ['fields_id = ?', \K::$fw->POST['fields_id']]
                );
            }

            if (isset(\K::$fw->GET['id'])) {
                //parent can't be the same as record id
                if (\K::$fw->POST['parent_id'] == \K::$fw->GET['id']) {
                    $sql_data['parent_id'] = 0;
                }

                //db_perform('app_fields_choices', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_update('app_fields_choices', $sql_data, ['id = ?', \K::$fw->GET['id']]);

                $choices_id = \K::$fw->GET['id'];
            } else {
                $mapper = \K::model()->db_perform('app_fields_choices', $sql_data);
                $choices_id = \K::model()->db_insert_id($mapper);
            }

            //upload and prepare image map filename
            \Tools\FieldsTypes\Fieldtype_image_map::upload_map_filename($choices_id);

            \K::model()->commit();

            \Helpers\Urls::redirect_to(
                'main/entities/fields_choices',
                'entities_id=' . \K::$fw->POST['entities_id'] . '&fields_id=' . \K::$fw->POST['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id']) and isset(\K::$fw->GET['fields_id'])) {
            if (isset(\K::$fw->GET['id'])) {
                //TODO Refactoring this absurd
                $msg = \Models\Main\Fields_choices::check_before_delete(\K::$fw->GET['id']);

                if (strlen($msg) > 0) {
                    \K::flash()->addMessage($msg, 'error');
                } else {
                    $name = \Models\Main\Fields_choices::get_name_by_id(\K::$fw->GET['id']);

                    $tree = \Models\Main\Fields_choices::get_tree(\K::$fw->GET['fields_id'], \K::$fw->GET['id']);

                    \K::model()->begin();

                    foreach ($tree as $v) {
                        \K::model()->db_delete_row('app_fields_choices', $v['id']);
                    }

                    \K::model()->db_delete_row('app_fields_choices', \K::$fw->GET['id']);

                    //delete choices filters
                    /*$reports_info_query = db_query(
                        "select * from app_reports where reports_type='fields_choices" . \K::$fw->GET['id'] . "'"
                    );*/

                    $reports_info = \K::model()->db_fetch_one('app_reports', [
                        'reports_type = ?',
                        'fields_choices' . (int)\K::$fw->GET['id']
                    ]);

                    if ($reports_info) {
                        /*db_query(
                            "delete from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'"
                        );
                        db_query("delete from app_reports where id='" . db_input($reports_info['id']) . "'");*/

                        \K::model()->db_delete_row('app_reports_filters', $reports_info['id'], 'reports_id');
                        \K::model()->db_delete_row('app_reports', $reports_info['id']);
                    }

                    //delete map images
                    \Tools\FieldsTypes\Fieldtype_image_map::delete_map_files(\K::$fw->GET['id']);

                    \K::model()->commit();

                    \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');
                }

                \Helpers\Urls::redirect_to(
                    'main/entities/fields_choices',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_reset()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['fields_id']) and isset(\K::$fw->GET['entities_id'])) {
            /*db_query(
                 "update app_fields_choices set sort_order = 0 where fields_id = '" . db_input(
                     \K::$fw->GET['fields_id']
                 ) . "'"
             );*/

            \K::model()->db_update('app_fields_choices', ['sort_order' => 0], [
                'fields_id = ?',
                \K::$fw->GET['fields_id']
            ]);

            \Helpers\Urls::redirect_to(
                'main/entities/fields_choices',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['fields_id']) and isset(\K::$fw->GET['entities_id'])) {
            $choices_sorted = \K::$fw->POST['choices_sorted'];
            $parent_id = \K::$fw->POST['parent_id'] ?? 0;

            if (strlen($choices_sorted) > 0) {
                $choices_sorted = json_decode(stripslashes($choices_sorted), true);

                \Models\Main\Fields_choices::sort_tree(\K::$fw->GET['fields_id'], $choices_sorted, $parent_id);
            }

            \Helpers\Urls::redirect_to(
                'main/entities/fields_choices',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}