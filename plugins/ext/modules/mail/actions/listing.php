<?php

//set filters
$app_mail_filters = [
    'folder' => $_POST['folder'],
    'accounts_id' => (isset($_POST['accounts_id']) ? $_POST['accounts_id'] : 0),
    'search' => $_POST['search'],
];

$count_accounts = (int)$_POST['count_accounts'];

$html_mobile = '<ul class="listing-mobile">';

$html = '
<div class="table-scrollable mail-listing">
	<table class="table table-striped table-bordered table-hover">		
		<thead>
		  ' . (in_array($app_mail_filters['folder'], ['trash', 'spam']) ? '<th></th>' : '') . '
			<th colspan="2" style="text-align: center">
                            ' . (in_array($app_mail_filters['folder'], ['inbox']
    ) ? '<a href="#" id="mail_fetch_all"><i class="fa fa-refresh" aria-hidden="true"></i></a> <div class="fa fa-spinner fa-spin mail-fetch-all-loading hidden"></div>' : '') . '
                        </th>			
			<th>' . TEXT_EXT_EMAIL_FROM . '</th>
			' . (($count_accounts > 2 or $app_mail_filters['folder'] == 'sent') ? '<th>' . TEXT_EXT_EMAIL_TO . '</th>' : '') . '										
			<th width="100%">' . TEXT_EXT_EMAIL_SUBJECT . '</th>
			<th>' . TEXT_DATE_ADDED . '</th>
                        ' . (!in_array($app_mail_filters['folder'], ['trash']
    ) ? '<th><input type="checkbox" id="select_all_mail" ></th>' : '') . '    
		</thead>		
		<tbody>
';

//apply filters
$where_sql = "and m.in_trash=0 and m.is_spam=0";
$where_sub_sql = "and m2.is_sent=0 and m2.is_spam=0 and m2.in_trash=0";
$where_count_sql = "and m3.in_trash=0 and m.is_spam=0";

switch ($app_mail_filters['folder']) {
    case 'trash':
        $where_sql = "and m.in_trash=1";
        $where_sub_sql = "and m2.in_trash=1";
        $where_count_sql = "and m3.in_trash=1";
        break;
    case 'sent':
        $where_sub_sql = "and m2.is_sent=1";
        break;
    case 'starred':
        $where_sub_sql = "and m2.is_star=1";
        break;
    case 'spam':
        $where_sql = "and m.in_trash=0 and m.is_spam=1";
        $where_sub_sql = "and m2.is_sent=0 and m.is_spam=1";
        $where_count_sql = "and m3.in_trash=0 and m.is_spam=1";
        break;
}

if ($app_mail_filters['accounts_id'] > 0) {
    $where_sql .= " and m.accounts_id='" . $app_mail_filters['accounts_id'] . "'";
}

if (strlen($app_mail_filters['search'])) {
    if (app_parse_search_string($app_mail_filters['search'], $search_keywords)) {
        if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
            $where_sub_sql .= " and (";
            for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i++) {
                switch ($search_keywords[$i]) {
                    case '(':
                    case ')':
                    case 'and':
                    case 'or':
                        $where_sub_sql .= " " . $search_keywords[$i] . " ";
                        break;
                    default:
                        $keyword = $search_keywords[$i];
                        $where_sub_sql .= " m2.from_name like '%" . db_input(
                                $keyword
                            ) . "%'  or m2.from_email like '%" . db_input(
                                $keyword
                            ) . "%'  or  m2.subject_cropped like '%" . db_input(
                                $keyword
                            ) . "%' or m2.body like '%" . db_input($keyword) . "%'  or m2.body_text like '%" . db_input(
                                $keyword
                            ) . "%'";
                        break;
                }
            }
            $where_sub_sql .= ")";
        }
    }
}

$listing_sql = "select m.*, ma.bg_color, (select count(*) from app_ext_mail m3 where m3.groups_id=m.groups_id {$where_count_sql}) as count_mails, (select count(*) from app_ext_mail m4 where m4.groups_id=m.groups_id and is_star=1) as has_star from app_ext_mail m left join app_ext_mail_accounts ma on ma.id=m.accounts_id where ma.is_active=1 and m.date_added = (select max(m2.date_added) from app_ext_mail m2 where m2.groups_id=m.groups_id {$where_sub_sql}) and m.accounts_id in (select accounts_id from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "') {$where_sql} group by m.groups_id order by m.date_added desc";

