<?php

class fieldtype_input_numeric
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_INPUT_NUMERIC_TITLE];
    }

    function get_configuration($params = [])
    {
        $cfg = [];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_WIDHT,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => TEXT_INPTUT_SMALL,
                'input-medium' => TEXT_INPUT_MEDIUM,
                'input-large' => TEXT_INPUT_LARGE,
                'input-xlarge' => TEXT_INPUT_XLARGE
            ],
            'tooltip' => TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => CFG_APP_NUMBER_FORMAT
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => tooltip_icon(TEXT_CALCULATE_TOTALS_INFO) . TEXT_CALCULATE_TOTALS,
            'name' => 'calclulate_totals',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => tooltip_icon(
                    TEXT_CALCULATE_AVERAGE_VALUE_INFO
                ) . TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => TEXT_DEFAULT_VALUE_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_DISPLAY_PREFIX_SUFFIX_IN_FORM,
            'name' => 'display_prefix_suffix_in_form',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_MIN_VALUE,
            'tooltip_icon' => TEXT_MIN_MAX_VALUE_TIP,
            'name' => 'min_value',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_MAX_VALUE,
            'tooltip_icon' => TEXT_MIN_MAX_VALUE_TIP,
            'name' => 'max_value',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        if (is_ext_installed()) {
            if (count(currencies::get_choices())) {
                $cfg[TEXT_VALUE][] = [
                    'title' => TEXT_EXT_CURRENCIES,
                    'name' => 'currencies',
                    'type' => 'dropdown',
                    'choices' => currencies::get_choices(),
                    'params' => ['class' => 'form-control input-medium chosen-select', 'multiple' => 'multiple']
                ];
            }
        }

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        global $app_currencies_cache;

        $value = $obj['field_' . $field['id']];

        $cfg = new fields_types_cfg($field['configuration']);

        //handle default value
        if ($params['is_new_item'] == true and strlen($cfg->get('default_value')) > 0) {
            $value = $cfg->get('default_value');
        }

        $decimals = 2;

        if (strlen($cfg->get('number_format')) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));
            $decimals = $format[0];
        }

        $attributes = [
            'class' => 'number form-control ' . $cfg->get('width') .
                ' fieldtype_input_numeric field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : '') .
                ($decimals == 0 ? ' digitsCustom' : '')
        ];
        //handle min/max values
        if (strlen($cfg->get('min_value')) and is_numeric($cfg->get('min_value'))) {
            $attributes['min'] = $cfg->get('min_value');
        }

        if (strlen($cfg->get('max_value')) and is_numeric($cfg->get('max_value'))) {
            $attributes['max'] = $cfg->get('max_value');
        }

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);


        //hande cusrrencies
        $html = '';
        $currencies = [];

        if (is_array($cfg->get('currencies'))) {
            $currencies = $cfg->get('currencies');
        } elseif (strlen($cfg->get('currencies'))) {
            $currencies = explode(',', $cfg->get('currencies'));
        }

        if (count($currencies) > 1) {
            foreach ($app_currencies_cache as $currency) {
                if (!in_array($currency['code'], $cfg->get('currencies'))) {
                    continue;
                }

                if ($currency['is_default'] == 1) {
                    $attributes = currencies::prepare_input_attributes(
                        $attributes,
                        $currency['code'],
                        '-' . $field['id'] . ' currency-field-grouped'
                    );
                    $attributes['data-field-id'] = $field['id'];
                } else {
                    $html .= '
    						<div class="input-group input-small" style="margin-top: 3px;">
									<span class="input-group-addon">' . $currency['symbol'] . '</span>
									' . input_tag(
                            'currency_' . $currency['code'],
                            '',
                            [
                                'class' => 'form-control currency-field-' . $field['id'] . ' currency-field-grouped',
                                'data-field-id' => $field['id'],
                                'data-currency-value' => $currency['value'],
                                'data-currency-default' => 0
                            ]
                        ) . '																				
								</div>
    					';
                }
            }
        } elseif (count($currencies) == 1) {
            $attributes = currencies::prepare_input_attributes($attributes, current($currencies));
        }

        //handle min by field value
        if (strlen($cfg->get('min_value')) and strstr($cfg->get('min_value'), '[')) {
            $field_val_id = (int)str_replace(['[', ']'], '', $cfg->get('min_value'));

            $html .= '
    		<script>
    			$(function(){
    			  $("#fields_' . $field['id'] . '").keyup(function(){ $(this).attr("min",number_format($("#fields_' . $field_val_id . '").val(),"' . $decimals . '",".","")) })
    			  $("#fields_' . $field_val_id . '").change(function(){ $("#fields_' . $field['id'] . '").attr("min",number_format($(this).val(),"' . $decimals . '",".","")) })
    			});
    		</script>
    			';
        }

        //handle max by field value
        if (strlen($cfg->get('max_value')) and strstr($cfg->get('max_value'), '[')) {
            $field_val_id = (int)str_replace(['[', ']'], '', $cfg->get('max_value'));

            $html .= '
    		<script>
    			$(function(){
    			  $("#fields_' . $field['id'] . '").keyup(function(){ $(this).attr("max",number_format($("#fields_' . $field_val_id . '").val(),"' . $decimals . '",".","")) })    			
    			  $("#fields_' . $field_val_id . '").change(function(){ $("#fields_' . $field['id'] . '").attr("max",number_format($(this).val(),"' . $decimals . '",".","")) })
    			});
    		</script>
    			';
        }

        if ($cfg->get('display_prefix_suffix_in_form') == 1 and (strlen($cfg->get('prefix')) or strlen(
                    $cfg->get('suffix')
                ))) {
            return '
    			<div class="input-group ' . $cfg->get('width') . '">
						' . (strlen($cfg->get('prefix')) ? '<span class="input-group-addon">' . $cfg->get(
                        'prefix'
                    ) . '</span>' : '')
                . input_tag('fields[' . $field['id'] . ']', $value, $attributes)
                . (strlen($cfg->get('suffix')) ? '<span class="input-group-addon">' . $cfg->get(
                        'suffix'
                    ) . '</span>' : '') .
                '</div>
				<label id="fields_' . $field['id'] . '-error" class="error" for="fields_' . $field['id'] . '" style="none"></label>		    
    			' . $html;
        } else {
            return input_tag('fields[' . $field['id'] . ']', $value, $attributes) . $html;
        }
    }

    function process($options)
    {
        global $app_changed_fields;

        if (!$options['is_new_item']) {
            $cfg = new fields_types_cfg($options['field']['configuration']);

            if ($options['value'] != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                $app_changed_fields[] = [
                    'name' => $options['field']['name'],
                    'value' => str_replace([',', ' '], ['.', ''], db_prepare_input($options['value'])),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $options['value'],
                    'current_field_value' => $options['current_field_value'],
                    'current_value' => str_replace([',', ' '],
                        ['.', ''],
                        db_prepare_input($options['current_field_value'])),
                ];
            }
        }

        return str_replace([',', ' '], ['.', ''], db_prepare_input($options['value']));
    }

    function output($options)
    {
        //return non-formated value if export
        if (isset($options['is_export']) and !isset($options['is_print'])) {
            return $options['value'];
        }

        $cfg = new fields_types_cfg($options['field']['configuration']);

        if (strlen($cfg->get('number_format')) > 0 and strlen($options['value']) > 0 and is_numeric(
                $options['value']
            )) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($options['value'], $format[0], $format[1], $format[2]);
        } else {
            $value = $options['value'];
        }

        //add prefix and sufix
        $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');

        return $value;
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = reports::prepare_numeric_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }

    static function number_format($value, $configuration)
    {
        $cfg = new fields_types_cfg($configuration);

        if (strlen($cfg->get('number_format')) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($value, $format[0], $format[1], $format[2]);

            //add prefix and sufix
            $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
        }

        return $value;
    }
}