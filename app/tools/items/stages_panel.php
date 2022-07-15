<?php

namespace Tools\Items;

class Stages_panel
{
    public static function get_type_choices()
    {
        return [
            'trianlge' => \K::$fw->TEXT_TRIANGLE,
            'rectangle' => \K::$fw->TEXT_RECTANGLE,
            'dot' => \K::$fw->TEXT_DOT,
            'circle' => \K::$fw->TEXT_CIRCLE
        ];
    }

    public static function render($entities_id, $item_info)
    {
        global $app_fields_cache, $app_path, $app_user;

        $fields_access_schema = users::get_fields_access_schema($entities_id, $app_user['group_id']);

        //check access rules
        $access_rules = new access_rules($entities_id, $item_info);
        $fields_access_schema += $access_rules->get_fields_view_only_access();

        $has_update_access = false;

        if (users::has_access('update', $access_rules->get_access_schema())) {
            $has_update_access = true;
        }

        if (users::has_users_access_name_to_entity('action_with_assigned', $entities_id)) {
            if (!users::has_access_to_assigned_item($entities_id, $item_info['id'])) {
                $has_update_access = false;
            }
        }

        $html = '';

        foreach ($app_fields_cache[$entities_id] as $field) {
            //check field types
            if (!in_array($field['type'], ['fieldtype_stages', 'fieldtype_autostatus'])) {
                continue;
            }

            //check field access
            if (isset($fields_access_schema[$field['id']])) {
                if ($fields_access_schema[$field['id']] == 'hide') {
                    continue;
                }
            }

            $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

            //check if panel type is enabled
            if (!strlen($cfg->get('panel_type'))) {
                continue;
            }

            $current_choice_id = $item_info['field_' . $field['id']];

            if ($cfg->get('use_global_list') > 0) {
                if ($cfg->get('display_type') == 'branching') {
                    $choices = self::get_branching_choices_global_list(
                        $cfg->get('use_global_list'),
                        $current_choice_id
                    );
                } else {
                    $choices = global_lists::get_choices($cfg->get('use_global_list'), false, '', '', true);
                }
            } else {
                if ($cfg->get('display_type') == 'branching') {
                    $choices = self::get_branching_choices($field['id'], $current_choice_id);
                } else {
                    $choices = fields_choices::get_choices($field['id'], false, '', '', '', true);
                }
            }

            switch ($cfg->get('panel_type')) {
                case 'trianlge':
                    $panel_type = 'cd-breadcrumb triangle';
                    break;
                case 'rectangle':
                    $panel_type = 'cd-multi-steps text-center';
                    break;
                case 'dot':
                    $panel_type = 'cd-multi-steps text-top';
                    break;
                case 'circle':
                    $panel_type = 'cd-multi-steps text-bottom count';
                    break;
            }

            $html .= '
				<div class="prolet-body-actions form-group-' . $field['id'] . '">	
					<ol class="stages-panel-' . $field['id'] . ' ' . $panel_type . '">';

            $has_current = (isset($choices[$current_choice_id]) ? false : true);
            $count_after_current = 0;
            foreach ($choices as $choice_id => $choice_name) {
                $li_class = '';

                if ($has_current) {
                    $count_after_current++;
                }

                //hanlde lie css class
                if ($current_choice_id == $choice_id) {
                    $li_class = 'class="current"';
                    $has_current = true;
                } elseif (!$has_current) {
                    $li_class = 'class="visited"';
                }

                //handle click action
                $click_url = '#';
                $click_action = 'onClick="return false"';

                //check if has process url
                $process_url = '';
                $process_access = false;
                if (($process_id = (int)$cfg->get('run_process_for_choice_' . $choice_id)) > 0) {
                    $buttons_query = db_query(
                        "select * from app_ext_processes where id='" . $process_id . "' and is_active=1"
                    );
                    if ($buttons = db_fetch_array($buttons_query)) {
                        $processes = new processes($entities_id);

                        //get manually url
                        if ($processes->has_enter_manually_fields($process_id)) {
                            $process_url = url_for(
                                'items/processes',
                                'id=' . $process_id . '&path=' . $app_path . '&redirect_to=items_info'
                            );
                        }

                        //check if has access (is there is button in list)
                        $buttons_list = $processes->get_buttons_list();

                        foreach ($buttons_list as $button) {
                            if ($button['id'] == $process_id) {
                                $process_access = true;
                            }
                        }
                    }
                }

                if ($cfg->get('click_action') == 'change_value') {
                    $click_action = 'onClick="open_dialog(\'' . (strlen($process_url) ? $process_url : url_for(
                            'items/stages',
                            'path=' . $app_path . '&field_id=' . $field['id'] . '&value_id=' . $choice_id
                        )) . '\')" class="clickable"';
                } elseif ($cfg->get('click_action') == 'change_value_next_step' and $count_after_current == 1) {
                    $click_action = 'onClick="open_dialog(\'' . (strlen($process_url) ? $process_url : url_for(
                            'items/stages',
                            'path=' . $app_path . '&field_id=' . $field['id'] . '&value_id=' . $choice_id
                        )) . '\')" class="clickable"';
                }

                //resed edit action if now edit access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'view') {
                        $click_url = '#';
                        $click_action = 'onClick="return false"';
                    }
                }

                //reset edit action if no access to process
                if (($process_id > 0 and !$process_access) or !$has_update_access) {
                    $click_url = '#';
                    $click_action = 'onClick="return false"';
                }

                $html .= '<li ' . $li_class . '><a href="' . $click_url . '" ' . $click_action . '>' . $choice_name . '</a></li>';

                //display only one stage after current
                if ($cfg->get('display_type') == 'consistently' and $count_after_current == 1) {
                    break;
                }
            }

            $html .= '
					</ol>
				</div>';

