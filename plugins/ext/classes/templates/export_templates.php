<?php

class export_templates
{

    static function delete_by_entity_id($entities_id)
    {
        $templates_query = db_query("select id from app_ext_export_templates where entities_id='" . $entities_id . "'");
        while ($templates = db_fetch_array($templates_query)) {
            reports::delete_reports_by_type('export_templates' . $templates['id']);

            export_templates_blocks::delele_blocks_by_template_id($templates['id']);

            db_query("delete from app_ext_export_templates where id='" . db_input($templates['id']) . "'");
        }
    }

    static function get_available_fields_for_all_entities(
        $template_entity_id,
        $css_class = "insert_to_template_description"
    ) {
        $entities_list = [];
        $entities_list[] = $template_entity_id;

        foreach (entities::get_parents($template_entity_id) as $id) {
            $entities_list[] = $id;
        }

        //print_rr($entities_list);

        $html = '<ul class="list-inline">';

        foreach ($entities_list as $entity_id) {
            $html .= '<li>';

            $html .= '
  			<div class="dropdown">
				  <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				    ' . entities::get_name_by_id($entity_id) . '
				    <span class="caret"></span>
				  </button>
  				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">';

            if ($template_entity_id == $entity_id) {
                $entity_cfg = entities::get_cfg($entity_id);

                if ($entity_cfg['use_comments'] == 1) {
                    $html .= '
				    <li style="cursor: pointer">
				      <a href="#" class="' . $css_class . '">{#comments:' . TEXT_COMMENTS . '}</a>
				    </li>';
                }

                $html .= '
				    <li>
				      <a href="#" class="' . $css_class . '">{#id:' . TEXT_FIELDTYPE_ID_TITLE . '}</a>
				    </li>
				    <li>
				      <a href="#" class="' . $css_class . '">{#date_added:' . TEXT_FIELDTYPE_DATEADDED_TITLE . '}</a>
				    </li>
				    <li>
				      <a href="#" class="' . $css_class . '">{#date_updated:' . TEXT_FIELDTYPE_DATE_UPDATED_TITLE . '}</a>
				    </li>
				    <li>
				      <a href="#" class="' . $css_class . '">{#created_by:' . TEXT_FIELDTYPE_CREATEDBY_TITLE . '}</a>
				    </li>
                                    <li>
				      <a href="#" class="' . $css_class . '">{#parent_item_id:' . TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE . '}</a>
				    </li>
                                    ';
            }

            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserved_types_list(
                ) . ") and f.entities_id='" . $entity_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );

            while ($fields = db_fetch_array($fields_query)) {
                if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                    $html .= fieldtype_dropdown_multilevel::output_export_template($fields);
                } else {
                    $html .= '
				    <li>
				      <a href="#" class="' . $css_class . '">{#' . $fields['id'] . ':' . strip_tags(
                            fields_types::get_option($fields['type'], 'name', $fields['name'])
                        ) . '}</a>
				    </li>';
                }
            }

            $html .= '
					</ul>
				</div>';

            $html .= '</li>';
        }


        $html .= '
			<li>
  			<div class="dropdown">
				  <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				    ' . TEXT_CURRENT_DATE . '
				    <span class="caret"></span>
				  </button>
  				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">
				    <li><a href="#" class="' . $css_class . '">{#current_date}</a></li>
				    <li><a href="#" class="' . $css_class . '">{#current_date_time}</a></li>
					</ul>
				</div>
			</li>';

        if ($css_class == 'insert_to_template_description') {
            $html .= '
					<li>
						<a href="javascript: open_dialog(\'' . url_for(
                    'ext/templates/export_templates_help'
                ) . '\')" ><i class="fa fa-question-circle" aria-hidden="false"></i> ' . TEXT_HELP . '</a>
					</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    static function get_position_choices()
    {
        $choices = [];
        $choices['default'] = TEXT_DEFAULT;
        $choices['menu_more_actions'] = TEXT_EXT_MENU_MORE_ACTIONS;
        $choices['menu_with_selected'] = TEXT_EXT_MENU_WITH_SELECTED;
        $choices['menu_print'] = TEXT_EXT_PRINT_BUTTON;

        return $choices;
    }

    static function get_users_templates_by_position($entities_id, $position, $url_params = '')
    {
        global $app_user;

        $templates_list = [];

        $html = '';

        $templates_query = db_query(
            "select ep.* from app_ext_export_templates ep, app_entities e where ep.is_active=1 and e.id=ep.entities_id and find_in_set('" . str_replace(
                '_dashboard',
                '',
                $position
            ) . "',ep.button_position) and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) order by ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            if (!in_array($position, ['menu_with_selected', 'menu_with_selected_dashboard'])) {
                if (!self::check_buttons_filters($templates)) {
                    continue;
                }
            }

            $button_title = (strlen($templates['button_title']) ? $templates['button_title'] : $templates['name']);
            $button_icon = (strlen($templates['button_icon']) ? $templates['button_icon'] : 'fa-print');

            $style = (strlen($templates['button_color']) ? 'color: ' . $templates['button_color'] : '');

            switch ($position) {
                case 'default':
                    $html .= '<li>' . button_tag(
                            $button_title,
                            url_for(
                                'items/export_template',
                                'path=' . $_GET['path'] . '&templates_id=' . $templates['id']
                            ),
                            true,
                            ['class' => 'btn btn-primary btn-sm btn-template-' . $templates['id']],
                            $button_icon
                        ) . '</li>';
                    $html .= self::prepare_button_css($templates);
                    break;
                case 'menu_more_actions':
                    $templates_list[] = [
                        'id' => $templates['id'],
                        'name' => $button_title,
                        'entities_id' => $templates['entities_id'],
                        'button_icon' => $button_icon
                    ];
                    break;
                case 'menu_with_selected':
                    $templates_list[] = [
                        'id' => $templates['id'],
                        'name' => $button_title,
                        'entities_id' => $templates['entities_id'],
                        'button_icon' => $button_icon
                    ];
                    break;
                case 'menu_print':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for(
                                'items/export_template',
                                'path=' . $_GET['path'] . '&templates_id=' . $templates['id']
                            ),
                            ['style' => $style]
                        ) . '</li>';
                    break;
                case 'menu_with_selected_dashboard':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for('items/print_template', 'templates_id=' . $templates['id'] . $url_params),
                            ['style' => $style]
                        ) . '</li>';
                    break;
            }
        }


        switch ($position) {
            case 'default':
            case 'menu_with_selected_dashboard':
            case 'menu_print':
                return $html;
                break;
            case 'menu_more_actions':
            case 'menu_with_selected':
                return $templates_list;
                break;
        }
    }

    public static function prepare_button_css($buttons)
    {
        $css = '';

        if (strlen($buttons['button_color'])) {
            $rgb = convert_html_color_to_RGB($buttons['button_color']);
            $rgb[0] = $rgb[0] - 25;
            $rgb[1] = $rgb[1] - 25;
            $rgb[2] = $rgb[2] - 25;
            $css = '
					<style>
						.btn-template-' . $buttons['id'] . '{
							background-color: ' . $buttons['button_color'] . ';
						  border-color: ' . $buttons['button_color'] . ';
						}
						.btn-primary.btn-template-' . $buttons['id'] . ':hover,
						.btn-primary.btn-template-' . $buttons['id'] . ':focus,
						.btn-primary.btn-template-' . $buttons['id'] . ':active,
						.btn-primary.btn-template-' . $buttons['id'] . '.active{
						  background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
						  border-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
						}
					</style>
			';
        }

        return $css;
    }

    public static function check_buttons_filters($buttons)
    {
        global $current_item_id, $current_entity_id, $sql_query_having;

        $reports_info_query = db_query(
            "select id from app_reports where entities_id='" . db_input(
                $buttons['entities_id']
            ) . "' and reports_type='export_templates" . $buttons['id'] . "'"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $listing_sql_query = '';
            $listing_sql_query_select = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select(
                $current_entity_id,
                $listing_sql_query_select
            );

            $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$current_entity_id])) {
                $listing_sql_query_having = reports::prepare_filters_having_query(
                    $sql_query_having[$current_entity_id]
                );
            }

            $listing_sql_query .= $listing_sql_query_having;

            $item_info_sql = "select e.id " . $listing_sql_query_select . " from app_entity_" . $buttons['entities_id'] . " e  where e.id='" . $current_item_id . "' " . $listing_sql_query;

            $item_info_query = db_query($item_info_sql);
            if ($item_info = db_fetch_array($item_info_query)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    static function get_users_templates($entities_id)
    {
        global $app_user;

        $templates_list = [];

        $templates_query = db_query(
            "select ep.* from app_ext_export_templates ep, app_entities e where ep.is_active=1 and e.id=ep.entities_id and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) order by ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            $templates_list[] = [
                'id' => $templates['id'],
                'name' => $templates['name'],
                'entities_id' => $templates['entities_id']
            ];
        }

        return $templates_list;
    }

    static function has_users_access($entities_id, $templates_id)
    {
        global $app_user;

        $templates_query = db_query(
            "select ep.* from app_ext_export_templates ep, app_entities e where e.id=ep.entities_id and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . db_input($app_user['group_id']) . ",users_groups) or find_in_set(" . db_input(
                $app_user['id']
            ) . ",assigned_to)) and ep.id='" . db_input($templates_id) . "' order by ep.sort_order, ep.name"
        );
        if ($templates = db_fetch_array($templates_query)) {
            return true;
        } else {
            return false;
        }
    }

    static function output_comments_list($entities_id, $items_id, $pattern)
    {
        global $app_users_cache, $fields_access_schema_holder, $app_user, $app_path;

        $html = '';

        if (!isset($fields_access_schema_holder[$entities_id])) {
            $fields_access_schema = $fields_access_schema_holder[$entities_id] = users::get_fields_access_schema(
                $entities_id,
                $app_user['group_id']
            );
        } else {
            $fields_access_schema = $fields_access_schema_holder[$entities_id];
        }

        $limit = false;
        if (preg_match("/\[(.+)\]/", $pattern, $matches)) {
            $limit = (int)$matches[1];
        }

        $count = 0;
        $comments_query_sql = "select * from app_comments where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'  order by date_added desc " . ($limit ? ' limit ' . $limit : '');
        $items_query = db_query($comments_query_sql);
        while ($item = db_fetch_array($items_query)) {
            $descripttion = $item['description'];

            //include attachments
            if (strlen($item['attachments'])) {
                $descripttion .= "<div style='padding-top: 7px;'>";
                foreach (explode(',', $item['attachments']) as $filename) {
                    $file = attachments::parse_filename($filename);
                    $descripttion .= $file['name'] . "<br>";
                }
                $descripttion .= "</div>";
            }

            $html_fields = '';
            $comments_fields_query = db_query(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input(
                    $item['id']
                ) . "' and f.id=ch.fields_id order by ch.id"
            );
            while ($field = db_fetch_array($comments_fields_query)) {
                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_export' => true,
                    'is_print' => true,
                    'path' => $app_path
                ];


                $html_fields .= "
            <tr>
      				<th style='text-align: left;vertical-align: top; font-size: 11px;'>&bull;&nbsp;" . htmlspecialchars(
                        $field['name']
                    ) . ":&nbsp;</th>
      				<td style='font-size: 11px;'>" . htmlspecialchars(
                        strip_tags(fields_types::output($output_options))
                    ) . "</td>
      			</tr>
        ";
            }

            //include comments fileds
            if (strlen($html_fields)) {
                $descripttion .= "<table style='padding-top: 7px;' border='0'>" . $html_fields . "</table>";
            }


            if (strlen($descripttion)) {
                $html .= '<div ' . ($count > 0 ? 'style="margin-top: 8px; padding-top: 8px; border-top: 1px solid black;"' : '') . '><b>' . $app_users_cache[$item['created_by']]['name'] . ' - ' . format_date_time(
                        $item['date_added']
                    ) . '</b></div>';
                $html .= '<div >' . $descripttion . '</div>';

                $count++;
            }
        }

