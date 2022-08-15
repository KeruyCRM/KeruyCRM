<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_user_accessgroups
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE
        ];
    }

    public function render($field, $obj, $params = [])
    {
        if (!isset($obj['multiple_access_groups'])) {
            $obj['multiple_access_groups'] = '';
        }

        if (!isset($obj['id'])) {
            $obj['id'] = 0;
        }

        if (strlen($obj['multiple_access_groups'])) {
            $value = $obj['multiple_access_groups'];
        } elseif (($default_group_id = \Models\Main\Access_groups::get_default_group_id()) > 0 and strlen(
                $obj['field_' . $field['id']]
            ) == 0) {
            $value = $default_group_id;
        } else {
            $value = $obj['field_' . $field['id']];
        }

        if (\K::$fw->app_module_path == 'users/registration') {
            $choices = [];
            $choices[''] = \K::$fw->TEXT_SELECT_SOME_VALUES;
            /*$groups_query = db_fetch_all(
                'app_access_groups',
                (strlen(
                    \K::$fw->CFG_PUBLIC_REGISTRATION_USER_GROUP
                ) ? 'id in (' . \K::$fw->CFG_PUBLIC_REGISTRATION_USER_GROUP . ')' : ''),
                'sort_order, name'
            );*/

            $groups_query = \K::model()->db_fetch(
                'app_access_groups',
                (strlen(
                    \K::$fw->CFG_PUBLIC_REGISTRATION_USER_GROUP
                ) ? ['id in (' . \K::$fw->CFG_PUBLIC_REGISTRATION_USER_GROUP . ')'] : []),
                ['order' => 'sort_order, name'],
                'id,name'
            );

            //while ($v = db_fetch_array($groups_query)) {
            foreach ($groups_query as $v) {
                $v = $v->cast();

                $choices[$v['id']] = $v['name'];
            }
        } elseif (!$choices = self::get_choices_by_rules()) {
            $include_administrator = (\K::$fw->app_user['group_id'] > 0 ? false : true);
            $choices = \Models\Main\Access_groups::get_choices($include_administrator);
        }

        if ($obj['id'] == \K::$fw->app_user['id'] and $obj['id'] > 0) {
            return '<p class="form-control-static">' . \Models\Main\Access_groups::get_name_by_id(
                    \K::$fw->app_user['group_id']
                ) . '</p>' . \Helpers\Html::input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $value,
                    ['class' => 'field_' . $field['id']]
                );
        }

        if (\K::$fw->CFG_ENABLE_MULTIPLE_ACCESS_GROUPS or strlen($obj['multiple_access_groups'])) {
            if (\K::$fw->app_module_path == 'users/registration') {
                if (\K::$fw->CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS) {
                    unset($choices['']);
                    return \Helpers\Html::select_checkboxes_tag(
                            'fields[' . $field['id'] . ']',
                            $choices,
                            $value,
                            ['class' => 'required field_' . $field['id']]
                        ) . \Models\Main\Fields_types::custom_error_handler($field['id']);
                } else {
                    return \Helpers\Html::select_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        ['class' => 'form-control input-medium required field_' . $field['id']]
                    );
                }
            } else {
                return \Helpers\Html::select_tag(
                        'fields[' . $field['id'] . '][]',
                        $choices,
                        $value,
                        [
                            'class' => 'form-control input-large chosen-select required field_' . $field['id'],
                            'multiple' => 'multiple'
                        ]
                    ) . \Models\Main\Fields_types::custom_error_handler($field['id']);
            }
        } else {
            return \Helpers\Html::select_tag(
                'fields[' . $field['id'] . ']',
                $choices,
                $value,
                ['class' => 'form-control input-medium required field_' . $field['id']]
            );
        }
    }

    public function process($options)
    {
        //check allowed group
        if (\K::$fw->app_user['group_id'] > 0) {
            //get allowed groups
            if (!$choices = self::get_choices_by_rules()) {
                $include_administrator = (\K::$fw->app_user['group_id'] > 0 ? false : true);
                $choices = \Models\Main\Access_groups::get_choices($include_administrator);
            }

            if (is_array($options['value'])) {
                foreach ($options['value'] as $k => $id) {
                    if (!isset($choices[$id])) {
                        unset($options['value'][$k]);
                        unset(\K::$fw->POST['fields'][6][$k]);
                    }
                }

                if (!count($options['value'])) {
                    $options['value'] = \K::$fw->app_user['group_id'];
                }
            } else {
                $values = explode(',', $options['value']);
                foreach ($values as $k => $id) {
                    if (!isset($choices[$id])) {
                        unset($values[$k]);
                    }
                }

                if (!count($values)) {
                    $options['value'] = \K::$fw->app_user['group_id'];
                } else {
                    $options['value'] = implode(',', $values);
                }
            }
        }

        if (is_array($options['value'])) {
            if (\K::$fw->app_module_path == 'ext/processes/fields') {
                return implode(',', $options['value']);
            } else {
                return $options['value'][0];
            }
        } else {
            return $options['value'];
        }
    }

    public static function prepare_multiple_access_groups($entity_id, $item_id)
    {
        //handle process action
        if (\K::$fw->app_module_path == 'items/processes' and $entity_id == 1 and isset(\K::$fw->sql_data['field_6'])) {
            /*db_query(
                "update app_entity_1 set multiple_access_groups='" . (count(
                    explode(',', \K::$fw->sql_data['field_6'])
                ) > 1 ? \K::$fw->sql_data['field_6'] : '') . "' where id='" . $item_id . "'"
            );*/

            \K::model()->db_update('app_entity_1', [
                'multiple_access_groups' => (count(
                    explode(',', \K::$fw->sql_data['field_6'])
                ) > 1 ? \K::$fw->sql_data['field_6'] : '')
            ], [
                'id = ?',
                $item_id
            ]);
        } //handle default form post
        elseif ($entity_id == 1 and isset(\K::$fw->POST['fields'][6])) {
            if (is_array(\K::$fw->POST['fields'][6]) and count(\K::$fw->POST['fields'][6]) > 1) {
                /*db_query(
                    "update app_entity_1 set multiple_access_groups='" . db_input(
                        implode(',', \K::$fw->POST['fields'][6])
                    ) . "' where id='" . $item_id . "'"
                );*/

                \K::model()->db_update('app_entity_1', [
                    'multiple_access_groups' => implode(',', \K::$fw->POST['fields'][6])
                ], [
                    'id = ?',
                    $item_id
                ]);
            } else {
                /*db_query("update app_entity_1 set multiple_access_groups='' where id='" . $item_id . "'");*/

                \K::model()->db_update('app_entity_1', [
                    'multiple_access_groups' => ''
                ], [
                    'id = ?',
                    $item_id
                ]);
            }
        }
    }

    public function output($options)
    {
        if (strstr($options['value'], ',')) {
            $options['item']['multiple_access_groups'] = $options['value'];
        }

        if (isset($options['item']['multiple_access_groups']) and strlen($options['item']['multiple_access_groups'])) {
            $output = [];
            foreach (explode(',', $options['item']['multiple_access_groups']) as $id) {
                $output[] = \Models\Main\Access_groups::get_name_by_id($id);
            }

            if (isset($options['is_export'])) {
                return implode(', ', $output);
            } else {
                return implode('<br>', $output);
            }
        } else {
            return \Models\Main\Access_groups::get_name_by_id($options['value']);
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $sql_query_extra = [];
            $exp = explode(',', $filters['filters_values']);

            foreach ($exp as $id) {
                $id = (int)$id;

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

    public static function get_choices_by_rules()
    {
        if (\K::$fw->app_user['group_id'] == 0) {
            return false;
        }

        /*$rules_query = db_query(
            "select * from app_records_visibility_rules where entities_id='1' and find_in_set(" . \K::$fw->app_user['group_id'] . ",users_groups)"
        );*/

        $rules = \K::model()->db_fetch_one('app_records_visibility_rules', [
            'entities_id = 1 and find_in_set(:find_in_set,users_groups)',
            ':find_in_set' => [\K::$fw->app_user['group_id'] => \PDO::PARAM_INT]
        ], [], 'id');

        if ($rules) {
            /*$reports_query = db_query(
                "select * from app_reports where entities_id=1 and reports_type='records_visibility" . db_input(
                    $rules['id']
                ) . "'"
            );*/

            $reports_query = \K::model()->db_fetch_one('app_reports', [
                'entities_id = 1 and reports_type = ?',
                'records_visibility' . $rules['id']
            ], [], 'id');

            if ($reports_query) {
                $filters_query = \K::model()->db_query_exec(
                    "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.fields_id = 6 and rf.reports_id = ? and length(filters_values) > 0 order by rf.id",
                    $reports_query['id'],
                    'app_reports_filters,app_fields'
                );

                if (count($filters_query)) {
                    $include = [];
                    $exclude = [];

                    //while ($filters = db_fetch_array($filters_query)) {
                    foreach ($filters_query as $filters) {
                        if ($filters['filters_condition'] == 'include') {
                            $include = array_merge($include, explode(',', $filters['filters_values']));
                        } else {
                            $exclude = array_merge($exclude, explode(',', $filters['filters_values']));
                        }
                    }

                    $choices = [];
                    $choices[''] = \K::$fw->TEXT_SELECT_SOME_VALUES;

                    /*$groups_query = db_query(
                        "select id,name from app_access_groups where id>0 " . (count(
                            $include
                        ) ? " and id in (" . implode(',', $include) . ")" : "") . (count(
                            $exclude
                        ) ? " and id not in (" . implode(',', $exclude) . ")" : "") . " order by sort_order, name",
                        false
                    );*/

                    $groups_query = \K::model()->db_fetch('app_access_groups', [
                        'id > 0'
                        . (count($include) ? " and id in (" . implode(',', $include) . ")" : '')
                        . (count($exclude) ? " and id not in (" . implode(',', $exclude) . ")" : '')
                    ], ['order' => 'sort_order,name'], 'id,name');

                    //while ($groups = db_fetch_array($groups_query)) {
                    foreach ($groups_query as $groups) {
                        $groups = $groups->cast();

                        $choices[$groups['id']] = $groups['name'];
                    }

                    return $choices;
                }
            }
        }

        return false;
    }
}