<?php

if (!defined('CFG_ENABLE_CHAT')) {
    define('CFG_ENABLE_CHAT', 0);
}
if (!defined('CFG_CHAT_SEND_ALERTS')) {
    define('CFG_CHAT_SEND_ALERTS', 0);
}
if (!defined('CFG_CHAT_ALERTS_SUBJECT')) {
    define('CFG_CHAT_ALERTS_SUBJECT', '');
}
if (!defined('CFG_CURRENCIES_UPDATE_MODULE')) {
    define('CFG_CURRENCIES_UPDATE_MODULE', 'cbr');
}
if (!defined('CFG_CURRENCIES_WIDGET_USERS_GROUPS')) {
    define('CFG_CURRENCIES_WIDGET_USERS_GROUPS', '');
}
if (!defined('CFG_INCOMING_CALL_ENTITY')) {
    define('CFG_INCOMING_CALL_ENTITY', 0);
}
if (!defined('CFG_INCOMING_CALL_FIELD')) {
    define('CFG_INCOMING_CALL_FIELD', '');
}
if (!defined('CFG_PERSONAL_CALENDAR_DEFAULT_VIEW')) {
    define('CFG_PERSONAL_CALENDAR_DEFAULT_VIEW', 'month');
}
if (!defined('CFG_PERSONAL_CALENDAR_HIGHLIGHTING_WEEKENDS')) {
    define('CFG_PERSONAL_CALENDAR_HIGHLIGHTING_WEEKENDS', '');
}
if (!defined('CFG_PUBLIC_CALENDAR_DEFAULT_VIEW')) {
    define('CFG_PUBLIC_CALENDAR_DEFAULT_VIEW', 'month');
}
if (!defined('CFG_PUBLIC_CALENDAR_HIGHLIGHTING_WEEKENDS')) {
    define('CFG_PUBLIC_CALENDAR_HIGHLIGHTING_WEEKENDS', '');
}
if (!defined('CFG_MAIL_INTEGRATION')) {
    define('CFG_MAIL_INTEGRATION', 0);
}
if (!defined('CFG_MAIL_INTEGRATION_USERS')) {
    define('CFG_MAIL_INTEGRATION_USERS', '');
}
if (!defined('CFG_MAIL_DATETIME_FORMAT')) {
    define('CFG_MAIL_DATETIME_FORMAT', CFG_APP_DATETIME_FORMAT);
}
if (!defined('CFG_MAIL_ROWS_PER_PAGE')) {
    define('CFG_MAIL_ROWS_PER_PAGE', CFG_APP_ROWS_PER_PAGE);
}
if (!defined('CFG_MAIL_DISPLAY_IN_MENU')) {
    define('CFG_MAIL_DISPLAY_IN_MENU', 0);
}
if (!defined('CFG_MAIL_DISPLAY_IN_HEADER')) {
    define('CFG_MAIL_DISPLAY_IN_HEADER', 1);
}
if (!defined('CFG_IPAGES_ACCESS_TO_USERS')) {
    define('CFG_IPAGES_ACCESS_TO_USERS', '');
}
if (!defined('CFG_IPAGES_ACCESS_TO_USERS_GROUP')) {
    define('CFG_IPAGES_ACCESS_TO_USERS_GROUP', '');
}
if (!defined('CFG_PUBLIC_CALENDAR_MIN_TIME')) {
    define('CFG_PUBLIC_CALENDAR_MIN_TIME', '');
}
if (!defined('CFG_PUBLIC_CALENDAR_MAX_TIME')) {
    define('CFG_PUBLIC_CALENDAR_MAX_TIME', '');
}
if (!defined('CFG_PUBLIC_CALENDAR_TIME_SLOT_DURATION')) {
    define('CFG_PUBLIC_CALENDAR_TIME_SLOT_DURATION', '00:30:00');
}
if (!defined('CFG_PERSONAL_CALENDAR_MIN_TIME')) {
    define('CFG_PERSONAL_CALENDAR_MIN_TIME', '');
}
if (!defined('CFG_PERSONAL_CALENDAR_MAX_TIME')) {
    define('CFG_PERSONAL_CALENDAR_MAX_TIME', '');
}
if (!defined('CFG_PERSONAL_CALENDAR_TIME_SLOT_DURATION')) {
    define('CFG_PERSONAL_CALENDAR_TIME_SLOT_DURATION', '00:30:00');
}
if (!defined('CFG_PUBLIC_CALENDAR_VIEW_MODES')) {
    define('CFG_PUBLIC_CALENDAR_VIEW_MODES', '');
}
if (!defined('CFG_PERSONAL_CALENDAR_VIEW_MODES')) {
    define('CFG_PERSONAL_CALENDAR_VIEW_MODES', '');
}
if (!defined('CFG_CHAT_SOUND_NOTIFICATION')) {
    define('CFG_CHAT_SOUND_NOTIFICATION', '');
}
if (!defined('CFG_CHAT_INSTANT_NOTIFICATION')) {
    define('CFG_CHAT_INSTANT_NOTIFICATION', 0);
}

