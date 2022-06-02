<?php

$mail_groups_id = _get::int('mail_groups_id');

$accounts_query = db_query(
    "select mae.* from app_ext_mail_accounts_entities mae, app_ext_mail_groups mg where mae.accounts_id=mg.accounts_id and mg.id='" . $mail_groups_id . "' and mae.entities_id='" . $current_entity_id . "'"
);
if ($accounts = db_fetch_array($accounts_query)) {
    //print_r($accounts);

    $actions_fields_query = db_query(
        "select af.id, af.fields_id, af.value, f.name, f.type as field_type from app_ext_mail_accounts_entities_fields af, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=af.fields_id and af.account_entities_id='" . $accounts['id'] . "' order by t.sort_order, t.name, f.sort_order, f.name"
    );
    while ($actions_fields = db_fetch_array($actions_fields_query)) {
        $obj['field_' . $actions_fields['fields_id']] = $actions_fields['value'];
    }

    $mail_query = db_query(
        "select * from app_ext_mail where groups_id='" . $mail_groups_id . "' and is_sent=0 order by id desc limit 1"
    );
    if ($mail = db_fetch_array($mail_query)) {
        foreach (['from_name', 'from_email', 'subject', 'body', 'attachments'] as $key) {
            if ($accounts[$key]) {
                switch (true) {
                    case $key == 'subject':
                        $value = $mail['subject_cropped'];
                        break;

                    case $key == 'body':
                        $value = (strlen($mail['body']) ? $mail['body'] : $mail['body_text']);
                        break;

                    case $key == 'attachments':
                        $value = '';

                        if (strlen($mail['attachments'])) {
                            $verifyToken = md5($app_user['id'] . time());
                            $item_attachments = [];

                            foreach (explode(',', $mail['attachments']) as $attachment) {
                                $attachment_info = mail_info::parse_attachment_filename($attachment);

                                if (is_file($attachment_info['file_path'])) {
                                    $filename = substr($attachment, strpos($attachment, '_') + 1);

                                    $file = attachments::prepare_filename($filename);

                                    if (copy(
                                        $attachment_info['file_path'],
                                        DIR_FS_ATTACHMENTS . $file['folder'] . '/' . $file['file']
                                    )) {
                                        //print_rr($file);
                                        //echo $attachment_info['file_path'];

                                        $item_attachments[] = $file['name'];

                                        //add attachments to tmp table
                                        $sql_data = [
                                            'form_token' => $verifyToken,
                                            'filename' => $file['name'],
                                            'date_added' => date('Y-m-d'),
                                            'container' => $accounts[$key]
                                        ];
                                        db_perform('app_attachments', $sql_data);

                                        //add file to queue                                        
                                        $file_storage = new file_storage();
                                        $file_storage->add_to_queue($accounts[$key], $file['name']);
                                    }
                                }
                            }

                            $value = implode(',', $item_attachments);
                        }

                        break;
                    default:
                        $value = $mail[$key];
                        break;
                }

                $obj['field_' . $accounts[$key]] = $value;
            }
        }
    }
}