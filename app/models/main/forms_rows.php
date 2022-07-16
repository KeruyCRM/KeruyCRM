<?php

namespace Models\Main;

class Forms_rows
{
    public $entities_id, $tabs_id, $fields_access_schema, $obj, $is_new_item, $parent_entity_item_id, $hidden_fields, $excluded_fileds_types;

    function __construct($entities_id, $tabs_id)
    {
        $this->entities_id = $entities_id;
        $this->tabs_id = $tabs_id;
        $this->fields_access_schema = [];
        $this->obj = [];
        $this->is_new_item = true;
        $this->hidden_fields = '';
        $this->excluded_fileds_types = '';
    }

    function render()
    {
        $obj = $this->obj;

        $html = '';

        /*$rows_query = db_query(
            "select * from app_forms_rows where entities_id='" . $this->entities_id . "' and forms_tabs_id='" . $this->tabs_id . "' order by sort_order",
            false
        );*/

        $rows_query = \K::model()->db_fetch('app_forms_rows', [
            'entities_id = ? and forms_tabs_id = ?',
            $this->entities_id,
            $this->tabs_id
        ], ['order' => 'sort_order']);

        //while ($rows = db_fetch_array($rows_query)) {
        foreach ($rows_query as $rows) {
            $rows = $rows->cast();

            $html .= '<div class="row forms-rows">';

            for ($i = 1; $i <= $rows['columns']; $i++) {
                $html .= '<div class="col-md-' . $rows['column' . $i . '_width'] . '">';

                $where_sql = (strlen(
                    $this->hidden_fields
                ) ? " and f.id not in (" . $this->hidden_fields . ")" : '');

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
                    ) . (strlen(
                        $this->excluded_fileds_types
                    ) ? ',' . $this->excluded_fileds_types : '') . ") and  f.entities_id = ? and f.forms_tabs_id = t.id and f.forms_tabs_id = ? and f.forms_rows_position = ? {$where_sql} order by t.sort_order, t.name, f.sort_order, f.name",
                    [
                        $this->entities_id,
                        $this->tabs_id,
                        $rows['id'] . ":" . $i
                    ]
                );

                //while ($v = db_fetch_array($fields_query)) {
                foreach ($fields_query as $v) {
                    //check field access
                    if (isset($this->fields_access_schema[$v['id']])) {
                        continue;
                    }

                    $label_width = '4';
                    $field_width = '8';

                    //handle params from GET
                    if (isset(\K::$fw->GET['fields'][$v['id']])) {
                        $obj['field_' . $v['id']] = \K::model()->db_prepare_input(\K::$fw->GET['fields'][$v['id']]);
                    }

                    if ($v['type'] == 'fieldtype_section') {
                        $html_field = '<div class="form-group-' . $v['id'] . '">' . \Models\Main\Fields_types::render(
                                $v['type'],
                                $v,
                                $obj,
                                ['count_fields' => 0]
                            ) . '</div>';
                    } elseif ($v['type'] == 'fieldtype_dropdown_multilevel') {
                        $html_field = \Models\Main\Fields_types::render(
                            $v['type'],
                            $v,
                            $obj,
                            [
                                'parent_entity_item_id' => $this->parent_entity_item_id,
                                'form' => 'item',
                                'is_new_item' => $this->is_new_item
                            ]
                        );
                    } else {
                        $v['is_required'] = (in_array(
                            $v['type'],
                            [
                                'fieldtype_user_firstname',
                                'fieldtype_user_lastname',
                                'fieldtype_user_username',
                                'fieldtype_user_email'
                            ]
                        ) ? 1 : $v['is_required']);

                        if ($rows['field_name_new_row'] == 1) {
                            $label_width = '12';
                            $field_width = '12';
                        } else {
                            $label_width = '4';
                            $field_width = '8';
                        }

                        $html_field = '
                    	          <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
                    	          	<label class="col-md-' . $label_width . ' control-label" for="fields_' . $v['id'] . '">' .
                            ($v['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                            ($v['tooltip_display_as'] == 'icon' ? \Helpers\App::tooltip_icon($v['tooltip']) : '') .
                            fields_types::get_option($v['type'], 'name', $v['name']) .
                            '</label>
                    	            <div class="col-md-' . $field_width . '">
                    	          	  <div id="fields_' . $v['id'] . '_rendered_value">' .
                            fields_types::render($v['type'], $v, $obj, [
                                'parent_entity_item_id' => $this->parent_entity_item_id,
                                'form' => 'item',
                                'is_new_item' => $this->is_new_item,
                                'is_form_row' => true,
                            ]) .
                            '</div>
                    	              ' . ($v['tooltip_display_as'] != 'icon' ? \Helpers\App::tooltip_text(
                                $v['tooltip']
                            ) : '') . '
                    	            </div>
                    	          </div>
                    	        ';
                    }

                    $html .= $html_field;

                    //including user password field for new user form
                    if ($v['type'] == 'fieldtype_user_username' and (int)$obj['id'] == 0) {
                        $html .= '
                                <div class="form-group form-group-' . $v['id'] . ' form-group-' . $v['type'] . '">
                                    <label class="col-md-' . $label_width . ' control-label" for="password"><span class="required-label">*</span>' . \K::$fw->TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
                                    <div class="col-md-' . $field_width . '">	
                                          ' . \Helpers\Html::input_password_tag(
                                'password',
                                ['class' => 'form-control input-medium', 'autocomplete' => 'off']
                            ) . '
                                      ' . \Helpers\App::tooltip_text(\K::$fw->TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
                                    </div>			
                                </div>                
                              ';
                    }
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        }

        return $html;
    }
}