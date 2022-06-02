<?php

class mail_filters
{
    static function check($mail, $action = '')
    {
        $check = false;
        $filters_query = db_query(
            "select * from app_ext_mail_filters where accounts_id='" . $mail['accounts_id'] . "' " . (strlen(
                $action
            ) ? " and action='" . $action . "'" : "")
        );
        while ($filters = db_fetch_array($filters_query)) {
            if (strlen($filters['from_email'])) {
                if (strstr($filters['from_email'], '@')) {
                    if ($mail['from_email'] == $filters['from_email']) {
                        $check = true;
                    }
                } else {
                    if (strstr($mail['from_email'], $filters['from_email'])) {
                        $check = true;
                    }
                }
            }

            if (strlen($filters['has_words'])) {
                foreach (preg_split('/[\ \n\,]+/', $filters['has_words']) as $wrod) {
                    if (strstr($mail['subject'], $wrod) or strstr($mail['body'], $wrod) or strstr(
                            $mail['body_text'],
                            $wrod
                        )) {
                        $check = true;
                    }
                }
            }

            if ($check) {
                return $filters['action'];
            }
        }

        return '';
    }

    static function has_auto_create_filters($accounts_id)
    {
        $filters_query = db_query(
            "select id from app_ext_mail_filters where accounts_id='" . $accounts_id . "' and action='auto_create_item'"
        );
        if ($filters = db_fetch_array($filters_query)) {
            return true;
        } else {
            return false;
        }
    }

    static function get_action_choices()
    {
        $choices = [];

        $choices['delete'] = TEXT_EXT_DELETE_MAIL;
        $choices['skip_spam'] = TEXT_EXT_NEVER_SEND_TO_SPAM;

        return $choices;
    }

    static function get_action_name($key)
    {
        $choices = self::get_action_choices();

        return $choices[$key];
    }

}