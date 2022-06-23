<?php

class Controller
{
    public $allowed_modules = [
        'users/login',
        'users/guest_login',
        'users/restore_password',
        'users/ldap_login',
        'users/signature_login',
        'users/photo',
        'ext/public/form',
        'ext/public/check',
        'ext/public/form_inactive',
        'dashboard/vpic',
        'ext/telephony/save_call',
        'dashboard/select2_json',
        'dashboard/select2_ml_json',
        'export/xml',
        'export/file',
        'users/2step_verification',
        'users/login_by_phone',
        'dashboard/ajax_request',
        'subentity/form',
        'ext/map_reports/public',
        'dashboard/token_error',
        'social_login/google',
        'social_login/vkontakte',
        'social_login/yandex',
        'social_login/facebook',
        'social_login/linkedin',
        'social_login/twitter',
        'social_login/steam',
        'feeders/rss',
        'feeders/ical',
    ];
    private $defaultCfg = [
        'CFG_APP_FIRST_DAY_OF_WEEK' => 0,
        'CFG_APP_LOGIN_PAGE_BACKGROUND' => '',
        'CFG_APP_DISPLAY_USER_NAME_ORDER' => 'firstname_lastname',
        'CFG_APP_COPYRIGHT_NAME' => '',
        'CFG_APP_NUMBER_FORMAT' => '2/./*',
        'CFG_APP_LOGO_URL' => '',
        'CFG_ALLOW_CHANGE_USERNAME' => 0,
        'CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL' => 0,
        'CFG_MAINTENANCE_MODE' => 0,
        'CFG_MAINTENANCE_MESSAGE_HEADING' => '',
        'CFG_MAINTENANCE_MESSAGE_CONTENT' => '',
        'CFG_APP_LOGIN_MAINTENANCE_BACKGROUND' => '',
        'CFG_RESIZE_IMAGES' => 0,
        'CFG_MAX_IMAGE_WIDTH' => 1600,
        'CFG_MAX_IMAGE_HEIGHT' => 900,
        'CFG_RESIZE_IMAGES_TYPES' => '2',
        'CFG_SKIP_IMAGE_RESIZE' => '5000',
        'CFG_NOTIFICATIONS_SCHEDULE' => 0,
        'CFG_SEND_EMAILS_ON_SCHEDULE' => 0,
        'CFG_MAXIMUM_NUMBER_EMAILS' => 3,
        'CFG_USE_PUBLIC_REGISTRATION' => 0,
        'CFG_PUBLIC_REGISTRATION_USER_GROUP' => '',
        'CFG_PUBLIC_REGISTRATION_PAGE_HEADING' => '',
        'CFG_PUBLIC_REGISTRATION_PAGE_CONTENT' => '',
        'CFG_REGISTRATION_BUTTON_TITLE' => '',
        'CFG_APP_DISABLE_CHANGE_PWD' => '',
        'CFG_LOGIN_PAGE_HIDE_REMEMBER_ME' => 0,
        'CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS' => '',
        'CFG_USE_API' => 0,
        'CFG_API_KEY' => '',
        'CFG_DISABLE_CHECK_FOR_UPDATES' => 0,
        'CFG_REGISTRATION_NOTIFICATION_USERS' => '',
        'CFG_USE_CACHE_REPORTS_IN_HEADER' => 0,
        'CFG_CACHE_REPORTS_IN_HEADER_LIFETIME' => 300,
        'CFG_LDAP_FIRSTNAME_ATTRIBUTE' => '',
        'CFG_LDAP_LASTNAME_ATTRIBUTE' => '',
        'CFG_PUBLIC_REGISTRATION_USER_AGREEMENT' => '',
        'CFG_ENCRYPT_FILE_NAME' => 1,
        'CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS' => '',
        'CFG_USE_GLOBAL_SEARCH' => 0,
        'CFG_GLOBAL_SEARCH_ALLOWED_GROUPS' => '',
        'CFG_GLOBAL_SEARCH_INPUT_MIN' => 3,
        'CFG_GLOBAL_SEARCH_INPUT_MAX' => 40,
        'CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER' => 0,
        'CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU' => 0,
        'CFG_PUBLIC_ATTACHMENTS' => '',
        'CFG_LOGIN_DIGITAL_SIGNATURE_MODULE' => '',
        'CFG_2STEP_VERIFICATION_ENABLED' => 0,
        'CFG_2STEP_VERIFICATION_TYPE' => 'email',
        'CFG_2STEP_VERIFICATION_SMS_MODULE' => '',
        'CFG_2STEP_VERIFICATION_USER_PHONE' => '',
        'CFG_LOGIN_BY_PHONE_NUMBER' => 0,
        'CFG_PUBLIC_REGISTRATION_USER_ACTIVATION' => 'automatic',
        'CFG_REGISTRATION_SUCCESS_PAGE_HEADING' => '',
        'CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION' => '',
        'CFG_USER_ACTIVATION_EMAIL_SUBJECT' => '',
        'CFG_USER_ACTIVATION_EMAIL_BODY' => '',
        'CFG_HIDE_POWERED_BY_TEXT' => 0,
        'CFG_APP_FAVICON' => '',
        'CFG_CREATE_ATTACHMENTS_PREVIEW' => 0,
        'CFG_DISPLAY_USER_GROUP_IN_MENU' => 0,
        'CFG_DISPLAY_USER_GROUP_ID_IN_MENU' => '',
        'CFG_ENABLE_MULTIPLE_ACCESS_GROUPS' => 0,
        'CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS' => 0,
        'CFG_ENABLE_SOCIAL_LOGIN' => 0,
        'CFG_ENABLE_VKONTAKTE_LOGIN' => 0,
        'CFG_VKONTAKTE_APP_ID' => '',
        'CFG_VKONTAKTE_SECRET_KEY' => '',
        'CFG_VKONTAKTE_BUTTON_TITLE' => '',
        'CFG_SOCAL_LOGIN_CREATE_USER' => 'autocreate',
        'CFG_SOCAL_LOGIN_USER_GROUP' => '',
        'CFG_ENABLE_GOOGLE_LOGIN' => 0,
        'CFG_GOOGLE_APP_ID' => '',
        'CFG_GOOGLE_SECRET_KEY' => '',
        'CFG_GOOGLE_BUTTON_TITLE' => '',
        'CFG_ENABLE_FACEBOOK_LOGIN' => 0,
        'CFG_FACEBOOK_APP_ID' => '',
        'CFG_FACEBOOK_SECRET_KEY' => '',
        'CFG_FACEBOOK_BUTTON_TITLE' => '',
        'CFG_ENABLE_LINKEDIN_LOGIN' => 0,
        'CFG_LINKEDIN_APP_ID' => '',
        'CFG_LINKEDIN_SECRET_KEY' => '',
        'CFG_LINKEDIN_BUTTON_TITLE' => '',
        'CFG_ENABLE_TWITTER_LOGIN' => 0,
        'CFG_TWITTER_APP_ID' => '',
        'CFG_TWITTER_SECRET_KEY' => '',
        'CFG_TWITTER_BUTTON_TITLE' => '',
        'CFG_ENABLE_GUEST_LOGIN' => 0,
        'CFG_GUEST_LOGIN_USER' => '',
        'CFG_GUEST_LOGIN_BUTTON_TITLE' => '',
        'CFG_PUBLIC_CALENDAR_ICAL' => 0,
        'CFG_PERSONAL_CALENDAR_ICAL' => 0,
        'CFG_IS_STRONG_PASSWORD' => 0,
        'CFG_EMAIL_SMTP_DEBUG' => 0,
        'CFG_DROP_DOWN_MENU_ON_HOVER' => 0,
        'CFG_CUSTOM_HTML_HEAD' => '',
        'CFG_CUSTOM_HTML_BODY' => '',
        'CFG_AUTOBACKUP_KEEP_FILES_DAYS' => 30,
        'CFG_EMAIL_HTML_LAYOUT' => '',
        'CFG_USE_EMAIL_HTML_LAYOUT' => 0,
        'CFG_ENABLE_STEAM_LOGIN' => 0,
        'CFG_STEAM_API_KEY' => '',
        'CFG_STEAM_DOMAIN' => '',
        'CFG_STEAM_BUTTON_TITLE' => '',
        'CFG_ENABLE_YANDEX_LOGIN' => 0,
        'CFG_YANDEX_APP_ID' => '',
        'CFG_YANDEX_SECRET_KEY' => '',
        'CFG_YANDEX_BUTTON_TITLE' => '',
    ];

