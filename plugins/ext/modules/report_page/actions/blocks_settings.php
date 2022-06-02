<?php


$report_page_query = db_query("select * from app_ext_report_page where id='" . _GET('report_id') . "'");
if (!$report_page = db_fetch_array($report_page_query)) {
    exit();
}

if (isset($_POST['id'])) {
    $obj = db_find('app_ext_report_page_blocks', $_POST['id']);
} else {
    $obj = db_show_columns('app_ext_report_page_blocks');
}

$block_type = $_POST['block_type'] ?? '';
$settings = new settings($obj['settings']);

$html = '';

switch ($block_type) {
    case 'field':
        $choices = fields::get_choices($report_page['entities_id'], ['include_parents' => true]);
        $html .= '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_FIELD . '</label>
                <div class="col-md-9">' . select_tag(
                'field_id',
                $choices,
                $obj['field_id'],
                ['class' => 'input-xlarge chosen-select']
            ) . '</div>			
            </div>
            
            <div id="field_settings"></div>
            
            <script>
            field_settings();

            $("#field_id").change(function()
            {
                field_settings();
            })
            </script>
            ';
        break;
    case 'php':
        $html .= '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_PHP_CODE . '</label>
                <div class="col-md-9">' . textarea_tag(
                'settings[php_code]',
                $settings->get('php_code'),
                ['class' => 'code_mirror', 'mode' => 'php']
            ) . '</div>			
            </div>
            ';
        break;
    case 'html':
        $html .= '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_HTML_CODE . '</label>
                <div class="col-md-9">' . textarea_tag(
                'settings[html_code]',
                $settings->get('html_code'),
                ['class' => 'code_mirror', 'mode' => 'xml']
            ) . '</div>			
            </div>
            ';
        break;

    case 'table':
        $html .= '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_MYSQL_QUERY . '</label>
                <div class="col-md-9">' . textarea_tag(
                'settings[mysql_query]',
                $settings->get('mysql_query'),
                ['class' => 'code_mirror', 'mode' => 'sql']
            ) . '</div>			
            </div>
            ';
        break;
}

echo $html;

exit();
