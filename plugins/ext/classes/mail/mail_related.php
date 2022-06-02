<?php

class mail_related
{

    public $has_access, $entities_id;

    function __construct($entities_id, $position)
    {
        global $app_user;

        $this->has_access = false;
        $this->entities_id = $entities_id;

        $accounts_entities_query = db_query(
            "select ae.* from app_ext_mail_accounts_entities ae where ae.entities_id='" . $this->entities_id . "' and ae.related_emails_position='" . $position . "' and ae.accounts_id in (select au.accounts_id from app_ext_mail_accounts_users au where au.users_id='" . $app_user['id'] . "')"
        );
        if ($accounts_entities = db_fetch_array($accounts_entities_query)) {
            $this->has_access = true;

            $this->accounts_entities = $accounts_entities;
        }
    }

    function render_list($items_id)
    {
        if (!$this->has_access) {
            return false;
        }


        $html = '
		<div class="portlet portlet-related-items">
			<div class="portlet-title">
				<div class="caption">        
          ' . TEXT_EXT_RELATED_EMAILS . '              
        </div>
        <div class="tools">
					<a href="javascript:;" class="collapse"></a>
				</div>
        
			</div>
			<div class="portlet-body">
         <table class="table ">';

        $related_contacts = [];

        $count_mails = 0;
        $related_mails_query = db_query(
            "select mg.id, mg.subject_cropped from app_ext_mail_to_items m2i left join app_ext_mail_groups mg on mg.id=m2i.mail_groups_id where m2i.entities_id='" . $this->entities_id . "' and m2i.items_id='" . $items_id . "'"
        );
        while ($related_mails = db_fetch_array($related_mails_query)) {
            $html .= '
					<tr>
						<td><a href="' . url_for(
                    'ext/mail/info',
                    'id=' . $related_mails['id']
                ) . '"><i class="fa fa-envelope-o" aria-hidden="true"></i> ' . htmlspecialchars(
                    $related_mails['subject_cropped']
                ) . '</a></td>
					</tr>';

            $count_mails++;

            if ($this->accounts_entities['bind_to_sender'] == 1) {
                $mail_info_query = db_query(
                    "select from_email, from_name from app_ext_mail where groups_id='" . $related_mails['id'] . "' order by id asc"
                );
                if ($mail_info = db_fetch_array($mail_info_query)) {
                    $count_query = db_query(
                        "select count(*) as total from app_ext_mail where from_email='" . $mail_info['from_email'] . "' and in_trash=0"
                    );
                    $count = db_fetch_array($count_query);

                    $related_contacts[$mail_info['from_email']] = [
                        'from_email' => $mail_info['from_email'],
                        'from_name' => $mail_info['from_name'],
                        'count_mails' => $count['total']
                    ];
                }
            }
        }

        if (count($related_contacts)) {
            $html .= '
						<tr>
							<td style="padding-top: 15px;">' . TEXT_EXT_RELATED_CONTACTS . '</td>
						</tr>';

            foreach ($related_contacts as $contact) {
                $html .= '
						<tr>
							<td><a href="' . url_for(
                        'ext/mail/accounts',
                        'search=' . $contact['from_email']
                    ) . '">' . $contact['from_name'] . ' (' . $contact['count_mails'] . ')</a></td>
						</tr>';
            }
        }

        $html .= '
					</table>
				</div>
    </div>
				';

        if (!$count_mails) {
            return '';
        }

        return $html;
    }

    static function link_item_to_mail($mail_data, $path)
    {
        $path_array = explode('-', $path);
        $entity_id = $path_array[0];
        $item_id = $path_array[1];

        $accounts_entities_query = db_query(
            "select ae.* from app_ext_mail_accounts_entities ae where ae.entities_id='" . $entity_id . "' and accounts_id ='" . $mail_data['accounts_id'] . "'"
        );
        if ($accounts_entities = db_fetch_array($accounts_entities_query)) {
            $sql_data = [
                'mail_groups_id' => $mail_data['groups_id'],
                'entities_id' => $entity_id,
                'items_id' => $item_id,
                'from_email' => $mail_data['from_email'],
            ];

            db_perform('app_ext_mail_to_items', $sql_data);
        }
    }

}
