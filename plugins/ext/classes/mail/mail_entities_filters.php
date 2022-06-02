<?php

class mail_entities_filters
{
    static function get_filter($mail, $account_entities_id)
    {
        $check = false;
        $filters_query = db_query(
            "select * from app_ext_mail_accounts_entities_filters where account_entities_id='" . $account_entities_id . "'"
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

    static function get_field($mail, $account_entities_id, $fields_id)
    {
        if ($filter = self::get_filter($mail, $account_entities_id)) {
            $entities_fields_query = db_query(
                "select * from app_ext_mail_accounts_entities_fields where account_entities_id='" . $account_entities_id . "' and fields_id='" . $fields_id . "' and filters_id='" . $filter['id'] . "'"
            );
            if ($entities_fields = db_fetch_array($entities_fields_query)) {
                return $entities_fields;
            }
        }

        return false;
    }

    static function get_parent_item_id($mail, $account_entities_id, $parent_item_id)
    {
        if ($filter = self::get_filter($mail, $account_entities_id)) {
            return $filter['parent_item_id'];
        }

        return $parent_item_id;
    }
}