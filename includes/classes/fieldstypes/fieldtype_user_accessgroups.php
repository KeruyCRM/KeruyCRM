<?php

class fieldtype_user_accessgroups
{

    public $options;

    function __construct()
    {
        $this->options = [
            'name' => TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE,
            'title' => TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE
        ];
    }

    function render($field, $obj, $params = [])
    {
        global $app_user, $app_module_path;

        if (!isset($obj['multiple_access_groups'])) {
            $obj['multiple_access_groups'] = '';
        }
        if (!isset($obj['id'])) {
            $obj['id'] = 0;
        }

        if (strlen($obj['multiple_access_groups'])) {
            $value = $obj['multiple_access_groups'];
        } elseif (($default_group_id = access_groups::get_default_group_id()) > 0 and strlen(
                $obj['field_' . $field['id']]
            ) == 0) {
            $value = $default_group_id;
        } else {
            $value = $obj['field_' . $field['id']];
        }

        if ($app_module_path == 'users/registration') {
            $choices = [];
            $choices[''] = TEXT_SELECT_SOME_VALUES;
            $groups_query = db_fetch_all(
                'app_access_groups',
                (strlen(
                    CFG_PUBLIC_REGISTRATION_USER_GROUP
                ) ? 'id in (' . CFG_PUBLIC_REGISTRATION_USER_GROUP . ')' : ''),
                'sort_order, name'
            );
            while ($v = db_fetch_array($groups_query)) {
                $choices[$v['id']] = $v['name'];
            }
        } else {
            if (!$choices = self::get_choices_by_rules()) {
                $include_administrator = ($app_user['group_id'] > 0 ? false : true);
                $choices = access_groups::get_choices($include_administrator);
            }
        }

        if ($obj['id'] == $app_user['id'] and $obj['id'] > 0) {
            return '<p class="form-control-static">' . access_groups::get_name_by_id(
                    $app_user['group_id']
                ) . '</p>' . input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value,
                    ['class' => 'field_' . $field['id']]
                );
        }

