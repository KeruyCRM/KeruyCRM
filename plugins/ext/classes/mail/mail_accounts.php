<?php

class mail_accounts
{

    static function user_has_access()
    {
        global $app_user;

        $check_query = db_query(
            "select mau.accounts_id from app_ext_mail_accounts_users mau left join app_ext_mail_accounts ma on ma.id=mau.accounts_id where is_active=1 and users_id='" . $app_user['id'] . "' limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            return true;
        } else {
            return false;
        }
    }

    static function send_mail($account, $options)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->CharSet = "UTF-8";
            $mail->setLanguage(APP_LANGUAGE_SHORT_CODE);

            if ($account['use_smtp'] == 1) {
                $mail->isSMTP();                          // Set mailer to use SMTP
                $mail->Host = $account['smtp_server'];      // Specify main and backup server
                $mail->Port = $account['smtp_port'];

                if (strlen($account['smtp_login']) > 0 or strlen($account['smtp_password'])) {
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = $account['smtp_login'];               // SMTP username
                    $mail->Password = $account['smtp_password'];            // SMTP password
                } else {
                    $mail->SMTPAuth = false;
                }

                //set encryption
                switch ($account['smtp_encryption']) {
                    case 'ssl':
                    case 'tls':
                        $mail->SMTPSecure = $account['smtp_encryption'];
                        break;
                    default:
                        $mail->SMTPAutoTLS = false;
                        $mail->SMTPSecure = false;
                        break;
                }

                //set debug mode                
                if (CFG_EMAIL_SMTP_DEBUG) {
                    $mail->SMTPDebug = 3;
                    $mail->Debugoutput = 'app_smtp_error_log';
                }
            }


            $mail->setFrom($options['from'], $options['from_name'], false);

