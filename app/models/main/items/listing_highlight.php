<?php

namespace Models\Main\Items;

class Listing_highlight
{
    public $entities_id, $has_rules, $rules, $css;

    public function __construct($entities_id)
    {
        $this->entities_id = $entities_id;
        $this->rules = [];

        $fields_query = db_query(
            "select r.*, f.name, f.type, f.configuration from app_listing_highlight_rules r, app_fields f where r.is_active=1 and f.id = r.fields_id and r.entities_id='" . $this->entities_id . "' order by r.sort_order, r.id"
        );
        while ($v = db_fetch_array($fields_query)) {
            $this->rules[] = $v;

            $color = trim($v['bg_color']);

            if (strlen($color)) {
                $rgb = convert_html_color_to_RGB($color);
                $class_name = '.listing_highlight_' . $this->entities_id . '_' . $v['id'];

                if (($rgb[0] + $rgb[1] + $rgb[2]) < 480) {
                    $this->css .= '
                        ' . $class_name . ', 
                        ' . $class_name . '>td, .table-striped>tbody>' . $class_name . ':nth-child(odd)>td,
                        ul.listing-grid>li' . $class_name . ',
                        ul.listing-mobile>li' . $class_name . '        
                        {
                          background: ' . $color . ';
                          color: white;
                        }

                        ' . $class_name . ' .item_heading_link{
                          color: white;      
                        }
                        .table-hover > tbody > ' . $class_name . ':hover > td{
                          background-color: rgba(' . ($rgb[0] + 20) . ',' . ($rgb[1] + 20) . ',' . ($rgb[2] + 20) . ');
                        }

                        ' . $class_name . ' .popover{
                            color: #000;
                        }      
                        ';
                } else {
                    $this->css .= '
                        ' . $class_name . ', ' . $class_name . '>td, .table-striped>tbody>' . $class_name . ':nth-child(odd)>td,
                        ul.listing-grid>li' . $class_name . ',
                        ul.listing-mobile>li' . $class_name . '
                        {
                          background: ' . $color . ';      
                        } 
                        .table-hover > tbody > ' . $class_name . ':hover > td{                        
                          background-color: rgba(' . ($rgb[0] + 20) . ',' . ($rgb[1] + 20) . ',' . ($rgb[2] + 20) . ');
                        }                       
                        ';
                }
            }
        }
    }

    public function has_rules()
    {
        return (count($this->rules) ? true : false);
    }

    public function render_css()
    {
        if (!$this->has_rules()) {
            return '';
        }

        return '
           <style>
               ' . $this->css .
            '</style>';
    }

    public function apply($item)
    {
        if (!$this->has_rules()) {
            return '';
        }

        $css = '';
        foreach ($this->rules as $rule) {
            if (!isset($item['field_' . $rule['fields_id']])) {
                continue;
            }

            if ($this->match_rule($rule, $item['field_' . $rule['fields_id']])) {
                return ' listing_highlight_' . $this->entities_id . '_' . $rule['id'];
            }
        }

        return '';
    }

