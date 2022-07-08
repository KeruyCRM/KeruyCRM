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
        'users/two_step_verification',
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
        'CFG_2STEP_VERIFICATION_ENABLED' => 1,
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
            \K::fw()->reroute('/set/install/index');
        }

        $this->_setSession();
        $this->_extractSession();

        $this->_setCfgIni();

        \K::fw()->mset($this->defaultCfg);

        $this->_setCfgFromDB();
        $this->_setCfg();

        //set php timezone
        \K::fw()->TZ = \K::$fw->CFG_APP_TIMEZONE;

        //set mysql timezone as it's configured for app
        \K::model()->db_query_exec("SET time_zone = '" . date('P') . "'");

        //cache vars
        $get_heading_fields = \Models\Main\Fields::get_heading_fields();
        \K::$fw->app_heading_fields_cache = $get_heading_fields['id'];
        \K::$fw->app_heading_fields_id_cache = $get_heading_fields['entities_id'];

        \K::$fw->app_not_formula_fields_cache = \Models\Main\Fields::not_formula_fields_cache();
        \K::$fw->app_formula_fields_cache = \Models\Main\Fields::formula_fields_cache();
        \K::$fw->app_fields_cache = \Models\Main\Fields::get_cache();

        \K::$fw->app_access_rules_fields_cache = \Models\Main\Access_rules::get_access_rules_fields_cache();
        \K::$fw->app_mysql_query_fields_cache = \Tools\FieldsTypes\Fieldtype_mysql_query::get_fields_cache();

        \K::$fw->app_entities_cache = \Models\Main\Entities::get_cache();
        \K::$fw->app_choices_cache = \Models\Main\Fields_choices::get_cache();
        \K::$fw->app_global_choices_cache = \Models\Main\Global_lists::get_cache();
        \K::$fw->app_access_groups_cache = \Models\Main\Access_groups::get_cache();

        //TODO num2str
        //$app_num2str = new num2str();

        \Models\Main\Custom_php::include();

        $this->_setCfgSession();
        \K::$fw->app_session_token = \K::security()->getAppSessionToken();
        \K::$fw->app_extension = \K::fw()->get('PARAMS.extensionName');

        \K::$fw->app_plugin_path = '';
        \K::$fw->app_module = \K::fw()->get('PARAMS.moduleName');
        \K::$fw->app_action = (\K::fw()->exists('PARAMS.controllerName') ? \K::fw()->get(
            'PARAMS.controllerName'
        ) : 'PARAMS.moduleName');

        if (\K::fw()->get('PARAMS.extensionName') == 'main') {
            \K::$fw->app_module_path = \K::$fw->app_module . '/' . \K::$fw->app_action;
        } else {
            \K::$fw->app_module_path = \K::fw()->get(
                    'PARAMS.extensionName'
                ) . '/' . \K::$fw->app_module . '/' . \K::$fw->app_action;
        }

        \K::$fw->app_title = (strlen(
            \K::$fw->CFG_APP_SHORT_NAME
        ) > 0 ? \K::$fw->CFG_APP_SHORT_NAME : \K::$fw->CFG_APP_NAME);

        \K::$fw->app_module_action = (\K::fw()->exists('PARAMS.actionName') ? \K::fw()->get(
            'PARAMS.actionName'
        ) : 'index');

        \K::$fw->app_redirect_to = ($_GET['redirect_to'] ?? (isset($_POST['redirect_to']) ? $_POST['redirect_to'] : ''));

        \K::$fw->app_path = ($_GET['path'] ?? (isset($_POST['path']) ? $_POST['path'] : ''));

        \K::$fw->pathSubTemplate = \K::$fw->app_extension . '/' . \K::$fw->app_module . '/';

        if (\K::$fw->CFG_USE_PUBLIC_REGISTRATION == 1) {
            $this->allowed_modules[] = 'users/registration';
            $this->allowed_modules[] = 'users/validate_form';
            $this->allowed_modules[] = 'users/registration_success';
        }

        $this->_setPlugin();//TODO include plugin
        $this->_userLogin();

        $this->_setCfgSession2();
        $this->_checkEnvironment();

        \Models\Main\Users\Two_step_verification::check();

        //email confirmation check
        \Models\Main\Users\Email_verification::check();

        //check if maintenance mode enabled
        \Tools\Maintenance_mode::check();

        //set skin
        if (strlen(\K::$fw->CFG_APP_SKIN) > 0) {
            \K::$fw->app_skin = \K::$fw->CFG_APP_SKIN . '/' . \K::$fw->CFG_APP_SKIN . '.css';
        } elseif (\K::fw()->exists('app_user')) {
            if (strlen(\K::$fw->app_user['skin']) > 0) {
                \K::$fw->app_skin = \K::$fw->app_user['skin'] . '/' . \K::$fw->app_user['skin'] . '.css';
            } else {
                \K::$fw->app_skin = 'default/default.css';
            }
        } elseif (\K::cookieExists('user_skin')) {
            \K::$fw->app_skin = \K::cookieGet('user_skin') . '/' . \K::cookieGet('user_skin') . '.css';
        } else {
            \K::$fw->app_skin = 'default/default.css';
        }

        \K::$fw->app_users_cache = \Models\Main\Users\Users::get_cache();
    }

    public function beforeroute()
    {
    }

    public function afterroute()
    {
        echo '<PRE style="white-space: pre-wrap;">' . PHP_EOL . \K::model()->db->log() . '</PRE>';
    }

    public function _extractSession()
    {
        foreach (\K::fw()->SESSION as $key => $value) {
            \K::fw()->{$key} = $value;
            \K::fw()->SESSION[$key] = &\K::fw()->{$key};
        }
    }

    private function _setSession()
    {
        /*
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
        $SESS_LIFE = 1440;
    }
        */
        if (\K::$fw->STORE_SESSIONS == 'mysql') {
            new \DB\SQL\Session(\K::model()->db, 'app_sessions_new', false, function ($session) {
                if (\K::$fw->CFG_SESSION_CHECK_IP and $session->ip() !== \K::$fw->IP) {
                    return false;
                }
                if (\K::$fw->CFG_SESSION_CHECK_BROWSER and $session->agent() !== \K::$fw->AGENT) {
                    return false;
                }
                return true;
            });
        } else {
            $sessionCache = new \Cache(\K::$fw->SESSION_WRITE_DIRECTORY); // Session cache
            new \Session(function ($session) {
                if (\K::$fw->CFG_SESSION_CHECK_IP and $session->ip() !== \K::$fw->IP) {
                    return false;
                }
                if (\K::$fw->CFG_SESSION_CHECK_BROWSER and $session->agent() !== \K::$fw->AGENT) {
                    return false;
                }
                return true;
            }, null, $sessionCache);
        }
    }

    private function _setCfgFromDB()
    {
        $cfg = \K::model()->db_fetch_all('app_configuration', null, [\K::$fw->TTL_APP, 'app_configuration']);

        foreach ($cfg as $v) {
            \K::$fw->{$v->configuration_name} = $v->configuration_value;
        }
    }

    private function _setCfg()
    {
        \K::fw()->mset(
            [
                'DIR_FS_BACKUPS_AUTO' => \K::$fw->DIR_FS_CATALOG . 'backups/auto/',
                'CFG_GLOBAL_SEARCH_ROWS_PER_PAGE' => \K::$fw->CFG_APP_ROWS_PER_PAGE,
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
        if (!\K::app_session_is_registered('uploadify_attachments')) {
            \K::$fw->uploadify_attachments = [];
            \K::app_session_register('uploadify_attachments');
        }

        if (!\K::app_session_is_registered('uploadify_attachments_queue')) {
            \K::$fw->uploadify_attachments_queue = [];
            \K::app_session_register('uploadify_attachments_queue');
        }

        // create the alerts object
        /*if (!app_session_is_registered('alerts') || !is_object($alerts)) {
            app_session_register('alerts');
            $alerts = new alerts;
        }*/

        if (!\K::app_session_is_registered('app_send_to')) {
            \K::app_session_register('app_send_to');
            \K::$fw->app_send_to = [];
        }

        if (!\K::app_session_is_registered('app_token')) {
            \K::$fw->app_token = \K::security()->getAppToken();
            \K::app_session_register('app_token');
        }

        /*if (!\K::app_session_is_registered('app_session_token')) {
            \K::$fw->app_session_token = \K::security()->genAppSessionToken();
            \K::app_session_register('app_session_token');
        }*/

        if (!\K::app_session_is_registered('app_current_users_filter')) {
            \K::$fw->app_current_users_filter = [];
            \K::app_session_register('app_current_users_filter');
        }

        if (!\K::app_session_is_registered('app_previously_logged_user')) {
            \K::$fw->app_previously_logged_user = 0;
            \K::app_session_register('app_previously_logged_user');
        }

        if (!\K::app_session_is_registered('two_step_verification_info')) {
            \K::$fw->two_step_verification_info = [];
            \K::app_session_register('two_step_verification_info');
        }

        if (!\K::app_session_is_registered('app_email_verification_code')) {
            \K::$fw->app_email_verification_code = '';
            \K::app_session_register('app_email_verification_code');
        }

        if (!\K::app_session_is_registered('app_force_print_template')) {
            \K::app_session_register('app_force_print_template');
            \K::$fw->app_force_print_template = false;
        }
    }

    private function _setCfgSession2()
    {
        if (!\K::app_session_is_registered('app_current_version')) {
            \K::$fw->app_current_version = '';
            \K::app_session_register('app_current_version');
        }

        if (\K::$fw->CFG_DISABLE_CHECK_FOR_UPDATES == 1) {
            \K::$fw->app_current_version = '';
        }

        if (!\K::app_session_is_registered('app_selected_items')) {
            \K::$fw->app_selected_items = [];
            \K::app_session_register('app_selected_items');
        }

        if (!\K::app_session_is_registered('listing_page_keeper')) {
            \K::$fw->listing_page_keeper = [];
            \K::app_session_register('listing_page_keeper');
        }

        if (!\K::app_session_is_registered('user_roles_dropdown_change_holder')) {
            \K::$fw->user_roles_dropdown_change_holder = [];
            \K::app_session_register('user_roles_dropdown_change_holder');
        }

        if (!\K::app_session_is_registered('app_subentity_form_items')) {
            \K::$fw->app_subentity_form_items = [];
            \K::app_session_register('app_subentity_form_items');
        }

        if (!\K::app_session_is_registered('app_subentity_form_items_deleted')) {
            \K::$fw->app_subentity_form_items_deleted = [];
            \K::app_session_register('app_subentity_form_items_deleted');
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
                $error_list[] = sprintf(\K::$fw->TEXT_ERROR_LIB, strtoupper($ext));
            }
        }

        //check folder
        $check_folders = [
            \K::$fw->DIR_FS_UPLOADS,
            \K::$fw->DIR_FS_ATTACHMENTS,
            \K::$fw->DIR_FS_ATTACHMENTS_PREVIEW,
            \K::$fw->DIR_FS_IMAGES,
            \K::$fw->DIR_FS_USERS,
            \K::$fw->DIR_FS_BACKUPS,
            \K::$fw->DIR_FS_TMP,
            \K::$fw->DIR_FS_CACHE,
            \K::$fw->DIR_FS_CATALOG . 'log/'
        ];

        foreach ($check_folders as $v) {
            if (is_dir($v)) {
                if (!is_writable($v)) {
                    $error_list[] = sprintf(
                        'Error: folder "%s" is not writable!',
                        str_replace(\K::$fw->DIR_FS_CATALOG, '', $v)
                    );
                }
            } else {
                $error_list[] = sprintf(
                    'Error: folder "%s" does not exist',
                    str_replace(\K::$fw->DIR_FS_CATALOG, '', $v)
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

        if (!\K::app_session_is_registered('app_logged_users_id') and !in_array(
                \K::$fw->app_module_path,
                $this->allowed_modules
            )) {
            //allows redirect user to current page after login if there is no any actions

            if (!\K::fw()->exists('GET.action') and !\K::fw()->exists('POST.action') and !\K::$fw->AJAX) {
                \K::cookieSet('app_login_redirect_to', \K::$fw->URI, 10 * 60);
            }
            //if (isset($_COOKIE["app_remember_me"]) and isset($_COOKIE["app_stay_logged"])) {
            if (\K::cookieExists('app_remember_me') and \K::cookieExists('app_stay_logged')) {
                //do not ask verification do if login by remember me function
                \K::$fw->two_step_verification_info['is_checked'] = true;

                \Models\Main\Users\Users::login(
                    base64_decode(\K::cookieGet('app_remember_user')),
                    '',
                    1,
                    base64_decode(\K::cookieGet('app_remember_pass'))
                );
            } else {
                \Helpers\Urls::redirect_to('main/users/login');
            }
        } elseif (\K::app_session_is_registered('app_logged_users_id')) {
            $user_query = \Models\Main\Users\Users::getGroupAndAccessByUserId(\K::$fw->app_logged_users_id);

            if (isset($user_query[0])) {
                $user = $user_query[0];

                if (strlen($user['field_10']) > 0) {
                    $file = \Tools\Attachments::parse_filename($user['field_10']);
                    $photo = $file['file_sha1'];
                } else {
                    $photo = '';
                }

                \K::$fw->app_user = [
                    'id' => $user['id'],
                    'group_id' => (int)$user['field_6'],
                    'group_name' => $user['group_name'],
                    'client_id' => $user['client_id'],
                    'multiple_access_groups' => $user['multiple_access_groups'],
                    'name' => \Models\Main\Users\Users::output_heading_from_item($user),
                    'username' => $user['field_12'],
                    'email' => $user['field_9'],
                    'is_email_verified' => $user['is_email_verified'],
                    'photo' => $photo,
                    'language' => $user['field_13'],
                    'skin' => $user['field_14'],
                    'fields' => $user,
                ];

                //generate users access to entities schema
                if (\K::$fw->app_user['group_id'] > 0) {
                    \K::$fw->app_users_access = \Models\Main\Users\Users::get_users_access_schema(
                        \K::$fw->app_user['group_id']
                    );
                } else {
                    \K::$fw->app_users_access = [];
                }

                //set unique client id for rss or ical
                \Models\Main\Users\Users::set_client_id();
            } else {
                \K::app_session_unregister('app_logged_users_id');
                \Helpers\Urls::redirect_to('main/users/login');
            }
        }
    }
}
/*\K::model()->db_perform(
    'app_configuration',
    ['configuration_name' => 'TEST_TEST_T2', 'configuration_value' => 123]
);*/
//backward compatibility
//value_displya_own_column
//dinamic_query
//calclulate_diff_days
//calclulate_totals