<?php


switch ($app_module_action) {
    case 'add_row':

        if ($_GET['block_type'] == 'thead') {
            $sort_order_query = db_query(
                "select (min(sort_order)-1) as sort_order from app_ext_report_page_blocks where block_type='" . $_GET['block_type'] . "' and report_id=" . $report_page['id'] . " and parent_id=" . $block_info['id']
            );
        } else {
            $sort_order_query = db_query(
                "select (max(sort_order)+1) as sort_order from app_ext_report_page_blocks where block_type='" . $_GET['block_type'] . "' and report_id=" . $report_page['id'] . " and parent_id=" . $block_info['id']
            );
        }

        $sort_order = db_fetch_array($sort_order_query);

        $sql_data = [
            'report_id' => $report_page['id'],
            'block_type' => $_GET['block_type'],
            'parent_id' => _GET('block_id'),
            'field_id' => 0,
            'settings' => '',
            'sort_order' => $sort_order['sort_order'],
        ];

        //print_rr($_POST);
        //EXIT();

        if (isset($_GET['id'])) {
            db_perform('app_ext_report_page_blocks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_report_page_blocks', $sql_data);
        }

        redirect_to('ext/report_page/blocks_entity_table', 'block_id=' . $block_info['id']);
        break;
    case 'delete_row':
        if (isset($_GET['id'])) {
            report_page\blocks::delete(_GET('id'));

            redirect_to('ext/report_page/blocks_entity_table', 'block_id=' . $block_info['id']);
        }
        break;
    case 'save':
        $sql_data = [
            'report_id' => $report_page['id'],
            'block_type' => $row_info['block_type'] . '_cell',
            'parent_id' => $row_info['id'],
            'field_id' => (isset($_POST['fields_id']) ? $_POST['fields_id'] : 0),
            'settings' => (isset($_POST['settings']) ? json_encode($_POST['settings']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_report_page_blocks', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_report_page_blocks', $sql_data);
        }

        redirect_to(
            'ext/report_page/extra_rows',
            'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
        );
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            report_page\blocks::delete(_GET('id'));

            redirect_to(
                'ext/report_page/extra_rows',
                'report_id=' . $report_page['id'] . '&block_id=' . $block_info['id'] . '&row_id=' . $row_info['id']
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
                  ';

                break;
        }

        echo $html;

        exit();

        break;
}