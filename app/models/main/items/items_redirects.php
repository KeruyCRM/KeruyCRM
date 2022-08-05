<?php

namespace Models\Main\Items;

class Items_redirects
{
    static function get_reports_choices($entities_id)
    {
        $choices = [];

        $entities_query = db_query("select id, name from app_entities where parent_id={$entities_id}");
        while ($entities = db_fetch_array($entities_query)) {
            $reports_query = db_query(
                "select g.id, g.name from app_ext_ganttchart g, app_entities e where e.id=g.entities_id and e.id={$entities['id']} order by g.name"
            );
            while ($v = db_fetch_array($reports_query)) {
                $choices[$entities['name'] . ': ' . TEXT_EXT_GANTTCHART_REPORT]['ganttreport' . $v['id']] = $v['name'];
            }

            $reports_query = db_query(
                "select k.id, k.name from app_ext_kanban k, app_entities e where e.id=k.entities_id and e.id={$entities['id']} order by k.name"
            );
            while ($v = db_fetch_array($reports_query)) {
                $choices[$entities['name'] . ': ' . TEXT_EXT_KANBAN]['kanban' . $v['id']] = $v['name'];
            }

            $reports_query = db_query(
                "select c.id, c.name from app_ext_calendar c, app_entities e where e.id=c.entities_id and e.id={$entities['id']} order by c.name"
            );
            while ($v = db_fetch_array($reports_query)) {
                $choices[$entities['name'] . ': ' . TEXT_EXT_CALENDAR]['calendarreport' . $v['id']] = $v['name'];
            }
        }

        return $choices;
    }

    static function redirect_to_report($reports_type, $path)
    {
        global $app_user;

        $reports_id = str_replace(entities_menu::get_reports_types(), '', $reports_type);

        switch (true) {
            case strstr($reports_type, 'ganttreport'):
                if ($app_user['group_id'] > 0) {
                    $reports_query = db_query(
                        "select g.id, g.name,g.entities_id from app_ext_ganttchart g, app_entities e, app_ext_ganttchart_access ga where g.id='" . $reports_id . "' and e.id=g.entities_id  and g.id=ga.ganttchart_id and ga.access_groups_id='" . db_input(
                            $app_user['group_id']
                        ) . "' order by name"
                    );
                } else {
                    $reports_query = db_query(
                        "select g.id, g.name,g.entities_id from app_ext_ganttchart g, app_entities e where g.id='" . $reports_id . "' and  e.id=g.entities_id order by g.name"
                    );
                }

                if ($reports_info = db_fetch_array($reports_query)) {
                    redirect_to(
                        'ext/ganttchart/dhtmlx',
                        'id=' . $reports_info['id'] . '&path=' . $path . '/' . $reports_info['entities_id']
                    );
                }

                break;
            case strstr($reports_type, 'kanban'):
                if ($app_user['group_id'] > 0) {
                    $reports_query = db_query(
                        "select c.id, c.name, c.entities_id from app_ext_kanban c, app_entities e where c.id='" . $reports_id . "' and e.id=c.entities_id and find_in_set(" . $app_user['group_id'] . ",c.users_groups) order by c.name"
                    );
                } else {
                    $reports_query = db_query(
                        "select c.id, c.name, c.entities_id from app_ext_kanban c, app_entities e where c.id='" . $reports_id . "' and e.id=c.entities_id order by c.name"
                    );
                }

                if ($reports_info = db_fetch_array($reports_query)) {
                    redirect_to(
                        'ext/kanban/view',
                        'id=' . $reports_info['id'] . '&path=' . $path . '/' . $reports_info['entities_id']
                    );
                }
                break;
            case strstr($reports_type, 'calendarreport'):
                if ($app_user['group_id'] > 0) {
                    $reports_query = db_query(
                        "select c.* from app_ext_calendar c, app_entities e, app_ext_calendar_access ca where c.id='" . $reports_id . "' and e.id=c.entities_id and c.id=ca.calendar_id and ca.access_groups_id='" . db_input(
                            $app_user['group_id']
                        ) . "' order by c.name"
                    );
                } else {
                    $reports_query = db_query(
                        "select c.* from app_ext_calendar c, app_entities e where c.id='" . $reports_id . "' and  e.id=c.entities_id order by c.name"
                    );
                }

                if ($reports_info = db_fetch_array($reports_query)) {
                    redirect_to(
                        'ext/calendar/report',
                        'id=' . $reports_info['id'] . '&path=' . $path . '/' . $reports_info['entities_id']
                    );
                }
                break;
        }
    }
}