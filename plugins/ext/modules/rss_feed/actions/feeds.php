<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'entities_id' => $_POST['entities_id'] ?? 0,
            'heading_template' => $_POST['heading_template'] ?? '',
            'start_date' => $_POST['start_date'] ?? 0,
            'end_date' => $_POST['end_date'] ?? 0,
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_rss_feeds', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_rss_feeds', $sql_data);
            $id = db_insert_id();

            rss_feed::generate_rss_id($id);

            if (in_array($_POST['type'], ['entity', 'entity_calendar'])) {
                reports::auto_create_report_by_type($_POST['entities_id'], 'rss_feed' . $id);
            }
        }

        redirect_to('ext/rss_feed/feeds');

        break;
    case 'delete':
        $obj = db_find('app_ext_rss_feeds', $_GET['id']);

        db_delete_row('app_ext_rss_feeds', $_GET['id']);

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/rss_feed/feeds');
        break;

    case 'settings':
        $type = $_POST['type'];

        if ($_POST['id']) {
            $obj = db_find('app_ext_rss_feeds', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_rss_feeds');
        }

        $html = '';

        if (in_array($type, ['entity', 'entity_calendar'])) {
            $html .= '
                <div class="form-group">
                    <label class="col-md-3 control-label" for="type">' . TEXT_ENTITY . '</label>
                    <div class="col-md-9">	
                        ' . select_tag(
                    'entities_id',
                    entities::get_choices(),
                    $obj['entities_id'],
                    ['class' => 'form-control input-large required', 'onChange' => 'load_entity_settings()']
                ) . '        
                    </div>			
                </div>  
                ';
        }

        echo $html;

        exit();
        break;

    case 'entity_settings':
        $type = $_POST['type'];
        $entities_id = $_POST['entities_id'];

        if ($_POST['id']) {
            $obj = db_find('app_ext_rss_feeds', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_rss_feeds');
        }

        $html = '';

        if (in_array($type, ['entity', 'entity_calendar'])) {
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
        }

        if (in_array($type, ['entity_calendar'])) {
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
        }

        echo $html;

        exit();
        break;
}