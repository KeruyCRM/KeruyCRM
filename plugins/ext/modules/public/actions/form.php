<?php

//check security settings if they are enabled
app_restricted_countries::verify();
app_restricted_ip::verify();

$app_layout = 'public_layout.php';

$public_form_query = db_query("select * from app_ext_public_forms where id='" . db_input(_get::int('id')) . "'");
if (!$public_form = db_fetch_array($public_form_query)) {
    die(TEXT_PAGE_NOT_FOUND_CONTENT);
}

if ($public_form['is_active'] == 0) {
    redirect_to('ext/public/form_inactive', 'id=' . $public_form['id']);
}

//check if submit form disabled and check enquiry enabled
if ($public_form['check_enquiry'] == 1 and $public_form['disable_submit_form'] == 1) {
    redirect_to('ext/public/check', 'id=' . $public_form['id']);
}

$app_title = (strlen($public_form['page_title']) > 0 ? $public_form['page_title'] : $public_form['name']);

$current_entity_id = $public_form['entities_id'];
$current_path_array = [$public_form['entities_id']];
$app_user = [];
$app_user['id'] = 0;
$app_user['group_id'] = 0;
$app_user['name'] = CFG_EMAIL_NAME_FROM;
$app_user['email'] = CFG_EMAIL_ADDRESS_FROM;
$app_user['language'] = CFG_APP_LANGUAGE;

$entity_cfg = new entities_cfg($current_entity_id);

if (!app_session_is_registered('public_form_success_msg')) {
    $public_form_success_msg = '';
    app_session_register('public_form_success_msg');
}

