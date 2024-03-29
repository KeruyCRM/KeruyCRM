<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_grouped_users
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_GROUPEDUSERS_TITLE, 'has_choices' => true];
    }

    public function get_configuration($params = [])
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'tooltip' => \K::$fw->TEXT_DISPLAY_USERS_AS_TOOLTIP,
            'type' => 'dropdown',
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'checkboxes' => \K::$fw->TEXT_DISPLAY_USERS_AS_CHECKBOXES,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ],
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT_TEXT_INFO,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS,
            'name' => 'disable_notification',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO
        ];

        //cfg global list if exist
        if (count($choices = \Models\Main\Global_lists::get_lists_choices()) > 0) {
            $cfg[\K::$fw->TEXT_SETTINGS][] = [
                'title' => \K::$fw->TEXT_USE_GLOBAL_LIST,
                'name' => 'use_global_list',
                'type' => 'dropdown',
                'choices' => $choices,
                'tooltip' => \K::$fw->TEXT_USE_GLOBAL_LIST_TOOLTIP,
                'params' => ['class' => 'form-control input-medium']
            ];
        }

        $choices = [];
        $choices['listing'] = \K::$fw->TEXT_IN_LISTING;
        $choices['info_page'] = \K::$fw->TEXT_IN_ITEM_PAGE;
        $cfg[\K::$fw->TEXT_USERS][] = [
            'title' => \K::$fw->TEXT_SHOW_USERS,
            'name' => 'show_users',
            'type' => 'dropdown',
            'choices' => $choices,
            'params' => ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple'],
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GROUPEDUSERS_SHOW_USERS_TIP
        ];

        $cfg[\K::$fw->TEXT_USERS][] = [
            'title' => \K::$fw->TEXT_SHOW_USERS_ACCESS_GROUP,
            'name' => 'show_users_access_group',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $display_as = (strlen($cfg->get('display_as')) > 0 ? $cfg->get('display_as') : 'dropdown');

        if ($cfg->get('use_global_list') > 0) {
            $choices = \Models\Main\Global_lists::get_choices(
                $cfg->get('use_global_list'),
                (($display_as == 'dropdown' and ($field['is_required'] == 0 or strlen(
                            $cfg->get('default_text')
                        )) > 0) ? true : false),
                $cfg->get('default_text'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Global_lists::get_choices_default_id($cfg->get('use_global_list'));
        } else {
            $choices = \Models\Main\Fields_choices::get_choices(
                $field['id'],
                (($display_as == 'dropdown' and ($field['is_required'] == 0 or strlen(
                            $cfg->get('default_text')
                        )) > 0) ? true : false),
                $cfg->get('default_text'),
                $cfg->get('display_choices_values'),
                $obj['field_' . $field['id']],
                true
            );
            $default_id = \Models\Main\Fields_choices::get_default_id($field['id']);
        }

        $value = $obj['field_' . $field['id']];
        $value = ($value > 0 ? $value : $default_id);

        switch ($display_as) {
            case 'dropdown':
                $attributes = [
                    'class' => 'form-control ' . $cfg->get(
                            'width'
                        ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
                ];

                return \Helpers\Html::select_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes);
                break;
            case 'checkboxes':
                $attributes = ['class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

                return '<div class="checkboxes_list ' . ($field['is_required'] == 1 ? ' required' : '') . '">' . \Helpers\Html::select_checkboxes_tag(
                        'fields[' . $field['id'] . ']',
                        $choices,
                        $value,
                        $attributes
                    ) . '</div>';
                break;
            case 'dropdown_multiple':
                $attributes = [
                    'class' => 'form-control chosen-select ' . $cfg->get(
                            'width'
                        ) . ' field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                    'multiple' => 'multiple',
                    'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
                ];

                return \Helpers\Html::select_tag(
                    'fields[' . $field['id'] . '][]',
                    $choices,
                    explode(',', $value),
                    $attributes
                );
                break;
        }
    }

    public function process($options)
    {
        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1) {
            $exp = explode(',', $value);

            foreach ($exp as $choices_id) {
                if ($cfg->get('use_global_list') > 0) {
                    /*$choice_query = db_query(
                        "select * from app_global_lists_choices where id='" . db_input(
                            $choices_id
                        ) . "' and lists_id = '" . db_input($cfg->get('use_global_list')) . "' and length(users)>0"
                    );*/

                    $choice = \K::model()->db_fetch_one('app_global_lists_choices', [
                        'id = ? and lists_id = ? and length(users) > 0',
                        $choices_id,
                        $cfg->get('use_global_list')
                    ], [], 'users');
                } else {
                    /* $choice_query = db_query(
                         "select * from app_fields_choices where id='" . db_input($choices_id) . "' and length(users)>0"
                     );*/

                    $choice = \K::model()->db_fetch_one('app_fields_choices', [
                        'id = ? and length(users) > 0',
                        $choices_id
                    ], [], 'users');
                }

                if ($choice) {
                    $exp = explode(',', $choice['users']);

                    \K::$fw->app_send_to = array_merge(\K::$fw->app_send_to, $exp);
                    /*foreach ($exp as $id) {
                        \K::$fw->app_send_to[] = $id;
                    }*/

                    //check if value changed
                    if (!$options['is_new_item']) {
                        if (!in_array($choices_id, explode(',', $options['current_field_value']))) {
                            \K::$fw->app_send_to_new_assigned = array_merge(\K::$fw->app_send_to_new_assigned, $exp);
                            /*foreach ($exp as $id) {
                                \K::$fw->app_send_to_new_assigned[] = $id;
                            }*/
                        }
                    }
                }
            }
        }

        return $value;
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $html = ($cfg->get('use_global_list') > 0 ? \Models\Main\Global_lists::render_value(
            $options['value']
        ) : \Models\Main\Fields_choices::render_value($options['value']));

        if (!isset($options['is_export'])) {
            $html .= $this->show_users($options);
        }

        return $html;
    }

    public function show_users($options)
    {
        $html = '';

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $show_users = (is_array($cfg->get('show_users')) ? $cfg->get('show_users') : []);

        if ((isset($options['is_listing']) and in_array(
                    'listing',
                    $show_users
                )) or (!isset($options['is_listing']) and in_array('info_page', $show_users))) {
            $users_list = [];

            if (strlen($options['value'])) {
                $exp = explode(',', $options['value']);

                foreach ($exp as $choices_id) {
                    if ($cfg->get('use_global_list') > 0) {
                        /*$choice_query = db_query(
                            "select * from app_global_lists_choices where id='" . db_input(
                                $choices_id
                            ) . "' and lists_id = '" . db_input($cfg->get('use_global_list')) . "' and length(users)>0"
                        );*/

                        $choice = \K::model()->db_fetch_one('app_global_lists_choices', [
                            'id = ? and lists_id = ? and length(users) > 0',
                            $choices_id,
                            $cfg->get('use_global_list')
                        ], [], 'users');
                    } else {
                        /*$choice_query = db_query(
                            "select * from app_fields_choices where id='" . db_input(
                                $choices_id
                            ) . "' and length(users)>0"
                        );*/

                        $choice = \K::model()->db_fetch_one('app_fields_choices', [
                            'id = ? and length(users) > 0',
                            $choices_id
                        ], [], 'users');
                    }

                    if ($choice) {
                        $exp = explode(',', $choice['users']);
                        $users_list = array_merge($users_list, $exp);
                        /*foreach (explode(',', $choice['users']) as $id) {
                            $users_list[] = $id;
                        }*/
                    }
                }
            }

            if (count($users_list)) {
                $current_group_name = '';

                $html .= '<div class="grouped_users_list">';
                foreach ($users_list as $users_id) {
                    if (!isset(\K::$fw->app_users_cache[$users_id])) {
                        continue;
                    }

                    if ($cfg->get(
                            'show_users_access_group'
                        ) == 1 and $current_group_name != \K::$fw->app_users_cache[$users_id]['group_name']) {
                        $current_group_name = \K::$fw->app_users_cache[$users_id]['group_name'];

                        $html .= '<span class="grouped_users_group_name">' . $current_group_name . '</span><br>';
                    }

                    $html .= '<span ' . \Models\Main\Users\Users::render_public_profile(
                            \K::$fw->app_users_cache[$users_id],
                            true
                        ) . '> - ' . \K::$fw->app_users_cache[$users_id]['name'] . '</span><br>';
                }
            }

            $html .= '</div>';
        }

        return $html;
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $sql_query[] = "(select count(*) from app_entity_" . (int)$options['entities_id'] . "_values as cv where cv.items_id = e.id and cv.fields_id = " . (int)$options['filters']['fields_id'] . " and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? ' > 0' : ' = 0');
        }

        return $sql_query;
    }

    public static function get_send_to($value, $cfg)
    {
        $send_to = [];

        if (strlen($value) > 0) {
            $exp = explode(',', $value);

            foreach ($exp as $choices_id) {
                if ($cfg->get('use_global_list') > 0) {
                    /*$choice_query = db_query(
                        "select * from app_global_lists_choices where id='" . db_input(
                            $choices_id
                        ) . "' and lists_id = '" . db_input($cfg->get('use_global_list')) . "' and length(users)>0"
                    );*/
                    $choice = \K::model()->db_fetch_one('app_global_lists_choices', [
                        'id = ? and lists_id = ? and length(users) > 0',
                        $choices_id,
                        $cfg->get('use_global_list')
                    ], [], 'users');
                } else {
                    /*$choice_query = db_query(
                        "select * from app_fields_choices where id='" . db_input($choices_id) . "' and length(users)>0"
                    );*/

                    $choice = \K::model()->db_fetch_one('app_fields_choices', [
                        'id = ? and length(users) > 0',
                        $choices_id
                    ], [], 'users');
                }

                if ($choice) {
                    $exp = explode(',', $choice['users']);
                    $send_to = array_merge($send_to, $exp);
                    /*foreach (explode(',', $choice['users']) as $id) {
                        $send_to[] = $id;
                    }*/
                }
            }
        }

        return $send_to;
    }
}