        if (CFG_ENABLE_MULTIPLE_ACCESS_GROUPS or strlen($obj['multiple_access_groups'])) {
            if ($app_module_path == 'users/registration') {
                if (CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS) {
                    unset($choices['']);
                    return select_checkboxes_tag(
                            'fields[' . $field['id'] . ']',
                            $choices,
                            $value,
                            ['class' => 'required field_' . $field['id']]
                        ) . fields_types::custom_error_handler($field['id']);
                } else {
                    return select_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        ['class' => 'form-control input-medium required field_' . $field['id']]
                    );
                }
            } else {
                return select_tag(
                        'fields[' . $field['id'] . '][]',
                        $choices,
                        $value,
                        [
                            'class' => 'form-control input-large chosen-select required field_' . $field['id'],
                            'multiple' => 'multiple'
                        ]
                    ) . fields_types::custom_error_handler($field['id']);
            }
        } else {
            return select_tag(
                'fields[' . $field['id'] . ']',
                $choices,
                $value,
                ['class' => 'form-control input-medium required field_' . $field['id']]
            );
        }
    }

    function process($options)
    {
        global $app_module_path, $app_user;

        //check allowed group
        if ($app_user['group_id'] > 0) {
            //get allowed groups
            if (!$choices = self::get_choices_by_rules()) {
                $include_administrator = ($app_user['group_id'] > 0 ? false : true);
                $choices = access_groups::get_choices($include_administrator);
            }

            if (is_array($options['value'])) {
                foreach ($options['value'] as $k => $id) {
                    if (!isset($choices[$id])) {
                        unset($options['value'][$k]);
                        unset($_POST['fields'][6][$k]);
                    }
                }

                if (!count($options['value'])) {
                    $options['value'] = $app_user['group_id'];
                }
            } else {
                $values = explode(',', $options['value']);
                foreach ($values as $k => $id) {
                    if (!isset($choices[$id])) {
                        unset($values[$k]);
                    }
                }

                if (!count($values)) {
                    $options['value'] = $app_user['group_id'];
                } else {
                    $options['value'] = implode(',', $values);
                }
            }
        }

        //print_rr($options['value']);        
        //exit();

        if (is_array($options['value'])) {
            if ($app_module_path == 'ext/processes/fields') {
                return implode(',', $options['value']);
            } else {
                return $options['value'][0];
            }
        } else {
            return $options['value'];
        }
    }

    static function prepare_multiple_access_groups($entity_id, $item_id)
    {
        global $sql_data, $app_module_path;

        //handle process aciton
        if ($app_module_path == 'items/processes' and $entity_id == 1 and isset($sql_data['field_6'])) {
            db_query(
                "update app_entity_1 set multiple_access_groups='" . (count(
                    explode(',', $sql_data['field_6'])
                ) > 1 ? $sql_data['field_6'] : '') . "' where id='" . $item_id . "'"
            );
        } //handle default form post
        elseif ($entity_id == 1 and isset($_POST['fields'][6])) {
            if (is_array($_POST['fields'][6]) and count($_POST['fields'][6]) > 1) {
                db_query(
                    "update app_entity_1 set multiple_access_groups='" . db_input(
                        implode(',', $_POST['fields'][6])
                    ) . "' where id='" . $item_id . "'"
                );
            } else {
                db_query("update app_entity_1 set multiple_access_groups='' where id='" . $item_id . "'");
            }
        }
    }

    function output($options)
    {
        if (strstr($options['value'], ',')) {
            $options['item']['multiple_access_groups'] = $options['value'];
        }

        if (isset($options['item']['multiple_access_groups']) and strlen($options['item']['multiple_access_groups'])) {
            $output = [];
            foreach (explode(',', $options['item']['multiple_access_groups']) as $id) {
                $output[] = access_groups::get_name_by_id($id);
            }

            if (isset($options['is_export'])) {
                return implode(', ', $output);
            } else {
                return implode('<br>', $output);
            }
        } else {
            return access_groups::get_name_by_id($options['value']);
        }
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = [];

        if (strlen($filters['filters_values']) > 0) {
            $sql_query_extra = [];
            foreach (explode(',', $filters['filters_values']) as $id) {
                $sql_query_extra[] = "find_in_set({$id},multiple_access_groups)";
            }

            if ($filters['filters_condition'] == 'include') {
                $sql_query[] = "(e.field_6 in (" . $filters['filters_values'] . ") or " . implode(
                        ' or ',
                        $sql_query_extra
                    ) . ")";
            } else {
                $sql_query[] = "(e.field_6 not in (" . $filters['filters_values'] . ")  and !" . implode(
                        ' and !',
                        $sql_query_extra
                    ) . " )";
            }
        }

        return $sql_query;
    }

    static function get_choices_by_rules()
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return false;
        }

        $rules_query = db_query(
            "select * from app_records_visibility_rules where entities_id='1' and find_in_set(" . $app_user['group_id'] . ",users_groups)"
        );
        if ($rules = db_fetch_array($rules_query)) {
            $reports_query = db_query(
                "select * from app_reports where entities_id=1 and reports_type='records_visibility" . db_input(
                    $rules['id']
                ) . "'"
            );
            if ($reports_query = db_fetch_array($reports_query)) {
                $filters_query = db_query(
                    "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.fields_id=6 and rf.reports_id='" . db_input(
                        $reports_query['id']
                    ) . "' and length(filters_values)>0 order by rf.id"
                );
                if (db_num_rows($filters_query)) {
                    $include = [];
                    $exclude = [];
                    while ($filters = db_fetch_array($filters_query)) {
                        if ($filters['filters_condition'] == 'include') {
                            $include = array_merge($include, explode(',', $filters['filters_values']));
                        } else {
                            $exclude = array_merge($exclude, explode(',', $filters['filters_values']));
                        }
                    }

                    $choices = [];
                    $choices[''] = TEXT_SELECT_SOME_VALUES;
                    $groups_query = db_query(
                        "select id,name from app_access_groups where id>0 " . (count(
                            $include
                        ) ? " and id in (" . implode(',', $include) . ")" : "") . (count(
                            $exclude
                        ) ? " and id not in (" . implode(',', $exclude) . ")" : "") . " order by sort_order, name",
                        false
                    );
                    while ($groups = db_fetch_array($groups_query)) {
                        $choices[$groups['id']] = $groups['name'];
                    }

                    //print_rr($choices);

                    return $choices;
                }
            }
        }

        return false;
    }

}
