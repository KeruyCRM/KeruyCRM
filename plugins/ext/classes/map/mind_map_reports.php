<?php

class mind_map_reports
{
    public $reports, $path, $parent_entity_item_id, $entities_id, $fields_in_popup;

    function __construct($reports, $path)
    {
        $this->reports = $reports;
        $this->reports_id = $reports['id'];
        $this->entities_id = $reports['entities_id'];
        $this->use_background = $reports['use_background'];
        $this->icons = strlen($reports['icons']) ? json_decode($reports['icons'], true) : [];
        $this->path = $path;
        $this->background_colors = [];
        $this->fields_in_popup = $reports['fields_in_popup'];

        $current_path_array = explode('/', $this->path);
        if (count($current_path_array) > 1) {
            $v = explode('-', $current_path_array[count($current_path_array) - 2]);
            $this->parent_entity_item_id = $v[1];
        } else {
            $this->parent_entity_item_id = 0;
        }

        if ($this->use_background) {
            $this->set_background_colors();
        }
    }


    function is_report()
    {
        return true;
    }

    function set_background_colors()
    {
        if (!$this->use_background) {
            return false;
        }

        $field_info_query = db_query("select * from app_fields where id='" . $this->use_background . "'");
        if (!$field_info = db_fetch_array($field_info_query)) {
            return false;
        }


        $cfg = new fields_types_cfg($field_info['configuration']);
        if ($cfg->get('use_global_list') > 0) {
            $choices_query = db_query(
                "select * from app_global_lists_choices where lists_id = '" . db_input(
                    $cfg->get('use_global_list')
                ) . "' and length(bg_color)>0"
            );
        } else {
            $choices_query = db_query(
                "select * from app_fields_choices where fields_id = '" . db_input(
                    $this->use_background
                ) . "' and length(bg_color)>0"
            );
        }

        while ($choices = db_fetch_array($choices_query)) {
            $this->background_colors[$choices['id']] = $choices['bg_color'];
        }
    }

