<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

if (!app_session_is_registered('kanban_entity_filter')) {
    $kanban_entity_filter = 0;
    app_session_register('kanban_entity_filter');
}

switch ($app_module_action) {
    case 'set_reports_filter':
        $kanban_entity_filter = $_POST['reports_filter'];

        redirect_to('ext/kanban/reports');
        break;
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'width' => $_POST['width'],
            'heading_template' => $_POST['heading_template'],
            'entities_id' => $_POST['entities_id'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'group_by_field' => $_POST['group_by_field'],
            'sum_by_field' => (isset($_POST['sum_by_field']) ? implode(',', $_POST['sum_by_field']) : ''),
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'exclude_choices' => (isset($_POST['exclude_choices']) ? implode(',', $_POST['exclude_choices']) : ''),
        ];


        if (isset($_GET['id'])) {
            db_perform('app_ext_kanban', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_kanban', $sql_data);
        }

        redirect_to('ext/kanban/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_kanban', $_GET['id']);

        db_delete_row('app_ext_kanban', $_GET['id']);

        $report_info_query = db_query(
            "select * from app_reports where reports_type='kanban" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/kanban/reports');
        break;

    case 'get_entities_fields_choices':
        $html = '';
        $field_query = db_query("select * from app_fields where id='" . _post::int('fields_id') . "'");
        if ($field = db_fetch_array($field_query)) {
            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_kanban', $_POST['id']);
            } else {
                $obj = db_show_columns('app_ext_kanban');
            }

            $cfg = new fields_types_cfg($field['configuration']);


            if ($app_entities_cache[$field['entities_id']]['parent_id'] == 0) {
                $html .= '
	        	<div class="form-group">
					  	<label class="col-md-3 control-label" for="in_menu">' . TEXT_IN_MENU . '</label>
					    <div class="col-md-9">
					  	  <div class="checkbox-list"><label class="checkbox-inline">' . input_checkbox_tag(
                        'in_menu',
                        '1',
                        ['checked' => $obj['in_menu']]
                    ) . '</label></div>
					    </div>
					  </div>
	        ';
            }

            if ($cfg->get('use_global_list') > 0) {
                $choices = global_lists::get_choices($cfg->get('use_global_list'));
            } else {
                $choices = fields_choices::get_choices($field['id']);
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
            $obj = db_find('app_ext_kanban', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_kanban');
        }

        $html = '';


        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_stages','fieldtype_dropdown','fieldtype_autostatus','fieldtype_radioboxes') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
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

        $exclude_types = [
            "'fieldtype_action'",
            "'fieldtype_parent_item_id'",
            "'fieldtype_related_records'",
            "'fieldtype_mapbbcode'",
            "'fieldtype_section'",
            "'fieldtype_attachments'"
        ];
        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . implode(
                ",",
                $exclude_types
            ) . ") and  f.entities_id='" . db_input(
                $entities_id
            ) . "' and  f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ) . ' (#' . $fields['id'] . ')';
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_FIELDS_IN_LISTING . '</label>
            <div class="col-md-9">
          	   ' . select_tag(
                'fields_in_listing[]',
                $choices,
                $obj['fields_in_listing'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'multiple' => 'multiple',
                    'chosen_order' => $obj['fields_in_listing']
                ]
            ) . '               
            </div>
          </div>
        ';

        echo $html;

        exit();
        break;
}