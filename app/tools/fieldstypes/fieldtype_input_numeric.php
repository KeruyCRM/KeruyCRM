<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_numeric
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_NUMERIC_TITLE];
    }

    public function get_configuration($params = [])
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => \K::$fw->CFG_APP_NUMBER_FORMAT
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_CALCULATE_TOTALS_INFO) . \K::$fw->TEXT_CALCULATE_TOTALS,
            'name' => 'calculate_totals',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \Helpers\App::tooltip_icon(
                    \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE_INFO
                ) . \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => \Models\Main\Fields_types::get_is_unique_choices(\K::$fw->POST['entities_id']),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_VALUE_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_DISPLAY_PREFIX_SUFFIX_IN_FORM,
            'name' => 'display_prefix_suffix_in_form',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_MIN_VALUE,
            'tooltip_icon' => \K::$fw->TEXT_MIN_MAX_VALUE_TIP,
            'name' => 'min_value',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VALUE][] = [
            'title' => \K::$fw->TEXT_MAX_VALUE,
            'tooltip_icon' => \K::$fw->TEXT_MIN_MAX_VALUE_TIP,
            'name' => 'max_value',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        if (\Helpers\App::is_ext_installed()) {
            if (count(currencies::get_choices())) {
                $cfg[\K::$fw->TEXT_VALUE][] = [
                    'title' => \K::$fw->TEXT_EXT_CURRENCIES,
                    'name' => 'currencies',
                    'type' => 'dropdown',
                    'choices' => currencies::get_choices(),
                    'params' => ['class' => 'form-control input-medium chosen-select', 'multiple' => 'multiple']
                ];
            }
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $value = $obj['field_' . $field['id']];

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

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

        $attributes = \Models\Main\Fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        //hande currencies
        $html = '';
        $currencies = [];

        if (is_array($cfg->get('currencies'))) {
            $currencies = $cfg->get('currencies');
        } elseif (strlen($cfg->get('currencies'))) {
            $currencies = explode(',', $cfg->get('currencies'));
        }

        if (count($currencies) > 1) {
            foreach (\K::$fw->app_currencies_cache as $currency) {
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
									' . \Helpers\Html::input_tag(
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
                . \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $value, $attributes)
                . (strlen($cfg->get('suffix')) ? '<span class="input-group-addon">' . $cfg->get(
                        'suffix'
                    ) . '</span>' : '') .
                '</div>
				<label id="fields_' . $field['id'] . '-error" class="error" for="fields_' . $field['id'] . '" style="none"></label>		    
    			' . $html;
        } else {
            return \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $value, $attributes) . $html;
        }
    }

    public function process($options)
    {
        if (!$options['is_new_item']) {
            $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

            if ($options['value'] != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1) {
                \K::$fw->app_changed_fields[] = [
                    'name' => $options['field']['name'],
                    'value' => str_replace([',', ' '], ['.', ''], \K::model()->db_prepare_input($options['value'])),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $options['value'],
                    'current_field_value' => $options['current_field_value'],
                    'current_value' => str_replace([',', ' '],
                        ['.', ''],
                        \K::model()->db_prepare_input($options['current_field_value'])),
                ];
            }
        }

        return str_replace([',', ' '], ['.', ''], \K::model()->db_prepare_input($options['value']));
    }

    public function output($options)
    {
        //return non-formatted value if export
        if (isset($options['is_export']) and !isset($options['is_print'])) {
            return $options['value'];
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (strlen($cfg->get('number_format')) > 0 and strlen($options['value']) > 0 and is_numeric(
                $options['value']
            )) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($options['value'], $format[0], $format[1], $format[2]);
        } else {
            $value = $options['value'];
        }

        //add prefix and suffix
        return (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
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

    public static function number_format($value, $configuration)
    {
        $cfg = new \Models\Main\Fields_types_cfg($configuration);

        if (strlen($cfg->get('number_format')) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($value, $format[0], $format[1], $format[2]);

            //add prefix and suffix
            $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
        }

        return $value;
    }
}