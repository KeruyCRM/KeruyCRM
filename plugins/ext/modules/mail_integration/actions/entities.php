<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'accounts_id' => $_POST['accounts_id'],
            'entities_id' => $_POST['entities_id'],
            'parent_item_id' => isset($_POST['parent_item_id']) ? $_POST['parent_item_id'] : 0,
            'from_name' => $_POST['from_name'],
            'from_email' => $_POST['from_email'],
            'subject' => $_POST['subject'],
            'body' => $_POST['body'],
            'attachments' => $_POST['attachments'],
            'bind_to_sender' => (isset($_POST['bind_to_sender']) ? 1 : 0),
            'auto_create' => $_POST['auto_create'],
            'title' => $_POST['title'],
            'sort_order' => $_POST['sort_order'],
            'hide_buttons' => (isset($_POST['hide_buttons']) ? implode(',', $_POST['hide_buttons']) : ''),
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'fields_in_popup' => (isset($_POST['fields_in_popup']) ? implode(',', $_POST['fields_in_popup']) : ''),
            'related_emails_position' => $_POST['related_emails_position'],
        ];

        $report_info = mail_related_items::get_report_info($sql_data['entities_id']);
        db_query(
            "update app_reports set fields_in_listing='" . $sql_data['fields_in_listing'] . "' where id='" . $report_info['id'] . "'"
        );

        if (isset($_GET['id'])) {
            $mail_accounts_entities = db_find('app_ext_mail_accounts_entities', $_GET['id']);

            //check entity and if it's changed remove process action
            if ($mail_accounts_entities['entities_id'] != $_POST['entities_id']) {
                db_query(
                    "delete from app_ext_mail_accounts_entities_fields where account_entities_id='" . db_input(
                        $_GET['id']
                    ) . "'"
                );
                db_query(
                    "delete from app_ext_mail_accounts_entities_filters where account_entities_id='" . db_input(
                        $_GET['id']
                    ) . "'"
                );
            }

            db_perform('app_ext_mail_accounts_entities', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_mail_accounts_entities', $sql_data);
        }

        redirect_to('ext/mail_integration/entities');

        break;
    case 'delete':

        db_delete_row('app_ext_mail_accounts_entities', $_GET['id']);
        db_query(
            "delete from app_ext_mail_accounts_entities_fields where account_entities_id='" . db_input(
                $_GET['id']
            ) . "'"
        );
        db_query(
            "delete from app_ext_mail_accounts_entities_filters where account_entities_id='" . db_input(
                $_GET['id']
            ) . "'"
        );

        redirect_to('ext/mail_integration/entities');
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_mail_accounts_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_mail_accounts_entities');
        }

        $html = '';


        $choices = [];
        $choices[] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_input','fieldtype_input_email','fieldtype_textarea','fieldtype_textarea_wysiwyg') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_name">' . TEXT_EXT_EMAIL_FROM . '</label>
            <div class="col-md-8">
          	   ' . select_tag('from_name', $choices, $obj['from_name'], ['class' => 'form-control input-large']) . '               
            </div>
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_email">' . TEXT_EMAIL . '</label>
            <div class="col-md-8">
          	   ' . select_tag('from_email', $choices, $obj['from_email'], ['class' => 'form-control input-large']) . '
            </div>
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="subject">' . TEXT_EXT_EMAIL_SUBJECT . '</label>
            <div class="col-md-8">
          	   ' . select_tag('subject', $choices, $obj['subject'], ['class' => 'form-control input-large']) . '
            </div>
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="body">' . TEXT_EXT_MAIL_BODY . '</label>
            <div class="col-md-8">
          	   ' . select_tag('body', $choices, $obj['body'], ['class' => 'form-control input-large']) . '
            </div>
          </div>
        ';

        $choices = [];
        $choices[] = '';
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_attachments') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="attachments">' . TEXT_ATTACHMENTS . '</label>
            <div class="col-md-8">
          	   ' . select_tag('attachments', $choices, $obj['attachments'], ['class' => 'form-control input-large']) . '
            </div>
          </div>
        ';


        echo $html;

        exit();
        break;

    case 'get_entities_fields_settings':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_mail_accounts_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_mail_accounts_entities');
        }

        $html = '';


        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_name">' . TEXT_TITLE . '</label>
            <div class="col-md-8">
          	   ' . input_tag('title', $obj['title'], ['class' => 'form-control input-large']) . '
          	   ' . tooltip_text(TEXT_DEFAULT . ': ' . $entities_info['name']) . '		
            </div>
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_name">' . TEXT_FIELDS_IN_LISTING . '</label>
            <div class="col-md-8">
          	   ' . select_tag(
                'fields_in_listing[]',
                fields::get_fields_in_listing_choices($entities_id, true),
                $obj['fields_in_listing'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'multiple' => 'multiple',
                    'chosen_order' => $obj['fields_in_listing']
                ]
            ) . '
            </div>
          </div>
        ';

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_name">' . TEXT_FIELDS_IN_POPUP . '</label>
            <div class="col-md-8">
          	   ' . select_tag(
                'fields_in_popup[]',
                fields::get_fields_in_popup_choices($entities_id, true),
                $obj['fields_in_popup'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'multiple' => 'multiple',
                    'chosen_order' => $obj['fields_in_popup']
                ]
            ) . '
            </div>
          </div>
        ';

        echo $html;

        exit();
        break;

    case 'get_entities_parent_item':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        if ($entities_info['parent_id'] == 0) {
            exit();
        }

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_mail_accounts_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_mail_accounts_entities');
        }

        $choices = [];

        //build query
        $listing_sql = "select e.* from app_entity_" . $entities_info['parent_id'] . " e ";
        $items_query = db_query($listing_sql);

        $choices = [];

        $choices[''] = TEXT_NONE;

        while ($item = db_fetch_array($items_query)) {
            $path_info = items::get_path_info($entities_info['parent_id'], $item['id']);

            //print_r($path_info);

            $parent_name = '';
            if (strlen($path_info['parent_name']) > 0) {
                $parent_name = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ';
            }

            $choices[$item['id']] = $parent_name . items::get_heading_field($entities_info['parent_id'], $item['id']);
        }

        $html = '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="from_name">' . TEXT_PARENT . '</label>
            <div class="col-md-8">
          	   ' . select_tag(
                'parent_item_id',
                $choices,
                $obj['parent_item_id'],
                ['class' => 'form-control input-large chosen-select']
            ) . '          	   
            </div>
          </div>
        ';


        echo $html;

        exit();
        break;
}