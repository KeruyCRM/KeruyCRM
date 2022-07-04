<?php

namespace Models\Reports;

class Reports_notification
{
    function __construct()
    {
    }

    function send()
    {
        //global $app_user;

        $hot_reports = new hot_reports();

        $users_query = db_query("select * from app_entity_1 where field_5=1");
        while ($user = db_fetch_array($users_query)) {
            \K::$fw->app_user = [
                'id' => $user['id'],
                'group_id' => (int)$user['field_6'],
                'name' => users::output_heading_from_item($user),
                'email' => $user['field_9'],
                'language' => $user['field_13'],
            ];

            $where_sql = '';

            //check hidden common reports
            if (strlen(users_cfg::get_value_by_users_id(\K::$fw->app_user['id'], 'hidden_common_reports')) > 0) {
                $where_sql = " and r.id not in (" . users_cfg::get_value_by_users_id(
                        \K::$fw->app_user['id'],
                        'hidden_common_reports'
                    ) . ")";
            }

            //get common reports list
            $common_reports_list = [];
            $reports_query = db_query(
                "select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input(
                    \K::$fw->app_user['group_id']
                ) . "' and find_in_set(" . \K::$fw->app_user['group_id'] . ",r.users_groups) and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name"
            );
            while ($reports = db_fetch_array($reports_query)) {
                $common_reports_list[] = $reports['id'];
            }

            $reports_query = db_query(
                "select * from app_reports where ((created_by='" . \K::$fw->app_user['id'] . "' and reports_type='standard') " . (count(
                    $common_reports_list
                ) > 0 ? " or id in(" . implode(',', $common_reports_list) . ")" : "") . ") and find_in_set('" . date(
                    'N'
                ) . "',notification_days) and find_in_set('" . date('H') . "',notification_time)"
            );
            while ($reports = db_fetch_array($reports_query)) {
                $items_info = $hot_reports->get_items($reports, ['is_email' => true]);

                if ($items_info['items_count'] > 0) {
                    $reports_url = \K::$fw->CRON_HTTP_SERVER_HOST . 'index.php?module=reports/view&reports_id=' . $reports['id'];

                    $items_html = $this->render_items_html($items_info);

                    $number_of_items_html = sprintf(
                        \K::$fw->TEXT_DISPLAY_NUMBER_OF_ITEMS,
                        1,
                        ($items_info['items_count'] > $hot_reports->popup_items_limit ? $hot_reports->popup_items_limit : $items_info['items_count']),
                        $items_info['items_count']
                    );

                    $email_html = sprintf(
                            \K::$fw->TEXT_REPORTS_NOTIFICATION_EMAIL,
                            $reports['name']
                        ) . '<p><a href="' . $reports_url . '">' . $reports_url . '</a></p><h4>' . $reports['name'] . ':</h4>' . $items_html . $number_of_items_html;

                    $options = [
                        'to' => \K::$fw->app_user['email'],
                        'to_name' => \K::$fw->app_user['name'],
                        'subject' => $reports['name'],
                        'body' => $email_html,
                        'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                        'from_name' => \K::$fw->CFG_EMAIL_NAME_FROM
                    ];

                    users::send_email($options);
                }
            }
        }
    }

    function render_items_html($items_info)
    {
        $html = '';

        if ($items_info['items_count'] > 0) {
            $html = '<ul>';
            foreach ($items_info['items_array'] as $v) {
                $html .= '<li><a href="' . \K::$fw->CRON_HTTP_SERVER_HOST . 'index.php?module=items/info&path=' . $v['path'] . '">' . $v['name'] . '</a></li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }
}