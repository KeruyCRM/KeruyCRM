<?php

class fieldtype_progress
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_PROGRESS_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_SETP,
            'name' => 'step',
            'type' => 'dropdown',
            'choices' => ['5' => 5, '10' => 10, '1' => 1],
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[TEXT_PROGRESS_BAR][] = [
            'title' => TEXT_DISPLAY_PROGRESS_BAR,
            'name' => 'display_progress_bar',
            'type' => 'checkbox'
        ];
        $cfg[TEXT_PROGRESS_BAR][] = [
            'title' => TEXT_MIN_WIDTH,
            'tooltip_icon' => TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_LBANK,
            'name' => 'bar_min_width',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_PROGRESS_BAR][] = ['title' => TEXT_COLOR, 'name' => 'bar_color', 'type' => 'colorpicker'];

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

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

    static function get_choices($cfg)
    {
        $choices = [];

        for ($i = $cfg->get('step'); $i <= 100; $i += $cfg->get('step')) {
            $choices[$i] = $i . '%';
        }

        return $choices;
    }

    function process($options)
    {
        return $options['value'];
    }

    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

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

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        $sql_query[] = $prefix . '.field_' . $filters['fields_id'] . ($filters['filters_condition'] == 'include' ? ' in ' : ' not in ') . '(' . $filters['filters_values'] . ') ';

        return $sql_query;
    }
}