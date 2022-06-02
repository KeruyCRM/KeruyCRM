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
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'fields_id' => $_POST['fields_id'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'background' => $_POST['background'],
            'zoom' => $_POST['zoom'],
            'latlng' => trim(preg_replace('/ +/', ',', $_POST['latlng'])),
            'is_public_access' => $_POST['is_public_access'] ?? 0,
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_map_reports', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_map_reports', $sql_data);
        }

        redirect_to('ext/map_reports/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_map_reports', $_GET['id']);

        db_delete_row('app_ext_map_reports', $_GET['id']);

        $report_info_query = db_query(
            "select * from app_reports where reports_type='public_map" . db_input($_GET['id']) . "'"
        );
        if ($report_info = db_fetch_array($report_info_query)) {
            reports::delete_reports_by_id($report_info['id']);
        }

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/map_reports/reports');
        break;


    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_map_reports', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_map_reports');
        }

        $html = '';

        $fields_type_by_id_js = '';

        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_mapbbcode','fieldtype_yandex_map','fieldtype_google_map','fieldtype_google_map_directions') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];

            $fields_type_by_id_js .= 'fields_type_by_id[' . $fields['id'] . ']="' . $fields['type'] . '"; ' . "\n";
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="allowed_groups">' . TEXT_FIELD . '</label>
            <div class="col-md-8">	
          	   ' . select_tag(
                'fields_id',
                $choices,
                $obj['fields_id'],
                ['class' => 'form-control input-large required', 'onChange' => 'check_field_type()']
            ) . '
               ' . tooltip_text(
                TEXT_AVAILABLE_FIELS . ': ' . TEXT_FIELDTYPE_MAPBBCODE_TITLE . ', ' . TEXT_FIELDTYPE_GOOGLE_MAP_TITLE
            ) . '
            </div>			
          </div>
          <script>
           var fields_type_by_id = [];
           ' . $fields_type_by_id_js . '    		
          </script>
        ';


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
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
            </div>
          </div>
        ';

        $choices = ['' => ''];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_autostatus') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group from-group-background">
          	<label class="col-md-4 control-label" for="allowed_groups">' . tooltip_icon(
                TEXT_EXT_MAP_REPORTS_BACKGROUND_COLOR_INFO
            ) . TEXT_BACKGROUND_COLOR . '</label>
            <div class="col-md-8">
          	   ' . select_tag('background', $choices, $obj['background'], ['class' => 'form-control input-large']) . '               
            </div>
          </div>
        ';

        echo $html;

        exit();
        break;
}