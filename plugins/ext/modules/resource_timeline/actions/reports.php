<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'heading_template' => $_POST['heading_template'],
            'column_width' => $_POST['column_width'],
            'listing_width' => $_POST['listing_width'],
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'default_view' => $_POST['default_view'],
            'view_modes' => (isset($_POST['view_modes']) ? implode(',', $_POST['view_modes']) : ''),
            'time_slot_duration' => $_POST['time_slot_duration'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'display_legend' => (isset($_POST['display_legend']) ? $_POST['display_legend'] : 0),
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            $calendar_id = _GET('id');

            $obj = db_find('app_ext_resource_timeline', $calendar_id);

            if ($obj['entities_id'] != $_POST['entities_id']) {
                reports::delete_reports_by_type('resource_timeline' . $calendar_id);
                reports::auto_create_report_by_type($_POST['entities_id'], 'resource_timeline' . $calendar_id);

                $entities_query = db_query(
                    "select id from app_ext_resource_timeline_entities where calendars_id='" . $calendar_id . "'"
                );
                while ($entities = db_fetch_array($entities_query)) {
                    reports::delete_reports_by_type('resource_timeline_entities' . $entities['id']);
                }

                db_delete_row('app_ext_resource_timeline_entities', $calendar_id, 'calendars_id');
            }

            db_perform('app_ext_resource_timeline', $sql_data, 'update', "id='" . db_input($calendar_id) . "'");
        } else {
            db_perform('app_ext_resource_timeline', $sql_data);
            $calendar_id = db_insert_id();

            reports::auto_create_report_by_type($_POST['entities_id'], 'resource_timeline' . $calendar_id);
        }


        redirect_to('ext/resource_timeline/reports');
        break;

    case 'delete':
        $calendar_id = _get::int('id');

        $obj = db_find('app_ext_resource_timeline', $calendar_id);

        db_delete_row('app_ext_resource_timeline', $calendar_id);

        reports::delete_reports_by_type('resource_timeline' . $calendar_id);

        $entities_query = db_query(
            "select id from app_ext_resource_timeline_entities where calendars_id='" . $calendar_id . "'"
        );
        while ($entities = db_fetch_array($entities_query)) {
            reports::delete_reports_by_type('resource_timeline_entities' . $entities['id']);
        }

        db_delete_row('app_ext_resource_timeline_entities', $calendar_id, 'calendars_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/resource_timeline/reports');
        break;

    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_resource_timeline', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_resource_timeline');
        }

        $html = '
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

        $fields_in_listing = [];
        $fields_query = fields::get_query(
            $entities_id,
            "and (is_heading=0 or is_heading is null) and type not in ('fieldtype_action')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $fields_in_listing[$fields['id']] = fields::get_name($fields);
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_FIELDS_IN_POPUP . '</label>
            <div class="col-md-9">
          	   ' . select_tag(
                'fields_in_popup[]',
                $fields_in_listing,
                $obj['fields_in_popup'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'chosen_order' => $obj['fields_in_popup'],
                    'multiple' => 'multiple'
                ]
            ) . '
            </div>
          </div>
        ';

        $html .= '
         <h3 class="form-section">' . TEXT_LIST . '</h3>  
             
          <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . tooltip_icon(
                TEXT_ENTER_VALUE_IN_PERCENT_OR_PIXELS
            ) . TEXT_WIDTH . '</label>
            <div class="col-md-9">
          	   ' . input_tag('listing_width', $obj['listing_width'], ['class' => 'form-control input-small']) . '                    
            </div>
          </div>
          
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_FIELDS_IN_LISTING . '</label>
            <div class="col-md-9">
          	   ' . select_tag(
                'fields_in_listing[]',
                $fields_in_listing,
                $obj['fields_in_listing'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'chosen_order' => $obj['fields_in_listing'],
                    'multiple' => 'multiple'
                ]
            ) . '
            </div>
          </div>
        ';


        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_COLUMN_WIDTH . '</label>
            <div class="col-md-9">
          	   ' . input_tag('column_width', $obj['column_width'], ['class' => 'form-control input-large']) . '
                    ' . tooltip_text(
                TEXT_EXT_ENTER_COLUMN_WIDHT_IN_PP_BY_COMMA . '<br>' . TEXT_EXAMPLE . ': 30%,50,80'
            ) . '
            </div>
          </div>
        ';


        echo $html;
        app_exit();

        break;
}