//echo $listing_sql;

$listing_split = new split_page($listing_sql, 'email_listing', 'query_num_rows', CFG_MAIL_ROWS_PER_PAGE);
$items_query = db_query($listing_split->sql_query);

if (!db_num_rows($items_query)) {
    $html .= '<tr><td colspan="10">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
    $html_mobile .= '<li>' . TEXT_NO_RECORDS_FOUND . '</li>';
}

while ($item = db_fetch_array($items_query)) {
    $css = ($item['is_new'] ? 'class="new-email"' : '');

    $last_item_query = db_query(
        "select body, body_text from app_ext_mail where groups_id='" . $item['groups_id'] . "' order by date_added desc limit 1"
    );
    if ($last_item = db_fetch_array($last_item_query)) {
        $body_short = mb_substr(
            strip_tags((strlen($last_item['body_text']) ? $last_item['body_text'] : $last_item['body'])),
            0,
            160
        );
    }

    $onClick = 'onClick="location.href=\'' . url_for('ext/mail/info', 'id=' . $item['groups_id']) . '\'"';

    $delete_action = '<i class="fa fa-trash-o mail-trash" data_mail_group_id="' . $item['groups_id'] . '" data_folder="' . $app_mail_filters['folder'] . '" title="' . TEXT_DELETE . '"></i>';
    $from_name = (strlen(
            $item['from_name']
        ) ? $item['from_name'] : $item['from_email']) . ($item['count_mails'] > 1 ? ' <span class="count-mails">' . $item['count_mails'] . '</span>' : '');
    $move_to_inbox = '<i class="fa fa-arrow-circle-left mail-move-inbox" data_mail_group_id="' . $item['groups_id'] . '" title="' . TEXT_EXT_MOVE_TO_INBOX . '"></i>';
    $star_action = ($item['has_star'] > 0 ? '<i class="fa fa-star mail-star mail-star-active" data_mail_group_id="' . $item['groups_id'] . '"></i>' : '<i class="fa fa-star mail-star" data_mail_id="' . $item['id'] . '" data_mail_group_id="' . $item['groups_id'] . '"></i>');

    if (is_mobile()) {
        $html_mobile .= '
				<li>
					<table style="width: 100%">
						<tr>
							<td valign="top" style="padding-top: 24px;">' . $star_action . '</td>
							<td ' . $onClick . ' width="100%" ' . $css . '>
								<div class="mobile-mail-from">' . $from_name . '</div>
								<div class="mobile-mail-subject"><i class="fa fa-angle-double-right"></i> ' . htmlspecialchars(
                $item['subject_cropped']
            ) . '</div>
								<div>' . mb_substr($body_short, 0, 60) . '</div>											
							</td>						
						</tr>	
						<tr>
							<td colspan="2" align="right">
								' . (in_array($app_mail_filters['folder'], ['trash', 'spam']
            ) ? $move_to_inbox : '') . '&nbsp;&nbsp;' . $delete_action . '&nbsp;&nbsp;' . date(
                CFG_MAIL_DATETIME_FORMAT,
                $item['date_added']
            ) . '
							</td>			
						</tr>				
					</table>
				</li>
				';
    } else {
        $to_email = (strstr($item['to_email'], ',') ? TEXT_EXT_RECIPIENTS . ': ' . (substr_count(
                    $item['to_email'],
                    ','
                ) + 1) : $item['to_email']);

        $html .= '
			<tr ' . $css . ' >
				' . (in_array($app_mail_filters['folder'], ['trash', 'spam']
            ) ? '<td>' . $move_to_inbox . '</td>' : '') . '
				<td>' . $delete_action . '</td>
				<td>' . $star_action . '</td>
				<td ' . $onClick . '>' . $from_name . '</td>
				' . (($count_accounts > 2 or $app_mail_filters['folder'] == 'sent') ? '<td ' . $onClick . '>' . render_bg_color_block(
                    $item['bg_color'],
                    $to_email
                ) . '</td>' : '') . '
				<td ' . $css . ' ' . $onClick . ' style="white-space:normal;"><a href="' . url_for(
                'ext/mail/info',
                'id=' . $item['groups_id']
            ) . '">' . htmlspecialchars($item['subject_cropped']) . '</a> <span class="body-short">' . $body_short . '</span></td>
				<td ' . $onClick . '>' . date(CFG_MAIL_DATETIME_FORMAT, $item['date_added']) . '</td>
                                ' . (!in_array($app_mail_filters['folder'], ['trash']
            ) ? '<th style="text-align:center"><input class="mail_item_checkbox" type="checkbox" name="mail[]" value="' . $item['groups_id'] . '"></th>' : '') . '        
			</tr>
			';
    }
}

