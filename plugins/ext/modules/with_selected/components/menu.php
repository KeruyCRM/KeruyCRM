<?php

/**
 *  Copy/Move/Update actions
 */
if ($app_module_path == 'items/items' or $app_module_path == 'reports/view') {
    if ($app_module_path == 'items/items') {
        if (users::has_access('update_selected')) {
            //update records
            $app_plugin_menu['with_selected'][] = [
                'title' => '<i class="fa fa-edit"></i> ' . TEXT_EXT_UPDATE_RECORDS,
                'url' => url_for('ext/with_selected/update', 'path=' . $current_path),
                'modalbox' => true
            ];

            //link records
            if (count(related_records::get_fields_choices_available_to_relate_to_entity($current_entity_id)) > 0) {
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa fa-link"></i> ' . TEXT_EXT_LINK_RECORDS,
                    'url' => url_for(
                        'ext/with_selected/link',
                        'path=' . $current_path . '&entities_id=' . $current_entity_id
                    ),
                    'modalbox' => true
                ];
            }
        }

        //copy records
        if (users::has_access('copy')) {
            $app_plugin_menu['with_selected'][] = [
                'title' => '<i class="fa fa-files-o"></i> ' . TEXT_COPY_RECORDS,
                'url' => url_for('ext/with_selected/copy', 'path=' . $current_path),
                'modalbox' => true
            ];
        }

        //move records
        if (users::has_access('move')) {
            if (count(explode('/', $current_path)) > 1) {
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa fa-arrows-h"></i> ' . TEXT_MOVE_RECORDS,
                    'url' => url_for('ext/with_selected/move'),
                    'modalbox' => true
                ];
            }
        }

        //templates menu		
        if (count(
                $templates = export_templates::get_users_templates_by_position($current_entity_id, 'menu_with_selected')
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for(
                        'items/print_template',
                        'path=' . $template['entities_id'] . '&templates_id=' . $template['id']
                    ),
                    'modalbox' => true
                ];
            }
        }

        //xml
        if (count(
                $templates = xml_export::get_users_templates_by_position($current_entity_id, 'menu_with_selected')
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for(
                        'items/xml_export_multiple',
                        'path=' . $template['entities_id'] . '&templates_id=' . $template['id']
                    ),
                    'modalbox' => true
                ];
            }
        }

        //export selected
        if (count(
                $templates = export_selected::get_users_templates_by_position($current_entity_id, 'menu_with_selected')
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for('ext/with_selected/export', 'templates_id=' . $template['id']),
                    'modalbox' => true
                ];
            }
        }
    } elseif ($app_module_path == 'reports/view') {
        $reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
        $reports_info = db_fetch_array($reports_info_query);

        $access_schema = users::get_entities_access_schema($reports_info['entities_id'], $app_user['group_id']);

        if (users::has_access('update_selected', $access_schema)) {
            //update records
            $app_plugin_menu['with_selected'][] = [
                'title' => '<i class="fa fa-edit"></i> ' . TEXT_EXT_UPDATE_RECORDS,
                'url' => url_for('ext/with_selected/update', 'reports_id=' . $_GET['reports_id']),
                'modalbox' => true
            ];

            //link records
            if (count(
                    related_records::get_fields_choices_available_to_relate_to_entity($reports_info['entities_id'])
                ) > 0) {
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa fa-link"></i> ' . TEXT_EXT_LINK_RECORDS,
                    'url' => url_for(
                        'ext/with_selected/link',
                        'path=' . $reports_info['entities_id'] . '&reports_id=' . $reports_info['id'] . '&entities_id=' . $reports_info['entities_id']
                    ),
                    'modalbox' => true
                ];
            }
        }

        //copy records
        if (users::has_access('copy', $access_schema)) {
            $app_plugin_menu['with_selected'][] = [
                'title' => '<i class="fa fa-files-o"></i> ' . TEXT_COPY_RECORDS,
                'url' => url_for('ext/with_selected/copy', 'reports_id=' . $_GET['reports_id']),
                'modalbox' => true
            ];
        }

        //move records
        if (users::has_access('move', $access_schema)) {
            $entity_info = db_find('app_entities', $reports_info['entities_id']);

            if ($entity_info['parent_id'] > 0) {
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa fa-arrows-h"></i> ' . TEXT_MOVE_RECORDS,
                    'url' => url_for('ext/with_selected/move', 'reports_id=' . $_GET['reports_id']),
                    'modalbox' => true
                ];
            }
        }

        //templates menu
        if (count(
                $templates = export_templates::get_users_templates_by_position(
                    $reports_info['entities_id'],
                    'menu_with_selected'
                )
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for(
                        'items/print_template',
                        'path=' . $template['entities_id'] . '&templates_id=' . $template['id'] . '&reports_id=' . $reports_info['id']
                    ),
                    'modalbox' => true
                ];
            }
        }

        //xml
        if (count(
                $templates = xml_export::get_users_templates_by_position(
                    $reports_info['entities_id'],
                    'menu_with_selected'
                )
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for(
                        'items/xml_export_multiple',
                        'path=' . $template['entities_id'] . '&templates_id=' . $template['id'] . '&reports_id=' . $reports_info['id']
                    ),
                    'modalbox' => true
                ];
            }
        }

        //export selected
        if (count(
                $templates = export_selected::get_users_templates_by_position(
                    $reports_info['entities_id'],
                    'menu_with_selected'
                )
            ) > 0) {
            foreach ($templates as $template) {
                //print selected items in listing
                $app_plugin_menu['with_selected'][] = [
                    'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                    'url' => url_for(
                        'ext/with_selected/export',
                        'templates_id=' . $template['id'] . '&reports_id=' . $reports_info['id']
                    ),
                    'style' => $template['style'],
                    'modalbox' => true
                ];
            }
        }
    }
}

