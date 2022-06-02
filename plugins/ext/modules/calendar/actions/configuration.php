<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save_personal':
        //reset access
        db_query("delete from app_ext_calendar_access where calendar_type='personal'");

        //insert access
        if (isset($_POST['allowed_groups'])) {
            foreach ($_POST['allowed_groups'] as $group_id) {
                $sql_data = ['access_groups_id' => $group_id, 'calendar_type' => 'personal', 'access_schema' => 'full'];

                db_perform('app_ext_calendar_access', $sql_data);
            }
        }

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/calendar/configuration_personal');
        break;

    case 'save_public':
        //reset access
        db_query("delete from app_ext_calendar_access where calendar_type='public'");

        //insert access
        if (isset($_POST['access'])) {
            foreach ($_POST['access'] as $group_id => $access_schema) {
                if (strlen($access_schema) > 0) {
                    $sql_data = [
                        'access_groups_id' => $group_id,
                        'calendar_type' => 'public',
                        'access_schema' => $access_schema
                    ];

                    db_perform('app_ext_calendar_access', $sql_data);
                }
            }
        }

        require(component_path('ext/ext/save_configuration'));

        $alerts->add(TEXT_CONFIGURATION_UPDATED, 'success');

        redirect_to('ext/calendar/configuration_public');
        break;

    case 'save_report':

        //check min/max dates
        $min_time = $_POST['min_time'];
        $max_time = $_POST['max_time'];

        if ((int)$min_time > (int)$max_time) {
            $max_time = '';
        }

        if (!strstr($min_time, ':00') and !strstr($min_time, ':30') and strlen($min_time)) {
            $min_time = explode(':', $min_time);
            $min_time = $min_time[0] . ':00';
        }

        if (!strstr($max_time, ':00') and !strstr($max_time, ':30') and strlen($max_time)) {
            $max_time = explode(':', $max_time);
            $max_time = $max_time[0] . ':00';
        }

        $sql_data = [
            'name' => $_POST['name'],
            'enable_ical' => $_POST['enable_ical'],
            'default_view' => $_POST['default_view'],
            'view_modes' => (isset($_POST['view_modes']) ? implode(',', $_POST['view_modes']) : ''),
            'event_limit' => $_POST['event_limit'],
            'highlighting_weekends' => (isset($_POST['highlighting_weekends']) ? implode(
                ',',
                $_POST['highlighting_weekends']
            ) : ''),
            'min_time' => $min_time,
            'max_time' => $max_time,
            'time_slot_duration' => $_POST['time_slot_duration'],
            'entities_id' => $_POST['entities_id'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'heading_template' => $_POST['heading_template'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'use_background' => $_POST['use_background'],
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'filters_panel' => $_POST['filters_panel'],

        ];


        if (isset($_GET['id'])) {
            $calendar_id = $_GET['id'];

            db_perform('app_ext_calendar', $sql_data, 'update', "id='" . db_input($calendar_id) . "'");
        } else {
            db_perform('app_ext_calendar', $sql_data);
            $calendar_id = db_insert_id();
        }

        db_query("delete from app_ext_calendar_access where calendar_id='" . db_input($calendar_id) . "'");

        foreach ($_POST['access'] as $group_id => $access) {
            if (strlen($access) == 0) {
                continue;
            }

            $sql_data = [
                'calendar_id' => $calendar_id,
                'access_groups_id' => $group_id,
                'calendar_type' => 'report',
                'access_schema' => $access,
            ];

            db_perform('app_ext_calendar_access', $sql_data);
        }

        redirect_to('ext/calendar/configuration_reports');
        break;

    case 'delete':
        $obj = db_find('app_ext_calendar', $_GET['id']);

        db_delete_row('app_ext_calendar', $_GET['id']);
        db_delete_row('app_ext_calendar_access', $_GET['id'], 'calendar_id');

        $report_info_query = db_query(
            "select * from app_reports where reports_type='calendarreport" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/calendar/configuration_reports');
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_calendar', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_calendar');
        }

        $html = '';

        if ($app_entities_cache[$entities_id]['parent_id'] > 0) {
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
            	  </div>
                ';
        }

        $start_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $entities_id
            ) . "' order by sort_order, name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $start_date_fields[$fields['id']] = ($fields['type'] == 'fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']);
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_START_DATE . '</label>
            <div class="col-md-9">	
          	   ' . select_tag(
                'start_date',
                $start_date_fields,
                $obj['start_date'],
                ['class' => 'form-control input-large required']
            ) . '               
            </div>			
          </div>
        ';

        $end_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $entities_id
            ) . "' order by sort_order, name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $end_date_fields[$fields['id']] = ($fields['type'] == 'fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']);
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_END_DATE . '</label>
            <div class="col-md-9">	
          	   ' . select_tag(
                'end_date',
                $end_date_fields,
                $obj['end_date'],
                ['class' => 'form-control input-large required']
            ) . '               
            </div>			
          </div>
        ';


        $html .= '
            <div class="form-group">
            	<label class="col-md-3 control-label" for="name">' . tooltip_icon(
                TEXT_ENTER_TEXT_PATTERN_INFO
            ) . TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper($entities_id, 'heading_template') . '</label>
              <div class="col-md-9">
            	  ' . input_tag('heading_template', $obj['heading_template'], ['class' => 'form-control input-large']) .
            tooltip_text(TEXT_HEADING_TEMPLATE_INFO) . '
              </div>
            </div>
            ';


        $use_fields = [];
        $use_fields[''] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_stages','fieldtype_autostatus','fieldtype_grouped_users') and entities_id='" . db_input(
                $entities_id
            ) . "' order by sort_order, name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $use_fields[$fields['id']] = $fields['name'];
        }

        if (count($use_fields)) {
            $html .= '
	         <div class="form-group">
	          	<label class="col-md-3 control-label" for="allowed_groups">' . tooltip_icon(
                    TEXT_EXT_USE_BACKGROUND_INFO
                ) . TEXT_EXT_USE_BACKGROUND . '</label>
	            <div class="col-md-9">
	          	   ' . select_tag(
                    'use_background',
                    $use_fields,
                    $obj['use_background'],
                    ['class' => 'form-control input-large']
                ) . '
	            </div>
	          </div>
	        ';
        }

        $html .= '
	         <div class="form-group">
	          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_FIELDS_IN_POPUP_TOOLTIP . '</label>
	            <div class="col-md-9">
	          	   ' . select_tag(
                'fields_in_popup[]',
                fields::get_choices($entities_id),
                $obj['fields_in_popup'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
	            </div>
	          </div>
	        ';


        echo $html;

        exit();
        break;
}