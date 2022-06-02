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
            'shape' => $_POST['shape'],
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'fields_id' => (isset($_POST['fields_id']) ? $_POST['fields_id'] : 0),
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'use_background' => $_POST['use_background'],
            'icons' => (isset($_POST['icons']) ? json_encode($_POST['icons']) : 0),
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_mind_map', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_mind_map', $sql_data);
        }

        redirect_to('ext/mind_map_reports/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_mind_map', $_GET['id']);

        db_delete_row('app_ext_mind_map', $_GET['id']);

        db_delete_row('app_mind_map', $_GET['id'], 'reports_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/mind_map_reports/reports');
        break;

    case 'get_entities_fields_icons':

        $entities_id = $_POST['entities_id'];
        $fields_id = $_POST['fields_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_mind_map', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_mind_map');
        }

        $html = '';

        $field_info_query = db_query("select * from app_fields where id='" . $fields_id . "'");
        if ($field_info = db_fetch_array($field_info_query)) {
            $html .= '
	        	<div class="form-group">
					  	<label class="col-md-4 control-label" for="in_menu">' . TEXT_ICONS . '</label>
					    <div class="col-md-8">';

            $cfg = new fields_types_cfg($field_info['configuration']);
            if ($cfg->get('use_global_list') > 0) {
                $choices_query = db_query(
                    "select * from app_global_lists_choices where lists_id = '" . db_input(
                        $cfg->get('use_global_list')
                    ) . "'"
                );
            } else {
                $choices_query = db_query(
                    "select * from app_fields_choices where fields_id = '" . db_input($fields_id) . "'"
                );
            }

            $icon = (strlen($obj['icons']) ? json_decode($obj['icons'], true) : []);

            $html .= '<table>';

            while ($choices = db_fetch_array($choices_query)) {
                $html .= '
	  			<tr>
	  				<td>' . input_tag(
                        'icons[' . $choices['id'] . ']',
                        (isset($icon[$choices['id']]) ? $icon[$choices['id']] : ''),
                        ['class' => 'form-control input-small']
                    ) . '</td>
	  				<td>&nbsp;' . $choices['name'] . '</td>	  				
	  			</tr>	
	  		';
            }

            $html .= '
	  			</table>
	  			' . tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) . '
  			 </div>			
				</div>		
	    ';

            echo $html;
        }
        exit();
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_mind_map', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_mind_map');
        }

        $html = '';

        if ($app_entities_cache[$entities_id]['parent_id'] == 0) {
            $html .= '
	        	<div class="form-group">
					  	<label class="col-md-4 control-label" for="in_menu">' . TEXT_IN_MENU . '</label>
					    <div class="col-md-8">	
					  	  <div class="checkbox-list"><label class="checkbox-inline">' . input_checkbox_tag(
                    'in_menu',
                    '1',
                    ['checked' => $obj['in_menu']]
                ) . '</label></div>
					    </div>			
					  </div>		
	        ';
        }

        $exclude_types = [
            "'fieldtype_image_ajax'",
            "'fieldtype_image'",
            "'fieldtype_attachments'",
            "'fieldtype_action'",
            "'fieldtype_parent_item_id'",
            "'fieldtype_related_records'",
            "'fieldtype_mapbbcode'",
            "'fieldtype_section'",
            "'fieldtype_attachments'"
        ];
        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type not in (" . implode(
                ",",
                $exclude_types
            ) . ") and entities_id='" . db_input($entities_id) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="allowed_groups">' . TEXT_FIELDS_IN_POPUP . '</label>
            <div class="col-md-8">
          	   ' . select_tag(
                'fields_in_popup[]',
                $choices,
                $obj['fields_in_popup'],
                ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
            ) . '
            </div>
          </div>
        ';

        $use_fields = [];
        $use_fields[''] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_autostatus','fieldtype_stages') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $use_fields[$fields['id']] = $fields['name'];
        }

        if (count($use_fields)) {
            $html .= '
	         <div class="form-group">
	          	<label class="col-md-4 control-label" for="allowed_groups">' . tooltip_icon(
                    TEXT_EXT_USE_BACKGROUND_INFO
                ) . TEXT_EXT_USE_BACKGROUND . '</label>
	            <div class="col-md-8">
	          	   ' . select_tag(
                    'use_background',
                    $use_fields,
                    $obj['use_background'],
                    [
                        'class' => 'form-control input-large',
                        'onChange' => 'ext_get_entities_fields_icons(' . $entities_id . ',this.value)'
                    ]
                ) . '
	            </div>
	          </div>
	          	   		
	         <div id="reports_entities_fields_icons"></div> 	   		
	        ';
        }

        echo $html;

        exit();
        break;
}