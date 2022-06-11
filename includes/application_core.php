<?php

define('PROJECT_VERSION', '1.0.1');
define('PROJECT_VERSION_DEV', '');

//is HTTPS
define('IS_HTTPS', (isset($_SERVER['HTTPS']) ? (strtolower($_SERVER['HTTPS']) == 'on' ? true : false) : false));

//check HTTP_HOST    
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '';

require('config/server.php');
require('config/security.php');

if (!defined('CFG_SLOW_QUERY_TIME')) {
    define('CFG_SLOW_QUERY_TIME', 0.1);
}

require('config/database.php');

// set the level of error reporting
if (DEV_MODE) {
    error_reporting(E_ALL);
} else {
    error_reporting(E_ALL & ~E_NOTICE);
}

$app_db_query_log = [];
$app_db_slow_query_log = [];

//path to libs
define('CFG_PATH_TO_DOMPDF', 'includes/libs/dompdf/1.1.1/vendor/autoload.php');
define('CFG_PATH_TO_DOMPDF_FONTS', 'includes/libs/dompdf/fonts/');

define('CFG_PATH_TO_PHPWORD', 'includes/libs/PHPWord/0.18.2/vendor/autoload.php');
define('CFG_PATH_TO_PHPSPREADSHEET', 'includes/libs/PHPSpreadsheet/1.18.0/vendor/autoload.php');


//include classes
require('includes/classes/backup.php');
require('includes/classes/alerts.php');
require('includes/classes/attachments.php');
require('includes/classes/cache.php');
require('includes/classes/fields_types.php');
require('includes/classes/fields_types_cfg.php');
require('includes/classes/related_records.php');
require('includes/classes/ldap_login.php');
require('includes/classes/split_page.php');
require('includes/classes/plugins.php');
require('includes/classes/session.php');
require('includes/classes/entities_cfg.php');
require('includes/classes/listing_search.php');
require('includes/classes/maintenance_mode.php');
require('includes/classes/app_recaptcha.php');
require('includes/classes/app_restricted_countries.php');
require('includes/classes/app_restricted_ip.php');
require('includes/classes/_get.php');
require('includes/classes/_post.php');
require('includes/classes/users/users_alerts.php');
require('includes/classes/num2str.php');
require('includes/classes/maps/image_map.php');
require('includes/classes/maps/image_map_nested.php');
require('includes/classes/maps/mind_map.php');
require('includes/classes/settings.php');
require('includes/classes/csrf_protect.php');
require('includes/classes/global_vars.php');
require('includes/classes/custom_php.php');

