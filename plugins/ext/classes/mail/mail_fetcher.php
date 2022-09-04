<?php

class mail_fetcher
{

    public $mail_account, $mail;

    function __construct()
    {
        //reset account if all fetched
        $accounts_query = db_query("select id from app_ext_mail_accounts where is_fetched=0 and is_active=1 limit 1");
        if (!$accounts = db_fetch_array($accounts_query)) {
            db_query("update app_ext_mail_accounts set is_fetched=0 where is_active=1");
        }

        //get one active account
        $accounts_query = db_query(
            "select * from app_ext_mail_accounts where is_fetched=0 and is_active=1 order by id limit 1"
        );
        if ($accounts = db_fetch_array($accounts_query)) {
            $this->mail_account = $accounts;
        } else {
            $this->mail_account = false;
        }
    }

    static function fetch_all()
    {
        $mail = new mail_fetcher();

        db_query("update app_ext_mail_accounts set is_fetched=0 where is_active=1");

        $accounts_query = db_query(
            "select * from app_ext_mail_accounts where is_fetched=0 and is_active=1 order by id"
        );
        while ($accounts = db_fetch_array($accounts_query)) {
            $mail->mail_account = $accounts;
            $mail->fetch();
        }
    }

    function fetch()
    {
        //skip if no accounts
        if (!$this->mail_account) {
            return false;
        }

        //update is_fetched for current account
        db_query("update app_ext_mail_accounts set is_fetched=1 where id='" . $this->mail_account['id'] . "'");

        //get connection
        $mode = CL_EXPUNGE;
        if (!$conn = imap_open(
            "{" . $this->mail_account['imap_server'] . "}" . $this->mail_account['mailbox'],
            $this->mail_account['login'],
            $this->mail_account['password'],
            $mode
        )) {
            echo imap_last_error();
            exit();
        }

        //get num msg UNSEEN
        if ($uids = imap_search($conn, 'UNSEEN')) //RECENT //ALL //NEW //UNSEEN
        {
            $this->fetch_msg($conn, $uids);
        }

        imap_expunge($conn);
        imap_close($conn);
    }

    function fetch_msg($conn, $uids)
    {
        //print_r($uids);

        $this->mail = [];

        foreach ($uids as $i) {
            //echo $i . '<br>';
            //if($i!=4) continue;
            //delete emails if set flag
            if ($this->mail_account['delete_emails'] == 1) {
                imap_delete($conn, $i);
            }

            // Get the message header
            $header = imap_fetchheader($conn, $i, FT_PREFETCHTEXT);

            //get body
            $body = imap_body($conn, $i);

            // get some readable text from the mime input
            $params = [];
            $params['decode_headers'] = 'UTF-8';
            $params['crlf'] = "\r\n";
            $params['include_bodies'] = true;
            $params['decode_bodies'] = true;
            $params['input'] = $header . $body;
            $Mail_mimeDecode = new Mail_mimeDecode($header . $body);
            $output = $Mail_mimeDecode->decode($params);

            //print_rr($output);

            $structure = imap_fetchstructure($conn, $i);

            $part_array = $this->create_part_array($structure);

            $parts = [];
            $this->parse_mime_decode_output($output, $parts);

            if (!isset($parts['html'])) {
                $parts['html'][0] = '';
            }
            if (!isset($parts['text'])) {
                $parts['text'][0] = '';
            }

            //print_rr($body);			
            //exit();
            //set mail data
            $this->mail[$i]['date_added'] = $this->get_email_date($output);
            $this->mail[$i]['is_new'] = 1;
            $this->mail[$i]['accounts_id'] = $this->mail_account['id'];
            $this->mail[$i]['subject'] = trim($output->headers['subject']);
            $this->mail[$i]['body'] = (strlen($parts['html'][0]) > 0 ? (trim($parts['html'][0])) : '');
            $this->mail[$i]['body_text'] = (trim($parts['text'][0]));

            //set headers
            $this->get_headers($output, 'to', $i);
            $this->get_headers($output, 'from', $i);
            $this->get_headers($output, 'reply-to', $i);
            $this->get_headers($output, 'cc', $i);
            $this->get_headers($output, 'bcc', $i);

            //check mail filters before load attachments
            if (strlen($filets_action = mail_filters::check($this->mail[$i]))) {
                if ($filets_action == 'delete') {
                    unset($this->mail[$i]);
                    continue;
                }

                if ($filets_action == 'skip_spam') {
                    $this->mail[$i]['is_spam'] = 0;
                }
            }

            //set attachments
            $attachments = $this->get_attachments($part_array);
            $attachemts_list = $this->insert_attachments($conn, $i, $attachments);
            //print_rr($attachemts_list);

            $this->mail[$i]['attachments'] = $attachemts_list;

            //set subject cropped
            $subject_cropped = $this->crop_subject($this->mail[$i]['subject']);

            //if no subject entered
            if (!strlen($subject_cropped)) {
                if (strlen($this->mail[$i]['body'])) {
                    $subject_cropped = $this->crop_subject(substr(strip_tags($this->mail[$i]['body']), 0, 60));
                } elseif (strlen($this->mail[$i]['body_text'])) {
                    $subject_cropped = $this->crop_subject(substr($this->mail[$i]['body_text'], 0, 60));
                }

                $this->mail[$i]['subject'] = $subject_cropped;
            }

            $this->mail[$i]['subject_cropped'] = $subject_cropped;

            //handle groups
            $group_info = $this->get_mail_groups_id($subject_cropped, $this->mail[$i]['from_email']);
            $this->mail[$i]['groups_id'] = $group_info['id'];
            $this->mail[$i]['is_new_group'] = $group_info['is_new'];

            //if spam flag is not set by filters
            if (!isset($this->mail[$i]['is_spam'])) {
                $this->mail[$i]['is_spam'] = $this->get_spam_flag($output);
            }
        }

        //print_rr($this->mail);
        //exit();

        $this->save_msg();
    }

