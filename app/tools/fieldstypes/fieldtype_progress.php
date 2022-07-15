<?php

namespace Tools\FieldsTypes;

class Fieldtype_progress
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_PROGRESS_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];
        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_STEP,
            'name' => 'step',
            'type' => 'dropdown',
            'choices' => ['5' => 5, '10' => 10, '1' => 1],
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_PROGRESS_BAR][] = [
            'title' => \K::$fw->TEXT_DISPLAY_PROGRESS_BAR,
            'name' => 'display_progress_bar',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_PROGRESS_BAR][] = [
            'title' => \K::$fw->TEXT_MIN_WIDTH,
            'tooltip_icon' => \K::$fw->TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_BLANK,
            'name' => 'bar_min_width',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[\K::$fw->TEXT_PROGRESS_BAR][] = [
            'title' => \K::$fw->TEXT_COLOR,
            'name' => 'bar_color',
            'type' => 'colorpicker'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = ['class' => 'form-control input-small fieldtype_input field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

        $value = $obj['field_' . $field['id']];

        $choices = [];
        if ($params['form'] == 'comment') {
            $choices[''] = '';
            $value = '';
        } else {
            $choices['0'] = '0%';
        }

        $choices = $choices + self::get_choices($cfg);

        return select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
    }

    public static function get_choices($cfg)
    {
        $choices = [];

        for ($i = $cfg->get('step'); $i <= 100; $i += $cfg->get('step')) {
            $choices[$i] = $i . '%';
        }

        return $choices;
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (strlen($options['value']) > 0) {
            if (isset($options['is_export'])) {
                return $options['value'] . '%';
            } elseif ($cfg->get('display_progress_bar') == 1) {
                $min_width = (int)$cfg->get('bar_min_width');
                $html = '
	    			<div class="progress" style="' . ($min_width > 0 ? 'min-width: ' . $min_width . 'px' : '') . '">	
	    				<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="' . $options['value'] . '" aria-valuemin="0" aria-valuemax="100" 
	    							style="width: ' . $options['value'] . '%; ' . (strlen(
                        $cfg->get('bar_color')
                    ) ? '    background-color: ' . $cfg->get('bar_color') : '') . '; text-align: left; padding-left: 5px;">
								<span>
									 ' . $options['value'] . '%
								</span>
							</div>
	    			</div>
    				';

                return $html;
            } else {
                return $options['value'] . '%';
            }
        } else {
            return '';
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        $sql_query[] = $prefix . '.field_' . $filters['fields_id'] . ($filters['filters_condition'] == 'include' ? ' in ' : ' not in ') . '(' . $filters['filters_values'] . ') ';

        return $sql_query;
    }
}