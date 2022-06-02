<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'type' => $_POST['type'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'group_by_field' => $_POST['group_by_field'],
            'sum_by_field' => (isset($_POST['sum_by_field']) ? implode(',', $_POST['sum_by_field']) : ''),
            'exclude_choices' => (isset($_POST['exclude_choices']) ? implode(',', $_POST['exclude_choices']) : ''),
            'hide_zero_values' => (isset($_POST['hide_zero_values']) ? $_POST['hide_zero_values'] : 0),
            'colors' => (isset($_POST['colors']) and is_array($_POST['colors'])) ? json_encode($_POST['colors']) : '',
        ];


        if (isset($_GET['id'])) {
            db_perform('app_ext_funnelchart', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_funnelchart', $sql_data);
        }

        redirect_to('ext/funnelchart/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_funnelchart', $_GET['id']);

        db_delete_row('app_ext_funnelchart', $_GET['id']);

        $report_info_query = db_query(
            "select * from app_reports where reports_type='funnelchart" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/funnelchart/reports');
        break;

    case 'get_entities_fields_choices':
        $html = '';
        $field_query = db_query("select * from app_fields where id='" . _post::int('fields_id') . "'");
        if ($field = db_fetch_array($field_query)) {
            $entity_info = db_find('app_entities', $field['entities_id']);

            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_funnelchart', $_POST['id']);
            } else {
                $obj = db_show_columns('app_ext_funnelchart');
            }

            $cfg = new fields_types_cfg($field['configuration']);

            if ($field['type'] == 'fieldtype_entity') {
                $choices = funnelchart::get_choices_by_entity($cfg->get('entity_id'));
            } elseif ($field['type'] == 'fieldtype_parent_item_id') {
                $choices = funnelchart::get_choices_by_entity($entity_info['parent_id']);
            } elseif ($field['type'] == 'fieldtype_users' or $field['type'] == 'fieldtype_users_ajax') {
                $choices = users::get_choices_by_entity($field['entities_id']);
            } elseif ($field['type'] == 'fieldtype_created_by') {
                $choices = users::get_choices_by_entity($field['entities_id'], 'create');
            } else {
                if ($cfg->get('use_global_list') > 0) {
                    $choices = global_lists::get_choices($cfg->get('use_global_list'), false);
                } else {
                    $choices = fields_choices::get_choices($field['id'], false);
                }

                //choices colors
                $colors_html = '';
                foreach ($choices as $id => $name) {
                    $colors_html .= '
                        <tr>
                            <td>' . $name . '</td>
                            <td>' . input_color(
                            'colors[' . $id . ']',
                            funnelchart::get_color_by_choice_id($id, $obj['colors'])
                        ) . '</td>                            
                        </tr>';
                }

                $html .= '
                <div class="form-group">
                    <label class="col-md-3 control-label">' . TEXT_COLOR . '</label>
                    <div class="col-md-9">
                           <table>' . $colors_html . '</table>
                    </div>
                 </div>
               ';
            }

            $html .= '
                <div class="form-group">
                       <label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_EXCLUDE_CHOICES . '</label>
                   <div class="col-md-9">
                          ' . select_tag(
                    'exclude_choices[]',
                    $choices,
                    $obj['exclude_choices'],
                    ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
                ) . '
                   </div>
                 </div>
               ';
        }

        echo $html;
        exit();
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_funnelchart', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_funnelchart');
        }

        $html = '';

        if ($entities_info['parent_id'] > 0) {
            $html .= '
        		<div class="form-group">
        		<label class="col-md-3 control-label" for="in_menu">' . tooltip_icon(
                    TEXT_EXT_IN_MENU_SUBENTITY_REPORT
                ) . TEXT_IN_MENU . '</label>
        	    <div class="col-md-9">	
        	  	  <div class="checkbox-list"><label class="checkbox-inline">' . input_checkbox_tag(
                    'in_menu',
                    '1',
                    ['checked' => $obj['in_menu']]
                ) . '</label></div>
        	    </div>			
        	  </div>';
        }

        $choices = [];
        $fields_query = db_query(
            "select f.*, if(f.type in (" . fields_types::get_reserverd_data_types_list(
            ) . "),-1,t.sort_order) as tab_sort_order from app_fields f,  app_forms_tabs t where f.forms_tabs_id=t.id  and f.type in ('fieldtype_stages','fieldtype_dropdown','fieldtype_autostatus','fieldtype_radioboxes','fieldtype_users','fieldtype_entity', 'fieldtype_entity_ajax','fieldtype_grouped_users','fieldtype_dropdown_multiple','fieldtype_checkboxes','fieldtype_created_by'" . ($entities_info['parent_id'] > 0 ? ",'fieldtype_parent_item_id'" : '') . ") and f.entities_id='" . db_input(
                $entities_id
            ) . "' order by tab_sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            if ($fields['type'] == 'fieldtype_parent_item_id') {
                $choices[$fields['id']] = TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE . ' (' . entities::get_name_by_id(
                        $entities_info['parent_id']
                    ) . ')';
            } elseif ($fields['type'] == 'fieldtype_created_by') {
                $choices[$fields['id']] = TEXT_FIELDTYPE_CREATEDBY_TITLE;
            } else {
                $choices[$fields['id']] = $fields['name'];
            }
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GROUP_BY_FIELD . '</label>
            <div class="col-md-9">	
          	   ' . select_tag(
                'group_by_field',
                $choices,
                $obj['group_by_field'],
                ['class' => 'form-control input-large required', 'onChange' => 'ext_get_entities_fields_choices()']
            ) . '
               ' . tooltip_text(TEXT_EXT_GROUP_BY_FIELD_INFO) . '
            </div>			
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="hide_zero_values">' . TEXT_EXT_HIDE_ZERO_VALUES . '</label>
            <div class="col-md-9">
          	   <p class="form-control-static">' . input_checkbox_tag(
                'hide_zero_values',
                1,
                ['checked' => $obj['hide_zero_values']]
            ) . '</p>               	                  
            </div>
          </div>
        ';


        $html .= '<div id="fields_chocies_list"></div>';

        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_numeric','fieldtype_input_numeric_comments','fieldtype_formula','fieldtype_js_formula','fieldtype_mysql_query','fieldtype_days_difference','fieldtype_hours_difference') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_SUM_BY_FIELD . '</label>
            <div class="col-md-9">
          	   ' . select_tag(
                'sum_by_field[]',
                $choices,
                $obj['sum_by_field'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
               ' . tooltip_text(TEXT_EXT_SUM_BY_FIELD_INFO) . '
            </div>
          </div>
        ';

        echo $html;

        exit();
        break;
}