<?php

$mail_groups_id = _post::int('mail_groups_id');

$mail_query = db_query(
    "select id, from_email,to_email, is_sent from app_ext_mail where groups_id='" . $mail_groups_id . "' order by id limit 1"
);
$mail = db_fetch_array($mail_query);

$from_email = '';

if ($mail['is_sent'] == 1) {
    if (!strstr($mail['to_email'], ',')) {
        $from_email = $mail['to_email'];
    }
} else {
    $from_email = $mail['from_email'];
}

$sql_data = [
    'mail_groups_id' => $mail_groups_id,
    'entities_id' => $current_entity_id,
    'items_id' => $item_id,
    'from_email' => $from_email,
];

db_perform('app_ext_mail_to_items', $sql_data);

redirect_to('ext/mail/info', 'id=' . _post::int('mail_groups_id'));