require('plugins/ext/classes/ganttchart.php');
require('plugins/ext/classes/calendar.php');
require('plugins/ext/classes/calendar_notification.php');
require('plugins/ext/classes/functions.php');
require('plugins/ext/classes/timer.php');
require('plugins/ext/classes/ipages.php');
require('plugins/ext/classes/pivotreports.php');
require('plugins/ext/classes/timeline_reports.php');
require('classes/chat/app_chat.php');
require('classes/chat/app_chat_notification.php');
require('plugins/ext/classes/public_forms.php');
require('plugins/ext/classes/modules.php');
require('plugins/ext/classes/sms.php');
require('plugins/ext/classes/api.php');
require('plugins/ext/classes/track_changes.php');
require('plugins/ext/classes/file_storage.php');
require('plugins/ext/classes/currencies.php');
require('plugins/ext/classes/kanban.php');
require('plugins/ext/classes/smart_input.php');
require('plugins/ext/classes/recurring_tasks.php');
require('plugins/ext/classes/mailing.php');
require('plugins/ext/classes/email_rules.php');
require('plugins/ext/classes/funnelchart.php');
require('plugins/ext/classes/common_filters.php');
require('plugins/ext/classes/pivot_calendars.php');
require('plugins/ext/classes/item_pivot_tables.php');
require('plugins/ext/classes/global_search.php');
require('plugins/ext/classes/pivot_tables.php');
require('plugins/ext/classes/resource_timeline.php');
require('plugins/ext/classes/rss_feed.php');
require('plugins/ext/classes/icalendar.php');
require('plugins/ext/classes/graphicreport.php');

require('plugins/ext/classes/report_page/report.php');
require('plugins/ext/classes/report_page/blocks_dropdown.php');
require('plugins/ext/classes/report_page/blocks.php');
require('plugins/ext/classes/report_page/blocks_html.php');
require('plugins/ext/classes/report_page/blocks_php.php');

require('plugins/ext/classes/map/map_reports.php');
require('plugins/ext/classes/map/mind_map_reports.php');
require('plugins/ext/classes/map/pivot_map_reports.php');

require('plugins/ext/classes/templates/entities_templates.php');
require('plugins/ext/classes/templates/comments_templates.php');
require('plugins/ext/classes/templates/export_templates.php');
require('plugins/ext/classes/templates/export_templates_blocks.php');
require('plugins/ext/classes/templates/export_templates_file.php');
require('plugins/ext/classes/templates/import_templates.php');
require('plugins/ext/classes/templates/xml_export.php');
require('plugins/ext/classes/templates/xml_import.php');
require('plugins/ext/classes/templates/export_selected.php');
require('plugins/ext/classes/templates/export_selected_docx.php');

require('plugins/ext/classes/processes/processes.php');
require('plugins/ext/classes/processes/clone_subitems.php');
require('plugins/ext/classes/processes/link_records_by_mysql_query.php');
require('plugins/ext/classes/processes/process_form.php');

require('plugins/ext/classes/mail/mail_fetcher.php');
require('plugins/ext/classes/mail/mime_decode.php');
require('plugins/ext/classes/mail/mail_accounts_users.php');
require('plugins/ext/classes/mail/mail_accounts.php');
require('plugins/ext/classes/mail/mail_related_items.php');
require('plugins/ext/classes/mail/mail_info.php');
require('plugins/ext/classes/mail/mail_related.php');
require('plugins/ext/classes/mail/mail_filters.php');
require('plugins/ext/classes/mail/mail_entities_filters.php');
require('plugins/ext/classes/mail/mail_entities_rules.php');


if (is_ext_installed()) {
    $app_functions_cache = functions::get_cache();
    $app_currencies_cache = currencies::get_cache();
}