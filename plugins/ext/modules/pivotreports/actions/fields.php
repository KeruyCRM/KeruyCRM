<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

//check if report exist
$reports_query = db_query("select * from app_ext_pivotreports where id='" . db_input($_GET['id']) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'save':
        if (isset($_POST['fields'])) {
            db_query("delete from app_ext_pivotreports_fields where pivotreports_id='" . db_input($_GET['id']) . "'");

            foreach ($_POST['fields'] as $entities_id => $entities_fields) {
                foreach ($entities_fields as $fields_id) {
                    $sql_data = [
                        'pivotreports_id' => $_GET['id'],
                        'entities_id' => $entities_id,
                        'fields_id' => $fields_id,
                        'fields_name' => $_POST['fields_name'][$entities_id][$fields_id],
                        'cfg_date_format' => (isset($_POST['fields_date_format'][$entities_id][$fields_id]) ? $_POST['fields_date_format'][$entities_id][$fields_id] : ''),
                    ];

                    db_perform('app_ext_pivotreports_fields', $sql_data);
                }
            }
        } else {
            db_query("delete from app_ext_pivotreports_fields where pivotreports_id='" . db_input($_GET['id']) . "'");
        }

        $alerts->add(TEXT_DATA_SAVED);

        redirect_to('ext/pivotreports/fields', 'id=' . $_GET['id']);

        break;
}
