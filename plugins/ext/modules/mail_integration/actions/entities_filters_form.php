<?php

$accounts_entities_query = db_query(
    "select me.*, ma.name as server_name,e.name as entities_name, e.parent_id from app_ext_mail_accounts_entities me left join app_ext_mail_accounts ma on me.accounts_id=ma.id left join app_entities e on me.entities_id=e.id where  me.id='" . _get::int(
        'account_entities_id'
    ) . "' order by id"
);
if (!$accounts_entities = db_fetch_array($accounts_entities_query)) {
    redirect_to('ext/mail_integration/entities');
}


$obj = [];

if (isset($_GET['id'])) {
    $obj = db_find('app_ext_mail_accounts_entities_filters', $_GET['id']);
} else {
    $obj = db_show_columns('app_ext_mail_accounts_entities_filters');
}