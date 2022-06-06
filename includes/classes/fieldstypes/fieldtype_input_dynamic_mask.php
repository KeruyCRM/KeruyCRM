<?php

class fieldtype_input_dynamic_mask
{

    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => TEXT_INPUT_SMALL,
                'input-medium' => TEXT_INPUT_MEDIUM,
                'input-large' => TEXT_INPUT_LARGE,
                'input-xlarge' => TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_INPUT_FIELD_MASK,
            'name' => 'mask',
            'type' => 'input',
            'tooltip' => TEXT_INPUT_FIELD_MASK_TIP . '<br>' .
                TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_INFO . '<br>' . TEXT_EXAMPLE . ': aa-9{1,4} <br><br>' .
                TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_OPTIONAL_INFO . '<br>' . TEXT_EXAMPLE . ': 999[-999]',
            'params' => ['class' => 'form-control']
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

        $cfg[TEXT_JS_CODE][] = [
            'title' => '',
            'name' => 'js_code',
            'type' => 'code',
            'params' => ['class' => 'form-control']
        ];
        $cfg[TEXT_JS_CODE . ' (' . TEXT_EXAMPLE . ')'][] = [
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

    function render($field, $obj, $params = [])
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
            $error_info = TEXT_ERROR . ' ' . TEXT_FIELD . ' #' . $field['id'] . ' (' . addslashes($field['name']) . ')';
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

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        return $options['value'];
    }

}
