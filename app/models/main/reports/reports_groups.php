<?php

namespace Models\Main\Reports;

class Reports_groups
{
    static function render_dashboard_tabs()
    {
        $active_id = isset($_GET['id']) ? _GET('id') : 0;

        \K::fw()->exists('GET.id', $active_id);

        $html = '';
        $menu = [];

        $menu[] = [
            'id' => 0,
            'title' => \K::$fw->TEXT_MENU_DASHBOARD,
            'url' => \Helpers\Urls::url_for('main/dashboard/dashboard'),
            'class' => 'fa-home',
            'icon_color' => '',
            'bg_color' => '',
        ];

        /*$reports_query = db_query(
            "select * from app_reports_groups where created_by = '" . \K::$fw->app_user['id'] . "' and is_common=0 and in_dashboard=1 order by sort_order, name"
        );*/

        $reports_query = \K::model()->db_fetch('app_reports_groups', [
            'created_by = ? and is_common = 0 and in_dashboard = 1',
            \K::$fw->app_user['id']
        ], ['order' => 'sort_order,name']);

        //while ($v = db_fetch_array($reports_query)) {
        foreach ($reports_query as $v) {
            $v = $v->cast();
            $menu[] = [
                'id' => $v['id'],
                'title' => $v['name'],
                'url' => \Helpers\Urls::url_for('main/dashboard/reports', 'id=' . $v['id']),
                'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : ''),
                'icon_color' => $v['icon_color'],
                'bg_color' => $v['bg_color'],
            ];
        }

        /*$reports_query = db_query(
            "select * from app_reports_groups where (find_in_set(" . \K::$fw->app_user['group_id'] . ",users_groups) or find_in_set(" . \K::$fw->app_user['id'] . ",assigned_to)) and is_common=1 and in_dashboard=1 order by sort_order, name"
        );*/
        $reports_query = \K::model()->db_fetch('app_reports_groups', [
            '(find_in_set( ? ,users_groups) or find_in_set( ? ,assigned_to)) and is_common = 1 and in_dashboard = 1',
            \K::$fw->app_user['group_id'],
            \K::$fw->app_user['id']
        ], ['order' => 'sort_order,name']);

        //while ($v = db_fetch_array($reports_query)) {
        foreach ($reports_query as $v) {
            $v = $v->cast();
            $menu[] = [
                'id' => $v['id'],
                'title' => $v['name'],
                'url' => \Helpers\Urls::url_for('main/dashboard/reports_groups', 'id=' . $v['id']),
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
                $icon = strlen($v['class']) ? \Helpers\App::app_render_icon(
                        $v['class'],
                        $icon_color
                    ) . ' reports_groups.php' : '';
                $bg_color = strlen($v['bg_color']) ? 'style="background-color: ' . $v['bg_color'] . '"' : '';

                $html .= '<li class="' . ($active_id == $v['id'] ? 'active' : '') . '" ><a ' . $bg_color . ' href="' . $v['url'] . '">' . $icon . $v['title'] . '</a></li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }
}