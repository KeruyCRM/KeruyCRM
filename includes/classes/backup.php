<?php

class backup
{

    public $description;
    public $is_export;
    public $filename;
    public $backup_dir;

    function __construct($is_auto = false)
    {
        $this->is_export = false;
        $this->filename = '';
        $this->is_auto = $is_auto;
        $this->backup_dir = $this->is_auto ? DIR_FS_BACKUPS_AUTO : DIR_FS_BACKUPS;
        $this->description = '';
    }

    function set_description($description)
    {
        $this->description = $description;
    }

    function set_filename($filename)
    {
        $this->is_export = true;
        $this->filename = $filename;
    }

    function create()
    {
        global $app_user;

        set_time_limit(0);

        $tables_list = [];

        $tables_query = db_query("show tables");
        while ($tables = db_fetch_array($tables_query)) {
            $tables_list[] = current($tables);
        }

        //if export we just save filename	
        if ($this->is_export) {
            $filename = $this->filename;
        } else {
            $timestamp = time();

            $sql_data = [
                'description' => $this->description,
                'users_id' => (isset($app_user['id']) ? $app_user['id'] : 0),
                'date_added' => $timestamp,
                'is_auto' => $this->is_auto,
                'filename' => ''
            ];

            db_perform('app_backups', $sql_data);
            $backups_id = db_insert_id();

            $filename = self::prepare_filename($backups_id, $timestamp, $this->is_auto);
        }

        $fp = fopen($this->backup_dir . $filename, 'w+');

        //add description
        $description = 'KeruyCRM ' . PROJECT_VERSION . (strlen($this->description) ? "\n" . $this->description : '');
        foreach (preg_split('/\r\n|\r|\n/', $description) as $text) {
            fwrite($fp, "#" . $text . ";\n");
        }

        fwrite($fp, "\n");


        foreach ($tables_list as $table) {
            //skip backups table
            if ($table == 'app_backups') {
                continue;
            }

            fwrite($fp, "DROP TABLE IF EXISTS " . $table . ";\n");
        }

        fwrite($fp, "\n\n");

        foreach ($tables_list as $table) {
            $show_query = db_query('SHOW CREATE TABLE ' . $table);
            $show = db_fetch_array($show_query);

            $show['Create Table'] = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $show['Create Table']);

            fwrite($fp, $show['Create Table'] . ";\n\n");

            $where_sql = '';

            //skip data for entity tables if do export
            if ($this->is_export) {
                $skip_insert = [
                    'app_choices_values',
                    'app_comments',
                    'app_comments_history',
                    'app_related_items',
                    'app_sessions',
                    'app_users_configuration',
                    'app_users_notifications',
                    'app_users_search_settings',
                    'app_items_export_templates',
                    'app_ext_calendar_events',
                    'app_ext_timer',
                    'app_ext_chat_conversations',
                    'app_ext_chat_conversations_messages',
                    'app_ext_chat_messages',
                    'app_ext_chat_unread_messages',
                    'app_ext_chat_users_online',
                    'app_ext_file_storage_queue',
                    'app_ext_ganttchart_depends',
                    'app_ext_track_changes_log',
                    'app_ext_track_changes_log_fields',
                    'app_ext_mail',
                    'app_ext_mail_accounts',
                    'app_ext_mail_accounts_entities',
                    'app_ext_mail_accounts_entities_fields',
                    'app_ext_mail_accounts_entities_filters',
                    'app_ext_mail_accounts_entities_rules',
                    'app_ext_mail_accounts_users',
                    'app_ext_mail_contacts',
                    'app_ext_mail_filters',
                    'app_ext_mail_groups',
                    'app_ext_mail_groups_from',
                    'app_ext_mail_to_items',
                    'app_emails_on_schedule',
                    'app_approved_items',
                    'app_attachments',
                    'app_users_login_log',
                    'app_ext_cryptopro_certificates',
                    'app_ext_signed_items',
                    'app_ext_signed_items_signatures',
                    'app_ext_modules_cfg',
                    'app_favorites',
                    'app_image_map_markers_nested',

                ];

                $reports_where_sql = " where (created_by='" . $app_user['id'] . "' or reports_type in ('common','default','parent_item_info_page')) or LOCATE('entityfield',reports_type) or LOCATE('fields_choices',reports_type) or LOCATE('process',reports_type) or LOCATE('process_action',reports_type) or LOCATE('functions',reports_type)";

                //get reports
                if ($table == 'app_reports') {
                    $where_sql = $reports_where_sql;
                } //get reports filters
                elseif ($table == 'app_reports_filters') {
                    $where_sql = " where reports_id in (select id from app_reports " . $reports_where_sql . ")";
                } //users filters
                elseif ($table == 'app_users_filters') {
                    $where_sql = " where users_id='" . $app_user['id'] . "'";
                } //users filters
                elseif ($table == 'app_user_filters_values') {
                    $where_sql = " where filters_id in (select id from app_users_filters where users_id='" . $app_user['id'] . "')";
                } //get only current user
                elseif ($table == 'app_entity_1') {
                    $where_sql = " where id='" . $app_user['id'] . "'";
                } elseif (strstr($table, 'app_entity_') or in_array($table, $skip_insert) or preg_match(
                        '/app_related_items_(\d+)_(\d+)/',
                        $table
                    ) or preg_match('/app_entity_(\d+)_values/', $table)) {
                    continue;
                }
            }

            //skip backups table
            if ($table == 'app_backups') {
                continue;
            }

            $count_query = db_query('SELECT COUNT(*) as total FROM  ' . $table);
            $count = db_fetch_array($count_query);

            if ($count['total'] > 0) {
                $columns_null = [];

                $columns_query = db_query('SHOW COLUMNS FROM  ' . $table);
                while ($columns = db_fetch_array($columns_query)) {
                    if ($columns['Null'] == 'YES') {
                        $columns_null[] = $columns['Field'];
                    }
                }

                //check if items exists
                $items_query = db_query('SELECT * FROM  ' . $table . $where_sql . ' limit 1');
                if ($items = db_fetch_array($items_query)) {
                    fwrite($fp, "INSERT INTO " . $table . " VALUES");
                }

                $limit = 100;
                $from = 0;
                $i = 0;

                do {
                    $items_query = db_query(
                        'SELECT * FROM  ' . $table . $where_sql . ' LIMIT ' . $from . ', ' . $limit
                    );

                    if (db_num_rows($items_query) > 0 and $from > 0) {
                        fwrite($fp, ";\n\n");
                        fwrite($fp, "INSERT INTO " . $table . " VALUES");
                        $i = 0;
                    }

                    while ($items = db_fetch_array($items_query)) {
                        $i++;

                        foreach ($items as $k => $v) {
                            if (is_null($v) or (strlen($v) == 0 and in_array($k, $columns_null))) {
                                $items[$k] = "NULL";
                            } else {
                                $items[$k] = "'" . db_input($v) . "'";
                            }
                        }

                        fwrite($fp, ($i > 1 ? "," : "") . "\n(" . implode(",", $items) . ")");
                    }


                    $from += $limit;
                } while ($from < ($count['total'] + $limit));

                fwrite($fp, ";\n\n");
            }
        }

