<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_parent_value
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_PARENT_VALUE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $entities_id = \K::$fw->POST['entities_id'];

        $entities_info = \K::model()->db_find('app_entities', $entities_id);

        $choices = [];

        if ($entities_info['parent_id'] > 0) {
            $choices = ['' => ''];
            $reserved_fields_types = array_merge(
                \Models\Main\Fields_types::get_reserved_data_types(),
                \Models\Main\Fields_types::get_users_types()
            );

            $reserved_fields_types_list = \K::model()->quoteToString($reserved_fields_types);
            $typeNotIn = \K::model()->quoteToString(['fieldtype_action', 'fieldtype_parent_item_id']);

            $fields_query = \K::model()->db_query_exec(
                "select f.*, t.name as tab_name, if(f.type in (" . $reserved_fields_types_list . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in (" . $typeNotIn . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by tab_sort_order, t.name, f.sort_order, f.name",
                $entities_info['parent_id'],
                'app_fields,app_forms_tabs'
            );

            //while ($fields = db_fetch_array($fields_query)) {
            foreach ($fields_query as $fields) {
                $choices[$fields['tab_name']][$fields['id']] = (strlen(
                    $fields['name']
                ) ? $fields['name'] : \Models\Main\Fields_types::get_title($fields['type']));
            }
        }

        $cfg[] = [
            'title' => \K::$fw->TEXT_SELECT_FIELD,
            'name' => 'field_id',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large required']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return false;
    }

    public function output($options)
    {
        $html = '';
        $entities_id = $options['field']['entities_id'];
        $parent_item_id = $options['item']['parent_item_id'];

        $entities_info = \K::model()->db_find('app_entities', $entities_id);

        if ($parent_item_id > 0 and $entities_info['parent_id'] > 0) {
            //prepare query cache
            if (!isset(\K::$fw->parent_items_values_cache[$parent_item_id])) {
                $select_fields = [];
                /*$fields_query = db_query(
                    "select id, configuration from app_fields where entities_id='" . db_input(
                        $entities_id
                    ) . "' and type='fieldtype_parent_value'"
                );*/

                $fields_query = \K::model()->db_fetch('app_fields', [
                    'entities_id = ? and type = ?',
                    $entities_id,
                    'fieldtype_parent_value'
                ], [], 'configuration');//FIX

                //while ($fields = db_fetch_array($fields_query)) {
                foreach ($fields_query as $fields) {
                    $fields = $fields->cast();

                    $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                    if (strlen($cfg->get('field_id'))) {
                        $select_fields[] = $cfg->get('field_id');
                    }
                }

                if (count($select_fields)) {
                    //TODO Add cache?
                    $parent_item_info = \K::model()->db_query_exec_one(
                        "select e.* " . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                            $entities_info['parent_id'],
                            '',
                            false,
                            ['fields_in_listing' => implode(',', $select_fields)]
                        ) . " from app_entity_" . (int)$entities_info['parent_id'] . " e  where e.id = ?",
                        $parent_item_id
                    );

                    if ($parent_item_info) {
                        \K::$fw->parent_items_values_cache[$parent_item_id] = $parent_item_info;
                    }
                }
            }

            //output field value
            if (isset(\K::$fw->parent_items_values_cache[$parent_item_id])) {
                $item = \K::$fw->parent_items_values_cache[$parent_item_id];

                $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

                if (strlen($cfg->get('field_id'))) {
                    //$field_query = db_query("select * from app_fields where id='" . $cfg->get('field_id') . "'");

                    $field = \K::model()->db_fetch_one('app_fields', [
                        'id = ?',
                        $cfg->get('field_id')
                    ]);

                    if (!$field) {
                        return '';
                    }

                    //prepare field value
                    $value = \Models\Main\Items\Items::prepare_field_value_by_type($field, $item);

                    if (isset($options['output_db_value'])) {
                        $html = $value;
                    } else {
                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_listing' => true,
                            'redirect_to' => '',
                            'reports_id' => 0,
                            'path' => $entities_info['parent_id'],
                        ];

                        if (isset($options['is_export'])) {
                            $output_options['is_export'] = $options['is_export'];
                        }
                        if (isset($options['is_print'])) {
                            $output_options['is_print'] = $options['is_print'];
                        }

                        $html = \Models\Main\Fields_types::output($output_options);
                    }
                }
            }
        }

        return $html;
    }
}