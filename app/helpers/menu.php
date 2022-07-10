<?php

namespace Helpers;

class Menu
{
    public static function build_user_menu()
    {
        global $app_user;

        $menu = [];

        //check if logged user is guest
        if (guest_login::is_guest()) {
            if (strlen($app_user['multiple_access_groups'])) {
                $menu[] = [
                    'title' => \K::$fw->TEXT_CHANGE_ACCESS_GROUP,
                    'url' => url_for('users/change_access_group'),
                    'modalbox' => true,
                    'class' => 'fa-user-o'
                ];
            }

            $menu[] = [
                'title' => \K::$fw->TEXT_LOGOFF,
                'url' => url_for('users/login&action=logoff'),
                'class' => 'fa-sign-out'
            ];

            return $menu;
        }

        //build menu for standard tuser
        $menu[] = ['title' => \K::$fw->TEXT_MY_ACCOUNT, 'url' => url_for('users/account'), 'class' => 'fa-user'];

        if (is_ext_installed() and \K::$fw->CFG_LOGIN_DIGITAL_SIGNATURE_MODULE > 0) {
            $menu[] = [
                'title' => \K::$fw->TEXT_EXT_MY_DIGITAL_SIGNATURE,
                'url' => url_for('users/signature_account'),
                'class' => 'fa-address-card-o'
            ];
        }

        if (count($plugin_menu = plugins::include_menu('account_menu')) > 0) {
            $menu = array_merge($menu, $plugin_menu);
        }

        if (strlen(\K::$fw->CFG_APP_SKIN) == 0) {
            $menu[] = [
                'title' => \K::$fw->TEXT_CHANGE_SKIN,
                'url' => url_for('users/change_skin'),
                'modalbox' => true,
                'class' => 'fa-picture-o'
            ];
        }

        $menu[] = [
            'title' => \K::$fw->TEXT_CONFIGURE_DASHBOARD,
            'url' => url_for('dashboard/configure'),
            'modalbox' => true,
            'class' => 'fa-bars'
        ];
        $menu[] = [
            'title' => \K::$fw->TEXT_CONFIGURE_THEME,
            'url' => url_for('dashboard/configure_theme'),
            'modalbox' => true,
            'class' => 'fa-gear'
        ];

        if ((!in_array($app_user['group_id'], explode(',', \K::$fw->CFG_APP_DISABLE_CHANGE_PWD)) or strlen(
                    \K::$fw->CFG_APP_DISABLE_CHANGE_PWD
                ) == 0) and \K::$fw->CFG_USE_LDAP_LOGIN_ONLY == false) {
            $menu[] = [
                'title' => \K::$fw->TEXT_CHANGE_PASSWORD,
                'url' => url_for('users/change_password'),
                'class' => 'fa-unlock-alt'
            ];
        }

        if (strlen($app_user['multiple_access_groups'])) {
            $menu[] = [
                'title' => \K::$fw->TEXT_CHANGE_ACCESS_GROUP,
                'url' => url_for('users/change_access_group'),
                'is_hr' => true,
                'modalbox' => true,
                'class' => 'fa-user-o'
            ];
        }

        $menu[] = [
            'title' => \K::$fw->TEXT_LOGOFF,
            'url' => url_for('users/login&action=logoff'),
            'is_hr' => true,
            'class' => 'fa-sign-out'
        ];

        return $menu;
    }

    public static function build_entities_menu($menu)
    {
        global $app_user;

        $custom_entities_menu = [];
        $menu_query = db_fetch_all('app_entities_menu', 'length(entities_list)>0', 'sort_order, name');
        while ($v = db_fetch_array($menu_query)) {
            $custom_entities_menu = array_merge($custom_entities_menu, explode(',', $v['entities_list']));
        }

        $where_sql = '';

        if (count($custom_entities_menu) > 0) {
            $where_sql = " and e.id not in (" . implode(',', $custom_entities_menu) . ")";
        }

        if ($app_user['group_id'] == 0) {
            $entities_query = db_query(
                "select * from app_entities e where (e.parent_id = 0 or e.display_in_menu=1) {$where_sql} order by e.sort_order, e.name"
            );
        } else {
            $entities_query = db_query(
                "select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input(
                    $app_user['group_id']
                ) . "' and (e.parent_id = 0 or display_in_menu=1) {$where_sql} order by e.sort_order, e.name"
            );
        }