//include field types
require('includes/classes/fieldstypes/fieldtype_action.php');
require('includes/classes/fieldstypes/fieldtype_attachments.php');
require('includes/classes/fieldstypes/fieldtype_checkboxes.php');
require('includes/classes/fieldstypes/fieldtype_created_by.php');
require('includes/classes/fieldstypes/fieldtype_date_added.php');
require('includes/classes/fieldstypes/fieldtype_dropdown.php');
require('includes/classes/fieldstypes/fieldtype_dropdown_multiple.php');
require('includes/classes/fieldstypes/fieldtype_progress.php');
require('includes/classes/fieldstypes/fieldtype_entity.php');
require('includes/classes/fieldstypes/fieldtype_formula.php');
require('includes/classes/fieldstypes/fieldtype_grouped_users.php');
require('includes/classes/fieldstypes/fieldtype_id.php');
require('includes/classes/fieldstypes/fieldtype_parent_item_id.php');
require('includes/classes/fieldstypes/fieldtype_input.php');
require('includes/classes/fieldstypes/fieldtype_input_date.php');
require('includes/classes/fieldstypes/fieldtype_input_datetime.php');
require('includes/classes/fieldstypes/fieldtype_input_file.php');
require('includes/classes/fieldstypes/fieldtype_input_numeric.php');
require('includes/classes/fieldstypes/fieldtype_input_numeric_comments.php');
require('includes/classes/fieldstypes/fieldtype_input_url.php');
require('includes/classes/fieldstypes/fieldtype_radioboxes.php');
require('includes/classes/fieldstypes/fieldtype_textarea.php');
require('includes/classes/fieldstypes/fieldtype_textarea_wysiwyg.php');
require('includes/classes/fieldstypes/fieldtype_users.php');
require('includes/classes/fieldstypes/fieldtype_user_accessgroups.php');
require('includes/classes/fieldstypes/fieldtype_user_email.php');
require('includes/classes/fieldstypes/fieldtype_user_firstname.php');
require('includes/classes/fieldstypes/fieldtype_user_language.php');
require('includes/classes/fieldstypes/fieldtype_user_lastname.php');
require('includes/classes/fieldstypes/fieldtype_user_photo.php');
require('includes/classes/fieldstypes/fieldtype_user_skin.php');
require('includes/classes/fieldstypes/fieldtype_user_status.php');
require('includes/classes/fieldstypes/fieldtype_user_username.php');
require('includes/classes/fieldstypes/fieldtype_related_records.php');
require('includes/classes/fieldstypes/fieldtype_input_masked.php');
require('includes/classes/fieldstypes/fieldtype_image.php');
require('includes/classes/fieldstypes/fieldtype_image_ajax.php');
require('includes/classes/fieldstypes/fieldtype_boolean.php');
require('includes/classes/fieldstypes/fieldtype_text_pattern.php');
require('includes/classes/fieldstypes/fieldtype_input_vpic.php');
require('includes/classes/fieldstypes/fieldtype_mapbbcode.php');
require('includes/classes/fieldstypes/fieldtype_barcode.php');
require('includes/classes/fieldstypes/fieldtype_qrcode.php');
require('includes/classes/fieldstypes/fieldtype_input_email.php');
require('includes/classes/fieldstypes/fieldtype_section.php');
require('includes/classes/fieldstypes/fieldtype_random_value.php');
require('includes/classes/fieldstypes/fieldtype_dropdown_multilevel.php');
require('includes/classes/fieldstypes/fieldtype_autostatus.php');
require('includes/classes/fieldstypes/fieldtype_js_formula.php');
require('includes/classes/fieldstypes/fieldtype_todo_list.php');
require('includes/classes/fieldstypes/fieldtype_parent_value.php');
require('includes/classes/fieldstypes/fieldtype_mysql_query.php');
require('includes/classes/fieldstypes/fieldtype_image_map.php');
require('includes/classes/fieldstypes/fieldtype_mind_map.php');
require('includes/classes/fieldstypes/fieldtype_years_difference.php');
require('includes/classes/fieldstypes/fieldtype_days_difference.php');
require('includes/classes/fieldstypes/fieldtype_hours_difference.php');
require('includes/classes/fieldstypes/fieldtype_boolean_checkbox.php');
require('includes/classes/fieldstypes/fieldtype_auto_increment.php');
require('includes/classes/fieldstypes/fieldtype_text_pattern_static.php');
require('includes/classes/fieldstypes/fieldtype_user_last_login_date.php');
require('includes/classes/fieldstypes/fieldtype_phone.php');
require('includes/classes/fieldstypes/fieldtype_date_updated.php');
require('includes/classes/fieldstypes/fieldtype_google_map.php');
require('includes/classes/fieldstypes/fieldtype_input_protected.php');
require('includes/classes/fieldstypes/fieldtype_tags.php');
require('includes/classes/fieldstypes/fieldtype_entity_ajax.php');
require('includes/classes/fieldstypes/fieldtype_user_roles.php');
require('includes/classes/fieldstypes/fieldtype_entity_multilevel.php');
require('includes/classes/fieldstypes/fieldtype_months_difference.php');
require('includes/classes/fieldstypes/fieldtype_users_approve.php');
require('includes/classes/fieldstypes/fieldtype_google_map_directions.php');
require('includes/classes/fieldstypes/fieldtype_dynamic_date.php');
require('includes/classes/fieldstypes/fieldtype_access_group.php');
require('includes/classes/fieldstypes/fieldtype_signature.php');
require('includes/classes/fieldstypes/fieldtype_stages.php');
require('includes/classes/fieldstypes/fieldtype_iframe.php');
require('includes/classes/fieldstypes/fieldtype_time.php');
require('includes/classes/fieldstypes/fieldtype_digital_signature.php');
require('includes/classes/fieldstypes/fieldtype_ajax_request.php');
require('includes/classes/fieldstypes/fieldtype_users_ajax.php');
require('includes/classes/fieldstypes/fieldtype_items_by_query.php');
require('includes/classes/fieldstypes/fieldtype_php_code.php');
require('includes/classes/fieldstypes/fieldtype_process_button.php');
require('includes/classes/fieldstypes/fieldtype_video.php');
require('includes/classes/fieldstypes/fieldtype_input_encrypted.php');
require('includes/classes/fieldstypes/fieldtype_textarea_encrypted.php');
require('includes/classes/fieldstypes/fieldtype_jalali_calendar.php');
require('includes/classes/fieldstypes/fieldtype_subentity_form.php');
require('includes/classes/fieldstypes/fieldtype_input_ip.php');
require('includes/classes/fieldstypes/fieldtype_input_dynamic_mask.php');
require('includes/classes/fieldstypes/fieldtype_nested_calculations.php');
require('includes/classes/fieldstypes/fieldtype_color.php');
require('includes/classes/fieldstypes/fieldtype_image_map_nested.php');
require('includes/classes/fieldstypes/fieldtype_yandex_map.php');


