<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

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

        //\K::fw()->exists('DB_ENCRYPTION_KEY', $encryption_key);

        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . \K::$fw->TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">' . \Helpers\Html::input_tag(
                'encryption_key',
                \K::$fw->DB_ENCRYPTION_KEY,
                ['class' => 'form-control input-large required', 'readonly' => 'readonly']
            ) . \Helpers\App::tooltip_text(\K::$fw->TEXT_ENCRYPTION_KEY_INFO) . '
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
        $cfg = \Models\Main\Fields_types::parse_configuration($field['configuration']);

        $attributes = [
            'rows' => '3',
            'class' => 'form-control ' . $cfg['width'] . ($field['is_heading'] == 1 ? ' autofocus' : '') . ' fieldtype_textarea field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        $value = \Tools\FieldsTypes\Fieldtype_input_encrypted::decrypt_value($obj['field_' . $field['id']]);

        return \Helpers\Html::textarea_tag(
            'fields[' . $field['id'] . ']',
            str_replace(['&lt;', '&gt;'], ['<', '>'], $value),
            $attributes
        );
    }

    public function process($options)
    {
        if (!\K::model()->db_has_encryption_key()) {
            \K::flash()->addMessage(sprintf(\K::$fw->TEXT_ENCRYPTION_KEY_ERROR, $options['field']['name']), 'error');
            return '';
        }

        $value = str_replace(['<', '>'], ['&lt;', '&gt;'], $options['value']);
        /*$value_query = db_query(
            "select AES_ENCRYPT('" . db_input(trim($value)) . "','" . db_input(
                \K::$fw->DB_ENCRYPTION_KEY
            ) . "') as text",
            false
        );
        $value = db_fetch_array($value_query);*/

        $value = \K::model()->db_query_exec_one(
            'select AES_ENCRYPT(?,?) as text',
            [trim($value), \K::$fw->DB_ENCRYPTION_KEY],
            '',
            false
        );

        return $value['text'];
    }

    public function output($options)
    {
        if (isset($options['is_export'])) {
            return (!isset($options['is_print']) ? str_replace(['&lt;', '&gt;'], ['<', '>'], $options['value']) : nl2br(
                $options['value']
            ));
        } else {
            return \Helpers\Urls::auto_link_text(nl2br($options['value']));
        }
    }
}