/**
 * Copy and Move actions in Item info page
 */
if ($app_module_path == 'items/info') {
    $access_rules = new access_rules($current_entity_id, $current_item_id);

    if (users::has_access('copy', $access_rules->get_access_schema())) {
        $app_plugin_menu['more_actions'][] = [
            'title' => '<i class="fa fa-files-o"></i> ' . TEXT_COPY_RECORD,
            'url' => url_for('ext/with_selected/copy_single', 'path=' . $_GET['path']),
            'modalbox' => true
        ];
    }

    //move records
    if (users::has_access('move', $access_rules->get_access_schema())) {
        if (count(explode('/', $_GET['path'])) > 1) {
            $app_plugin_menu['more_actions'][] = [
                'title' => '<i class="fa fa-arrows-h"></i> ' . TEXT_MOVE_RECORDS,
                'url' => url_for('ext/with_selected/move_single', 'path=' . $_GET['path']),
                'modalbox' => true
            ];
        }
    }


    //templates menu
    if (count(
            $templates = export_templates::get_users_templates_by_position($current_entity_id, 'menu_more_actions')
        ) > 0) {
        foreach ($templates as $template) {
            //print or export single template
            $app_plugin_menu['more_actions'][] = [
                'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                'url' => url_for('items/export_template', 'path=' . $_GET['path'] . '&templates_id=' . $template['id']),
                'modalbox' => true
            ];
        }
    }

    //report page
    if (count(
            $templates = report_page\report::get_buttons_by_position(
                $current_entity_id,
                $current_item_id,
                'menu_more_actions'
            )
        ) > 0) {
        foreach ($templates as $template) {
            //print or export single template
            $app_plugin_menu['more_actions'][] = [
                'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                'url' => url_for('items/report_page', 'path=' . $_GET['path'] . '&report_id=' . $template['id']),
                'modalbox' => true
            ];
        }
    }

    //xml
    if (count($templates = xml_export::get_users_templates_by_position($current_entity_id, 'menu_more_actions')) > 0) {
        foreach ($templates as $template) {
            //print or export single template
            $app_plugin_menu['more_actions'][] = [
                'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                'url' => url_for(
                    'items/xml_export_multiple',
                    'path=' . $_GET['path'] . '&templates_id=' . $template['id']
                ),
                'modalbox' => true
            ];
        }
    }

    //xml import
    if (count($templates = xml_import::get_users_templates_by_position($current_entity_id, 'menu_more_actions')) > 0) {
        foreach ($templates as $template) {
            //print or export single template
            $app_plugin_menu['more_actions'][] = [
                'title' => '<i class="fa ' . $template['button_icon'] . '"></i> ' . $template['name'],
                'url' => url_for(
                    'items/xml_import_multiple',
                    'path=' . $_GET['path'] . '&templates_id=' . $template['id']
                ),
                'modalbox' => true
            ];
        }
    }
}