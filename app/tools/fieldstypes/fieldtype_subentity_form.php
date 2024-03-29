<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_subentity_form
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_SUBENTITY_FORM_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $exclude_entities = [];
        /*$fields_query = db_query(
             "select configuration from app_fields where entities_id='" . _POST(
                 'entities_id'
             ) . "' and type='fieldtype_subentity_form' " . ($_POST['id'] > 0 ? " and id!='" . _POST('id') . "'" : '')
         );*/

        $fields_query = \K::model()->db_fetch(
            'app_fields',
            [
                'entities_id = :entities_id and type = :type' . \K::$fw->POST['id'] > 0 ? 'and id != :id' : '',
                ':entities_id' => \K::$fw->POST['entities_id'],
                ':type' => 'fieldtype_subentity_form'
            ] + (\K::$fw->POST['id'] > 0 ? [':id' => \K::$fw->POST['id']] : []),
            [],
            'configuration'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $fields_cfg = new \Tools\Settings($fields['configuration']);
            $exclude_entities[] = $fields_cfg->get('entity_id');
        }

        $choices = [];

        //$entities_query = db_query("select id, name from app_entities where parent_id='" . _POST('entities_id') . "'");

        $entities_query = \K::model()->db_fetch('app_entities', [
            'parent_id = ?',
            \K::$fw->POST['entities_id']
        ], [], 'id,name');

        //while ($entities = db_fetch_array($entities_query)) {
        foreach ($entities_query as $entities) {
            $entities = $entities->cast();

            if (!in_array($entities['id'], $exclude_entities)) {
                $choices[$entities['id']] = $entities['name'];
            }
        }

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_SUB_ENTITY,
            'name' => 'entity_id',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => [
                'class' => 'form-control input-xlarge required',
                'onChange' => 'fields_types_ajax_configuration(\'fields_in_form\',this.value)'
            ]
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_MAX_COUNT_RECORDS,
            'name' => 'max_count_records',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small'],
            'tooltip_icon' => \K::$fw->TEXT_MAX_COUNT_RECORDS_IN_FORM_INFO
        ];

        $choices = [
            'column' => \K::$fw->TEXT_INTO_COLUMN,
            'row' => \K::$fw->TEXT_INTO_ROW,
            'window' => \K::$fw->TEXT_IN_NEW_WINDOW,
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_FIELDS_DISPLAY,
            'name' => 'fields_display',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-medium required']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'type' => 'html',
            'html' => '
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">
                    <div class="help-block" id="help_blcok_row">' . \K::$fw->TEXT_FIELDS_DISPLAY_IN_FOR_TYPE_ROW . '</div>
                    <div class="help-block" id="help_blcok_windows">' . \K::$fw->TEXT_FIELDS_DISPLAY_IN_FOR_TYPE_NEW_WINDOW . '</div>
                </div>
	        </div>
                
                <script>
                    $(function(){
                        check_fields_display_type()
                        
                        $("#fields_configuration_fields_display").change(function(){ 
                            check_fields_display_type();
                        })
                    })
                    
                    function check_fields_display_type()
                    {                    
                        if($("#fields_configuration_fields_display").val()=="window")
                        {
                           $("#help_blcok_windows").show() 
                           $(".from_group_fields_in_listing").show()                           
                           $("#help_blcok_row").hide() 
                           $(".from_group_fields_in_form").hide()                           
                           $(".from_group_auto_insert").hide()
                           $(".from_group_listing_type").show();                           
                        }
                        else
                        {
                            $("#help_blcok_windows").hide() 
                            $(".from_group_fields_in_listing").hide()                            
                            $("#help_blcok_row").show() 
                            $(".from_group_fields_in_form").show()                            
                            $(".from_group_auto_insert").show()
                            $(".from_group_listing_type").hide();                           
                        }
                        
                        if($("#fields_configuration_fields_display").val()=="column")
                        {
                            $(".from_group_column_width").hide()
                            $(".from_group_has_count").show()
                        }
                        else
                        {
                            $(".from_group_column_width").show()
                            $(".from_group_has_count").hide()
                        }
                    }
                </script>
                ',
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'name' => 'fields_in_form',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'fields_in_form\',$("#fields_configuration_entity_id").val())</script>'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_INSERT_RECORD_AUTOMATICALLY,
            'tooltip_icon' => \K::$fw->TEXT_INSERT_RECORD_AUTOMATICALLY_INFO,
            'name' => 'auto_insert',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_SHOW_NUMBER_OF_RECORDS,
            'name' => 'has_count',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox'
        ];

        //button configuration
        $cfg[\K::$fw->TEXT_BUTTON][] = [
            'title' => \K::$fw->TEXT_BUTTON_TITLE,
            'name' => 'button_title',
            'type' => 'input',
            'params' => ['class' => 'form-control input-medium'],
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_ADD
        ];

        $choices = [
            'left' => \K::$fw->TEXT_ON_LEFT,
            'right' => \K::$fw->TEXT_ON_RIGHT,
            'center' => \K::$fw->TEXT_ALIGN_CENTER,
        ];

        $cfg[\K::$fw->TEXT_BUTTON][] = [
            'title' => \K::$fw->TEXT_POSITION,
            'name' => 'button_position',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_BUTTON][] = [
            'title' => \K::$fw->TEXT_ICON,
            'name' => 'button_icon',
            'type' => 'input',
            'params' => ['class' => 'form-control input-medium'],
            'tooltip' => \K::$fw->TEXT_MENU_ICON_TITLE_TOOLTIP
        ];

        $cfg[\K::$fw->TEXT_BUTTON][] = [
            'title' => \K::$fw->TEXT_COLOR,
            'name' => 'button_color',
            'type' => 'colorpicker'
        ];

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'fields_in_form':
                $entities_id = $value;

                $allowed_types = [
                    'fieldtype_input',
                    'fieldtype_input_masked',
                    'fieldtype_input_dynamic_mask',
                    'fieldtype_input_protected',
                    'fieldtype_input_encrypted',
                    'fieldtype_input_url',
                    'fieldtype_video',
                    'fieldtype_iframe',
                    'fieldtype_input_email',
                    'fieldtype_phone',
                    'fieldtype_input_numeric',
                    'fieldtype_input_date',
                    'fieldtype_input_datetime',
                    'fieldtype_jalali_calendar',
                    'fieldtype_time',
                    'fieldtype_textarea',
                    'fieldtype_textarea_encrypted',
                    'fieldtype_dropdown',
                    'fieldtype_dropdown_multiple',
                    'fieldtype_checkboxes',
                    'fieldtype_radioboxes',
                    'fieldtype_boolean',
                    'fieldtype_boolean_checkbox',
                    'fieldtype_progress',
                    'fieldtype_users',
                    'fieldtype_grouped_users',
                    'fieldtype_access_group',
                    'fieldtype_entity',
                    'fieldtype_stages',
                ];

                $choices = [];

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . \K::model(
                    )->quoteToString(
                        $allowed_types
                    ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    $entities_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    $choices[$fields['id']] = \Models\Main\Fields_types::get_option(
                            $fields['type'],
                            'name',
                            $fields['name']
                        ) . ' (#' . $fields['id'] . ')';
                }

                $cfg[] = [
                    'title' => \K::$fw->TEXT_FIELDS_IN_FORM,
                    'name' => 'fields_in_form',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'params' => [
                        'class' => 'form-control chosen-select chosen-sortable input-xlarge',
                        'multiple' => 'multiple'
                    ]
                ];

                $choices = [];

                $fields_query = \K::model()->db_query_exec(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
                    ) . "," . \Models\Main\Fields_types::get_type_list_excluded_in_form(
                    ) . "," . \Models\Main\Fields_types::get_attachments_types_list(
                    ) . ") and  f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
                    $entities_id,
                    'app_fields,app_forms_tabs'
                );

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    $choices[$fields['id']] = \Models\Main\Fields_types::get_option(
                            $fields['type'],
                            'name',
                            $fields['name']
                        ) . ' (#' . $fields['id'] . ')';
                }

                $cfg[] = [
                    'title' => \K::$fw->TEXT_FIELDS_IN_LISTING,
                    'name' => 'fields_in_listing',
                    'type' => 'dropdown',
                    'tooltip' => \K::$fw->TEXT_FIELDS_IN_LISTING_ON_FORM_PAGE,
                    'choices' => $choices,
                    'params' => [
                        'class' => 'form-control chosen-select chosen-sortable input-xlarge',
                        'multiple' => 'multiple'
                    ]
                ];

                $choices = [
                    'table' => \K::$fw->TEXT_TABLE,
                    'list' => \K::$fw->TEXT_LIST,
                ];

                $cfg[] = [
                    'title' => \K::$fw->TEXT_DISPLAY_AS,
                    'name' => 'listing_type',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'params' => ['class' => 'form-control input-medium']
                ];

                $cfg[] = [
                    'title' => \K::$fw->TEXT_COLUMN_WIDTH,
                    'name' => 'column_width',
                    'type' => 'input',
                    'params' => ['class' => 'form-control input-large'],
                    'tooltip' => \K::$fw->TEXT_ENTER_COLUMN_WIDTH_BY_COMMA
                ];

                $cfg[] = [
                    'type' => 'html',
                    'html' => '
                            <script> 
                                $(function(){
                                    check_fields_display_type() 
                                })                            
                            </script>'
                ];

                break;
        }

        return $cfg;
    }

    public function process($options)
    {
    }

    public function output($options)
    {
    }

    public function render($field, $obj, $params = [])
    {
        //reset items list
        if (isset(\K::$fw->app_subentity_form_items[$field['id']])) {
            \K::$fw->app_subentity_form_items[$field['id']] = [];
        }

        if (isset($app_subentity_form_items_deleted[$field['id']])) {
            //TODO Not init in global
            \K::$fw->app_subentity_form_items_deleted[$field['id']] = [];
        }

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $subentity_form = new \Models\Main\Items\Subentity_form($field['entities_id'], $obj['id'], $field['id']);

        $html = '
            <style>
                .form-group-' . $field['id'] . ' >label.col-md-3{
                    display:none;
                }
                
                .form-group-' . $field['id'] . ' >div.col-md-9{
                    width: 100%
                }
            </style>
            ';

        if ($cfg->get('fields_display') != 'column' and $cfg->get('hide_field_name') != '1') {
            $html .= '
                <h3 class="form-section subentity-form-row-section">' . $field['name'] . '</h3>
                ';
        }

        $items = $subentity_form->render_items();

        $html .= '
            <div id="subentity_form' . $field['id'] . '" class="subentity_form">' . $items['html'] . '</div>';

        $html .= \Helpers\Html::input_hidden_tag('subentity_form' . $field['id'] . '_rows_count', $items['rows_count']);
        $html .= \Helpers\Html::input_hidden_tag(
            'subentity_form' . $field['id'] . '_max_rows_count',
            (int)$cfg->get('max_count_records')
        );
        $html .= \Helpers\Html::input_hidden_tag(
            'fields[' . $field['id'] . ']',
            '',
            ['class' => ($field['is_required'] == 1 ? ' required' : '')]
        );

        $html .= $subentity_form->render_js();
        $html .= $subentity_form->render_button();

        return $html;
    }

    public static function update_items_fields($entities_id, $items_id)
    {
        /*$fields_query = db_query(
            "select id from app_fields where entities_id='" . db_input(
                $entities_id
            ) . "' and type='fieldtype_subentity_form'"
        );*/
        $fields_query = \K::model()->db_fetch('app_fields', [
            'entities_id = ? and type = ?',
            $entities_id,
            'fieldtype_subentity_form'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        $forceCommit = \K::model()->forceCommit();

        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $subentity_form = new \Models\Main\Items\Subentity_form($entities_id, $items_id, $fields['id']);
            $subentity_form->save_form();
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }
}