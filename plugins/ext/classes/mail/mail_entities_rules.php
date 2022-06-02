<?php

class mail_entities_rules
{
    static function has_rules($account_entities_id)
    {
        $filters_query = db_query(
            "select id from app_ext_mail_accounts_entities_rules where account_entities_id='" . $account_entities_id . "' limit 1"
        );
        if ($filters = db_fetch_array($filters_query)) {
            return true;
        } else {
            return false;
        }
    }

    static function get_rule($mail, $account_entities_id)
    {
        $check = false;
        $filters_query = db_query(
            "select * from app_ext_mail_accounts_entities_rules where account_entities_id='" . $account_entities_id . "'"
        );
        while ($filters = db_fetch_array($filters_query)) {
            $check_from_email = true;
            $check_has_words = true;

            if (strlen($filters['from_email'])) {
                $check_from_email = false;

                if (strstr($filters['from_email'], '@')) {
                    if ($mail['from_email'] == $filters['from_email']) {
                        $check_from_email = true;
                    }
                } else {
                    if (strstr($mail['from_email'], $filters['from_email'])) {
                        $check_from_email = true;
                    }
                }
            }

            if (strlen($filters['has_words'])) {
                $check_has_words = false;

                foreach (explode(',', $filters['has_words']) as $wrod) {
                    if (strstr($mail['subject'], $wrod) or strstr($mail['body'], $wrod) or strstr(
                            $mail['body_text'],
                            $wrod
                        )) {
                        $check_has_words = true;
                    }
                }
            }

            if ($check_from_email and $check_has_words) {
                return $filters;
            }
        }

        return false;
    }
}