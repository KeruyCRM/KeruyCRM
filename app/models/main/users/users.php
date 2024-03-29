<?php

namespace Models\Main\Users;

class Users
{
    public static function output_heading_from_item($item)
    {
        return (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname'
            ? $item['field_7'] . ' ' . $item['field_8']
            : $item['field_8'] . ' ' . $item['field_7']);
    }

    public static function get_cache()
    {
        $include_public_profile = false;

        //include public profile for page where it needs only
        //if (isset($_GET['module'])) {
        if (in_array(\K::$fw->app_module_path, ['items/listing', 'items/info', 'items/comments_listing'])) {
            $include_public_profile = true;
        }
        //}

        $public_profile_fields = [];

        //get public profile fields
        //if (defined('CFG_PUBLIC_USER_PROFILE_FIELDS') and $include_public_profile) {
        if (\K::fw()->exists('CFG_PUBLIC_USER_PROFILE_FIELDS')
            and strlen(\K::$fw->CFG_PUBLIC_USER_PROFILE_FIELDS) > 0
            and $include_public_profile) {
            $fields_query = \K::model()->db_query_exec(
                'select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (' . \Models\Main\Fields_types::get_reserved_types_list(
                ) . ') and f.id in (' . \K::$fw->CFG_PUBLIC_USER_PROFILE_FIELDS . ') and  f.entities_id = 1 and f.forms_tabs_id = t.id order by field(f.id,' . \K::$fw->CFG_PUBLIC_USER_PROFILE_FIELDS . ')',
                null,
                'app_fields,app_forms_tabs'
            );
            //while ($v = db_fetch_array($fields_query)) {
            foreach ($fields_query as $v) {
                //TODO require foreach?
                $public_profile_fields[] = $v;
            }
        }

        $cache = [];

        $listing_sql_query_select = '';
        if (count($public_profile_fields)) {
            $listing_sql_query_select = \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(1, '');
        }

        $field_heading_id = \Models\Main\Fields::get_heading_id(1);

        $users_query = \K::model()->db_query_exec(
            'select e.* ' . $listing_sql_query_select . ', a.name as group_name, a.id as group_id from app_entity_1 e left join app_access_groups a on a.id = e.field_6 order by e.field_8, e.field_7',
            null,
            'app_entity_1,app_access_groups'
        );
        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $profile_fields = [];

            //generate public profile data
            foreach ($public_profile_fields as $field) {
                $value = $users['field_' . $field['id']];

                if (strlen($value) > 0) {
                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $users,
                        'is_listing' => true,
                        'is_export' => true,
                        'redirect_to' => '',
                        'reports_id' => 0,
                        'path' => 1
                    ];

                    //fix notice in cron
                    /*if (!defined('TEXT_' . strtoupper($field['type']) . '_TITLE') and defined('IS_CRON')) {
                        define('TEXT_' . strtoupper($field['type']) . '_TITLE', '');
                    }*/
                    if (!\K::fw()->exists('TEXT_' . strtoupper($field['type']) . '_TITLE')) {
                        \K::fw()->set('TEXT_' . strtoupper($field['type']) . '_TITLE', '');
                    }

                    $profile_fields[] = [
                        'name' => \Models\Main\Fields_types::get_option($field['type'], 'name', $field['name']),
                        'value' => \Models\Main\Fields_types::output($output_options)
                    ];
                }
            }

            if (strlen($users['field_10']) > 0) {
                $file = \Tools\Attachments::parse_filename($users['field_10']);

                $photo = $file['file_sha1'];
            } else {
                $photo = '';
            }

            if ($field_heading_id and $field_heading_id != 12) {
                $name = \Models\Main\Items\Items::get_heading_field_value($field_heading_id, $users);
            } else {
                $name = (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? $users['field_7'] . ' ' . $users['field_8'] : $users['field_8'] . ' ' . $users['field_7']);
            }

