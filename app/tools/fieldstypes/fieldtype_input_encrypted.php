<?php

namespace Tools\FieldsTypes;

class Fieldtype_input_encrypted
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::f3()->TEXT_FIELDTYPE_INPUT_ENCRYPTED_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $encryption_key = (defined('DB_ENCRYPTION_KEY') ? DB_ENCRYPTION_KEY : '');

        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . \K::f3()->TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">' . input_tag(
                'encryption_key',
                $encryption_key,
                ['class' => 'form-control input-large required', 'readonly' => 'readonly']
            ) . tooltip_text(\K::f3()->TEXT_ENCRYPTION_KEY_INFO) . '
        	        </div>			
    	        </div>
            ';

        $cfg[\K::f3()->TEXT_SETTINGS][] = ['type' => 'html', 'html' => $html];

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
            'title' => \K::f3()->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::f3()->TEXT_SETTINGS][] = [
            'title' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::f3()->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::f3()->TEXT_HIDE_VALUE][] = [
            'title' => \K::f3()->TEXT_HIDE_VALUE,
            'name' => 'hide_value',
            'type' => 'checkbox'
        ];

        $cfg[\K::f3()->TEXT_HIDE_VALUE][] = [
            'title' => \K::f3()->TEXT_REPLACE_WITH_SYMBOL,
            'name' => 'replace_symbol',
            'type' => 'input',
            'tooltip' => \K::f3()->TEXT_DEFAULT . ': X',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::f3()->TEXT_HIDE_VALUE][] = [
            'title' => \K::f3()->TEXT_DISCLOSE_NUMBER_FIRST_LETTERS,
            'name' => 'count_first_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];
        $cfg[\K::f3()->TEXT_HIDE_VALUE][] = [
            'title' => \K::f3()->TEXT_DISCLOSE_NUMBER_LAST_LETTERS,
            'name' => 'count_last_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];

        $choices = [];
        $choices[0] = \K::f3()->TEXT_ADMINISTRATOR;

        $groups_query = db_fetch_all('app_access_groups', '', 'sort_order, name');
        while ($groups = db_fetch_array($groups_query)) {
            $entities_access_schema = users::get_entities_access_schema($_POST['entities_id'], $groups['id']);

            if (!in_array('view', $entities_access_schema) and !in_array('view_assigned', $entities_access_schema)) {
                continue;
            }

            $choices[$groups['id']] = $groups['name'];
        }

        $cfg[\K::f3()->TEXT_HIDE_VALUE][] = [
            'title' => \K::f3()->TEXT_USERS_GROUPS,
            'name' => 'users_groups',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::f3()->TEXT_FIELDTYPE_INPUT_PROTECTED_USERS_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

        return $cfg;
    }

    public static function decrypt_value($value)
    {
        if (!strlen($value) or !db_has_encryption_key()) {
            return '';
        }

        //check if value is encrypted
        if (!ctype_print($value)) {
            $value_query = db_query(
                "select AES_DECRYPT('" . db_input($value) . "','" . db_input(
                    \K::f3()->DB_ENCRYPTION_KEY
                ) . "') as text",
                false
            );
            $value = db_fetch_array($value_query);

            return $value['text'];
        } else {
            return $value;
        }
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        return input_tag(
            'fields[' . $field['id'] . ']',
            self::decrypt_value($obj['field_' . $field['id']]),
            $attributes
        );
    }

    public function process($options)
    {
        global $alerts;

        if (!db_has_encryption_key()) {
            $alerts->add(sprintf(\K::f3()->TEXT_ENCRYPTION_KEY_ERROR, $options['field']['name']), 'error');
            return '';
        }

        if (strlen($options['value'])) {
            $value_query = db_query(
                "select AES_ENCRYPT('" . db_input(trim($options['value'])) . "','" . db_input(
                    \K::f3()->DB_ENCRYPTION_KEY
                ) . "') as text",
                false
            );
            $value = db_fetch_array($value_query);
            return $value['text'];
        } else {
            return '';
        }
    }

    public function output($options)
    {
        global $app_user;

        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('hide_value') != '1') {
            return $options['value'];
        }

        $users_groups = $cfg->get('users_groups');

        if (is_array($users_groups)) {
            if (in_array($app_user['group_id'], $users_groups)) {
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
        global $app_fields_cache;

        foreach ($app_fields_cache[$entities_id] as $field) {
            if (in_array($field['type'], ['fieldtype_input_encrypted', 'fieldtype_textarea_encrypted']
                ) and db_has_encryption_key()) {
                $listing_sql_query_select .= ", AES_DECRYPT(field_" . $field['id'] . ",'" . db_input(
                        \K::f3()->DB_ENCRYPTION_KEY
                    ) . "') as field_" . $field['id'];
            }
        }

        return $listing_sql_query_select;
    }
}