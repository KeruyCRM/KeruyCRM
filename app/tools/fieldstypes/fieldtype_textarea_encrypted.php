<?php

namespace Tools\FieldsTypes;

class Fieldtype_textarea_encrypted
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TEXTAREA_ENCRYPTED_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        //$encryption_key = (defined('DB_ENCRYPTION_KEY') ? DB_ENCRYPTION_KEY : '');
        \K::fw()->exists('DB_ENCRYPTION_KEY', $encryption_key);

        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . \K::$fw->TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">' . input_tag(
                'encryption_key',
                $encryption_key,
                ['class' => 'form-control input-large required', 'readonly' => 'readonly']
            ) . tooltip_text(\K::$fw->TEXT_ENCRYPTION_KEY_INFO) . '
        	        </div>
    	        </div>
            ';

        $cfg[] = ['type' => 'html', 'html' => $html];

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
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
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

        return $cfg;
    }

    public function render($field, $obj, $params = [])
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

    public function process($options)
    {
        global $alerts;

        if (!db_has_encryption_key()) {
            $alerts->add(sprintf(\K::$fw->TEXT_ENCRYPTION_KEY_ERROR, $options['field']['name']), 'error');
            return '';
        }

        $value = str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
        $value_query = db_query(
            "select AES_ENCRYPT('" . db_input(trim($value)) . "','" . db_input(
                \K::$fw->DB_ENCRYPTION_KEY
            ) . "') as text",
            false
        );
        $value = db_fetch_array($value_query);

        return $value['text'];
    }

    public function output($options)
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