<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Comments extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $access_rules = new \Models\Main\Access_rules(\K::$fw->current_entity_id, \K::$fw->current_item_id);

            //checking access
            if (isset(\K::$fw->GET['id']) and !\Models\Main\Users\Users::has_comments_access(
                    'update',
                    $access_rules->get_comments_access_schema()
                )) {
                \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
            } elseif (!\Models\Main\Users\Users::has_comments_access(
                'create',
                $access_rules->get_comments_access_schema()
            )) {
                \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
            }

            //check access for edit comment
            if (isset(\K::$fw->GET['id'])) {
                //check if comment exist
                //$comment_query = db_query("select created_by from app_comments where id='" . _GET('id') . "'");

                $comment = \K::model()->db_fetch_one('app_comments', [
                    'id = ?',
                    \K::$fw->GET['id']
                ], [], 'created_by');

                if (!$comment) {
                    \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
                }

                //check if user can edit comment
                if (\K::$fw->app_user['group_id'] > 0 and $comment['created_by'] != \K::$fw->app_user['id'] and !\Models\Main\Users\Users::has_comments_access(
                        'full',
                        $access_rules->get_comments_access_schema()
                    )) {
                    \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
                }
            }

            $entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->current_entity_id);

            $attachments = (\K::$fw->POST['fields']['attachments'] ?? '');

            if (isset(\K::$fw->GET['is_quick_comment'])) {
                $description = \K::$fw->POST['quick_comments_description'];
            } else {
                $description = \K::$fw->POST['description'];
            }

            if (isset(\K::$fw->GET['is_quick_comment']) and $entity_cfg->get('use_editor_in_comments') == 1) {
                $description = nl2br($description);
            }

            $sql_data = [
                'description' => \K::model()->db_prepare_html_input($description),
                'entities_id' => \K::$fw->current_entity_id,
                'items_id' => \K::$fw->current_item_id,
                'attachments' => \Models\Main\Fields_types::process(
                    ['class' => 'fieldtype_attachments', 'value' => $attachments]
                ),
            ];

            \K::model()->begin();

            if (isset(\K::$fw->GET['id'])) {
                \K::model()->db_update('app_comments', $sql_data, [
                    'id = ?',
                    \K::$fw->GET['id']
                ]);
            } else {
                $sql_data['date_added'] = time();
                $sql_data['created_by'] = \K::$fw->app_user['id'];

                $mapper = \K::model()->db_perform('app_comments', $sql_data);

                $comments_id = \K::model()->db_insert_id($mapper);

                //get item info before update
                /*$item_info_query = db_query(
                    "select * from app_entity_" . \K::$fw->current_entity_id . " where id='" . \K::$fw->current_item_id . "'"
                );
                $item_info = db_fetch_array($item_info_query);*/

                $item_info = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                    'id = ?',
                    \K::$fw->current_item_id
                ]);

                //update fields in comments form if they are exist
                if (isset(\K::$fw->POST['fields'])) {
                    $fields_values_cache = \Models\Main\Items\Items::get_fields_values_cache(
                        \K::$fw->POST['fields'],
                        \K::$fw->current_path_array,
                        \K::$fw->current_entity_id
                    );

                    $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
                        \K::$fw->current_entity_id,
                        \K::$fw->app_user['group_id']
                    );

                    $sql_data = [];

                    $updated_fields = [];

                    /*$fields_query = db_query(
                        "select f.* from app_fields f where f.type not in (" . fields_types::get_reserved_types_list(
                        ) . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input(
                            \K::$fw->current_entity_id
                        ) . "' and f.comments_status = 1 order by f.comments_sort_order, f.name"
                    );*/

                    $fields_query = \K::model()->db_fetch('app_fields', [
                        'type not in (' . \Models\Main\Fields_types::get_reserved_types_list(
                        ) . ',' . \Models\Main\Fields_types::get_users_types_list(
                        ) . ') and entities_id = ? and comments_status = 1',
                        \K::$fw->current_entity_id
                    ], ['order' => 'comments_sort_order,name']);

                    //while ($field = db_fetch_array($fields_query)) {
                    foreach ($fields_query as $field) {
                        $field = $field->cast();

                        //check field access
                        if (isset($fields_access_schema[$field['id']])) {
                            continue;
                        }

                        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

                        $value = (\K::$fw->POST['fields'][$field['id']] ?? '');

                        $process_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'fields_cache' => $fields_values_cache,
                            'field' => $field,
                            'is_new_item' => false,
                            'current_field_value' => ''
                        ];

                        $fields_value = \Models\Main\Fields_types::process($process_options);

                        if (in_array(
                                $field['type'],
                                ['fieldtype_input_date', 'fieldtype_input_datetime', 'fieldtype_time']
                            ) and $fields_value == 0) {
                            $fields_value = '';
                        }

                        if (strlen($fields_value) > 0) {
                            $updated_fields[$field['id']] = $fields_value;

                            //insert comment history
                            \K::model()->db_perform(
                                'app_comments_history',
                                [
                                    'comments_id' => $comments_id,
                                    'fields_id' => $field['id'],
                                    'fields_value' => $fields_value
                                ]
                            );

                            if ($field['type'] == 'fieldtype_time' and $cfg->get('sum_in_comments') == 1) {
                                $sql_data['field_' . (int)$field['id']] = \Tools\FieldsTypes\Fieldtype_time::get_fields_sum_in_comments(
                                    \K::$fw->current_entity_id,
                                    \K::$fw->current_item_id,
                                    $field['id']
                                );
                            } elseif ($field['type'] == 'fieldtype_input_numeric_comments') {
                                $filed_type = new \Tools\FieldsTypes\Fieldtype_input_numeric_comments();
                                $sql_data['field_' . (int)$field['id']] = $filed_type->get_fields_sum(
                                    \K::$fw->current_entity_id,
                                    \K::$fw->current_item_id,
                                    $field['id']
                                );
                            } else {
                                $sql_data['field_' . (int)$field['id']] = $fields_value;

                                //update choices values
                                $choices_values = new \Models\Main\Choices_values(\K::$fw->current_entity_id);
                                $choices_values->process_by_field_id(
                                    \K::$fw->current_item_id,
                                    $field['id'],
                                    $field['type'],
                                    $fields_value
                                );
                            }
                        }
                    }

                    //update item if there are fields to change
                    if (count($sql_data) > 0) {
                        $sql_data['date_updated'] = time();
                        \K::model()->db_update('app_entity_' . (int)\K::$fw->current_entity_id, $sql_data, [
                                'id = ?',
                                \K::$fw->current_item_id
                            ]
                        );

                        \K::$fw->app_changed_fields = [];

                        //autoupdate all field types
                        \Models\Main\Fields_types::update_items_fields(
                            \K::$fw->current_entity_id,
                            \K::$fw->current_item_id
                        );

                        if (\Helpers\App::is_ext_installed()) {
                            //run actions after item update
                            $processes = new processes(\K::$fw->current_entity_id);
                            $processes->run_after_update(\K::$fw->current_item_id);
                        }

                        //autostatus insert change in history if exist
                        foreach (\K::$fw->app_changed_fields as $field) {
                            \K::model()->db_perform(
                                'app_comments_history',
                                [
                                    'comments_id' => $comments_id,
                                    'fields_id' => $field['fields_id'],
                                    'fields_value' => $field['fields_value']
                                ]
                            );
                        }
                    } else {
                        \K::model()->db_perform(
                            'app_entity_' . (int)\K::$fw->current_entity_id, ['date_updated' => time()],
                            [
                                'id = ?',
                                \K::$fw->current_item_id
                            ]
                        );
                    }

                    if (\Helpers\App::is_ext_installed()) {
                        //check public form notification
                        //using $item_info as item with previous values
                        public_forms::send_client_notification(\K::$fw->current_entity_id, $item_info, true);

                        //sending sms
                        $modules = new modules('sms');
                        $sms = new sms(\K::$fw->current_entity_id, \K::$fw->current_item_id);
                        $sms->send_to = false;
                        $sms->send_edit_msg($item_info);
                    }
                }

                //send notification
                \Helpers\App::app_send_new_comment_notification(
                    $comments_id,
                    \K::$fw->current_item_id,
                    \K::$fw->current_entity_id
                );

                //track changes
                if (\Helpers\App::is_ext_installed()) {
                    $log = new track_changes(\K::$fw->current_entity_id, \K::$fw->current_item_id);
                    $log->log_comment($comments_id, (isset(\K::$fw->POST['fields']) ? $updated_fields : []));

                    //email rules
                    $email_rules = new email_rules(\K::$fw->current_entity_id, \K::$fw->current_item_id);
                    $email_rules->send_edit_msg($item_info);
                    $email_rules->send_comments_msg($item_info);
                }
            }

            \K::model()->commit();

            \Helpers\Urls::redirect_to('main/items/info', 'path=' . \K::$fw->POST['path']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST') {
            $access_rules = new \Models\Main\Access_rules(\K::$fw->current_entity_id, \K::$fw->current_item_id);

            if (!\Models\Main\Users\Users::has_comments_access('delete', $access_rules->get_comments_access_schema())) {
                \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
            }

            if (isset(\K::$fw->GET['id'])) {
                \Tools\Attachments::delete_comments_attachments(\K::$fw->GET['id']);

                \K::model()->begin();

                \K::model()->db_delete_row('app_comments', \K::$fw->GET['id']);

                //db_query("delete from app_comments_history where comments_id = '" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_delete_row('app_comments_history', \K::$fw->GET['id'], 'comments_id');

                \Models\Main\Fields_types::recalculate_numeric_comments_sum(\K::$fw->current_entity_id, \K::$fw->current_item_id);

                \K::model()->commit();

                \K::flash()->addMessage(\K::$fw->TEXT_COMMENT_WAS_DELETED, 'success');

                \Helpers\Urls::redirect_to('main/items/info', 'path=' . \K::$fw->GET['path']);
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}