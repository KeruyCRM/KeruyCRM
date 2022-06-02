<?php

switch ($app_module_action) {
    case 'remove_related_items';
        if (isset($_POST['items'])) {
            foreach ($_POST['items'] as $items_id) {
                db_query(
                    "delete from app_ext_mail_to_items where mail_groups_id='" . _get::int(
                        'mail_groups_id'
                    ) . "' and entities_id='" . _get::int('entities_id') . "' and items_id='" . $items_id . "'"
                );
            }
        }

        redirect_to('ext/mail/info', 'id=' . _get::int('mail_groups_id'));
        break;

    case 'add_related_item':

        if (isset($_POST['items'])) {
            foreach ($_POST['items'] as $items_id) {
                $check_query = db_query(
                    "select id from app_ext_mail_to_items where mail_groups_id='" . _get::int(
                        'mail_groups_id'
                    ) . "' and entities_id='" . _get::int('entities_id') . "' and items_id='" . $items_id . "'"
                );
                if (!$check = db_fetch_array($check_query)) {
                    $from_email = '';
                    $from_query = db_query(
                        "select from_email from app_ext_mail_groups_from where mail_groups_id='" . _get::int(
                            'mail_groups_id'
                        ) . "'"
                    );
                    if ($from = db_fetch_array($from_query)) {
                        $from_email = $from['from_email'];
                    }

                    $sql_data = [
                        'mail_groups_id' => _get::int('mail_groups_id'),
                        'entities_id' => _get::int('entities_id'),
                        'items_id' => $items_id,
                        'from_email' => $from_email,
                    ];

                    db_perform('app_ext_mail_to_items', $sql_data);
                }
            }
        }

        redirect_to('ext/mail/info', 'id=' . _get::int('mail_groups_id'));

        break;
}