            $mail->addAddress($options['to'], $options['to_name']);  // Add a recipient

            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $filename => $name) {
                    if (is_file($filename)) {
                        $mail->addAttachment($filename, $name);
                    }
                }
            }

            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $options['subject'];
            $mail->Body = $options['body'];

            $h2t = new html2text($options['body']);
            $mail->AltBody = $h2t->get_text();

            $mail->send();
        } catch (Exception $e) {
            return ['status' => 'error', 'text' => $mail->ErrorInfo];
        }

        return ['status' => 'success'];
    }

    static function get_choices()
    {
        $choices = [];
        $accounts_query = db_query("select id, name from app_ext_mail_accounts order by id");
        while ($accounts = db_fetch_array($accounts_query)) {
            $choices[$accounts['id']] = $accounts['name'];
        }

        return $choices;
    }

    static function get_default()
    {
        $accounts_query = db_query("select id from app_ext_mail_accounts where is_default=1");
        if ($accounts = db_fetch_array($accounts_query)) {
            return $accounts['id'];
        } else {
            return 0;
        }
    }

    static function get_choices_by_user($choices_type = 'full', $add_empyt = false, $empty_text = '')
    {
        global $app_user;

        $choices = [];


        $accounts_query = db_query(
            "select ma.* from app_ext_mail_accounts ma, app_ext_mail_accounts_users mau where ma.is_active=1 and ma.id=mau.accounts_id and mau.users_id='" . $app_user['id'] . "'"
        );

        if ($add_empyt and db_num_rows($accounts_query) > 1) {
            $choices[''] = $empty_text;
        }

        while ($accounts = db_fetch_array($accounts_query)) {
            $accounts['login'] = (strlen($accounts['email']) ? $accounts['email'] : $accounts['login']);

            switch ($choices_type) {
                case 'full':
                    $choices[$accounts['id']] = $accounts['name'] . ' <' . $accounts['login'] . '>';
                    break;
                case 'name':
                    $choices[$accounts['id']] = $accounts['name'];
                    break;
                case 'email':
                    $choices[$accounts['id']] = $accounts['login'];
                    break;
            }
        }

        return $choices;
    }

    static function inbox_count()
    {
        global $app_user;

        $items_query = db_query(
            "select m.id from app_ext_mail m left join app_ext_mail_accounts ma on m.accounts_id=ma.id where m.is_new=1 and m.in_trash=0 and ma.is_active=1 and m.accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') group by groups_id"
        );

        return db_num_rows($items_query);
    }

    static function spam_count()
    {
        global $app_user;

        $items_query = db_query(
            "select id from app_ext_mail where  in_trash=0 and is_spam=1 and accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') group by groups_id"
        );

        return db_num_rows($items_query);
    }

    static function render_menu_item($menu)
    {
        if (CFG_MAIL_INTEGRATION == 1) {
            if (CFG_MAIL_DISPLAY_IN_MENU == 1 and mail_accounts_users::has_access()) {
                $count = self::inbox_count();
                $menu[] = [
                    'title' => TEXT_EXT_INBOX,
                    'url' => url_for('ext/mail/accounts', 'action=set_folder&folder=inbox'),
                    'class' => 'fa-envelope',
                    'badge' => 'badge-info mail-menu-inbox-count ' . ($count == 0 ? 'hidden' : ''),
                    'badge_content' => $count
                ];
            }
        }

        return $menu;
    }

    static function render_dropdown_notification()
    {
        $html = '';

        if (CFG_MAIL_INTEGRATION == 1) {
            if (!mail_accounts_users::has_access()) {
                return '';
            }

            if (!self::user_has_access()) {
                return '';
            }

            if (CFG_MAIL_DISPLAY_IN_HEADER == 1) {
                $html = '
	        <li class="dropdown hot-reports" id="mail_account_notification">
	          ' . '
	        </li>
			
	        <script>
	          function mail_accounts_notification_dropdown()
	          {
	            $("#mail_account_notification").load("' . url_for("ext/mail/accounts", "action=update_notifications") . '",function(){
	                $(\'[data-hover="dropdown"]\').dropdownHover();
	            		app_handle_scrollers();
	              })
	          }
			
	          $(function(){
	             setInterval(function(){
	              mail_accounts_notification_dropdown()
	             },60000);
                     
                    mail_accounts_notification_dropdown();  		
	          });	
	            		
	          
			
	        </script>
	      ';
            } elseif (CFG_MAIL_DISPLAY_IN_MENU == 1) {
                $html = '
					<script>
	          function mail_accounts_notification_count()
	          {	            	            	
	            $.ajax({
	            		url: "' . url_for("ext/mail/accounts", "action=inbox_count") . '"
							}).done(function(msg){
	            	 if(parseInt(msg)>0)
	            	 {	            				
	            			$(".mail-menu-inbox-count").removeClass("hidden").html(msg);
								 }
							})
	          }
			
	          $(function(){
	             setInterval(function(){
	              mail_accounts_notification_count()
	             },60000);
	          });
	            				
	        </script>
						';
            }
        }

        return $html;
    }

    public static function get_entities_fields_choices($entity_id)
    {
        $available_types = [
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_boolean',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_input_numeric',
            'fieldtype_input',
            'fieldtype_input_email',
            'fieldtype_input_url',
            'fieldtype_input_masked',
            'fieldtype_textarea',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_input_masked',
            'fieldtype_entity',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_grouped_users',
            'fieldtype_progress',
            'fieldtype_todo_list',
        ];

        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (\"" . implode(
                '","',
                $available_types
            ) . "\")  and f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }

    static function get_auto_create_choices()
    {
        $choices = [];
        $choices[0] = TEXT_NO;
        $choices[1] = TEXT_YES;
        $choices[2] = TEXT_EXT_YES_AND_DELETE_EMAIL;

        return $choices;
    }

    static function get_auto_create_choices_name($k)
    {
        $choices = self::get_auto_create_choices();

        return (isset($choices[$k]) ? $choices[$k] : '');
    }

    static function get_folders_choices()
    {
        $count_spam = self::spam_count();

        $choices = [];
        $choices['inbox'] = TEXT_EXT_INBOX;
        $choices['starred'] = TEXT_EXT_STARRED;
        $choices['spam'] = TEXT_EXT_SPAM . ($count_spam ? ' (' . $count_spam . ')' : '');
        $choices['sent'] = TEXT_EXT_SENT;
        $choices['trash'] = TEXT_EXT_TRASH;

        return $choices;
    }

    static function delete_mail_group_by_id($groups_id)
    {
        //delete mail group if there is no mail with this group
        $check_query = db_query("select id from app_ext_mail where groups_id='" . $groups_id . "'");
        if (!$check = db_fetch_array($check_query)) {
            db_delete_row('app_ext_mail_groups', $groups_id);
            db_delete_row('app_ext_mail_groups_from', $groups_id, 'mail_groups_id');
            db_delete_row('app_ext_mail_to_items', $groups_id, 'mail_groups_id');
        }
    }

}