    public function __construct()
    {
        if (!file_exists('config/database.php')) {
            \K::f3()->reroute('/set/install/index');
        }

        $this->_setSession();

        \K::security()->checkCsrfToken();
        \K::security()->initCsrfToken();

        $this->_setCfgIni();

        \K::f3()->mset($this->defaultCfg);

        $this->_setCfgFromDB();
        $this->_setCfg();

        \K::f3()->app_global_vars = \Tools\GlobalVars::instance();

        //set php timezone
        \K::f3()->TZ = \K::f3()->CFG_APP_TIMEZONE;

        //set mysql timezone as it's configured for app
        \K::model()->exec("SET time_zone = '" . date('P') . "'");

        //cache vars
        $get_heading_fields = \Models\Fields::get_heading_fields();
        \K::f3()->app_heading_fields_cache = $get_heading_fields['id'];
        \K::f3()->app_heading_fields_id_cache = $get_heading_fields['entities_id'];

        \K::f3()->app_not_formula_fields_cache = \Models\Fields::not_formula_fields_cache();
        \K::f3()->app_formula_fields_cache = \Models\Fields::formula_fields_cache();
        \K::f3()->app_fields_cache = \Models\Fields::get_cache();

        \K::f3()->app_access_rules_fields_cache = \Models\Access_rules::get_access_rules_fields_cache();
        \K::f3()->app_mysql_query_fields_cache = \Tools\FieldsTypes\Fieldtype_mysql_query::get_fields_cache();

        \K::f3()->app_entities_cache = \Models\Entities::get_cache();
        \K::f3()->app_choices_cache = \Models\Fields_choices::get_cache();
        \K::f3()->app_global_choices_cache = \Models\Global_lists::get_cache();
        \K::f3()->app_access_groups_cache = \Models\Access_groups::get_cache();

        //TODO num2str
        //$app_num2str = new num2str();

        \Models\Custom_php::include();

        $this->_setCfgSession();

        \K::f3()->app_plugin_path = '';
        \K::f3()->app_module = \K::f3()->get('PARAMS.controllerName');
        \K::f3()->app_action = (\K::f3()->exists('PARAMS.actionName') ? \K::f3()->get('PARAMS.actionName') : 'index');

        if (\K::f3()->get('PARAMS.moduleName') == 'module') {
            \K::f3()->app_module_path = \K::f3()->app_module . '/' . \K::f3()->app_action;
        } else {
            \K::f3()->app_module_path = \K::f3()->get('PARAMS.moduleName') . '/' . \K::f3()->app_module . '/' . \K::f3(
                )->app_action;
        }

        \K::f3()->app_title = (strlen(\K::f3()->CFG_APP_SHORT_NAME) > 0 ? \K::f3()->CFG_APP_SHORT_NAME : \K::f3(
        )->CFG_APP_NAME);

        \K::f3()->app_module_action = ($_GET['action'] ?? (isset($_POST['action']) ? $_POST['action'] : ''));

        \K::f3(
        )->app_redirect_to = ($_GET['redirect_to'] ?? (isset($_POST['redirect_to']) ? $_POST['redirect_to'] : ''));

        \K::f3()->app_path = ($_GET['path'] ?? (isset($_POST['path']) ? $_POST['path'] : ''));

        if (\K::f3()->CFG_USE_PUBLIC_REGISTRATION == 1) {
            $this->allowed_modules[] = 'users/registration';
            $this->allowed_modules[] = 'users/validate_form';
            $this->allowed_modules[] = 'users/registration_success';
        }

        $this->_setPlugin();//TODO include plugin
        $this->_userLogin();

        \K::f3()->app_users_cfg = new \Models\Users\Users_cfg();

        $this->_setCfgSession2();
        $this->_checkEnvironment();

        \Models\Users\Two_step_verification::check();

        //email confirmation check
        \Models\Users\Email_verification::check();

        //check if maintenance mode enabled
        \Tools\Maintenance_mode::check();
    }

