<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_vpic
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_VPIC_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_VPIC_AUTO_FILL_FIELDS,
            'name' => 'auto_fill_fields',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_VPIC_AUTO_FILL_FIELDS_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_VPIC_OTHER_DETAILS,
            'name' => 'other_details',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_VPIC_OTHER_DETAILS_TIP,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => \Models\Main\Fields_types::get_is_unique_choices(\K::$fw->POST['entities_id']),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control input-medium' .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
            'maxlength' => 17,
        ];

        $attributes = \Models\Main\Fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return '
    		<div class="input-group input-medium">' .
            \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes) .
            '<div class="input-group-btn">  			
	        		<button type="button" title="' . \K::$fw->TEXT_DECODE_VIN . '" class="btn btn-default vpic-vin-decoder" data-field-id="' . $field['id'] . '" data-toggle="dropdown"><i class="fa fa-search"></i></button>
	        				<div class="dropdown-menu hold-on-click dropdown-checkboxes" role="menu">
  									<div id="field_' . $field['id'] . '_vin_data">
  											
  									</div>	  			
	  			        </div>
  			
	  		   </div>
	    	</div>
	      ';
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if (isset($options['is_export'])) {
            return $options['value'];
        } elseif ($options['field']['is_heading']) {
            return $options['value'];
        } else {
            return '<a href="https://vpic.nhtsa.dot.gov/decoder/Decoder?VIN=' . $options['value'] . '" target="blank">' . $options['value'] . '</a>';
        }
    }
}