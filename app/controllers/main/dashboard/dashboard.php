<?php

namespace Controllers\Main\Dashboard;

class Dashboard extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \K::$fw->app_title = \Helpers\App::app_set_title(\K::$fw->TEXT_MENU_DASHBOARD);
        /*if (!strlen(\K::$fw->app_module_action)) {
            //autoreset session table if default _sess_gc function not working
            app_session_table_reset();
        }*/
    }

    public function index()
    {
        \Helpers\App::app_reset_selected_items();
        \K::$fw->render_dashboard_tabs = \Models\Main\Reports\Reports_groups::render_dashboard_tabs();

        $page = new \Tools\Pages\Dashboard_pages();
        \K::$fw->render_info_blocks = $page->render_info_blocks();
        \K::$fw->render_info_pages = $page->render_info_pages();

        \K::$fw->has_reports_on_dashboard = $page->has_pages;

        $reports_counter = new \Models\Main\Reports\Reports_counter();
        $html = $reports_counter->render();

        if (strlen($html)) {
            \K::$fw->html = $html;

            \K::$fw->has_reports_on_dashboard = true;
        } else {
            \K::$fw->html = '';
        }

        /*$reports_query = db_query(
            "select * from app_reports where created_by='" . db_input(
                \K::$fw->app_logged_users_id
            ) . "' and in_dashboard=1 and reports_type in ('standard') order by dashboard_sort_order, name"
        );*/

        $reports_query = \K::model()->db_fetch('app_reports', [
            "created_by = ? and in_dashboard = 1 and reports_type in ('standard')",
            \K::$fw->app_logged_users_id
        ], ['order' => 'dashboard_sort_order,name'], 'id,name');

        $reports = [];
        foreach ($reports_query as $report) {
            /*$check_query = db_query(
                "select id from app_reports_sections where (report_left='standard{$reports['id']}' or report_right='standard{$reports['id']}') and reports_groups_id=0 and created_by='" . \K::$fw->app_user['id'] . "'"
            );*/

            $check = \K::model()->db_fetch_count('app_reports_sections', [
                "(report_left = 'standard{$report->id}' or report_right = 'standard{$report->id}') and reports_groups_id = 0 and created_by = ?",
                \K::$fw->app_user['id']
            ]);

            $value = [
                'check' => $check,
                'id' => $report->id,
                'name' => $report->name
            ];

            $reports[] = $value;
        }

        \K::$fw->reports = $reports;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'dashboard.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.hidden_common_reports', $hidden_common_reports)) {
                \K::app_users_cfg()->set('hidden_common_reports', implode(',', $hidden_common_reports));
            } else {
                \K::app_users_cfg()->set('hidden_common_reports', '');
            }
        }

        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function keep_session()
    {
        echo \K::security()->getAppSessionToken();
    }

    public function sort_reports()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.reports_on_dashboard', $reports_on_dashboard)) {
                $sort_order = 0;
                foreach (explode(',', $reports_on_dashboard) as $v) {
                    $sql_data = ['in_dashboard' => 1, 'dashboard_sort_order' => $sort_order];

                    $this->_update_reports($sql_data, $v);

                    $sort_order++;
                }
            }

            if (\K::fw()->exists('POST.reports_excluded_from_dashboard', $reports_excluded_from_dashboard)) {
                foreach (explode(',', $reports_excluded_from_dashboard) as $v) {
                    $sql_data = ['in_dashboard' => 0, 'dashboard_sort_order' => 0];

                    $this->_update_reports($sql_data, $v);
                }
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_reports_counter()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.reports_counter_on_dashboard', $reports_counter_on_dashboard)) {
                $sort_order = 0;
                $exp = explode(',', $reports_counter_on_dashboard);

                if (count($exp)) {
                    \K::model()->begin();
                }

                foreach ($exp as $v) {
                    $sql_data = ['in_dashboard_counter' => 1, 'dashboard_counter_sort_order' => $sort_order];

                    $this->_update_reports($sql_data, $v);

                    $sort_order++;
                }
            }

            if (\K::fw()->exists(
                'POST.reports_counter_excluded_from_dashboard',
                $reports_counter_excluded_from_dashboard
            )) {
                $exp = explode(',', $reports_counter_excluded_from_dashboard);

                if (!\K::model()->trans() and count($exp)) {
                    \K::model()->begin();
                }

                foreach ($exp as $v) {
                    $sql_data = ['in_dashboard_counter' => 0, 'dashboard_counter_sort_order' => 0];

                    $this->_update_reports($sql_data, $v);
                }
            }

            if (\K::model()->trans()) {
                \K::model()->commit();
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_reports_header()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.reports_in_header', $reports_in_header)) {
                $sort_order = 0;
                $exp = explode(',', $reports_in_header);

                if (count($exp)) {
                    \K::model()->begin();
                }

                foreach ($exp as $v) {
                    $sql_data = ['in_header' => 1, 'header_sort_order' => $sort_order];

                    $this->_update_reports($sql_data, $v);

                    $sort_order++;
                }
            }

            if (\K::fw()->exists('POST.reports_excluded_in_header', $reports_excluded_in_header)) {
                $exp = explode(',', $reports_excluded_in_header);

                if (!\K::model()->trans() and count($exp)) {
                    \K::model()->begin();
                }

                foreach ($exp as $v) {
                    $sql_data = ['in_header' => 0, 'header_sort_order' => 0];

                    $this->_update_reports($sql_data, $v);
                }
            }

            if (\K::model()->trans()) {
                \K::model()->commit();
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_hot_reports()
    {
        if (\K::fw()->exists('GET.reports_id', $reports_id)) {
            $reports_info = \K::model()->db_fetch_one('app_reports', [
                'id = ?',
                $reports_id
            ]);

            if (!$reports_info) {
                return;
            }

            //check report access
            if ($reports_info['reports_type'] == 'common') {
                //check access for common report
                $check_query = \K::model()->db_query_exec(
                    "select r.* from app_reports r, app_entities e, app_entities_access ea where r.id = ? and  r.entities_id = e.id and e.id = ea.entities_id and length(ea.access_schema) > 0 and ea.access_groups_id = ? and (find_in_set( ? ,r.users_groups) or find_in_set( ? ,r.assigned_to)) and r.reports_type = 'common' order by r.dashboard_sort_order, r.name"
                    ,
                    [
                        $reports_info['id'],
                        \K::$fw->app_user['group_id'],
                        \K::$fw->app_user['group_id'],
                        \K::$fw->app_user['id']
                    ]
                );
                if (!count($check_query)) {
                    return;
                }
            } elseif (\K::$fw->app_logged_users_id != $reports_info['created_by']) {
                return;
            }

            $hot_reports = new \Models\Main\Reports\Hot_reports();

            //TODO add render
            echo $hot_reports->render_dropdown($reports_id);

            //TODO db_dev_log
            // db_dev_log();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_favorites_header_dropdown()
    {
        echo \Models\Main\Items\Favorites::render_header_dropdown();
    }

    public function update_user_notifications_report()
    {
        echo \Models\Main\Users\Users_notifications::render_dropdown();
    }

    public function set_users_alerts_viewed()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.id', $id)) {
                $sql_data = [
                    'users_id' => \K::$fw->app_user['id'],
                    'alerts_id' => $id,
                ];

                \K::model()->db_perform('app_users_alerts_viewed', $sql_data);
            } else {
                \K::errorPost();
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_filter_status()
    {
        if (\K::$fw->VERB == 'POST') {
            if (\K::fw()->exists('POST.is_active', $is_active)
                and \K::fw()->exists('POST.filter_id', $filter_id)) {
                \K::model()->db_perform('app_reports_filters', ['is_active' => $is_active], ['id = ?', $filter_id]);
                \Helpers\App::app_exit();
            } else {
                \K::errorPost();
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    private function _update_reports($sql_data, $v)
    {
        \K::model()->db_perform('app_reports', $sql_data, [
            'id = ? and created_by = ?',
            str_replace('report_', '', $v),
            \K::fw()->app_user['id']
        ]);
    }
}