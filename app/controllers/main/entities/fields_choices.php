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
        $field_info = db_find('app_fields', $_GET['fields_id']);

        $cfg = new fields_types_cfg($field_info['configuration']);

        if ($cfg->get('use_global_list') > 0) {
            redirect_to('global_lists/choices', 'lists_id=' . $cfg->get('use_global_list'));
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_choices.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        $sql_data = [
            'fields_id' => $_POST['fields_id'],
            'parent_id' => $_POST['parent_id'] ?? 0,
            'name' => $_POST['name'],
            'users' => (isset($_POST['users']) ? implode(',', $_POST['users']) : ''),
            'is_default' => (isset($_POST['is_default']) ? $_POST['is_default'] : 0),
            'is_active' => (isset($_POST['is_active']) ? $_POST['is_active'] : 0),
            'bg_color' => $_POST['bg_color'],
            'sort_order' => $_POST['sort_order'],
            'value' => (isset($_POST['value']) ? str_replace(',', '.', $_POST['value']) : ''),
        ];

        if (isset($_POST['is_default'])) {
            db_query(
                "update app_fields_choices set is_default = 0 where fields_id = '" . db_input($_POST['fields_id']) . "'"
            );
        }

        if (isset($_GET['id'])) {
            //paretn can't be the same as record id
            if ($_POST['parent_id'] == $_GET['id']) {
                $sql_data['parent_id'] = 0;
            }

            db_perform('app_fields_choices', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            $choices_id = $_GET['id'];
        } else {
            db_perform('app_fields_choices', $sql_data);
            $choices_id = db_insert_id();
        }

        //upload and prepare image map filename
        fieldtype_image_map::upload_map_filename($choices_id);

        redirect_to(
            'entities/fields_choices',
            'entities_id=' . $_POST['entities_id'] . '&fields_id=' . $_POST['fields_id']
        );
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $msg = fields_choices::check_before_delete($_GET['id']);

            if (strlen($msg) > 0) {
                $alerts->add($msg, 'error');
            } else {
                $name = fields_choices::get_name_by_id($_GET['id']);

                $tree = fields_choices::get_tree($_GET['fields_id'], $_GET['id']);

                foreach ($tree as $v) {
                    db_delete_row('app_fields_choices', $v['id']);
                }

                db_delete_row('app_fields_choices', $_GET['id']);

                //delete choices filters
                $reports_info_query = db_query(
                    "select * from app_reports where reports_type='fields_choices" . $_GET['id'] . "'"
                );
                if ($reports_info = db_fetch_array($reports_info_query)) {
                    db_query(
                        "delete from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'"
                    );
                    db_query("delete from app_reports where id='" . db_input($reports_info['id']) . "'");
                }

                //delete map images
                fieldtype_image_map::delete_map_files($_GET['id']);

                $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            redirect_to(
                'entities/fields_choices',
                'entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
            );
        }
    }

    public function sort_reset()
    {
        db_query(
            "update app_fields_choices set sort_order = 0 where fields_id = '" . db_input($_GET['fields_id']) . "'"
        );

        redirect_to(
            'entities/fields_choices',
            'entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
        );
    }

    public function sort()
    {
        $choices_sorted = $_POST['choices_sorted'];
        $parent_id = $_POST['parent_id'] ?? 0;

        if (strlen($choices_sorted) > 0) {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);

            fields_choices::sort_tree($_GET['fields_id'], $choices_sorted, $parent_id);
        }

        redirect_to(
            'entities/fields_choices',
            'entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']
        );
    }
}