<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_qrcode
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_QRCODE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_QRCODE_PATTERN . \Models\Main\Fields::get_available_fields_helper(
                    \K::$fw->POST['entities_id'],
                    'fields_configuration_pattern'
                ),
            'name' => 'pattern',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CODE_ERROR_CORRECTION,
            'name' => 'ecc',
            'type' => 'dropdown',
            'choices' => [
                'l' => \K::$fw->TEXT_CODE_ERROR_CORRECTION_L,
                'm' => \K::$fw->TEXT_CODE_ERROR_CORRECTION_M,
                'q' => \K::$fw->TEXT_CODE_ERROR_CORRECTION_Q,
                'h' => \K::$fw->TEXT_CODE_ERROR_CORRECTION_H
            ],
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PIXEL_SIZE,
            'name' => 'pixel_size',
            'type' => 'dropdown',
            'choices' => ['2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6],
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_ON_INFO_PAGE,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '';
    }

    public function process($options)
    {
        return '';
    }

    public function output($options)
    {
        $html = '';

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $entities_id = $options['field']['entities_id'];

        $item = $options['item'];

        $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
            $entities_id,
            \K::$fw->app_user['group_id']
        );

        if (isset($options['custom_pattern'])) {
            $pattern = $options['custom_pattern'];
        } else {
            $pattern = $cfg->get('pattern');
        }

        if (strlen($pattern) > 0) {
            if (preg_match_all('/\[(\w+)\]/', $pattern, $matches)) {
                //use to check if formulas fields using in text pattern
                $formulas_fields = false;

                foreach ($matches[1] as $matches_key => $fields_id) {
                    /*$field_query = db_query(
                        "select f.* from app_fields f where f.type not in ('fieldtype_action') and (f.id ='" . db_input(
                            $fields_id
                        ) . "' or type='fieldtype_" . db_input($fields_id) . "') and  f.entities_id='" . db_input(
                            $entities_id
                        ) . "'"
                    );*/

                    $field = \K::model()->db_fetch_one('app_fields', [
                        'type not in (?) and (id = ? or type = ?) and entities_id = ?',
                        'fieldtype_action',
                        $fields_id,
                        'fieldtype_' . $fields_id,
                        $entities_id
                    ]);

                    if ($field) {
                        //check field access
                        if (isset($fields_access_schema[$field['id']])) {
                            if ($fields_access_schema[$field['id']] == 'hide') {
                                continue;
                            }
                        }

                        switch ($field['type']) {
                            case 'fieldtype_parent_item_id':
                                $entities_info = \K::model()->db_find('app_entities', $entities_id);

                                if ($entities_info['parent_id'] > 0 and $item['parent_item_id'] > 0) {
                                    $value = \Models\Main\Items\Items::get_heading_field(
                                        $entities_info['parent_id'],
                                        $item['parent_item_id']
                                    );
                                } else {
                                    $value = '';
                                }
                                break;
                            case 'fieldtype_created_by':
                                $value = $item['created_by'];
                                break;
                            case 'fieldtype_date_added':
                                $value = $item['date_added'];
                                break;
                            case 'fieldtype_action':
                            case 'fieldtype_id':
                                $value = $item['id'];
                                break;
                            case 'fieldtype_formula':
                                //check if formula value exist in item and if not then do extra query to calculate it
                                if (strlen($item['field_' . $field['id']]) == 0) {
                                    //prepare formulas query
                                    if (!$formulas_fields) {
                                        //TODO Add cache
                                        $formulas_fields = \K::model()->db_query_exec_one(
                                            "select e.* " . fieldtype_formula::prepare_query_select(
                                                $entities_id,
                                                ''
                                            ) . " from app_entity_" . (int)$entities_id . " e where id = ?",
                                            $item['id']
                                        );
                                        //$formulas_fields = db_fetch_array($formulas_fields_query);
                                    }

                                    $value = $item['field_' . $field['id']] = $formulas_fields['field_' . $field['id']];
                                } else {
                                    $value = $item['field_' . $field['id']];
                                }
                                break;
                            default:
                                $value = $item['field_' . $field['id']];
                                break;
                        }

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'path' => $options['path']
                        ];

                        if (in_array($field['type'], ['fieldtype_textarea_wysiwyg'])) {
                            $output = trim(\Models\Main\Fields_types::output($output_options));
                        } elseif ($field['type'] == 'fieldtype_parent_item_id') {
                            $output = $value;
                        } else {
                            $output = trim(strip_tags(\Models\Main\Fields_types::output($output_options)));
                        }

                        $pattern = str_replace($matches[0][$matches_key], $output, $pattern);
                    }
                }

                //check if fields was replaced
                if (preg_replace('/\[(\d+)\]/', '', $cfg->get('pattern')) != $pattern) {
                    $html = $pattern;
                }
            }
        }

        if (isset($options['is_export'])) {
            return '<img src="data:image/png;base64,' . base64_encode(
                    \QRcode::png($html, false, $cfg->get('ecc'), $cfg->get('pixel_size'))
                ) . '">';
        }

        return $html;
    }
}