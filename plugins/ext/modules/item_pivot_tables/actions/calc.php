<?php

$reports_query = db_query("select * from app_ext_item_pivot_tables where id='" . _get::int('reports_id') . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('ext/item_pivot_tables/reports');
}


switch ($app_module_action) {
    case 'save':

        if ($_POST['type'] == 'calc') {
            $sql_data = [
                'reports_id' => _get::int('reports_id'),
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'select_query' => $_POST['select_query'],
                'where_query' => (count($_POST['where_query']) ? json_encode($_POST['where_query']) : ''),
            ];
        } else {
            $sql_data = [
                'reports_id' => _get::int('reports_id'),
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'formula' => $_POST['formula'],
                'sort_order' => $_POST['sort_order'],
            ];
        }

        if (isset($_GET['id'])) {
            db_perform('app_ext_item_pivot_tables_calcs', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_item_pivot_tables_calcs', $sql_data);
        }

        redirect_to('ext/item_pivot_tables/calc', 'reports_id=' . _get::int('reports_id'));

        break;

    case 'delete':

        $obj = db_find('app_ext_item_pivot_tables_calcs', $_GET['id']);

        db_delete_row('app_ext_item_pivot_tables_calcs', $_GET['id']);

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/item_pivot_tables/calc', 'reports_id=' . _get::int('reports_id'));
        break;
}