<?php

class processes
{

    public $entities_id;
    public $rdirect_to;
    public $items_id;

    function __construct($entities_id)
    {
        $this->entities_id = $entities_id;
        $this->rdirect_to = 'items_info';
        $this->items_id = 0;
    }

    function run_after_insert($items_id)
    {
        $this->items_id = $items_id;

        foreach ($this->get_buttons_list('run_after_insert') as $process_info) {
            if ($this->check_buttons_filters($process_info)) {
                $_post_fields = $_POST['fields'] ?? []; //save post fields
                $_POST['fields'] = []; //reset post fields

                $this->run($process_info, false, true);

                $_POST['fields'] = $_post_fields; //restore post fields;
            }
        }
    }

    function run_after_update($items_id)
    {
        $this->items_id = $items_id;

        foreach ($this->get_buttons_list('run_after_update') as $process_info) {
            if ($this->check_buttons_filters($process_info)) {
                $_post_fields = $_POST['fields'] ?? []; //save post fields
                $_POST['fields'] = []; //reset post fields

                $this->run($process_info, false, true);

                $_POST['fields'] = $_post_fields; //restore post fields;
            }
        }
    }

    function run_before_delete($items_id)
    {
        $this->items_id = $items_id;

        foreach ($this->get_buttons_list('run_before_delete') as $process_info) {
            if ($this->check_buttons_filters($process_info)) {
                $this->run($process_info, false, true);
            }
        }
    }

    function button_has_warnign_text($button)
    {
        return strlen(trim(strip_tags($button['warning_text']))) ? true : false;
    }

    public function render_buttons($position, $reports_id = 0)
    {
        global $app_path;

        $buttons_list = $this->get_buttons_list($position);

        $html = '';

        switch ($position) {
            case 'in_listing':
                if (!strlen($app_path)) {
                    $reports_info = db_find('app_reports', $reports_id);
                    $app_path = $reports_info['entities_id'];
                }

                foreach ($buttons_list as $buttons) {
                    $params = '&reports_id=' . $reports_id;
                    $html .= button_tag(
                        $buttons['button_title'],
                        url_for(
                            'items/processes',
                            'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                        ),
                        true,
                        ['class' => 'btn btn-primary btn-process-' . $buttons['id']],
                        $buttons['button_icon']
                    );
                    $html .= $this->prepare_button_css($buttons);
                }

                $html .= $this->render_buttons_by_buttons_groups_in_listing_menu($reports_id);

                break;
            case 'default':

                foreach ($buttons_list as $buttons) {
                    if ($this->check_buttons_filters($buttons)) {
                        if (strlen($buttons['payment_modules'])) {
                            $html .= '<li>' . button_tag(
                                    $buttons['button_title'],
                                    url_for(
                                        'items/processes_checkout',
                                        'id=' . $buttons['id'] . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to
                                    ),
                                    true,
                                    ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id']],
                                    $buttons['button_icon']
                                ) . '</li>';
                        } else {
                            $is_dialog = ((strlen(
                                    $buttons['confirmation_text']
                                ) or $buttons['allow_comments'] == 1 or $buttons['preview_prcess_actions'] == 1 or $this->has_enter_manually_fields(
                                    $buttons['id']
                                )) ? true : false);
                            $params = (!$is_dialog ? '&action=run' : '');
                            $css = (!$is_dialog ? ' prevent-double-click' : '');
                            $html .= '<li>' . button_tag(
                                    $buttons['button_title'],
                                    url_for(
                                        'items/processes',
                                        'id=' . $buttons['id'] . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                                    ),
                                    $is_dialog,
                                    ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                                    $buttons['button_icon']
                                ) . '</li>';
                        }

                        $html .= $this->prepare_button_css($buttons);
                    } elseif ($this->button_has_warnign_text($buttons)) {
                        $html .= '<li>' . button_tag(
                                $buttons['button_title'],
                                url_for('items/processes_warning', 'id=' . $buttons['id'] . '&path=' . $app_path),
                                true,
                                ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                                $buttons['button_icon']
                            ) . '</li>';
                        $html .= $this->prepare_button_css($buttons);
                    }
                }

                $html .= $this->render_buttons_by_buttons_groups_default_menu();

                break;
            case 'menu_more_actions':
                foreach ($buttons_list as $buttons) {
                    $title = (strlen($buttons['button_icon']) ? app_render_icon(
                                $buttons['button_icon']
                            ) . ' ' : '') . $buttons['button_title'];
                    $style = (strlen($buttons['button_color']) ? 'color: ' . $buttons['button_color'] : '');

                    if ($this->check_buttons_filters($buttons)) {
                        if (strlen($buttons['payment_modules'])) {
                            $url = url_for(
                                'items/processes_checkout',
                                'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to
                            );
                            $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
                        } else {
                            $is_dialog = ((strlen(
                                    $buttons['confirmation_text']
                                ) or $buttons['allow_comments'] == 1 or $buttons['preview_prcess_actions'] == 1 or $this->has_enter_manually_fields(
                                    $buttons['id']
                                )) ? true : false);
                            $params = (!$is_dialog ? '&action=run' : '');
                            $url = url_for(
                                'items/processes',
                                'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                            );

                            if ($is_dialog) {
                                $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
                            } else {
                                $html .= '<li>' . link_to($title, $url, ['style' => $style]) . '</li>';
                            }
                        }
                    } elseif ($this->button_has_warnign_text($buttons)) {
                        $url = url_for('items/processes_warning', 'id=' . $buttons['id'] . '&path=' . $app_path);
                        $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
                    }
                }
                break;
            case 'menu_with_selected':
                if (!strlen($app_path)) {
                    $reports_info = db_find('app_reports', $reports_id);
                    $app_path = $reports_info['entities_id'];
                }

                foreach ($buttons_list as $buttons) {
                    if (!strlen($buttons['payment_modules'])) {
                        $title = (strlen($buttons['button_icon']) ? app_render_icon(
                                    $buttons['button_icon']
                                ) . ' ' : '') . $buttons['button_title'];
                        $params = '&reports_id=' . $reports_id;
                        $style = (strlen($buttons['button_color']) ? 'color: ' . $buttons['button_color'] : '');

                        $html .= '<li>' . link_to_modalbox(
                                $title,
                                url_for(
                                    'items/processes',
                                    'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                                ),
                                ['style' => $style]
                            ) . '</li>';
                    }
                }

                $html .= $this->render_buttons_by_buttons_groups_with_selected_menu($app_path, $reports_id);
                break;
            case 'comments_section':

                foreach ($buttons_list as $buttons) {
                    if ($this->check_buttons_filters($buttons)) {
                        if (strlen($buttons['payment_modules'])) {
                            $html .= button_tag(
                                $buttons['button_title'],
                                url_for(
                                    'items/processes_checkout',
                                    'id=' . $buttons['id'] . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to
                                ),
                                true,
                                ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id']],
                                $buttons['button_icon']
                            );
                        } else {
                            $is_dialog = ((strlen(
                                    $buttons['confirmation_text']
                                ) or $buttons['allow_comments'] == 1 or $buttons['preview_prcess_actions'] == 1 or $this->has_enter_manually_fields(
                                    $buttons['id']
                                )) ? true : false);
                            $params = (!$is_dialog ? '&action=run' : '');
                            $css = (!$is_dialog ? ' prevent-double-click' : '');
                            $html .= button_tag(
                                $buttons['button_title'],
                                url_for(
                                    'items/processes',
                                    'id=' . $buttons['id'] . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                                ),
                                $is_dialog,
                                ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                                $buttons['button_icon']
                            );
                        }
                        $html .= $this->prepare_button_css($buttons);
                    } elseif ($this->button_has_warnign_text($buttons)) {
                        $html .= button_tag(
                            $buttons['button_title'],
                            url_for('items/processes_warning', 'id=' . $buttons['id'] . '&path=' . $app_path),
                            true,
                            ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                            $buttons['button_icon']
                        );
                        $html .= $this->prepare_button_css($buttons);
                    }
                }
                break;
        }

        return $html;
    }

    public function render_buttons_by_buttons_groups_in_listing_menu($reports_id)
    {
        global $app_path;

        if (!strlen($app_path)) {
            $reports_info = db_find('app_reports', $reports_id);
            $app_path = $reports_info['entities_id'];
        }

        $buttons_html = '';
        $buttons_groups_query = db_query(
            "select * from app_ext_processes_buttons_groups where entities_id='" . $this->entities_id . "' and find_in_set('in_listing',button_position) order by sort_order, name"
        );
        while ($buttons_groups = db_fetch_array($buttons_groups_query)) {
            $html = '';

            $buttons_list = $this->get_buttons_list('buttons_groups_' . $buttons_groups['id']);

            foreach ($buttons_list as $buttons) {
                $title = app_render_icon($buttons['button_icon']) . $buttons['button_title'];
                $params = '&reports_id=' . $reports_id;
                $url = url_for(
                    'items/processes',
                    'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                );

                $style = (strlen($buttons['button_color']) ? 'color: ' . $buttons['button_color'] : '');

                $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
            }

            if (strlen($html)) {
                $buttons_html .= '
						<div class="btn-group">
							<button class="btn dropdown-toggle btn-primary btn-process-groups-' . $buttons_groups['id'] . '" type="button" data-toggle="dropdown" data-hover="dropdown" aria-expanded="false">
							' . (strlen($buttons_groups['button_icon']) ? app_render_icon(
                            $buttons_groups['button_icon']
                        ) . ' ' : '') . $buttons_groups['name'] . ' <i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu" role="menu">
								' . $html . '
							</ul>
						</div>
						';

                $buttons_html .= $this->prepare_button_css($buttons_groups, 'groups-');
            }
        }

        return $buttons_html;
    }

    public function render_buttons_by_buttons_groups_default_menu()
    {
        global $app_path;

        $buttons_html = '';
        $buttons_groups_query = db_query(
            "select * from app_ext_processes_buttons_groups where entities_id='" . $this->entities_id . "' and find_in_set('default',button_position) order by sort_order, name"
        );
        while ($buttons_groups = db_fetch_array($buttons_groups_query)) {
            $html = '';

            $buttons_list = $this->get_buttons_list('buttons_groups_' . $buttons_groups['id']);

            foreach ($buttons_list as $buttons) {
                if ($this->check_buttons_filters($buttons)) {
                    $title = app_render_icon($buttons['button_icon']) . $buttons['button_title'];
                    $is_dialog = ((strlen(
                            $buttons['confirmation_text']
                        ) or $buttons['allow_comments'] == 1 or $buttons['preview_prcess_actions'] == 1 or $this->has_enter_manually_fields(
                            $buttons['id']
                        )) ? true : false);
                    $params = (!$is_dialog ? '&action=run' : '');
                    $url = url_for(
                        'items/processes',
                        'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $app_path . '&redirect_to=' . $this->rdirect_to . $params
                    );

                    $style = (strlen($buttons['button_color']) ? 'color: ' . $buttons['button_color'] : '');

                    if ($is_dialog) {
                        $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
                    } else {
                        $html .= '<li>' . link_to($title, $url, ['style' => $style]) . '</li>';
                    }
                }
            }

            if (strlen($html)) {
                $buttons_html .= '
						<div class="btn-group">
							<button class="btn  btn-sm dropdown-toggle btn-primary btn-process-groups-' . $buttons_groups['id'] . '" type="button" data-toggle="dropdown" data-hover="dropdown" aria-expanded="false">
							' . (strlen($buttons_groups['button_icon']) ? app_render_icon(
                            $buttons_groups['button_icon']
                        ) . ' ' : '') . $buttons_groups['name'] . ' <i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu" role="menu">                                       
								' . $html . '									
							</ul>
						</div>
						';

                $buttons_html .= $this->prepare_button_css($buttons_groups, 'groups-');
            }
        }

        return $buttons_html;
    }