        return $html;
    }

    static function output_entities_items_list(
        $entities_id,
        $items_id,
        $export_entity_id,
        $pattern,
        $is_tree_table = false
    ) {
        global $current_path;

        //prepare pattern <...>
        $pattern = str_replace(['&lt;', '&gt;'], ['<', '>'], $pattern);
        preg_match("/<(.+)>/", $pattern, $matches);

        $pattern_array = explode(':', $pattern);

        //echo $pattern;
        //print_r($matches);

        $fields_list = [];

        //get id if they setup in pattern
        if (isset($matches[1])) {
            $fields_list = explode(',', $matches[1]);
            $reports_id = str_replace($matches[1], '', $pattern_array[1]);
        } else {
            $reports_id = $pattern_array[1];

            //get default listing configuration
            $fields_query = db_query(
                "select f.* from app_fields f where f.listing_status = 1 and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id='" . db_input(
                    $export_entity_id
                ) . "' order by f.listing_sort_order"
            );
            while ($v = db_fetch_array($fields_query)) {
                $fields_list[] = $v['id'];
            }
        }

        if ($reports_id > 0) {
            //get reports filds listing configuraion
            $reports_info_query = db_query(
                "select * from app_reports where id='" . db_input($reports_id) . "' and entities_id='" . db_input(
                    $export_entity_id
                ) . "'"
            );
            if ($reports_info = db_fetch_array($reports_info_query)) {
                if (strlen($reports_info['fields_in_listing'])) {
                    $fields_list = [];
                    $fields_query = db_query(
                        "select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name  from app_fields f where f.id in (" . $reports_info['fields_in_listing'] . ") and  f.entities_id='" . db_input(
                            $export_entity_id
                        ) . "' order by field(f.id," . $reports_info['fields_in_listing'] . ")"
                    );
                    while ($v = db_fetch_array($fields_query)) {
                        $fields_list[] = $v['id'];
                    }
                }
            } else {
                $reports_id = false;
            }
        }

        //return 'OK-' . $entities_id . ' - '  . $items_id . ' - '. $export_entity_id . ' - ' . $reports_id;

        $html = '';

        if (count($fields_list)) {
            $html = '<table border="1"  width="100%" class="export-table export-table-' . $export_entity_id . '"><tr>';

            foreach ($fields_list as $id) {
                $field_query = db_query("select * from app_fields where id='" . $id . "'");
                if ($field = db_fetch_array($field_query)) {
                    $html .= '<th style="padding: 1px 5px; text-align: left;" class="export-table-th-' . $field['id'] . '">' . (strlen(
                            $field['short_name']
                        ) ? $field['short_name'] : fields_types::get_option(
                            $field['type'],
                            'name',
                            $field['name']
                        )) . '</th>';
                }
            }

            $html .= '</tr>';

            $export_entity_info = db_find('app_entities', $export_entity_id);

            //get parents
            $export_entities_parents = [];
            if ($export_entity_info['parent_id'] > 0) {
                $export_entities_parents = entities::get_parents($export_entity_id);
            }

            $listing_sql_query = '';
            $listing_sql_query_join = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            if ($is_tree_table) {
                $listing_sql_query .= " and e.parent_id=0";
            }

            if ($entities_id == $export_entity_info['parent_id']) {
                $listing_sql_query .= " and e.parent_item_id='" . $items_id . "'";
            } elseif (in_array($entities_id, $export_entities_parents)) {
                //include parents in query
                $count = 0;
                $listing_sql_query .= " and e.parent_item_id in (";
                foreach ($export_entities_parents as $id) {
                    if ($entities_id == $id) {
                        $listing_sql_query .= $items_id . ")" . str_repeat(')', $count);
                        break;
                    } else {
                        $listing_sql_query .= "select id from app_entity_" . $id . " where parent_item_id in (";
                        $count++;
                    }
                }
            }

            //if reportn set then use filters and order settings from report
            if ($reports_id > 0) {
                $listing_sql_query = reports::add_filters_query($reports_id, $listing_sql_query);

                //prepare having query for formula fields
                if (isset($sql_query_having[$export_entity_id])) {
                    $listing_sql_query_having = reports::prepare_filters_having_query(
                        $sql_query_having[$export_entity_id]
                    );
                }

                if (strlen($reports_info['listing_order_fields']) > 0) {
                    $info = reports::add_order_query($reports_info['listing_order_fields'], $export_entity_id);

                    $listing_sql_query .= $info['listing_sql_query'];
                    $listing_sql_query_join .= $info['listing_sql_query_join'];
                }

                $listing_sql_query = items::add_access_query(
                    $reports_info['entities_id'],
                    $listing_sql_query,
                    $reports_info['displays_assigned_only']
                );
            } else {
                $listing_sql_query = items::add_access_query($export_entity_id, $listing_sql_query);
            }

            $listing_sql_query .= $listing_sql_query_having;

            $items_info_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                    $export_entity_id,
                    ''
                ) . " from app_entity_" . $export_entity_id . " e {$listing_sql_query_join} where e.id>0 {$listing_sql_query}";
            $items_query = db_query($items_info_sql, false);
            while ($item = db_fetch_array($items_query)) {
                $html .= '<tr>';
                foreach ($fields_list as $id) {
                    $field_query = db_query("select * from app_fields where id='" . $id . "'");
                    if ($field = db_fetch_array($field_query)) {
                        //prepare field value
                        $value = items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'path' => $current_path
                        ];

                        if (in_array(
                            $field['type'],
                            ['fieldtype_textarea_wysiwyg', 'fieldtype_barcode', 'fieldtype_qrcode']
                        )) {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . trim(
                                    fields_types::output($output_options)
                                ) . '</td>';
                        } elseif (in_array(
                            $field['type'],
                            [
                                'fieldtype_months_difference',
                                'fieldtype_years_difference',
                                'fieldtype_hours_difference',
                                'fieldtype_days_difference',
                                'fieldtype_mysql_query',
                                'fieldtype_input_numeric',
                                'fieldtype_formula',
                                'fieldtype_js_formula',
                                'fieldtype_input_numeric_comments'
                            ]
                        )) {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px; text-align: right;">' . trim(
                                    strip_tags(fields_types::output($output_options))
                                ) . '</td>';
                        } else {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . trim(
                                    strip_tags(fields_types::output($output_options))
                                ) . '</td>';
                        }
                    }
                }

                $html .= '</tr>';

                if ($is_tree_table) {
                    $html = self::output_entities_nested_items_list(
                        $export_entity_id,
                        $item['id'],
                        $fields_list,
                        $html
                    );
                }
            }

            $html .= '</table>';
        }

        return $html;
    }

    static function output_entities_nested_items_list($entities_id, $items_id, $fields_list, $html, $level = 1)
    {
        global $current_path, $app_heading_fields_cache;

        $items_info_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $entities_id,
                ''
            ) . " from app_entity_" . $entities_id . " e  where e.parent_id={$items_id} order by e.sort_order, e.id";
        $items_query = db_query($items_info_sql, false);
        while ($item = db_fetch_array($items_query)) {
            $html .= '<tr>';
            foreach ($fields_list as $id) {
                $field_query = db_query("select * from app_fields where id='" . $id . "'");
                if ($field = db_fetch_array($field_query)) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $item);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $item,
                        'is_export' => true,
                        'is_print' => true,
                        'path' => $current_path
                    ];

                    $output_padding = '';
                    if ($field['is_heading']) {
                        $output_padding = str_repeat(' - ', $level);
                    }

                    if (in_array($field['type'], ['fieldtype_textarea_wysiwyg', 'fieldtype_barcode', 'fieldtype_qrcode']
                    )) {
                        $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . $output_padding . trim(
                                fields_types::output($output_options)
                            ) . '</td>';
                    } elseif (in_array(
                        $field['type'],
                        [
                            'fieldtype_months_difference',
                            'fieldtype_years_difference',
                            'fieldtype_hours_difference',
                            'fieldtype_days_difference',
                            'fieldtype_mysql_query',
                            'fieldtype_input_numeric',
                            'fieldtype_formula',
                            'fieldtype_js_formula',
                            'fieldtype_input_numeric_comments'
                        ]
                    )) {
                        $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px; text-align: right;">' . $output_padding . trim(
                                strip_tags(fields_types::output($output_options))
                            ) . '</td>';
                    } else {
                        $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . $output_padding . trim(
                                strip_tags(fields_types::output($output_options))
                            ) . '</td>';
                    }
                }
            }

            $html .= '</tr>';

            $html = self::output_entities_nested_items_list($entities_id, $item['id'], $fields_list, $html, $level + 1);
        }

        return $html;
    }

    static function check_external_images($html, $templates_id)
    {
        if (!strlen($html)) {
            return false;
        }

        preg_match_all('/<img[^>]+>/i', $html, $result);

        foreach ($result[0] as $element) {
            preg_match('/src=("[^"]*")/i', $element, $src);

            $src = str_replace('"', '', $src[1]);

            //print_rr($src);
            //exit();

            if (substr($src, 0, 4) == 'http') {
                $file = attachments::prepare_image_filename(time() . '_' . pathinfo($src, PATHINFO_BASENAME));

                //check if file xeist
                $original_filename = pathinfo($src, PATHINFO_FILENAME);
                $fileext = pathinfo($src, PATHINFO_EXTENSION);
                $filename = $original_filename . '.' . $fileext;
                $counter = 2;
                while (file_exists(DIR_WS_IMAGES . $file['folder'] . '/' . $filename)) {
                    $filename = $original_filename . '(' . $counter . ').' . $fileext;
                    $counter++;
                };

                $filename = DIR_WS_IMAGES . $file['folder'] . '/' . $filename;

                //echo $filename;

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $src);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $data = curl_exec($curl);
                curl_close($curl);

                file_put_contents($filename, $data);

                $html = str_replace($src, $filename, $html);

                db_query(
                    "update app_ext_export_templates set description='" . db_input(
                        $html
                    ) . "' where id='" . $templates_id . "'"
                );
            }
        }

        return $html;
    }

    static function get_template_extra($selected_items, $template_info, $type)
    {
        global $app_entities_cache;

        $check_query = db_query(
            "select parent_item_id from app_entity_" . $template_info['entities_id'] . " where id='" . current(
                $selected_items
            ) . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            $parent_item_id = $check['parent_item_id'];
        } else {
            $parent_item_id = 0;
        }

        if ($parent_item_id > 0) {
            $parent_entity_id = $app_entities_cache[$template_info['entities_id']]['parent_id'];

            return export_templates::get_html($parent_entity_id, $parent_item_id, $template_info['id'], $type);
        } else {
            $export_template = $template_info[$type];

            //hande current dates
            $export_template = str_replace('{#current_date}', format_date(time()), $export_template);
            $export_template = str_replace('{#current_date_time}', format_date_time(time()), $export_template);

            return $export_template;
        }
    }

    static function get_html($entities_id, $items_id, $templates_id, $template_field = 'description')
    {
        global $app_user, $current_path, $app_num2str, $app_fields_cache, $parent_items_holder;


        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($entities_id, '');

        $item_query = db_query(
            "select e.*  " . $listing_sql_query_select . " from app_entity_" . $entities_id . " e where e.id='" . $items_id . "'"
        );
        if ($item = db_fetch_array($item_query)) {
            $templates_query = db_query(
                "select * from app_ext_export_templates where id='" . db_input($templates_id) . "'"
            );
            if ($templates = db_fetch_array($templates_query)) {
                $export_template = self::check_external_images($templates[$template_field], $templates['id']);

                //hande current dates
                $export_template = str_replace('{#current_date}', format_date(time()), $export_template);
                $export_template = str_replace('{#current_date_time}', format_date_time(time()), $export_template);

                //num2str
                $export_template = $app_num2str->prepare($export_template, $item);

                //search fields
                if (preg_match_all('/{#(\w+):[^}]*}/', $export_template, $matches)) {
                    //echo '<pre>';
                    //print_r($matches);
                    //prepare parent items
                    $parent_items_holder = [];
                    $parent_items_holder[$entities_id] = ['items_id' => $items_id, 'item' => $item];

                    $parent_item_id = $item['parent_item_id'];

                    $entities_list = [];
                    $entities_list[] = $entities_id;

                    foreach (entities::get_parents($entities_id) as $entity_id) {
                        $entities_list[] = $entity_id;

                        $parent_item_query = db_query(
                            "select e.*  " . fieldtype_formula::prepare_query_select(
                                $entity_id,
                                ''
                            ) . " from app_entity_" . $entity_id . " e where e.id='" . $parent_item_id . "'"
                        );
                        $parent_item = db_fetch_array($parent_item_query);

                        $parent_items_holder[$entity_id] = ['items_id' => $parent_item['id'], 'item' => $parent_item];

                        $parent_item_id = $parent_item['parent_item_id'];
                    }

                    //print_rr($parent_items_holder);
                    //check fields
                    foreach ($matches[1] as $matches_key => $fields_id) {
                        //handle parents
                        if (count($entities_list) > 1) {
                            foreach ($entities_list as $entity_id) {
                                if (isset($app_fields_cache[$entity_id][$fields_id])) {
                                    $entities_id = $entity_id;
                                    $items_id = $parent_items_holder[$entity_id]['items_id'];
                                    $item = $parent_items_holder[$entity_id]['item'];
                                }
                            }
                        }


                        if (strstr($fields_id, 'comments')) {
                            $output = self::output_comments_list($entities_id, $items_id, $matches[0][$matches_key]);

                            $export_template = str_replace($matches[0][$matches_key], $output, $export_template);
                        } elseif (strstr($fields_id, 'entity_tree')) {
                            $output = self::output_entities_items_list(
                                $entities_id,
                                $items_id,
                                str_replace('entity_tree', '', $fields_id),
                                $matches[0][$matches_key],
                                true
                            );

                            $export_template = str_replace($matches[0][$matches_key], $output, $export_template);
                        } elseif (strstr($fields_id, 'entity')) {
                            $output = self::output_entities_items_list(
                                $entities_id,
                                $items_id,
                                str_replace('entity', '', $fields_id),
                                $matches[0][$matches_key]
                            );

                            $export_template = str_replace($matches[0][$matches_key], $output, $export_template);
                        } else {
                            $field_query = db_query(
                                "select f.* from app_fields f where f.type not in ('fieldtype_action') and (f.id ='" . db_input(
                                    (int)$fields_id
                                ) . "' or type='fieldtype_" . db_input(
                                    $fields_id
                                ) . "') and  f.entities_id='" . db_input($entities_id) . "'",
                                false
                            );
                            if ($field = db_fetch_array($field_query)) {
                                //prepare field value
                                $value = items::prepare_field_value_by_type($field, $item);

                                $output_options = [
                                    'class' => $field['type'],
                                    'value' => $value,
                                    'field' => $field,
                                    'item' => $item,
                                    'is_export' => true,
                                    'is_print' => true,
                                    'path' => $current_path
                                ];

                                //print_rr($output_options);

                                if (in_array(
                                    $field['type'],
                                    [
                                        'fieldtype_php_code',
                                        'fieldtype_items_by_query',
                                        'fieldtype_mysql_query',
                                        'fieldtype_formula',
                                        'fieldtype_todo_list',
                                        'fieldtype_users_approve',
                                        'fieldtype_signature',
                                        'fieldtype_textarea_wysiwyg',
                                        'fieldtype_barcode',
                                        'fieldtype_qrcode',
                                        'fieldtype_text_pattern',
                                        'fieldtype_text_pattern_static'
                                    ]
                                )) {
                                    $output = trim(fields_types::output($output_options));
                                } elseif (in_array($field['type'], ['fieldtype_dropdown_multilevel'])) {
                                    $output = fieldtype_dropdown_multilevel::output_export_template_value(
                                        $fields_id,
                                        $output_options
                                    );
                                } elseif (in_array(
                                    $field['type'],
                                    ['fieldtype_entity', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']
                                )) {
                                    $cfg = new fields_types_cfg($field['configuration']);

                                    $output = self::prepare_output_for_entities(
                                        $cfg->get('entity_id'),
                                        $value,
                                        $matches[0][$matches_key]
                                    );
                                } elseif (in_array($field['type'], ['fieldtype_related_records'])) {
                                    $cfg = new fields_types_cfg($field['configuration']);

                                    $reladed_records = new related_records($entities_id, $item['id']);
                                    $reladed_records->set_related_field($field['id']);
                                    $related_items = $reladed_records->get_related_items();

                                    $value = implode(',', $related_items);

                                    $output = self::prepare_output_for_entities(
                                        $cfg->get('entity_id'),
                                        $value,
                                        $matches[0][$matches_key]
                                    );
                                } elseif (in_array(
                                    $field['type'],
                                    ['fieldtype_attachments', 'fieldtype_image', 'fieldtype_image_ajax']
                                )) {
                                    $cfg = new fields_types_cfg($field['configuration']);

                                    $output = self::prepare_output_for_images($value, $matches[0][$matches_key], $cfg);
                                } elseif (in_array(
                                    $field['type'],
                                    ['fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_created_by']
                                )) {
                                    $output = self::prepare_output_for_entities(1, $value, $matches[0][$matches_key]);
                                } elseif (in_array($field['type'], ['fieldtype_parent_item_id'])) {
                                    $output = $value;
                                } else {
                                    $output = trim(strip_tags(fields_types::output($output_options)));
                                }

                                //echo '<br>' . $fields_id . ' ' . $output . ' ' . $matches[0][$matches_key];  

                                $export_template = str_replace($matches[0][$matches_key], $output, $export_template);
                            }
                        }
                    }
                }

                //name2case
                $export_template = self::name2case($export_template);

                //conditions
                $export_template = self::apply_conditions($export_template, $parent_items_holder);

                return '<div class="export-template">' . $export_template . '</div>';
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    static function apply_conditions($html, $items_holder)
    {
        if (!strlen($html)) {
            return $html;
        }

        $item = [];

        if (is_array($items_holder)) {
            foreach ($items_holder as $v) {
                $item = array_merge($item, $v['item']);
            }
        }

        //print_rr($item);

        if (preg_match_all('/({{if[^:]+:}})[^{{]+{{endif}}/', $html, $matches)) {
            //print_rr($matches);

            foreach ($matches[1] as $matches_key => $condition) {
                //prepare fields values in condition
                foreach ($item as $k => $v) {
                    if (strstr($k, 'field_')) {
                        $k = str_replace('field_', '', $k);
                        $value = !is_numeric($v) ? "'" . $v . "'" : $v;
                        $condition = str_replace('[' . $k . ']', $value, $condition);
                    }
                }

                //prepare condition php code
                $condition = str_replace(['{{if', ':}}'], '', $condition);

                $condition = str_replace(['&lt;', '&gt;', '&#39;', '&quot;'], ['<', '>', "'", '"'], $condition);

                $php_code = ' $condition = (' . $condition . ' ? true:false);';

                //echo $php_code;

                //eval code
                try {
                    eval($php_code);
                } catch (Error $e) {
                    echo alert_error(
                        TEXT_ERROR . ' ' . $e->getMessage() . ' on line ' . $e->getLine() . '<br>' . $php_code
                    );
                }

                //echo $condition;

                //remove code if condition return false
                if (!$condition) {
                    $html = str_replace($matches[0][$matches_key], '', $html);
                } else {
                    //remove commands
                    $html = str_replace([$matches[1][$matches_key] . '<br />', $matches[1][$matches_key]], '', $html);
                }
            }

            //remove {{endif}} at the end to keep html blocks
            $html = str_replace(['{{endif}}<br />', '{{endif}}'], '', $html);
        }

        return $html;
    }

    static function prepare_output_for_images($value, $pattern, $cfg)
    {
        global $app_path, $app_module_action;

        if (strlen($value) == 0) {
            return '';
        }

        $count_images = 0;

        $is_pdf = in_array($app_module_action, ['export', 'run', 'save']);

        $html = '';
        foreach (explode(',', $value) as $filename) {
            $file = attachments::parse_filename($filename);

            if ($file['is_image']) {
                $count_images++;

                $width = (strlen($cfg->get('width')) ? $cfg->get('width') : 250);
                $height = false;

                if (preg_match("/\[(.+)\]/", $pattern, $matches)) {
                    if (strstr($matches[1], ',')) {
                        $v = explode(',', $matches[1]);
                        $width = $v[0];
                        $height = $v[1];
                    } else {
                        $width = $matches[1];
                    }
                }

                $img_params = ($width ? 'width="' . $width . '"' : '') . ' ' . ($height ? 'height="' . $height . '"' : '') . '';

                if ($is_pdf) {
                    $html .= '<img src="' . $file['file_path'] . '" ' . $img_params . ' vspace="0" hspace="0" style="margin-right: 10px; margin-bottom: 10px;">';
                } else {
                    $html .= '<li style="display: inline; padding:0 10px 0 0;"><img style="margin-bottom: 10px;" src="' . url_for(
                            'items/info&path=' . $app_path,
                            '&action=download_attachment&preview=1&file=' . urlencode(base64_encode($filename))
                        ) . '" ' . $img_params . '></li>';
                }
            }
        }

        if ($is_pdf and $count_images > 1) {
            $html = '<div style="height: 40px;">&nbsp;</div>' . $html;
        } else {
            $html = '<ul style="list-style:none; margin: 0; padding:0;" class="list-inline">' . $html . '</ul>';
        }

        return $html;
    }

    static function prepare_output_for_entities($entity_id, $value, $pattern)
    {
        global $current_path, $app_fields_cache;

        if (strlen($value) == 0) {
            return '';
        }

        //prepare pattern <...>
        $pattern = str_replace(['&lt;', '&gt;'], ['<', '>'], $pattern);
        if (preg_match("/<(.+)>/", $pattern)) {
            return self::prepare_output_table_for_entities($entity_id, $value, $pattern);
        }

        $output = [];

        $items_info_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $entity_id,
                ''
            ) . " from app_entity_" . $entity_id . " e where e.id in (" . $value . ")";
        $items_query = db_query($items_info_sql);
        while ($item = db_fetch_array($items_query)) {
            if (preg_match("/\[(.+)\]/", $pattern, $matches)) {
                $name = [];

                //check if non numeric entered
                $mathces_ids = array_map('trim', explode(',', $matches[1]));

                //check if first field is image then use only this field to select
                if (in_array(
                    $app_fields_cache[$entity_id][$mathces_ids[0]]['type'],
                    ['fieldtype_attachments', 'fieldtype_image', 'fieldtype_image_ajax']
                )) {
                    $matches[1] = $mathces_ids[0];
                }

                foreach ($mathces_ids as $k => $v) {
                    if (!is_numeric($v)) {
                        echo '<span class="alert alert-danger">' . TEXT_ERROR . ' ' . $pattern . '</span> ';
                        unset($mathces_ids[$k]);
                    }
                }

                //Skip if none fields mathced
                if (count($mathces_ids) == 0) {
                    continue;
                }

                $fields_query = db_query(
                    "select f.* from app_fields f where f.type not in ('fieldtype_action') and f.id in (" . $matches[1] . ") and  f.entities_id='" . db_input(
                        $entity_id
                    ) . "' order by field(f.id," . $matches[1] . ")"
                );
                while ($field = db_fetch_array($fields_query)) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $item);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $item,
                        'is_export' => true,
                        'is_print' => true,
                        'path' => $current_path
                    ];

                    if (in_array($field['type'], ['fieldtype_attachments', 'fieldtype_image', 'fieldtype_image_ajax']
                    )) {
                        $cfg = new fields_types_cfg($field['configuration']);

                        $output_settings = (isset($mathces_ids[1]) ? '[' . $mathces_ids[1] . (isset($mathces_ids[2]) ? ', ' . $mathces_ids[2] : '') . ']' : '');

                        $output = self::prepare_output_for_images($value, $output_settings, $cfg);

                        return $output;
                    } else {
                        $name[] = trim(strip_tags(fields_types::output($output_options)));
                    }
                }

                $name = implode(', ', $name);
            } else {
                $name = items::get_heading_field($entity_id, $item['id']);
            }

            $output[] = $name;
        }
        return implode(', ', $output);
    }

    static function prepare_output_table_for_entities($entity_id, $value, $pattern)
    {
        global $current_path;

        preg_match("/<(.+)>/", $pattern, $matches);

        $fields_list = explode(',', $matches[1]);

        $html = '';

        if (count($fields_list)) {
            $html = '<table border="1" width="100%" class="export-table export-table-' . $entity_id . '"><tr>';

            foreach ($fields_list as $id) {
                $field_query = db_query("select * from app_fields where id='" . $id . "'");
                if ($field = db_fetch_array($field_query)) {
                    $html .= '<th class="export-table-th-' . $field['id'] . '" style="padding: 1px 5px; text-align: left;">' . (strlen(
                            $field['short_name']
                        ) ? $field['short_name'] : fields_types::get_option(
                            $field['type'],
                            'name',
                            $field['name']
                        )) . '</th>';
                }
            }

            $html .= '</tr>';

            $items_info_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . $value . ")";
            $items_query = db_query($items_info_sql);
            while ($item = db_fetch_array($items_query)) {
                $html .= '<tr>';
                foreach ($fields_list as $id) {
                    $field_query = db_query("select * from app_fields where id='" . $id . "'");
                    if ($field = db_fetch_array($field_query)) {
                        //prepare field value
                        $value = items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'path' => $current_path
                        ];

                        if (in_array(
                            $field['type'],
                            ['fieldtype_textarea_wysiwyg', 'fieldtype_barcode', 'fieldtype_qrcode']
                        )) {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . trim(
                                    fields_types::output($output_options)
                                ) . '</td>';
                        } elseif (in_array(
                            $field['type'],
                            [
                                'fieldtype_months_difference',
                                'fieldtype_years_difference',
                                'fieldtype_hours_difference',
                                'fieldtype_days_difference',
                                'fieldtype_mysql_query',
                                'fieldtype_input_numeric',
                                'fieldtype_formula',
                                'fieldtype_js_formula',
                                'fieldtype_input_numeric_comments'
                            ]
                        )) {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px; text-align: right;">' . trim(
                                    (fields_types::output($output_options))
                                ) . '</td>';
                        } else {
                            $html .= '<td class="export-table-td-' . $field['id'] . '" style="padding: 1px 5px;">' . trim(
                                    strip_tags(fields_types::output($output_options))
                                ) . '</td>';
                        }
                    }
                }

                $html .= '</tr>';
            }

            $html .= '</table>';
        }

        return $html;
    }

    static function force_print_template()
    {
        global $app_force_print_template;

        if ($app_force_print_template == false) {
            return '';
        }

        $force_print_template = explode('_', $app_force_print_template);
        $template_id = (int)$force_print_template[0];
        $force_print_type = $force_print_template[1];
        $entity_id = (int)$force_print_template[2];
        $item_id = (int)$force_print_template[3];

        //reset
        $app_force_print_template = false;

        //echo $app_force_print_template;

        $html = '';
        $template_info_query = db_query(
            "select id,name,template_filename,type from app_ext_export_templates where id={$template_id} and entities_id={$entity_id}"
        );
        if ($template_info = db_fetch_array($template_info_query)) {
            if (strlen($template_info['template_filename'])) {
                $item = items::get_info($entity_id, $item_id);

                $pattern = new fieldtype_text_pattern;
                $filename = $pattern->output_singe_text($template_info['template_filename'], $entity_id, $item);
            } else {
                $filename = $template_info['name'] . '_' . $entity_id;
            }

            //prepare form target (note: popup blocked by browser by default)
            $target = ($force_print_type == 'printPopup') ? '_new' : '_self';

            switch (true) {
                case ($template_info['type'] == 'docx' and $force_print_type == 'pdf'):
                    $action = 'export_pdf';
                    break;
                case ($template_info['type'] != 'docx' and $force_print_type == 'pdf'):
                    $action = 'export';
                    break;
                case ($template_info['type'] == 'docx' and $force_print_type == 'docx'):
                    $action = 'export';
                    break;
                case ($template_info['type'] != 'docx' and $force_print_type == 'docx'):
                    $action = 'export_word';
                    break;
                default:
                    $action = 'print';
                    break;
            }

            $html = form_tag(
                    'print_template',
                    url_for(
                        'items/export_template',
                        'path=' . $entity_id . '-' . $item_id . '&templates_id=' . $template_info['id']
                    ),
                    ['target' => $target]
                ) .
                input_hidden_tag('action', $action) . input_hidden_tag('filename', $filename) . '</form>';

            $html .= '
                <script>
                $(function(){
                    $("#print_template").submit();                    
                })
                </script>    
                ';
        }

        return $html;
    }

    static function name2case($text)
    {
        if (preg_match_all('/name2case\(([^,]*),(\d)\)/', $text, $matches)) {
            //print_rr($matches);

            foreach ($matches[1] as $key => $value) {
                $text = str_replace($matches[0][$key], app_name2case($value, $matches[2][$key]), $text);
            }
        }

        if (preg_match_all('/name2case_ua\(([^,]*),(\d)\)/', $text, $matches)) {
            //print_rr($matches);

            foreach ($matches[1] as $key => $value) {
                $text = str_replace($matches[0][$key], app_name2case_ua($value, $matches[2][$key]), $text);
            }
        }
        return $text;
    }

    static function has_button($type, $templagte)
    {
        return (!strlen($templagte['save_as']) or strstr($templagte['save_as'], $type)) ? true : false;
    }

}