    function get_spam_flag($output)
    {
        if (isset($output->headers['x-spam-flag'])) {
            if ($output->headers['x-spam-flag'] == 'YES') {
                return 1;
            }
        }

        return 0;
    }

    function save_msg()
    {
        //check if entity related to mail account with auto create option
        $check_query = db_query(
            "select id from app_ext_mail_accounts_entities where auto_create in (1,2) and accounts_id='" . $this->mail_account['id'] . "' limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            $this->save_msg_auto_create_items();
        } else {
            db_batch_insert('app_ext_mail', $this->mail);
        }

        //handle autoreply
        $this->send_autoreply_to_all();
    }

    function save_msg_auto_create_items()
    {
        global $app_send_to;

        foreach ($this->mail as $mail_data) {
            //insert email in database
            db_perform('app_ext_mail', $mail_data);
            $mail_id = db_insert_id();

            //skip autocreate email if email with the same group already exist
            if (!$mail_data['is_new_group']) {
                continue;
            }

            $app_send_to = [];

            //autocreate items
            $account_entities_query = db_query(
                "select * from app_ext_mail_accounts_entities where auto_create in (1,2) and accounts_id='" . $this->mail_account['id'] . "'"
            );
            while ($account_entities = db_fetch_array($account_entities_query)) {
                //check entities rules
                if (mail_entities_rules::has_rules($account_entities['id'])) {
                    if (!mail_entities_rules::get_rule($mail_data, $account_entities['id'])) {
                        continue;
                    }
                }

                $current_entity_id = $account_entities['entities_id'];

                $sql_data = [];

                $choices_values = new choices_values($current_entity_id);

                $fields_query = db_query(
                    "select f.* from app_fields f where f.type not in (" . fields_types::get_reserved_types_list(
                    ) . ",'fieldtype_related_records','fieldtype_user_last_login_date','fieldtype_google_map') and  f.entities_id='" . db_input(
                        $current_entity_id
                    ) . "' order by f.sort_order, f.name"
                );
                while ($field = db_fetch_array($fields_query)) {
                    $default_field_value = '';


                    //get default fields values setup
                    $entities_fields_query = db_query(
                        "select * from app_ext_mail_accounts_entities_fields where account_entities_id='" . $account_entities['id'] . "' and fields_id='" . $field['id'] . "' and filters_id=0"
                    );
                    if ($entities_fields = db_fetch_array($entities_fields_query)) {
                        if ($field['type'] == 'fieldtype_input_date') {
                            $sql_data['field_' . $field['id']] = (strlen(
                                $entities_fields['value']
                            ) < 5 ? get_date_timestamp(
                                date('Y-m-d', strtotime($entities_fields['value'] . ' day'))
                            ) : $entities_fields['value']);
                            continue;
                        } elseif ($field['type'] == 'fieldtype_input_datetime') {
                            $sql_data['field_' . $field['id']] = (strlen($entities_fields['value']) < 5 ? strtotime(
                                $entities_fields['value'] . ' day'
                            ) : $entities_fields['value']);
                            continue;
                        } else {
                            $default_field_value = $entities_fields['value'];
                        }
                    } else {
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
                            } else {
                                continue;
                            }
                        } elseif (!isset($_GET['id']) and $field['type'] == 'fieldtype_user_accessgroups') {
                            $default_field_value = access_groups::get_default_group_id();
                        }
                    }

                    //get field value by filter

                    if ($entities_fields = mail_entities_filters::get_field(
                        $mail_data,
                        $account_entities['id'],
                        $field['id']
                    )) {
                        if ($field['type'] == 'fieldtype_input_date') {
                            $sql_data['field_' . $field['id']] = (strlen(
                                $entities_fields['value']
                            ) < 5 ? get_date_timestamp(
                                date('Y-m-d', strtotime($entities_fields['value'] . ' day'))
                            ) : $entities_fields['value']);
                            continue;
                        } elseif ($field['type'] == 'fieldtype_input_datetime') {
                            $sql_data['field_' . $field['id']] = (strlen($entities_fields['value']) < 5 ? strtotime(
                                $entities_fields['value'] . ' day'
                            ) : $entities_fields['value']);
                            continue;
                        } else {
                            $default_field_value = $entities_fields['value'];
                        }
                    }

                    //prepare process options
                    $process_options = [
                        'class' => $field['type'],
                        'value' => $default_field_value,
                        'fields_cache' => [],
                        'field' => $field,
                        'is_new_item' => true,
                        'current_field_value' => '',
                    ];

                    $sql_data['field_' . $field['id']] = fields_types::process($process_options);

                    //prepare choices values for fields with multiple values
                    $choices_values->prepare($process_options);
                }

                if ($account_entities['from_name'] > 0) {
                    $sql_data['field_' . $account_entities['from_name']] = $mail_data['from_name'];
                }
                if ($account_entities['from_email'] > 0) {
                    $sql_data['field_' . $account_entities['from_email']] = $mail_data['from_email'];
                }
                if ($account_entities['subject'] > 0) {
                    $sql_data['field_' . $account_entities['subject']] = $mail_data['subject_cropped'];
                }
                if ($account_entities['body'] > 0) {
                    $sql_data['field_' . $account_entities['body']] = (strlen(
                        $mail_data['body_text']
                    ) ? $mail_data['body_text'] : strip_tags(
                        $mail_data['body'],
                        '<strong><big><b><i><u><s><strike><hr><div><br><p><ol><ul><li><blockquote><font><img><pre><table><td><th><tr><h1><h2><h3><h4><h5><h6><sub><sup>'
                    ));
                }
                if ($account_entities['attachments'] > 0) {
                    $sql_data['field_' . $account_entities['attachments']] = $this->insert_item_attachments(
                        $mail_data['attachments']
                    );
                };

                $sql_data['date_added'] = time();
                $sql_data['created_by'] = 0;
                $sql_data['parent_item_id'] = mail_entities_filters::get_parent_item_id(
                    $mail_data,
                    $account_entities['id'],
                    $account_entities['parent_item_id']
                );
                db_perform('app_entity_' . $current_entity_id, $sql_data);
                $item_id = db_insert_id();

                //insert choices values for fields with multiple values
                $choices_values->process($item_id);

                //autoupdate all field types
                fields_types::update_items_fields($current_entity_id, $item_id);

                //send nofitication
                items::send_new_item_nofitication($current_entity_id, $item_id);

                //subscribe
                $modules = new modules('mailing');
                $mailing = new mailing($current_entity_id, $item_id);
                $mailing->subscribe();

                if ($account_entities['auto_create'] == 1) {
                    //relate mail to item
                    $sql_data = [
                        'mail_groups_id' => $mail_data['groups_id'],
                        'entities_id' => $current_entity_id,
                        'items_id' => $item_id,
                        'from_email' => $mail_data['from_email'],
                    ];

                    db_perform('app_ext_mail_to_items', $sql_data);
                } else {
                    //delete email
                    db_delete_row('app_ext_mail', $mail_id);
                    $this->delete_mail_attachments($mail_data['attachments']);

                    mail_accounts::delete_mail_group_by_id($mail_data['groups_id']);
                }
            }
        }
    }

    function send_autoreply_to_all()
    {
        if ($this->mail_account['send_autoreply'] == 0) {
            return false;
        }

        foreach ($this->mail as $mail_data) {
            if (!$mail_data['is_new_group']) {
                continue;
            }

            //stop sending autoreply to exist mail accounts
            $check_query = db_query(
                "select id from app_ext_mail_accounts where login='{$mail_data['from_email']}' or email='{$mail_data['from_email']}' limit 1"
            );
            if ($check = db_fetch_array($check_query)) {
                continue;
            }

            $options = [
                'from' => $this->mail_account['login'],
                'from_name' => $this->mail_account['name'],
                'to' => $mail_data['from_email'],
                'to_name' => $mail_data['from_name'],
                'subject' => TEXT_EXT_EMAIL_SUBJECT_RE . ' ' . $mail_data['subject'],
                'body' => $this->mail_account['autoreply_msg'] . '<br/><br/>' . format_date_time(
                        $mail_data['date_added'],
                        CFG_MAIL_DATETIME_FORMAT
                    ) . ', ' . $mail_data['from_name'] . ' &lt;' . $mail_data['from_email'] . '&gt;:<br/><blockquote style="margin:0px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex;">' . (strlen(
                        $mail_data['body']
                    ) ? $mail_data['body'] : $mail_data['body_text']) . '</blockquote>',
            ];

            //print_rr($options);

            mail_accounts::send_mail($this->mail_account, $options);
        }
    }

    function send_autoreply($mail_data)
    {
        if ($this->mail_account['send_autoreply'] == 0) {
            return false;
        }

        $options = [
            'from' => $this->mail_account['login'],
            'from_name' => $this->mail_account['name'],
            'to' => $mail_data['from_email'],
            'to_name' => $mail_data['from_name'],
            'subject' => TEXT_EXT_EMAIL_SUBJECT_RE . ' ' . $mail_data['subject'],
            'body' => $this->mail_account['autoreply_msg'] . '<br/><br/>' . format_date_time(
                    $mail_data['date_added'],
                    CFG_MAIL_DATETIME_FORMAT
                ) . ', ' . $mail_data['from_name'] . ' &lt;' . $mail_data['from_email'] . '&gt;:<br/><blockquote style="margin:0px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex;">' . (strlen(
                    $mail_data['body']
                ) ? $mail_data['body'] : $mail_data['body_text']) . '</blockquote>',
        ];

        //print_rr($options);

        mail_accounts::send_mail($this->mail_account, $options);
    }

    function crop_subject($subject)
    {
        if ($this->mail_account['not_group_by_subject'] == 0) {
            $xtra = "|RE\[\d+\]|FW\[\d+\]|FYI\[\d+\]|RIF\[\d+\]|I\[\d+\]|FS\[\d+\]|VB\[\d+\]|RV\[\d+\]|ENC\[\d+\]|ODP\[\d+\]|PD\[\d+\]|YNT\[\d+\]|ILT\[\d+\]|SV\[\d+\]|VS\[\d+\]|VL\[\d+\]|AW\[\d+\]|WG\[\d+\]|ΑΠ\[\d+\]|ΣΧΕΤ\[\d+\]|ΠΡΘ\[\d+\]|תגובה\[\d+\]|הועבר\[\d+\]|主题|转发\[\d+\]|FWD\[\d+\]";
            $subject = preg_replace(
                "/([\[\(] *)?(RE?S?|FW" . $xtra . "|FYI|RIF|I|FS|VB|RV|ENC|ODP|PD|YNT|ILT|SV|VS|VL|AW|WG|ΑΠ|ΣΧΕΤ|ΠΡΘ|תגובה|הועבר|主题|转发|FWD?) *([-:;)\]][ :;\])-]*|$)|\]+ *$/im",
                '',
                $subject
            );
            return trim($subject);
        } else {
            return trim($subject);
        }
    }

    function get_mail_groups_id($subject_cropped, $from_email)
    {
        $mail_groups_query = db_query(
            "select mg.id from app_ext_mail_groups mg, app_ext_mail_groups_from mgf where mg.accounts_id='" . $this->mail_account['id'] . "' and mg.subject_cropped = '" . db_input(
                $subject_cropped
            ) . "' and mgf.mail_groups_id=mg.id and mgf.from_email='" . $from_email . "'"
        );
        if ($mail_groups = db_fetch_array($mail_groups_query) and $this->mail_account['not_group_by_subject'] == 0) {
            return ['id' => $mail_groups['id'], 'is_new' => false];
        } else {
            $data = [
                'accounts_id' => $this->mail_account['id'],
                'subject_cropped' => $subject_cropped,
            ];

            db_perform('app_ext_mail_groups', $data);

            $mail_groups_id = db_insert_id();

            $data = [
                'mail_groups_id' => $mail_groups_id,
                'from_email' => $from_email
            ];

            db_perform('app_ext_mail_groups_from', $data);

            return ['id' => $mail_groups_id, 'is_new' => true];
        }
    }

    function get_email_date($output)
    {
        if (empty($output->headers['date'])) {
            return time();
        } else {
            return strtotime($output->headers['date']);
        }
    }

    function get_headers($output, $key, $i)
    {
        if (empty($output->headers[$key])) {
            $key = str_replace('-', '_', $key);
            $this->mail[$i][$key . '_name'] = '';
            $this->mail[$i][$key . '_email'] = '';

            return false;
        }

        $key_name = [];
        $key_email = [];

        foreach (explode(',', $output->headers[$key]) as $to) {
            $to = trim($to);

            if (preg_match('/"([^"]+)" <([^>]+)>/', $to, $regs)) {
                $key_name[] = trim($regs[1]);
                $key_email[] = trim($regs[2]);
            } elseif (preg_match('/([^<]+)<([^>]+)>/', $to, $regs)) {
                $key_name[] = trim($regs[1]);
                $key_email[] = trim($regs[2]);
            } elseif (substr($to, 0, 1) == '<') {
                $key_name[] = substr($to, 1, -1);
                $key_email[] = substr($to, 1, -1);
            } else {
                $key_name[] = $to;
                $key_email[] = $to;
            }
        }

        $key = str_replace('-', '_', $key);
        $this->mail[$i][$key . '_name'] = db_prepare_input(implode(',', $key_name));
        $this->mail[$i][$key . '_email'] = db_prepare_input(implode(',', $key_email));
    }

    function text_decode($str)
    {
        $text = '';
        $charset = null;

        $text_array = imap_mime_header_decode($str);

        foreach ($text_array as $v) {
            $text .= rtrim($v->text, "\t");
            $charset = $v->charset;
        }

        if ($charset == 'default') {
            $charset = 'UTF-8';
        }

        return $this->mime_encode($text, '', $charset);
    }

    //Convert text to desired encoding..defaults to utf8
    function mime_encode($text, $parameters, $charset = null, $enc = 'utf-8')
    { //Thank in part to afterburner
        $encodings = ['UTF-8', 'WINDOWS-1251', 'ISO-8859-5', 'ISO-8859-1', 'KOI8-R'];

        if (is_array($parameters)) {
            foreach ($parameters as $v) {
                if ($v->attribute == 'charset') {
                    $charset = $v->value;
                }
            }
        }

        if (function_exists("iconv") and $text) {
            if ($charset) {
                return quoted_printable_decode(iconv($charset, $enc . '//IGNORE', $text));
            } elseif (function_exists("mb_detect_encoding")) {
                return quoted_printable_decode(iconv(mb_detect_encoding($text, $encodings), $enc, $text));
            }
        }

        return quoted_printable_decode(utf8_encode($text));
    }

    function create_part_array($struct)
    {
        $part_array = [];

        if (isset($struct->parts)) {
            if (sizeof($struct->parts) > 0) {    // There some sub parts
                foreach ($struct->parts as $count => $part) {
                    $this->add_part_to_array($part, ($count + 1), $part_array);
                }
            } else {    // Email does not have a seperate mime attachment for text
                $part_array[] = ['part_number' => '1', 'part_object' => $struct];
            }
        }
        return $part_array;
    }

    // Sub public function for create_part_array(). Only called by create_part_array() and itself.
    function add_part_to_array($obj, $partno, &$part_array)
    {
        $part_array[] = ['part_number' => $partno, 'part_object' => $obj];
        if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
            //print_r($obj);
            if (isset($obj->parts) and sizeof($obj->parts) > 0) {    // Check to see if the email has parts
                foreach ($obj->parts as $count => $part) {
                    // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                    if (sizeof($part->parts) > 0) {
                        foreach ($part->parts as $count2 => $part2) {
                            $this->add_part_to_array($part2, $partno . "." . ($count2 + 1), $part_array);
                        }
                    } else {    // Attached email does not have a seperate mime attachment for text
                        $part_array[] = ['part_number' => $partno . '.' . ($count + 1), 'part_object' => $obj];
                    }
                }
            } else {    // Not sure if this is possible
                $part_array[] = ['part_number' => $prefix . '.1', 'part_object' => $obj];
            }
        } else {    // If there are more sub-parts, expand them out.
            if (isset($obj->parts)) {
                if (sizeof($obj->parts) > 0) {
                    foreach ($obj->parts as $count => $p) {
                        $this->add_part_to_array($p, $partno . "." . ($count + 1), $part_array);
                    }
                }
            }
        }
    }

    function get_attachments($part_array)
    {
        reset($part_array);

        $attachments = [];

        foreach ($part_array as $value) {
            if ($value['part_object']->ifdparameters == '1' && $value['part_object']->dparameters[0]->value) {
                $attachments[] = [
                    'part_number' => $value['part_number'],
                    'encoding' => $value['part_object']->encoding,
                    'filename' => $value['part_object']->dparameters[0]->value
                ];
            }

            if (($value['part_object']->subtype == 'PNG' or $value['part_object']->subtype == 'JPEG' or $value['part_object']->subtype == 'GIF' or $value['part_object']->subtype == 'BMP') and $value['part_object']->ifdparameters == '0' and
                is_array($value['part_object']->parameters)) {
                //$attachments[] = array('part_number'=>$value['part_number'],'encoding'=>$value['part_object']->encoding,'filename'=>$value['part_object']->parameters[0]->value,'id'=>str_replace(array('<','>'),'',$value['part_object']->id));
                $attachments[] = [
                    'part_number' => $value['part_number'],
                    'encoding' => $value['part_object']->encoding,
                    'filename' => $value['part_object']->parameters[0]->value
                ];
            }
        }

        if (sizeof($attachments) > 0) {
            return $attachments;
        } else {
            return false;
        }
    }

    function delete_mail_attachments($attachments)
    {
        if (!strlen($attachments)) {
            return false;
        }

        foreach (explode(',', $attachments) as $filename) {
            //get filetime
            $filename_array = explode('_', $filename);
            $filetime = (int)$filename_array[0];

            //get foler
            $folder = date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime);

            $filename_encrypted = (CFG_ENCRYPT_FILE_NAME == 1 ? sha1($filename) : $filename);

            $file_path = DIR_WS_MAIL_ATTACHMENTS . $folder . '/' . $filename_encrypted;

            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
    }

    function insert_item_attachments($attachments)
    {
        if (!strlen($attachments)) {
            return false;
        }

        foreach (explode(',', $attachments) as $filename) {
            //get filetime
            $filename_array = explode('_', $filename);
            $filetime = (int)$filename_array[0];

            //get foler
            $folder = date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime);

            $filename_encrypted = (CFG_ENCRYPT_FILE_NAME == 1 ? sha1($filename) : $filename);

            $file_path = DIR_WS_MAIL_ATTACHMENTS . $folder . '/' . $filename_encrypted;

            if (is_file($file_path)) {
                if (!is_dir(DIR_WS_ATTACHMENTS . date('Y', $filetime))) {
                    mkdir(DIR_WS_ATTACHMENTS . date('Y', $filetime));
                }

                if (!is_dir(DIR_WS_ATTACHMENTS . date('Y', $filetime) . '/' . date('m', $filetime))) {
                    mkdir(DIR_WS_ATTACHMENTS . date('Y', $filetime) . '/' . date('m', $filetime));
                }

                if (!is_dir(
                    DIR_WS_ATTACHMENTS . date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime)
                )) {
                    mkdir(
                        DIR_WS_ATTACHMENTS . date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date(
                            'd',
                            $filetime
                        )
                    );
                }

                //echo $file_path . '<br>';
                copy($file_path, DIR_WS_ATTACHMENTS . $folder . '/' . $filename_encrypted);
            }
        }

        return $attachments;
    }

    function insert_attachments($mbox, $msg_number, $attachments_list)
    {
        $list = [];
        if (is_array($attachments_list)) {
            //print_rr($attachments_list);

            foreach ($attachments_list as $v) {
                $filename = time() . '_' . str_replace([" ", ","], "_", trim($this->text_decode($v['filename'])));

                //echo $filename . '<br>';

                $filename_encrypted = (CFG_ENCRYPT_FILE_NAME == 1 ? sha1($filename) : $filename);

                if (!is_dir(DIR_WS_MAIL_ATTACHMENTS . date('Y'))) {
                    mkdir(DIR_WS_MAIL_ATTACHMENTS . date('Y'));
                }

                if (!is_dir(DIR_WS_MAIL_ATTACHMENTS . date('Y') . '/' . date('m'))) {
                    mkdir(DIR_WS_MAIL_ATTACHMENTS . date('Y') . '/' . date('m'));
                }

                if (!is_dir(DIR_WS_MAIL_ATTACHMENTS . date('Y') . '/' . date('m') . '/' . date('d'))) {
                    mkdir(DIR_WS_MAIL_ATTACHMENTS . date('Y') . '/' . date('m') . '/' . date('d'));
                }

                $folder = date('Y') . '/' . date('m') . '/' . date('d');

                $file_contnt = $this->decode($v['encoding'], imap_fetchbody($mbox, $msg_number, $v['part_number']));

                file_put_contents(
                    DIR_WS_MAIL_ATTACHMENTS . $folder . '/' . $filename_encrypted,
                    $file_contnt,
                    FILE_TEXT | FILE_APPEND | LOCK_EX
                );

                $list[] = $filename;
            }
        }

        return implode(',', $list);
    }

    function decode($encoding, $text)
    {
        switch ($encoding) {
            case 1:
                $text = imap_8bit($text);
                break;
            case 2:
                $text = imap_binary($text);
                break;
            case 3:
                $text = imap_base64($text);
                break;
            case 4:
                $text = imap_qprint($text);
                break;
            case 5:
            default:
                $text = $text;
        }
        return $text;
    }

    function parse_output(&$obj, &$parts, $i)
    {
        $ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

        //echo $ctype . '<br>';
        //print_rr($obj);

        if (isset($obj->parts)) {
            for ($i = 0; $i < count($obj->parts); $i++) {
                $this->parse_output($obj->parts[$i], $parts, $i);
            }
        } else {
            switch ($ctype) {
                case 'text/plain':
                    if (!empty($obj->disposition) and $obj->disposition == 'attachment') {
                        $names = explode(';', $obj->headers["content-disposition"]);

                        $names = explode('=', $names[1]);
                        $aux['name'] = $names[1];
                        $aux['content-type'] = $obj->headers["content-type"];
                        $aux['part'] = $i;
                        $parts['attachments'][] = $aux;
                    } else {
                        $parts['text'][] = $obj->body;
                    }

                    break;
                case 'text/html':
                    if (!empty($obj->disposition) and $obj->disposition == 'attachment') {
                        $names = explode(';', $obj->headers["content-disposition"]);

                        $names = explode('=', $names[1]);
                        $aux['name'] = $names[1];
                        $aux['content-type'] = $obj->headers["content-type"];
                        $aux['part'] = $i;
                        $parts['attachments'][] = $aux;
                    } else {
                        $parts['html'][] = $obj->body;
                    }

                    break;

                default:
                    if (isset($obj->headers["content-disposition"])) {
                        $names = explode(';', $obj->headers["content-disposition"]);
                        $names = explode('=', $names[1]);
                        $aux['name'] = $names[1];
                    } else {
                        $aux['name'] = '';
                    }

                    $aux['content-type'] = $obj->headers["content-type"];
                    $aux['part'] = $i;
                    $parts['attachments'][] = $aux;
            }
        }
    }

    function parse_mime_decode_output(&$obj, &$parts)
    {
        if (!empty($obj->parts)) {
            for ($i = 0; $i < count($obj->parts); $i++) {
                $this->parse_output($obj->parts[$i], $parts, $i);
            }
        } else {
            $ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

            switch ($ctype) {
                case 'text/plain':
                    if (!empty($obj->disposition) and $obj->disposition == 'attachment') {
                        $parts['attachments'][] = $obj->body;
                    } else {
                        $parts['text'][] = $obj->body;
                    }
                    break;
                case 'text/html':
                    if (!empty($obj->disposition) and $obj->disposition == 'attachment') {
                        $parts['attachments'][] = $obj->body;
                    } else {
                        $parts['html'][] = $obj->body;
                    }
                    break;
                default:
                    $parts['attachments'][] = $obj->body;
            }
        }
    }

}
