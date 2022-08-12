<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_js_formula
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_JS_FORMULA_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_FORMULA . \Models\Main\Fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_formula'
                ),
            'name' => 'formula',
            'type' => 'code_small',
            'tooltip' => \K::$fw->TEXT_JS_FORMULA_TIP,
            'params' => ['class' => 'form-control code']
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => \K::$fw->CFG_APP_NUMBER_FORMAT
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_CALCULATE_TOTALS_INFO) . \K::$fw->TEXT_CALCULATE_TOTALS,
            'name' => 'calculate_totals',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE_INFO
                ) . \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $formula = $js_formula = $cfg->get('formula');

        $js_function_name = 'form_handle_js_formula_' . $field['id'] . '()';
        $js_function_name_delay = 'setTimeout(function (){ ' . $js_function_name . '; ' . $this->include_extra_js_fieldtypes(
                $field
            ) . '},10);';

        $html_change_handler = '';

        //start build funciton
        $html = '
    	<script>
    		function ' . $js_function_name . '
    		{
    		';

        $field_use_global_list = [];
        //prepare app_choices_values
        if (preg_match_all("/get_value\(([^)]*)\)/", $formula, $matches)) {
            $prepared_fields = [];
            foreach ($matches[1] as $field_id) {
                $field_id = str_replace(['[', ']'], '', $field_id);
                if (!in_array($field_id, $prepared_fields)) {
                    $prepared_fields[] = $field_id;

                    $field_cfg = new \Tools\Settings(
                        \K::$fw->app_fields_cache[$field['entities_id']][$field_id]['configuration']
                    );

                    if ((int)$field_cfg->get('use_global_list') > 0) {
                        $field_use_global_list[] = $field_id;
                        /*$fields_choices_query = db_query(
                            "select id,value from app_global_lists_choices where lists_id='" . $field_cfg->get(
                                'use_global_list'
                            ) . "'"
                        );*/

                        $fields_choices_query = \K::model()->db_fetch('app_global_lists_choices', [
                            'lists_id = ?',
                            $field_cfg->get('use_global_list')
                        ], [], 'id,value');

                        //while ($fields_choices = db_fetch_array($fields_choices_query)) {
                        foreach ($fields_choices_query as $fields_choices) {
                            $fields_choices = $fields_choices->cast();

                            $html .= 'app_global_choices_values[' . $fields_choices['id'] . ']= ' . (strlen(
                                    $fields_choices['value']
                                ) ? $fields_choices['value'] : 0) . ';' . "\n";
                        }
                    } else {
                        /*$fields_choices_query = db_query(
                            "select id,value from app_fields_choices where fields_id='" . $field_id . "'"
                        );*/

                        $fields_choices_query = \K::model()->db_fetch('app_fields_choices', [
                            'fields_id = ?',
                            $field_id
                        ], [], 'id,value');

                        //while ($fields_choices = db_fetch_array($fields_choices_query)) {
                        foreach ($fields_choices_query as $fields_choices) {
                            $fields_choices = $fields_choices->cast();

                            $html .= 'app_choices_values[' . $fields_choices['id'] . ']= ' . (strlen(
                                    $fields_choices['value']
                                ) ? $fields_choices['value'] : 0) . ';' . "\n";
                        }
                    }
                }
            }
        }

        //prepare fields values and change handler
        if (preg_match_all("/\[([^]]*)\]/", $formula, $matches)) {
            $entities_id = $field['entities_id'];

            foreach ($matches[1] as $field_id) {
                if (isset(\K::$fw->app_fields_cache[$entities_id][$field_id])) {
                    switch (\K::$fw->app_fields_cache[$entities_id][$field_id]['type']) {
                        case 'fieldtype_parent_value':
                            $parent_value = new \Tools\FieldsTypes\Fieldtype_parent_value();
                            $value = $parent_value->output(
                                [
                                    'field' => \K::$fw->app_fields_cache[$entities_id][$field_id],
                                    'item' => ['parent_item_id' => \K::$fw->parent_entity_item_id],
                                    'output_db_value' => true
                                ]
                            );
                            $value = str_replace([' ', ','], ['', '.'], $value);
                            $value = (is_numeric($value) ? $value : 0);
                            $html .= 'var field_' . $field_id . ' = ' . $value . "\n";
                            break;
                        case 'fieldtype_input_numeric':
                            $html .= 'var field_' . $field_id . ' = ($("#fields_' . $field_id . '").val().length>0) ? Number($("#fields_' . $field_id . '").val()):0;' . "\n";
                            $html_change_handler .= '$("#fields_' . $field_id . '").on("input",function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        case 'fieldtype_js_formula':
                            $html .= 'var field_' . $field_id . ' = ($("#fields_' . $field_id . '").val().length>0) ? Number($("#fields_' . $field_id . '").val()):0;' . "\n";
                            break;
                        case 'fieldtype_dropdown_multiple':
                            $html .= 'var field_' . $field_id . ' = $("#fields_' . $field_id . '").val();' . "\n";
                            $html_change_handler .= '$("#fields_' . $field_id . '").change(function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        case 'fieldtype_dropdown_multilevel':
                            $html .= 'var field_' . $field_id . ' = new Array();' . "\n";
                            $html .= '$(".field_' . $field_id . '").each(function(){ field_' . $field_id . '.push($(this).val()); })' . "\n";
                            $html_change_handler .= '$(".field_' . $field_id . '").change(function(){ ' . $js_function_name_delay . ' })' . "\n";
                            break;
                        case 'fieldtype_checkboxes':
                            $html .= 'var field_' . $field_id . ' = new Array();' . "\n";
                            $html .= '$(".field_' . $field_id . ':checked").each(function(){ field_' . $field_id . '.push($(this).val()); })' . "\n";
                            $html_change_handler .= '$(".field_' . $field_id . '").change(function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        case 'fieldtype_radioboxes':
                            $html .= 'var field_' . $field_id . ' = ($(".field_' . $field_id . ':checked").val()>0) ? Number($(".field_' . $field_id . ':checked").val()):0;' . "\n";
                            $html_change_handler .= '$(".field_' . $field_id . '").change(function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        case 'fieldtype_dropdown':
                            $html .= 'var field_' . $field_id . ' = ($("#fields_' . $field_id . '").val()>0) ? Number($("#fields_' . $field_id . '").val()):0;' . "\n";
                            $html_change_handler .= '$("#fields_' . $field_id . '").change(function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        case 'fieldtype_boolean_checkbox':
                            $html .= 'var field_' . $field_id . ' = $("#fields_' . $field_id . '").is(":checked");' . "\n";
                            $html_change_handler .= '$("#fields_' . $field_id . '").change(function(){ ' . $js_function_name_delay . '})' . "\n";
                            break;
                        default:
                            $html .= '
    							var field_' . $field_id . ' = 0;
    							';
                            break;
                    }

                    $html .= 'if($(".form-group-' . $field_id . '").css("display") == "none"){ field_' . $field_id . '=0; }' . "\n";
                }

                //prepare fields
                $js_formula = str_replace('[' . $field_id . ']', 'field_' . $field_id, $js_formula);
            }
        }

        //set app_get_choices_values funciton 
        $js_formula = str_replace('get_value(', 'app_get_choices_values(', $js_formula);

        foreach ($field_use_global_list as $id) {
            $js_formula = str_replace(
                'app_get_choices_values(field_' . $id . ')',
                'app_get_global_choices_values(field_' . $id . ')',
                $js_formula
            );
        }

        $js_formula = \K::app_global_vars()->apply_to_text($js_formula);

        //try calculate js formula to value    
        $html .= '
    	try{	                          
    		 value = ' . $js_formula . ';
    		 value_html = value;';

        //toFixed() returns a string, with the number written with a specified number of decimals:
        $decimals = 2;
        $dec_point = '.';
        $thousands_sep = '';
        if (strlen($cfg->get('number_format')) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $decimals = $format[0];
            $dec_point = $format[1];
            $thousands_sep = $format[2];

            $html .= '
    		 value_html = number_format(value,"' . $decimals . '","' . $dec_point . '","' . $thousands_sep . '")';
        }

        //set value to field
        $html .= '
    		 $("#fields_' . $field['id'] . '").val(value).change();
    		 $("#fields_' . $field['id'] . '_html_value").html("' . $cfg->get('prefix') . '"+value_html+"' . $cfg->get(
                'suffix'
            ) . '")
    		} 
    		catch (err) {
					alert("' . \K::$fw->TEXT_JS_FORMULA_ERROR . ': ' . str_replace(["\n", "\r", "\n\r"],
                '',
                addslashes($js_formula)) . '"+"\n"+err)  				
				}
    	 }
							
			 $(function(){ 				
    		' . $html_change_handler . '
    		' . ($params['is_new_item'] ? $js_function_name : '') . '
    	 })
    	</script>	
    		';

        return $html . '<div id="fields_' . $field['id'] . '_html_value" class="form-control-static js-formula-value">' . $cfg->get(
                'prefix'
            ) . number_format((float)$obj['field_' . $field['id']], $decimals, $dec_point, $thousands_sep) . $cfg->get(
                'suffix'
            ) . '</div>' . \Helpers\Html::input_hidden_tag(
                'fields[' . $field['id'] . ']',
                $obj['field_' . $field['id']]
            );
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        return \Models\Main\Fields_types::outputFormula($options);
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_numeric_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    public function include_extra_js_fieldtypes($current_field)
    {
        $html = '';
        $fields_query = \K::model()->db_query_exec(
            "select f.id from app_fields f, app_forms_tabs t where f.forms_tabs_id = t.id and f.type = 'fieldtype_js_formula' and f.id != " . (int)$current_field['id'] . " and f.entities_id = " . (int)$current_field['entities_id'] . " order by t.sort_order, t.name, f.sort_order, f.name",
            null,
            'app_fields,app_forms_tabs'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $html .= ' form_handle_js_formula_' . $fields['id'] . '();';
        }

        return $html;
    }
}