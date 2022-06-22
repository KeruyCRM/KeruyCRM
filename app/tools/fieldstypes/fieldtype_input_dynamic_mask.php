<?php

namespace Tools\FieldsTypes;

class Fieldtype_input_dynamic_mask
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::f3()->TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::f3()->TEXT_INPUT_SMALL,
                'input-medium' => \K::f3()->TEXT_INPUT_MEDIUM,
                'input-large' => \K::f3()->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::f3()->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::f3()->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_INPUT_FIELD_MASK,
            'name' => 'mask',
            'type' => 'input',
            'tooltip' => \K::f3()->TEXT_INPUT_FIELD_MASK_TIP . '<br>' .
                \K::f3()->TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_INFO . '<br>' . \K::f3(
                )->TEXT_EXAMPLE . ': aa-9{1,4} <br><br>' .
                \K::f3()->TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_OPTIONAL_INFO . '<br>' . \K::f3(
                )->TEXT_EXAMPLE . ': 999[-999]',
            'params' => ['class' => 'form-control']
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => \K::f3()->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::f3()->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::f3()->TEXT_DEFAULT . ': ' . \K::f3()->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[\K::f3()->TEXT_JS_CODE][] = [
            'title' => '',
            'name' => 'js_code',
            'type' => 'code',
            'params' => ['class' => 'form-control']
        ];
        $cfg[\K::f3()->TEXT_JS_CODE . ' (' . \K::f3()->TEXT_EXAMPLE . ')'][] = [
            'type' => 'html',
            'html' => '
<textarea class="form-control code" style="height: 300px" readonly>
{
    mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
    greedy: false,
    clearIncomplete:true,
    onBeforePaste: function (pastedValue, opts) {
      pastedValue = pastedValue.toLowerCase();
      return pastedValue.replace("mailto:", "");
    },
    definitions: {
      \'*\': {
        validator: "[0-9A-Za-z!#$%&\'*+/=?^_`{|}~\-]",
        casing: "lower"
      }
    }
}</textarea>
            '
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
        ];

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        $script = '';

        if (strlen($cfg->get('js_code'))) {
            $error_info = \K::f3()->TEXT_ERROR . ' ' . \K::f3()->TEXT_FIELD . ' #' . $field['id'] . ' (' . addslashes(
                    $field['name']
                ) . ')';
            $script = '
                <script>
                  jQuery(function($){  
                    try{                    
                        $(".field_' . $field['id'] . '").inputmask(' . $cfg->get('js_code') . ');               
                    }catch(error){        
                        alert("' . $error_info . '"+`\n`+error)
                    }
                  });
                </script>';
        } elseif (strlen($cfg->get('mask'))) {
            $script = '
                <script>
                  jQuery(function($){                                    
                     $(".field_' . $field['id'] . '").inputmask({
                        mask: "' . $cfg->get('mask') . '",
                        greedy: false,
                        clearIncomplete:true,
                        definitions: {
                            "я": {
                              validator: "[А-ЯЁа-яё]"                              
                            }
                          }
                    });               
                  });
                </script>';
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
