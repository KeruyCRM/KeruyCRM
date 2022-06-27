<?php

namespace Tools\FieldsTypes;

class Fieldtype_random_value
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_RANDOM_VALUE];
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
            'title' => \K::$fw->TEXT_VALUE_LENGTH,
            'name' => 'value_length',
            'default' => 5,
            'type' => 'input',
            'params' => ['class' => 'form-control input-xsmall required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CHARACTERS,
            'name' => 'value_characters',
            'type' => 'textarea',
            'tooltip_icon' => \K::$fw->TEXT_CHARACTERS_TIP,
            'params' => ['class' => 'form-control textarea-small'],
            'tooltip' => '~!@#$%^&*()_+abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKMNOPQRSTUVWXYZ'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SPLIT_VALUE,
            'name' => 'split_value',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_SPLIT_VALUE_INFO,
            'params' => ['class' => 'form-control input-xsmall']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SPLIT_VALUE_CHAR,
            'name' => 'split_value_char',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_SPLIT_VALUE_CHAR_INFO,
            'params' => ['class' => 'form-control input-xsmall']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_START_ROW,
            'name' => 'start_row',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_START_ROW_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_END_ROW,
            'name' => 'end_row',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '<p class="form-control-static">' . $obj['field_' . $field['id']] . '</p>' . input_hidden_tag(
                'fields[' . $field['id'] . ']',
                $obj['field_' . $field['id']]
            );
    }

    public function process($options)
    {
        if ($options['is_new_item'] or !strlen($options['value'])) {
            do {
                $value = $this->render_random_value($options);

                $check_query = db_query(
                    "select id from app_entity_" . $options['field']['entities_id'] . " where field_" . $options['field']['id'] . "='" . db_input(
                        $value
                    ) . "'"
                );
            } while ($check = db_fetch_array($check_query));

            //echo $value;
            //exit();

            return $value;
        } else {
            return db_prepare_input($options['value']);
        }
    }

    public function output($options)
    {
        return $options['value'];
    }

    public function render_random_value($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

        $characters = (strlen($cfg->get('value_characters')) ? $cfg->get('value_characters') : '0123456789');
        $value_length = (strlen($cfg->get('value_length')) ? $cfg->get('value_length') : 5);
        $split_value = (strlen($cfg->get('split_value')) ? $cfg->get('split_value') : 0);
        $split_value_char = (strlen($cfg->get('split_value_char')) ? $cfg->get('split_value_char') : '-');
        $start_row = (strlen($cfg->get('start_row')) ? $cfg->get('start_row') : '');
        $end_row = (strlen($cfg->get('end_row')) ? $cfg->get('end_row') : '');

        $value = '';

        if ($split_value > 1) {
            $value_array = [];
            for ($j = 0; $j < $split_value; $j++) {
                $value_array[$j] = '';

                for ($i = 0; $i < floor($value_length / $split_value); $i++) {
                    $value_array[$j] .= $characters[rand(0, strlen($characters) - 1)];
                }
            }

            $value = implode($split_value_char, $value_array);
        } else {
            for ($i = 0; $i < $value_length; $i++) {
                $value .= $characters[rand(0, strlen($characters) - 1)];
            }
        }

        $value = $start_row . $value . $end_row;

        return $value;
    }
}