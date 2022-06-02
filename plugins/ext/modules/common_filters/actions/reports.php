<?php

if (!app_session_is_registered('common_filters_filter')) {
    $common_filters_filter = 0;
    app_session_register('common_filters_filter');
}

$app_title = app_set_title(TEXT_HEADING_REPORTS);

switch ($app_module_action) {
    case 'copy':
        $reports_id = _get::int('reports_id');
        reports::copy($reports_id);
        redirect_to('ext/common_filters/reports');
        break;
    case 'set_reports_filter':
        $common_filters_filter = $_POST['reports_filter'];

        redirect_to('ext/common_filters/reports');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'menu_icon' => $_POST['menu_icon'],
            'dashboard_sort_order' => $_POST['sort_order'],
            'in_dashboard_counter' => (isset($_POST['in_dashboard_counter']) ? $_POST['in_dashboard_counter'] : 0),
            'in_dashboard_icon' => 1,
            'in_dashboard_counter_color' => $_POST['in_dashboard_counter_color'],
            'in_dashboard_counter_bg_color' => db_prepare_input($_POST['in_dashboard_counter_bg_color']),
            'in_dashboard_counter_fields' => (isset($_POST['in_dashboard_counter_fields']) ? implode(
                ',',
                $_POST['in_dashboard_counter_fields']
            ) : ''),
            'dashboard_counter_sum_by_field' => $_POST['dashboard_counter_sum_by_field'],
            'dashboard_counter_hide_count' => (isset($_POST['dashboard_counter_hide_count']) ? 1 : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'reports_type' => 'common_filters',
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'in_dashboard' => (isset($_POST['in_dashboard']) ? $_POST['in_dashboard'] : 0),
            'in_header' => (isset($_POST['in_header']) ? $_POST['in_header'] : 0),
            'in_header_autoupdate' => (isset($_POST['in_header_autoupdate']) ? $_POST['in_header_autoupdate'] : 0),
            'displays_assigned_only' => (isset($_POST['displays_assigned_only']) ? $_POST['displays_assigned_only'] : 0),
            'created_by' => $app_logged_users_id,
            'notification_days' => (isset($_POST['notification_days']) ? implode(
                ',',
                $_POST['notification_days']
            ) : ''),
            'notification_time' => (isset($_POST['notification_time']) ? implode(
                ',',
                $_POST['notification_time']
            ) : ''),
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'listing_type' => (isset($_POST['listing_type']) ? $_POST['listing_type'] : ''),
        ];

        if (isset($_GET['id'])) {
            $report_info = db_find('app_reports', $_GET['id']);

            //check reprot entity and if it's changed remove report filters and parent reports
            if ($report_info['entities_id'] != $_POST['entities_id']) {
                db_query("delete from app_reports_filters where reports_id='" . db_input($_GET['id']) . "'");

                //delete paretn reports
                reports::delete_parent_reports($_GET['id']);
                $sql_data['parent_id'] = 0;
            }

            db_perform('app_reports', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_reports', $sql_data);

            $insert_id = db_insert_id();
            //reports::auto_create_parent_reports($insert_id);
        }

        redirect_to('ext/common_filters/reports');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            //delete paretn reports
            reports::delete_parent_reports($_GET['id']);

            db_query("delete from app_reports where id='" . db_input($_GET['id']) . "'");
            db_query("delete from app_reports_filters where reports_id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_WARN_DELETE_REPORT_SUCCESS, 'success');


            redirect_to('ext/common_filters/reports');
        }
        break;
}