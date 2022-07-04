<?php

namespace Models\Reports;

class Reports_groups
{
    static function render_dashboard_tabs()
    {
        global $app_user;

        $active_id = isset($_GET['id']) ? _GET('id') : 0;

        $html = '';

        $menu = [];

        $menu[] = [
            'id' => 0,
            'title' => TEXT_MENU_DASHBOARD,
            'url' => url_for('dashboard/dashboard'),
            'class' => 'fa-home',
            'icon_color' => '',
            'bg_color' => '',
        ];

        $reports_query = db_query(
            "select * from app_reports_groups where created_by = '" . $app_user['id'] . "' and is_common=0 and in_dashboard=1 order by sort_order, name"
        );
        while ($v = db_fetch_array($reports_query)) {
            $menu[] = [
                'id' => $v['id'],
                'title' => $v['name'],
                'url' => url_for('dashboard/reports', 'id=' . $v['id']),
                'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : ''),
                'icon_color' => $v['icon_color'],
                'bg_color' => $v['bg_color'],
            ];
        }

        $reports_query = db_query(
            "select * from app_reports_groups where (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set({$app_user['id']},assigned_to)) and is_common=1 and in_dashboard=1 order by sort_order, name"
        );
        while ($v = db_fetch_array($reports_query)) {
            $menu[] = [
                'id' => $v['id'],
                'title' => $v['name'],
                'url' => url_for('dashboard/reports_groups', 'id=' . $v['id']),
                'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : ''),
                'icon_color' => $v['icon_color'],
                'bg_color' => $v['bg_color'],
            ];
        }

        if (count($menu) > 1) {
            $html .= '<ul class="nav nav-tabs nav-reports-groups">';

            foreach ($menu as $v) {
                $icon_color = (isset($v['icon_color']) and strlen(
                        $v['icon_color']
                    )) ? 'style="color: ' . $v['icon_color'] . '"' : '';
                $icon = strlen($v['class']) ? app_render_icon($v['class'], $icon_color) . ' ' : '';
                $bg_color = strlen($v['bg_color']) ? 'style="background-color: ' . $v['bg_color'] . '"' : '';

                $html .= '<li class="' . ($active_id == $v['id'] ? 'active' : '') . '" ><a ' . $bg_color . ' href="' . $v['url'] . '">' . $icon . $v['title'] . '</a></li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }
}