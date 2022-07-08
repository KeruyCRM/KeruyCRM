<?php

namespace Models\Main\Users;

class Public_registration
{
    public static function send_user_activation_email_msg($user_id, $previous_item_info)
    {
        //skip notification
        if (\K::$fw->CFG_USE_PUBLIC_REGISTRATION == 0 or \K::$fw->CFG_PUBLIC_REGISTRATION_USER_ACTIVATION != 'manually') {
            return false;
        }

        if ($previous_item_info['field_5'] == 0) {
            $item_query = db_query("select e.* from app_entity_1 e where e.id='" . $user_id . "' and e.field_5=1");
            if ($item = db_fetch_array($item_query)) {
                $to_name = (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? $item['field_7'] . ' ' . $item['field_8'] : $item['field_8'] . ' ' . $item['field_7']);

                $options = [
                    'to' => $item['field_9'],
                    'to_name' => $to_name,
                    'subject' => (strlen(
                        \K::$fw->CFG_USER_ACTIVATION_EMAIL_SUBJECT
                    ) > 0 ? \K::$fw->CFG_USER_ACTIVATION_EMAIL_SUBJECT : \K::$fw->TEXT_USER_ACTIVATION_EMAIL_SUBJECT),
                    'body' => (strlen(\K::$fw->CFG_USER_ACTIVATION_EMAIL_BODY) > 0 ? \K::f3(
                    )->CFG_USER_ACTIVATION_EMAIL_BODY : sprintf(
                        \K::$fw->TEXT_USER_ACTIVATION_EMAIL_BODY,
                        url_for('users/login', '', true)
                    )),
                    'from' => \K::$fw->CFG_EMAIL_ADDRESS_FROM,
                    'from_name' => \K::$fw->CFG_EMAIL_NAME_FROM
                ];

                users::send_email($options);
            }
        }
    }
}