<?php

namespace Controllers\Main\Users;

class Account extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        //check if logged user is guest
        if (\Models\Main\Users\Guest_login::is_guest()) {
            \Helpers\Urls::redirect_to('main/dashboard');
        }

        \K::$fw->current_entity_id = 1;
        \K::$fw->entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->current_entity_id);

        \K::$fw->obj = \K::model()->db_query_exec(
            'select e.* ' . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                1,
                ''
            ) . ' from app_entity_1 e where e.id = ? and e.field_5 = 1',
            \K::$fw->app_logged_users_id
        );
        //$obj = db_fetch_array($user_query);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'account.php';

        echo \K::view()->render($this->app_layout);
    }

    public function set_cfg()
    {
        if (\K::$fw->POST['key'] and \K::$fw->POST['value']) {
            \K::app_users_cfg()->set(\K::$fw->POST['key'], \K::$fw->POST['value']);
        }
    }

    public function update()
    {
        $msg = [];

        if (\K::fw()->exists('POST.fields.12')) {
            if (\K::$fw->CFG_ALLOW_CHANGE_USERNAME == 1) {
                if (strlen($_POST['fields'][12]) == 0) {
                    $msg[] = \K::$fw->TEXT_ERROR_USERNAME_EMPTY;
                }
            }
        }

        if (\K::fw()->exists('POST.fields.9')) {
            if (strlen(\K::$fw->POST['fields'][9]) == 0) {
                $msg[] = \K::$fw->TEXT_ERROR_USEREMAIL_EMPTY;
            } elseif (strlen(
                    \K::$fw->POST['fields'][9]
                ) > 0 and \K::$fw->CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL == 0) {
                /*$check_query = db_query(
                    "select count(*) as total from app_entity_1 where field_9='" . db_input(
                        $_POST['fields'][9]
                    ) . "'  and id!='" . db_input(\K::$fw->app_logged_users_id) . "'"
                );
                $check = db_fetch_array($check_query);*/
                $check = \K::model()->db_fetch_count('app_entity_1', [
                    'field_9 = ? and id != ?',
                    \K::$fw->POST['fields'][9],
                    \K::$fw->app_logged_users_id
                ]);

                if ($check > 0) {
                    $msg[] = \K::$fw->TEXT_ERROR_USEREMAIL_EXIST;
                }
            }
        }

        if (\K::$fw->CFG_ALLOW_CHANGE_USERNAME == 1 and isset($_POST['fields'][12])) {
            if (strlen($_POST['fields'][12]) > 0) {
                /*$check_query = db_query(
                    "select count(*) as total from app_entity_1 where field_12='" . db_input(
                        $_POST['fields'][12]
                    ) . "'  and id!='" . db_input(\K::$fw->app_logged_users_id) . "'"
                );
                $check = db_fetch_array($check_query);*/
                $check = \K::model()->db_fetch_count('app_entity_1', [
                    'field_12 = ? and id != ?',
                    \K::$fw->POST['fields'][12],
                    \K::$fw->app_logged_users_id
                ]);

                if ($check > 0) {
                    $msg[] = \K::$fw->TEXT_ERROR_USERNAME_EXIST;
                }
            }
        }

        if (count($msg) > 0) {
            foreach ($msg as $v) {
                \K::flash()->addMessage($v, 'error');
            }

            \Helpers\Urls::redirect_to('main/users/account');
        }

        if (\K::fw()->exists('POST.fields')) {
            $fields_values_cache = \Tools\Items\Items::get_fields_values_cache(
                \K::$fw->POST['fields'],
                [\K::$fw->current_entity_id],
                \K::$fw->current_entity_id
            );
        }

        $fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
            \K::$fw->current_entity_id,
            \K::$fw->app_user['group_id']
        );

        /*$item_info_query = db_query(
            "select * from app_entity_" . \K::$fw->current_entity_id . " where id='" . db_input(
                \K::$fw->app_user['id']
            ) . "'"
        );
        $item_info = db_fetch_array($item_info_query);*/

        $item_info = \K::model()->db_fetch_one('app_entity_' . \K::$fw->current_entity_id, [
            'id = ?',
            \K::$fw->app_user['id']
        ]);

        $sql_data = [];

        $excluded_fields_types = "'fieldtype_user_accessgroups','fieldtype_user_status','fieldtype_user_skin','fieldtype_user_last_login_date'";

        if (\K::$fw->CFG_ALLOW_CHANGE_USERNAME == 0) {
            $excluded_fields_types .= ",'fieldtype_user_username'";
        }

        $choices_values = new \Models\Main\Choices_values(\K::$fw->current_entity_id);

        $fields_query = db_query(
            "select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(
            ) . "," . $excluded_fields_types . ") and  f.entities_id='" . db_input(
                \K::$fw->current_entity_id
            ) . "' order by f.sort_order, f.name"
        );
        while ($field = db_fetch_array($fields_query)) {
            //check field access and skip fields without access
            if (isset($fields_access_schema[$field['id']])) {
                continue;
            }

            $value = (\K::$fw->POST['fields'][$field['id']] ?? '');

            //current field value
            $current_field_value = ($item_info['field_' . $field['id']] ?? '');

            $process_options = [
                'class' => $field['type'],
                'value' => $value,
                'fields_cache' => $fields_values_cache,
                'field' => $field,
                'is_new_item' => false,
                'current_field_value' => $current_field_value,
            ];

            $sql_data['field_' . $field['id']] = \Models\Main\Fields_types::process($process_options);

            //prepare choices values for fields with multiple values
            $choices_values->prepare($process_options);
        }

        if (count($sql_data)) {
            \K::model()->db_perform(
                'app_entity_' . \K::$fw->current_entity_id,
                $sql_data,
                ['id = ?', \K::$fw->app_logged_users_id]
            );
        }

        //insert choices values for fields with multiple values
        $choices_values->process(\K::$fw->app_logged_users_id);

        //autoupdate all field types
        \Models\Main\Fields_types::update_items_fields(\K::$fw->current_entity_id, \K::$fw->app_logged_users_id);

        //set user configuration options
        $cfg = ['disable_notification', 'disable_internal_notification', 'disable_highlight_unread'];
        foreach ($cfg as $key) {
            \K::app_users_cfg()->set($key, (\K::$fw->POST['cfg'][$key] ?? ''));
        }

        if (\Helpers\App::is_ext_installed()) {
            //subscribe
            $modules = new modules('mailing');
            $mailing = new mailing(\K::$fw->current_entity_id, \K::$fw->app_logged_users_id);
            $mailing->update($item_info);
        }

        email_verification::check_if_user_email_is_updated();

        $alerts->add(\K::$fw->TEXT_ACCOUNT_UPDATED, 'success');

        \Helpers\Urls::redirect_to('main/users/account');
    }

    public function attachments_upload()
    {
        $verifyToken = md5($app_user['id'] . $_POST['timestamp']);

        if (strlen($_FILES['Filedata']['tmp_name']) and $_POST['token'] == $verifyToken) {
            $file = attachments::prepare_filename($_FILES['Filedata']['name']);

            if (move_uploaded_file(
                $_FILES['Filedata']['tmp_name'],
                DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']
            )) {
                //autoresize images if enabled
                attachments::resize(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file']);

                //add attachments to tmp table
                $sql_data = [
                    'form_token' => $verifyToken,
                    'filename' => $file['name'],
                    'date_added' => date('Y-m-d'),
                    'container' => $_GET['field_id']
                ];
                db_perform('app_attachments', $sql_data);

                //add file to queue
                if (class_exists('file_storage')) {
                    $file_storage = new file_storage();
                    $file_storage->add_to_queue($_GET['field_id'], $file['name']);
                }
            }
        }
    }

    public function attachments_preview()
    {
        $field_id = $_GET['field_id'];

        $attachments_list = $uploadify_attachments[$field_id];

        //get new attachments
        $attachments_query = db_query(
            "select filename from app_attachments where form_token='" . db_input(
                $_GET['token']
            ) . "' and container='" . db_input($_GET['field_id']) . "'"
        );
        while ($attachments = db_fetch_array($attachments_query)) {
            $attachments_list[] = $attachments['filename'];

            if (!in_array($attachments['filename'], $uploadify_attachments_queue[$field_id])) {
                $uploadify_attachments_queue[$field_id][] = $attachments['filename'];
            }
        }

        $delete_file_url = url_for('users/account', 'action=attachments_delete_in_queue');

        echo attachments::render_preview($field_id, $attachments_list, $delete_file_url);
    }

    public function attachments_delete_in_queue()
    {
        attachments::delete_in_queue($_POST['field_id'], $_POST['filename']);
    }

    public function check_unique()
    {
        echo items::check_unique(
            _get::int('entities_id'),
            _post::int('fields_id'),
            $_POST['fields_value'],
            $app_user['id']
        );
    }
}
