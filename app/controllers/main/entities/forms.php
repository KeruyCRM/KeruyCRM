<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save_javascript()
    {
        $cfg = new entities_cfg($_GET['entities_id']);
        $cfg->set('javascript_in_from', $_POST['javascript_in_from']);
        $cfg->set('javascript_onsubmit', $_POST['javascript_onsubmit']);

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('entities/forms', 'entities_id=' . $_GET['entities_id']);
    }

    public function sort_fields()
    {
        $tabs_query = db_fetch_all(
            'app_forms_tabs',
            "entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (isset($_POST['forms_tabs_' . $tabs['id']])) {
                $sort_order = 0;
                foreach (explode(',', $_POST['forms_tabs_' . $tabs['id']]) as $v) {
                    db_perform(
                        'app_fields',
                        ['forms_tabs_id' => $tabs['id'], 'sort_order' => $sort_order, 'forms_rows_position' => ''],
                        'update',
                        "id='" . db_input(str_replace('form_fields_', '', $v)) . "'"
                    );

                    $sort_order++;
                }
            }

            //handle rows
            $rows_query = db_query(
                "select * from app_forms_rows where entities_id='" . _GET(
                    'entities_id'
                ) . "' and forms_tabs_id='" . $tabs['id'] . "' order by sort_order"
            );
            while ($rows = db_fetch_array($rows_query)) {
                for ($i = 1; $i <= $rows['columns']; $i++) {
                    if (isset($_POST['forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i])) {
                        $sort_order = 0;
                        foreach (
                            explode(
                                ',',
                                $_POST['forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i]
                            ) as $v
                        ) {
                            db_perform(
                                'app_fields',
                                [
                                    'forms_tabs_id' => $tabs['id'],
                                    'sort_order' => $sort_order,
                                    'forms_rows_position' => $rows['id'] . ':' . $i
                                ],
                                'update',
                                "id='" . db_input(str_replace('form_fields_', '', $v)) . "'"
                            );

                            $sort_order++;
                        }
                    }
                }
            }
        }
    }

    public function sort_tabs()
    {
        if (isset($_POST['forms_tabs_ol'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('forms_tabs_', '', $_POST['forms_tabs_ol'])) as $v) {
                db_perform('app_forms_tabs', ['sort_order' => $sort_order], 'update', "id='" . db_input($v) . "'");
                $sort_order++;
            }
        }
    }

    public function save_tab()
    {
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'description' => $_POST['description'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_forms_tabs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $sql_data['sort_order'] = (forms_tabs::get_last_sort_number($_POST['entities_id']) + 1);
            db_perform('app_forms_tabs', $sql_data);
        }

        redirect_to('entities/forms', 'entities_id=' . $_POST['entities_id']);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $msg = forms_tabs::check_before_delete($_GET['id']);

            if (strlen($msg) > 0) {
                $alerts->add($msg, 'error');
            } else {
                $name = forms_tabs::get_name_by_id($_GET['id']);

                db_delete_row('app_forms_tabs', $_GET['id']);

                //delete rows
                db_delete_row('app_forms_rows', _GET('id'), 'forms_tabs_id');

                $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            redirect_to('entities/forms', 'entities_id=' . $_GET['entities_id']);
        }
    }
}