        fclose($fp);

        //create zip archive if not export
        if (!$this->is_export) {
            $zip = new ZipArchive();
            $zip_filename = $filename . ".zip";
            $zip_filepath = $this->backup_dir . $zip_filename;

            //open zip archive
            $zip->open($zip_filepath, ZipArchive::CREATE);

            //add files to archive
            $zip->addFile($this->backup_dir . $filename, '/' . $filename);

            $zip->close();

            unlink($this->backup_dir . $filename);

            db_perform('app_backups', ['filename' => $zip_filename], 'update', "id='" . $backups_id . "'");
        }

        if ($this->is_auto) {
            $this->reset_backup_auto_dir();
        }
    }

    function reset_backup_auto_dir()
    {
        if (CFG_AUTOBACKUP_KEEP_FILES_DAYS == 0) {
            return false;
        }

        $files = glob(DIR_FS_BACKUPS_AUTO . '*');

        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (time() - filemtime($file) >= 3600 * 24 * CFG_AUTOBACKUP_KEEP_FILES_DAYS) {
                        unlink($file);
                    }
                }
            }
        }
    }

    static function prepare_filename($id, $timestamp, $is_auto = false)
    {
        return ($is_auto ? 'autobackup' : $id) . '_' . date(
                'Y-m-d_H-i',
                $timestamp
            ) . '_KeruyCRM_' . PROJECT_VERSION . '.sql';
    }

    static function reset($is_auto = false)
    {
        global $app_user;

        $backup_dir = $is_auto ? DIR_FS_BACKUPS_AUTO : DIR_FS_BACKUPS;

        //remove db records if files not exist
        $backups_query = db_query("select * from app_backups where is_auto='{$is_auto}' order by date_added desc");
        while ($backups = db_fetch_array($backups_query)) {
            if (!is_file($backup_dir . $backups['filename'])) {
                db_delete_row('app_backups', $backups['id']);
            }
        }

        //check if new fiels are loaded
        $dir = dir($backup_dir);
        $backups = [];
        while ($file = $dir->read()) {
            if (!is_dir($backup_dir . $file) and $file != '.htaccess' and (substr($file, -4) == '.zip' or substr(
                        $file,
                        -4
                    ) == '.sql')) {
                $count_query = db_query(
                    "select count(*) as total from app_backups where filename='" . db_input(
                        $file
                    ) . "' and is_auto='{$is_auto}'"
                );
                $count = db_fetch_array($count_query);
                $count = $count['total'];

                if ($count == 0) {
                    //remove any special chars in filename
                    $filename = str_replace(" ", "-", preg_replace("/[^A-Za-z0-9\-\._]/", "", $file));

                    //rename file
                    if ($file != $filename) {
                        if ($filename == '.zip' or $filename == '.sql') {
                            $filename = date('Y-m-d', filemtime($backup_dir . $file)) . $filename;
                        }

                        rename($backup_dir . $file, $backup_dir . $filename);

                        $file = $filename;
                    }

                    $sql_data = [
                        'description' => '',
                        'filename' => $file,
                        'users_id' => (isset($app_user['id']) ? $app_user['id'] : 0),
                        'date_added' => filemtime($backup_dir . $file),
                        'is_auto' => $is_auto,
                    ];

                    db_perform('app_backups', $sql_data);
                }
            }
        }
    }

    static function restore_fp_read_str($fp)
    {
        global $file_cache;

        if (is_null($file_cache)) {
            $file_cache = '';
        }

        $string = '';
        $file_cache = ltrim($file_cache);

        $pos = strpos($file_cache, "\n", 0);

        if ($pos < 1) {
            while (!strlen($string) && ($str = fread($fp, 4096))) {
                $pos = strpos($str, "\n", 0);

                if ($pos === false) {
                    $file_cache .= $str;
                } elseif ($pos == 0) {
                    $string = $file_cache . substr($str, 0, 1);
                    $file_cache = substr($str, 1);
                } else {
                    $string = $file_cache . substr($str, 0, $pos);
                    $file_cache = substr($str, $pos + 1);
                }
            }

            if (!$str) {
                if (strlen($file_cache)) {
                    $string = $file_cache;
                    $file_cache = '';

                    return trim($string);
                }

                return false;
            }
        } else {
            $string = substr($file_cache, 0, $pos);
            $file_cache = substr($file_cache, $pos + 1);
        }

        return trim($string);
    }

    static function restore($filename, $is_auto = false)
    {
        global $alerts;

        $backup_dir = $is_auto ? DIR_FS_BACKUPS_AUTO : DIR_FS_BACKUPS;

        if (is_file($backup_dir . $filename)) {
            set_time_limit(0);

            $tables_query = db_query("show tables");
            while ($tables = db_fetch_array($tables_query)) {
                if (current($tables) == 'app_backups') {
                    continue;
                }

                db_query('DROP TABLE ' . current($tables));
            }

            $fp = fopen($backup_dir . $filename, 'r');

            $file_cache = $sql = $table = $insert = '';
            $query_len = 0;
            $execute = 0;

            while (($str = self::restore_fp_read_str($fp)) !== false) {
                if (empty($str) || preg_match("/^(#|--)/", $str)) {
                    continue;
                }

                $query_len += strlen($str);


                if (!strlen($insert) && preg_match("/INSERT INTO ([^`]*?) VALUES([^`]*?)/i", $str, $m)) {
                    if ($table != $m[1]) {
                        $table = $m[1];
                    }

                    $insert = $m[0] . ' ';

                    $sql .= '';
                } else {
                    $sql .= $str;
                }

                if (!strlen($insert) && preg_match("/CREATE TABLE `([^`]*?)`/i", $str, $m) && $table != $m[1]) {
                    $table = $m[1];
                    $insert = '';
                }

                if (strlen($sql)) {
                    if (preg_match("/;$/", $str)) {
                        $sql = rtrim($insert . $sql, ";");

                        $insert = '';
                        $execute = 1;
                    }

                    if ($query_len >= 65536 && preg_match("/,$/", $str)) {
                        $sql = rtrim($insert . $sql, ",");
                        $execute = 1;
                    }

                    if ($execute) {
                        db_query($sql);

                        $sql = '';
                        $query_len = 0;
                        $execute = 0;
                    }
                }
            }

            //prepare procedures			
            fieldtype_days_difference::prepare_procedure();
            fieldtype_hours_difference::prepare_procedure();
            fieldtype_years_difference::prepare_procedure();
            fieldtype_months_difference::prepare_procedure();
            fieldtype_phone::prepare_procedure();

            $alerts->add(TEXT_BACKUP_RESTORED, 'success');
        }
    }

}