        while ($entities = db_fetch_array($entities_query)) {
            if ($entities['parent_id'] == 0) {
                $s = [];

                $entity_cfg = new entities_cfg($entities['id']);
                $menu_title = (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get(
                    'menu_title'
                ) : $entities['name']);
                $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get(
                    'menu_icon'
                ) : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));

                $menu[] = [
                    'title' => $menu_title,
                    'url' => url_for(
                        'items/items',
                        'path=' . $entities['id']
                    ),
                    'class' => $menu_icon,
                    'icon_color' => $entity_cfg->get('menu_icon_color'),
                    'bg_color' => $entity_cfg->get('menu_bg_color'),
                ];
            } else {
                $reports_info = reports::create_default_entity_report($entities['id'], 'entity_menu');

                //check if parent reports was not set
                if ($reports_info['parent_id'] == 0) {
                    reports::auto_create_parent_reports($reports_info['id']);
                }

                $entity_cfg = new entities_cfg($entities['id']);
                $menu_title = (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get(
                    'menu_title'
                ) : $entities['name']);
                $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get('menu_icon') : 'fa-reorder');

                $menu[] = [
                    'title' => $menu_title,
                    'url' => url_for('reports/view', 'reports_id=' . $reports_info['id']),
                    'class' => $menu_icon,
                    'icon_color' => $entity_cfg->get('menu_icon_color'),
                    'bg_color' => $entity_cfg->get('menu_bg_color'),
                ];
            }
        }

        return $menu;
    }

    public static function build_custom_entities_menu($menu, $parent_id = 0, $level = 0)
    {
        global $app_user;

        if ($level > 3) {
            return [];
        }

        $custom_entities_menu = [];
        $entities_menu_query = db_fetch_all('app_entities_menu', 'parent_id=' . $parent_id, 'sort_order, name');
        while ($entities_menu = db_fetch_array($entities_menu_query)) {
            $sub_menu = [];

            //add entities
            if (strlen($entities_menu['entities_list']) and $entities_menu['type'] == 'entity') {
                $where_sql = " e.id in (" . $entities_menu['entities_list'] . ")";

                if ($app_user['group_id'] == 0) {
                    $entities_query = db_query(
                        "select * from app_entities e where e.id in (" . $entities_menu['entities_list'] . ") order by field(e.id," . $entities_menu['entities_list'] . ")"
                    );
                } else {
                    $entities_query = db_query(
                        "select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input(
                            $app_user['group_id']
                        ) . "' and e.id in (" . $entities_menu['entities_list'] . ") order by field(e.id," . $entities_menu['entities_list'] . ")"
                    );
                }

                while ($entities = db_fetch_array($entities_query)) {
                    if ($entities['parent_id'] == 0) {
                        $s = [];

                        $entity_cfg = new entities_cfg($entities['id']);
                        $menu_title = (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get(
                            'menu_title'
                        ) : $entities['name']);
                        $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get(
                            'menu_icon'
                        ) : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));

                        $sub_menu[] = [
                            'title' => $menu_title,
                            'url' => url_for('items/items', 'path=' . $entities['id']),
                            'class' => $menu_icon,
                            'icon_color' => $entity_cfg->get('menu_icon_color'),
                            'bg_color' => $entity_cfg->get('menu_bg_color'),
                        ];
                    } else {
                        $reports_info = reports::create_default_entity_report($entities['id'], 'entity_menu');

                        //check if parent reports was not set
                        if ($reports_info['parent_id'] == 0) {
                            reports::auto_create_parent_reports($reports_info['id']);
                        }

                        $entity_cfg = new entities_cfg($entities['id']);
                        $menu_title = (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get(
                            'menu_title'
                        ) : $entities['name']);
                        $menu_icon = (strlen($entity_cfg->get('menu_icon')) > 0 ? $entity_cfg->get(
                            'menu_icon'
                        ) : ($entities['id'] == 1 ? 'fa-user' : 'fa-reorder'));

                        $sub_menu[] = [
                            'title' => $menu_title,
                            'url' => url_for('reports/view', 'reports_id=' . $reports_info['id']),
                            'class' => $menu_icon,
                            'icon_color' => $entity_cfg->get('menu_icon_color'),
                            'bg_color' => $entity_cfg->get('menu_bg_color'),
                        ];
                    }
                }
            }

            //add reports
            if ($entities_menu['type'] == 'entity') {
                $sub_menu = entities_menu::build_menu($entities_menu['reports_list'], $sub_menu);
                $sub_menu = entities_menu::build_pages_menu($entities_menu['pages_list'], $sub_menu);
            }

            //add urls
            if ($entities_menu['type'] == 'url' and strlen($entities_menu['url'])) {
                if ((strlen($entities_menu['users_groups']) and in_array(
                            $app_user['group_id'],
                            explode(',', $entities_menu['users_groups'])
                        )) or strlen($entities_menu['assigned_to']) and in_array(
                        $app_user['id'],
                        explode(',', $entities_menu['assigned_to'])
                    )) {
                    $menu_icon = (strlen($entities_menu['icon']) > 0 ? $entities_menu['icon'] : 'fa-reorder');
                    $sub_menu[] = [
                        'title' => $entities_menu['name'],
                        'url' => $entities_menu['url'],
                        'class' => $menu_icon,
                        'target' => '_blank',
                        'icon_color' => $entities_menu['icon_color'],
                        'bg_color' => $entities_menu['bg_color'],
                    ];
                }
            }

            $sub_menu = build_custom_entities_menu($sub_menu, $entities_menu['id'], $level + 1);

            $nested_query = db_query(
                "select id from app_entities_menu where parent_id='" . $entities_menu['id'] . "' limit 1"
            );
            $has_nested = db_fetch_array($nested_query);

            if (count($sub_menu) == 1 and !$has_nested) {
                $menu_icon = (strlen($entities_menu['icon']) > 0 ? $entities_menu['icon'] : 'fa-reorder');
                $menu[] = [
                    'title' => $entities_menu['name'],
                    'url' => $sub_menu[0]['url'],
                    'class' => $menu_icon,
                    'icon_color' => $entities_menu['icon_color'],
                    'bg_color' => $entities_menu['bg_color'],
                    'target' => $sub_menu[0]['target'] ?? false
                ];
            } elseif (count($sub_menu) > 0) {
                $menu_icon = (strlen($entities_menu['icon']) > 0 ? $entities_menu['icon'] : 'fa-reorder');
                $menu[] = [
                    'title' => $entities_menu['name'],
                    'url' => $sub_menu[0]['url'],
                    'class' => $menu_icon,
                    'icon_color' => $entities_menu['icon_color'],
                    'bg_color' => $entities_menu['bg_color'],
                    'submenu' => $sub_menu
                ];
            }
        }

        return $menu;
    }

    public static function build_reports_menu($menu)
    {
        global $app_logged_users_id, $app_user, $app_users_cfg;

        if ($has_reports_access = users::has_reports_access()) {
            //get standard reports
            $reports_query = db_query(
                "select * from app_reports where created_by='" . db_input(
                    $app_logged_users_id
                ) . "' and in_menu=1 and reports_type in ('standard') order by name"
            );
            while ($v = db_fetch_array($reports_query)) {
                $menu[] = [
                    'title' => $v['name'],
                    'url' => url_for('reports/view', 'reports_id=' . $v['id']),
                    'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : 'fa-list-alt'),
                    'icon_color' => $v['icon_color'],
                    'bg_color' => $v['bg_color'],
                ];
            }
        }

        //get common reports
        $reports_query = db_query(
            "select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input(
                $app_user['group_id']
            ) . "' and find_in_set(" . $app_user['group_id'] . ",r.users_groups) and r.in_menu=1 and r.reports_type = 'common' " . (strlen(
                $app_users_cfg->get('hidden_common_reports')
            ) > 0 ? "  and r.id not in (" . $app_users_cfg->get(
                    'hidden_common_reports'
                ) . ") " : "") . " order by r.dashboard_sort_order, name"
        );
        while ($v = db_fetch_array($reports_query)) {
            $menu[] = [
                'title' => $v['name'],
                'url' => url_for('reports/view', 'reports_id=' . $v['id']),
                'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : 'fa-list-alt'),
                'icon_color' => $v['icon_color'],
                'bg_color' => $v['bg_color'],
            ];
        }

        $s = [];

        if ($has_reports_access) {
            $s[] = ['title' => TEXT_STANDARD_REPORTS, 'url' => url_for('reports/reports')];
            $s[] = ['title' => TEXT_REPORTS_GROUPS, 'url' => url_for('reports_groups/reports')];
        }

        if (count($plugin_menu = plugins::include_menu('reports')) > 0) {
            $s = array_merge($s, $plugin_menu);
        }

        if (count($s)) {
            $menu[] = [
                'title' => TEXT_REPORTS,
                'url' => url_for('reports/reports'),
                'submenu' => $s,
                'class' => 'fa-bar-chart-o'
            ];
        }

        return $menu;
    }

    public static function build_reports_groups_menu($menu)
    {
        global $app_user;

        $reports_query = db_query(
            "select * from app_reports_groups where created_by = '" . $app_user['id'] . "' and is_common=0 and in_menu=1 order by sort_order, name"
        );
        while ($v = db_fetch_array($reports_query)) {
            $check_query = db_query(
                "select id from app_entities_menu where find_in_set('dashboard" . $v['id'] . "',reports_list)"
            );
            if (!$check = db_fetch_array($check_query)) {
                $menu[] = [
                    'title' => $v['name'],
                    'url' => url_for('dashboard/reports', 'id=' . $v['id']),
                    'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : 'fa-cubes'),
                    'icon_color' => $v['icon_color'],
                    'bg_color' => $v['bg_color'],
                ];
            }
        }

        $reports_query = db_query(
            "select * from app_reports_groups where (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set({$app_user['id']},assigned_to)) and is_common=1 and in_menu=1 order by sort_order, name"
        );
        while ($v = db_fetch_array($reports_query)) {
            $check_query = db_query(
                "select id from app_entities_menu where find_in_set('dashboard" . $v['id'] . "',reports_list)"
            );
            if (!$check = db_fetch_array($check_query)) {
                $menu[] = [
                    'title' => $v['name'],
                    'url' => url_for('dashboard/reports_groups', 'id=' . $v['id']),
                    'class' => (strlen($v['menu_icon']) > 0 ? $v['menu_icon'] : 'fa-cubes'),
                    'icon_color' => $v['icon_color'],
                    'bg_color' => $v['bg_color'],
                ];
            }
        }

        return $menu;
    }

    public static function build_search_menu($menu)
    {
        global $app_user;

        $allowed_groups = (strlen(CFG_GLOBAL_SEARCH_ALLOWED_GROUPS) ? explode(
            ',',
            CFG_GLOBAL_SEARCH_ALLOWED_GROUPS
        ) : []);

        if (CFG_USE_GLOBAL_SEARCH == 1 and CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU == 1 and in_array(
                $app_user['group_id'],
                $allowed_groups
            )) {
            $menu[] = ['title' => TEXT_SEARCH, 'url' => url_for('global_search/search'), 'class' => 'fa-search'];
        }

        return $menu;
    }

    public static function build_main_menu()
    {
        global $app_user;

        $menu = [];

        if (is_ext_installed()) {
            $menu = mail_accounts::render_menu_item($menu);
        }

        $menu[] = ['title' => TEXT_MENU_DASHBOARD, 'url' => url_for('dashboard/dashboard'), 'class' => 'fa-home'];

        $menu = build_reports_groups_menu($menu);

        $menu = build_entities_menu($menu);

        $menu = build_custom_entities_menu($menu);

        $menu = build_reports_menu($menu);

        $menu = build_search_menu($menu);

        if (count($plugin_menu = plugins::include_menu('menu')) > 0) {
            $menu = array_merge($menu, $plugin_menu);
        }

        //only administrators have access to configurations
        if ($app_user['group_id'] == 0) {
            //menu Configuration

            $s = [];
            $s[] = ['title' => TEXT_MENU_APPLICATION, 'url' => url_for('configuration/application')];

            $ss = [];
            $ss[] = ['title' => TEXT_USERS_CONFIGURATION, 'url' => url_for('configuration/users_settings')];
            $ss[] = [
                'title' => TEXT_MENU_USER_REGISTRATION_EMAIL,
                'url' => url_for('configuration/users_registration')
            ];
            $ss[] = ['title' => TEXT_PUBLIC_REGISTRATION, 'url' => url_for('configuration/public_users_registration')];
            $s[] = ['title' => TEXT_USERS, 'url' => url_for('configuration/users_settings'), 'submenu' => $ss];

            $ss = [];
            $ss[] = ['title' => TEXT_MENU_LOGIN_PAGE, 'url' => url_for('configuration/login_page')];
            $ss[] = ['title' => TEXT_2STEP_VERIFICATION, 'url' => url_for('configuration/2step_verification')];
            $ss[] = ['title' => TEXT_SOCIAL_LOGIN, 'url' => url_for('configuration/social_login')];
            $ss[] = ['title' => TEXT_GUEST_LOGIN, 'url' => url_for('configuration/guest_login')];
            $s[] = ['title' => TEXT_BUTTON_LOGIN, 'url' => url_for('configuration/login_page'), 'submenu' => $ss];

            $ss = [];
            $ss[] = ['title' => TEXT_MENU_EMAIL_OPTIONS, 'url' => url_for('configuration/emails')];
            $ss[] = ['title' => TEXT_EMAIL_SMTP_CONFIGURATION, 'url' => url_for('configuration/emails_smtp')];
            $ss[] = ['title' => TEXT_EMAILS_LAYOUT, 'url' => url_for('configuration/emails_layout')];
            $ss[] = ['title' => TEXT_BUTTON_SEND_TEST_EMAIL, 'url' => url_for('configuration/emails_send_test')];
            $s[] = ['title' => TEXT_MENU_EMAIL_OPTIONS, 'url' => url_for('configuration/emails'), 'submenu' => $ss];

            $s[] = ['title' => TEXT_MENU_ATTACHMENTS, 'url' => url_for('configuration/attachments')];
            $s[] = ['title' => TEXT_MENU_SECURITY, 'url' => url_for('configuration/security')];
            $s[] = ['title' => TEXT_SERVER_LOAD, 'url' => url_for('configuration/server_load')];
            $s[] = ['title' => TEXT_MENU_LDAP, 'url' => url_for('configuration/ldap')];
            $s[] = ['title' => 'PDF', 'url' => url_for('configuration/pdf')];
            $s[] = ['title' => TEXT_HOLIDAYS, 'url' => url_for('holidays/holidays')];

            $s[] = ['title' => TEXT_MENU_MAINTENANCE_MODE, 'url' => url_for('configuration/maintenance_mode')];
            $s[] = ['title' => TEXT_CUSTOM_CSS, 'url' => url_for('configuration/custom_css')];
            $s[] = ['title' => TEXT_CUSTOM_HTML, 'url' => url_for('configuration/custom_html')];
            $s[] = ['title' => TEXT_CUSTOM_PHP, 'url' => url_for('custom_php/code')];

            $menu[] = [
                'title' => TEXT_MENU_CONFIGURATION,
                'url' => url_for('configuration/application'),
                'submenu' => $s,
                'class' => 'fa-gear'
            ];

            $s = [];
            $s[] = ['title' => TEXT_MENU_ENTITIES_LIST, 'url' => url_for('entities/entities')];
            $s[] = ['title' => TEXT_MENU_USERS_ACCESS_GROUPS, 'url' => url_for('users_groups/users_groups')];
            $s[] = ['title' => TEXT_MENU_GLOBAL_LISTS, 'url' => url_for('global_lists/lists')];
            $s[] = ['title' => TEXT_GLOBAL_VARS, 'url' => url_for('global_vars/vars')];
            $s[] = ['title' => TEXT_MENU_CONFIGURATION_MENU, 'url' => url_for('entities/menu')];
            $s[] = ['title' => TEXT_DASHBOARD_CONFIGURATION, 'url' => url_for('dashboard_configure/index')];
            $menu[] = [
                'title' => TEXT_MENU_APPLICATION_STRUCTURE,
                'url' => url_for('entities/'),
                'class' => 'fa-sitemap',
                'submenu' => $s
            ];

            $s = plugins::include_menu('extension');

            if (count($s) > 0) {
                $menu[] = [
                    'title' => TEXT_MENU_EXTENSION,
                    'url' => url_for('ext/ext/'),
                    'submenu' => $s,
                    'class' => 'fa-puzzle-piece'
                ];
            } else {
                $menu[] = [
                    'title' => TEXT_MENU_EXTENSION,
                    'url' => url_for('tools/extension'),
                    'class' => 'fa-puzzle-piece'
                ];
            }

            //Menu Tools
            $s = [];
            $s[] = ['title' => TEXT_USERS_ALERTS, 'url' => url_for('users_alerts/users_alerts')];
            $s[] = ['title' => TEXT_USERS_LOGIN_LOG, 'url' => url_for('tools/users_login_log')];

            $ss = [];
            $ss[] = ['title' => TEXT_MENU_BACKUP, 'url' => url_for('tools/db_backup')];
            $ss[] = ['title' => TEXT_AUTO_BACKUP, 'url' => url_for('tools/db_backup_auto')];
            $s[] = ['title' => TEXT_MENU_BACKUP, 'url' => url_for('tools/db_backup'), 'submenu' => $ss];
            $s[] = ['title' => TEXT_MENU_CHECK_VERSION, 'url' => url_for('tools/check_version')];
            $s[] = ['title' => TEXT_MENU_SERVER_INFO, 'url' => url_for('tools/server_info')];
            $menu[] = [
                'title' => TEXT_MENU_TOOLS,
                'url' => url_for('tools/db_backup'),
                'submenu' => $s,
                'class' => 'fa-wrench'
            ];

            $s = [];
            $s[] = [
                'title' => TEXT_DOCUMENTATION,
                'url' => 'https://keruy.com.ua/',
                'target' => '_blank'
            ];
            $s[] = [
                'title' => TEXT_MENU_REPORT_FORUM,
                'url' => 'https://forum.keruy.com.ua/',
                'target' => '_blank'
            ];
            $s[] = [
                'title' => TEXT_NEWS,
                'url' => 'https://www.facebook.com/KeruyCRM/timeline',
                'target' => '_blank'
            ];
            $s[] = [
                'title' => TEXT_MENU_DONATE,
                'url' => 'https://keruy.com.ua/donate',
                'target' => '_blank'
            ];
            $s[] = [
                'title' => TEXT_MENU_CONTACT_US,
                'url' => 'https://keruy.com.ua/contact_us',
                'target' => '_blank'
            ];
            $menu[] = [
                'title' => TEXT_DOCUMENTATION,
                'url' => 'https://keruy.com.ua/',
                'submenu' => $s,
                'class' => 'fa-book'
            ];
        }

        return $menu;
    }

    public static function renderSidebarMenu($menu = [], $html = '', $level = 0)
    {
        if ($level > 0) {
            $html .= '
        <ul class="sub-menu">';
        }

        foreach ($menu as $v) {
            if (isset($v['is_hr'])) {
                if ($v['is_hr'] == true) {
                    $html .= '<li class="divider"></li>';
                }
            }

            $is_active = isSidebarMenuItemActive([], $v['url'], $level);

            if (strlen($html) == 0) {
                $html .= '<li class="start ' . ($is_active ? 'active' : '') . '">';
            } else {
                $html .= '<li  ' . ($is_active ? 'class="active"' : '') . ' >';
            }

            $url = '';

            if (isset($v['url'])) {
                if (isset($v['modalbox'])) {
                    $url = 'onClick="open_dialog(\'' . $v['url'] . '\')" class="cursor-pointer"';
                } else {
                    $url = 'href="' . $v['url'] . '"';
                }
            } elseif (isset($v['onClick'])) {
                $url = 'onClick="' . $v['onClick'] . '" class="cursor-pointer"';
            }

            if (!isset($v['target'])) {
                $v['target'] = false;
            }

            $link_class = '';

            if (strstr($url, '?')) {
                $query = parse_url($url, PHP_URL_QUERY);
                parse_str($query, $query);
                $query = $query['module'] . ($query['id'] ?? '') . ($query['path'] ?? '') . ($query['reports_id'] ?? '');
                $link_class = 'menu-' . preg_replace('/[\W]/', '', $query);
            }

            //$bg_color = (isset($v['bg_color']) and strlen($v['bg_color'])) ? 'style="background-color: ' . $v['bg_color'] . ' !important"':'';

            $icon_color = (isset($v['icon_color']) and strlen(
                    $v['icon_color']
                )) ? 'style="color: ' . $v['icon_color'] . '"' : '';

            if (isset($v['bg_color']) and strlen($v['bg_color'])) {
                $link_class .= ' menu-color-' . substr($v['bg_color'], 1);

                $html .= '
                <style>
                    .menu-color-' . substr($v['bg_color'], 1) . ',
                    .page-sidebar-menu > li > ul.sub-menu > li > a.menu-color-' . substr($v['bg_color'], 1) . '{
                        background-color: ' . $v['bg_color'] . '
                    }
                </style>';
            }

            $html .= '
        <a class="' . $link_class . '" ' . ($v['target'] ? 'target="' . $v['target'] . '"' : '') . ' ' . $url . '>' .
                (isset($v['class']) ? app_render_icon($v['class'], $icon_color) . ' ' : '') .
                '<span class="title ' . (isset($v['badge']) ? 'title-with-badge ' : '') . (isset($v['submenu']) ? 'submenu submenu-level-' . $level . (isset($v['class']) ? ' width-icon' : '') : 'level-' . $level) . '">' . $v['title'] . '</span>' .
                (isset($v['submenu']) ? '<span class="arrow ' . ($is_active ? 'open' : '') . '"></span>' : '') .
                (isset($v['badge']) ? '<span class="badge ' . $v['badge'] . '">' . $v['badge_content'] . '</span> ' : '') .
                '</a>';

            if (isset($v['submenu'])) {
                $html = renderSidebarMenu($v['submenu'], $html, $level + 1);
            }

            $html .= '
        </li>' . "\n";
        }

        if ($level > 0) {
            $html .= '
        </ul>';
        }

        return $html;
    }

    public static function isSidebarMenuItemActive($menu, $menu_url, $menu_level, $check_level = 0)
    {
        global $sidebarMenu;

        if (count($menu) == 0) {
            $menu = $sidebarMenu;
        }

        $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (strstr($current_url, 'module=entities/') and strstr($current_url, 'entities_id=')) {
            $current_url = preg_replace(
                '/module=(.*)&entities_id=/',
                'module=entities/entities_configuration&entities_id=',
                $current_url
            );
            $current_url = preg_replace('/&fields_id=(.*)&/', '&', $current_url);
        }

        $current_url = str_replace('module=items/&', 'module=items/items&', $current_url);

        if ((strstr($current_url, $menu_url) && strlen($current_url) == strlen($menu_url)) || strstr(
                $current_url,
                $menu_url . '&'
            )) {
            return true;
        } else {
            foreach ($menu as $v) {
                if (isset($v['submenu'])) {
                    $url_list = [];
                    $url_list[] = $v['url'];
                    $url_list = getSidebarLevelUrls($v['submenu'], $url_list);

                    if ($menu_level == $check_level and hasSidebarLevelUrls(
                            $current_url,
                            $url_list
                        ) and hasSidebarLevelUrls($menu_url, $url_list)) {
                        return true;
                    }

                    if (isSidebarMenuItemActive($v['submenu'], $menu_url, $menu_level, $check_level + 1)) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    public static function hasSidebarLevelUrls($current_url, $url_list)
    {
        foreach ($url_list as $url) {
            if ((strstr($current_url, $url) and strlen($current_url) == strlen($url)) || strstr(
                    $current_url,
                    $url . '&'
                )) {
                return true;
            }
        }

        return false;
    }

    public static function getSidebarLevelUrls($submenu, $url_list)
    {
        foreach ($submenu as $v) {
            $url_list[] = $v['url'];

            if (isset($v['submenu'])) {
                $url_list = getSidebarLevelUrls($v['submenu'], $url_list);
            }
        }

        return $url_list;
    }

    public static function hasSidebarLevelUrl($submenu, $url)
    {
        foreach ($submenu as $v) {
            if ($v['url'] == $url) {
                return true;
            }

            if (isset($v['submenu'])) {
                hasSidebarLevelUrl($v['submenu'], $url);
            }
        }

        return false;
    }

    public static function renderDropDownMenu($menu = [], $html = '', $level = 0)
    {
        if ($level == 0) {
            $html .= '
        <ul class="dropdown-menu">';
        }

        foreach ($menu as $v) {
            if (isset($v['is_hr'])) {
                if ($v['is_hr'] == true) {
                    $html .= '<li class="divider"></li>';
                }
            }

            if (isset($v['modalbox'])) {
                $url = 'onClick="open_dialog(\'' . $v['url'] . '\')" class="cursor-pointer"';
            } else {
                $url = 'href="' . $v['url'] . '"';
            }

            $html .= '
        <li><a ' . $url . '><i class="fa ' . $v['class'] . '"></i> ' . $v['title'] . '</a>';

            if (isset($v['submenu'])) {
                $html = renderDropDownMenu($v['submenu'], $html, $level + 1);
            }

            $html .= '
        </li>' . "\n";
        }

        $html .= '
      </ul>';

        return $html;
    }

    public static function renderNavbarMenu($menu = [], $html = '', $level = 0, $selected_id = 0)
    {
        if (strlen($html) == 0) {
            $html = '<ul class="nav navbar-nav">';
        } elseif ($level == 1) {
            $html .= '<ul class="dropdown-menu">';
        }

        foreach ($menu as $v) {
            if (isset($v['modalbox'])) {
                $url = 'onClick="open_dialog(\'' . $v['url'] . '\')" class="cursor-pointer"';
            } elseif (isset($v['url'])) {
                $url = 'href="' . $v['url'] . '"';
            }

            if (!isset($v['selected_id'])) {
                $v['selected_id'] = 0;
            }

            if (isset($v['submenu'])) {
                $html .= '<li class="dropdown ' . ($selected_id == $v['selected_id'] ? 'selected' : '') . '"><a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . $v['title'] . ' <i class="fa fa-angle-down"></i></a>';
            } else {
                $html .= '<li class="' . ($selected_id == $v['selected_id'] ? 'selected' : '') . '"><a ' . $url . '>' . $v['title'] . '</a>';
            }

            if (isset($v['submenu'])) {
                $html = renderNavbarMenu($v['submenu'], $html, $level + 1);
            }

            $html .= '
        </li>' . "\n";
        }

        $html .= '
      </ul>';

        return $html;
    }
}