$html .= '
		</tbody>
	</table>
</div>';

$html_mobile .= '</ul>';

if (is_mobile()) {
    echo $html_mobile;
} else {
    echo $html;
}

?>


<table width="100%">
    <tr>
        <td><?php
            echo $listing_split->display_count() ?></td>
        <td align="right"><?php
            echo $listing_split->display_links() ?></td>
    </tr>
</table>

<script>

    function delete_selected_mail() {
        if ($('.mail_item_checkbox:checked').length == 0) {
            alert('<?php echo TEXT_PLEASE_SELECT_ITEMS ?>')
            return false;
        }

        let selected_items = [];
        $('.mail_item_checkbox:checked').each(function () {
            selected_items.push($(this).val())
        })


        $.ajax({
            method: 'POST',
            url: '<?php echo url_for('ext/mail/accounts', 'action=delete_mails') ?>',
            data: {selected_items: selected_items}
        }).done(function (msg) {
            load_items_listing('email_listing',<?php echo (int)$_POST['page']?>, '');
        })
    }

    $(function () {

//select all mail
        $('#select_all_mail').change(function () {
            select_all_by_classname('select_all_mail', 'mail_item_checkbox')

        })

//delete email
        $('.mail-trash').click(function () {
            $.ajax({
                method: 'POST',
                url: '<?php echo url_for('ext/mail/accounts', 'action=delete_mail') ?>',
                data: {mail_group_id: $(this).attr('data_mail_group_id'), folder: $(this).attr('data_folder')}
            }).done(function (msg) {
                load_items_listing('email_listing',<?php echo (int)$_POST['page']?>, '');
            })
        })

//move to inbox
        $('.mail-move-inbox').click(function () {
            $.ajax({
                method: 'POST',
                url: '<?php echo url_for('ext/mail/accounts', 'action=move_to_inbox') ?>',
                data: {mail_group_id: $(this).attr('data_mail_group_id')}
            }).done(function (msg) {
                load_items_listing('email_listing',<?php echo (int)$_POST['page']?>, '');
            })
        })

//star email	
        $('.mail-star').click(function () {
            if ($(this).hasClass('mail-star-active')) {
                $(this).removeClass('mail-star-active');

                $.ajax({
                    method: 'POST',
                    url: '<?php echo url_for('ext/mail/accounts', 'action=unset_star') ?>',
                    data: {mail_group_id: $(this).attr('data_mail_group_id')}
                })
            } else {
                $(this).addClass('mail-star-active');

                $.ajax({
                    method: 'POST',
                    url: '<?php echo url_for('ext/mail/accounts', 'action=set_star') ?>',
                    data: {mail_id: $(this).attr('data_mail_id')}
                })
            }
        })

        $("#mail_fetch_all").click(function () {
            $(this).addClass('hidden');
            $('.mail-fetch-all-loading').removeClass('hidden')

            $.ajax({
                method: 'POST',
                url: '<?php echo url_for('ext/mail/accounts', 'action=fetch_all') ?>',
                data: {mail_id: $(this).attr('data_mail_id')}
            }).done(function (response) {
                if (response != 'success') {
                    alert(response)
                }

                load_items_listing('email_listing', 1, '');

            })
        })
    })
</script>


