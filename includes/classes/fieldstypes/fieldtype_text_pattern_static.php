<?php

class fieldtype_text_pattern_static
{

    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_TEXT_PATTERN_STATIC];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => TEXT_PATTERN . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_pattern'
                ),
            'name' => 'pattern',
            'type' => 'textarea',
            'tooltip' => TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control']
        ];

        $cfg[] = [
            'title' => TEXT_TRIM_VALUE,
            'name' => 'trim_value',
            'type' => 'input',
            'tooltip_icon' => TEXT_TRIM_VALUE_INFO,
            'tooltip' => TEXT_TRIM_VALUE_EXAMPLE,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];


        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        return '';
    }

    function process($options)
    {
        return '';
    }

    function output($options)
    {
        return $options['value'];
    }

    static function set($entities_id, $items_id, $item_info = false)
    {
        global $sql_query_having, $app_changed_fields, $app_choices_cache;

        $fields_query = db_query(
            "select * from app_fields where entities_id='" . db_input(
                $entities_id
            ) . "' and type='fieldtype_text_pattern_static'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($fields['configuration']);

            if (!$item_info) {
                $item_info_query = db_query(
                    "select e.* " . fieldtype_formula::prepare_query_select(
                        $entities_id,
                        ''
                    ) . fieldtype_related_records::prepare_query_select(
                        $entities_id,
                        ''
                    ) . " from app_entity_" . $entities_id . " e where e.id='" . db_input($items_id) . "'"
                );
                $item_info = db_fetch_array($item_info_query);
            }

            if ($item_info) {
                $options = [
                    'field' => $fields,
                    'item' => $item_info,
                    'path' => $entities_id . '-' . $items_id,
                ];

                $fieldtype_text_pattern = new fieldtype_text_pattern;
                $value = $fieldtype_text_pattern->output($options);

                if (strlen($cfg->get('trim_value'))) {
                    $trim_value = explode(',', $cfg->get('trim_value'));

                    if (count($trim_value) == 1) {
                        $value = substr($value, (int)$trim_value[0]);
                    } else {
                        $value = substr($value, (int)$trim_value[0], (int)$trim_value[1]);
                    }
                }

                db_query(
                    "update app_entity_" . $entities_id . " set field_" . $fields['id'] . " = '" . db_input(
                        $value
                    ) . "' where id='" . $items_id . "'"
                );
            }
        }
    }

}
