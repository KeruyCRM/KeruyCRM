<?php

class mail_accounts_users
{
    //rest of list of users who has access to mail
    static function reset_cfg()
    {
        $list = [];
        $accounts_users_query = db_query("select * from app_ext_mail_accounts_users");
        while ($accounts_users = db_fetch_array($accounts_users_query)) {
            $list[] = $accounts_users['users_id'];
        }

        configuration::set('CFG_MAIL_INTEGRATION_USERS', implode(',', $list));
    }

    static function has_access()
    {
        global $app_user;

        if (strlen(CFG_MAIL_INTEGRATION_USERS)) {
            if (in_array($app_user['id'], explode(',', CFG_MAIL_INTEGRATION_USERS))) {
                return true;
            }
        }

        return false;
    }

    static function get_signature()
    {
        global $app_user;

        $accounts_users_query = db_query(
            "select * from app_ext_mail_accounts_users where users_id='" . $app_user['id'] . "' and length(signature)>0"
        );
        if ($accounts_users = db_fetch_array($accounts_users_query)) {
            return $accounts_users['signature'];
        }

        return '';
    }

}