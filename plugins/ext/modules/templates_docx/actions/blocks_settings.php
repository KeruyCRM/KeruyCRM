<?php

$field_query = db_query("select * from app_fields where id=" . _POST('fields_id'));
if (!$field = db_fetch_array($field_query)) {
    exit();
}

if (isset($_POST['id'])) {
    $obj = db_find('app_ext_items_export_templates_blocks', $_POST['id']);
} else {
    $obj = db_show_columns('app_ext_items_export_templates_blocks');
}

//for subentities
$is_subentity = false;
if ($field['type'] == 'fieldtype_id' and $app_entities_cache[$field['entities_id']]['parent_id'] == $template_info['entities_id']) {
    $field['type'] = 'fieldtype_entity';
    $field_entity_id = $field['entities_id'];
    $is_subentity = true;
}


$cfg = new settings($field['configuration']);

$settings = new settings($obj['settings']);

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
    case 'fieldtype_attachments':
    case 'fieldtype_image':
    case 'fieldtype_image_ajax':
        $html = '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_WIDTH . '</label>
                <div class="col-md-9">' . input_tag(
                'settings[width]',
                $settings->get('width'),
                ['class' => 'form-control input-small number']
            ) . '</div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_HEIGHT . '</label>
                    <div class="col-md-9">' . input_tag(
                'settings[height]',
                $settings->get('height'),
                ['class' => 'form-control input-small number']
            ) . '</div>
            </div>
         ';

        if ($field['type'] == 'fieldtype_attachments') {
            $html .= '
                <div class="form-group">
                    <label class="col-md-3 control-label" for="fields_id">' . TEXT_GRID . '</label>
                    <div class="col-md-9">' . input_tag(
                    'settings[grid]',
                    $settings->get('grid', 1),
                    ['class' => 'form-control input-xsmall number']
                ) . '</div>
                </div>';
        }
        break;
    case 'fieldtype_input_numeric':
    case 'fieldtype_input_numeric_comments':
    case 'fieldtype_formula':
    case 'fieldtype_js_formula':
    case 'fieldtype_mysql_query':
    case 'fieldtype_ajax_request':

        $choices = [];
        $choices[''] = '';

        foreach ($app_num2str->data as $k => $v) {
            $choices[$k] = $k;
        }

        $html = '
          <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id">' . tooltip_icon(
                TEXT_EXT_NUMBER_IN_WORDS_INFO
            ) . TEXT_EXT_NUMBER_IN_WORDS . '</label>
            <div class="col-md-9">' . select_tag(
                'settings[number_in_words]',
                $choices,
                $settings->get('number_in_words'),
                ['class' => 'form-control input-small']
            ) . '</div>
          </div>';
        break;

    case 'fieldtype_textarea_wysiwyg':
    case 'fieldtype_textarea':
    case 'fieldtype_items_by_query':
        $html = '
            <div class="form-group ">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_NAME . '</label>
                <div class="col-md-4">' . input_tag(
                'settings[font_name]',
                $settings->get('font_name', 'Times New Roman'),
                ['class' => 'form-control input-medium required']
            ) . tooltip_text(TEXT_EXAMPLE . ': Times New Roman, Arial') . '</div>                
              </div>

              <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_SIZE . '</label>
                <div class="col-md-9">' . input_tag(
                'settings[font_size]',
                $settings->get('font_size', '12'),
                ['class' => 'form-control input-small required number']
            ) . '</div>			
              </div>
            ';
        break;

    case 'fieldtype_entity':
    case 'fieldtype_entity_ajax':
    case 'fieldtype_related_records':
    case 'fieldtype_users':
    case 'fieldtype_users_ajax':
    case 'fieldtype_users_approve':
    case 'fieldtype_user_roles':

        if (!in_array($cfg->get('display_as'), ['dropdown_multiple', 'checkboxes', 'dropdown_muliple']
            ) and !isset($field_entity_id) and $field['type'] != 'fieldtype_related_records') {
            break;
        }

        $list_dislay_choices = [
            'inline' => TEXT_IN_ONE_LINE,
            'list' => TEXT_LIST,
            'table' => TEXT_TABLE,
            'table_list' => TEXT_EXT_TABLE_LIST,
            'tree_table' => TEXT_TREE_TABLE,
        ];

        $direction_choices = [
            '' => '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>',
            'BTLR' => '<i class="fa fa-long-arrow-up" aria-hidden="true"></i>',
            'TBRL' => '<i class="fa fa-long-arrow-down" aria-hidden="true"></i>',
        ];

        if (!isset($field_entity_id)) {
            $field_entity_id = (in_array(
                $field['type'],
                ['fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_users_approve']
            ) ? 1 : $cfg->get('entity_id'));
        }

        if ($is_subentity) {
            //filter by reports ID
            $html .= '
            <div class="form-group">
                <label class="col-md-3 control-label" for="fields_id">' . TEXT_REPORT . '</label>
                <div class="col-md-9">' . input_tag(
                    'settings[reports_id]',
                    $settings->get('reports_id'),
                    ['class' => 'form-control input-small number']
                ) . tooltip_text(TEXT_EXT_ENTER_REPORT_ID_TO_FILTER) . '</div>
            </div>';
        }

        $html .= '
          <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_DISPLAY_AS . '</label>
            <div class="col-md-9">' . select_tag(
                'settings[display_us]',
                $list_dislay_choices,
                $settings->get('display_us'),
                ['class' => 'form-control input-medium']
            ) . '</div>			
          </div>

        <!--list-->
          <div class="form-group settings-list settings-inline">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_PATTERN . fields::get_available_fields_helper(
                $field_entity_id,
                'settings_pattern'
            ) . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[pattern]',
                $settings->get('pattern'),
                ['class' => 'form-control input-xlarge code']
            ) . tooltip_text(TEXT_HEADING_TEMPLATE_INFO) . '</div>			
          </div>

          <div class="form-group settings-list settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_NAME . '</label>
            <div class="col-md-4">' . input_tag(
                'settings[font_name]',
                $settings->get('font_name', 'Times New Roman'),
                ['class' => 'form-control input-medium required']
            ) . tooltip_text(TEXT_EXAMPLE . ': Times New Roman, Arial') . '</div>
            <div class="col-md-3 settings-table">' . input_color('settings[font_color]', $settings->get('font_color')) . '</div>			
          </div>

          <div class="form-group settings-list settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_SIZE . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[font_size]',
                $settings->get('font_size', '12'),
                ['class' => 'form-control input-small required number']
            ) . '</div>			
          </div>
                
          <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_ADD_EMPTY_ROW . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[empty_row]',
                $settings->get('empty_row', '0'),
                ['class' => 'form-control input-xsmall required number']
            ) . '</div>			
          </div>

        <!--inline-->
          <div class="form-group settings-inline">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_SEPARATOR . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[separator]',
                $settings->get('separator', ', '),
                ['class' => 'form-control input-small']
            ) . '</div>			
          </div>

        <!--table-->               
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_BORDER . '</label>
            <div class="col-md-1">' . input_tag(
                'settings[border]',
                $settings->get('border', '0.1'),
                ['class' => 'form-control input-xsmall required number']
            ) . '</div>
            <div class="col-md-3">' . input_color('settings[border_color]', $settings->get('border_color')) . '</div> 
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_BACKGROUND_COLOR . '</label>           
            <div class="col-md-3">' . input_color('settings[table_color]', $settings->get('table_color')) . '</div> 
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_CELL_MARGIN . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[cell_margin]',
                $settings->get('cell_margin', '3'),
                ['class' => 'form-control input-xsmall required number']
            ) . '</div>			
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_CELL_SPACING . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[cell_spacing]',
                $settings->get('cell_spacing', '0'),
                ['class' => 'form-control input-xsmall required number']
            ) . '</div>			
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_HEADER_HEIGHT . '</label>
            <div class="col-md-1">' . input_tag(
                'settings[header_height]',
                $settings->get('header_height', ''),
                ['class' => 'form-control input-xsmall number']
            ) . '</div>
            <div class="col-md-3">' . input_color('settings[header_color]', $settings->get('header_color')) . '</div>			
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="settings_line_numbering">' . TEXT_EXT_LINE_NUMBERING . '</label>
            <div class="col-md-1"><p class="form-control-static">' . input_checkbox_tag(
                'settings[line_numbering]',
                1,
                ['checked' => $settings->get('line_numbering')]
            ) . '</p></div>
            <div class="col-md-2">' . input_tag(
                'settings[line_numbering_heading]',
                $settings->get('line_numbering_heading'),
                ['class' => 'form-control input-small', 'placeholder' => TEXT_HEADING]
            ) . '</div>
            <div class="col-md-3">' . select_radioboxes_button(
                'settings[line_numbering_direction]',
                $direction_choices,
                $settings->get('line_numbering_direction', '')
            ) . '</div>			
          </div>
          <div class="form-group settings-table">
            <label class="col-md-3 control-label" for="settings_column_numbering">' . TEXT_EXT_COLUMN_NUMBERING . '</label>
            <div class="col-md-1"><p class="form-control-static">' . input_checkbox_tag(
                'settings[column_numbering]',
                1,
                ['checked' => $settings->get('column_numbering')]
            ) . '</p></div>            		
          </div>         
                ';

        break;
    case 'fieldtype_access_group':
    case 'fieldtype_tags':
    case 'fieldtype_grouped_users':
    case 'fieldtype_checkboxes':
    case 'fieldtype_dropdown_multiple':

        $list_dislay_choices = [
            'inline' => TEXT_IN_ONE_LINE,
            'list' => TEXT_LIST,
        ];

        $html = '
          <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_DISPLAY_AS . '</label>
            <div class="col-md-9">' . select_tag(
                'settings[display_us]',
                $list_dislay_choices,
                $settings->get('display_us'),
                ['class' => 'form-control input-medium']
            ) . '</div>
          </div>
                         
          <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_NAME . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[font_name]',
                $settings->get('font_name', 'Times New Roman'),
                ['class' => 'form-control input-medium required']
            ) . '</div>
          </div>
                
          <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_FONT_SIZE . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[font_size]',
                $settings->get('font_size', '12'),
                ['class' => 'form-control input-small required number']
            ) . '</div>
          </div>
                
          <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_EXT_ADD_EMPTY_ROW . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[empty_row]',
                $settings->get('empty_row', '0'),
                ['class' => 'form-control input-xsmall required number']
            ) . '</div>
          </div>
                
          <div class="form-group settings-inline">
            <label class="col-md-3 control-label" for="fields_id">' . TEXT_SEPARATOR . '</label>
            <div class="col-md-9">' . input_tag(
                'settings[separator]',
                $settings->get('separator', ', '),
                ['class' => 'form-control input-small']
            ) . '</div>
          </div>
                ';

        break;
}

echo $html;

?>

<script>
    $(function () {

        $('#settings_display_us').change(function () {
            show_box_settigns();
        })

        show_box_settigns();
    })

    function show_box_settigns() {
        $('.settings-list, .settings-inline, .settings-table').hide();

        switch ($('#settings_display_us').val()) {
            case 'inline':
                $('.settings-inline').show();
                break;
            case 'list':
                $('.settings-list').show();
                break;
            case 'table':
                $('.settings-table').show();
                break;
            case 'table_list':
                $('.settings-table').show();
                break;
            case 'tree_table':
                $('.settings-table').show();
                break;
        }

        $(window).resize();
    }
</script>



