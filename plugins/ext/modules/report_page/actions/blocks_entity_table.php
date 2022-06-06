<?php

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'report_id' => $report_page['id'],
            'block_type' => 'body_cell',
            'parent_id' => $block_info['id'],
            'field_id' => _POST('fields_id'),
            'settings' => (isset($_POST['settings']) ? json_encode($_POST['settings']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_report_page_blocks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_report_page_blocks', $sql_data);
        }

        redirect_to(
            'ext/report_page/blocks_entity_table',
            'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id']
        );
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            report_page\blocks::delete(_GET('id'));

            redirect_to(
                'ext/report_page/blocks_entity_table',
                'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id']
            );
        }
        break;
    case 'get_field_settings':
        $field_query = db_query("select type from app_fields where id=" . _POST('fields_id'));
        if (!$field = db_fetch_array($field_query)) {
            exit();
        }

        if ($_GET['id'] > 0) {
            $obj = db_find('app_ext_report_page_blocks', _GET('id'));
            $settings = new settings($obj['settings']);
        } else {
            $settings = new settings('');
        }

        $html = '';

        switch ($field['type']) {
            case 'fieldtype_input_date':
                $html = '
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_DATE_FORMAT . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[date_format]',
                        $settings->get('date_format'),
                        ['class' => 'form-control input-small']
                    ) . tooltip_text(TEXT_DEFAULT . ': ' . CFG_APP_DATE_FORMAT . ', ' . TEXT_DATE_FORMAT_INFO) . '</div>
                    </div>';

                break;
            case 'fieldtype_date_added':
            case 'fieldtype_date_updated':
            case 'fieldtype_dynamic_date':
            case 'fieldtype_input_datetime':
                $html = '
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_DATE_FORMAT . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[date_format]',
                        $settings->get('date_format'),
                        ['class' => 'form-control input-small']
                    ) . tooltip_text(TEXT_DEFAULT . ': ' . CFG_APP_DATETIME_FORMAT . ', ' . TEXT_DATE_FORMAT_INFO) . '</div>
                    </div>';

                break;
            case 'fieldtype_user_photo':
            case 'fieldtype_image':
            case 'fieldtype_image_ajax':
                $html = '
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_WIDTH . '</label>
                        <div class="col-md-9">' . input_tag(
                        'settings[width]',
                        $settings->get('width', 100),
                        ['class' => 'form-control input-small number']
                    ) . '</div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="fields_id">' . TEXT_HEIGHT . '</label>
                            <div class="col-md-9">' . input_tag(
                        'settings[height]',
                        $settings->get('height', 100),
                        ['class' => 'form-control input-small number']
                    ) . '</div>
                    </div>
                 ';
                break;
            case 'fieldtype_input_numeric':
            case 'fieldtype_input_numeric_comments':
            case 'fieldtype_formula':
            case 'fieldtype_js_formula':
            case 'fieldtype_mysql_query':
            case 'fieldtype_ajax_request':
                $html = '
                  <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id">' . tooltip_icon(
                        TEXT_NUMBER_FORMAT_INFO
                    ) . TEXT_NUMBER_FORMAT . '</label>
                    <div class="col-md-9">' . input_tag(
                        'settings[number_format]',
                        $settings->get('number_format', CFG_APP_NUMBER_FORMAT),
                        ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~']
                    ) . '</div>			
                  </div>  
                  <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id">' . TEXT_PREFIX . '</label>
                    <div class="col-md-9">' . input_tag(
                        'settings[content_value_prefix]',
                        $settings->get('content_value_prefix', ''),
                        ['class' => 'form-control input-medium']
                    ) . '</div>			
                  </div>
                  
                  <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="fields_id">' . TEXT_SUFFIX . '</label>
                    <div class="col-md-9">' . input_tag(
                        'settings[content_value_suffix]',
                        $settings->get('content_value_suffix', ''),
                        ['class' => 'form-control input-medium']
                    ) . '</div>			
                  </div>
                  
                  <div class="form-group settings-list">
                    <label class="col-md-3 control-label" for="settings_calculate_totals">' . TEXT_CALCULATE_TOTALS . '</label>
                    <div class="col-md-9"><p class="form-control-static">' . input_checkbox_tag(
                        'settings[calculate_totals]',
                        1,
                        ['checked' => $settings->get('calculate_totals')]
                    ) . '</p></div>			
                  </div>';
                break;
        }

        echo $html;

        exit();

        break;
}