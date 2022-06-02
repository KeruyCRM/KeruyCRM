<?php

if (!mail_accounts::user_has_access()) {
    redirect_to('dashboard/access_forbidden');
}

if (!app_session_is_registered('app_mail_filters')) {
    $app_mail_filters = [
        'folder' => 'inbox',
        'accounts_id' => 0,
        'search' => '',
    ];
    app_session_register('app_mail_filters');
}

if (isset($_GET['search'])) {
    $app_mail_filters['search'] = db_prepare_input($_GET['search']);
}


switch ($app_module_action) {
    case 'fetch_all':
        mail_fetcher::fetch_all();
        echo 'success';
        exit;
        break;
    case 'search_contacts':
        $response = '';

        $results = [];

        if (strlen($_GET['q']) > 1) {
            $contacts_query = db_query(
                "select * from app_ext_mail_contacts where name like '%" . db_input(
                    $_GET['q']
                ) . "%' or email like '%" . db_input(
                    $_GET['q']
                ) . "%' and accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') limit 10"
            );
            while ($contacts = db_fetch_array($contacts_query)) {
                if (strlen($contacts['name'])) {
                    $text = $contacts['name'] . ' <' . $contacts['email'] . '>';
                } else {
                    $text = $contacts['email'];
                }

                $results[] = ['id' => $text, 'text' => $text];
            }
        }

        $response = ['results' => $results];

        echo json_encode($response);

        exit();

        break;
    case 'set_folder':
        $app_mail_filters['folder'] = $_GET['folder'];
        redirect_to('ext/mail/accounts');
        break;
    case 'empty_trash':
        //delete attachments
        $mail_query = db_query(
            "select attachments from app_ext_mail where in_trash=1 and length(attachments)>0 " . ($app_mail_filters['accounts_id'] > 0 ? " and accounts_id='" . $app_mail_filters['accounts_id'] . "'" : '')
        );
        while ($mail = db_fetch_array($mail_query)) {
            foreach (explode(',', $mail['attachments']) as $filename) {
                $file = mail_info::parse_attachment_filename($filename);

                if (is_file($file['file_path'])) {
                    unlink($file['file_path']);
                }
            }
        }

        //delete rows
        db_query(
            "delete from app_ext_mail where in_trash=1 " . ($app_mail_filters['accounts_id'] > 0 ? " and accounts_id='" . $app_mail_filters['accounts_id'] . "'" : '')
        );

        //reset mail groups
        $groups_list = [];
        $groups_query = db_query(
            "select mg.id from app_ext_mail_groups mg where (select count(*) from app_ext_mail m where m.groups_id=mg.id)=0"
        );
        while ($groups = db_fetch_array($groups_query)) {
            $groups_list[] = $groups['id'];
        }

        //print_r($groups_list);
        //exit();

        if (count($groups_list)) {
            db_query("delete from app_ext_mail_groups where id in (" . implode(',', $groups_list) . ")");
            db_query(
                "delete from app_ext_mail_groups_from where mail_groups_id in (" . implode(',', $groups_list) . ")"
            );
            db_query("delete from app_ext_mail_to_items where mail_groups_id in (" . implode(',', $groups_list) . ")");
        }

        redirect_to('ext/mail/accounts');
        break;
    case 'attachments_upload':

        if (strlen($_FILES['Filedata']['tmp_name'])) {
            $file = mail_info::prepare_attachment_filename($_FILES['Filedata']['name']);

            if (move_uploaded_file(
                $_FILES['Filedata']['tmp_name'],
                DIR_WS_MAIL_ATTACHMENTS . $file['folder'] . '/' . $file['file']
            )) {
                //add attachments to tmp table
                $sql_data = [
                    'form_token' => $_POST['token'],
                    'filename' => $file['name'],
                    'date_added' => date('Y-m-d'),
                    'container' => 0
                ];
                db_perform('app_attachments', $sql_data);
            }
        }
        exit();
        break;

    case 'attachments_preview':

        echo mail_info::render_attachments_preview($_GET['token']);

        exit();
        break;
    case 'attachment_delete':
        $attachments_query = db_query(
            "select * from app_attachments where id='" . db_input($_POST['id']) . "' and container=0"
        );
        if ($attachments = db_fetch_array($attachments_query)) {
            $file = mail_info::parse_attachment_filename($attachments['filename']);

            if (is_file($file['file_path'])) {
                unlink($file['file_path']);
            }

            db_delete_row('app_attachments', $attachments['id']);
        }
        break;
    case 'move_to_inbox':
        db_query("update app_ext_mail set in_trash=0, is_spam=0 where groups_id='" . _post::int('mail_group_id') . "'");
        exit();
        break;
    case 'delete_mails':

        if (isset($_POST['selected_items']) and is_array($_POST['selected_items'])) {
            db_query(
                "update app_ext_mail set in_trash=1 where groups_id in (" . implode(',', $_POST['selected_items']) . ")"
            );
        }

        exit();

        break;
    case 'delete_mail':
        $mail_group_id = _post::int('mail_group_id');
        $folder = $_POST['folder'];

        if ($folder != 'trash') {
            db_query("update app_ext_mail set in_trash=1 where groups_id='" . $mail_group_id . "'");
        } else {
            mail_info::delete_by_group_id($mail_group_id);
        }

        exit();

        break;
    case 'set_star':
        db_query("update app_ext_mail set is_star=1 where id='" . _post::int('mail_id') . "'");
        exit();
        break;
    case 'unset_star':
        db_query("update app_ext_mail set is_star=0 where groups_id='" . _post::int('mail_group_id') . "'");
        exit();
        break;

    case 'inbox_count':
        echo mail_accounts::inbox_count();
        exit();
        break;

    case 'update_notifications':

        $poup_items_limit = 10;

        $items_html = '';

        $items_display_count = 0;
        $items_query = db_query(
            "select *, count(*) as count_mails from app_ext_mail m left join app_ext_mail_accounts ma on m.accounts_id=ma.id where m.is_new=1 and ma.is_active=1 and m.accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') group by groups_id order by date_added desc limit " . $poup_items_limit
        );
        while ($items = db_fetch_array($items_query)) {
            //$path_info = items::get_path_info($items['entities_id'],$items['items_id']);

            $items_html .= '
          <li>
  					<a href="' . url_for('ext/mail/info', 'id=' . $items['groups_id']) . '">' . $items['subject'] . '<br> 
  					<span class="parent-name">' . (strlen(
                    $items['from_name']
                ) ? $items['from_name'] : $items['from_email']) . ' ' . ($items['count_mails'] > 1 ? $items['count_mails'] : '') . '</span></a>
  				</li>
        ';

            $items_display_count++;
        }

        $items_count = mail_accounts::inbox_count();

        if ($items_count == 0) {
            $items_html .= '					
          <li>
  					<a onClick="return false;">' . TEXT_NO_RECORDS_FOUND . '</a>
  				</li>
        ';
        }

        $dropdown_menu_height = ($items_display_count > 0 ? ($items_display_count * 77) : 50);

        $external_html = '';
        if ($items_count > 0) {
            $external_html = '
          <li class="external">
						<a href="' . url_for('ext/mail/accounts') . '">' . sprintf(
                    TEXT_EXT_DISPLAY_NUMBER_OF_MAIL_INBOX,
                    $items_display_count
                ) . '</a>
					</li>
        ';
        }

        $badge_html = ($items_count > 0 ? '<span class="badge badge-success">' . $items_count . '</span>' : '');

        $html = '
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				  <i class="fa fa-envelope"></i>
				  ' . $badge_html . '
				</a>
				<ul class="dropdown-menu extended tasks">
					<li style="cursor:pointer" onClick="location.href=\'' . url_for(
                'ext/mail/accounts',
                'action=set_folder&folder=inbox'
            ) . '\'">
						<p>' . TEXT_EXT_INBOX . ' (' . $items_count . ')</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
							' . $items_html . '
              ' . $external_html . '  
						</ul>
					</li>
          
				</ul>
				';

        if ($items_count > 0) {
            $html .= '        		
				<script>
					$(".mail-menu-inbox-count").removeClass("hidden").html("' . $items_count . '");              		
	      </script>              		
	      ';
        }

        echo $html;

        exit();

        break;
}		