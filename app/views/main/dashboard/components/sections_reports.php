<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

$reports_id = str_replace(entities_menu::get_reports_types(), '', \K::$fw->section_report);

switch (true) {
    case strstr(\K::$fw->section_report, 'standard'):

        $reports = \K::model()->db_fetch_one('app_reports', [
            'created_by = ? and id = ?',
            \K::$fw->app_logged_users_id,
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;

            echo \K::view()->render(\Helpers\Urls::components_path('main/dashboard/render_standard_reports'));
        }
        break;
    case strstr(\K::$fw->section_report, 'common'):

        $reports = \K::model()->db_fetch_one('app_reports', [
            'find_in_set( ? ,users_groups) and id = ?',
            \K::$fw->app_user['group_id'],
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;

            echo \K::view()->render(\Helpers\Urls::components_path('main/dashboard/render_standard_reports'));
        }
        break;
    case strstr(\K::$fw->section_report, 'graphicreport'):

        $reports = \K::model()->db_fetch_one('app_ext_graphicreport', [
            'id = ?',
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;
            if (in_array(
                    \K::$fw->app_user['group_id'],
                    explode(',', $reports['allowed_groups'])
                ) or \K::$fw->app_user['group_id'] == 0) {
                echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                        'ext/graphicreport/view',
                        'id=' . $reports['id']
                    ) . '">' . $reports['name'] . '</a></h3>';

                echo \K::view()->render(\Helpers\Urls::components_path('ext/graphicreport/view'));
            }
        }
        break;
    case strstr(\K::$fw->section_report, 'funnelchart'):

        $reports = \K::model()->db_fetch_one('app_ext_funnelchart', [
            'id = ?',
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;
            if (in_array(
                    \K::$fw->app_user['group_id'],
                    explode(',', $reports['users_groups'])
                ) or \K::$fw->app_user['group_id'] == 0) {
                echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                        'ext/funnelchart/view',
                        'id=' . $reports['id']
                    ) . '">' . $reports['name'] . '</a></h3>';

                echo \K::view()->render(\Helpers\Urls::components_path('ext/funnelchart/view'));
            }
        }
        break;

    case strstr(\K::$fw->section_report, 'pivot_tables'):

        $pivot_tables = \K::model()->db_fetch_one('app_ext_pivot_tables', [
            'id = ?',
            $reports_id
        ]);

        if ($pivot_tables) {
            \K::$fw->pivot_tables = $pivot_tables;
            echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                    'ext/pivot_tables/view',
                    'id=' . $pivot_tables['id']
                ) . '">' . $pivot_tables['name'] . '</a></h3>';
            $pivot_table = new pivot_tables($pivot_tables);

            echo \K::view()->render(\Helpers\Urls::components_path('ext/pivot_tables/pivot_table'));
        }
        break;
    case strstr(\K::$fw->section_report, 'pivotreports'):

        $pivotreports = \K::model()->db_fetch_one('app_ext_pivotreports', [
            'id = ?',
            $reports_id
        ]);

        if ($pivotreports) {
            if (in_array(
                    \K::$fw->app_user['group_id'],
                    explode(',', $pivotreports['allowed_groups'])
                ) or \K::$fw->app_user['group_id'] == 0) {
                echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                        'ext/pivotreports/view',
                        'id=' . $pivotreports['id']
                    ) . '">' . $pivotreports['name'] . '</a></h3>';
                echo '
						<style>
							.pvtVals{
								display:none;
							}
							.pvtRendererTD{
								display:none;
							}
						</style>
						';

                //allow edit
                \K::$fw->pivotreports = pivotreports::apply_allow_edit($pivotreports);

                echo \K::view()->render(\Helpers\Urls::components_path('ext/pivotreports/pivottable'));
            }
        }
        break;
    case strstr(\K::$fw->section_report, 'calendar_personal'):

        echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                'ext/calendar/personal'
            ) . '">' . \K::$fw->TEXT_EXT_MY_CALENDAR . '</a>' . icalendar::get_url(
                \K::$fw->CFG_PERSONAL_CALENDAR_ICAL,
                'personal'
            ) . '</h3>';

        echo \K::view()->render(\Helpers\Urls::components_path('ext/calendar/personal'));
        break;
    case strstr(\K::$fw->section_report, 'calendar_public'):

        echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                'ext/calendar/public'
            ) . '">' . \K::$fw->TEXT_EXT_CALENDAR . '</a> ' . icalendar::get_url(
                \K::$fw->CFG_PUBLIC_CALENDAR_ICAL,
                'public'
            ) . '</h3>';

        echo \K::view()->render(\Helpers\Urls::components_path('ext/calendar/public'));
        break;
    case strstr(\K::$fw->section_report, 'calendarreport'):

        if (\K::$fw->app_user['group_id'] > 0) {
            $reports_query = \K::model()->db_query_exec(
                'select c.* from app_ext_calendar c, app_entities e, app_ext_calendar_access ca where c.id = :reports_id and e.id = c.entities_id and c.id = ca.calendar_id and ca.access_groups_id = :group_id order by c.name',
                [
                    ':reports_id' => $reports_id,
                    ':group_id' => \K::$fw->app_user['group_id']
                ]
            );
        } else {
            $reports_query = \K::model()->db_query_exec(
                "select c.* from app_ext_calendar c, app_entities e where c.id = :reports_id and  e.id = c.entities_id order by c.name",
                [
                    ':reports_id' => $reports_id
                ]
            );
        }
        if (isset($reports_query[0])) {
            $reports = $reports_query[0];
            \K::$fw->reports = $reports;

            echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                    'ext/calendar/report',
                    'id=' . $reports['id']
                ) . '">' . $reports['name'] . '</a> ' . icalendar::get_url(
                    $reports['enable_ical'],
                    'report',
                    $reports['id']
                ) . '</h3>';

            echo \K::view()->render(\Helpers\Urls::components_path('ext/calendar/report'));
        }
        break;
    case strstr(\K::$fw->section_report, 'pivot_calendars'):

        $reports = \K::model()->db_fetch_one('app_ext_pivot_calendars', [
            'id = ?',
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;
            if (pivot_calendars::has_access($reports['users_groups'])) {
                echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                        'ext/pivot_calendars/view',
                        'id=' . $reports['id']
                    ) . '">' . $reports['name'] . '</a>' . icalendar::get_url(
                        $reports['enable_ical'],
                        'pivot_report',
                        $reports['id']
                    ) . '</h3>';

                echo \K::view()->render(\Helpers\Urls::components_path('ext/pivot_calendars/report'));
            }
        }
        break;
    case strstr(\K::$fw->section_report, 'resource_timeline'):

        $reports = \K::model()->db_fetch_one('app_ext_resource_timeline', [
            'id = ?',
            $reports_id
        ]);

        if ($reports) {
            \K::$fw->reports = $reports;
            echo '<h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                    'ext/resource_timeline/view',
                    'id=' . $reports['id']
                ) . '">' . $reports['name'] . '</a></h3>';

            echo \K::view()->render(\Helpers\Urls::components_path('ext/resource_timeline/report'));
        }
        break;
}