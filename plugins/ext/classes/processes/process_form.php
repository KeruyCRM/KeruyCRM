<?php

class process_form
{
    function __construct($process_id)
    {
        $this->process_id = $process_id;
        $this->process_fields = [];
        $this->process_fields_in_tabs = [];

        $this->enter_manually_use_value = [];
        $this->app_process_info = [];

        $this->set_process_fields();
    }

    function count_process_fields()
    {
        return count($this->process_fields);
    }

    function is_field_in_tab($field_id)
    {
        return in_array($field_id, $this->process_fields_in_tabs);
    }

    function is_field_in_form($field_id)
    {
        return isset($this->process_fields[$field_id]);
    }

    function set_process_fields()
    {
        $fields_query = "select af.fields_id from app_ext_processes_actions_fields af,app_ext_processes_actions pa where af.actions_id=pa.id and af.enter_manually in (1,2) and af.actions_id in (select pa2.id from app_ext_processes_actions pa2 where pa2.process_id='" . $this->process_id . "') order by pa.sort_order";
        $fields_query = "select f.* from app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id in ({$fields_query}) order by f.entities_id, t.sort_order, t.name, f.sort_order, f.name";
        $fields_query = db_query($fields_query);
        while ($field = db_fetch_array($fields_query)) {
            $this->process_fields[$field['id']] = $field;
        }

        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . $this->process_id . "'  order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (strlen($tabs['fields'])) {
                $this->process_fields_in_tabs = array_merge(
                    $this->process_fields_in_tabs,
                    explode(',', $tabs['fields'])
                );
            }