    public function beforeroute()
    {
    }

    public function afterroute()
    {
        echo '<PRE style="white-space: pre-wrap;">' . PHP_EOL . \K::model()->db->log() . '</PRE>';
    }

    private function _setSession()
    {
        new \DB\SQL\Session(\K::model()->db, 'app_sessions_new', false, function ($session) {
            if (K::f3()->SESSION_CHECK_IP and $session->ip() !== \K::f3()->IP) {
                return false;
            }
            if (K::f3()->SESSION_CHECK_BROWSER and $session->agent() !== \K::f3()->AGENT) {
                return false;
            }
            return true;
        });
    }

    private function _setCfgFromDB()
    {
        $cfg = \K::model()->db_fetch_all('app_configuration', null, [\K::f3()->TTL_APP, 'app_configuration']);

        foreach ($cfg as $v) {
            $v = $v->cast();

            \K::f3()->{$v['configuration_name']} = $v['configuration_value'];
        }
    }

    private function _setCfg()
    {
        \K::f3()->mset(
            [
                'DIR_FS_BACKUPS_AUTO' => \K::f3()->DIR_FS_CATALOG . 'backups/auto/',
                'CFG_GLOBAL_SEARCH_ROWS_PER_PAGE' => \K::f3()->CFG_APP_ROWS_PER_PAGE,
                'CFG_SERVER_UPLOAD_MAX_FILESIZE' => ((int)ini_get("post_max_size") < (int)ini_get(
                    "upload_max_filesize"
                ) ? (int)ini_get(
                    "post_max_size"
                ) : (int)ini_get(
                    "upload_max_filesize"
                ))
            ]
        );
    }

