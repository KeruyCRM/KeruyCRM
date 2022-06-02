<?php

$pivot_map_info_query = db_query("select * from app_ext_pivot_map_reports where id='" . _get::int('reports_id') . "'");
if (!$pivot_map_info = db_fetch_array($pivot_map_info_query)) {
    redirect_to('ext/pivot_map_reports/reports');
}


switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'reports_id' => $pivot_map_info['id'],
            'entities_id' => $_POST['entities_id'],
            'fields_id' => $_POST['fields_id'],
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'background' => $_POST['background'],
            'marker_color' => $_POST['marker_color'],
            'marker_icon' => $_POST['marker_icon'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_pivot_map_reports_entities', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            $pivot_map_id = $_GET['id'];
        } else {
            db_perform('app_ext_pivot_map_reports_entities', $sql_data);
            $pivot_map_id = db_insert_id();
        }

        //create default fitler
        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $_POST['entities_id']
            ) . "' and reports_type='pivot_map" . $pivot_map_id . "'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $sql_data = [
                'name' => '',
                'entities_id' => $_POST['entities_id'],
                'reports_type' => 'pivot_map' . $pivot_map_id,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'listing_order_fields' => '',
                'created_by' => $app_logged_users_id,
            ];

            db_perform('app_reports', $sql_data);
            $insert_id = db_insert_id();

            reports::auto_create_parent_reports($insert_id);
        }

        redirect_to('ext/pivot_map_reports/entities', 'reports_id=' . $pivot_map_info['id']);

        break;
    case 'delete':

        db_delete_row('app_ext_pivot_map_reports_entities', _get::int('id'));

        reports::delete_reports_by_type('pivot_map' . _get::int('id'));

        redirect_to('ext/pivot_map_reports/entities', 'reports_id=' . $pivot_map_info['id']);
        break;


    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_pivot_map_reports_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_pivot_map_reports_entities');
        }

        $html = '';

        $fields_type_by_id_js = '';


        switch (pivot_map_reports::get_map_type($pivot_map_info['id'])) {
            case 'yandex':
                $use_field_type = "'fieldtype_yandex_map'";
                break;
            case 'google':
                $use_field_type = "'fieldtype_google_map','fieldtype_google_map_directions'";
                break;
            case 'mapbbcode':
                $use_field_type = "'fieldtype_mapbbcode'";
                break;
            default:
                $use_field_type = "'fieldtype_mapbbcode','fieldtype_google_map','fieldtype_google_map_directions','fieldtype_yandex_map'";
                break;
        }


        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ({$use_field_type}) and entities_id='" . db_input(
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
                TEXT_AVAILABLE_FIELS . ': ' . TEXT_FIELDTYPE_MAPBBCODE_TITLE . ', ' . TEXT_FIELDTYPE_GOOGLE_MAP_TITLE . ', ' . TEXT_FIELDTYPE_YANDEX_MAP_TITLE
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