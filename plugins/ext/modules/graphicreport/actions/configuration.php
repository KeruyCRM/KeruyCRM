<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

if (!app_session_is_registered('graphicreport_entity_filter')) {
    $graphicreport_entity_filter = 0;
    app_session_register('graphicreport_entity_filter');
}

switch ($app_module_action) {
    case 'set_reports_filter':
        $graphicreport_entity_filter = $_POST['reports_filter'];

        redirect_to('ext/graphicreport/configuration');
        break;
    case 'save':

        $yaxis = [];
        foreach ($_POST['yaxis'] as $k => $v) {
            if ($v > 0) {
                $yaxis[] = $v . ':' . $_POST['yaxis_color'][$k];
            }
        }

        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'allowed_groups' => (isset($_POST['allowed_groups']) ? implode(',', $_POST['allowed_groups']) : ''),
            'xaxis' => $_POST['xaxis'],
            'yaxis' => implode(',', $yaxis),
            'chart_type' => $_POST['chart_type'],
            'period' => $_POST['period'],
            'show_totals' => $_POST['show_totals'] ?? 0,
            'hide_zero' => $_POST['hide_zero'] ?? 0,
        ];


        if (isset($_GET['id'])) {
            db_perform('app_ext_graphicreport', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_graphicreport', $sql_data);
        }

        redirect_to('ext/graphicreport/configuration');

        break;
    case 'delete':
        $obj = db_find('app_ext_graphicreport', $_GET['id']);

        db_delete_row('app_ext_graphicreport', $_GET['id']);

        $report_info_query = db_query(
            "select * from app_reports where reports_type='graphicreport" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/graphicreport/configuration');
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_graphicreport', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_graphicreport');
        }

        $xaxis_fields = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_date_added','fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $xaxis_fields[$fields['id']] = ($fields['type'] == 'fieldtype_date_added' ? TEXT_FIELDTYPE_DATEADDED_TITLE : $fields['name']);
        }

        $html = '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_HORIZONTAL_AXIS . '</label>
            <div class="col-md-9">	
          	   ' . select_tag('xaxis', $xaxis_fields, $obj['xaxis'], ['class' => 'form-control input-large required']
            ) . '
               ' . tooltip_text(TEXT_EXT_HORIZONTAL_AXIS_INFO) . '
            </div>			
          </div>
        ';


        $yaxis_fields = [];
        $yaxis_fields_select = ['' => ''];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input_numeric','fieldtype_input_numeric_comments','fieldtype_formula','fieldtype_js_formula','fieldtype_mysql_query','fieldtype_days_difference','fieldtype_hours_difference') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $yaxis_fields[$fields['id']] = $fields['name'];
            $yaxis_fields_select[$fields['id']] = $fields['name'];
        }

        if (count($yaxis_fields) == 0) {
            $yaxis_fields = ['' => ''];
        }

        $obj_yaxis = explode(',', $obj['yaxis']);
        $is_required = true;
        $key = 0;
        foreach ($yaxis_fields as $v) {
            $value = (isset($obj_yaxis[$key]) ? explode(':', $obj_yaxis[$key]) : ['']);
            $yaxis = $value[0];
            $yaxis_color = $value[1] ?? '';

            $html .= '
           <div class="form-group">
            	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_EXT_VERTICAL_AXIS . ' ' . ($key + 1) . '</label>
              <div class="col-md-5">	
            	   ' . select_tag(
                    'yaxis[]',
                    ($key == 0 ? $yaxis_fields : $yaxis_fields_select),
                    $yaxis,
                    ['class' => 'form-control  ' . ($is_required ? 'required' : '')]
                ) . '
                 ' . tooltip_text(TEXT_EXT_VERTICAL_AXIS_INFO) . '
              </div>
              <div>
                ' . input_color('yaxis_color[]', $yaxis_color) . '
              </div>
            </div>
          ';
            $is_required = false;
            $key++;
        }

        echo $html;

        exit();
        break;
}