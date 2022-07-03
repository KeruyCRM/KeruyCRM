<?php

namespace Tools\FieldsTypes;

class Fieldtype_input_masked
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_MASKED];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_INPUT_FIELD_MASK,
            'name' => 'mask',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_INPUT_FIELD_MASK_TIP,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_INPUT_FIELD_MASK_DEFINITIONS,
            'name' => 'mask_definitions',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_INPUT_FIELD_MASK_DEFINITIONS_TIP_ICON,
            'tooltip' => \K::$fw->TEXT_INPUT_FIELD_MASK_DEFINITIONS_TIP,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
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
        $cfg = new \Tools\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
        ];

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        $script = '';

        if (strlen($cfg->get('mask')) > 0) {
            $mask_definitions = '';
            if (strlen($cfg->get('mask_definitions'))) {
                foreach (explode("\n", $cfg->get('mask_definitions')) as $v) {
                    $vv = explode('=', $v, 2);
                    $mask_definitions .= "$.mask.definitions['" . trim($vv[0]) . "']='" . trim($vv[1]) . "';\n";
                }
            }

            $script = '
        <script>
          jQuery(function($){   
      			 ' . $mask_definitions . '	
             $(".field_' . $field['id'] . '").mask("' . $cfg->get('mask') . '");                 
          });
        </script>
      ';
        }

        return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes) . $script;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        return $options['value'];
    }
}