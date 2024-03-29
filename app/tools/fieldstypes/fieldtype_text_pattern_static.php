<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_text_pattern_static
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TEXT_PATTERN_STATIC];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PATTERN . \Models\Main\Fields::get_available_fields_helper(
                    \K::$fw->POST['entities_id'],
                    'fields_configuration_pattern'
                ),
            'name' => 'pattern',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_TRIM_VALUE,
            'name' => 'trim_value',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_TRIM_VALUE_INFO,
            'tooltip' => \K::$fw->TEXT_TRIM_VALUE_EXAMPLE,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
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
        return $options['value'];
    }

    public static function set($entities_id, $items_id, $item_info = false)
    {
        /*$fields_query = db_query(
            "select * from app_fields where entities_id='" . db_input(
                $entities_id
            ) . "' and type='fieldtype_text_pattern_static'"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'entities_id = ? and type = ?',
            $entities_id,
            'fieldtype_text_pattern_static'
        ]);
        //while ($fields = db_fetch_array($fields_query)) {
        $forceCommit = \K::model()->forceCommit();

        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            if (!$item_info) {
                //TODO Add cache
                $item_info = \K::model()->db_query_exec_one(
                    "select e.* " . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                        $entities_id,
                        ''
                    ) . fieldtype_related_records::prepare_query_select(
                        $entities_id,
                        ''
                    ) . " from app_entity_" . (int)$entities_id . " e where e.id = ?", [$items_id]
                );
                //$item_info = db_fetch_array($item_info_query);
            }

            if ($item_info) {
                $options = [
                    'field' => $fields,
                    'item' => $item_info,
                    'path' => $entities_id . '-' . $items_id,
                ];

                $fieldtype_text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
                $value = $fieldtype_text_pattern->output($options);

                if (strlen($cfg->get('trim_value'))) {
                    $trim_value = explode(',', $cfg->get('trim_value'));

                    if (count($trim_value) == 1) {
                        $value = substr($value, (int)$trim_value[0]);
                    } else {
                        $value = substr($value, (int)$trim_value[0], (int)$trim_value[1]);
                    }
                }

                /*db_query(
                    "update app_entity_" . $entities_id . " set field_" . $fields['id'] . " = '" . db_input(
                        $value
                    ) . "' where id='" . $items_id . "'"
                );*/

                \K::model()->db_update(
                    'app_entity_' . (int)$entities_id,
                    ['field_' . (int)$fields['id'] => $value],
                    ['id = ?', $items_id]
                );
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }
}