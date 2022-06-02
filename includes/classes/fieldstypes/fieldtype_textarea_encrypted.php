<?php

class fieldtype_textarea_encrypted
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_TEXTAREA_ENCRYPTED_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $encryption_key = (defined('DB_ENCRYPTION_KEY') ? DB_ENCRYPTION_KEY : '');

        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">' . input_tag(
                'encryption_key',
                $encryption_key,
                ['class' => 'form-control input-large required', 'readonly' => 'readonly']
            ) . tooltip_text(TEXT_ENCRYPTION_KEY_INFO) . '
        	        </div>
    	        </div>
            ';

        $cfg[] = ['type' => 'html', 'html' => $html];

        $cfg[] = [
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

        $cfg[] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
            'title' => TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        $cfg = fields_types::parse_configuration($field['configuration']);

        $attributes = [
            'rows' => '3',
            'class' => 'form-control ' . $cfg['width'] . ($field['is_heading'] == 1 ? ' autofocus' : '') . ' fieldtype_textarea field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        $value = fieldtype_input_encrypted::decrypt_value($obj['field_' . $field['id']]);

        return textarea_tag(
            'fields[' . $field['id'] . ']',
            str_replace(['&lt;', '&gt;'], ['<', '>'], $value),
            $attributes
        );
    }

    function process($options)
    {
        global $alerts;

        if (!db_has_encryption_key()) {
            $alerts->add(sprintf(TEXT_ENCRYPTION_KEY_ERROR, $options['field']['name']), 'error');
            return '';
        }

        $value = str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
        $value_query = db_query(
            "select AES_ENCRYPT('" . db_input(trim($value)) . "','" . db_input(DB_ENCRYPTION_KEY) . "') as text",
            false
        );
        $value = db_fetch_array($value_query);

        return $value['text'];
    }

    function output($options)
    {
        if (isset($options['is_export'])) {
            return (!isset($options['is_print']) ? str_replace(['&lt;', '&gt;'], ['<', '>'], $options['value']) : nl2br(
                $options['value']
            ));
        } else {
            return auto_link_text(nl2br($options['value']));
        }
    }
}