<?php

namespace Tools\FieldsTypes;

class Fieldtype_action
{
    public $options;

    public function __construct()
    {
        $this->options = ['name' => \K::$fw->TEXT_FIELDTYPE_ACTION_TITLE];
    }

    public function output($options)
    {
        $list = [];

        $access_rules = new \Models\Main\Access_rules($options['field']['entities_id'], $options['item']);

        $redirect_to = '';
        if (isset(\K::$fw->POST['page'])) {
            $redirect_to = '&gotopage[' . $options['reports_id'] . ']=' . \K::$fw->POST['page'];
        }

        if (isset($options['redirect_to'])) {
            if (strlen($options['redirect_to']) > 0) {
                $redirect_to .= '&redirect_to=' . $options['redirect_to'];
            }
        }

        if (users::has_access('delete', $access_rules->get_access_schema())) {
            $check = true;

            if (users::has_access(
                    'delete_creator',
                    $access_rules->get_access_schema()
                ) and $options['item']['created_by'] != \K::$fw->app_user['id']) {
                $check = false;
            }

            if ($check) {
                $list[] = button_icon_delete(
                    url_for(
                        'items/delete',
                        'id=' . $options['value'] . '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to
                    )
                );
            }
        }

        if (users::has_access('update', $access_rules->get_access_schema())) {
            $list[] = button_icon_edit(
                url_for(
                    'items/form',
                    'id=' . $options['value'] . '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to
                )
            );
        }

        //change user pasword
        if (users::has_access('update', $access_rules->get_access_schema()) and $options['field']['entities_id'] == 1) {
            $list[] = button_icon(
                \K::$fw->TEXT_CHANGE_PASSWORD,
                'fa fa-unlock-alt',
                url_for(
                    'items/change_user_password',
                    'path=' . $options['path'] . ($options['item']['parent_item_id'] == 0 ? '-' . $options['value'] : '') . $redirect_to
                ),
                false
            );
        }

        //login as user
        if (\K::$fw->app_user['group_id'] == 0 and $options['field']['entities_id'] == 1) {
            if ($options['item']['field_5'] == 1) {
                $list[] = button_icon(
                    \K::$fw->TEXT_BUTTON_LOGIN,
                    'fa fa-sign-in',
                    url_for('users/login_as', 'users_id=' . $options['item']['id']),
                    true
                );
            }
        }

        //check access to action with assigned only
        if ($options['hide_actions_buttons'] == 1) {
            $list = [];
        } else {
            if (users::has_users_access_name_to_entity('action_with_assigned', $options['field']['entities_id'])) {
                if (!users::has_access_to_assigned_item($options['field']['entities_id'], $options['item']['id'])) {
                    $list = [];
                }
            }
        }

        if (isset($options['listing_type']) and $options['listing_type'] == 'tree_table' and users::has_access(
                'create',
                $access_rules->get_access_schema()
            )) {
            $list[] = button_icon(
                \K::$fw->TEXT_BUTTON_CREATE,
                'fa fa-plus',
                url_for(
                    'items/form',
                    'parent_id=' . $options['item']['id'] . '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to
                )
            );
        } else {
            $list[] = button_icon(
                \K::$fw->TEXT_BUTTON_INFO,
                'fa fa-info',
                url_for(
                    'items/info',
                    'path=' . (count(
                        $options['path_info']
                    ) ? $options['path'] : $options['path'] . '-' . $options['value']) . '&gotopage[' . \K::$fw->POST['reports_id'] . ']=' . \K::$fw->POST['page']
                ),
                false
            );
        }

        //hide checkbox for mulitple items update if users don't have access
        if (count($list) == 1 and !users::has_users_access_name_to_entity(
                'export_selected',
                $options['field']['entities_id']
            )) {
            $list[] = '<style>#uniform-items_' . $options['item']['id'] . '{display:none}</style>';

            if (\K::$fw->is_select_all_checkbox_hidden != true) {
                \K::$fw->is_select_all_checkbox_hidden = true;

                $list[] = '<style>#entity_items_listing' . $options['reports_id'] . '_' . $options['field']['entities_id'] . ' #uniform-select_all_items{display:none}</style>';
            }
        }

        return implode(' ', $list);
    }
}