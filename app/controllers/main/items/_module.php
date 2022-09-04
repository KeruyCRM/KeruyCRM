<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class _Module
{
    public static function top()
    {
        $current_path = false;

        if (isset(\K::$fw->GET['path'])) {
            $current_path = \K::$fw->GET['path'];
        } elseif (isset(\K::$fw->POST['path'])) {
            $current_path = \K::$fw->POST['path'];
        }

        if (!$current_path) {
            \Helpers\Urls::redirect_to('main/dashboard');
        }

        \K::$fw->current_path_array = explode('/', $current_path);
        $current_item_array = explode('-', \K::$fw->current_path_array[count(\K::$fw->current_path_array) - 1]);

        \K::$fw->current_entity_id = (int)$current_item_array[0];
        \K::$fw->current_item_id = (isset($current_item_array[1]) ? (int)$current_item_array[1] : 0);

        //set current item ID by get ID
        if ((\K::$fw->app_module_path == 'items/form' or (\K::$fw->app_module_path == 'items/items' and \K::$fw->app_module_action == 'delete')) and isset(\K::$fw->GET['id'])) {
            \K::$fw->current_item_id = \K::$fw->GET['id'];
        }

        //check if entity exist
        if (\K::$fw->current_entity_id > 0) {
            //$tables_list = [];
            $tables_list = \K::model()->getTables();

            /*while ($tables = db_fetch_array($tables_query)) {
                $tables_list[] = current($tables);
            }*/

            if (!in_array('app_entity_' . \K::$fw->current_entity_id, $tables_list)) {
                \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
            }
        }

        //check if item exist
        if (\K::$fw->current_item_id > 0) {
            //check if item exist including access to parent item
            $item_query = \K::model()->db_query_exec_one(
                "select e.id from app_entity_" . \K::$fw->current_entity_id . " e where e.id = ? " . \Models\Main\Users\Records_visibility::add_access_query(
                    \K::$fw->current_entity_id
                ) . " " . \Models\Main\Items\Items::add_access_query_for_parent_entities(\K::$fw->current_entity_id),
                \K::$fw->current_item_id
            );

            if (!$item_query) {
                \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
            }

            //check path to item
            $path_info = \Models\Main\Items\Items::get_path_info(\K::$fw->current_entity_id, \K::$fw->current_item_id);

            if (\K::$fw->app_module_path == 'items/info' and $current_path != $path_info['full_path']) {
                \Helpers\Urls::redirect_to('main/items/info', 'path=' . $path_info['full_path']);
            }
        }

        if (count(\K::$fw->current_path_array) > 1) {
            $v = explode('-', \K::$fw->current_path_array[count(\K::$fw->current_path_array) - 2]);
            $parent_entity_id = (int)$v[0];
            \K::$fw->parent_entity_item_id = (int)$v[1];

            //check path to entity
            if (\K::$fw->current_item_id == 0) {
                $path_info = \Models\Main\Items\Items::get_path_info($v[0], $v[1]);

                if ($current_path != $path_info['full_path'] . '/' . \K::$fw->current_entity_id) {
                    \Helpers\Urls::redirect_to(
                        'main/items/items',
                        'path=' . $path_info['full_path'] . '/' . \K::$fw->current_entity_id
                    );
                }

                //if path is corret then check access to parent item including check access to other parent items
                $item_query = \K::model()->db_query_exec_one(
                    "select e.id from app_entity_" . $parent_entity_id . " e where e.id = ? " . \Models\Main\Items\Items::add_access_query(
                        $parent_entity_id,
                        ''
                    ) . ' ' . \Models\Main\Items\Items::add_access_query_for_parent_entities($parent_entity_id),
                    \K::$fw->parent_entity_item_id
                );

                if (!$item_query) {
                    \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
                }
            }
        } else {
            $parent_entity_id = 0;
            \K::$fw->parent_entity_item_id = 0;
        }

        \K::$fw->app_breadcrumb = \Models\Main\Items\Items::get_breadcrumb(\K::$fw->current_path_array);

        /**
         * access configuration
         */
        //get access by roles if set
        $user_roles_info = false;

        //if parent select check roles access to current entity
        if (\K::$fw->parent_entity_item_id > 0) {
            $user_roles_info = \Models\Main\Users\User_roles::get_access_by_role(
                $parent_entity_id,
                \K::$fw->parent_entity_item_id,
                \K::$fw->current_entity_id
            );
        } elseif (\K::$fw->current_item_id) {
            $user_roles_info = \Models\Main\Users\User_roles::get_access_by_role(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id
            );
        }

        //if there is roles then apply it to $app_users_access
        if ($user_roles_info) {
            foreach ($user_roles_info['roles_entities_access'] as $entity_id => $access) {
                if (count($access)) {
                    $app_users_access[$entity_id] = $access;
                } elseif (isset($app_users_access[$entity_id])) {
                    unset($app_users_access[$entity_id]);
                }
            }
        }

        //get access schema for current entity
        \K::$fw->current_access_schema = ($user_roles_info['current_access_schema'] ?? \Models\Main\Users\Users::get_entities_access_schema(
            \K::$fw->current_entity_id,
            \K::$fw->app_user['group_id']
        ));

        //get comments access schema for current entity
        \K::$fw->current_comments_access_schema = ($user_roles_info['current_comments_access_schema'] ?? \Models\Main\Users\Users::get_comments_access_schema(
            \K::$fw->current_entity_id,
            \K::$fw->app_user['group_id']
        ));

        //set roles fields access if exist
        \K::$fw->roles_fields_access_schema = ($user_roles_info['fields_access_schema'] ?? false);

        if (in_array(
                \K::$fw->app_module_action,
                ['preview_attachment_exel', 'preview_attachment_image', 'download_attachment']
            ) and \K::$fw->current_entity_id == 1 and \K::$fw->current_item_id == \K::$fw->app_user['id']) {
            //allows access to download attachment from my account page
        } else {
            //checking view access
            if (!\Models\Main\Users\Users::has_access('view') and !\Models\Main\Users\Users::has_access(
                    'view_assigned'
                )) {
                \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
            }

            //check assigned access
            if (\Models\Main\Users\Users::has_access(
                    'view_assigned'
                ) and \K::$fw->app_user['group_id'] > 0 and \K::$fw->current_item_id > 0) {
                if (!\Models\Main\Users\Users::has_access_to_assigned_item(
                    \K::$fw->current_entity_id,
                    \K::$fw->current_item_id
                )) {
                    \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
                }
            }

            //check assigned access only
            if (\Models\Main\Users\Users::has_access('action_with_assigned') and \K::$fw->app_user['group_id'] > 0 and
                ((\K::$fw->app_module_path == 'items/items' and strlen(\K::$fw->app_module_action) and !in_array(
                            \K::$fw->app_module_action,
                            ['attachments_upload', 'attachments_preview']
                        )) or
                    (\K::$fw->app_module_path == 'items/form' and isset(\K::$fw->GET['id'])))) {
                if (!\Models\Main\Users\Users::has_access_to_assigned_item(
                        \K::$fw->current_entity_id,
                        \K::$fw->current_item_id
                    ) and \K::$fw->current_item_id > 0) {
                    \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
                }
            }
        }
    }
}