//include models
require('includes/classes/model/access_groups.php');
require('includes/classes/model/comments.php');
require('includes/classes/model/entities.php');
require('includes/classes/model/entities_groups.php');
require('includes/classes/model/fields.php');
require('includes/classes/model/fields_choices.php');
require('includes/classes/model/forms_tabs.php');
require('includes/classes/model/comments_forms_tabs.php');
require('includes/classes/model/choices_values.php');
require('includes/classes/model/global_lists.php');
require('includes/classes/model/configuration.php');
require('includes/classes/model/forms_fields_rules.php');
require('includes/classes/model/access_rules.php');
require('includes/classes/model/entities_menu.php');
require('includes/classes/model/listing_types.php');
require('includes/classes/model/holidays.php');
require('includes/classes/model/forms_rows.php');
require('includes/classes/model/portlets.php');

//users 
require('includes/classes/users/users.php');
require('includes/classes/users/users_cfg.php');
require('includes/classes/users/users_notifications.php');
require('includes/classes/users/users_login_log.php');
require('includes/classes/users/user_roles.php');
require('includes/classes/users/records_visibility.php');
require('includes/classes/users/2step_verification.php');
require('includes/classes/users/public_registration.php');
require('includes/classes/users/email_verification.php');
require('includes/classes/users/guest_login.php');

//items
require('includes/classes/items/items.php');
require('includes/classes/items/items_search.php');
require('includes/classes/items/items_listing.php');
require('includes/classes/items/items_copy.php');
require('includes/classes/items/approved_items.php');
require('includes/classes/items/stages_panel.php');
require('includes/classes/items/listing_highlight.php');
require('includes/classes/items/items_page.php');
require('includes/classes/items/subentity_form.php');
require('includes/classes/items/tree_table.php');
require('includes/classes/items/editable_listing.php');
require('includes/classes/items/favorites.php');
require('includes/classes/items/items_redirects.php');
require('includes/classes/items/forms_wizard.php');
require('includes/classes/items/items_filters.php');

//reports	
require('includes/classes/reports/reports.php');
require('includes/classes/reports/hot_reports.php');
require('includes/classes/reports/filters_preview.php');
require('includes/classes/reports/users_filters.php');
require('includes/classes/reports/reports_counter.php');
require('includes/classes/reports/reports_notification.php');
require('includes/classes/reports/reports_sections.php');
require('includes/classes/reports/filters_panels.php');
require('includes/classes/reports/default_filters.php');
require('includes/classes/reports/reports_groups.php');

//pages	
require('includes/classes/pages/dashboard_pages.php');
require('includes/classes/pages/help_pages.php');

