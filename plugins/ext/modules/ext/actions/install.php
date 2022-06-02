<?php

if (!defined('CFG_PLUGIN_EXT_INSTALLED')) {
    $install_sql = "
CREATE TABLE IF NOT EXISTS `app_ext_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `enable_ical` tinyint(1) NOT NULL,
  `in_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `default_view` varchar(16) NOT NULL,
  `view_modes` varchar(255) NOT NULL,
  `event_limit` smallint(6) NOT NULL,
  `highlighting_weekends` varchar(64) NOT NULL,
  `min_time` varchar(5) NOT NULL,
  `max_time` varchar(5) NOT NULL,
  `time_slot_duration` varchar(8) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `use_background` int(11) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `filters_panel` varchar(16) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_calendar_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_id` int(11) DEFAULT NULL,
  `calendar_type` varchar(16) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_calendar_id` (`calendar_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` bigint(20) UNSIGNED NOT NULL,
  `end_date` bigint(20) NOT NULL,
  `event_type` varchar(16) NOT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) NOT NULL,
  `repeat_type` varchar(16) NOT NULL,
  `repeat_interval` int(11) DEFAULT NULL,
  `repeat_days` varchar(16) NOT NULL,
  `repeat_end` int(11) DEFAULT NULL,
  `repeat_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_call_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(16) NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  `direction` varchar(16) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `duration` int(11) NOT NULL,
  `sms_text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `menu_icon_color` varchar(16) NOT NULL,
  `assigned_to` text NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_conversations_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversations_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachments` text NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_conversations_id` (`conversations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachments` text NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_users_assigned` (`users_id`,`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_unread_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `messages_id` int(11) NOT NULL,
  `conversations_id` int(11) NOT NULL,
  `notification_status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_messages_id` (`messages_id`),
  KEY `idx_conversations_id` (`conversations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_chat_users_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `date_check` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_comments_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_comments_templates_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templates_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_templates_id` (`templates_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_cryptopro_certificates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `thumbprint` varchar(64) NOT NULL,
  `certbase64` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `thumbprint` (`thumbprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_default` tinyint(1) NOT NULL,
  `title` varchar(64) NOT NULL,
  `code` varchar(16) NOT NULL,
  `symbol` varchar(16) NOT NULL,
  `value` float(13,8) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_email_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `action_type` varchar(64) NOT NULL,
  `send_to_users` text NOT NULL,
  `send_to_assigned_users` text NOT NULL,
  `send_to_email` text NOT NULL,
  `send_to_assigned_email` text NOT NULL,
  `monitor_fields_id` int(11) NOT NULL,
  `monitor_choices` text NOT NULL,
  `date_fields_id` int(11) NOT NULL,
  `number_of_days` varchar(32) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `attach_attachments` tinyint(1) NOT NULL,
  `attach_template` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_monitor_fields_id` (`monitor_fields_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_entities_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_entities_templates_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templates_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_templates_id` (`templates_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_export_selected` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `type` varchar(64) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `template_filename` varchar(64) NOT NULL,
  `export_fields` text NOT NULL,
  `export_url` tinyint(1) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_export_selected_blocks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `templates_id` int(11) NOT NULL,
  `block_type` varchar(32) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templates_id` (`templates_id`),
  KEY `fields_id` (`fields_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_export_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'html',
  `label_size` varchar(16) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `template_filename` varchar(255) NOT NULL,
  `save_as` varchar(32) NOT NULL,
  `template_css` text NOT NULL,
  `page_orientation` varchar(16) NOT NULL,
  `split_into_pages` tinyint(1) NOT NULL DEFAULT 1,
  `template_header` text NOT NULL,
  `template_footer` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_file_storage_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_file_storage_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `modules_id` int(11) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  `functions_name` varchar(32) NOT NULL,
  `functions_formula` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_funnelchart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(16) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `group_by_field` int(11) NOT NULL,
  `hide_zero_values` tinyint(1) NOT NULL,
  `exclude_choices` text NOT NULL,
  `sum_by_field` text NOT NULL,
  `users_groups` text NOT NULL,
  `colors` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_ganttchart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `weekends` varchar(16) NOT NULL,
  `gantt_date_format` varchar(16) NOT NULL,
  `progress` int(11) DEFAULT NULL,
  `fields_in_listing` text NOT NULL,
  `use_background` int(11) NOT NULL DEFAULT 0,
  `default_fields_in_listing` varchar(64) NOT NULL,
  `grid_width` smallint(6) NOT NULL,
  `default_view` varchar(16) NOT NULL,
  `skin` varchar(32) NOT NULL,
  `auto_scheduling` tinyint(1) NOT NULL,
  `highlight_critical_path` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_ganttchart_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ganttchart_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ganttchart_id` (`ganttchart_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_ganttchart_depends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ganttchart_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `depends_id` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_depends_id` (`depends_id`),
  KEY `idx_ganttchart_id` (`ganttchart_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_global_search_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `fields_for_search` text NOT NULL,
  `fields_in_listing` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_graphicreport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `xaxis` int(11) NOT NULL,
  `yaxis` varchar(255) NOT NULL,
  `allowed_groups` text NOT NULL,
  `chart_type` varchar(16) NOT NULL,
  `period` text NOT NULL,
  `show_totals` tinyint(1) NOT NULL,
  `hide_zero` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_image_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `scale` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_import_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `multilevel_import` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `import_fields` text NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_ipages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(64) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `icon_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `description` text NOT NULL,
  `html_code` text NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_menu` tinyint(1) NOT NULL DEFAULT 0,
  `attachments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_items_export_templates_blocks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `templates_id` int(11) NOT NULL,
  `block_type` varchar(32) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templates_id` (`templates_id`),
  KEY `fields_id` (`fields_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_item_pivot_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `allowed_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `related_entities_id` int(11) NOT NULL,
  `related_entities_fields` text NOT NULL,
  `position` varchar(16) NOT NULL,
  `rows_per_page` int(11) NOT NULL,
  `fields_in_listing` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_related_entities_id` (`related_entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_item_pivot_tables_calcs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `name` varchar(64) NOT NULL,
  `formula` text NOT NULL,
  `select_query` text NOT NULL,
  `where_query` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_kanban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `in_menu` tinyint(1) NOT NULL DEFAULT 0,
  `heading_template` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_by_field` int(11) NOT NULL,
  `exclude_choices` text NOT NULL,
  `fields_in_listing` text NOT NULL,
  `sum_by_field` text NOT NULL,
  `width` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  `is_new` tinyint(1) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `subject_cropped` varchar(255) NOT NULL,
  `groups_id` int(11) NOT NULL,
  `is_new_group` tinyint(1) NOT NULL,
  `body` longtext NOT NULL,
  `body_text` longtext NOT NULL,
  `to_name` text NOT NULL,
  `to_email` text NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `reply_to_name` text NOT NULL,
  `reply_to_email` text NOT NULL,
  `cc_name` text NOT NULL,
  `cc_email` text NOT NULL,
  `bcc_name` text NOT NULL,
  `bcc_email` text NOT NULL,
  `attachments` text NOT NULL,
  `error_msg` tinytext NOT NULL,
  `is_sent` tinyint(1) NOT NULL,
  `is_star` tinyint(1) NOT NULL,
  `in_trash` tinyint(1) NOT NULL,
  `is_spam` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`),
  KEY `idx_groups_id` (`groups_id`),
  KEY `idx_to_email` (`to_email`(128)),
  KEY `idx_from_email` (`from_email`(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `bg_color` varchar(16) NOT NULL,
  `imap_server` varchar(255) NOT NULL,
  `mailbox` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `delete_emails` tinyint(1) NOT NULL,
  `is_fetched` tinyint(1) NOT NULL,
  `use_smtp` tinyint(1) NOT NULL,
  `smtp_server` varchar(255) NOT NULL,
  `smtp_port` varchar(16) NOT NULL,
  `smtp_encryption` varchar(16) NOT NULL,
  `smtp_login` varchar(64) NOT NULL,
  `smtp_password` varchar(64) NOT NULL,
  `send_autoreply` tinyint(1) NOT NULL,
  `autoreply_msg` text NOT NULL,
  `not_group_by_subject` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `parent_item_id` int(11) NOT NULL,
  `from_name` int(11) NOT NULL,
  `from_email` int(11) NOT NULL,
  `subject` int(11) NOT NULL,
  `body` int(11) NOT NULL,
  `attachments` int(11) NOT NULL,
  `bind_to_sender` tinyint(1) NOT NULL,
  `auto_create` int(1) NOT NULL,
  `title` varchar(64) NOT NULL,
  `hide_buttons` varchar(64) NOT NULL,
  `fields_in_listing` text NOT NULL,
  `fields_in_popup` text NOT NULL,
  `related_emails_position` varchar(16) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts_entities_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_entities_id` int(10) UNSIGNED NOT NULL,
  `filters_id` int(11) NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_account_entities_id` (`account_entities_id`) USING BTREE,
  KEY `idx_filters_id` (`filters_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts_entities_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_entities_id` int(11) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `has_words` text NOT NULL,
  `parent_item_id` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_account_entities_id` (`account_entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts_entities_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_entities_id` int(11) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `has_words` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_account_entities_id` (`account_entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_accounts_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `send_mail_as` varchar(128) NOT NULL,
  `signature` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`),
  KEY `idx_name` (`name`(128)),
  KEY `idx_email` (`email`(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `has_words` text NOT NULL,
  `action` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `subject_cropped` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_check` (`accounts_id`,`subject_cropped`(191)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mail_groups_from` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_groups_id` int(11) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mail_groups_id` (`mail_groups_id`,`from_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_mail_to_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_groups_id` int(11) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_mail_groups_id` (`mail_groups_id`) USING BTREE,
  KEY `idx_from_email` (`from_email`(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_map_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `background` int(11) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `zoom` tinyint(1) NOT NULL,
  `latlng` varchar(16) NOT NULL,
  `is_public_access` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_mind_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `use_background` int(11) NOT NULL,
  `icons` text NOT NULL,
  `fields_in_popup` text NOT NULL,
  `shape` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_modules_cfg` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `modules_id` int(10) UNSIGNED NOT NULL,
  `cfg_key` varchar(64) NOT NULL,
  `cfg_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivotreports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `allowed_groups` text NOT NULL,
  `allow_edit` tinyint(1) NOT NULL,
  `cfg_numer_format` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `reports_settings` text NOT NULL,
  `view_mode` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivotreports_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pivotreports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_name` varchar(64) NOT NULL,
  `cfg_date_format` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pivotreports_id` (`pivotreports_id`),
  KEY `idx_entitites_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivotreports_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `reports_settings` text NOT NULL,
  `view_mode` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `default_view` varchar(16) NOT NULL,
  `view_modes` varchar(255) NOT NULL,
  `event_limit` smallint(6) NOT NULL DEFAULT 6,
  `highlighting_weekends` varchar(64) NOT NULL,
  `min_time` varchar(5) NOT NULL,
  `max_time` varchar(5) NOT NULL,
  `time_slot_duration` varchar(8) NOT NULL,
  `display_legend` tinyint(1) NOT NULL DEFAULT 0,
  `in_menu` tinyint(1) NOT NULL,
  `users_groups` text NOT NULL,
  `enable_ical` tinyint(1) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_calendars_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendars_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `bg_color` varchar(10) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `background` varchar(10) NOT NULL,
  `use_background` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_calendars_id` (`calendars_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_map_reports` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `zoom` tinyint(1) NOT NULL,
  `latlng` varchar(16) NOT NULL,
  `display_legend` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_map_reports_entities` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `background` int(11) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `marker_color` varchar(16) NOT NULL,
  `marker_icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `filters_panel` varchar(16) NOT NULL,
  `height` smallint(6) NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `chart_type` varchar(16) NOT NULL,
  `chart_position` varchar(16) NOT NULL,
  `chart_height` smallint(6) NOT NULL,
  `colors` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_name` varchar(64) NOT NULL,
  `cfg_date_format` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entitites_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_processes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(255) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `print_template` varchar(32) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `access_to_assigned` text NOT NULL,
  `window_width` varchar(64) NOT NULL,
  `confirmation_text` text NOT NULL,
  `warning_text` text NOT NULL,
  `allow_comments` tinyint(1) UNSIGNED NOT NULL,
  `preview_prcess_actions` tinyint(1) UNSIGNED NOT NULL,
  `notes` text NOT NULL,
  `payment_modules` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `apply_fields_access_rules` tinyint(1) NOT NULL DEFAULT 0,
  `apply_fields_display_rules` tinyint(1) NOT NULL,
  `hide_entity_name` tinyint(1) NOT NULL DEFAULT 0,
  `success_message` text NOT NULL,
  `redirect_to_items_listing` tinyint(1) NOT NULL DEFAULT 0,
  `disable_comments` tinyint(1) NOT NULL,
  `javascript_in_from` text NOT NULL,
  `javascript_onsubmit` text NOT NULL,
  `is_form_wizard` tinyint(1) NOT NULL DEFAULT 0,
  `is_form_wizard_progress_bar` tinyint(4) NOT NULL,
  `submit_button_title` varchar(32) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_processes_actions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `process_id` int(64) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_processes_actions_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `actions_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `value` text NOT NULL,
  `enter_manually` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_actions_id` (`actions_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_processes_buttons_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_processes_clone_subitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actions_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `from_entity_id` int(11) NOT NULL,
  `to_entity_id` int(11) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_from_entity_id` (`from_entity_id`),
  KEY `idx_to_entity_id` (`to_entity_id`),
  KEY `idx_actions_id` (`actions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_process_form_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `columns` tinyint(4) NOT NULL,
  `column1_width` tinyint(4) NOT NULL,
  `column2_width` tinyint(4) NOT NULL,
  `column3_width` tinyint(4) NOT NULL,
  `column4_width` tinyint(4) NOT NULL,
  `column5_width` tinyint(4) NOT NULL,
  `column6_width` tinyint(4) NOT NULL,
  `field_name_new_row` tinyint(1) NOT NULL,
  `column1_fields` text NOT NULL,
  `column2_fields` text NOT NULL,
  `column3_fields` text NOT NULL,
  `column4_fields` text NOT NULL,
  `column5_fields` text NOT NULL,
  `column6_fields` text NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`process_id`),
  KEY `forms_tabs_id` (`forms_tabs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_process_form_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `fields` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_public_forms` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) UNSIGNED NOT NULL,
  `parent_item_id` int(11) NOT NULL,
  `hide_parent_item` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `inactive_message` text NOT NULL,
  `name` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `button_save_title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `successful_sending_message` text NOT NULL,
  `after_submit_action` varchar(32) NOT NULL,
  `after_submit_redirect` varchar(255) NOT NULL,
  `user_agreement` text NOT NULL,
  `hidden_fields` text NOT NULL,
  `customer_name` varchar(64) NOT NULL,
  `customer_email` int(11) NOT NULL,
  `customer_message_title` varchar(255) NOT NULL,
  `customer_message` text NOT NULL,
  `admin_name` varchar(64) NOT NULL,
  `admin_email` varchar(64) NOT NULL,
  `admin_notification` tinyint(1) NOT NULL,
  `check_enquiry` tinyint(1) NOT NULL,
  `disable_submit_form` tinyint(1) NOT NULL,
  `check_page_title` varchar(255) NOT NULL,
  `check_page_description` text NOT NULL,
  `check_button_title` varchar(64) NOT NULL,
  `check_page_fields` text NOT NULL,
  `check_page_comments` tinyint(1) NOT NULL,
  `check_page_comments_heading` varchar(255) NOT NULL,
  `check_page_comments_fields` text NOT NULL,
  `notify_field_change` int(11) UNSIGNED NOT NULL,
  `notify_message_title` varchar(255) NOT NULL,
  `notify_message_body` text NOT NULL,
  `check_enquiry_fields` varchar(255) NOT NULL,
  `form_css` text NOT NULL,
  `form_js` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_recurring_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `repeat_type` varchar(16) NOT NULL,
  `repeat_interval` int(11) NOT NULL,
  `repeat_days` varchar(16) NOT NULL,
  `repeat_start` bigint(20) UNSIGNED NOT NULL,
  `repeat_end` bigint(20) UNSIGNED NOT NULL,
  `repeat_limit` int(11) NOT NULL,
  `repeat_time` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_recurring_tasks_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tasks_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tasks_id` (`tasks_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_resource_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `fields_in_listing` varchar(255) NOT NULL,
  `display_legend` tinyint(1) NOT NULL,
  `listing_width` varchar(4) NOT NULL,
  `column_width` varchar(64) NOT NULL,
  `fields_in_popup` varchar(255) NOT NULL,
  `default_view` varchar(16) NOT NULL,
  `view_modes` varchar(255) NOT NULL,
  `time_slot_duration` varchar(8) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_resource_timeline_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendars_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `related_entity_field_id` int(11) NOT NULL,
  `bg_color` varchar(10) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `background` varchar(10) NOT NULL,
  `use_background` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_calendars_id` (`calendars_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_rss_feeds` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rss_id` int(10) UNSIGNED NOT NULL,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `start_date` int(10) UNSIGNED NOT NULL,
  `end_date` int(10) UNSIGNED NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_rss_id` (`rss_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_signed_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `position` varchar(64) NOT NULL,
  `inn` varchar(64) NOT NULL,
  `ogrn` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`entities_id`),
  KEY `items_id` (`items_id`),
  KEY `users_id` (`users_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_signed_items_signatures` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `signed_items_id` int(11) NOT NULL,
  `signed_text` text NOT NULL,
  `singed_filename` varchar(255) NOT NULL,
  `signature` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `signed_items_id` (`signed_items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_smart_input_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_modules_id` (`modules_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_sms_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `modules_id` int(11) NOT NULL,
  `action_type` varchar(64) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `monitor_fields_id` int(11) NOT NULL,
  `monitor_choices` text NOT NULL,
  `date_fields_id` int(11) NOT NULL,
  `date_type` varchar(16) NOT NULL,
  `number_of_days` varchar(32) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `send_to_assigned_users` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_modules_id` (`modules_id`),
  KEY `idx_monitor_fields_id` (`monitor_fields_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_subscribe_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `modules_id` int(11) NOT NULL,
  `contact_list_id` varchar(255) NOT NULL,
  `contact_email_field_id` int(11) NOT NULL,
  `contact_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_modules_id` (`modules_id`),
  KEY `idx_fields_id` (`contact_email_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_timeline_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `in_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `allowed_groups` text NOT NULL,
  `use_background` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_timer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `seconds` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_timer_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `position` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `color_insert` varchar(7) NOT NULL,
  `color_update` varchar(7) NOT NULL,
  `color_comment` varchar(7) NOT NULL,
  `color_delete` varchar(7) NOT NULL,
  `rows_per_page` smallint(6) NOT NULL,
  `keep_history` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `track_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `comments_id` int(11) NOT NULL,
  `items_name` varchar(255) NOT NULL,
  `date_added` bigint(20) UNSIGNED NOT NULL,
  `created_by` int(11) NOT NULL,
  `is_cron` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_comments_id` (`comments_id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_log_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_log_id` (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_xml_export_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `template_header` text NOT NULL,
  `template_body` text NOT NULL,
  `template_footer` text NOT NULL,
  `template_filename` varchar(255) NOT NULL,
  `transliterate_filename` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_xml_import_templates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `data_path` varchar(255) NOT NULL,
  `import_fields` text NOT NULL,
  `import_fields_path` text NOT NULL,
  `import_action` varchar(16) NOT NULL,
  `update_by_field` int(11) NOT NULL,
  `update_by_field_path` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `parent_item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;      

ALTER TABLE `app_ext_export_templates` ADD `save_attachments` VARCHAR(255) NOT NULL AFTER `save_as`;

CREATE TABLE IF NOT EXISTS `app_ext_email_rules_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

";

    foreach (explode(';', $install_sql) as $query) {
        if (strlen(trim($query)) > 0) {
            db_query(trim($query));
        }
    }

    db_perform('app_configuration', ['configuration_value' => 1, 'configuration_name' => 'CFG_PLUGIN_EXT_INSTALLED']);

    $alerts->add(TEXT_EXT_PLUGIN_INSTALLED, 'success');

    redirect_to('dashboard/dashboard');
}