    function render_legend()
    {
        $html = '';

        if ($this->use_background) {
            $field_info_query = db_query("select * from app_fields where id='" . $this->use_background . "'");
            if ($field_info = db_fetch_array($field_info_query)) {
                $cfg = new fields_types_cfg($field_info['configuration']);
                if ($cfg->get('use_global_list') > 0) {
                    $choices_query = db_query(
                        "select * from app_global_lists_choices where lists_id = '" . db_input(
                            $cfg->get('use_global_list')
                        ) . "'"
                    );
                } else {
                    $choices_query = db_query(
                        "select * from app_fields_choices where fields_id = '" . db_input($this->use_background) . "'"
                    );
                }

                $html = '<p><span>' . $field_info['name'] . '</span><table>';

                while ($choices = db_fetch_array($choices_query)) {
                    $html .= '
							<tr>
								<td style="width: 35px; ' . (strlen(
                            $choices['bg_color']
                        ) ? 'color: ' . $choices['bg_color'] : '') . '"><i class="fa ' . (strlen(
                            $this->icons[$choices['id']]
                        ) ? $this->icons[$choices['id']] : 'fa-angle-down') . '"></i></td>
								<td style="text-align: left; font-size: 11px; ' . (strlen(
                            $choices['bg_color']
                        ) ? 'color: ' . $choices['bg_color'] : '') . '">' . $choices['name'] . '</td>
							</tr>
							';
                    //$this->background_colors[$choices['id']] = $choices['bg_color'];
                }

                $html .= '</table></p>';
            }
        }

        return $html;
    }

    function save($data)
    {
        $this->save_root($data);

        $this->save_children($data);

        $this->prepare_deleted_items($data);
    }

    function prepare_deleted_items($data)
    {
        $chilider_list = $this->get_childer_list($data);

        $mm_query = db_query(
            "select id, mm_items_id from app_mind_map where " . (count($chilider_list) ? " mm_id not in (" . implode(
                    ',',
                    $chilider_list
                ) . ") and  length(mm_parent_id)>0 and " : '') . " entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "'"
        );
        while ($mm = db_fetch_array($mm_query)) {
            db_query("delete from app_mind_map where id='" . $mm['id'] . "'");

            if ($mm['mm_items_id'] > 0) {
                items::delete($this->entities_id, $mm['mm_items_id']);
            }
        }
    }

    function get_childer_list($data, $chilider_list = [])
    {
        if (isset($data['children'])) {
            foreach ($data['children'] as $children) {
                $chilider_list[] = "'" . $children['id'] . "'";

                $chilider_list = $this->get_childer_list($children, $chilider_list);
            }
        }

        return $chilider_list;
    }

    function save_root($data)
    {
        $sql_data = [
            'mm_id' => $data['id'],
            'mm_text' => strip_tags($data['text']),
            'mm_layout' => $data['layout'],
            'mm_shape' => (isset($data['shape']) ? $data['shape'] : ''),
        ];

        $mm_query = db_query(
            "select id from app_mind_map where mm_id='" . $data['id'] . "' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "'"
        );
        if ($mm = db_fetch_array($mm_query)) {
            db_perform('app_mind_map', $sql_data, 'update', "id='" . $mm['id'] . "'");
        } else {
            $sql_data['entities_id'] = $this->entities_id;
            $sql_data['reports_id'] = $this->reports_id;
            $sql_data['parent_entity_item_id'] = $this->parent_entity_item_id;

            db_perform('app_mind_map', $sql_data);
        }
    }

    function save_children($data)
    {
        if (isset($data['children'])) {
            $sort_order = 0;
            foreach ($data['children'] as $children) {
                $sql_data = [
                    'mm_id' => $children['id'],
                    'mm_parent_id' => $data['id'],
                    'mm_text' => strip_tags($children['text']),
                    'mm_layout' => (isset($children['layout']) ? $children['layout'] : ''),
                    'mm_shape' => (isset($children['shape']) ? $children['shape'] : ''),
                    'mm_side' => (isset($children['side']) ? $children['side'] : ''),
                    'mm_collapsed' => (isset($children['collapsed']) ? $children['collapsed'] : ''),
                    'sort_order' => $sort_order,
                ];

                $mm_query = db_query(
                    "select id,mm_items_id from app_mind_map where mm_id='" . $children['id'] . "' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "'"
                );
                if ($mm = db_fetch_array($mm_query)) {
                    db_perform('app_mind_map', $sql_data, 'update', "id='" . $mm['id'] . "'");

                    $this->update_item($mm['mm_items_id'], $children['text']);
                } else {
                    $sql_data['entities_id'] = $this->entities_id;
                    $sql_data['reports_id'] = $this->reports_id;
                    $sql_data['parent_entity_item_id'] = $this->parent_entity_item_id;

                    db_perform('app_mind_map', $sql_data);
                    $mind_map_id = db_insert_id();

                    $this->save_item($mind_map_id, $children['text']);
                }

                $sort_order++;

                $this->save_children($children);
            }
        }
    }

    function update_item($item_id, $name)
    {
        $heading_field_id = fields::get_heading_id($this->entities_id);

        if ($heading_field_id) {
            db_query(
                "update app_entity_" . $this->entities_id . " set field_" . $heading_field_id . " = '" . db_input(
                    strip_tags($name)
                ) . "' where id='" . $item_id . "'"
            );
        }
    }

    function save_item($mind_map_id, $name)
    {
        global $app_user;

        $heading_field_id = fields::get_heading_id($this->entities_id);

        $sql_data = [
            'field_' . $heading_field_id => strip_tags($name),
            'date_added' => time(),
            'created_by' => $app_user['id'],
        ];

        //set parent
        if ($this->parent_entity_item_id) {
            $sql_data['parent_item_id'] = $this->parent_entity_item_id;
        }

        //prepare choices with default value
        $fields_query = db_query(
            "select f.* from app_fields f where f.type not in (" . fields_types::get_reserved_types_list(
            ) . ",'fieldtype_related_records') and  f.entities_id='" . db_input(
                $this->entities_id
            ) . "' order by f.sort_order, f.name"
        );
        while ($field = db_fetch_array($fields_query)) {
            if (in_array($field['type'], fields_types::get_types_wich_choices())) {
                $cfg = new fields_types_cfg($field['configuration']);

                if ($cfg->get('use_global_list') > 0) {
                    $check_query = db_query(
                        "select id from app_global_lists_choices where lists_id = '" . db_input(
                            $cfg->get('use_global_list')
                        ) . "' and is_default=1"
                    );
                } else {
                    $check_query = db_query(
                        "select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1"
                    );
                }

                if ($check = db_fetch_array($check_query)) {
                    $sql_data['field_' . $field['id']] = $check['id'];
                }
            }
        }

        db_perform('app_entity_' . $this->entities_id, $sql_data);
        $item_id = db_insert_id();

        //atuoset fieldtype autostatus
        fieldtype_autostatus::set($this->entities_id, $item_id);

        db_query("update app_mind_map set mm_items_id='" . $item_id . "' where id='" . $mind_map_id . "'");

        return $item_id;
    }


    function prepare_new_items()
    {
        //prepare root
        $mm_query = db_query(
            "select * from app_mind_map where mm_parent_id='' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "'"
        );
        if (!$mm = db_fetch_array($mm_query)) {
            $sql_data = [
                'mm_id' => 'root',
                'mm_text' => TEXT_START,
                'mm_layout' => 'map',
                'entities_id' => $this->entities_id,
                'reports_id' => $this->reports_id,
                'parent_entity_item_id' => $this->parent_entity_item_id
            ];

            db_perform('app_mind_map', $sql_data);
        }

        //prepare new items
        $itesm_query = db_query(
            "select e.* from app_entity_" . $this->entities_id . " e where e.id not in (select mm.mm_items_id from app_mind_map mm where mm.entities_id='" . $this->entities_id . "' and mm.reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "') " . ($this->parent_entity_item_id ? " and e.parent_item_id='" . $this->parent_entity_item_id . "'" : "")
        );
        while ($itesm = db_fetch_array($itesm_query)) {
            $sort_order_query = db_query(
                "select max(sort_order) as max_sort_order from app_mind_map where mm_parent_id='root' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "'"
            );
            $sort_order = db_fetch_array($sort_order_query);
            $sort_order = $sort_order['max_sort_order'] + 1;

            $sql_data = [
                'mm_id' => $itesm['id'],
                'mm_items_id' => $itesm['id'],
                'mm_text' => items::get_heading_field($this->entities_id, $itesm['id'], $itesm),
                'mm_parent_id' => 'root',
                'mm_color' => '#999',
                'entities_id' => $this->entities_id,
                'reports_id' => $this->reports_id,
                'parent_entity_item_id' => $this->parent_entity_item_id,
                'sort_order' => $sort_order,
            ];

            db_perform('app_mind_map', $sql_data);
        }
    }

    function get_json()
    {
        $this->prepare_new_items();

        if (count($tree = $this->get_tree())) {
            return json_encode($tree, JSON_NUMERIC_CHECK);
        } else {
            return '';
        }
    }

    function get_tree($data = [], $mm_parent_id = '')
    {
        global $app_user;

        $count = 0;

        $mm_query = db_query(
            "select * from app_mind_map where mm_parent_id='" . $mm_parent_id . "' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "' order by sort_order"
        );
        while ($mm = db_fetch_array($mm_query)) {
            if (strlen($mm_parent_id)) {
                //check view only own access
                if (users::has_access('view_assigned') and $app_user['group_id'] > 0) {
                    if (!users::has_access_to_assigned_item($this->entities_id, $mm['mm_items_id'])) {
                        continue;
                    }
                }

                $item_info = db_find('app_entity_' . $this->entities_id, $mm['mm_items_id']);

                $data[$count] = [
                    'id' => $mm['mm_id'],
                    'text' => str_replace(["'", '"'], ['&apos;', '&quot;'], $mm['mm_text']),
                    'color' => $this->set_color($item_info),
                    'icon' => $this->set_icon($item_info),
                    'popup' => $this->get_popup($item_info),
                ];

                if (strlen($mm['mm_layout'])) {
                    $data[$count]['layout'] = $mm['mm_layout'];
                }

                if (strlen($mm['mm_shape'])) {
                    $data[$count]['shape'] = $mm['mm_shape'];
                }

                if (strlen($mm['mm_side'])) {
                    $data[$count]['side'] = $mm['mm_side'];
                }

                if (strlen($mm['mm_collapsed'])) {
                    $data[$count]['collapsed'] = $mm['mm_collapsed'];
                }

                if (strlen($mm['mm_value'])) {
                    $data[$count]['value'] = $mm['mm_value'];
                }

                if (strlen($mm['mm_items_id'] > 0)) {
                    $data[$count]['items_id'] = $mm['mm_items_id'];
                }

                $check_query = db_query(
                    "select id from app_mind_map where mm_parent_id='" . $mm['mm_id'] . "' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "' limit 1"
                );
                if ($check = db_fetch_array($check_query)) {
                    $data[$count]['children'] = $this->get_tree([], $mm['mm_id']);
                }

                $count++;
            } else {
                $data['root'] = [
                    'id' => $mm['mm_id'],
                    'text' => ($mm['mm_text']),
                    'layout' => $mm['mm_layout'],
                ];

                if (strlen($mm['mm_shape'])) {
                    $data['root']['shape'] = $mm['mm_shape'];
                }

                if (strlen($mm['mm_icon'])) {
                    $data['root']['icon'] = $mm['mm_icon'];
                }

                if (strlen($mm['mm_color'])) {
                    $data['root']['color'] = $mm['mm_color'];
                }

                $check_query = db_query(
                    "select id from app_mind_map where mm_parent_id='" . $mm['mm_id'] . "' and entities_id='" . $this->entities_id . "' and reports_id='" . $this->reports_id . "' and parent_entity_item_id = '" . $this->parent_entity_item_id . "' limit 1"
                );
                if ($check = db_fetch_array($check_query)) {
                    $data['root']['children'] = $this->get_tree([], $mm['mm_id']);
                }
            }
        }

        return $data;
    }

    function set_icon($item_info)
    {
        if ($this->use_background) {
            if (strlen($value = $item_info['field_' . $this->use_background])) {
                if (isset($this->icons[$value])) {
                    return strlen($this->icons[$value]) ? $this->icons[$value] : 'fa-angle-down';
                }
            }
        }

        return 'fa-angle-down';
    }

    function set_color($item_info)
    {
        if ($this->use_background) {
            if (strlen($value = $item_info['field_' . $this->use_background])) {
                if (isset($this->background_colors[$value])) {
                    return $this->background_colors[$value];
                }
            }
        }

        return '#999';
    }


    function get_popup($items)
    {
        $html = '';

        if (strlen($this->fields_in_popup)) {
            $html .= '
					<table >
						<tbody>';


            foreach (explode(',', $this->fields_in_popup) as $fields_id) {
                $field_query = db_query("select * from app_fields where id='" . $fields_id . "'");
                if ($field = db_fetch_array($field_query)) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $items);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $items,
                        'is_listing' => true,
                        'path' => ''
                    ];

                    $value = trim(db_prepare_html_input(fields_types::output($output_options)));

                    if (strlen(strip_tags($value)) > 255 and in_array(
                            $field['type'],
                            ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea']
                        )) {
                        $value = htmlspecialchars(substr(strip_tags($value), 0, 255)) . '...';
                    }

                    if (strlen($value)) {
                        $html .= '
							<tr>
								<th>' . fields_types::get_option($field['type'], 'name', $field['name']) . '</th>
								<td>' . $value . '</td>
							</tr>';
                    }
                }
            }

            $html .= '
						</tbody>
					</table>
					';
        }

        return str_replace(["'", '"'], ['&apos;', '&quot;'], str_replace(["\n", "\r", "\n\r", "\t"], '', $html));
    }

    function is_editable()
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return 1;
        } else {
            if (self::has_access($this->reports['users_groups'], 'full')) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    static function has_access($users_groups, $access = false)
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return true;
        }

        if (strlen($users_groups)) {
            $users_groups = json_decode($users_groups, true);

            if (!$access) {
                if (isset($users_groups[$app_user['group_id']])) {
                    return (strlen($users_groups[$app_user['group_id']]) ? true : false);
                }
            } else {
                if (isset($users_groups[$app_user['group_id']])) {
                    return ($users_groups[$app_user['group_id']] == $access ? true : false);
                }
            }
        }

        return false;
    }

    static function delete($entities_id, $items_id)
    {
        $mm_query = db_query(
            "select * from app_mind_map where entities_id='" . $entities_id . "' and mm_items_id='" . $items_id . "'"
        );
        if ($mm = db_fetch_array($mm_query)) {
            $mm_parent_query = db_query(
                "select * from app_mind_map where entities_id='" . $entities_id . "' and mm_parent_id='" . $mm['mm_id'] . "'"
            );
            if ($mm_parent = db_fetch_array($mm_parent_query)) {
                db_query(
                    "update app_mind_map set mm_parent_id='" . $mm['mm_parent_id'] . "' where id='" . $mm_parent['id'] . "'"
                );
            }

            db_delete_row('app_mind_map', $mm['id']);
        }
    }
}