    public function match_rule($rule, $item_field_value)
    {
        switch (listing_highlight::get_field_type_key($rule['type'])) {
            case 'boolean':
                return ($rule['fields_values'] == $item_field_value ? true : false);
                break;
            case 'choices':
            case 'entities':
                $fields_values = (strlen($rule['fields_values']) ? explode(',', $rule['fields_values']) : []);

                if (count($fields_values) and strlen($item_field_value)) {
                    foreach (explode(',', $item_field_value) as $item_value) {
                        if (in_array($item_value, $fields_values)) {
                            return true;
                        }
                    }
                }

                break;
            case 'dates':

                if (!strlen($rule['fields_values']) or (int)$item_field_value == 0) {
                    return false;
                }

                $operator = '==';
                if (preg_match("/!=|>=|<=|>|</", $rule['fields_values'], $matches)) {
                    $operator = $matches[0];
                }

                $days = str_replace($operator, '', $rule['fields_values']);

                if (!in_array($days[0], ['+', '-'])) {
                    $days = '+' . $days;
                }

                if ($rule['type'] == 'fieldtype_input_datetime') {
                    $eval_str = '$check = ((' . $item_field_value . $operator . (strtotime(
                            $days . ' day'
                        )) . ') ? true:false);';
                } else {
                    $eval_str = '$check = ((' . $item_field_value . $operator . get_date_timestamp(
                            date('Y-m-d', strtotime($days . ' day'))
                        ) . ') ? true:false);';
                }

                //echo $eval_str;

                try {
                    eval($eval_str);
                } catch (ParseError $e) {
                    echo alert_error(
                        \K::$fw->TEXT_ERROR . ' listing highlight #' . $rule['id'] . ' <br>' . $eval_str . '<br>' . $e->getMessage(
                        )
                    );
                }

                if ($check) {
                    return true;
                }

                break;
            case 'numeric':

                if (!strlen($item_field_value)) {
                    return false;
                }

                $values = preg_split("/(&|\|)/", $rule['fields_values'], null, PREG_SPLIT_DELIM_CAPTURE);

                $default_condition = false;

                if (strlen($values[0]) > 0) {
                    $values[1] = (isset($values[1]) ? $values[1] : '');

                    if ($values[1] == '|') {
                        $values = array_merge(['', '|'], $values);
                        $default_condition = '|';
                    } else {
                        $values = array_merge(['', '&'], $values);
                        $default_condition = '&';
                    }
                }

                //print_rr($values);

                for ($i = 1; $i < count($values); $i += 2) {
                    if (!isset($values[$i + 1])) {
                        continue;
                    }

                    if (preg_match("/!=|>=|<=|>|</", $values[$i + 1], $matches)) {
                        $operator = $matches[0];
                        $value = (float)str_replace($matches[0], '', $values[$i + 1]);
                    } elseif (!is_numeric($values[$i + 1])) {
                        $operator = '==';
                        $value = "'" . substr($values[$i + 1], 0, 100) . "'";
                    } else {
                        $operator = '==';
                        $value = (float)$values[$i + 1];
                    }

                    if (!is_numeric($item_field_value)) {
                        $item_field_value = "'{$item_field_value}'";
                    }

                    $eval_str = '$check = ((' . $item_field_value . $operator . $value . ') ? true:false);';

                    switch ($values[$i]) {
                        case '|':

                            try {
                                eval($eval_str);
                            } catch (ParseError $e) {
                                echo alert_error(
                                    \K::$fw->TEXT_ERROR . ' listing highlight #' . $rule['id'] . ' <br>' . $eval_str . '<br>' . $e->getMessage(
                                    )
                                );
                            }

                            if ($check) {
                                return true;
                            }

                            break;
                        case '&':

                            try {
                                eval($eval_str);
                            } catch (ParseError $e) {
                                echo alert_error(
                                    \K::$fw->TEXT_ERROR . ' listing highlight #' . $rule['id'] . '<br>' . $eval_str . '<br>' . $e->getMessage(
                                    )
                                );
                            }

                            if (!$check) {
                                return false;
                            }

                            break;
                    }
                }

                switch ($default_condition) {
                    case '&':
                        return true;
                        break;
                    case '|':
                    default:
                        return false;
                        break;
                }

                break;
        }

        return false;
    }

    public static function get_allowed_types()
    {
        $allowed_types = [];

        $allowed_types['choices'] = [
            'fieldtype_autostatus',
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_dropdown_multilevel',
            'fieldtype_grouped_users',
            'fieldtype_grouped_users',
            'fieldtype_tags',
            'fieldtype_stages',
        ];

        $allowed_types['boolean'] = [
            'fieldtype_boolean_checkbox',
            'fieldtype_boolean',
        ];

        $allowed_types['entities'] = [
            'fieldtype_entity_multilevel',
            'fieldtype_entity_ajax',
            'fieldtype_entity',
        ];

        $allowed_types['dates'] = [
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_dynamic_date',
            'fieldtype_jalali_calendar',
        ];

        $allowed_types['numeric'] = [
            'fieldtype_input_numeric',
            'fieldtype_input_numeric_comments',
            'fieldtype_formula',
            'fieldtype_js_formula',
            'fieldtype_mysql_query',
        ];

        return $allowed_types;
    }

    public static function get_field_type_key($field_type)
    {
        foreach (self::get_allowed_types() as $key => $types) {
            foreach ($types as $type) {
                if ($type == $field_type) {
                    return $key;
                }
            }
        }
    }

    public static function get_fields_allowed_types()
    {
        $fields_allowed_types = [];
        foreach (self::get_allowed_types() as $types) {
            foreach ($types as $type) {
                $fields_allowed_types[] = $type;
            }
        }

        return $fields_allowed_types;
    }

    public static function get_fields_choices($entity_id)
    {
        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name, if(f.type in ('fieldtype_id','fieldtype_date_added','fieldtype_date_updated','fieldtype_created_by'),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type in ('" . implode(
                "','",
                self::get_fields_allowed_types()
            ) . "')  and f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by tab_sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = fields_types::get_option($v['type'], 'name', $v['name']);
        }

        return $choices;
    }

    public static function get_field_value_by_type($field, $value)
    {
        $html = '';

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        switch (self::get_field_type_key($field['type'])) {
            case 'boolean':
                $html = ($value == 'true' ? \K::$fw->TEXT_BOOLEAN_TRUE : \K::$fw->TEXT_BOOLEAN_FALSE);
                break;
            case 'choices':
                if ($cfg->get('use_global_list') > 0) {
                    $html = global_lists::render_value($value);
                } else {
                    $html = fields_choices::render_value($value);
                }
                break;
            case 'entities':
                if (strlen($value)) {
                    $items_info_sql = "select e.* from app_entity_" . $cfg->get(
                            'entity_id'
                        ) . " e where e.id in (" . db_input_in($value) . ")";
                    $items_query = db_query($items_info_sql);
                    while ($item = db_fetch_array($items_query)) {
                        $html .= items::get_heading_field($cfg->get('entity_id'), $item['id']) . '<br>';
                    }
                }
                break;
            case 'dates':
            case 'numeric':
                $html = $value;
                break;
        }

        return $html;
    }
}