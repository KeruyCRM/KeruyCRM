<?php

namespace Tools;

class Plugins
{
    public static function include_menu($key, $menu = [])
    {
        global $app_plugin_menu, $app_user, $app_redirect_to, $app_module_path, $app_path;

        if (isset($app_plugin_menu[$key])) {
            $menu = array_merge($menu, $app_plugin_menu[$key]);
        }

        return $menu;
    }

    public static function handle_action($action)
    {
        global $app_module, $app_action, $app_module_path, $app_user, $app_redirect_to, $app_path, $current_entity_id;

        if (defined('AVAILABLE_PLUGINS')) {
            foreach (explode(',', AVAILABLE_PLUGINS) as $plugin) {
                //include plugin
                if (is_file('plugins/' . $plugin . '/handles/' . $action . '.php')) {
                    require('plugins/' . $plugin . '/handles/' . $action . '.php');
                }
            }
        }
    }

    public static function include_part($part)
    {
        return;
        //TODO Refactored
        global $app_module, $app_action, $app_module_path, $app_user, $app_redirect_to, $app_path, $app_chat;

        if (defined('AVAILABLE_PLUGINS')) {
            foreach (explode(',', AVAILABLE_PLUGINS) as $plugin) {
                //include plugin
                if (is_file('plugins/' . $plugin . '/includes/' . $part . '.php')) {
                    require('plugins/' . $plugin . '/includes/' . $part . '.php');
                }
            }
        }
    }

    public static function render_simple_menu_items($key, $url_params = '')
    {
        $html = '';
        if (count($plugin_menu = self::include_menu($key)) > 0) {
            foreach ($plugin_menu as $v) {
                if (!isset($v['modalbox'])) {
                    $v['modalbox'] = false;
                }
                $v['style'] = $v['style'] ?? '';

                if ($v['modalbox'] == true) {
                    $html .= '<li>' . link_to_modalbox($v['title'], $v['url'] . $url_params, ['style' => $v['style']]
                        ) . '</li>';
                } else {
                    $html .= '<li>' . link_to($v['title'], $v['url'] . $url_params, ['style' => $v['style']]) . '</li>';
                }
            }
        }

        return $html;
    }

    public static function include_dashboard_with_selected_menu_items($reports_id, $url_params = '')
    {
        $html = '';

        /*$reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id) . "'");
        $reports_info = db_fetch_array($reports_info_query);*/

        $reports_info = \K::model()->db_fetch_one('app_reports', [
            'id = ?',
            $reports_id
        ], [], 'id,entities_id');

        if (\Helpers\App::is_ext_installed()) {
            $processes = new \Tools\Ext\Processes\Processes($reports_info['entities_id']);

            if (\K::$fw->app_module_path == 'dashboard/reports') {
                $processes->rdirect_to = 'reports_groups_' . \K::$fw->{'GET.id'};
            } else {
                $processes->rdirect_to = (strstr(
                    $url_params,
                    'parent_item_info_page'
                ) ? 'parent_item_info_page' : 'dashboard');
            }

            $html .= $processes->render_buttons('menu_with_selected', $reports_info['id']);
        }

        $access_schema = \Models\Main\Users\Users::get_entities_access_schema(
            $reports_info['entities_id'],
            \K::$fw->app_user['group_id']
        );

        if (\Helpers\App::is_ext_installed()) {
            if (\Models\Main\Users\Users::has_access('update_selected', $access_schema)) {
                //update records
                $html .= '<li>' . \Helpers\Urls::link_to_modalbox(
                        '<i class="fa fa-edit"></i> ' . \K::$fw->TEXT_EXT_UPDATE_RECORDS,
                        \Helpers\Urls::url_for('ext/with_selected/update', 'reports_id=' . $reports_id . $url_params)
                    ) . '</li>';

                //link records
                if (count(
                        \Tools\Related_records::get_fields_choices_available_to_relate_to_entity(
                            $reports_info['entities_id']
                        )
                    ) > 0) {
                    $html .= '<li>' . \Helpers\Urls::link_to_modalbox(
                            '<i class="fa fa-link"></i> ' . \K::$fw->TEXT_EXT_LINK_RECORDS,
                            \Helpers\Urls::url_for(
                                'ext/with_selected/link',
                                'reports_id=' . $reports_info['id'] . '&entities_id=' . $reports_info['entities_id'] . (strlen(
                                    $url_params
                                ) ? $url_params : '&path=' . $reports_info['entities_id'])
                            )
                        ) . '</li>';
                }
            }

            //copy records
            if (\Models\Main\Users\Users::has_access('copy', $access_schema)) {
                $html .= '<li>' . \Helpers\Urls::link_to_modalbox(
                        '<i class="fa fa-files-o"></i> ' . \K::$fw->TEXT_COPY_RECORDS,
                        \Helpers\Urls::url_for('ext/with_selected/copy', 'reports_id=' . $reports_id . $url_params)
                    ) . '</li>';
            }

            //move records
            if (\Models\Main\Users\Users::has_access('move', $access_schema)) {
                $entity_info = \K::model()->db_find('app_entities', $reports_info['entities_id']);

                if ($entity_info['parent_id'] > 0) {
                    $html .= '<li>' . \Helpers\Urls::link_to_modalbox(
                            '<i class="fa fa-arrows-h"></i> ' . \K::$fw->TEXT_MOVE_RECORDS,
                            \Helpers\Urls::url_for('ext/with_selected/move', 'reports_id=' . $reports_id . $url_params)
                        ) . '</li>';
                }
            }

            $html .= export_templates::get_users_templates_by_position(
                $reports_info['entities_id'],
                'menu_with_selected_dashboard',
                '&reports_id=' . $reports_id . $url_params . (!strstr(
                    $url_params,
                    'path'
                ) ? '&path=' . $reports_info['entities_id'] : '')
            );

            $html .= xml_export::get_users_templates_by_position(
                $reports_info['entities_id'],
                'menu_with_selected_dashboard',
                '&reports_id=' . $reports_id . $url_params . (!strstr(
                    $url_params,
                    'path'
                ) ? '&path=' . $reports_info['entities_id'] : '')
            );

            $html .= export_selected::get_users_templates_by_position(
                $reports_info['entities_id'],
                'menu_with_selected_dashboard',
                '&reports_id=' . $reports_id . $url_params
            );
        }

        if (\Models\Main\Entities::has_subentities(
                $reports_info['entities_id']
            ) == 0 and \Models\Main\Users\Users::has_access(
                'delete',
                $access_schema
            ) and \Models\Main\Users\Users::has_access(
                'delete_selected',
                $access_schema
            ) and $reports_info['entities_id'] != 1) {
            $html .= '<li>' . \Helpers\Urls::link_to_modalbox(
                    '<i class="fa fa-trash-o"></i> ' . \K::$fw->TEXT_BUTTON_DELETE,
                    \Helpers\Urls::url_for(
                        'main/items/delete_selected',
                        'reports_id=' . $reports_info['id'] . (strlen(
                            $url_params
                        ) ? $url_params : '&redirect_to=dashboard&path=' . $reports_info['entities_id'])
                    )
                ) . '</li>';
        }

        return $html;
    }
}