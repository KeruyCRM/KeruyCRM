<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_protected
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_PROTECTED_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_REPLACE_WITH_SYMBOL,
            'name' => 'replace_symbol',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': X',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISCLOSE_NUMBER_FIRST_LETTERS,
            'name' => 'count_first_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISCLOSE_NUMBER_LAST_LETTERS,
            'name' => 'count_last_letters',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small number']
        ];

        $choices = [];
        $choices[0] = \K::$fw->TEXT_ADMINISTRATOR;

        //$groups_query = db_fetch_all('app_access_groups', '', 'sort_order, name');

        $groups_query = \K::model()->db_fetch('app_access_groups', [], ['order' => 'sort_order,name'], 'id,name');

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

        $cfg[] = [
            'title' => \K::$fw->TEXT_USERS_GROUPS,
            'name' => 'users_groups',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_INPUT_PROTECTED_USERS_GROUPS_TIP,
            'params' => ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
        ];

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
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : '')
        ];

        $attributes = \Models\Main\Fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

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
}