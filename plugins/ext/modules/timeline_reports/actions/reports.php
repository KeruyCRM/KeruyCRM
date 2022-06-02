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
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'heading_template' => $_POST['heading_template'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'allowed_groups' => (isset($_POST['allowed_groups']) ? implode(',', $_POST['allowed_groups']) : ''),
            'use_background' => $_POST['use_background'],
        ];


        if (isset($_GET['id'])) {
            $reports_id = $_GET['id'];

            db_perform('app_ext_timeline_reports', $sql_data, 'update', "id='" . db_input($reports_id) . "'");
        } else {
            db_perform('app_ext_timeline_reports', $sql_data);
            $reports_id = db_insert_id();
        }


        redirect_to('ext/timeline_reports/reports');
        break;

    case 'delete':
        $obj = db_find('app_ext_timeline_reports', $_GET['id']);

        db_delete_row('app_ext_timeline_reports', $_GET['id']);

        $report_info_query = db_query(
            "select * from app_reports where reports_type='timelinereport" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/timeline_reports/reports');

        break;

    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_timeline_reports', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_timeline_reports');
        }

        $start_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_date_added','fieldtype_dynamic_date') and entities_id='" . db_input(
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
            </div>
          </div>
        ';

        $end_date_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_date_added','fieldtype_dynamic_date') and entities_id='" . db_input(
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
            </div>
          </div>
        ';

        $use_fields = [];
        $use_fields[''] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_autostatus','fieldtype_stages','fieldtype_grouped_users') and entities_id='" . db_input(
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