            $html .= self::render_css($field);
        }

        return $html;
    }

    public static function render_css($field)
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $css = '';

        if (strlen($cfg->get('color'))) {
            $css .= '
				@media only screen and (min-width: 768px) {	
					.stages-panel-' . $field['id'] . '.cd-breadcrumb.triangle li.visited > * {
					    /* selected step */
					    color: #ffffff;
					    background-color: ' . $cfg->get('color') . ';
					    border-color: ' . $cfg->get('color') . ';
					  }
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-center li.visited > * {					 
					    background-color: ' . $cfg->get('color') . ';
					}
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps li.visited::after {
					    background-color: ' . $cfg->get('color') . ';
					}
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-top li.visited > *::before{
					    background-color: ' . $cfg->get('color') . '; 		
					}
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-bottom li.visited > *::before{
					    background-color: ' . $cfg->get('color') . '; 		
					}
				}					    		
					';
        }

        if (strlen($cfg->get('color_active'))) {
            $css .= '
				@media only screen and (min-width: 768px) {
					.stages-panel-' . $field['id'] . '.cd-breadcrumb.triangle li.current > * {
					    /* selected step */
					    color: #ffffff;
					    background-color: ' . $cfg->get('color_active') . ';
					    border-color: ' . $cfg->get('color_active') . ';
					}
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-center li.current > * {
						  color: #ffffff;
						  background-color: ' . $cfg->get('color_active') . ';
					}
						    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-top li.current > *::before{
					    background-color: ' . $cfg->get('color_active') . '; 		
					}
					    		
					.stages-panel-' . $field['id'] . '.cd-multi-steps.text-bottom li.current > *::before{
					    background-color: ' . $cfg->get('color_active') . '; 		
					}
						
				}
					';
        }

        if (strlen($css)) {
            $css = '
					<style>
					' . $css . '
					</style>	
					';
        }

        return $css;
    }

    public static function get_branching_choices($field_id, $current_choice_id)
    {
        global $app_choices_cache;

        $choices = [];

        $parents_ids = fields_choices::get_paretn_ids($current_choice_id);
        $exclude_ids = [];
        if (count($parents_ids)) {
            $parents_ids = array_reverse($parents_ids);
            //print_rr($parents_ids);

            //prepare top parents
            $choices_query = db_query(
                "select fc.id, fc.name from app_fields_choices fc where fc.fields_id={$field_id} and fc.is_active=1 and fc.parent_id=0 order by fc.sort_order,fc.name"
            );
            while ($v = db_fetch_array($choices_query)) {
                if ($v['id'] == $parents_ids[0]) {
                    break;
                }

                if (fields_choices::has_nested($v['id'])) {
                    $exclude_ids[] = $v['id'];
                    continue;
                }

                $choices[$v['id']] = $v['name'];
            }

            //prepare current parents
            $choices_query = db_query(
                "select * from app_fields_choices where id  in (" . implode(
                    ',',
                    $parents_ids
                ) . ") order by field(id," . implode(',', $parents_ids) . ")"
            );
            while ($v = db_fetch_array($choices_query)) {
                $choices[$v['id']] = $v['name'];
            }
        }

        //get nested choices
        $choices_query = db_query(
            "select * from app_fields_choices where fields_id={$field_id} and is_active=1 and parent_id={$current_choice_id} order by sort_order, name"
        );
        if (!db_num_rows($choices_query) and $app_choices_cache[$current_choice_id]['parent_id'] == 0) {
            //if empyt get all top parents
            $choices_query = db_query(
                "select * from app_fields_choices where fields_id={$field_id} and is_active=1 and parent_id=0 " . (count(
                    $exclude_ids
                ) ? " and id not in (" . implode(',', $exclude_ids) . ")" : "") . " order by sort_order, name"
            );
        }

        while ($v = db_fetch_array($choices_query)) {
            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }

    public static function get_branching_choices_global_list($lists_id, $current_choice_id)
    {
        global $app_global_choices_cache;

        $choices = [];

        $parents_ids = global_lists::get_paretn_ids($current_choice_id);
        $exclude_ids = [];
        if (count($parents_ids)) {
            $parents_ids = array_reverse($parents_ids);
            //print_rr($parents_ids);

            //prepare top parents
            $choices_query = db_query(
                "select fc.id, fc.name from app_global_lists_choices fc where fc.lists_id={$lists_id} and fc.is_active=1 and fc.parent_id=0 order by fc.sort_order,fc.name"
            );
            while ($v = db_fetch_array($choices_query)) {
                if ($v['id'] == $parents_ids[0]) {
                    break;
                }

                if (global_lists::has_nested($v['id'])) {
                    $exclude_ids[] = $v['id'];
                    continue;
                }

                $choices[$v['id']] = $v['name'];
            }

            $choices_query = db_query(
                "select * from app_global_lists_choices where id  in (" . implode(
                    ',',
                    $parents_ids
                ) . ") order by field(id," . implode(',', $parents_ids) . ")"
            );
            while ($v = db_fetch_array($choices_query)) {
                $choices[$v['id']] = $v['name'];
            }
        }

        $choices_query = db_query(
            "select * from app_global_lists_choices where lists_id ='{$lists_id}' and is_active=1 and parent_id={$current_choice_id} order by sort_order, name"
        );
        if (!db_num_rows($choices_query) and $app_global_choices_cache[$current_choice_id]['parent_id'] == 0) {
            $choices_query = db_query(
                "select * from app_global_lists_choices where lists_id ='{$lists_id}' and is_active=1 and parent_id=0 " . (count(
                    $exclude_ids
                ) ? " and id not in (" . implode(',', $exclude_ids) . ")" : "") . " order by sort_order, name"
            );
        }
        while ($v = db_fetch_array($choices_query)) {
            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }
}