<?php

$calendar_info_query = db_query("select * from app_ext_resource_timeline where id='" . _get::int('calendars_id') . "'");
if (!$calendar_info = db_fetch_array($calendar_info_query)) {
    redirect_to('ext/resource_timeline/reports');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'calendars_id' => $calendar_info['id'],
            'entities_id' => $_POST['entities_id'],
            'related_entity_field_id' => ($_POST['related_entity_field_id'] ?? 0),
            'heading_template' => $_POST['heading_template'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'use_background' => $_POST['use_background'],
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'bg_color' => $_POST['bg_color'],
        ];


        if (isset($_GET['id'])) {
            $calendars_entities_id = _get::int('id');

            $obj = db_find('app_ext_resource_timeline_entities', $calendars_entities_id);
            if ($obj['entities_id'] != $_POST['entities_id']) {
                reports::delete_reports_by_type('resource_timeline_entities' . $calendars_entities_id);
                reports::auto_create_report_by_type(
                    $_POST['entities_id'],
                    'resource_timeline_entities' . $calendars_entities_id
                );
            }

            db_perform(
                'app_ext_resource_timeline_entities',
                $sql_data,
                'update',
                "id='" . db_input(_get::int('id')) . "'"
            );
        } else {
            db_perform('app_ext_resource_timeline_entities', $sql_data);
            $calendars_entities_id = db_insert_id();

            reports::auto_create_report_by_type(
                $_POST['entities_id'],
                'resource_timeline_entities' . $calendars_entities_id
            );
        }


        redirect_to('ext/resource_timeline/entities', 'calendars_id=' . $calendar_info['id']);
        break;

    case 'delete':

        db_delete_row('app_ext_resource_timeline_entities', _get::int('id'));

        reports::delete_reports_by_type('resource_timeline_entities' . _GET('id'));


        redirect_to('ext/resource_timeline/entities', 'calendars_id=' . $calendar_info['id']);
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];
        $html = '';

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_resource_timeline_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_resource_timeline_entities');
        }


        $entity_fields_types = [
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_entity_multilevel',
        ];

        if ($calendar_info['entities_id'] == 1) {
            $entity_fields_types = array_merge($entity_fields_types, [
                'fieldtype_users',
                'fieldtype_users_ajax',
                'fieldtype_user_roles',
                'fieldtype_users_approve',
                'fieldtype_created_by',
            ]);
        }

        //print_rr($entity_fields_types);

        $entity_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('" . implode(
                "','",
                $entity_fields_types
            ) . "') and entities_id='" . db_input($entities_id) . "' order by sort_order, name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $entity_fields[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $resource_entity_name = $app_entities_cache[$calendar_info['entities_id']]['name'];


        if ($app_entities_cache[$entities_id]['parent_id'] != $calendar_info['entities_id']) {
            $html .= '
             <div class="form-group">
                    <label class="col-md-3 control-label" for="allowed_groups">' . sprintf(
                    TEXT_EXT_RELATED_ENTITY_FIELD,
                    $resource_entity_name
                ) . '</label>
                <div class="col-md-9">
                       ' . select_tag(
                    'related_entity_field_id',
                    $entity_fields,
                    $obj['related_entity_field_id'],
                    ['class' => 'form-control input-large required']
                ) . '
                       ' . tooltip_text(sprintf(TEXT_EXT_RELATED_ENTITY_FIELD_INFO, $resource_entity_name)) . '
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
	          	<label class="col-md-3 control-label" for="allowed_groups">' . tooltip_icon(
                TEXT_ENTER_TEXT_PATTERN_INFO
            ) . TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper($entities_id, 'heading_template') . '</label>
	            <div class="col-md-9">
	          	   ' . input_tag('heading_template', $obj['heading_template'], ['class' => 'form-control input-large']
            ) . '
	          	   ' . tooltip_text(TEXT_HEADING_TEMPLATE_INFO) . '
	            </div>
	          </div>
	        ';


        $use_fields = [];
        $use_fields[''] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_stages') and entities_id='" . db_input(
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