switch ($app_module_action) {
    case 'get_css':
        header("Content-Type: text/css");
        header("X-Content-Type-Options: nosniff");
        header("Cache-Control: max-age=604800, public");
        echo $public_form['form_css'];
        exit();
        break;
    case 'save':

        //chck form token
        app_check_form_token('ext/public/form&id=' . $public_form['id']);

        $is_error = false;

        //check reaptcha
        if (app_recaptcha::is_enabled()) {
            if (!app_recaptcha::verify()) {
                $alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT, 'error');

                $is_error = true;
            }
        }

        if (!$is_error) {
            $fields_values_cache = items::get_fields_values_cache(
                $_POST['fields'],
                $current_path_array,
                $current_entity_id
            );

            $app_send_to = [];
            $is_new_item = true;
            $item_info = [];

            //prepare item data
            $sql_data = [];

            $choices_values = new choices_values($current_entity_id);

            $fields_query = db_query(
                "select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(
                ) . ",'fieldtype_related_records') and  f.entities_id='" . db_input(
                    $current_entity_id
                ) . "' order by f.sort_order, f.name"
            );
            while ($field = db_fetch_array($fields_query)) {
                $default_field_value = '';

                if (in_array($field['type'], fields_types::get_types_wich_choices())) {
                    $cfg = new fields_types_cfg($field['configuration']);

                    if ($cfg->get('use_global_list') > 0) {
                        $check_query = db_query(
                            "select id from app_global_lists_choices where lists_id = '" . db_input(
                                $cfg->get('use_global_list')
                            ) . "' and is_default=1"
                        );
                    } else {
                        $check_query = db_query(
                            "select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1"
                        );
                    }

                    if ($check = db_fetch_array($check_query)) {
                        $default_field_value = $check['id'];
                    }
                }

                //submited field value
                $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : $default_field_value);

                //current field value
                $current_field_value = (isset($item_info['field_' . $field['id']]) ? $item_info['field_' . $field['id']] : '');

                //prepare process options
                $process_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'fields_cache' => $fields_values_cache,
                    'field' => $field,
                    'is_new_item' => $is_new_item,
                    'current_field_value' => $current_field_value,
                ];

                $sql_data['field_' . $field['id']] = fields_types::process($process_options);

                //prepare choices values for fields with multiple values
                $choices_values->prepare($process_options);
            }

            $parent_item_id = 0;

            if (isset($_POST['parent_item_id'])) {
                $parent_item_id = (int)$_POST['parent_item_id'];
            }

            $sql_data['date_added'] = time();
            $sql_data['created_by'] = 0;
            $sql_data['parent_item_id'] = $parent_item_id;

            //echo '<pre>';
            //print_r($sql_data);
            //exit();

            db_perform('app_entity_' . $current_entity_id, $sql_data);
            $item_id = db_insert_id();

            //insert choices values for fields with multiple values
            $choices_values->process($item_id);

            //autoupdate all field types
            fields_types::update_items_fields($current_entity_id, $item_id);

            //log changeds
            if (class_exists('track_changes')) {
                $log = new track_changes($current_entity_id, $item_id);
                $log->log_insert();
            }

            //subscribe
            $modules = new modules('mailing');
            $mailing = new mailing($current_entity_id, $item_id);
            $mailing->subscribe();

            //run actions after item insert
            $processes = new processes($current_entity_id);
            $processes->run_after_insert($item_id);


            /**
             * Start email notification code
             **/

            $subject = items::send_new_item_nofitication($current_entity_id, $item_id, $app_send_to);


            //prepare email notification for customer
            $email_content = public_forms::prepare_email_content($public_form, $public_form['entities_id'], $item_id);
            $html = $email_content['html'];
            $attachments = $email_content['attachments'];
            $item = $email_content['item'];


            $fieldtype_text_pattern = new fieldtype_text_pattern();

            $output_options = ['item' => $item];
            $output_options['field']['configuration'] = '';
            $output_options['field']['entities_id'] = $public_form['entities_id'];
            $output_options['path'] = $public_form['entities_id'] . '-' . $item['id'];


            $output_options['custom_pattern'] = nl2br($public_form['successful_sending_message']);
            $successful_sending_message = $fieldtype_text_pattern->output($output_options);


            //send notification to customer
            if (strlen($public_form['customer_name']) and strlen($public_form['customer_email'])) {
                $customer_email = $item['field_' . $public_form['customer_email']];

                $customer_name = [];

                foreach (explode(',', $public_form['customer_name']) as $field_id) {
                    $customer_name[] = $item['field_' . $field_id];
                }

                $customer_name = implode(' ', $customer_name);


                //send email if valid
                if (app_validate_email($customer_email)) {
                    //subject
                    $output_options['custom_pattern'] = $public_form['customer_message_title'];
                    $customer_message_title = $fieldtype_text_pattern->output($output_options);

                    //body
                    $output_options['custom_pattern'] = $public_form['customer_message'];
                    $customer_message = $fieldtype_text_pattern->output($output_options);

                    $body = (strlen($customer_message) ? nl2br($customer_message) . '<br>' . $html : $html);

                    $body = users::use_email_pattern('single_column', ['email_single_column' => $body]);

                    $options = [
                        'to' => $customer_email,
                        'to_name' => $customer_name,
                        'subject' => $customer_message_title,
                        'body' => $body,
                        'attachments' => $attachments,
                        'force_send_from' => true,
                    ];

                    //Set form address. If set form admin then use it
                    if (strlen($public_form['admin_name']) and strlen($public_form['admin_email'])) {
                        $options['from'] = $public_form['admin_email'];
                        $options['from_name'] = $public_form['admin_name'];
                    } else {
                        $options['from'] = CFG_EMAIL_ADDRESS_FROM;
                        $options['from_name'] = CFG_EMAIL_NAME_FROM;
                    }

                    users::send_email($options);
                }
            }


            //send notification to admin
            if (strlen($public_form['admin_name']) and strlen(
                    $public_form['admin_email']
                ) and $public_form['admin_notification'] == 1) {
                $html = users::use_email_pattern('single_column', ['email_single_column' => $html]);

                $options = [
                    'to' => $public_form['admin_email'],
                    'to_name' => $public_form['admin_name'],
                    'from' => (isset($customer_email) ? $customer_email : CFG_EMAIL_ADDRESS_FROM),
                    'from_name' => (isset($customer_name) ? $customer_name : CFG_EMAIL_NAME_FROM),
                    'subject' => $subject,
                    'body' => $html,
                    'attachments' => $attachments,
                    'force_send_from' => true,
                ];

                users::send_email($options);
            }

            //print_r($attachments);
            //echo $body;
            //exit();

            /**
             * End email notification code
             **/

            //success msg
            $public_form_success_msg = (strlen(
                $successful_sending_message
            ) ? $successful_sending_message : TEXT_EXT_PB_SUCCESSFUL_SENDING_MESSAGE_DEFAULT);

            switch ($public_form['after_submit_action']) {
                case 'display_success_text':
                    redirect_to('ext/public/form', 'action=success&id=' . $public_form['id']);
                    break;
                case 'goto':
                    echo '
								<script>
									window.top.location.href = "' . $public_form['after_submit_redirect'] . '";
								</script>
								';

                    exit();
                    break;
                default:
                    $alerts->add($public_form_success_msg, 'success');

                    redirect_to('ext/public/form', 'id=' . $public_form['id']);
                    break;
            }
        }
        break;

    case 'success':
        $app_action = 'success';
        break;

    case 'attachments_upload':
        $verifyToken = md5($app_session_token . $_POST['timestamp']);

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
        exit();
        break;

    case 'attachments_preview':
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

        $delete_file_url = url_for('ext/public/form', 'action=attachments_delete_in_queue&id=' . $public_form['id']);

        echo attachments::render_preview($field_id, $attachments_list, $delete_file_url);

        exit();
        break;
    case 'attachments_delete_in_queue':
        //chck form token
        app_check_form_token();

        attachments::delete_in_queue($_POST['field_id'], $_POST['filename']);

        exit();
        break;

    case 'check_unique':

        //chck form token
        app_check_form_token();

        $unique_for_each_parent = $_POST['unique_for_each_parent'] == 1 ? _POST('parent_item_id') : false;
        echo items::check_unique(
            _get::int('entities_id'),
            _post::int('fields_id'),
            $_POST['fields_value'],
            false,
            $unique_for_each_parent
        );

        exit();
        break;
}
	