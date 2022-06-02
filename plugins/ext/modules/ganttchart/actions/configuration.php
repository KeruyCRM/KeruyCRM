<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'sort_fields':
        if (isset($_POST['fields_in_listing'])) {
            $sql_data = ['fields_in_listing' => str_replace('form_fields_', '', $_POST['fields_in_listing'])];

            db_perform('app_ext_ganttchart', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        }
        exit();
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'weekends' => (isset($_POST['weekends']) ? implode(',', $_POST['weekends']) : ''),
            'gantt_date_format' => $_POST['gantt_date_format'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'progress' => (isset($_POST['progress']) ? $_POST['progress'] : ''),
            'use_background' => $_POST['use_background'],
            'default_fields_in_listing' => (isset($_POST['default_fields_in_listing']) ? implode(
                ',',
                $_POST['default_fields_in_listing']
            ) : ''),
            'grid_width' => $_POST['grid_width'],
            'default_view' => $_POST['default_view'],
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'skin' => $_POST['skin'],
            'auto_scheduling' => (isset($_POST['auto_scheduling']) ? $_POST['auto_scheduling'] : 0),
            'highlight_critical_path' => (isset($_POST['highlight_critical_path']) ? $_POST['highlight_critical_path'] : 0),
        ];


        if (isset($_GET['id'])) {
            $ganttchart_id = $_GET['id'];

            db_perform('app_ext_ganttchart', $sql_data, 'update', "id='" . db_input($ganttchart_id) . "'");
        } else {
            db_perform('app_ext_ganttchart', $sql_data);
            $ganttchart_id = db_insert_id();
        }

        db_query("delete from app_ext_ganttchart_access where ganttchart_id='" . db_input($ganttchart_id) . "'");

        foreach ($_POST['access'] as $group_id => $access) {
            if (strlen($access) == 0) {
                continue;
            }

            $sql_data = [
                'ganttchart_id' => $ganttchart_id,
                'access_groups_id' => $group_id,
                'access_schema' => $access,
            ];

            db_perform('app_ext_ganttchart_access', $sql_data);
        }

        redirect_to('ext/ganttchart/configuration');

        break;
    case 'delete':
        $obj = db_find('app_ext_ganttchart', $_GET['id']);

        db_delete_row('app_ext_ganttchart', $_GET['id']);
        db_delete_row('app_ext_ganttchart_access', $_GET['id'], 'ganttchart_id');
        db_delete_row('app_ext_ganttchart_depends', $_GET['id'], 'ganttchart_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/ganttchart/configuration');
        break;

    case 'get_entity_listing_fields':
        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_ganttchart', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_ganttchart');
        }

        $exclude_fiedls_types = "'fieldtype_action','fieldtype_related_records','fieldtype_section','fieldtype_mapbbcode','fieldtype_qrcode','fieldtype_barcode','fieldtype_image','fieldtype_image_ajax','fieldtype_attachments','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_input_file'";

        $fields_query = db_query(
            "select f.*, t.name as tab_name, if(f.type in (" . fields_types::get_reserverd_types_list(
            ) . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where  f.entities_id='" . db_input(
                $entities_id
            ) . "' and f.type not in ({$exclude_fiedls_types}) and f.forms_tabs_id=t.id order by tab_sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = fields_types::get_option($v['type'], 'name', $v['name']);
        }

        $html = '
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
                    'chosen-order' => $obj['fields_in_listing']
                ]
            ) . '
               ' . tooltip_text(TEXT_SORT_ITEMS_IN_LIST) . '
            </div>
          </div>
        ';

        echo $html;

        exit();

        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_ganttchart', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_ganttchart');
        }

        $start_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_date_added','fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $start_date_fields[$fields['id']] = ($fields['type'] == 'fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']);
        }

        $html = '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_START_DATE . '</label>
            <div class="col-md-9">	
          	   ' . select_tag(
                'start_date',
                $start_date_fields,
                $obj['start_date'],
                ['class' => 'form-control input-large required']
            ) . '
               ' . tooltip_text(TEXT_EXT_GANTT_START_DATE_INFO) . '               
            </div>			
          </div>
        ';

        $end_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $entities_id
            ) . "'"
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
               ' . tooltip_text(TEXT_EXT_GANTT_END_DATE_INFO) . '               
            </div>			
          </div>
        ';


        $progress_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_progress') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $progress_fields[$fields['id']] = ($fields['type'] == 'fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']);
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_GANTT_PROGRESS . '</label>
            <div class="col-md-9">	
          	   ' . select_tag('progress', $progress_fields, $obj['progress'], ['class' => 'form-control input-large']
            ) . '
               ' . tooltip_text(TEXT_EXT_GANTT_PROGRESS_INFO) . '               
            </div>			
          </div>
        ';


        $use_fields = [];
        $use_fields[''] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_stages','fieldtype_color') and entities_id='" . db_input(
                $entities_id
            ) . "'"
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


        echo $html;

        exit();
        break;
}