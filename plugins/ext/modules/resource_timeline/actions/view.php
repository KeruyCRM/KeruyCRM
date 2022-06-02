<?php

//check if report exist
$reports_query = db_query("select * from app_ext_resource_timeline where id='" . db_input(_GET('id')) . "'");
if (!$reports = db_fetch_array($reports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!resource_timeline::has_access($reports['users_groups'])) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'get_resources':
        $resource_timeline = new resource_timeline($reports);
        echo $resource_timeline->get_resources();
        app_exit();
        break;
    case 'get_events':
        $resource_timeline = new resource_timeline($reports);
        echo $resource_timeline->get_events();
        app_exit();
        break;
    case 'resize':

        if (strstr($_POST['end'], 'T')) {
            $end = get_date_timestamp($_POST['end']);
        } else {
            $end = strtotime('-1 day', get_date_timestamp($_POST['end']));
        }

        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_resource_timeline_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' and ce.id='" . _POST(
                'reports_entities_id'
            ) . "'"
        );
        if ($reports_entities = db_fetch_array($reports_entities_query)) {
            $sql_data = ['field_' . $reports_entities['end_date'] => $end];

            db_perform(
                "app_entity_" . $reports_entities['entities_id'],
                $sql_data,
                'update',
                "id='" . db_input(_POST('id')) . "'"
            );
        }

        app_exit();
        break;

    case 'drop':

        if (isset($_POST['end'])) {
            if (strstr($_POST['end'], 'T')) {
                $end = get_date_timestamp($_POST['end']);
            } else {
                $end = strtotime('-1 day', get_date_timestamp($_POST['end']));
            }
        } else {
            $end = get_date_timestamp($_POST['start']);
        }


        $reports_entities_query = db_query(
            "select ce.*, e.name from app_ext_resource_timeline_entities ce, app_entities e where e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' and ce.id='" . _POST(
                'reports_entities_id'
            ) . "'"
        );
        if ($reports_entities = db_fetch_array($reports_entities_query)) {
            $sql_data = [
                'field_' . $reports_entities['start_date'] => get_date_timestamp($_POST['start']),
                'field_' . $reports_entities['end_date'] => $end
            ];

            db_perform(
                "app_entity_" . $reports_entities['entities_id'],
                $sql_data,
                'update',
                "id='" . _POST('id') . "'"
            );
        }

        app_exit();
        break;
}