//include functions
require('includes/functions/app.php');
require('includes/functions/database.php');
require('includes/functions/html.php');
require('includes/functions/menu.php');
require('includes/functions/sessions.php');
require('includes/functions/urls.php');
require('includes/functions/validations.php');

//include libs
require('includes/libs/PasswordHash.php');
require('includes/libs/htmlpurifier-4.12.0/library/HTMLPurifier.auto.php');
require('includes/libs/php-barcode-generator-master/src/Barcode.php');
require('includes/libs/php-barcode-generator-master/src/BarcodeBar.php');
require('includes/libs/php-barcode-generator-master/src/BarcodeGenerator.php');
require('includes/libs/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
require('includes/libs/phpqrcode-master/qrlib.php');

//PHPMailer	        
require 'includes/libs/PHPMailer/6.5.1/vendor/autoload.php';
require('includes/libs/PHPMailer/extras/Html2Text.php');

//NCLNameCase        
require('includes/libs/NameCase/0.4.1/Library/NCLNameCaseRu.php');
require('includes/libs/NameCase/0.4.1/Library/NCLNameCaseUa.php');

//set custom error handler
if (DEV_MODE) {
    set_error_handler('app_error_handler');
}

// make a connection to the database...
if (!defined('DB_SERVER_PORT')) {
    define('DB_SERVER_PORT', '');
}

db_connect();

// set the application parameters
$cfg_query = db_fetch_all('app_configuration');
while ($v = db_fetch_array($cfg_query)) {
    define($v['configuration_name'], $v['configuration_value']);
}

$app_global_vars = new global_vars();

//configuration added in next versions
if (!defined('CFG_APP_FIRST_DAY_OF_WEEK')) {
    define('CFG_APP_FIRST_DAY_OF_WEEK', 0);
}
if (!defined('CFG_APP_LOGIN_PAGE_BACKGROUND')) {
    define('CFG_APP_LOGIN_PAGE_BACKGROUND', '');
}
if (!defined('CFG_APP_DISPLAY_USER_NAME_ORDER')) {
    define('CFG_APP_DISPLAY_USER_NAME_ORDER', 'firstname_lastname');
}
if (!defined('CFG_APP_COPYRIGHT_NAME')) {
    define('CFG_APP_COPYRIGHT_NAME', '');
}
if (!defined('CFG_APP_NUMBER_FORMAT')) {
    define('CFG_APP_NUMBER_FORMAT', '2/./*');
}
if (!defined('CFG_APP_LOGO_URL')) {
    define('CFG_APP_LOGO_URL', '');
}
if (!defined('CFG_ALLOW_CHANGE_USERNAME')) {
    define('CFG_ALLOW_CHANGE_USERNAME', 0);
}
if (!defined('CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL')) {
    define('CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL', 0);
}
if (!defined('CFG_MAINTENANCE_MODE')) {
    define('CFG_MAINTENANCE_MODE', 0);
}
if (!defined('CFG_MAINTENANCE_MESSAGE_HEADING')) {
    define('CFG_MAINTENANCE_MESSAGE_HEADING', '');
}
if (!defined('CFG_MAINTENANCE_MESSAGE_CONTENT')) {
    define('CFG_MAINTENANCE_MESSAGE_CONTENT', '');
}
if (!defined('CFG_APP_LOGIN_MAINTENANCE_BACKGROUND')) {
    define('CFG_APP_LOGIN_MAINTENANCE_BACKGROUND', '');
}
if (!defined('CFG_RESIZE_IMAGES')) {
    define('CFG_RESIZE_IMAGES', 0);
}
if (!defined('CFG_MAX_IMAGE_WIDTH')) {
    define('CFG_MAX_IMAGE_WIDTH', 1600);
}
if (!defined('CFG_MAX_IMAGE_HEIGHT')) {
    define('CFG_MAX_IMAGE_HEIGHT', 900);
}
if (!defined('CFG_RESIZE_IMAGES_TYPES')) {
    define('CFG_RESIZE_IMAGES_TYPES', '2');
}
if (!defined('CFG_SKIP_IMAGE_RESIZE')) {
    define('CFG_SKIP_IMAGE_RESIZE', '5000');
}
if (!defined('CFG_NOTIFICATIONS_SCHEDULE')) {
    define('CFG_NOTIFICATIONS_SCHEDULE', 0);
}
if (!defined('CFG_SEND_EMAILS_ON_SCHEDULE')) {
    define('CFG_SEND_EMAILS_ON_SCHEDULE', 0);
}
if (!defined('CFG_MAXIMUM_NUMBER_EMAILS')) {
    define('CFG_MAXIMUM_NUMBER_EMAILS', 3);
}
if (!defined('CFG_USE_PUBLIC_REGISTRATION')) {
    define('CFG_USE_PUBLIC_REGISTRATION', 0);
}
if (!defined('CFG_PUBLIC_REGISTRATION_USER_GROUP')) {
    define('CFG_PUBLIC_REGISTRATION_USER_GROUP', '');
}
if (!defined('CFG_PUBLIC_REGISTRATION_PAGE_HEADING')) {
    define('CFG_PUBLIC_REGISTRATION_PAGE_HEADING', '');
}
if (!defined('CFG_PUBLIC_REGISTRATION_PAGE_CONTENT')) {
    define('CFG_PUBLIC_REGISTRATION_PAGE_CONTENT', '');
}
if (!defined('CFG_REGISTRATION_BUTTON_TITLE')) {
    define('CFG_REGISTRATION_BUTTON_TITLE', '');
}
if (!defined('CFG_APP_DISABLE_CHANGE_PWD')) {
    define('CFG_APP_DISABLE_CHANGE_PWD', '');
}
if (!defined('CFG_LOGIN_PAGE_HIDE_REMEMBER_ME')) {
    define('CFG_LOGIN_PAGE_HIDE_REMEMBER_ME', 0);
}
if (!defined('CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS')) {
    define('CFG_PUBLIC_REGISTRATION_HIDDEN_FIELDS', '');
}
if (!defined('CFG_USE_API')) {
    define('CFG_USE_API', 0);
}
if (!defined('CFG_API_KEY')) {
    define('CFG_API_KEY', '');
}
if (!defined('CFG_DISABLE_CHECK_FOR_UPDATES')) {
    define('CFG_DISABLE_CHECK_FOR_UPDATES', 0);
}
if (!defined('CFG_REGISTRATION_NOTIFICATION_USERS')) {
    define('CFG_REGISTRATION_NOTIFICATION_USERS', '');
}
if (!defined('CFG_USE_CACHE_REPORTS_IN_HEADER')) {
    define('CFG_USE_CACHE_REPORTS_IN_HEADER', 0);
}
if (!defined('CFG_CACHE_REPORTS_IN_HEADER_LIFETIME')) {
    define('CFG_CACHE_REPORTS_IN_HEADER_LIFETIME', 300);
}
if (!defined('CFG_LDAP_FIRSTNAME_ATTRIBUTE')) {
    define('CFG_LDAP_FIRSTNAME_ATTRIBUTE', '');
}
if (!defined('CFG_LDAP_LASTNAME_ATTRIBUTE')) {
    define('CFG_LDAP_LASTNAME_ATTRIBUTE', '');
}
if (!defined('CFG_PUBLIC_REGISTRATION_USER_AGREEMENT')) {
    define('CFG_PUBLIC_REGISTRATION_USER_AGREEMENT', '');
}
if (!defined('CFG_ENCRYPT_FILE_NAME')) {
    define('CFG_ENCRYPT_FILE_NAME', 1);
}
if (!defined('CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS')) {
    define('CFG_MAINTENANCE_ALLOW_LOGIN_FOR_USERS', '');
}
if (!defined('CFG_USE_GLOBAL_SEARCH')) {
    define('CFG_USE_GLOBAL_SEARCH', 0);
}
if (!defined('CFG_GLOBAL_SEARCH_ALLOWED_GROUPS')) {
    define('CFG_GLOBAL_SEARCH_ALLOWED_GROUPS', '');
}
if (!defined('CFG_GLOBAL_SEARCH_ROWS_PER_PAGE')) {
    define('CFG_GLOBAL_SEARCH_ROWS_PER_PAGE', CFG_APP_ROWS_PER_PAGE);
}
if (!defined('CFG_GLOBAL_SEARCH_INPUT_MIN')) {
    define('CFG_GLOBAL_SEARCH_INPUT_MIN', 3);
}
if (!defined('CFG_GLOBAL_SEARCH_INPUT_MAX')) {
    define('CFG_GLOBAL_SEARCH_INPUT_MAX', 40);
}
if (!defined('CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER')) {
    define('CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER', 0);
}
if (!defined('CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU')) {
    define('CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU', 0);
}
if (!defined('CFG_PUBLIC_ATTACHMENTS')) {
    define('CFG_PUBLIC_ATTACHMENTS', '');
}
if (!defined('CFG_LOGIN_DIGITAL_SIGNATURE_MODULE')) {
    define('CFG_LOGIN_DIGITAL_SIGNATURE_MODULE', '');
}
if (!defined('CFG_2STEP_VERIFICATION_ENABLED')) {
    define('CFG_2STEP_VERIFICATION_ENABLED', 0);
}
if (!defined('CFG_2STEP_VERIFICATION_TYPE')) {
    define('CFG_2STEP_VERIFICATION_TYPE', 'email');
}
if (!defined('CFG_2STEP_VERIFICATION_SMS_MODULE')) {
    define('CFG_2STEP_VERIFICATION_SMS_MODULE', '');
}
if (!defined('CFG_2STEP_VERIFICATION_USER_PHONE')) {
    define('CFG_2STEP_VERIFICATION_USER_PHONE', '');
}
if (!defined('CFG_LOGIN_BY_PHONE_NUMBER')) {
    define('CFG_LOGIN_BY_PHONE_NUMBER', 0);
}
if (!defined('CFG_PUBLIC_REGISTRATION_USER_ACTIVATION')) {
    define('CFG_PUBLIC_REGISTRATION_USER_ACTIVATION', 'automatic');
}
if (!defined('CFG_REGISTRATION_SUCCESS_PAGE_HEADING')) {
    define('CFG_REGISTRATION_SUCCESS_PAGE_HEADING', '');
}
if (!defined('CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION')) {
    define('CFG_REGISTRATION_SUCCESS_PAGE_DESCRIPTION', '');
}
if (!defined('CFG_USER_ACTIVATION_EMAIL_SUBJECT')) {
    define('CFG_USER_ACTIVATION_EMAIL_SUBJECT', '');
}
if (!defined('CFG_USER_ACTIVATION_EMAIL_BODY')) {
    define('CFG_USER_ACTIVATION_EMAIL_BODY', '');
}
if (!defined('CFG_HIDE_POWERED_BY_TEXT')) {
    define('CFG_HIDE_POWERED_BY_TEXT', 0);
}
if (!defined('CFG_APP_FAVICON')) {
    define('CFG_APP_FAVICON', '');
}
if (!defined('CFG_CREATE_ATTACHMENTS_PREVIEW')) {
    define('CFG_CREATE_ATTACHMENTS_PREVIEW', 0);
}
if (!defined('CFG_DISPLAY_USER_GROUP_IN_MENU')) {
    define('CFG_DISPLAY_USER_GROUP_IN_MENU', 0);
}
if (!defined('CFG_DISPLAY_USER_GROUP_ID_IN_MENU')) {
    define('CFG_DISPLAY_USER_GROUP_ID_IN_MENU', '');
}
if (!defined('CFG_ENABLE_MULTIPLE_ACCESS_GROUPS')) {
    define('CFG_ENABLE_MULTIPLE_ACCESS_GROUPS', 0);
}
if (!defined('CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS')) {
    define('CFG_USE_PUBLIC_REGISTRATION_MULTIPLE_USER_GROUPS', 0);
}
if (!defined('CFG_ENABLE_SOCIAL_LOGIN')) {
    define('CFG_ENABLE_SOCIAL_LOGIN', 0);
}
if (!defined('CFG_ENABLE_VKONTAKTE_LOGIN')) {
    define('CFG_ENABLE_VKONTAKTE_LOGIN', 0);
}
if (!defined('CFG_VKONTAKTE_APP_ID')) {
    define('CFG_VKONTAKTE_APP_ID', '');
}
if (!defined('CFG_VKONTAKTE_SECRET_KEY')) {
    define('CFG_VKONTAKTE_SECRET_KEY', '');
}
if (!defined('CFG_VKONTAKTE_BUTTON_TITLE')) {
    define('CFG_VKONTAKTE_BUTTON_TITLE', '');
}
if (!defined('CFG_SOCAL_LOGIN_CREATE_USER')) {
    define('CFG_SOCAL_LOGIN_CREATE_USER', 'autocreate');
}
if (!defined('CFG_SOCAL_LOGIN_USER_GROUP')) {
    define('CFG_SOCAL_LOGIN_USER_GROUP', '');
}
if (!defined('CFG_ENABLE_GOOGLE_LOGIN')) {
    define('CFG_ENABLE_GOOGLE_LOGIN', 0);
}
if (!defined('CFG_GOOGLE_APP_ID')) {
    define('CFG_GOOGLE_APP_ID', '');
}
if (!defined('CFG_GOOGLE_SECRET_KEY')) {
    define('CFG_GOOGLE_SECRET_KEY', '');
}
if (!defined('CFG_GOOGLE_BUTTON_TITLE')) {
    define('CFG_GOOGLE_BUTTON_TITLE', '');
}
if (!defined('CFG_ENABLE_FACEBOOK_LOGIN')) {
    define('CFG_ENABLE_FACEBOOK_LOGIN', 0);
}
if (!defined('CFG_FACEBOOK_APP_ID')) {
    define('CFG_FACEBOOK_APP_ID', '');
}
if (!defined('CFG_FACEBOOK_SECRET_KEY')) {
    define('CFG_FACEBOOK_SECRET_KEY', '');
}
if (!defined('CFG_FACEBOOK_BUTTON_TITLE')) {
    define('CFG_FACEBOOK_BUTTON_TITLE', '');
}
if (!defined('CFG_ENABLE_LINKEDIN_LOGIN')) {
    define('CFG_ENABLE_LINKEDIN_LOGIN', 0);
}
if (!defined('CFG_LINKEDIN_APP_ID')) {
    define('CFG_LINKEDIN_APP_ID', '');
}
if (!defined('CFG_LINKEDIN_SECRET_KEY')) {
    define('CFG_LINKEDIN_SECRET_KEY', '');
}
if (!defined('CFG_LINKEDIN_BUTTON_TITLE')) {
    define('CFG_LINKEDIN_BUTTON_TITLE', '');
}
if (!defined('CFG_ENABLE_TWITTER_LOGIN')) {
    define('CFG_ENABLE_TWITTER_LOGIN', 0);
}
if (!defined('CFG_TWITTER_APP_ID')) {
    define('CFG_TWITTER_APP_ID', '');
}
if (!defined('CFG_TWITTER_SECRET_KEY')) {
    define('CFG_TWITTER_SECRET_KEY', '');
}
if (!defined('CFG_TWITTER_BUTTON_TITLE')) {
    define('CFG_TWITTER_BUTTON_TITLE', '');
}
if (!defined('CFG_ENABLE_GUEST_LOGIN')) {
    define('CFG_ENABLE_GUEST_LOGIN', 0);
}
if (!defined('CFG_GUEST_LOGIN_USER')) {
    define('CFG_GUEST_LOGIN_USER', '');
}
if (!defined('CFG_GUEST_LOGIN_BUTTON_TITLE')) {
    define('CFG_GUEST_LOGIN_BUTTON_TITLE', '');
}
if (!defined('CFG_PUBLIC_CALENDAR_ICAL')) {
    define('CFG_PUBLIC_CALENDAR_ICAL', 0);
}
if (!defined('CFG_PERSONAL_CALENDAR_ICAL')) {
    define('CFG_PERSONAL_CALENDAR_ICAL', 0);
}
if (!defined('CFG_IS_STRONG_PASSWORD')) {
    define('CFG_IS_STRONG_PASSWORD', 0);
}
if (!defined('CFG_EMAIL_SMTP_DEBUG')) {
    define('CFG_EMAIL_SMTP_DEBUG', 0);
}
if (!defined('CFG_DROP_DOWN_MENU_ON_HOVER')) {
    define('CFG_DROP_DOWN_MENU_ON_HOVER', 0);
}
if (!defined('CFG_CUSTOM_HTML_HEAD')) {
    define('CFG_CUSTOM_HTML_HEAD', '');
}
if (!defined('CFG_CUSTOM_HTML_BODY')) {
    define('CFG_CUSTOM_HTML_BODY', '');
}
if (!defined('DIR_FS_BACKUPS_AUTO')) {
    define('DIR_FS_BACKUPS_AUTO', DIR_FS_CATALOG . 'backups/auto/');
}
if (!defined('CFG_AUTOBACKUP_KEEP_FILES_DAYS')) {
    define('CFG_AUTOBACKUP_KEEP_FILES_DAYS', 30);
}
if (!defined('CFG_EMAIL_HTML_LAYOUT')) {
    define('CFG_EMAIL_HTML_LAYOUT', '');
}
if (!defined('CFG_USE_EMAIL_HTML_LAYOUT')) {
    define('CFG_USE_EMAIL_HTML_LAYOUT', 0);
}

if (!defined('CFG_ENABLE_STEAM_LOGIN')) {
    define('CFG_ENABLE_STEAM_LOGIN', 0);
}
if (!defined('CFG_STEAM_API_KEY')) {
    define('CFG_STEAM_API_KEY', '');
}
if (!defined('CFG_STEAM_DOMAIN')) {
    define('CFG_STEAM_DOMAIN', '');
}
if (!defined('CFG_STEAM_BUTTON_TITLE')) {
    define('CFG_STEAM_BUTTON_TITLE', '');
}

if (!defined('CFG_ENABLE_YANDEX_LOGIN')) {
    define('CFG_ENABLE_YANDEX_LOGIN', 0);
}
if (!defined('CFG_YANDEX_APP_ID')) {
    define('CFG_YANDEX_APP_ID', '');
}
if (!defined('CFG_YANDEX_SECRET_KEY')) {
    define('CFG_YANDEX_SECRET_KEY', '');
}
if (!defined('CFG_YANDEX_BUTTON_TITLE')) {
    define('CFG_YANDEX_BUTTON_TITLE', '');
}


//get max upload file size
define(
    'CFG_SERVER_UPLOAD_MAX_FILESIZE',
    ((int)ini_get("post_max_size") < (int)ini_get("upload_max_filesize") ? (int)ini_get("post_max_size") : (int)ini_get(
        "upload_max_filesize"
    ))
);

//set php timezone	
date_default_timezone_set(CFG_APP_TIMEZONE);

//set myslq timezone as it's configured for app	
db_query("SET time_zone = '" . date('P') . "'");

//cache vars
$get_heading_fields = fields::get_heading_fields();
$app_heading_fields_cache = $get_heading_fields['id'];
$app_heading_fields_id_cache = $get_heading_fields['entities_id'];

$app_not_formula_fields_cache = fields::not_formula_fields_cache();
$app_formula_fields_cache = fields::formula_fields_cache();
$app_fields_cache = fields::get_cache();
$app_access_rules_fields_cache = access_rules::get_access_rules_fields_cache();
$app_mysql_query_fields_cache = fieldtype_mysql_query::get_fields_cache();

$app_entities_cache = entities::get_cache();
$app_choices_cache = fields_choices::get_cache();
$app_global_choices_cache = global_lists::get_cache();
$app_access_groups_cache = access_groups::get_cache();

$app_num2str = new num2str();

//include custom PHP code
custom_php::include();