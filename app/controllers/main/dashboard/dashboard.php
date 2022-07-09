<?php

namespace Controllers\Main\Dashboard;

class Dashboard extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        $app_title = app_set_title(TEXT_MENU_DASHBOARD);

        if (!strlen($app_module_action)) {
            //autoreset session table if default _sess_gc function not working
            app_session_table_reset();
        }
    }

    public function save()
    {
        if (isset($_POST['hidden_common_reports'])) {
            $app_users_cfg->set('hidden_common_reports', implode(',', $_POST['hidden_common_reports']));
        } else {
            $app_users_cfg->set('hidden_common_reports', '');
        }

        redirect_to('dashboard/');
    }

    public function keep_session()
    {
    }

    public function sort_reports()
    {
        if (isset($_POST['reports_on_dashboard'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['reports_on_dashboard']) as $v) {
                $sql_data = ['in_dashboard' => 1, 'dashboard_sort_order' => $sort_order];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
                $sort_order++;
            }
        }

        if (isset($_POST['reports_excluded_from_dashboard'])) {
            foreach (explode(',', $_POST['reports_excluded_from_dashboard']) as $v) {
                $sql_data = ['in_dashboard' => 0, 'dashboard_sort_order' => 0];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
            }
        }
    }

    public function sort_reports_countr()
    {
        if (isset($_POST['reports_counter_on_dashboard'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['reports_counter_on_dashboard']) as $v) {
                $sql_data = ['in_dashboard_counter' => 1, 'dashboard_counter_sort_order' => $sort_order];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
                $sort_order++;
            }
        }

        if (isset($_POST['reports_counter_excluded_from_dashboard'])) {
            foreach (explode(',', $_POST['reports_counter_excluded_from_dashboard']) as $v) {
                $sql_data = ['in_dashboard_counter' => 0, 'dashboard_counter_sort_order' => 0];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
            }
        }
    }

    public function sort_reports_header()
    {
        if (isset($_POST['reports_in_header'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['reports_in_header']) as $v) {
                $sql_data = ['in_header' => 1, 'header_sort_order' => $sort_order];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
                $sort_order++;
            }
        }

        if (isset($_POST['reports_excluded_in_header'])) {
            foreach (explode(',', $_POST['reports_excluded_in_header']) as $v) {
                $sql_data = ['in_header' => 0, 'header_sort_order' => 0];
                db_perform(
                    'app_reports',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('report_', '', $v)) . "' and created_by='" . db_input(
                        $app_user['id']
                    ) . "'"
                );
            }
        }
    }

    public function update_hot_reports()
    {
        $reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            exit();
        }

        //check report access
        if ($reports_info['reports_type'] == 'common') {
            //check access for common report
            $check_query = db_query(
                "select r.* from app_reports r, app_entities e, app_entities_access ea  where r.id = '" . $reports_info['id'] . "' and  r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input(
                    $app_user['group_id']
                ) . "' and (find_in_set(" . $app_user['group_id'] . ",r.users_groups) or find_in_set(" . $app_user['id'] . ",r.assigned_to)) and r.reports_type = 'common' order by r.dashboard_sort_order, r.name"
            );
            if (!$check = db_fetch_array($check_query)) {
                exit();
            }
        } elseif ($app_logged_users_id != $reports_info['created_by']) {
            exit();
        }

        $hot_reports = new hot_reports();
        echo $hot_reports->render_dropdown($_GET['reports_id']);

        db_dev_log();
    }

    public function update_favorites_header_dropdown()
    {
        echo favorites::render_header_dropdown();
    }

    public function update_user_notifications_report()
    {
        echo users_notifications::render_dropdown();
    }

    public function set_users_alers_viewed()
    {
        $sql_data = [
            'users_id' => $app_user['id'],
            'alerts_id' => _post::int('id'),
        ];

        db_perform('app_users_alerts_viewed', $sql_data);
    }

    public function set_filter_status()
    {
        db_query("update app_reports_filters set is_active=" . _POST('is_active') . " where id=" . _POST('filter_id'));

        app_exit();
    }
}