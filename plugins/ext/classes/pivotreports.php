<?php

class pivotreports
{
    static function apply_allow_edit($pivotreports)
    {
        global $app_user;

        if ($pivotreports['allow_edit'] == 1 and $app_user['group_id'] > 0) {
            $users_settings_query = db_query(
                "select * from app_ext_pivotreports_settings where reports_id='" . $pivotreports['id'] . "' and users_id='" . $app_user['id'] . "'"
            );
            if (!$users_settings = db_fetch_array($users_settings_query)) {
                $sql_data = [
                    'reports_id' => $pivotreports['id'],
                    'users_id' => $app_user['id'],
                    'reports_settings' => $pivotreports['reports_settings'],
                    'view_mode' => 1,
                ];

                db_perform('app_ext_pivotreports_settings', $sql_data);
                $settings_id = db_insert_id();

                $users_settings_query = db_query(
                    "select * from app_ext_pivotreports_settings where id='" . $settings_id . "'"
                );
                $users_settings = db_fetch_array($users_settings_query);
            }

            $pivotreports['view_mode'] = $users_settings['view_mode'];
            $pivotreports['reports_settings'] = $users_settings['reports_settings'];
        }

        return $pivotreports;
    }

    static function array_to_csv($output)
    {
        return implode(',', $output) . "\n";
    }

    static function css_prepare($output)
    {
        return '"' . str_replace('"', '""', trim(strip_tags($output))) . '"';
    }

    static function get_fields_by_entity($reports_id, $entities_id)
    {
        $reports_fields = [];
        $reports_fields_names = [];
        $reports_fields_dates_format = [];
        $pivotreports_fields_query = db_query(
            "select * from app_ext_pivotreports_fields where pivotreports_id='" . db_input(
                $reports_id
            ) . "' and entities_id='" . db_input($entities_id) . "'"
        );
        while ($pivotreports_fields = db_fetch_array($pivotreports_fields_query)) {
            $reports_fields[] = $pivotreports_fields['fields_id'];

            if (strlen($pivotreports_fields['fields_name']) > 0) {
                $reports_fields_names[$pivotreports_fields['fields_id']] = $pivotreports_fields['fields_name'];
            }

            if (strlen($pivotreports_fields['cfg_date_format']) > 0) {
                $reports_fields_dates_format[$pivotreports_fields['fields_id']] = $pivotreports_fields['cfg_date_format'];
            }
        }

        return [
            'reports_fields' => $reports_fields,
            'reports_fields_names' => $reports_fields_names,
            'reports_fields_dates_format' => $reports_fields_dates_format,
        ];
    }

    static function prepare_csv_output_for_parent_entities(
        $output_array,
        $parent_entities_listing_fields,
        $parrent_entities,
        $parent_item_id,
        $fields_dates_format
    ) {
        foreach ($parrent_entities as $entities_id) {
            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select($entities_id, '');

            $items_sql_query = "select * {$listing_sql_query_select} from app_entity_" . $entities_id . " e where id ='" . $parent_item_id . "'";
            $items_query = db_query($items_sql_query);
            if ($item = db_fetch_array($items_query)) {
                if (isset($parent_entities_listing_fields[$entities_id])) {
                    foreach ($parent_entities_listing_fields[$entities_id] as $field) {
                        $value = items::prepare_field_value_by_type($field, $item);

                        if (in_array(
                                $field['type'],
                                ['fieldtype_date_added', 'fieldtype_input_date', 'fieldtype_input_datetime']
                            ) and isset($fields_dates_format[$field['id']])) {
                            $output_array[] = pivotreports::css_prepare(
                                i18n_date($fields_dates_format[$field['id']], $value)
                            );
                        } else {
                            $output_options = [
                                'class' => $field['type'],
                                'value' => $value,
                                'field' => $field,
                                'item' => $item,
                                'is_export' => true,
                                'reports_id' => 0,
                                'path' => '',
                                'path_info' => ''
                            ];

                            $output_array[] = pivotreports::css_prepare(fields_types::output($output_options));
                        }
                    }
                }

                $parent_item_id = $item['parent_item_id'];
            }
        }

        return $output_array;
    }


    static function prepare_reports_settings_val($val)
    {
        $values = [];
        foreach ($val as $v) {
            $values[] = '"' . addslashes($v) . '"';
        }

        return implode(',', $values);
    }

    static function render_reports_settings($settings)
    {
        if (strlen($settings) > 0) {
            $settings_list = [];

            $settings = json_decode(stripslashes($settings), true);

            if (count($settings['cols']) > 0) {
                $settings_list[] = 'cols:[' . self::prepare_reports_settings_val($settings['cols']) . ']';
            }

            if (count($settings['rows']) > 0) {
                $settings_list[] = 'rows:[' . self::prepare_reports_settings_val($settings['rows']) . ']';
            }

            if (count($settings['vals']) > 0) {
                $settings_list[] = 'vals:[' . self::prepare_reports_settings_val($settings['vals']) . ']';
            }

            if (count($settings['exclusions']) > 0) {
                $exclusions = [];
                foreach ($settings['exclusions'] as $name => $val) {
                    $exclusions[] = '"' . addslashes($name) . '":[' . self::prepare_reports_settings_val($val) . ']';
                }

                $settings_list[] = 'exclusions:{' . implode(',', $exclusions) . '}';
            }

            $settings_list[] = 'aggregatorName: "' . $settings['aggregatorName'] . '"';

            $settings_list[] = 'rendererName: "' . $settings['rendererName'] . '"';

            //print_r($settings);

            if (count($settings_list) > 0) {
                return implode(',', $settings_list) . ',';
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

}