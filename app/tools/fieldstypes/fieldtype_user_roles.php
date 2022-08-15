<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_user_roles
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_USER_ROLES_TITLE];
    }

    public function get_configuration($params = [])
    {
        $entity_info = \K::model()->db_find('app_entities', $params['entities_id']);

        $cfg = [];
        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-xlarge'],
            'choices' => [
                'dropdown' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN,
                'dropdown_multiple' => \K::$fw->TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE
            ]
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_NAME_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS,
            'name' => 'disable_notification',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO
        ];

        if ($entity_info['parent_id'] > 0) {
            $cfg[] = [
                'title' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY,
                'name' => 'disable_dependency',
                'type' => 'checkbox',
                'tooltip_icon' => \K::$fw->TEXT_DISABLE_USERS_DEPENDENCY_INFO
            ];
        }

        $cfg[] = ['title' => \K::$fw->TEXT_HIDE_ADMIN, 'name' => 'hide_admin', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT,
            'name' => 'authorized_user_by_default',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_AUTHORIZED_USER_BY_DEFAULT_INFO
        ];

        return $cfg;
    }

    public static function get_choices($field, $params, $value = '')
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $entities_id = $field['entities_id'];

        //get access schema
        $access_schema = \Models\Main\Users\Users::get_entities_access_schema_by_groups($entities_id);

        //check if parent item has users fields and if users are assigned
        $has_parent_users = false;
        $parent_users_list = [];

        if (isset($params['parent_entity_item_id']) and $params['parent_entity_item_id'] > 0 and $cfg->get(
                'disable_dependency'
            ) != 1) {
            if ($parent_users_list = \Models\Main\Items\Items::get_parent_users_list(
                $entities_id,
                $params['parent_entity_item_id']
            )) {
                $has_parent_users = true;
            }
        }

        //get users choices
        //select all active users or already assigned users
        $where_sql = (strlen($value) ? "(u.field_5 = 1 or u.id in (" . $value . "))" : "u.field_5 = 1");

        //hide administrators
        if ($cfg->get('hide_admin') == 1) {
            $where_sql .= " and u.field_6 > 0 ";
        }

        $choices = [];
        $order_by_sql = (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');

        $users_query = \K::model()->db_query_exec(
            "select u.*, a.name as group_name from app_entity_1 u left join app_access_groups a on a.id = u.field_6 where {$where_sql} order by group_name, " . $order_by_sql,
            null,
            'app_entity_1,app_access_groups'
        );

        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            if (!isset($access_schema[$users['field_6']])) {
                $access_schema[$users['field_6']] = [];
            }

            if ($users['field_6'] == 0 or in_array('view', $access_schema[$users['field_6']]) or in_array(
                    'view_assigned',
                    $access_schema[$users['field_6']]
                )) {
                //check parent users and check already assigned
                if ($has_parent_users and !in_array($users['id'], $parent_users_list) and !in_array(
                        $users['id'],
                        explode(',', $value)
                    )) {
                    continue;
                }

                $group_name = (strlen($users['group_name']) > 0 ? $users['group_name'] : \K::$fw->TEXT_ADMINISTRATOR);
                $choices[$group_name][$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
            }
        }

        return $choices;
    }

    public static function set_user_roles_to_items($entities_id, $items_id)
    {
        if (!isset(\K::$fw->POST['user_roles'])) {
            return false;
        }

        /*$fields_query = db_query(
            "select id from app_fields where entities_id='" . $entities_id . "' and type='fieldtype_user_roles'"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'entities_id = ? and type = ?',
            $entities_id,
            'fieldtype_user_roles'
        ], [], 'id');

        $forceCommit = \K::model()->forceCommit();

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            //reset roles
            /*db_query(
                "delete from app_user_roles_to_items where entities_id='" . $entities_id . "' and items_id='" . $items_id . "' and fields_id='" . $fields['id'] . "'"
            );*/

            \K::model()->db_delete('app_user_roles_to_items', [
                'entities_id = ? and items_id = ? and fields_id = ?',
                $entities_id,
                $items_id,
                $fields['id']
            ]);

            if (isset(\K::$fw->POST['user_roles'][$fields['id']])) {
                $sql_data = [];

                foreach (\K::$fw->POST['user_roles'][$fields['id']] as $users_id => $roles_id) {
                    //skip if role not selected
                    if ((int)$roles_id == 0) {
                        continue;
                    }

                    $sql_data[] = [
                        'fields_id' => $fields['id'],
                        'entities_id' => $entities_id,
                        'items_id' => $items_id,
                        'users_id' => $users_id,
                        'roles_id' => $roles_id,
                    ];

                    \K::model()->db_perform('app_user_roles_to_items', $sql_data);
                }
                //db_batch_insert('app_user_roles_to_items', $sql_data);
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    public function render($field, $obj, $params = [])
    {
        //reset holder
        \K::$fw->user_roles_dropdown_change_holder = [];

        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        //$entities_id = $field['entities_id'];

        if ($params['is_new_item'] == 1) {
            $value = ($cfg->get('authorized_user_by_default') == 1 ? \K::$fw->app_user['id'] : '');
        } else {
            $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : '');
        }

        $choices = self::get_choices($field, $params, $value);

        $html = '';

        if ($cfg->get('display_as') == 'dropdown') {
            //add empty value for comment form
            $choices = ($params['form'] == 'comment' ? ['' => ''] + $choices : $choices);

            $attributes = ['class' => 'form-control chosen-select input-large field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')];

            $html = \Helpers\Html::select_tag(
                'fields[' . $field['id'] . ']',
                ['' => \K::$fw->TEXT_NONE] + $choices,
                $value,
                $attributes
            );
        } elseif ($cfg->get('display_as') == 'dropdown_multiple') {
            $attributes = [
                'class' => 'form-control input-xlarge chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => \K::$fw->TEXT_SELECT_SOME_VALUES
            ];
            $html = \Helpers\Html::select_tag(
                'fields[' . $field['id'] . '][]',
                $choices,
                explode(',', $value),
                $attributes
            );
        }

        $html .= '
    		<div id="user_roles_box_' . $field['id'] . '"></div>
    				
    		<script>
    			function render_user_roles_box_' . $field['id'] . '()
    			{
    			  $("#user_roles_box_' . $field['id'] . '").load("' . \Helpers\Urls::url_for(
                'main/items/user_roles_form',
                'path=' . $field['entities_id'] . '&items_id=' . $obj['id'] . '&fields_id=' . $field['id']
            ) . '",{users: $("#fields_' . $field['id'] . '").val()})		
    			}
    			  		
    			function user_rolese_' . $field['id'] . '_hold_change(user_id,role_id)
    			{
    			  $.ajax({
    					method: "POST",
    					url: "' . \Helpers\Urls::url_for(
                'main/items/user_roles_form/user_roles_hold_change',
                'path=' . $field['entities_id'] . '&items_id=' . $obj['id'] . '&fields_id=' . $field['id']
            ) . '",
    					data: {user_id:user_id,role_id:role_id}
  					})		
  				}
    					
    			$(function(){
  			  	render_user_roles_box_' . $field['id'] . '();
  			  	
  			  	$("#fields_' . $field['id'] . '").change(function(){
  			  		render_user_roles_box_' . $field['id'] . '();
  					})		
  				})		
    		</script>		
    		';

        return $html;
    }

    public function process($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1) {
            if (is_array($options['value'])) {
                \K::$fw->app_send_to = array_merge($options['value'], \K::$fw->app_send_to);
            } else {
                \K::$fw->app_send_to[] = $options['value'];
            }
        }

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //reset role if no users selected
        if (!strlen($value)) {
            /*db_query(
                "delete from app_user_roles_to_items where fields_id='" . $options['field']['id'] . "' and  entities_id='" . $options['field']['entities_id'] . "' and items_id='" . $options['item']['id'] . "'"
            );*/

            \K::model()->db_delete('app_user_roles_to_items', [
                'fields_id = ? and entities_id = ? and items_id = ?',
                $options['field']['id'],
                $options['field']['entities_id'],
                $options['item']['id']
            ]);
        }

        //check if value changed
        if ($cfg->get('disable_notification') != 1) {
            if (!$options['is_new_item']) {
                if ($value != $options['current_field_value']) {
                    $array = array_diff(explode(',', $value), explode(',', $options['current_field_value']));

                    \K::$fw->app_send_to_new_assigned = array_merge(\K::$fw->app_send_to_new_assigned, $array);
                    /*foreach (array_diff(explode(',', $value), explode(',', $options['current_field_value'])) as $v) {
                        \K::$fw->app_send_to_new_assigned[] = $v;
                    }*/
                }
            }
        }

        return $value;
    }

    public function output($options)
    {
        if (isset($options['is_export']) or isset($options['is_listing'])) {
            $users_list = [];
            foreach (explode(',', $options['value']) as $id) {
                if (isset(\K::$fw->app_users_cache[$id])) {
                    $users_list[] = \K::$fw->app_users_cache[$id]['name'];
                }
            }

            return implode(', ', $users_list);
        } else {
            $html = '';

            /*$roles_query = db_query(
                "select * from app_user_roles where fields_id='" . db_input(
                    $options['field']['id']
                ) . "' order by sort_order, name"
            );*/

            $roles_query = \K::model()->db_fetch('app_user_roles', [
                'fields_id = ?',
                $options['field']['id']
            ], ['order' => 'sort_order,name'], 'id,name');

            //while ($roles = db_fetch_array($roles_query)) {
            foreach ($roles_query as $roles) {
                $roles = $roles->cast();

                $users_list = [];
                /*$users_query = db_query(
                    "select users_id from app_user_roles_to_items where fields_id='" . $options['field']['id'] . "' and entities_id='" . $options['field']['entities_id'] . "' and items_id='" . $options['item']['id'] . "' and roles_id='" . $roles['id'] . "'"
                );*/

                $users_query = \K::model()->db_fetch('app_user_roles_to_items', [
                    'fields_id = ? and entities_id = ? and items_id = ? and roles_id = ?',
                    $options['field']['id'],
                    $options['field']['entities_id'],
                    $options['item']['id'],
                    $roles['id']
                ], [], 'users_id');

                //while ($users = db_fetch_array($users_query)) {
                foreach ($users_query as $users) {
                    $users = $users->cast();

                    $id = $users['users_id'];
                    if (isset(\K::$fw->app_users_cache[$id])) {
                        if (isset($options['display_user_photo'])) {
                            $photo = '<div class="user-photo-box">' . \Helpers\App::render_user_photo(
                                    \K::$fw->app_users_cache[$id]['photo']
                                ) . '</div>';
                            $is_photo_display = true;
                        } else {
                            $photo = '';
                            $is_photo_display = false;
                        }

                        $users_list[] = $photo . ' <div class="user-name" ' . \Models\Main\Users\Users::render_public_profile(
                                \K::$fw->app_users_cache[$id],
                                $is_photo_display
                            ) . '>' . \K::$fw->app_users_cache[$id]['name'] . '</div> <div style="clear:both"></div>';
                    }
                }

                if (count($users_list)) {
                    $html .= '<h5>' . $roles['name'] . '</h5>' . implode('', $users_list);
                }
            }

            return $html;
        }
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        if (strlen($filters['filters_values']) > 0) {
            $filters['filters_values'] = str_replace(
                'current_user_id',
                \K::$fw->app_user['id'],
                $filters['filters_values']
            );

            $sql_query[] = "(select count(*) from app_entity_" . (int)$options['entities_id'] . "_values as cv where cv.items_id = e.id and cv.fields_id = " . (int)$options['filters']['fields_id'] . " and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? ' > 0' : ' = 0');
        }

        return $sql_query;
    }
}