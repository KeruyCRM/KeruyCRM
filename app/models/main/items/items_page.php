<?php

namespace Models\Main\Items;

class Items_page
{
    public $items_id;
    public $entities_id;
    public $entity_cfg;
    public $fields_access_schema;
    public $fields_display_rules;
    public $item;

    public function __construct($entities_id, $items_id)
    {
        global $app_user, $current_item_info;

        $this->items_id = $items_id;
        $this->entities_id = $entities_id;

        $this->entity_cfg = new entities_cfg($this->entities_id);
        $this->fields_access_schema = users::get_fields_access_schema($this->entities_id, $app_user['group_id']);
        $this->fields_display_rules = [];

        $item_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $this->entities_id,
                ''
            ) . " from app_entity_" . $this->entities_id . " e where id='" . $this->items_id . "'",
            false
        );
        $current_item_info = $this->item = db_fetch_array($item_query);
    }

    public function render($type)
    {
        switch ($type) {
            case 'one_column_tabs':
                return $this->render_tabs();
                break;
            case 'one_column_accordion':
                return $this->render_accordion();
                break;
        }
    }

    public function get_tabs()
    {
        $tabs_list = [];
        $tabs_query = db_fetch_all(
            'app_forms_tabs',
            "entities_id='" . db_input($this->entities_id) . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            if (strlen($html = $this->get_tab_fields($tabs['id']))) {
                $tabs_list[] = [
                    'id' => $tabs['id'],
                    'name' => $tabs['name'],
                    'is_folder' => $tabs['is_folder'],
                    'parent_id' => $tabs['parent_id'],
                    'html' => $html
                ];
            } elseif ($tabs['is_folder']) {
                $tabs_list[] = [
                    'id' => $tabs['id'],
                    'name' => $tabs['name'],
                    'is_folder' => $tabs['is_folder'],
                    'parent_id' => $tabs['parent_id'],
                    'html' => ''
                ];
            }
        }

        return $tabs_list;
    }

    public function get_tab_fields($tabs_id)
    {
        global $current_path;

        $html = '';
        $html_fields = '';

        $fields_query = db_query(
            "select f.*,fr.sort_order as form_rows_sort_order,right(f.forms_rows_position,1) as forms_rows_pos, t.name as tab_name, if(f.type in ('fieldtype_id','fieldtype_date_added','fieldtype_date_updated','fieldtype_created_by'),-1,t.sort_order) as tab_sort_order from app_fields f left join app_forms_rows fr on fr.id=LEFT(f.forms_rows_position,length(f.forms_rows_position)-2), app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_related_records','fieldtype_parent_item_id')  and f.entities_id='" . db_input(
                $this->entities_id
            ) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input(
                $tabs_id
            ) . "' order by tab_sort_order, t.name, form_rows_sort_order, forms_rows_pos, f.sort_order, f.name",
            false
        );
        while ($field = db_fetch_array($fields_query)) {
            //check field access
            if (isset($this->fields_access_schema[$field['id']])) {
                if ($this->fields_access_schema[$field['id']] == 'hide') {
                    continue;
                }
            }

            //prepare field value
            $value = items::prepare_field_value_by_type($field, $this->item);

            $output_options = [
                'class' => $field['type'],
                'value' => $value,
                'field' => $field,
                'item' => $this->item,
                'display_user_photo' => true,
                'path' => $current_path,
            ];

            $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

            //hide if empty
            if ($cfg->get('hide_field_if_empty') == 1 and fields_types::is_empty_value($value, $field['type'])) {
                continue;
            }

            //hide if date updated empty
            if ($field['type'] == 'fieldtype_date_updated' and $value == 0) {
                continue;
            }

            //check fields display rules
            $check_query = db_query("select * from app_forms_fields_rules where fields_id='" . $field['id'] . "'");
            if ($check = db_fetch_array($check_query)) {
                $is_multiple = false;

                if (in_array($field['type'], ['fieldtype_dropdown_multiple', 'fieldtype_checkboxes'])) {
                    $is_multiple = true;
                }

                if ($field['type'] == 'fieldtype_grouped_users' and in_array(
                        $cfg->get('display_as'),
                        ['checkboxes', 'dropdown_multiple']
                    )) {
                    $is_multiple = true;
                }

                if (in_array($field['type'], ['fieldtype_boolean_checkbox', 'fieldtype_boolean'])) {
                    $value = ($value == 'true' ? 1 : 0);
                }

                if (in_array(
                        $field['type'],
                        ['fieldtype_dropdown', 'fieldtype_radioboxes', 'fieldtype_stages', 'fieldtype_autostatus']
                    ) and $value == 0) {
                    $value = '';
                }

                $this->fields_display_rules[] = 'app_handle_forms_fields_display_rules(\'\',' . $field['id'] . ',"","' . (strlen(
                        $value
                    ) ? $value : '') . '",' . (int)$is_multiple . '); ';
            }

            //skip heading or hidden fields from list but inlucde fields display rules before
            if ($field['is_heading'] == 1) {
                continue;
            }

            //skip hidden fields
            if (strlen($this->entity_cfg->get('item_page_hidden_fields', '')) and in_array(
                    $field['id'],
                    explode(',', $this->entity_cfg->get('item_page_hidden_fields', ''))
                )) {
                continue;
            }

            $field_name = fields_types::get_option($field['type'], 'name', $field['name']);

            $field_name .= fields::get_item_info_tooltip($field);

            if ($field['type'] == 'fieldtype_section') {
                $html_fields .= '
                    <tr class="form-group form-group-' . $field['id'] . '">
                      <th colspan="2" class="section-heading">' . $field_name . '</th>
                    </tr>
                  ';
            } elseif ($field['type'] == 'fieldtype_dropdown_multilevel') {
                $html_fields .= fieldtype_dropdown_multilevel::output_info_box($output_options);
            } //hide field name to save space to display value
            elseif ($cfg->get('hide_field_name') == 1) {
                $html_fields .= '
                    <tr class="form-group form-group-' . $field['id'] . '">
                      <td colspan="2">' . fields_types::output($output_options) . '</td>
                    </tr>
                  ';
            } elseif ($field['type'] == 'fieldtype_users') {
                $html_fields .= '
                    <tr class="form-group form-group-' . $field['id'] . '">
                      <th colspan="2" ' . (strlen(
                        $field_name
                    ) > 25 ? 'class="white-space-normal"' : '') . '>' . $field_name . '</th>
                	  </tr>
                	  <tr class="form-group-' . $field['id'] . '">
                      <td colspan="2">' . fields_types::output($output_options) . '</td>
                    </tr>
                  ';
            } elseif ($field['type'] == 'fieldtype_mapbbcode') {
                $html_fields .= '
                    <tr class="form-group form-group-' . $field['id'] . '">
                    	<th ' . (strlen($field_name) > 25 ? 'class="white-space-normal"' : '') . '>' . $field_name . '</th>
                      <td style="width: 100%">' . fields_types::output($output_options) . '</td>
                    </tr>
                  ';
            } else {
                $field_name_html = '';

                //add dwonload All Attachments link if more then 1 files
                if ($field['type'] == 'fieldtype_attachments' and count(explode(',', $value)) > 1) {
                    $field_name_html = '<br><span class="download-all-attachments"><a style="margin-left: 0; font-weight: normal" href="' . url_for(
                            'items/info',
                            'action=download_all_attachments&id=' . $field['id'] . '&path=' . $current_path
                        ) . '"><i class="fa fa-download"></i> ' . TEXT_DOWNLOAD_ALL_ATTACHMENTS . '</a></span>';
                }

                $html_fields .= '
                    <tr class="form-group form-group-' . $field['id'] . '">
                      <th ' . (strlen($field_name) > 25 ? 'class="white-space-normal"' : '') . '>' .
                    $field_name . $field_name_html .
                    '</th>
                      <td>' . fields_types::output($output_options) . '</td>
                    </tr>
                  ';
            }
        }

        //include TAB if there are fields in list
        if (strlen($html_fields)) {
            $html .= '	      
    	      <div class="table-scrollable">
        	      <table class="table table-bordered table-hover table-item-details table-item-details-' . $this->entities_id . '">
        	      		' . $html_fields . '
        	      </table>
    	      </div>';
        }

        return $html;
    }

    public function render_fields_display_rules()
    {
        $html = '';

        if (count($this->fields_display_rules)) {
            $html .= '<script>' . implode("\n", $this->fields_display_rules) . '</script>';
        }

        return $html;
    }

    public function render_tabs()
    {
        if (!count($tabs_list = $this->get_tabs())) {
            return '';
        }

        $html = '<ul class="nav nav-tabs">';

        foreach ($tabs_list as $k => $tab) {
            if ($tab['is_folder']) {
                $html .= '
                    <li class="dropdown check-form-tabs-dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $tab['name'] . ' <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu">';

                $subtabs_query = db_query(
                    "select * from app_forms_tabs where parent_id={$tab['id']} and entities_id={$this->entities_id} order by sort_order, name"
                );
                while ($subtabs = db_fetch_array($subtabs_query)) {
                    $html .= '
                        <li class="info-tab-' . $subtabs['id'] . ' check-form-tabs" cfg_tab_id="info-tab-' . $subtabs['id'] . '">
                            <a data-toggle="tab" href="#info-tab-' . $subtabs['id'] . '">' . $subtabs['name'] . '</a>
                        </li>';
                }

                $html .= '
                        </ul>
                    </li>';
            } elseif ($tab['parent_id'] == 0) {
                $html .= '
                <li class="check-form-tabs info-tab-' . $tab['id'] . ' ' . ($k == 0 ? 'active' : '') . '" cfg_tab_id="info-tab-' . $tab['id'] . '">
                    <a href="#info-tab-' . $tab['id'] . '" data-toggle="tab">' . $tab['name'] . '</a>
                </li>
                ';
            }
        }

        $html .= '</ul>';
        $html .= '<div class="tab-content">';

        foreach ($tabs_list as $k => $tab) {
            if ($tab['is_folder']) {
                continue;
            }

            $html .= '
                <div class="tab-pane fade ' . ($k == 0 ? 'active in' : '') . '" id="info-tab-' . $tab['id'] . '">
    				' . $tab['html'] . '
    			</div>
                ';
        }

        $html .= '</div>';

        $html .= $this->render_fields_display_rules();

        return $html;
    }

    public function render_accordion()
    {
        if (!count($tabs_list = $this->get_tabs())) {
            return '';
        }

        $html = '<div class="panel-group accordion item-page-accordion" id="item_page_accordion">';

        foreach ($tabs_list as $k => $tab) {
            $html .= '
                <div class="panel panel-default panel-heading-' . $tab['id'] . ' check-form-tabs" cfg_tab_id="panel-heading-' . $tab['id'] . '">
                    <div class="panel-heading accordion-toggle" data-toggle="collapse" data-parent="#item_page_accordion" href="#panel-heading-' . $tab['id'] . '">
					   <h4 class="panel-title">' . $tab['name'] . '</h4>
                    </div>
                    <div id="panel-heading-' . $tab['id'] . '" class="panel-collapse ' . ($k == 0 ? 'in' : 'collapse') . '">
                        <div class="panel-body">' . $tab['html'] . '</div>
                    </div>    				
    			</div>
                ';
        }

        $html .= '</div>';

        $html .= $this->render_fields_display_rules();

        return $html;
    }
}