            $cache[$users['id']] = [
                'name' => $name,
                'email' => $users['field_9'],
                'photo' => $photo,
                'group_id' => (int)$users['field_6'],
                'group_name' => ($users['group_id'] > 0 ? $users['group_name'] : \K::$fw->TEXT_ADMINISTRATOR),
                'profile' => $profile_fields
            ];
        }

        return $cache;
    }

    public static function get_assigned_users_by_item($entity_id, $item_id)
    {
        $users = [];

        $item_info = db_find('app_entity_' . $entity_id, $item_id);

        $fields_query = db_query(
            "select f.* from app_fields f where f.type in ('fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users') and  f.entities_id='" . db_input(
                $entity_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            switch ($fields['type']) {
                case 'fieldtype_users':
                case 'fieldtype_users_ajax':
                    if (strlen($item_info['field_' . $fields['id']])) {
                        $users = array_merge(explode(',', $item_info['field_' . $fields['id']]), $users);
                    }
                    break;
                case 'fieldtype_grouped_users':
                    if (strlen($choices_id = $item_info['field_' . $fields['id']])) {
                        $choices_query = db_query("select * from app_fields_choices where id='" . $choices_id . "'");
                        if ($choices = db_fetch_array($choices_query)) {
                            if (strlen($choices['users'])) {
                                $users = array_merge(explode(',', $choices['users']), $users);
                            }
                        }
                    }
                    break;
            }
        }

        return $users;
    }

    public static function get_name_by_id($id)
    {
        if (isset(\K::$fw->app_users_cache[$id])) {
            return \K::$fw->app_users_cache[$id]['name'];
        }
    }

    public static function render_public_profile($users_cache, $is_photo_display = false)
    {
        if (strlen(\K::$fw->app_module_action) > 0) {
            return '';
        }

        if (strlen($users_cache['photo']) and is_file(\K::$fw->DIR_WS_USERS . $users_cache['photo'])) {
            $photo = '<img src="' . \Helpers\Urls::url_for_file(
                    \K::$fw->DIR_WS_USERS . $users_cache['photo']
                ) . '" width="50">';
        } else {
            $photo = '<img src="' . \Helpers\Urls::url_for_file('images/' . 'no_photo.png') . '" width="50">';
        }

        if (!isset($users_cache['profile'])) {
            $users_cache['profile'] = [];
        }

        $content = '
      <table align=center>
        <tr>
          <td valign=top width=60>' . $photo . '</td>
          <td valign=top>
            <table class=popover-table-data>';

        foreach ($users_cache['profile'] as $field) {
            $content .= '
        <tr>
          <td valign=top>' . htmlspecialchars(strip_tags($field['name'])) . ': </td><td valign=top>' . htmlspecialchars(
                    strip_tags($field['value'])
                ) . '</td>
        </tr>';
        }

        $content .= '
            </table>
          </td>
        </tr>
      </table>';

        $display_profile = false;

        if (count($users_cache['profile']) > 0 and $is_photo_display) {
            $display_profile = true;
        } elseif (!$is_photo_display) {
            $display_profile = true;
        }

        if ($display_profile) {
            return 'data-toggle="popover" title="' . addslashes(
                    htmlspecialchars($users_cache['name'])
                ) . '" data-content="' . addslashes(str_replace(["\n", "\r", "\n\r"], ' ', $content)) . '"';
        } else {
            return '';
        }
    }

    public static function get_choices($options = [])
    {
        $choices = [];
        $users_query = \K::model()->db_query_exec(
            'select u.*, a.name as group_name from app_entity_1 u left join app_access_groups a on a.id = u.field_6 where u.field_5 = 1 order by u.field_8, u.field_7',
            null,
            'app_entity_1,app_access_groups'
        );
        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $group_name = ((is_string($users['group_name']) and strlen(
                    $users['group_name']
                ) > 0) ? $users['group_name'] : \K::$fw->TEXT_ADMINISTRATOR);
            $choices[$group_name][$users['id']] = $users['field_8'] . ' ' . $users['field_7'];
        }

        return $choices;
    }

    public static function get_choices_by_entity($entities_id, $has_access = '')
    {
        //global $app_users_cache;

        $access_schema = users::get_entities_access_schema_by_groups($entities_id);

        $choices = [];
        $order_by_sql = (\K::$fw->CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
        $users_query = db_query(
            "select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 order by " . $order_by_sql
        );
        while ($users = db_fetch_array($users_query)) {
            if (!isset($access_schema[$users['field_6']])) {
                $access_schema[$users['field_6']] = [];
            }

            if (strlen($has_access)) {
                if ($users['field_6'] == 0 or in_array($has_access, $access_schema[$users['field_6']])) {
                    $choices[$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
                }
            } elseif ($users['field_6'] == 0 or in_array('view', $access_schema[$users['field_6']]) or in_array(
                    'view_assigned',
                    $access_schema[$users['field_6']]
                )) {
                $choices[$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
            }
        }

        return $choices;
    }

    public static function use_email_pattern($pattern, $blocks = [])
    {
        $content = file_get_contents('app/views/patterns/email/' . $pattern . '.html');

        foreach ($blocks as $k => $v) {
            $v = users::use_email_pattern_style($v, $k);
            $content = str_replace('[' . $k . ']', $v, $content);
        }

        return $content;
    }

    public static function use_email_pattern_style($content, $style)
    {
        $content = preg_replace('/data-content="(.*)"/', '', $content);

        $styles = require('app/views/patterns/email/styles.php');
        $css_classes = require('app/views/patterns/email/css_classes.php');

        foreach ($styles[$style] as $tag => $css) {
            $content = str_replace(['<' . $tag . ' ', '<' . $tag . '>'],
                ['<' . $tag . ' style="' . $css . '" ', '<' . $tag . ' style="' . $css . '">'],
                $content);
        }

        foreach ($css_classes as $class => $styles) {
            $content = str_replace('class="' . $class . '"', 'style="' . $styles . '"', $content);
        }

        return $content;
    }

    public static function send_to($send_to, $subject, $body, $attachments = [])
    {
        foreach ($send_to as $users_id) {
            if (strstr($users_id, '@')) {
                if (\Helpers\App::app_validate_email($users_id)) {
                    $options = [
                        'to' => $users_id,
                        'to_name' => '',
                        'subject' => $subject,
                        'body' => $body,
                        'from' => \K::$fw->app_user['email'],
                        'from_name' => \K::$fw->app_user['name'],
                        'attachments' => $attachments,
                    ];

                    self::send_email($options);
                }
            } else {
                if (\K::$fw->CFG_EMAIL_COPY_SENDER == 0 and $users_id == \K::$fw->app_user['id']) {
                    continue;
                }

                if (\Models\Main\Users\Users_cfg::get_value_by_users_id($users_id, 'disable_notification') == 1) {
                    continue;
                }

                /*$users_info_query = db_query(
                    "select * from app_entity_1 where id='" . db_input($users_id) . "' and field_5=1"
                );*/
                $users_info = \K::model()->db_fetch_one('app_entity_1', [
                    'id = ? and field_5 = 1',
                    $users_id
                ]);

                if ($users_info and isset(\K::$fw->app_user['email'])) {
                    $options = [
                        'to' => $users_info['field_9'],
                        'to_name' => \K::$fw->app_users_cache[$users_info['id']]['name'],
                        'subject' => $subject,
                        'body' => $body,
                        'from' => \K::$fw->app_user['email'],
                        'from_name' => \K::$fw->app_user['name'],
                        'attachments' => $attachments,
                    ];

                    self::send_email($options);
                }
            }
        }
    }

    public static function send_email($options = [])
    {
        //check status
        if (\K::$fw->CFG_EMAIL_USE_NOTIFICATION == 0 and !isset($options['send_directly'])) {
            return false;
        }

        //Sending via cron. Use send_directly to skip corn
        if (\K::$fw->CFG_SEND_EMAILS_ON_SCHEDULE == 1 and !isset($options['send_directly'])) {
            $sql_data = [
                'date_added' => time(),
                'email_to' => $options['to'],
                'email_to_name' => $options['to_name'],
                'email_subject' => $options['subject'],
                'email_body' => $options['body'],
                'email_from' => $options['from'],
                'email_from_name' => $options['from_name'],
                'email_attachments' => self::emails_on_schedule_prepare_attachments(($options['attachments'] ?? [])),
            ];

            \K::model()->db_perform('app_emails_on_schedule', $sql_data);

            return true;
        }
        require_once 'app/libs/PHPMailer/6.5.1/vendor/autoload.php';
        require_once 'app/libs/PHPMailer/extras/Html2Text.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->CharSet = "UTF-8";
            $mail->setLanguage(\K::$fw->TEXT_APP_LANGUAGE_SHORT_CODE);

            if (\K::$fw->CFG_EMAIL_USE_SMTP == 1) {
                $mail->isSMTP();                          // Set mailer to use SMTP
                $mail->Host = \K::$fw->CFG_EMAIL_SMTP_SERVER;      // Specify main and backup server
                $mail->Port = \K::$fw->CFG_EMAIL_SMTP_PORT;

                if (strlen(\K::$fw->CFG_EMAIL_SMTP_LOGIN) > 0 or strlen(\K::$fw->CFG_EMAIL_SMTP_PASSWORD)) {
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = \K::$fw->CFG_EMAIL_SMTP_LOGIN;               // SMTP username
                    $mail->Password = \K::$fw->CFG_EMAIL_SMTP_PASSWORD;            // SMTP password
                } else {
                    $mail->SMTPAuth = false;
                }

                //set encryption
                switch (\K::$fw->CFG_EMAIL_SMTP_ENCRYPTION) {
                    case 'ssl':
                    case 'tls':
                        $mail->SMTPSecure = \K::$fw->CFG_EMAIL_SMTP_ENCRYPTION;
                        break;
                    default:
                        $mail->SMTPAutoTLS = false;
                        $mail->SMTPSecure = false;
                        break;
                }

                //set debug mode                
                if (\K::$fw->CFG_EMAIL_SMTP_DEBUG) {
                    $mail->SMTPDebug = 3;
                    $mail->Debugoutput = 'app_smtp_error_log';
                }
            }

            if (isset($options['force_send_from'])) {
                $mail->setFrom($options['from'], $options['from_name'], false);
            } elseif (\K::$fw->CFG_EMAIL_SEND_FROM_SINGLE == 1) {
                $mail->setFrom(\K::$fw->CFG_EMAIL_ADDRESS_FROM, \K::$fw->CFG_EMAIL_NAME_FROM, false);
            } else {
                $mail->setFrom($options['from'], $options['from_name'], false);
            }

            $mail->addAddress($options['to'], $options['to_name']);  // Add a recipient

            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $filename => $name) {
                    if (is_file($filename)) {
                        $mail->addAttachment($filename, $name);
                    }
                }
            }

            $mail->isHTML(true);  // Set email format to HTML

            //use custom html layout
            $options['html_layout'] = $options['html_layout'] ?? 1;

            if ($options['html_layout'] and \K::$fw->CFG_USE_EMAIL_HTML_LAYOUT and strstr(
                    \K::$fw->CFG_EMAIL_HTML_LAYOUT,
                    '${body}'
                )) {
                $options['body'] = str_replace('${body}', $options['body'], \K::$fw->CFG_EMAIL_HTML_LAYOUT);
            }

            $mail->Subject = (strlen(
                    \K::$fw->CFG_EMAIL_SUBJECT_LABEL
                ) > 0 ? \K::$fw->CFG_EMAIL_SUBJECT_LABEL . ' ' : '') . $options['subject'];
            $mail->Body = $options['body'];

            $h2t = new \html2text($options['body']);
            $mail->AltBody = $h2t->get_text();

            $mail->send();
        } catch (\Exception $e) {
            \K::flash()->addMessage(
                sprintf(
                    \K::$fw->TEXT_MAILER_ERROR,
                    $options['to']
                ) . ': ' . $mail->ErrorInfo . (\K::$fw->CFG_EMAIL_SMTP_DEBUG ? '<br>' . \K::$fw->TEXT_MORE_INFO . ': log/smtp_log.txt' : ''),
                'error'
            );

            if (\K::$fw->CFG_EMAIL_SMTP_DEBUG) {
                error_log("\n", 3, "log/smtp_log.txt");
            }

            return false;
        }

        return true;
    }

    static function emails_on_schedule_prepare_attachments($attachments)
    {
        $files_list = [];
        foreach ($attachments as $filename => $name) {
            if (is_file($filename)) {
                if (copy($filename, \K::$fw->DIR_FS_TMP . sha1($name))) {
                    $files_list[] = $name;
                }
            }
        }

        return implode(',', $files_list);
    }

    public static function get_random_password($length = null, $has_symbols = true)
    {
        if ($length === null) {
            $length = \K::$fw->CFG_PASSWORD_MIN_LENGTH;
        }
        $chars = "abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKMNOPQRSTUVWXYZ" . ($has_symbols ? '~!@#$%^&*()_+' : '');

        $password = '';
        try {
            for ($i = 0; $i < $length; $i++) {
                $password .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } catch (\Exception $e) {
        }

        return $password;
    }

    public static function get_fields_access_schema($entities_id, $access_groups_id)
    {
        if (isset(\K::$fw->roles_fields_access_schema) and \K::$fw->roles_fields_access_schema) {
            return \K::$fw->roles_fields_access_schema;
        }

        $access_schema = [];

        $access_info_query = \K::model()->db_fetch(
            'app_fields_access',
            [
                'entities_id = ? and access_groups_id = ?',
                $entities_id,
                $access_groups_id
            ],
            [],
            'fields_id,access_schema'
        );

        foreach ($access_info_query as $access_info) {
            $access_info = $access_info->cast();

            $access_schema[$access_info['fields_id']] = $access_info['access_schema'];
        }

        return $access_schema;
    }

    public static function get_entities_access_schema($entities_id, $access_groups_id)
    {
        $access_schema = [];

        $access_info = \K::model()->db_fetch_one('app_entities_access', [
            'entities_id = ? and access_groups_id = ?',
            $entities_id,
            $access_groups_id
        ], [], 'access_schema');

        if ($access_info) {
            $access_schema = explode(',', $access_info['access_schema']);
        }

        return $access_schema;
    }

    public static function get_users_access_schema($access_groups_id)
    {
        $access_schema = [];

        /*$access_info_query = db_query(
            "select * from app_entities_access where access_groups_id='" . db_input($access_groups_id) . "'"
        );*/

        $access_info_query = \K::model()->db_fetch(
            'app_entities_access',
            ['access_groups_id = ?', $access_groups_id],
            [],
            'entities_id,access_schema'
        );//FIX

        //while ($access_info = db_fetch_array($access_info_query)) {
        foreach ($access_info_query as $access_info) {
            $access_info = $access_info->cast();

            if (strlen($access_info['access_schema'])) {
                $access_schema[$access_info['entities_id']] = explode(',', $access_info['access_schema']);
            }
        }

        return $access_schema;
    }

    public static function has_users_access_name_to_entity($access, $entities_id)
    {
        //global $app_users_access, $app_user;

        //administrator have full access
        if (\K::$fw->app_user['group_id'] == 0) {
            if ($access == 'action_with_assigned') {
                return false;
            } else {
                return true;
            }
        }

        if (isset(\K::$fw->app_users_access[$entities_id])) {
            return in_array($access, \K::$fw->app_users_access[$entities_id]);
        } else {
            return false;
        }
    }

    public static function has_users_access_to_entity($entities_id)
    {
        //global $app_users_access, $app_user;

        if (isset(\K::$fw->app_users_access[$entities_id]) or \K::$fw->app_user['group_id'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_entities_access_schema_by_groups($entities_id)
    {
        $access_schema = [];

        /*$access_info_query = db_query(
            "select access_schema,access_groups_id  from app_entities_access where entities_id='" . db_input(
                $entities_id
            ) . "'"
        );*/

        $access_info_query = \K::model()->db_fetch('app_entities_access', [
            'entities_id = ?',
            $entities_id
        ], [], 'access_schema,access_groups_id');

        //while ($access_info = db_fetch_array($access_info_query)) {
        foreach ($access_info_query as $access_info) {
            $access_info = $access_info->cast();

            $access_schema[$access_info['access_groups_id']] = explode(',', $access_info['access_schema']);
        }

        return $access_schema;
    }

    public static function has_access($access, $access_schema = null)
    {
        //administrator have full access
        if (\K::$fw->app_user['group_id'] == 0) {
            if (in_array($access, ['action_with_assigned', 'delete_creator'])) {
                return false;
            } else {
                return true;
            }
        }

        $schema = [];

        if (isset($access_schema)) {
            $schema = $access_schema;
        } elseif (is_array(\K::$fw->current_access_schema)) {
            $schema = \K::$fw->current_access_schema;
        }

        return in_array($access, $schema);
    }

    public static function has_access_to_entity($entities_id, $access, $access_groups_id = null)
    {
        $access_schema = [];

        if (!isset($access_groups_id)) {
            $access_groups_id = \K::$fw->app_user['group_id'];
        }

        if ($access_groups_id == 0) {
            return true;
        }

        /*$access_info_query = db_query(
            "select access_schema from app_entities_access where entities_id='" . db_input(
                $entities_id
            ) . "' and access_groups_id='" . db_input($access_groups_id) . "'"
        );*/

        $access_info = \K::model()->db_fetch_one('app_entities_access', [
            'entities_id = ? and access_groups_id = ?',
            $entities_id,
            $access_groups_id
        ], [], 'access_schema');

        if ($access_info) {
            $access_schema = explode(',', $access_info['access_schema']);
        }

        return in_array($access, $access_schema);
    }

    public static function has_access_to_assigned_item($entities_id, $items_id)
    {
        //get users entities tree
        $users_entities_tree = entities::get_tree(1);

        //get users entities id list
        $users_entities = [];
        foreach ($users_entities_tree as $v) {
            $users_entities[] = $v['id'];
        }

        //force check users entities tree access
        if (in_array($entities_id, $users_entities) and \K::$fw->app_user['group_id'] > 0) {
            $item_query = db_query(
                "select e.id from app_entity_" . $entities_id . " e where e.id='" . db_input(
                    $items_id
                ) . "' " . items::add_access_query_for_user_parent_entities($entities_id)
            );
            if ($item = db_fetch_array($item_query)) {
                return true;
            } else {
                return false;
            }
        } else {
            $users_fields = [];
            $fields_query = db_query(
                "select f.id from app_fields f where f.type in ('fieldtype_users','fieldtype_users_ajax','fieldtype_user_roles','fieldtype_users_approve') and  f.entities_id='" . db_input(
                    $entities_id
                ) . "'"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $users_fields[] = $fields['id'];
            }

            $grouped_users_fields = [];
            $grouped_global_users_fields = [];
            $fields_query = db_query(
                "select f.id, f.configuration from app_fields f where f.type in ('fieldtype_grouped_users') and  f.entities_id='" . db_input(
                    $entities_id
                ) . "'"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                if ($cfg->get('use_global_list') > 0) {
                    $grouped_global_users_fields[$cfg->get('use_global_list')] = $fields['id'];
                } else {
                    $grouped_users_fields[] = $fields['id'];
                }
            }

            $access_group_fields = [];
            $fields_query = db_query(
                "select f.id from app_fields f where f.type in ('fieldtype_access_group') and  f.entities_id='" . db_input(
                    $entities_id
                ) . "'"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $access_group_fields[] = $fields['id'];
            }

            $sql_query_array = [];

            //check users fields
            foreach ($users_fields as $id) {
                $sql_query_array[] = "(select count(*) as total from app_entity_" . $entities_id . "_values cv where cv.items_id='" . db_input(
                        $items_id
                    ) . "' and cv.fields_id='" . $id . "' and cv.value='" . \K::$fw->app_user['id'] . "')>0";
            }

            //check gouped users
            foreach ($grouped_users_fields as $id) {
                $sql_query_array[] = "(select count(*) as total from app_entity_" . $entities_id . "_values cv where  cv.items_id='" . db_input(
                        $items_id
                    ) . "' and cv.fields_id='" . $id . "' and cv.value in (select id from app_fields_choices fc where fc.fields_id='" . $id . "' and find_in_set(" . \K::$fw->app_user['id'] . ",fc.users)))>0";
            }

            //check gouped users with globallist
            foreach ($grouped_global_users_fields as $list_id => $id) {
                $sql_query_array[] = "(select count(*) as total from app_entity_" . $entities_id . "_values cv where cv.items_id=e.id and cv.fields_id='" . $id . "' and cv.value in (select id from app_global_lists_choices fc where fc.lists_id='" . $list_id . "' and find_in_set(" . \K::$fw->app_user['id'] . ",fc.users)))>0";
            }

            //check access group fields
            foreach ($access_group_fields as $id) {
                $sql_query_array[] = "(select count(*) as total from app_entity_" . $entities_id . "_values cv where cv.items_id=e.id and cv.fields_id='" . $id . "' and cv.value='" . \K::$fw->app_user['group_id'] . "')>0";
            }

            //check created by
            $sql_query_array[] = "e.created_by='" . \K::$fw->app_user['id'] . "'";

            //check user entity
            if ($entities_id == 1) {
                $sql_query_array[] = "e.id='" . \K::$fw->app_user['id'] . "'";
            }

            $item_query = db_query(
                "select e.id from app_entity_" . $entities_id . " e where e.id='" . db_input(
                    $items_id
                ) . "' and (" . implode(' or ', $sql_query_array) . ") ",
                false
            );
            if ($item = db_fetch_array($item_query)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function get_comments_access_schema($entities_id, $access_groups_id)
    {
        $access_schema = [];

        /*$access_info_query = db_query(
            "select access_schema from app_comments_access where entities_id='" . db_input(
                $entities_id
            ) . "' and access_groups_id='" . db_input($access_groups_id) . "'"
        );*/

        $access_info = \K::model()->db_fetch_one('app_comments_access', [
            'entities_id = ? and access_groups_id = ?',
            $entities_id,
            $access_groups_id
        ], [], 'access_schema');

        if ($access_info) {
            $access_schema = explode(',', $access_info['access_schema']);
        }

        return $access_schema;
    }

    public static function has_comments_access($access, $comments_access_schema = null, $check_logged_user = true)
    {
        //global $current_comments_access_schema, $app_user;

        //administrator have full access
        if (\K::$fw->app_user['group_id'] == 0 and $check_logged_user) {
            return true;
        }

        if (isset($comments_access_schema)) {
            $schema = $comments_access_schema;
        } else {
            $schema = \K::$fw->current_comments_access_schema;
        }

        return in_array($access, $schema);
    }

    public static function has_reports_access()
    {
        //global $app_user;

        //administrator have full access
        if (\K::$fw->app_user['group_id'] == 0) {
            return true;
        } else {
            /*$access_query = db_query(
                "select * from app_entities_access where access_groups_id='" . db_input(
                    \K::$fw->app_user['group_id']
                ) . "' and find_in_set('reports',access_schema)"
            );*/

            $access = \K::model()->db_fetch_one('app_entities_access', [
                'access_groups_id = ? and find_in_set( ? ,access_schema)',
                \K::$fw->app_user['group_id'],
                'reports'
            ]);

            if ($access) {
                return true;
            } else {
                return false;
            }
        }
    }

    static function set_client_id()
    {
        //global $app_user;
        //TODO Refactor generation $client_id
        if (!\K::$fw->app_user['client_id']) {
            $client_id = mt_rand(100000, 999999) . \K::$fw->app_user['id'];

            //db_query("update app_entity_1 set client_id={$client_id} where id={$app_user['id']}");
            \K::model()->db_perform(
                'app_entity_1',
                ['client_id' => $client_id],
                ['id = ?', \K::$fw->app_user['id']]
            );

            \K::$fw->app_user['client_id'] = $client_id;
        }
    }

    public static function login($username, $password, $remember_me, $hash = null, $redirect_to = null)
    {
        /*$user_query = db_query(
            "select * from app_entity_1 where field_12='" . db_input(
                $username
            ) . "' " . (isset($password_hashed) ? " and password='" . db_input($password_hashed) . "'" : "")
        );*/

        $user = \K::model()->db_fetch_one(
            'app_entity_1',
            [
                'field_12 = :field_12',
                ':field_12' => $username
            ]
        );

        if ($user) {
            if ($user['field_5'] == 1) {
                //$hasher = new \Libs\PasswordHash(11, false);

                if (isset($hash) and \K::security()->isRememberMe($hash, $username, $user['password'])) {
                    \K::app_session_register('app_logged_users_id', $user['id']);

                    \Models\Main\Users\Users_login_log::success($username, $user['id']);

                    if (!\K::fw()->exists('GET.action')) {
                        //\Helpers\Urls::redirect_to($_GET['module'], get_all_get_url_params());
                        \Helpers\Urls::redirect_to(\K::$fw->URI);
                    } else {
                        \Helpers\Urls::redirect_to('main/dashboard');
                    }
                    //} elseif ($hasher->CheckPassword($password, $user['password'])) {
                } elseif (\K::security()->password_verify($password, $user['password'], $user['id'])) {
                    \K::app_session_register('app_logged_users_id', $user['id']);

                    //login log
                    if (\K::$fw->CFG_2STEP_VERIFICATION_ENABLED != 1) {
                        \Models\Main\Users\Users_login_log::success($username, $user['id']);
                    }

                    if ($remember_me == 1) {
                        $expires = time() + \K::$fw->CFG_COOKIE_TIME_REMEMBER_ME;
                        $app_remember_pass = \K::security()->getCookieHash(
                            $expires,
                            $user['field_12'],
                            $user['password']
                        );

                        \K::cookieSet('app_remember_me', 1, \K::$fw->CFG_COOKIE_TIME_REMEMBER_ME);
                        \K::cookieSet('app_stay_logged', 1, \K::$fw->CFG_COOKIE_TIME_REMEMBER_ME);
                        \K::cookieSet(
                            'app_remember_user',
                            base64_encode($user['field_12']),
                            \K::$fw->CFG_COOKIE_TIME_REMEMBER_ME
                        );
                        \K::cookieSet(
                            'app_remember_pass',
                            base64_encode($app_remember_pass),
                            \K::$fw->CFG_COOKIE_TIME_REMEMBER_ME
                        );
                    } else {
                        \K::cookieClear('app_remember_me');
                        \K::cookieClear('app_stay_logged');
                        \K::cookieClear('app_remember_user');
                        \K::cookieClear('app_remember_pass');
                    }

                    if (\K::cookieExists('app_login_redirect_to', $redirect_to)) {
                        \Helpers\Urls::redirect_to($redirect_to);
                    } else {
                        \Helpers\Urls::redirect_to('main/dashboard');
                    }
                } else {
                    //login log
                    \Models\Main\Users\Users_login_log::fail($username);

                    //TODO Fix lang?
                    //if (!defined('TEXT_USER_NOT_FOUND')) {
                    //    require('includes/languages/' . \K::$keruy->CFG_APP_LANGUAGE);
                    //}

                    //$alerts->add(\K::$keruy->TEXT_USER_NOT_FOUND, 'error');
                    \K::flash()->addMessage(\K::$fw->TEXT_USER_NOT_FOUND, 'error');
                    \Helpers\Urls::redirect_to('main/users/login');
                }
            } else {
                //login log
                \Models\Main\Users\Users_login_log::fail($username);

                //TODO Fix lang?
                //if (!defined('TEXT_USER_NOT_FOUND')) {
                //    require('includes/languages/' . \K::$keruy->CFG_APP_LANGUAGE);
                //}

                //$alerts->add(\K::$keruy->TEXT_USER_IS_NOT_ACTIVE, 'error');
                \K::flash()->addMessage(\K::$fw->TEXT_USER_IS_NOT_ACTIVE, 'error');
                \Helpers\Urls::redirect_to('main/users/login');
            }
        } else {
            //login log
            \Models\Main\Users\Users_login_log::fail($username);

            //TODO Fix lang?
            //if (!defined('TEXT_USER_NOT_FOUND')) {
            //    require('includes/languages/' . CFG_APP_LANGUAGE);
            //}

            //$alerts->add(\K::$keruy->TEXT_USER_NOT_FOUND, 'error');
            \K::flash()->addMessage(\K::$fw->TEXT_USER_NOT_FOUND, 'error');
            \Helpers\Urls::redirect_to('main/users/login');
        }
    }

    public static function getGroupAndAccessByUserId($users_id)
    {
        return \K::model()->db_query_exec(
            'select e.*, ag.name as group_name from app_entity_1 e left join app_access_groups ag on ag.id = e.field_6 where e.id = ? and e.field_5 = 1',
            $users_id,
            'app_entity_1,app_access_groups'
        );
    }
}