    private function _setCfgSession()
    {
        if (!\K::sessionExists('uploadify_attachments')) {
            \K::sessionSet('uploadify_attachments', []);
        }

        if (!\K::sessionExists('uploadify_attachments_queue')) {
            \K::sessionSet('uploadify_attachments_queue', []);
        }

        if (!\K::sessionExists('app_send_to')) {
            \K::sessionSet('app_send_to', []);
        }

        if (!\K::sessionExists('app_session_token')) {
            \K::sessionSet('app_session_token', \K::security()->genAppSessionToken());
        }

        if (!\K::sessionExists('app_current_users_filter')) {
            \K::sessionSet('app_current_users_filter', []);
        }

        if (!\K::sessionExists('app_previously_logged_user')) {
            \K::sessionSet('app_previously_logged_user', 0);
        }

        if (!\K::sessionExists('two_step_verification_info')) {
            \K::sessionSet('two_step_verification_info', []);
        }

        if (!\K::sessionExists('app_email_verification_code')) {
            \K::sessionSet('app_email_verification_code', '');
        }

        if (!\K::sessionExists('app_force_print_template')) {
            \K::sessionSet('app_force_print_template', false);
        }
    }

    private function _setCfgSession2()
    {
        if (!\K::sessionExists('app_current_version')) {
            \K::sessionSet('app_current_version', '');
        }

        if (\K::f3()->CFG_DISABLE_CHECK_FOR_UPDATES == 1) {
            \K::f3()->app_current_version = '';
        }

        if (!\K::sessionExists('app_selected_items')) {
            \K::sessionSet('app_selected_items', []);
        }

        if (!\K::sessionExists('listing_page_keeper')) {
            \K::sessionSet('listing_page_keeper', []);
        }

        if (!\K::sessionExists('user_roles_dropdown_change_holder')) {
            \K::sessionSet('user_roles_dropdown_change_holder', []);
        }

        if (!\K::sessionExists('app_subentity_form_items')) {
            \K::sessionSet('app_subentity_form_items', []);
        }

        if (!\K::sessionExists('app_subentity_form_items_deleted')) {
            \K::sessionSet('app_subentity_form_items_deleted', []);
        }
    }

    private function _setCfgIni()
    {
        if (function_exists('ini_set')) {
            ini_set('session.bug_compat_warn', 0);
            ini_set('session.bug_compat_42', 0);
            ini_set('gd.jpeg_ignore_warning', 1);
        }
    }

    private function _setPlugin()
    {
    }

    private function _checkEnvironment()
    {
        $error_list = [];

        //check required libs
        $required_php_extensions = [
            'gd',
            'mbstring',
            'xmlwriter',
            'curl',
            'zip',
            'xml',
            'fileinfo',
        ];

        foreach ($required_php_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $error_list[] = sprintf(\K::f3()->TEXT_ERROR_LIB, strtoupper($ext));
            }
        }

