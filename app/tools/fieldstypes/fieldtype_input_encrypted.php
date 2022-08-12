<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_encrypted
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_ENCRYPTED_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $encryption_key = \K::fw()->exists('DB_ENCRYPTION_KEY') ?? '';

        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . \K::$fw->TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">' . \Helpers\Html::input_tag(
                'encryption_key',
                $encryption_key,
                ['class' => 'form-control input-large required', 'readonly' => 'readonly']
            ) . \Helpers\App::tooltip_text(\K::$fw->TEXT_ENCRYPTION_KEY_INFO) . '
        	        </div>			
    	        </div>
            ';

        $cfg[\K::$fw->TEXT_SETTINGS][] = ['type' => 'html', 'html' => $html];

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
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_HIDE_VALUE][] = [
            'title' => \K::$fw->TEXT_HIDE_VALUE,
            'name' => 'hide_value',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_HIDE_VALUE][] = [
            'title' => \K::$fw->TEXT_REPLACE_WITH_SYMBOL,
            'name' => 'replace_symbol',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': X',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_HIDE_VALUE][] = [
            'title' => \K::$fw->TEXT_DISCLOSE_NUMBER_FIRST_LETTERS,
            'name' => 'count_first_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];
        $cfg[\K::$fw->TEXT_HIDE_VALUE][] = [
            'title' => \K::$fw->TEXT_DISCLOSE_NUMBER_LAST_LETTERS,
            'name' => 'count_last_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];

        $choices = [];
        $choices[0] = \K::$fw->TEXT_ADMINISTRATOR;

        //$groups_query = db_fetch_all('app_access_groups', '', 'sort_order, name');

        $groups_query = \K::model()->db_fetch('app_access_groups', [], ['order' => 'sort_order, name'], 'id,name');

        //while ($groups = db_fetch_array($groups_query)) {
        foreach ($groups_query as $groups) {
            $groups = $groups->cast();

            $entities_access_schema = \Models\Main\Users\Users::get_entities_access_schema(
                \K::$fw->POST['entities_id'],
                $groups['id']
            );

            if (!in_array('view', $entities_access_schema) and !in_array('view_assigned', $entities_access_schema)) {
                continue;
            }

            $choices[$groups['id']] = $groups['name'];
        }

        $cfg[\K::$fw->TEXT_HIDE_VALUE][] = [
            'title' => \K::$fw->TEXT_USERS_GROUPS,
            'name' => 'users_groups',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_INPUT_PROTECTED_USERS_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        return $cfg;
    }

    public static function decrypt_value($value)
    {
        if (!strlen($value) or !\K::model()->db_has_encryption_key()) {
            return '';
        }

        //check if value is encrypted
        if (!ctype_print($value)) {
            /*$value_query = db_query(
                "select AES_DECRYPT('" . db_input($value) . "','" . db_input(
                    \K::$fw->DB_ENCRYPTION_KEY
                ) . "') as text",
                false
            );
            $value = db_fetch_array($value_query);*/

            $value = \K::model()->db_query_exec_one(
                'select AES_DECRYPT(?,?) as text',
                [$value, \K::$fw->DB_ENCRYPTION_KEY],
                '',
                false
            );

            return $value['text'];
        } else {
            return $value;
        }
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        return \Helpers\Html::input_tag(
            'fields[' . $field['id'] . ']',
            self::decrypt_value($obj['field_' . $field['id']]),
            $attributes
        );
    }

    public function process($options)
    {
        if (!\K::model()->db_has_encryption_key()) {
            \K::flash()->addMessage(sprintf(\K::$fw->TEXT_ENCRYPTION_KEY_ERROR, $options['field']['name']), 'error');
            return '';
        }

        if (strlen($options['value'])) {
            /*$value_query = db_query(
                "select AES_ENCRYPT('" . db_input(trim($options['value'])) . "','" . db_input(
                    \K::$fw->DB_ENCRYPTION_KEY
                ) . "') as text",
                false
            );
            $value = db_fetch_array($value_query);*/

            $value = \K::model()->db_query_exec_one(
                'select AES_ENCRYPT(?,?) as text',
                [trim($options['value']), \K::$fw->DB_ENCRYPTION_KEY],
                '',
                false
            );

            return $value['text'];
        } else {
            return '';
        }
    }

    public function output($options)
    {
        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('hide_value') != '1') {
            return $options['value'];
        }

        $users_groups = $cfg->get('users_groups');

        if (is_array($users_groups)) {
            if (in_array(\K::$fw->app_user['group_id'], $users_groups)) {
                return $options['value'];
            }
        }

        $value_array = str_split($options['value']);
        $value_str = '';

        $replace_symbol = strlen($cfg->get('replace_symbol')) ? $cfg->get('replace_symbol') : 'X';
        $count_first_letters = strlen($cfg->get('count_first_letters')) ? (int)$cfg->get('count_first_letters') : 0;
        $count_last_letters = strlen($cfg->get('count_last_letters')) ? (int)$cfg->get('count_last_letters') : 0;

        for ($i = 0; $i < count($value_array); $i++) {
            if (($i < $count_first_letters and $count_first_letters > 0) or ($i >= (count(
                            $value_array
                        ) - $count_last_letters) and $count_last_letters > 0)) {
                $value_str .= $value_array[$i];
            } else {
                $value_str .= $replace_symbol;
            }
        }

        return $value_str;
    }

    public static function prepare_query_select($entities_id, $listing_sql_query_select = '')
    {
        foreach (\K::$fw->app_fields_cache[$entities_id] as $field) {
            if (in_array($field['type'], ['fieldtype_input_encrypted', 'fieldtype_textarea_encrypted']
                ) and \K::model()->db_has_encryption_key()) {
                $listing_sql_query_select .= ", AES_DECRYPT(field_" . (int)$field['id'] . "," . \K::model()->quote(
                        \K::$fw->DB_ENCRYPTION_KEY
                    ) . ") as field_" . (int)$field['id'];
            }
        }

        return $listing_sql_query_select;
    }
}