            $rows_query = db_query(
                "select * from app_ext_process_form_rows where process_id='" . $this->process_id . "' and forms_tabs_id='" . $tabs['id'] . "'  order by sort_order",
                false
            );
            while ($rows = db_fetch_array($rows_query)) {
                if (strlen($rows['column1_fields'])) {
                    $this->process_fields_in_tabs = array_merge(
                        $this->process_fields_in_tabs,
                        explode(',', $rows['column1_fields'])
                    );
                }

                if (strlen($rows['column2_fields'])) {
                    $this->process_fields_in_tabs = array_merge(
                        $this->process_fields_in_tabs,
                        explode(',', $rows['column2_fields'])
                    );
                }

                if (strlen($rows['column3_fields'])) {
                    $this->process_fields_in_tabs = array_merge(
                        $this->process_fields_in_tabs,
                        explode(',', $rows['column3_fields'])
                    );
                }

                if (strlen($rows['column4_fields'])) {
                    $this->process_fields_in_tabs = array_merge(
                        $this->process_fields_in_tabs,
                        explode(',', $rows['column4_fields'])
                    );
                }
            }
        }
    }

    function set_current_item($current_entity_id, $current_item_id, $parent_entity_id, $parent_entity_item_id)
    {
        $this->current_entity_id = $current_entity_id;
        $this->current_item_id = $current_item_id;
        $this->parent_entity_id = $parent_entity_id;
        $this->parent_entity_item_id = $parent_entity_item_id;

        if ($this->current_item_id) {
            $this->item_info = db_find('app_entity_' . $this->current_entity_id, $this->current_item_id);
            $this->item_access_rules = new access_rules($this->current_entity_id, $this->item_info);
        }
    }

    function render_form()
    {
        $html = '';

        //tab navigation
        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . $this->process_id . "' order by  sort_order, name"
        );
        if (db_num_rows($tabs_query) > 1) {
            $html .= '<ul class="nav nav-tabs">';
            while ($tabs = db_fetch_array($tabs_query)) {
                $is_active = !isset($is_active) ? true : false;
                $html .= '<li class="form_tab_' . $tabs['id'] . ' ' . ($is_active ? 'active' : '') . ' check-form-tabs" cfg_tab_id="form_tab_' . $tabs['id'] . '"><a href="#form_tab_' . $tabs['id'] . '"  data-toggle="tab">' . $tabs['name'] . '</a></li>';
            }
            $html .= '</ul>';
        }

        //tab content
        $html .= '            
            <div class="tab-content">
            ';

        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . $this->process_id . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            $is_active_in = !isset($is_active_in) ? true : false;
            $html .= '
                <div class="tab-pane fade ' . ($is_active_in ? 'active in' : '') . '" id="form_tab_' . $tabs['id'] . '">' .
                (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '') .
                $this->render_form_fields($tabs['fields']) .
                $this->render_form_rows($tabs['id']) .
                '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    function render_form_rows($tab_id)
    {
        $html = '';

        $rows_query = db_query(
            "select * from app_ext_process_form_rows where process_id='" . $this->process_id . "' and forms_tabs_id='" . $tab_id . "' order by sort_order",
            false
        );
        while ($rows = db_fetch_array($rows_query)) {
            $html .= '<div class="row forms-rows">';

            for ($i = 1; $i <= $rows['columns']; $i++) {
                $html .= '<div class="col-md-' . $rows['column' . $i . '_width'] . '">' . $this->render_form_fields(
                        $rows['column' . $i . '_fields'],
                        $rows['field_name_new_row']
                    ) . '</div>';
            }

            $html .= '</div>';
        }

        return $html;
    }

    function render_form_fields($fields, $field_name_new_row = 3)
    {
        global $app_user;

        if (!strlen($fields)) {
            return '';
        }

        $html = '';
        $fields_query = db_query("select * from app_fields where id in ({$fields}) order by field(id,{$fields})");
        while ($v = db_fetch_array($fields_query)) {
            if (!$this->is_field_in_form($v['id'])) {
                continue;
            }

            $entity_info = db_find('app_entities', $v['entities_id']);

            $fields = $v;

            $fields_access_schema = users::get_fields_access_schema($v['entities_id'], $app_user['group_id']);
            if ($this->current_entity_id == $v['entities_id'] and $this->current_item_id) {
                $access_rules = new access_rules($this->current_entity_id, $this->item_info);
                $fields_access_schema += $access_rules->get_fields_view_only_access();
            }

            //skip fields if no edit access
            if (isset($fields_access_schema[$v['id']]) and $this->app_process_info['apply_fields_access_rules'] == 1) {
                continue;
            }

            if ($this->current_entity_id == $v['entities_id'] and $this->current_item_id) {
                $obj = $this->item_info;
            } else {
                $obj = db_show_columns('app_entity_' . $v['entities_id']);
            }


            //prepare parent_entity_item_id that will be using for entity field type

            $use_parent_entity_item_id = 0;

            //use parent item id if parent entity the same
            if ($this->parent_entity_id == $entity_info['parent_id']) {
                $use_parent_entity_item_id = $this->parent_entity_item_id;
            }

            //use curent item id as parent 
            if ($this->current_entity_id == $entity_info['parent_id']) {
                $use_parent_entity_item_id = $this->current_entity_id;
            }

            if ($this->parent_entity_id == $entity_info['id'] and $this->parent_entity_item_id) {
                $obj = db_find('app_entity_' . $entity_info['id'], $this->parent_entity_item_id);
            }

            //handle enter manually with value
            if (isset($this->enter_manually_use_value[$fields['id']])) {
                $actions_fields_value = $this->enter_manually_use_value[$fields['id']];
                switch ($fields['type']) {
                    case 'fieldtype_input_date':
                        $obj['field_' . $fields['id']] = ($actions_fields_value == ' ' ? 0 : (strlen(
                            $actions_fields_value
                        ) < 5 ? get_date_timestamp(
                            date('Y-m-d', strtotime($actions_fields_value . ' day'))
                        ) : $actions_fields_value));
                        break;
                    case 'fieldtype_input_datetime':
                        $obj['field_' . $fields['id']] = ($actions_fields_value == ' ' ? 0 : (strlen(
                            $actions_fields_value
                        ) < 5 ? strtotime($actions_fields_value . ' day') : $actions_fields_value));
                        break;
                    default:
                        $obj['field_' . $fields['id']] = $actions_fields_value;
                        break;
                }
            }


            switch ($field_name_new_row) {
                case 1:
                    $label_widht = '12';
                    $field_widht = '12';
                    break;
                case 0:
                    $label_widht = '4';
                    $field_widht = '8';
                    break;
                default:
                    $label_widht = '3';
                    $field_widht = '9';
                    break;
            }

            $html .= '
	          <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . ' form-group-entity-' . $v['entities_id'] . '">
	          	<label class="col-md-' . $label_widht . ' control-label" for="fields_' . $v['id'] . '">' .
                ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                fields_types::get_option($v['type'], 'name', $v['name']) .
                '</label>
	            <div class="col-md-' . $field_widht . '">
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render(
                    $v['type'],
                    $v,
                    $obj,
                    [
                        'parent_entity_item_id' => $use_parent_entity_item_id,
                        'form' => 'item',
                        'is_new_item' => ($this->current_entity_id == $v['entities_id'] ? false : true)
                    ]
                ) . '</div>
	              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
	            </div>
	          </div>
	        ';
        }

        return $html;
    }

    static function has_editable_fields($process_id)
    {
        $check_query = db_query(
            "select af.fields_id from app_ext_processes_actions_fields af,app_ext_processes_actions pa where af.actions_id=pa.id and af.enter_manually in (1,2) and af.actions_id in (select pa2.id from app_ext_processes_actions pa2 where pa2.process_id='" . $process_id . "') limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_tab_max_sort_order($process_id)
    {
        $v = db_fetch_array(
            db_query(
                "select max(sort_order) as max_sort_order from app_ext_process_form_tabs where process_id  = '" . db_input(
                    $process_id
                ) . "'"
            )
        );

        return $v['max_sort_order'];
    }

}
