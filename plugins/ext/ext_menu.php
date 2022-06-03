<?php

if ($app_user['group_id'] == 0) {
    $ss = [];
    $ss[] = ['title' => TEXT_EXT_MENU_COMMON_REPORTS, 'url' => url_for('ext/common_reports/reports')];
    $ss[] = ['title' => TEXT_EXT_COMMON_REPORTS_GROUPS, 'url' => url_for('ext/reports_groups/reports')];
    $ss[] = ['title' => TEXT_EXT_COMMON_FILTERS, 'url' => url_for('ext/common_filters/reports')];

    $s = [];
    $s[] = ['title' => TEXT_EXT_MENU_COMMON_REPORTS, 'url' => url_for('ext/common_reports/reports'), 'submenu' => $ss];
    $s[] = ['title' => TEXT_EXT_CHANGE_HISTORY, 'url' => url_for('ext/track_changes/reports')];
    $s[] = ['title' => TEXT_EXT_GANTTCHART_REPORT, 'url' => url_for('ext/ganttchart/configuration')];
    $s[] = ['title' => TEXT_EXT_GRAPHIC_REPORT, 'url' => url_for('ext/graphicreport/configuration')];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_PIVOT_TABLES, 'url' => url_for('ext/pivot_tables/reports')];
    $ss[] = ['title' => TEXT_EXT_MENU_PIVOTREPORTS, 'url' => url_for('ext/pivotreports/reports')];
    $ss[] = ['title' => TEXT_EXT_ITEM_PIVOT_TABLES, 'url' => url_for('ext/item_pivot_tables/reports')];
    $s[] = ['title' => TEXT_EXT_MENU_PIVOTREPORTS, 'url' => url_for('ext/pivotreports/reports'), 'submenu' => $ss];

    $s[] = ['title' => TEXT_EXT_MENU_TIMELINE_REPORTS, 'url' => url_for('ext/timeline_reports/reports')];
    $s[] = ['title' => TEXT_EXT_FUNNELCHART, 'url' => url_for('ext/funnelchart/reports')];
    $s[] = ['title' => TEXT_EXT_KANBAN, 'url' => url_for('ext/kanban/reports')];


    $ss = [];
    $ss[] = ['title' => TEXT_EXT_MAP_REPORTS, 'url' => url_for('ext/map_reports/reports')];
    $ss[] = ['title' => TEXT_EXT_PIVOT_MAP_REPORT, 'url' => url_for('ext/pivot_map_reports/reports')];
    $ss[] = ['title' => TEXT_EXT_IMAGE_MAP, 'url' => url_for('ext/image_map/reports')];
    $s[] = ['title' => TEXT_EXT_MAP_REPORTS, 'url' => url_for('ext/map_reports/reports'), 'submenu' => $ss];
    $s[] = ['title' => TEXT_EXT_MIND_MAP, 'url' => url_for('ext/mind_map_reports/reports')];
    $app_plugin_menu['extension'][] = [
        'title' => TEXT_REPORTS,
        'url' => url_for('ext/ganttchart/configuration'),
        'submenu' => $s
    ];

    $s = [];
    $s[] = ['title' => TEXT_EXT_СALENDAR_PERSONAL, 'url' => url_for('ext/calendar/configuration_personal')];
    $s[] = ['title' => TEXT_EXT_СALENDAR_PUBLIC, 'url' => url_for('ext/calendar/configuration_public')];
    $s[] = ['title' => TEXT_EXT_CALENDAR_REPORT, 'url' => url_for('ext/calendar/configuration_reports')];
    $s[] = ['title' => TEXT_EXT_PIVOT_СALENDAR, 'url' => url_for('ext/pivot_calendars/reports')];
    $s[] = ['title' => TEXT_EXT_RESOURCE_TIMELINE, 'url' => url_for('ext/resource_timeline/reports')];
    $app_plugin_menu['extension'][] = [
        'title' => TEXT_EXT_СALENDAR,
        'url' => url_for('ext/calendar/configuration_personal'),
        'submenu' => $s
    ];

    //tools
    $s = [];
    $s[] = ['title' => TEXT_EXT_RECURRING_TASKS, 'url' => url_for('ext/recurring_tasks/recurring_tasks')];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_MENU_IPAGES, 'url' => url_for('ext/ipages/configuration')];
    $ss[] = ['title' => TEXT_ACCESS_CONFIGURATION, 'url' => url_for('ext/ipages/access_configuration')];
    $s[] = ['title' => TEXT_EXT_MENU_IPAGES, 'url' => url_for('ext/ipages/configuration'), 'submenu' => $ss];

    $ss = [];
    $ss[] = ['title' => TEXT_SETTINGS, 'url' => url_for('ext/global_search/settings')];
    $ss[] = ['title' => TEXT_EXT_ENTITIES, 'url' => url_for('ext/global_search/entities')];
    $s[] = ['title' => TEXT_EXT_GLOBAL_SEARCH, 'url' => url_for('ext/global_search/settings'), 'submenu' => $ss];

    $s[] = ['title' => TEXT_EXT_RSS_FEED, 'url' => url_for('ext/rss_feed/feeds')];
    $s[] = ['title' => TEXT_EXT_MENU_FUNCTIONS, 'url' => url_for('ext/functions/functions')];
    $s[] = ['title' => TEXT_EXT_MENU_TIMER, 'url' => url_for('ext/timer/configuration')];
    $s[] = ['title' => TEXT_EXT_CURRENCIES, 'url' => url_for('ext/currencies/currencies')];
    $s[] = ['title' => TEXT_EXT_MENU_CHAT, 'url' => url_for('ext/app_chat/configuration')];
    $s[] = ['title' => TEXT_EXT_API, 'url' => url_for('ext/ext/api')];
    $app_plugin_menu['extension'][] = [
        'title' => TEXT_MENU_TOOLS,
        'url' => url_for('ext/functions/functions'),
        'submenu' => $s
    ];

    $s = [];
    $s[] = ['title' => TEXT_EXT_ENTITIES_TEMPLATES, 'url' => url_for('ext/templates/entities_templates')];
    $s[] = ['title' => TEXT_EXT_COMMENTS_TEMPLATES, 'url' => url_for('ext/templates/comments_templates')];
    $s[] = ['title' => TEXT_EXT_EXPORT_TEMPLATES, 'url' => url_for('ext/templates/export_templates')];
    $s[] = ['title' => TEXT_EXT_EXPORT_SELECTED, 'url' => url_for('ext/export_selected/templates')];
    $s[] = ['title' => TEXT_EXT_TEMPLATES_FOR_IMPORT, 'url' => url_for('ext/templates/import_templates')];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_XML_EXPORT, 'url' => url_for('ext/xml_export/templates')];
    $ss[] = ['title' => TEXT_EXT_XML_IMPORT, 'url' => url_for('ext/xml_import/templates')];
    $s[] = ['title' => 'XML', 'url' => url_for('ext/xml_export/templates'), 'submenu' => $ss];
    $app_plugin_menu['extension'][] = [
        'title' => TEXT_EXT_TEMPLATES,
        'url' => url_for('ext/templates/entities_templates'),
        'submenu' => $s
    ];

    $s = [];
    $s[] = ['title' => TEXT_EXT_PAYMENT_MODULES, 'url' => url_for('ext/modules/modules', 'type=payment')];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_SMS_MODULES, 'url' => url_for('ext/modules/modules', 'type=sms')];
    $ss[] = ['title' => TEXT_EXT_SMS_SENDIGN_RULES, 'url' => url_for('ext/modules/sms_rules')];
    $s[] = ['title' => TEXT_EXT_SMS_MODULES, 'url' => url_for('ext/modules/modules', 'type=sms'), 'submenu' => $ss];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_FILE_STORAGE_MODULES, 'url' => url_for('ext/modules/modules', 'type=file_storage')];
    $ss[] = ['title' => TEXT_EXT_FILE_STORAGE_RULES, 'url' => url_for('ext/modules/file_storage_rules')];
    $s[] = [
        'title' => TEXT_EXT_FILE_STORAGE_MODULES,
        'url' => url_for('ext/modules/modules', 'type=file_storage'),
        'submenu' => $ss
    ];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_SAMRT_INPUT, 'url' => url_for('ext/modules/modules', 'type=smart_input')];
    $ss[] = ['title' => TEXT_EXT_SAMRT_INPUT_RULES, 'url' => url_for('ext/modules/smart_input_rules')];
    $s[] = [
        'title' => TEXT_EXT_SAMRT_INPUT,
        'url' => url_for('ext/modules/modules', 'type=smart_input'),
        'submenu' => $ss
    ];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_MAILING_SERVICES, 'url' => url_for('ext/modules/modules', 'type=mailing')];
    $ss[] = ['title' => TEXT_EXT_SUBSCRIBE_RULES, 'url' => url_for('ext/modules/subscribe_rules')];
    $s[] = [
        'title' => TEXT_EXT_MAILING_SERVICES,
        'url' => url_for('ext/modules/modules', 'type=mailing'),
        'submenu' => $ss
    ];

    $ss = [];
    $ss[] = ['title' => TEXT_EXT_MODULES, 'url' => url_for('ext/modules/modules', 'type=telephony')];
    $ss[] = ['title' => TEXT_SETTINGS, 'url' => url_for('ext/modules/telephony_settings')];
    $s[] = ['title' => TEXT_EXT_TELEPHONY, 'url' => url_for('ext/modules/modules', 'type=telephony'), 'submenu' => $ss];

    $s[] = ['title' => TEXT_EXT_DIGITAL_SIGNATURE, 'url' => url_for('ext/modules/modules', 'type=digital_signature')];

    $app_plugin_menu['extension'][] = [
        'title' => TEXT_EXT_MODULES,
        'url' => url_for('ext/modules/modules', 'type=payment'),
        'submenu' => $s
    ];

    $app_plugin_menu['extension'][] = [
        'title' => TEXT_EXT_PUBLIC_FORMS,
        'url' => url_for('ext/public_forms/public_forms')
    ];
    $app_plugin_menu['extension'][] = ['title' => TEXT_EXT_PROCESSES, 'url' => url_for('ext/processes/processes')];

    $s = [];
    $s[] = ['title' => TEXT_SETTINGS, 'url' => url_for('ext/mail_integration/settings')];
    $s[] = ['title' => TEXT_EXT_MAIL_ACCOUNTS, 'url' => url_for('ext/mail_integration/accounts')];
    $s[] = ['title' => TEXT_EXT_RELATD_ENTITIES, 'url' => url_for('ext/mail_integration/entities')];
    $app_plugin_menu['extension'][] = [
        'title' => TEXT_EXT_MAIL_INTEGRATION,
        'url' => url_for('ext/mail_integration/settings'),
        'submenu' => $s
    ];
}

require(component_path('ext/ganttchart/menu'));
require(component_path('ext/graphicreport/menu'));
require(component_path('ext/pivotreports/menu'));
require(component_path('ext/calendar/menu'));
require(component_path('ext/app_chat/menu'));
require(component_path('ext/ipages/menu'));
require(component_path('ext/with_selected/menu'));
require(component_path('ext/timeline_reports/menu'));
require(component_path('ext/track_changes/menu'));
require(component_path('ext/funnelchart/menu'));
require(component_path('ext/kanban/menu'));
require(component_path('ext/recurring_tasks/menu'));
require(component_path('ext/image_map/menu'));
require(component_path('ext/map_reports/menu'));
require(component_path('ext/mind_map_reports/menu'));
require(component_path('ext/pivot_calendars/menu'));
require(component_path('ext/xml_export/menu'));
require(component_path('ext/pivot_map_reports/menu'));
require(component_path('ext/pivot_tables/menu'));
require(component_path('ext/resource_timeline/menu'));
require(component_path('ext/rss_feed/menu'));