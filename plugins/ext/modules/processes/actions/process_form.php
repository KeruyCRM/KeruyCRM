<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}


switch ($app_module_action) {
    case 'sort_fields':
        //print_r($_POST);
        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . db_input($_GET['process_id']) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (isset($_POST['forms_tabs_' . $tabs['id']])) {
                db_query(
                    "update app_ext_process_form_tabs set fields='" . str_replace(
                        'form_fields_',
                        '',
                        $_POST['forms_tabs_' . $tabs['id']]
                    ) . "' where id=" . $tabs['id']
                );
            }

            //handle rows
            $rows_query = db_query(
                "select * from app_ext_process_form_rows where process_id='" . _GET(
                    'process_id'
                ) . "' and forms_tabs_id='" . $tabs['id'] . "' order by sort_order"
            );
            while ($rows = db_fetch_array($rows_query)) {
                for ($i = 1; $i <= $rows['columns']; $i++) {
                    if (isset($_POST['forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i])) {
                        db_query(
                            "update app_ext_process_form_rows set column{$i}_fields='" . str_replace(
                                'form_fields_',
                                '',
                                $_POST['forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i]
                            ) . "' where id=" . $rows['id']
                        );
                    }
                }
            }
        }
        exit();
        break;
    case 'sort_tabs':
        if (isset($_POST['forms_tabs_ol'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('forms_tabs_', '', $_POST['forms_tabs_ol'])) as $v) {
                db_perform(
                    'app_ext_process_form_tabs',
                    ['sort_order' => $sort_order],
                    'update',
                    "id='" . db_input($v) . "'"
                );
                $sort_order++;
            }
        }
        exit();
        break;
    case 'save_tab':
        $sql_data = [
            'name' => $_POST['name'],
            'process_id' => _GET('process_id'),
            'description' => $_POST['description'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_process_form_tabs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $sql_data['sort_order'] = (process_form::get_tab_max_sort_order(_GET('process_id')) + 1);
            db_perform('app_ext_process_form_tabs', $sql_data);
        }

        redirect_to('ext/processes/process_form', 'process_id=' . _GET('process_id'));
        break;
    case 'delete_tab':
        if (isset($_GET['id'])) {
            db_delete_row('app_ext_process_form_tabs', $_GET['id']);

            //delete rows
            db_delete_row('app_ext_process_form_rows', _GET('id'), 'forms_tabs_id');

            $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, ''), 'success');

            redirect_to('ext/processes/process_form', 'process_id=' . $_GET['process_id']);
        }
        break;
    case 'save_row':

        $sql_data = [
            'process_id' => $_GET['process_id'],
            'forms_tabs_id' => $_GET['forms_tabs_id'],
            'columns' => $_POST['columns'],
            'field_name_new_row' => (isset($_POST['field_name_new_row']) ? 1 : 0),
            'column1_width' => $_POST['column1_width'],
            'column2_width' => $_POST['column2_width'],
            'column3_width' => $_POST['column3_width'],
            'column4_width' => $_POST['column4_width'],
            'column5_width' => $_POST['column5_width'],
            'column6_width' => $_POST['column6_width'],
        ];

        if (isset($_GET['id'])) {
            //reset column fields if columns changed
            for ($i = 1; $i <= 6; $i++) {
                if ($i > $_POST['columns']) {
                    $sql_data['column' . $i . '_fields'] = '';
                }
            }

            db_perform('app_ext_process_form_rows', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $check_query = db_query(
                "select (max(sort_order)+1) as total from app_ext_process_form_rows where process_id='" . _GET(
                    'process_id'
                ) . "' and forms_tabs_id='" . _GET('forms_tabs_id') . "'"
            );
            $check = db_fetch_array($check_query);
            $sort_order = $check['total'];

            $sql_data['sort_order'] = $sort_order;

            db_perform('app_ext_process_form_rows', $sql_data);
        }

        redirect_to('ext/processes/process_form', 'process_id=' . _GET('process_id'));

        break;

    case 'sort_rows':
        //print_rr($_POST);
        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . db_input($_GET['process_id']) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (isset($_POST['forms_rows_' . $tabs['id']])) {
                $sort_order = 0;
                foreach (explode(',', str_replace('forms_rows_', '', $_POST['forms_rows_' . $tabs['id']])) as $v) {
                    db_perform(
                        'app_ext_process_form_rows',
                        ['sort_order' => $sort_order, 'forms_tabs_id' => $tabs['id']],
                        'update',
                        "id='" . db_input($v) . "'"
                    );
                    $sort_order++;
                }
            }
        }
        exit();
        break;

    case 'delete_row':

        if (isset($_GET['id'])) {
            db_delete_row('app_ext_process_form_rows', _GET('id'));
        }

        redirect_to('ext/processes/process_form', 'process_id=' . _GET('process_id'));
        break;
}