    public function render_buttons_by_buttons_groups_with_selected_menu($path, $reports_id)
    {
        global $app_path;

        $buttons_html = '';
        $buttons_groups_query = db_query(
            "select * from app_ext_processes_buttons_groups where entities_id='" . $this->entities_id . "' and find_in_set('menu_with_selected',button_position) order by sort_order, name"
        );
        while ($buttons_groups = db_fetch_array($buttons_groups_query)) {
            $html = '';

            $buttons_list = $this->get_buttons_list('buttons_groups_' . $buttons_groups['id']);

            foreach ($buttons_list as $buttons) {
                $title = app_render_icon($buttons['button_icon']) . $buttons['button_title'];
                $params = '&reports_id=' . $reports_id;
                $url = url_for(
                    'items/processes',
                    'id=' . $buttons['id'] . '&entity_id=' . $this->entities_id . '&path=' . $path . '&redirect_to=' . $this->rdirect_to . $params
                );

                $style = (strlen($buttons['button_color']) ? 'color: ' . $buttons['button_color'] : '');

                $html .= '<li>' . link_to_modalbox($title, $url, ['style' => $style]) . '</li>';
            }

            if (strlen($html)) {
                $buttons_html .= '
						<li class="dropdown-submenu">
							<a href="#" ' . (strlen(
                        $buttons_groups['button_color']
                    ) ? 'style="color: ' . $buttons_groups['button_color'] . '"' : '') . '>' . (strlen(
                        $buttons_groups['button_icon']
                    ) ? app_render_icon($buttons_groups['button_icon']) . ' ' : '') . $buttons_groups['name'] . '</a>
							<ul class="dropdown-menu">
									' . $html . '
							</ul>
						</li>
						';
            }
        }

        return $buttons_html;
    }

    public function has_enter_manually_fields($process_id)
    {
        $check_query = db_query(
            "select count(*) as total from app_ext_processes_actions_fields af where af.enter_manually in (1,2) and af.actions_id in (select pa.id from app_ext_processes_actions pa where pa.process_id='" . $process_id . "')"
        );
        $check = db_fetch_array($check_query);

        return (($check['total'] > 0 or $this->has_move_action($process_id) or $this->has_copy_action(
                $process_id
            ) or $this->has_clone_action_to_nested_entity($process_id)) ? true : false);
    }

    public function has_move_action($process_id)
    {
        $check_qeury = db_query(
            "select count(*) as total  from app_ext_processes_actions where process_id='" . $process_id . "' and locate('move_item_entity_',type)>0"
        );
        $check = db_fetch_array($check_qeury);

        return ($check['total'] > 0 ? true : false);
    }

    public function has_clone_action_to_nested_entity($process_id)
    {
        global $app_entities_cache;

        $actions_qeury = db_query(
            "select settings  from app_ext_processes_actions where process_id='" . $process_id . "' and locate('clone_item_entity_',type)>0"
        );
        while ($actions = db_fetch_array($actions_qeury)) {
            $settigns = new settings($actions['settings']);

            if (is_array($settigns->get('clone_to_entity'))) {
                if ($app_entities_cache[current($settigns->get('clone_to_entity'))]['parent_id'] > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function has_copy_action($process_id, $check_parent = true)
    {
        global $app_entities_cache;

        $check_qeury = db_query(
            "select count(*) as total  from app_ext_processes_actions where process_id='" . $process_id . "' and locate('copy_item_entity_',type)>0"
        );
        $check = db_fetch_array($check_qeury);

        if ($check_parent) {
            $process_query = db_query("select entities_id from app_ext_processes where id = '" . $process_id . "'");
            $process = db_fetch_array($process_query);

            return (($app_entities_cache[$process['entities_id']]['parent_id'] > 0 and $check['total'] > 0) ? true : false);
        } else {
            return ($check['total'] > 0 ? true : false);
        }
    }

    public function get_buttons_list($position = '', $buttons_id = '')
    {
        global $app_user, $app_fields_cache;

        $buttons_list = [];

        $buttons_query = db_query(
            "select *, if(length(button_title)>0,button_title,name) as button_title from app_ext_processes where " . (strlen(
                $position
            ) ? "find_in_set('" . $position . "',button_position) and " : '') . (strlen(
                $buttons_id
            ) ? ' id in (' . $buttons_id . ') and ' : '') . " entities_id='" . $this->entities_id . "' and is_active=1 order by sort_order, name"
        );
        while ($buttons = db_fetch_array($buttons_query)) {
            $has_access = false;

            //check access to assigned groups
            if (strlen($buttons['users_groups'])) {
                $has_access = in_array($app_user['group_id'], explode(',', $buttons['users_groups']));
            }

            //check access to assigned users
            if (strlen($buttons['assigned_to']) and !$has_access) {
                $has_access = in_array($app_user['id'], explode(',', $buttons['assigned_to']));
            }

            //check assess to assigned users in item
            if (strlen($buttons['access_to_assigned']) and $this->items_id > 0 and !$has_access) {
                $item_info_query = db_query(
                    "select e.* from app_entity_" . $this->entities_id . " e  where e.id='" . $this->items_id . "'"
                );
                if ($item_info = db_fetch_array($item_info_query)) {
                    foreach (explode(',', $buttons['access_to_assigned']) as $field_id) {
                        $field_info_query = db_query(
                            "select type, configuration from app_fields where id='" . $field_id . "'"
                        );
                        if ($field_info = db_fetch_array($field_info_query)) {
                            $cfg = new fields_types_cfg($field_info['configuration']);

                            switch ($field_info['type']) {
                                case 'fieldtype_grouped_users':
                                    if (strlen($item_info['field_' . $field_id])) {
                                        foreach (explode(',', $item_info['field_' . $field_id]) as $choices_id) {
                                            if ($cfg->get('use_global_list') > 0) {
                                                $choice_query = db_query(
                                                    "select * from app_global_lists_choices where id='" . db_input(
                                                        $choices_id
                                                    ) . "' and lists_id = '" . db_input(
                                                        $cfg->get('use_global_list')
                                                    ) . "' and length(users)>0 and find_in_set(" . $app_user['id'] . ",users)"
                                                );
                                            } else {
                                                $choice_query = db_query(
                                                    "select * from app_fields_choices where id='" . db_input(
                                                        $choices_id
                                                    ) . "' and length(users)>0 and find_in_set(" . $app_user['id'] . ",users)"
                                                );
                                            }

                                            if ($choice = db_fetch_array($choice_query)) {
                                                $has_access = true;
                                            }
                                        }
                                    }
                                    break;
                                case 'fieldtype_created_by':
                                    $has_access = ($app_user['id'] == $item_info['created_by'] ? true : false);
                                    break;
                                default:
                                    if (strlen($item_info['field_' . $field_id])) {
                                        $has_access = in_array(
                                            $app_user['id'],
                                            explode(',', $item_info['field_' . $field_id])
                                        );
                                    }
                                    break;
                            }
                        }

                        //stop checking if has access;
                        if ($has_access) {
                            break;
                        }
                    }
                }
            }

            if ($has_access) {
                $buttons_list[] = $buttons;
            }
        }

        return $buttons_list;
    }

    public function check_buttons_filters($buttons)
    {
        global $sql_query_having;

        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $buttons['entities_id']
            ) . "' and reports_type='process" . $buttons['id'] . "'"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $listing_sql_query = '';
            $listing_sql_query_select = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select(
                $this->entities_id,
                $listing_sql_query_select
            );

            $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$this->entities_id])) {
                $listing_sql_query_having = reports::prepare_filters_having_query(
                    $sql_query_having[$this->entities_id]
                );
            }

            $listing_sql_query .= $listing_sql_query_having;

            $item_info_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $buttons['entities_id'] . " e  where e.id='" . $this->items_id . "' " . $listing_sql_query;

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

    public function prepare_button_css($buttons, $css_class = '')
    {
        $css = '';

        if (strlen($buttons['button_color'])) {
            $rgb = convert_html_color_to_RGB($buttons['button_color']);
            $rgb[0] = $rgb[0] - 25;
            $rgb[1] = $rgb[1] - 25;
            $rgb[2] = $rgb[2] - 25;
            $css = '
					<style>
						.btn-process-' . $css_class . $buttons['id'] . '{
							background-color: ' . $buttons['button_color'] . '; 
						  border-color: ' . $buttons['button_color'] . ';
						}
						.btn-primary.btn-process-' . $css_class . $buttons['id'] . ':hover,
						.btn-primary.btn-process-' . $css_class . $buttons['id'] . ':focus,
						.btn-primary.btn-process-' . $css_class . $buttons['id'] . ':active,
						.btn-primary.btn-process-' . $css_class . $buttons['id'] . '.active,								
						.open .dropdown-toggle.btn-process-' . $css_class . $buttons['id'] . ',
                                                .open>.dropdown-toggle.btn-primary.btn-process-' . $css_class . $buttons['id'] . ':focus, 
                                                .open>.dropdown-toggle.btn-primary.btn-process-' . $css_class . $buttons['id'] . ':hover 
						{							
						  background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1); 
						  border-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
						}
					</style>		
			';
        }

        return $css;
    }

    public function preapre_values_from_current_item($sql_data, $process_info, $item_id)
    {
        global $sql_data_holder, $item_info_holder, $app_user, $app_fields_cache;

        $check = false;

        //check if there are values to replace
        foreach ($sql_data as $k => $v) {
            if (isset($sql_data_holder[$k])) {
                $v = $sql_data_holder[$k];
            }

            if (preg_match('/\[\d+\]/', $v) or strstr($v, '[created_by]') or strstr($v, '[current_user_id]')) {
                $check = true;
            }
        }

        //print_r($sql_data);
        //echo $check;
        //exit();


        if ($check) {
            if (!isset($item_info_holder[$item_id])) {
                $item_info_query = db_query(
                    "select e.* " . fieldtype_formula::prepare_query_select(
                        $process_info['entities_id'],
                        ''
                    ) . " from app_entity_" . $process_info['entities_id'] . " e  where e.id='" . $item_id . "'"
                );
                $item_info_holder[$item_id] = $item_info = db_fetch_array($item_info_query);
            } else {
                $item_info = $item_info_holder[$item_id];
            }

            //echo 'item_id=' . $item_id;
            //print_r($item_info);

            foreach ($sql_data as $k => $v) {
                //hold first sql data and use it for next items
                if (!isset($sql_data_holder[$k])) {
                    $sql_data_holder[$k] = $v;
                } else {
                    $v = $sql_data_holder[$k];
                }

                if (preg_match_all('/\[(\d+)\]/', $v, $matches)) {
                    foreach ($matches[1] as $matches_key => $fields_id) {
                        $v = str_replace('[' . $fields_id . ']', $item_info['field_' . $fields_id], $v);
                    }

                    $sql_data[$k] = $v;
                }

                //use created_by value for users
                if (strstr($v, '[created_by]')) {
                    $v = trim(str_replace('[created_by]', $item_info['created_by'], $v));
                    $sql_data[$k] = $v;
                }

                //use current user ID
                if (strstr($v, '[current_user_id]')) {
                    $v = trim(str_replace('[current_user_id]', $app_user['id'], $v));
                    $sql_data[$k] = $v;
                }
            }
        }

        //print_r($sql_data);		
        //exit();

        return $sql_data;
    }

    public function apply_button_filter_to_selected_items($process_info, $selected_items)
    {
        global $sql_query_having;

        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $process_info['entities_id']
            ) . "' and reports_type='process" . $process_info['id'] . "'"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $current_entity_id = $process_info['entities_id'];

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

            $item_info_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e  where e.id in (" . implode(
                    ',',
                    $selected_items
                ) . ")" . $listing_sql_query;

            $filtered_selected_items = [];
            $items_query = db_query($item_info_sql, false);
            while ($items = db_fetch_array($items_query)) {
                $filtered_selected_items[] = $items['id'];
            }

            $selected_items = $filtered_selected_items;
        }

        //print_r($selected_items);
        //exit();

        return $selected_items;
    }

    public function run($process_info, $reports_id = false, $is_ipn = false)
    {
        global $app_path, $app_redirect_to, $app_user, $app_selected_items, $alerts, $sql_data_holder, $item_info_holder, $current_item_id, $app_entities_cache, $sql_data, $app_fields_cache, $app_force_print_template;

        if (!$reports_id) {
            $selected_items = [$this->items_id];
        } else {
            if (count($app_selected_items[$reports_id])) {
                $selected_items = $app_selected_items[$reports_id];

                //apply filters if setup
                $selected_items = $this->apply_button_filter_to_selected_items($process_info, $selected_items);
            } else {
                die(TEXT_PLEASE_SELECT_ITEMS);
            }
        }

        //include sms modules
        $modules = new modules('sms');
        $modules = new modules('mailing');

        $actions_query = db_query(
            "select pa.*, p.name as process_name, p.entities_id from app_ext_processes_actions pa, app_ext_processes p where pa.is_active=1 and  pa.process_id='" . $process_info['id'] . "' and  p.id=pa.process_id order by pa.sort_order"
        );
        while ($actions = db_fetch_array($actions_query)) {
            $action_entity_id = self::get_entity_id_from_action_type($actions['type']);
            $action_entity_cfg = new entities_cfg($action_entity_id);

            //check fields access
            $fields_access_schema = users::get_fields_access_schema($action_entity_id, $app_user['group_id']);

            $sql_data = [];
            $sql_data_holder = [];

            $actions_fields_list = [];

            $actions_fields_query = db_query(
                "select af.enter_manually, af.id, af.fields_id, af.value, f.name, f.type from app_ext_processes_actions_fields af, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=af.fields_id and af.actions_id='" . db_input(
                    $actions['id']
                ) . "' order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($actions_fields = db_fetch_array($actions_fields_query)) {
                //skip fields if no edit access
                if (isset($fields_access_schema[$actions_fields['fields_id']]) and $process_info['apply_fields_access_rules'] == 1 and in_array(
                        $actions_fields['enter_manually'],
                        [1, 2]
                    )) {
                    continue;
                }

                //handle manually entered field
                if (isset($_POST['fields'][$actions_fields['fields_id']]) or isset($_FILES['fields']['name'][$actions_fields['fields_id']])) {
                    $field = db_find('app_fields', $actions_fields['fields_id']);
                    $value = isset($_POST['fields'][$actions_fields['fields_id']]) ? $_POST['fields'][$actions_fields['fields_id']] : '';

                    //prepare process options
                    $process_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'fields_cache' => [],
                        'field' => $field,
                        'is_new_item' => true,
                        'current_field_value' => '',
                    ];

                    $actions_fields['value'] = fields_types::process($process_options);
                } else {
                    //handle dates
                    if ($actions_fields['type'] == 'fieldtype_input_date') {
                        $actions_fields['value'] = ($actions_fields['value'] == ' ' ? 0 : (strlen(
                            $actions_fields['value']
                        ) < 5 ? get_date_timestamp(
                            date('Y-m-d', strtotime($actions_fields['value'] . ' day'))
                        ) : $actions_fields['value']));
                    } elseif ($actions_fields['type'] == 'fieldtype_input_datetime') {
                        $actions_fields['value'] = ($actions_fields['value'] == ' ' ? 0 : (strlen(
                            $actions_fields['value']
                        ) < 5 ? strtotime($actions_fields['value'] . ' day') : $actions_fields['value']));
                    }
                }

                switch ($actions_fields['type']) {
                    case 'fieldtype_users_approve':
                        if (strlen($actions_fields['value'])) {
                            $actions_fields['value'] = str_replace(
                                '[current_user_id]',
                                $app_user['id'],
                                $actions_fields['value']
                            );

                            db_query(
                                "delete from app_approved_items where entities_id='" . $action_entity_id . "' and items_id in (" . implode(
                                    ',',
                                    $selected_items
                                ) . ") and fields_id='" . $actions_fields['fields_id'] . "' and users_id not in (" . $actions_fields['value'] . ")"
                            );
                        } else {
                            db_query(
                                "delete from app_approved_items where entities_id='" . $action_entity_id . "' and items_id in (" . implode(
                                    ',',
                                    $selected_items
                                ) . ") and fields_id='" . $actions_fields['fields_id'] . "'"
                            );
                        }
                        $sql_data['field_' . $actions_fields['fields_id']] = $actions_fields['value'];
                        break;
                    case 'fieldtype_created_by':
                        $sql_data['created_by'] = $actions_fields['value'];
                        break;
                    default:
                        $sql_data['field_' . $actions_fields['fields_id']] = $actions_fields['value'];
                        break;
                }


                //prepare choices values for fields with multiple values
                $actions_fields_list[] = $actions_fields;
            }

            //paretn item for move action
            if (isset($_POST['parent_item_id']) and strstr($actions['type'], 'move_item_entity_')) {
                $sql_data['parent_item_id'] = _post::int('parent_item_id');
            }


            //print_rr($_POST);
            //print_rr($actions_fields_list);
            //print_rr($sql_data);
            //exit();
            //print_r($selected_items);

            if (count($sql_data) or strstr($actions['type'], 'edit_item_entity_') or
                strstr($actions['type'], 'copy_item_entity_') or
                strstr($actions['type'], 'clone_subitems_linked_entity_') or
                strstr($actions['type'], 'clone_item_entity_') or
                strstr($actions['type'], 'link_records_by_mysql_query_') or
                strstr($actions['type'], 'unlink_records_by_mysql_query_') or
                strstr($actions['type'], 'save_export_template_entity_') or
                strstr($actions['type'], 'repeat_item_entity_') or
                strstr($actions['type'], 'runphp_item_entity_')
            ) {
                foreach ($selected_items as $item_id) {
                    //echo '<pre>';
                    //echo 'item=' . $item_id . ' - acton id = ' . $actions['id'];
                    //handle values from current item
                    $sql_data = $this->preapre_values_from_current_item($sql_data, $process_info, $item_id);

                    //prepare choices values for fields with multiple values
                    $choices_values = new choices_values($action_entity_id);

                    foreach ($actions_fields_list as $actions_fields) {
                        if (isset($sql_data['field_' . $actions_fields['fields_id']])) {
                            $process_options = [
                                'class' => $actions_fields['type'],
                                'field' => ['id' => $actions_fields['fields_id']],
                                'value' => explode(',', $sql_data['field_' . $actions_fields['fields_id']])
                            ];

                            $choices_values->prepare($process_options);
                        }
                    }

                    //echo '<pre>';
                    //print_r($actions_fields_list);
                    //print_r($choices_values);
                    //print_r($sql_data);
                    //exit();
                    //continue;	


                    switch (true) {
                        case strstr($actions['type'], 'move_item_entity_'):
                        case strstr($actions['type'], 'edit_parent_item_entity_'):
                        case strstr($actions['type'], 'edit_item_entity_'):

                            //redefine $item_id, get parent_item_id value from selected item 
                            if (strstr($actions['type'], 'edit_parent_item_entity_')) {
                                $item_info_query = db_query(
                                    "select parent_item_id from app_entity_" . $process_info['entities_id'] . " where id='" . db_input(
                                        $item_id
                                    ) . "'"
                                );
                                $item_info = db_fetch_array($item_info_query);
                                $item_id = $item_info['parent_item_id'];
                            }

                            $has_comment = false;

                            //get previous item info
                            $item_info_query = db_query(
                                "select * from app_entity_" . $action_entity_id . " where id='" . db_input(
                                    $item_id
                                ) . "'"
                            );
                            $item_info = db_fetch_array($item_info_query);

                            if (count($sql_data)) {
                                //check unique value in users entity etc.
                                $sql_data = $this->validate_sql_data($sql_data, $action_entity_id, $item_id);

                                //update item
                                $sql_data['date_updated'] = time();
                                db_perform(
                                    'app_entity_' . $action_entity_id,
                                    $sql_data,
                                    'update',
                                    "id='" . db_input($item_id) . "'"
                                );

                                //insert choices values for fields with multiple values
                                $choices_values->process($item_id);

                                //prepare user roles
                                fieldtype_user_roles::set_user_roles_to_items($action_entity_id, $item_id);

                                //autoupdate all field types
                                fields_types::update_items_fields($action_entity_id, $item_id);

                                //check public form notification
                                public_forms::send_client_notification($action_entity_id, $item_info);

                                $has_comment = true;
                            } else {
                                //atuoset fieldtype autostatus
                                fieldtype_autostatus::set($action_entity_id, $item_id);
                            }

                            //send sms notification
                            $sms = new sms($action_entity_id, $item_id);
                            $sms->send_to = false;
                            $sms->send_edit_msg($item_info);

                            //email rules
                            $email_rules = new email_rules($action_entity_id, $item_id);
                            $email_rules->send_edit_msg($item_info);

                            if ($action_entity_id == 1) {
                                public_registration::send_user_activation_email_msg($item_id, $item_info);
                            }

                            //reset signatures
                            fieldtype_digital_signature::reset_signature_if_data_changed(
                                $action_entity_id,
                                $item_id,
                                $item_info
                            );

                            $attachments = '';
                            $description = (isset($_POST['description']) ? $_POST['description'] : '');

                            if (isset($_POST['fields']['attachments']) or strlen($description)) {
                                $attachments = (isset($_POST['fields']['attachments']) ? $_POST['fields']['attachments'] : '');
                                $description = $_POST['description'];

                                $has_comment = true;
                            }

                            //check if there are fields to update in comments
                            if (isset($_POST['fields'])) {
                                foreach ($_POST['fields'] as $k => $v) {
                                    if (is_array($v)) {
                                        if (count($v)) {
                                            $has_comment = true;
                                        }
                                    } elseif (strlen($v)) {
                                        $has_comment = true;
                                    }
                                }
                            }

                            //disable comments
                            if (($process_info['disable_comments'] == 1 and !strlen(
                                        $description
                                    )) or $action_entity_cfg->get('use_comments') != 1) {
                                $has_comment = false;
                            }

                            if ($has_comment) {
                                $sql_data_comments = [
                                    'description' => db_prepare_html_input($description),
                                    'entities_id' => $action_entity_id,
                                    'items_id' => $item_id,
                                    'attachments' => fields_types::process(
                                        ['class' => 'fieldtype_attachments', 'value' => $attachments]
                                    ),
                                ];

                                $sql_data_comments['date_added'] = time();
                                $sql_data_comments['created_by'] = $app_user['id'];

                                db_perform('app_comments', $sql_data_comments);

                                $comments_id = db_insert_id();

                                //insert comments history						 		
                                $track_fields = [];
                                foreach ($sql_data as $field => $value) {
                                    db_perform(
                                        'app_comments_history',
                                        [
                                            'comments_id' => $comments_id,
                                            'fields_id' => str_replace('field_', '', $field),
                                            'fields_value' => $value
                                        ]
                                    );

                                    $track_fields[str_replace('field_', '', $field)] = $value;
                                }

                                //
                                if (strstr($actions['type'], 'move_item_entity_')) {
                                    $field_query = db_query(
                                        "select id from app_fields where type='fieldtype_parent_item_id' and entities_id='" . $action_entity_id . "'"
                                    );
                                    $field = db_fetch_array($field_query);

                                    db_perform(
                                        'app_comments_history',
                                        [
                                            'comments_id' => $comments_id,
                                            'fields_id' => $field['id'],
                                            'fields_value' => _post::int('parent_item_id')
                                        ]
                                    );
                                }

                                //prepare input numeric in comments						 		
                                $sql_data_item = [];
                                $fields_query = db_query(
                                    "select f.* from app_fields f where f.type  in ('fieldtype_input_numeric_comments','fieldtype_time') and  f.entities_id='" . db_input(
                                        $action_entity_id
                                    ) . "' and f.comments_status=1 order by f.comments_sort_order, f.name"
                                );
                                while ($v = db_fetch_array($fields_query)) {
                                    $value = (isset($_POST['fields'][$v['id']]) ? $_POST['fields'][$v['id']] : 0);

                                    if ($value > 0) {
                                        db_perform(
                                            'app_comments_history',
                                            [
                                                'comments_id' => $comments_id,
                                                'fields_id' => $v['id'],
                                                'fields_value' => $value
                                            ]
                                        );

                                        $filed_type = new fieldtype_input_numeric_comments;
                                        $sql_data_item['field_' . $v['id']] = $filed_type->get_fields_sum(
                                            $action_entity_id,
                                            $item_id,
                                            $v['id']
                                        );

                                        $track_fields[$v['id']] = $value;
                                    }
                                }

                                //update item
                                if (count($sql_data_item)) {
                                    db_perform(
                                        'app_entity_' . $action_entity_id,
                                        $sql_data_item,
                                        'update',
                                        "id='" . db_input($item_id) . "'"
                                    );
                                }

                                //send notificaton
                                app_send_new_comment_notification($comments_id, $item_id, $action_entity_id);

                                //track changes
                                $log = new track_changes($action_entity_id, $item_id);

                                if (strstr($actions['type'], 'move_item_entity_')) {
                                    $log->log_move(_post::int('parent_item_id'));
                                }

                                if (strlen($description)) {
                                    $log->log_comment($comments_id, $track_fields);
                                } elseif (count($track_fields)) {
                                    $log->log_update($item_info);
                                }
                            }

                            break;

                        case strstr($actions['type'], 'copy_item_entity_'):
                            $settigns = new settings($actions['settings']);

                            $number_of_copies = $settigns->get('number_of_copies') > 0 ? $settigns->get(
                                'number_of_copies'
                            ) : 1;
                            $number_of_copies = isset($_POST['number_of_copies']) ? _POST(
                                'number_of_copies'
                            ) : $number_of_copies;

                            $copy_process = new items_copy($action_entity_id, $item_id, $settigns->get_settings());

                            //set paretn
                            if (isset($_POST['parent_item_id'])) {
                                if ($_POST['parent_item_id'] > 0) {
                                    $copy_process->set_parent_item_id(_post::int('parent_item_id'));
                                }
                            }

                            //set sql data
                            $copy_process->set_sql_data($sql_data);

                            for ($i = 1; $i <= $number_of_copies; $i++) {
                                if ($new_item_id = $copy_process->run() and count($selected_items) == 1) {
                                    $app_redirect_to == 'items_info';
                                    $app_path = $action_entity_id . '-' . $new_item_id;
                                }

                                //autoupdate all field types
                                fields_types::update_items_fields($action_entity_id, $new_item_id);

                                $choices_values->process($new_item_id);

                                //run actions after item insert
                                $processes = new processes($action_entity_id);
                                $processes->run_after_insert($new_item_id);
                            }

                            break;

                        case strstr($actions['type'], 'repeat_item_entity_'):
                            $settigns = new settings($actions['settings']);

                            $sql_data_tasks = [
                                'entities_id' => $action_entity_id,
                                'items_id' => $item_id,
                                'is_active' => 1,
                                'repeat_type' => $settigns->get('repeat_type'),
                                'repeat_time' => $settigns->get('repeat_time'),
                                'repeat_interval' => ($settigns->get('repeat_interval') > 0 ? $settigns->get(
                                    'repeat_interval'
                                ) : 1),
                                'repeat_days' => (($settigns->get('repeat_type') == 'weekly') ? implode(
                                    ',',
                                    $settigns->get('repeat_days')
                                ) : ''),
                                'repeat_start' => (strlen($settigns->get('repeat_start')) ? get_date_timestamp(
                                    $settigns->get('repeat_start')
                                ) : ''),
                                'repeat_end' => (strlen($settigns->get('repeat_end')) ? get_date_timestamp(
                                    $settigns->get('repeat_end')
                                ) : ''),
                                'repeat_limit' => $settigns->get('repeat_limit'),
                                'date_added' => time(),
                                'created_by' => $app_user['id'],
                            ];

                            db_perform('app_ext_recurring_tasks', $sql_data_tasks);
                            $tasks_id = db_insert_id();

                            if (count($sql_data)) {
                                foreach ($sql_data as $data_field_id => $data_field_value) {
                                    $data_field_id = str_replace('field_', '', $data_field_id);

                                    if (isset($app_fields_cache[$action_entity_id][$data_field_id])) {
                                        $sql_data = [
                                            'tasks_id' => $tasks_id,
                                            'fields_id' => $data_field_id,
                                            'value' => $data_field_value,
                                        ];

                                        db_perform('app_ext_recurring_tasks_fields', $sql_data);
                                    }
                                }
                            }

                            break;

                        case strstr($actions['type'], 'clone_item_entity_'):

                            $settigns = new settings($actions['settings']);

                            if ($settigns->get('clone_nested_items') == 1) {
                                clone_subitems::clone_nested_items_process(
                                    $actions['id'],
                                    $item_id,
                                    (isset($_POST['parent_item_id']) ? _post::int('parent_item_id') : 0)
                                );
                            } else {
                                clone_subitems::clone_process(
                                    $actions['id'],
                                    0,
                                    $item_id,
                                    (isset($_POST['parent_item_id']) ? _post::int('parent_item_id') : 0),
                                    'id'
                                );
                            }

                            break;

                        case strstr($actions['type'], 'save_export_template_entity_'):
                            $settigns = new settings($actions['settings']);
                            $save_export_template = $settigns->get('save_export_template');

                            if (is_array($save_export_template)) {
                                //include export libs
                                require_once(CFG_PATH_TO_DOMPDF);

                                require_once(CFG_PATH_TO_PHPWORD);

                                $sql_data_item = [];
                                foreach ($save_export_template as $field_id => $templates) {
                                    //skip fields without templates
                                    if (is_string($templates) and !strlen($templates)) {
                                        continue;
                                    }

                                    if (!is_array($templates)) {
                                        $templates = [$templates];
                                    }

                                    $filenames = [];
                                    foreach ($templates as $template) {
                                        $template = explode('_', $template);

                                        $export_templates_file = new export_templates_file($action_entity_id, $item_id);
                                        if (strlen(
                                            $filename = $export_templates_file->save($template[0], $template[1])
                                        )) {
                                            $filenames[] = $filename;
                                        }
                                    }

                                    $sql_data_item['field_' . $field_id] = implode(',', $filenames);
                                }

                                //print_rr($sql_data_item);                                                                                                                                                                        
                                //exit();

                                if (count($sql_data_item)) {
                                    db_perform(
                                        'app_entity_' . $action_entity_id,
                                        $sql_data_item,
                                        "update",
                                        "id='" . $item_id . "'"
                                    );
                                }
                            }

                            break;

                        case strstr($actions['type'], 'clone_subitems_linked_entity_'):
                            $value = explode('_', str_replace('clone_subitems_linked_entity_', '', $actions['type']));
                            $field_info_query = db_query(
                                "select id, configuration,type from app_fields where id='" . $value[1] . "'"
                            );
                            if ($field_info = db_fetch_array($field_info_query)) {
                                $use_field_name = "field_" . $field_info['id'];
                                $item_info_query = db_query(
                                    "select parent_item_id, {$use_field_name} from app_entity_" . $actions['entities_id'] . " where id='" . db_input(
                                        $item_id
                                    ) . "'"
                                );
                                if ($item_info = db_fetch_array($item_info_query)) {
                                    if (strlen($item_info[$use_field_name])) {
                                        foreach (explode(',', $item_info[$use_field_name]) as $linked_item_id) {
                                            clone_subitems::clone_process($actions['id'], 0, $linked_item_id, $item_id);
                                        }
                                    }
                                }
                            }

                            //exit();

                            break;

                        case strstr($actions['type'], 'edit_item_linked_entity_'):
                            $value = explode('_', str_replace('edit_item_linked_entity_', '', $actions['type']));
                            $field_info_query = db_query(
                                "select id, configuration,type from app_fields where id='" . $value[1] . "'"
                            );
                            if ($field_info = db_fetch_array($field_info_query)) {
                                $use_field_name = ($field_info['type'] == 'fieldtype_created_by' ? 'created_by' : "field_" . $field_info['id']);
                                $item_info_query = db_query(
                                    "select {$use_field_name} from app_entity_" . $actions['entities_id'] . " where id='" . db_input(
                                        $item_id
                                    ) . "'"
                                );
                                if ($item_info = db_fetch_array($item_info_query)) {
                                    if (strlen($item_info[$use_field_name])) {
                                        foreach (explode(',', $item_info[$use_field_name]) as $linked_item_id) {
                                            //get previous item info
                                            $item_info_query = db_query(
                                                "select * from app_entity_" . $action_entity_id . " where id='" . db_input(
                                                    $linked_item_id
                                                ) . "'"
                                            );
                                            $item_info = db_fetch_array($item_info_query);

                                            $sql_data['date_updated'] = time();

                                            //update item
                                            db_perform(
                                                'app_entity_' . $action_entity_id,
                                                $sql_data,
                                                'update',
                                                "id='" . db_input($linked_item_id) . "'"
                                            );
                                            $choices_values->process($linked_item_id);

                                            //autoupdate all field types
                                            fields_types::update_items_fields($action_entity_id, $linked_item_id);

                                            //run actions after item update
                                            $processes = new processes($action_entity_id);
                                            $processes->run_after_update($linked_item_id);

                                            //send sms notification
                                            $sms = new sms($action_entity_id, $linked_item_id);
                                            $sms->send_to = false;
                                            $sms->send_edit_msg($item_info);

                                            //email rules
                                            $email_rules = new email_rules($action_entity_id, $linked_item_id);
                                            $email_rules->send_edit_msg($item_info);
                                        }
                                    }
                                }
                            }
                            break;

                        case strstr($actions['type'], 'insert_item_linked_entity_'):
                            $value = explode('_', str_replace('insert_item_linked_entity_', '', $actions['type']));
                            $field_info_query = db_query(
                                "select type, id, configuration from app_fields where id='" . $value[1] . "'"
                            );
                            if ($field_info = db_fetch_array($field_info_query)) {
                                $item_info_query = db_query(
                                    "select parent_item_id, field_" . $field_info['id'] . " from app_entity_" . $actions['entities_id'] . " where id='" . db_input(
                                        $item_id
                                    ) . "'"
                                );
                                if ($item_info = db_fetch_array($item_info_query)) {
                                    //prepare data before insert
                                    $sql_data['parent_item_id'] = ($app_entities_chace[$actions['entities_id']]['parent_id'] == $app_entities_chace[$action_entity_id]['parent_id'] ? $item_info['parent_item_id'] : 0);
                                    $sql_data['created_by'] = $app_user['id'];
                                    $sql_data['date_added'] = time();

                                    $sql_data = $this->prepare_field_type_random_value($sql_data, $action_entity_id);

                                    //insert new item
                                    db_perform('app_entity_' . $action_entity_id, $sql_data);

                                    //insert choices values for fields with multiple values
                                    $new_item_id = db_insert_id();
                                    $choices_values->process($new_item_id);

                                    //autoupdate all field types
                                    fields_types::update_items_fields($action_entity_id, $new_item_id);

                                    //run actions after item insert
                                    $processes = new processes($action_entity_id);
                                    $processes->run_after_insert($new_item_id);

                                    //send nofitication
                                    items::send_new_item_nofitication($action_entity_id, $new_item_id);

                                    //log changeds
                                    $log = new track_changes($action_entity_id, $new_item_id);
                                    $log->log_insert();

                                    //subscribe									
                                    $mailing = new mailing($action_entity_id, $new_item_id);
                                    $mailing->subscribe();

                                    //update current item value
                                    $value = (strlen(
                                            $item_info['field_' . $field_info['id']]
                                        ) ? $item_info['field_' . $field_info['id']] . ',' : '') . $new_item_id;
                                    $sql_data = ['field_' . $field_info['id'] => $value];

                                    $cv = new choices_values($actions['entities_id']);
                                    $process_options = [
                                        'class' => $field_info['type'],
                                        'field' => ['id' => $field_info['id']],
                                        'value' => explode(',', $value)
                                    ];

                                    $cv->prepare($process_options);

                                    //update item
                                    db_perform(
                                        'app_entity_' . $actions['entities_id'],
                                        $sql_data,
                                        'update',
                                        "id='" . db_input($item_id) . "'"
                                    );
                                    $cv->process($item_id);
                                }
                            }
                            break;

                        case strstr($actions['type'], 'edit_item_subentity_'):

                            //get filtered items and skip and if no items found
                            if (($filtered_items = $this->include_filtered_items($actions, $item_id)) === false) {
                                break;
                            }

                            $sql_data['date_updated'] = time();

                            db_perform(
                                'app_entity_' . $action_entity_id,
                                $sql_data,
                                'update',
                                "parent_item_id='" . db_input($item_id) . "'" . (strlen(
                                    $filtered_items
                                ) ? ' and id in (' . $filtered_items . ')' : '')
                            );

                            //insert choices values for fields with multiple values
                            if (count($choices_values->choices_values_list)) {
                                $subitems_query = db_query(
                                    "select * from app_entity_" . $action_entity_id . " where parent_item_id='" . db_input(
                                        $item_id
                                    ) . "'" . (strlen($filtered_items) ? ' and id in (' . $filtered_items . ')' : '')
                                );
                                while ($subitems = db_fetch_array($subitems_query)) {
                                    $choices_values->process($subitems['id']);
                                }
                            }

                            //autoupdate time diff
                            $items_query = db_query(
                                "select id from app_entity_" . $action_entity_id . " where parent_item_id='" . db_input(
                                    $item_id
                                ) . "'" . (strlen($filtered_items) ? ' and id in (' . $filtered_items . ')' : '')
                            );
                            while ($items = db_fetch_array($items_query)) {
                                fields_types::update_items_fields($action_entity_id, $items['id']);

                                //run actions after item update
                                $processes = new processes($action_entity_id);
                                $processes->run_after_update($items['id']);
                            }

                            break;

                        case strstr($actions['type'], 'insert_item_subentity_'):

                            //prepare data before insert
                            $sql_data['parent_item_id'] = $item_id;
                            $sql_data['created_by'] = $app_user['id'];
                            $sql_data['date_added'] = time();

                            $sql_data = $this->prepare_field_type_random_value($sql_data, $action_entity_id);

                            //insert new item
                            db_perform('app_entity_' . $action_entity_id, $sql_data);

                            //insert choices values for fields with multiple values
                            $new_item_id = db_insert_id();
                            $choices_values->process($new_item_id);

                            //autoupdate all field types
                            fields_types::update_items_fields($action_entity_id, $new_item_id);

                            //run actions after item insert
                            $processes = new processes($action_entity_id);
                            $processes->run_after_insert($new_item_id);

                            //send nofitication
                            items::send_new_item_nofitication($action_entity_id, $new_item_id);

                            //log changeds
                            $log = new track_changes($action_entity_id, $new_item_id);
                            $log->log_insert();

                            //subscribe							
                            $mailing = new mailing($action_entity_id, $new_item_id);
                            $mailing->subscribe();

                            break;

                        //unlink recorts by query    
                        case strstr($actions['type'], 'unlink_records_by_mysql_query_'):
                            $settigns = new settings($actions['settings']);
                            $records = new link_records_by_mysql_query(
                                $this->entities_id,
                                $item_id,
                                $action_entity_id,
                                $settigns->get('where_query')
                            );
                            $records->process_action = 'unlink';
                            $records->process($sql_data, $choices_values);
                            break;

                        //link records by query
                        case strstr($actions['type'], 'link_records_by_mysql_query_'):
                            $settigns = new settings($actions['settings']);
                            $records = new link_records_by_mysql_query(
                                $this->entities_id,
                                $item_id,
                                $action_entity_id,
                                $settigns->get('where_query')
                            );
                            $records->process($sql_data, $choices_values);
                            break;


                        case strstr($actions['type'], 'edit_item_related_entity_'):
                            $table_info = related_records::get_related_items_table_name(
                                $this->entities_id,
                                $action_entity_id
                            );
                            $where_sql = "select entity_" . $action_entity_id . $table_info['sufix'] . "_items_id as item_id from " . $table_info['table_name'] . " where entity_" . $this->entities_id . "_items_id='" . db_input(
                                    $item_id
                                ) . "'";

                            //get filtered items and skip and if no items found
                            if (($filtered_items = $this->include_filtered_items($actions, 0, $where_sql)) === false) {
                                break;
                            }

                            $sql_data['date_updated'] = time();

                            db_perform(
                                'app_entity_' . $action_entity_id,
                                $sql_data,
                                'update',
                                "id in ({$where_sql})" . (strlen(
                                    $filtered_items
                                ) ? ' and id in (' . $filtered_items . ')' : '')
                            );

                            //insert choices values for fields with multiple values
                            if (count($choices_values->choices_values_list)) {
                                if (strlen($filtered_items)) {
                                    foreach (explode(',', $filtered_items) as $item_id) {
                                        $choices_values->process($item_id);
                                    }
                                } else {
                                    $subitems_query = db_query($where_sql);
                                    while ($subitems = db_fetch_array($subitems_query)) {
                                        $choices_values->process($subitems['item_id']);
                                    }
                                }
                            }

                            //autoupdate time diff
                            $items_query = db_query(
                                "select id from app_entity_" . $action_entity_id . " where id in ({$where_sql})" . (strlen(
                                    $filtered_items
                                ) ? ' and id in (' . $filtered_items . ')' : '')
                            );
                            while ($items = db_fetch_array($items_query)) {
                                fields_types::update_items_fields($action_entity_id, $items['id']);

                                //run actions after item update
                                $processes = new processes($action_entity_id);
                                $processes->run_after_update($items['id']);
                            }
                            break;
                        case strstr($actions['type'], 'insert_item_related_entity_'):

                            //prepare data before insert
                            $sql_data['created_by'] = $app_user['id'];
                            $sql_data['date_added'] = time();

                            $sql_data = $this->prepare_field_type_random_value($sql_data, $action_entity_id);

                            $action_entity_info = db_find('app_entities', $action_entity_id);

                            if ($action_entity_info['parent_id'] > 0) {
                                $item_info = db_find('app_entity_' . $this->entities_id, $item_id);
                                $sql_data['parent_item_id'] = $item_info['parent_item_id'];
                            }

                            //print_r($sql_data);
                            //exit();
                            //insert new item
                            db_perform('app_entity_' . $action_entity_id, $sql_data);
                            $related_items_id = db_insert_id();

                            //insert choices values for fields with multiple values							
                            $choices_values->process($related_items_id);

                            //autoupdate all field types
                            fields_types::update_items_fields($action_entity_id, $related_items_id);

                            //send nofitication
                            items::send_new_item_nofitication($action_entity_id, $related_items_id);

                            //log changeds
                            $log = new track_changes($action_entity_id, $related_items_id);
                            $log->log_insert();

                            $table_info = related_records::get_related_items_table_name(
                                $this->entities_id,
                                $action_entity_id
                            );

                            $sql_data_related = [
                                'entity_' . $this->entities_id . '_items_id' => $item_id,
                                'entity_' . $action_entity_id . $table_info['sufix'] . '_items_id' => $related_items_id
                            ];

                            db_perform($table_info['table_name'], $sql_data_related);

                            break;

                        case strstr($actions['type'], 'runphp_item_entity_'):

                            $settigns = new settings($actions['settings']);

                            $php_code = $settigns->get('php_code');

                            $php_code = str_replace('[current_user_id]', $app_user['id'], $php_code);

                            $item_info_query = db_query(
                                "select e.* " . fieldtype_formula::prepare_query_select(
                                    $action_entity_id
                                ) . " from app_entity_" . $action_entity_id . " e where id='" . db_input($item_id) . "'"
                            );
                            $item_info = db_fetch_array($item_info_query);

                            //print_rr($item_info);
                            //exit();

                            //prepare values to replace
                            foreach ($item_info as $field_id => $field_value) {
                                $field_id = str_replace('field_', '', $field_id);

                                if (!strlen($field_value)) {
                                    $field_value = 0;
                                } elseif (is_string($field_value)) {
                                    $field_value = "'" . addslashes($field_value) . "'";
                                }

                                $php_code = str_replace('[' . $field_id . ']', $field_value, $php_code);
                            }

                            if (strlen($php_code)) {
                                if ($settigns->get('debug_mode') == 1) {
                                    echo '<code>' . nl2br(htmlspecialchars($php_code)) . '</code>';
                                    exit();
                                }

                                try {
                                    eval($php_code);
                                } catch (Error $e) {
                                    die(TEXT_ERROR . ' ' . $e->getMessage() . ' on line ' . $e->getLine());
                                }
                            }

                            break;
                    }
                }
            }
        }

        //exit();

        if (!$is_ipn) {
            //prepare success msg
            if (strlen($process_info['success_message'])) {
                $alerts->add($process_info['success_message'], 'success');
            } else {
                $alerts->add(
                    sprintf(TEXT_EXT_PROCESS_COMPLETED, $process_info['name'], count($selected_items)),
                    'success'
                );
            }

            if (strlen($process_info['print_template']) and count($selected_items) == 1) {
                $app_force_print_template = $process_info['print_template'] . '_' . $process_info['entities_id'] . '_' . current(
                        $selected_items
                    );
            }

            //echo $app_redirect_to;
            //exit();

            $gotopage = '';
            if (isset($_GET['gotopage'])) {
                $gotopage = '&gotopage[' . key($_GET['gotopage']) . ']=' . current($_GET['gotopage']);
            }

            switch ($app_redirect_to) {
                case 'parent_item_info_page':
                    redirect_to('items/info', 'path=' . $app_path . $gotopage);
                    break;
                case 'dashboard':
                    redirect_to('dashboard/', substr($gotopage, 1));
                    break;
                case 'reports':
                    redirect_to('reports/view', 'reports_id=' . $reports_id);
                    break;
                case 'items':

                    if (strstr($app_path, '-')) {
                        //echo $app_path;

                        $path_array = explode('/', $app_path);
                        $path_array = explode('-', $path_array[count($path_array) - 1]);
                        if (isset($path_array[1])) {
                            $path_info = items::get_path_info($path_array[0], $path_array[1]);
                            $app_path = $path_info['full_path'];
                        }
                    }

                    redirect_to(
                        'items/items',
                        'path=' . ($current_item_id == 0 ? $app_path : substr(
                            $app_path,
                            0,
                            -(strlen($current_item_id) + 1)
                        )) . $gotopage
                    );
                    break;
                case 'items_info':
                    if ($process_info['redirect_to_items_listing'] == 1) {
                        $path_array = explode('-', $app_path);
                        $path_info = items::get_path_info($path_array[0], $path_array[1]);

                        redirect_to(
                            'items/items',
                            'path=' . substr($path_info['full_path'], 0, strrpos($path_info['full_path'], '-'))
                        );
                    } else {
                        redirect_to('items/info', 'path=' . $app_path);
                    }
                    break;
            }

            if (strstr($app_redirect_to, 'report_')) {
                redirect_to('reports/view', 'reports_id=' . str_replace('report_', '', $app_redirect_to) . $gotopage);
            }

            if (strstr($app_redirect_to, 'kanban')) {
                if (strstr($app_redirect_to, 'kanban-top')) {
                    redirect_to('ext/kanban/view', 'id=' . str_replace('kanban-top', '', $app_redirect_to));
                } else {
                    redirect_to(
                        'ext/kanban/view',
                        'id=' . str_replace(
                            'kanban',
                            '',
                            $app_redirect_to
                        ) . (isset($_GET['path']) ? '&path=' . $_GET['path'] : '')
                    );
                }
            }

            if (strstr($app_redirect_to, 'ganttreport')) {
                redirect_to(
                    'ext/ganttchart/dhtmlx',
                    'id=' . str_replace(
                        'ganttreport',
                        '',
                        $app_redirect_to
                    ) . ($app_entities_cache[$process_info['entities_id']]['parent_id'] > 0 ? '&path=' . $app_path : '')
                );
            }

            //redirect to reports group dashboard
            if (strstr($app_redirect_to, 'reports_groups_')) {
                redirect_to('dashboard/reports', 'id=' . str_replace('reports_groups_', '', $app_redirect_to));
            }

            if (strstr($app_redirect_to, 'item_info_page')) {
                redirect_to('items/info', 'path=' . str_replace('item_info_page', '', $app_redirect_to) . $gotopage);
            }

            if (strstr($app_redirect_to, 'related_records_info_page_')) {
                redirect_to(
                    'items/info',
                    'path=' . str_replace('related_records_info_page_', '', $app_redirect_to) . $gotopage
                );
            }

            if (strstr($app_redirect_to, 'user_reports_groups')) {
                redirect_to('dashboard/reports', 'id=' . str_replace('user_reports_groups', '', $app_redirect_to));
            } elseif (strstr($app_redirect_to, 'reports_groups')) {
                redirect_to('dashboard/reports_groups', 'id=' . str_replace('reports_groups', '', $app_redirect_to));
            }
        }
    }

    static function autoupdate_datetime_diff($entities_id, $item_id)
    {
        fieldtype_days_difference::update_items_fields($entities_id, $item_id);
        fieldtype_hours_difference::update_items_fields($entities_id, $item_id);
        fieldtype_years_difference::update_items_fields($entities_id, $item_id);
        fieldtype_months_difference::update_items_fields($entities_id, $item_id);

        //autoupdate static text pattern
        fieldtype_text_pattern_static::set($entities_id, $item_id);
    }

    public function include_filtered_items($action_info, $parent_item_id = 0, $related_items_sql = '')
    {
        global $sql_query_having;

        $items_list = [];

        $action_entity_id = self::get_entity_id_from_action_type($action_info['type']);

        //check if there report for aciton
        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $action_entity_id
            ) . "' and reports_type='process_action" . $action_info['id'] . "'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $sql_data = [
                'name' => '',
                'entities_id' => $action_entity_id,
                'reports_type' => 'process_action' . $action_info['id'],
                'in_menu' => 0,
                'in_dashboard' => 0,
                'created_by' => 0,
            ];

            db_perform('app_reports', $sql_data);
            $reports_id = db_insert_id();
            $reports_info = db_find('app_reports', $reports_id);
        }

        $settings = new settings($action_info['settings']);

        //check if there are filters for report and then include sql query
        //or include query if user has access "view_assigned" or "action_with_assigned"
        $filters_query = db_query(
            "select count(*) as total from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input(
                $reports_info['id']
            ) . "'"
        );
        $filters = db_fetch_array($filters_query);
        if ($filters['total'] > 0 or ($settings->get(
                    'apply_entity_access_rules'
                ) == 1 and (users::has_users_access_name_to_entity(
                        'view_assigned',
                        $action_entity_id
                    ) or $force_access_check = users::has_users_access_name_to_entity(
                        'action_with_assigned',
                        $action_entity_id
                    )))) {
            $listing_sql_query_select = '';
            $listing_sql_query = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];


            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select(
                $action_entity_id,
                $listing_sql_query_select
            );

            $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$action_entity_id])) {
                $listing_sql_query_having = reports::prepare_filters_having_query($sql_query_having[$action_entity_id]);
            }

            //check view assigned only access	
            if ($settings->get('apply_entity_access_rules') == 1) {
                $listing_sql_query = items::add_access_query(
                    $action_entity_id,
                    $listing_sql_query,
                    $force_access_check
                );
            }

            $listing_sql_query .= $listing_sql_query_having;

            //build itesm query
            $items_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $action_entity_id . " e " . " where e.id>0 " . $listing_sql_query;

            //include parent if exist
            if ($parent_item_id > 0) {
                $items_sql .= " and e.parent_item_id='" . $parent_item_id . "'";
            }

            //include related items if exist
            if (strlen($related_items_sql) > 0) {
                $items_sql .= " and e.id in ({$related_items_sql})";
            }

            //echo $items_sql;
            //build items list
            $items_query = db_query($items_sql);
            while ($items = db_fetch_array($items_query)) {
                $items_list[] = $items['id'];
            }

            //echo print_r($items_list);
            //exit();
            //return false if no items
            if (!count($items_list)) {
                return false;
            }
        }


        return implode(',', $items_list);
    }

    public static function get_entity_id_from_action_type($type)
    {
        $value = str_replace(
            [
                'runphp_item_entity_',
                'repeat_item_entity_',
                'save_export_template_entity_',
                'unlink_records_by_mysql_query_',
                'link_records_by_mysql_query_',
                'clone_item_entity_',
                'clone_subitems_linked_entity_',
                'move_item_entity_',
                'edit_item_users_entity_1',
                'insert_item_linked_entity_',
                'edit_item_linked_entity_',
                'edit_parent_item_entity_',
                'edit_item_entity_',
                'copy_item_entity_',
                'edit_item_subentity_',
                'insert_item_subentity_',
                'edit_item_related_entity_',
                'insert_item_related_entity_'
            ],
            '',
            $type
        );
        $value = explode('_', $value);
        return $value[0];
    }

    public static function get_actions_types_choices($entities_id)
    {
        global $app_entities_cache;

        $choices = [];

        $entity_info = db_find('app_entities', $entities_id);

        $choices['edit_item_entity_' . $entity_info['id']] = sprintf(
            TEXT_EXT_PROCESS_ACTION_EDIT_ITEM,
            $entity_info['name']
        );

        $choices['copy_item_entity_' . $entity_info['id']] = sprintf(
            TEXT_EXT_PROCESS_ACTION_COPY_ITEM,
            $entity_info['name']
        );

        $choices['clone_item_entity_' . $entity_info['id']] = sprintf(
            TEXT_EXT_PROCESS_ACTION_CLONE_ITEM,
            $entity_info['name']
        );

        $choices['repeat_item_entity_' . $entity_info['id']] = sprintf(
            TEXT_EXT_PROCESS_ACTION_REPEAT_ITEM,
            $entity_info['name']
        );

        $choices['runphp_item_entity_' . $entity_info['id']] = sprintf(
            TEXT_EXT_PROCESS_ACTION_RUN_PHP,
            $entity_info['name']
        );


        $templates_query = db_query("select id from app_ext_export_templates where entities_id='" . $entities_id . "'");
        if ($templates = db_fetch_array($templates_query)) {
            $choices['save_export_template_entity_' . $entity_info['id']] = sprintf(
                TEXT_EXT_PROCESS_ACTION_SAVE_EXPORT_TEMPLATE,
                $entity_info['name']
            );
        }

        if ($entity_info['parent_id'] > 0) {
            $choices['move_item_entity_' . $entity_info['id']] = sprintf(
                TEXT_EXT_PROCESS_ACTION_MOVE_ITEM,
                $entity_info['name']
            );
            $choices['edit_parent_item_entity_' . $entity_info['parent_id']] = sprintf(
                TEXT_EXT_PROCESS_ACTION_EDIT_PARENT_ITEM,
                $app_entities_cache[$entity_info['parent_id']]['name']
            );
        }

        $entities_query = db_query("select * from app_entities where parent_id='" . $entity_info['id'] . "'");
        while ($entities = db_fetch_array($entities_query)) {
            $choices['edit_item_subentity_' . $entities['id']] = sprintf(
                TEXT_EXT_PROCESS_ACTION_EDIT_ITEM_SUBENTITY,
                $entities['name']
            );
            $choices['insert_item_subentity_' . $entities['id']] = sprintf(
                TEXT_EXT_PROCESS_ACTION_INSERT_ITEM_SUBENTITY,
                $entities['name']
            );
        }

        $fields_query = db_query(
            "select * from app_fields where entities_id='" . $entity_info['id'] . "' and type in ('fieldtype_related_records', 'fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel','fieldtype_users','fieldtype_users_ajax','fieldtype_created_by')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($fields['configuration']);
            switch ($fields['type']) {
                case 'fieldtype_related_records':
                    $entity_id = (int)$cfg->get('entity_id');
                    if ($entity_id) {
                        $choices['edit_item_related_entity_' . $entity_id] = sprintf(
                            TEXT_EXT_PROCESS_ACTION_EDIT_ITEM_RELATED_ENTITY,
                            $fields['name']
                        );

                        //Check parent_id 
                        //Note: related items should be top entity or have the same parenet_id to insert new related item
                        $related_entity_info = db_find('app_entities', $entity_id);
                        if ($related_entity_info['parent_id'] == 0 or $related_entity_info['parent_id'] == $entity_info['parent_id']) {
                            $choices['insert_item_related_entity_' . $entity_id] = sprintf(
                                TEXT_EXT_PROCESS_ACTION_INSERT_ITEM_RELATDENTITY,
                                $fields['name']
                            );
                        }

                        $choices['link_records_by_mysql_query_' . $entity_id] = sprintf(
                            TEXT_EXT_PROCESS_ACTION_LINK_RECORDS_BY_MYSQL_QUERY,
                            $fields['name']
                        );
                        $choices['unlink_records_by_mysql_query_' . $entity_id] = sprintf(
                            TEXT_EXT_PROCESS_ACTION_UNLINK_RECORDS_BY_MYSQL_QUERY,
                            $fields['name']
                        );
                    }
                    break;

                case 'fieldtype_entity':
                case 'fieldtype_entity_ajax':
                case 'fieldtype_entity_multilevel':
                    $entity_id = (int)$cfg->get('entity_id');
                    if ($entity_id) {
                        $choices['edit_item_linked_entity_' . $entity_id . '_' . $fields['id']] = sprintf(
                            TEXT_EXT_PROCESS_ACTION_EDIT_ITEM_LINKED_ENTITY,
                            $app_entities_cache[$entity_id]['name'],
                            $fields['name']
                        );

                        //Check parent_id
                        //Note: related items should be top entity or have the same parenet_id to insert new related item
                        $related_entity_info = db_find('app_entities', $entity_id);
                        if ($related_entity_info['parent_id'] == 0 or $related_entity_info['parent_id'] == $entity_info['parent_id']) {
                            $choices['insert_item_linked_entity_' . $entity_id . '_' . $fields['id']] = sprintf(
                                TEXT_EXT_PROCESS_ACTION_INSERT_ITEM_LINKED_ENTITY,
                                $app_entities_cache[$entity_id]['name'],
                                $fields['name']
                            );
                        }

                        //prepare clone action
                        $check_query = db_query(
                            "(select count(*) as total from app_entities where parent_id='" . $entities_id . "')"
                        );
                        $check = db_fetch_array($check_query);

                        $check_query = db_query(
                            "(select count(*) as total from app_entities where parent_id='" . $entity_id . "')"
                        );
                        $check2 = db_fetch_array($check_query);

                        if ($check['total'] > 0 and $check2['total'] > 0) {
                            $choices['clone_subitems_linked_entity_' . $entity_id . '_' . $fields['id']] = sprintf(
                                TEXT_EXT_PROCESS_ACTION_CLONE_SUBITEMS_LINKED_ENTITY,
                                $app_entities_cache[$entity_id]['name'],
                                $fields['name']
                            );
                        }
                    }
                    break;
                case 'fieldtype_created_by':
                    $choices['edit_item_linked_entity_1_' . $fields['id']] = sprintf(
                        TEXT_EXT_PROCESS_ACTION_EDIT_ITEM_LINKED_ENTITY,
                        $app_entities_cache[1]['name'],
                        fields_types::get_option($fields['type'], 'name', $fields['name'])
                    );
                    break;
                case 'fieldtype_users_ajax':
                case 'fieldtype_users':
                    $choices['edit_item_linked_entity_1_' . $fields['id']] = sprintf(
                        TEXT_EXT_PROCESS_ACTION_EDIT_ITEM_LINKED_ENTITY,
                        $app_entities_cache[1]['name'],
                        $fields['name']
                    );
                    break;
            }
        }

        return $choices;
    }

    public static function get_actions_fields_choices($entity_id, $exclude_types = [])
    {
        $available_types = [
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_boolean',
            'fieldtype_boolean_checkbox',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_input_numeric',
            'fieldtype_input',
            'fieldtype_input_email',
            'fieldtype_input_url',
            'fieldtype_input_file',
            'fieldtype_input_masked',
            'fieldtype_attachments',
            'fieldtype_image',
            'fieldtype_image_ajax',
            'fieldtype_textarea',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_input_masked',
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_grouped_users',
            'fieldtype_progress',
            'fieldtype_todo_list',
            'fieldtype_auto_increment',
            'fieldtype_tags',
            'fieldtype_user_roles',
            'fieldtype_users_approve',
            'fieldtype_user_accessgroups',
            'fieldtype_user_status',
            'fieldtype_created_by',
            'fieldtype_phone',
            'fieldtype_stages',
            'fieldtype_entity_multilevel',
            'fieldtype_ajax_request',
            'fieldtype_time',
            'fieldtype_dropdown_multilevel',
            'fieldtype_input_dynamic_mask',
            'fieldtype_user_firstname',
            'fieldtype_user_lastname',
            'fieldtype_user_photo',
            'fieldtype_user_email',
            'fieldtype_user_username',
            'fieldtype_access_group',
            'fieldtype_iframe',
            'fieldtype_subentity_form',
            'fieldtype_input_vpic',
        ];

        if (count($exclude_types)) {
            $available_types = array_diff($available_types, $exclude_types);
        }

        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (\"" . implode(
                '","',
                $available_types
            ) . "\")  and f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = fields_types::get_option($v['type'], 'name', $v['name']);
        }

        return $choices;
    }

    public static function output_action_field_value($actions_fields)
    {
        if (!isset($actions_fields['enter_manually'])) {
            $actions_fields['enter_manually'] = 0;
        }

        if ($actions_fields['enter_manually'] == 1) {
            return TEXT_EXT_MANUALLY_ENTERED;
        }

        $field = db_find('app_fields', $actions_fields['fields_id']);

        $output_options = [
            'class' => $field['type'],
            'value' => $actions_fields['value'],
            'field' => $field,
            'is_listing' => true,
            'is_export' => true,
        ];

        if (in_array(
            $actions_fields['field_type'],
            [
                'fieldtype_users_approve',
                'fieldtype_users',
                'fieldtype_users_ajax',
                'fieldtype_created_by',
                'fieldtype_dropdown',
                'fieldtype_entity',
                'fieldtype_entity_ajax',
                'fieldtype_entity_multilevel'
            ]
        )) {
            if (strstr($actions_fields['value'], '[')) {
                return $actions_fields['value'];
            } else {
                return fields_types::output($output_options);
            }
        } elseif (in_array($actions_fields['field_type'], ['fieldtype_input_date', 'fieldtype_input_datetime'])) {
            if (strlen($actions_fields['value']) < 10) {
                return $actions_fields['value'];
            } else {
                return fields_types::output($output_options);
            }
        } elseif (in_array(
            $actions_fields['field_type'],
            ['fieldtype_input_file', 'fieldtype_attachments', 'fieldtype_image', 'fieldtype_image_ajax']
        )) {
            return $actions_fields['value'];
        } elseif (in_array($actions_fields['field_type'], ['fieldtype_input_numeric']) and strstr(
                $actions_fields['value'],
                '['
            )) {
            return $actions_fields['value'];
        } else {
            return fields_types::output($output_options);
        }
    }

    function prepare_field_type_random_value($sql_data, $action_entity_id)
    {
        $fields_query = db_query(
            "select * from app_fields where type='fieldtype_random_value' and entities_id='" . $action_entity_id . "'"
        );
        while ($field = db_fetch_array($fields_query)) {
            //prepare process options
            $process_options = [
                'class' => $field['type'],
                'value' => '',
                'fields_cache' => [],
                'field' => $field,
                'is_new_item' => true,
                'current_field_value' => '',
            ];

            $sql_data['field_' . $field['id']] = fields_types::process($process_options);
        }

        return $sql_data;
    }

    function validate_sql_data($sql_data, $entity_id, $item_id)
    {
        //validate users entity
        if ($entity_id == 1) {
            if (isset($sql_data['field_12'])) {
                //check if username exist
                $check_query = db_query(
                    "select id from app_entity_1 where field_12 = '" . db_input(
                        db_prepare_input($sql_data['field_12'])
                    ) . "' and id!=" . $item_id
                );
                if ($check = db_fetch_array($check_query)) {
                    unset($sql_data['field_12']);
                }
            }

            if (isset($sql_data['field_9']) and CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL == 0) {
                //check if email exist
                $check_query = db_query(
                    "select id from app_entity_1 where field_9 = '" . db_input(
                        db_prepare_input($sql_data['field_9'])
                    ) . "' and id!=" . $item_id
                );
                if ($check = db_fetch_array($check_query)) {
                    unset($sql_data['field_9']);
                }
            }
        }

        return $sql_data;
    }

}