        //check folder
        $check_folders = [
            \K::f3()->DIR_FS_UPLOADS,
            \K::f3()->DIR_FS_ATTACHMENTS,
            \K::f3()->DIR_FS_ATTACHMENTS_PREVIEW,
            \K::f3()->DIR_FS_IMAGES,
            \K::f3()->DIR_FS_USERS,
            \K::f3()->DIR_FS_BACKUPS,
            \K::f3()->DIR_FS_TMP,
            \K::f3()->DIR_FS_CACHE,
            \K::f3()->DIR_FS_CATALOG . 'log/'
        ];

        foreach ($check_folders as $v) {
            if (is_dir($v)) {
                if (!is_writable($v)) {
                    $error_list[] = sprintf(
                        'Error: folder "%s" is not writable!',
                        str_replace(\K::f3()->DIR_FS_CATALOG, '', $v)
                    );
                }
            } else {
                $error_list[] = sprintf(
                    'Error: folder "%s" does not exist',
                    str_replace(\K::f3()->DIR_FS_CATALOG, '', $v)
                );
            }
        }

        //display errors if exist
        if (count($error_list)) {
            foreach ($error_list as $v) {
                \K::flash()->addMessage($v, 'error');
            }
        }
    }

    private function _userLogin()
    {
        //TODO AUTOlogin https://github.com/symfony/symfony/blob/4.4/src/Symfony/Component/Security/Http/RememberMe/TokenBasedRememberMeServices.php#L101

        if (!\K::sessionExists('app_logged_users_id') and !in_array(!\K::f3()->module, $this->allowed_modules)) {
            //allows redirect user to current page after login if there is no any actions

            if (!\K::f3()->exists('GET.action') and !\K::f3()->exists('POST.action') and !\K::f3()->AJAX) {
                \K::cookieSet('app_login_redirect_to', \K::f3()->URI, 10 * 60);
                //setcookie('app_login_redirect_to', $_SERVER['QUERY_STRING'], time() + 10 * 60, '/');
            }
            //if (isset($_COOKIE["app_remember_me"]) and isset($_COOKIE["app_stay_logged"])) {
            if (\K::cookieExists('app_remember_me') and \K::cookieExists('app_stay_logged')) {
                //do not ask verification do if login by remember me function
                \K::f3()->two_step_verification_info['is_checked'] = true;

                \Models\Users\Users::login(
                    base64_decode(\K::cookieGet('app_remember_user')),
                    '',
                    1,
                    base64_decode(\K::cookieGet('app_remember_pass'))
                );
            } else {
                //redirect_to('users/login');
                \K::f3()->reroute('@Login');
            }
        } elseif (\K::sessionExists('app_logged_users_id')) {
            $user_query = \Models\Users\Users::getGroupAndAccessByUserId(\K::f3()->app_logged_users_id);

            if (isset($user_query[0])) {
                $user = $user_query[0];

                if (strlen($user['field_10']) > 0) {
                    $file = \Tools\Attachments::parse_filename($user['field_10']);
                    $photo = $file['file_sha1'];
                } else {
                    $photo = '';
                }

                \K::f3()->app_user = [
                    'id' => $user['id'],
                    'group_id' => (int)$user['field_6'],
                    'group_name' => $user['group_name'],
                    'client_id' => $user['client_id'],
                    'multiple_access_groups' => $user['multiple_access_groups'],
                    'name' => \Models\Users\Users::output_heading_from_item($user),
                    'username' => $user['field_12'],
                    'email' => $user['field_9'],
                    'is_email_verified' => $user['is_email_verified'],
                    'photo' => $photo,
                    'language' => $user['field_13'],
                    'skin' => $user['field_14'],
                    'fields' => $user,
                ];

                //generate users access to entities schema
                if (\K::f3()->app_user['group_id'] > 0) {
                    \K::f3()->app_users_access = \Models\Users\Users::get_users_access_schema(
                        \K::f3()->app_user['group_id']
                    );
                } else {
                    \K::f3()->app_users_access = [];
                }

                //set unique client id for rss or ical
                \Models\Users\Users::set_client_id();
            } else {
                //app_session_unregister('app_logged_users_id');
                \K::sessionClear('app_logged_users_id');
                \K::f3()->reroute('@Login');
                //redirect_to('users/login');
            }
        }
    }
}
/*\K::model()->db_perform(
    'app_configuration',
    ['configuration_name' => 'TEST_TEST_T2', 'configuration_value' => 123]
);*/