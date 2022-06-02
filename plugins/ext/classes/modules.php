<?php

class modules
{

    public $type;
    public $path;

    function __construct($type)
    {
        $this->type = $type;

        switch ($this->type) {
            case 'payment':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/payment_modules/';
                $this->include_modules();
                break;
            case 'sms':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/sms_modules/';
                $this->include_modules();
                break;
            case 'file_storage':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/file_storage_modules/';
                $this->include_modules();
                break;
            case 'smart_input':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/smart_input_modules/';
                $this->include_modules();
                break;
            case 'mailing':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/mailing_modules/';
                $this->include_modules();
                break;
            case 'telephony':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/telephony_modules/';
                $this->include_modules();
                break;
            case 'digital_signature':
                $this->path = DIR_FS_CATALOG . 'plugins/ext/digital_signature_modules/';
                $this->include_modules();
                break;
        }
    }

    public function include_modules()
    {
        global $app_user;

        $app_user_language = (isset($app_user['language']) ? $app_user['language'] : CFG_APP_LANGUAGE);

        if ($dir = @dir($this->path)) {
            while ($file = $dir->read()) {
                if (is_dir($this->path . $file) and $file != '.' and $file != '..') {
                    if (is_file($this->path . $file . '/' . $file . '.php') and !class_exists($file)) {
                        require($this->path . $file . '/' . $file . '.php');

                        foreach ([$app_user_language, CFG_APP_LANGUAGE, 'english.php', 'ukrainian.php'] as $lng) {
                            if (is_file($this->path . $file . '/languages/' . $lng)) {
                                require($this->path . $file . '/languages/' . $lng);
                                break;
                            }
                        }
                    }
                }
            }

            $dir->close();
        }
    }

    static function include_module($module_info, $type)
    {
        global $app_user;

        if (class_exists($module_info['module'])) {
            return false;
        }

        $module_path = DIR_FS_CATALOG . 'plugins/ext/' . $type . '_modules/';

        foreach ([$app_user['language'], CFG_APP_LANGUAGE, 'english.php', 'ukrainian.php'] as $lng) {
            if (is_file($module_path . $module_info['module'] . '/languages/' . $lng)) {
                require($module_path . $module_info['module'] . '/languages/' . $lng);
                break;
            }
        }

        require($module_path . $module_info['module'] . '/' . $module_info['module'] . '.php');
    }

    public function get_available_modules()
    {
        $modules_installed = $this->get_installed_modules();

        $modules_array = [];
        if ($dir = @dir($this->path)) {
            while ($file = $dir->read()) {
                if (is_dir($this->path . $file) and $file != '.' and $file != '..') {
                    if (is_file($this->path . $file . '/' . $file . '.php')) {
                        if (!in_array($file, $modules_installed)) {
                            $module = new $file;
                            $country = $module->country ?? '';
                            $modules_array[$country][] = $file;
                        }
                    }
                }
            }
            ksort($modules_array);
            $dir->close();
        }

        //print_rr($modules_array);

        return $modules_array;
    }

    public function get_installed_modules()
    {
        $modules_array = [];

        $modules_query = db_query("select * from app_ext_modules where type='" . $this->type . "' order by sort_order");
        while ($v = db_fetch_array($modules_query)) {
            $modules_array[] = $v['module'];
        }

        return $modules_array;
    }

    public function get_active_modules()
    {
        $modules_array = [];

        $modules_query = db_query(
            "select * from app_ext_modules where type='" . $this->type . "' and is_active=1 order by sort_order"
        );
        while ($v = db_fetch_array($modules_query)) {
            $module = new $v['module'];
            $modules_array[$v['id']] = $module->title;
        }

        return $modules_array;
    }

    public function install($module)
    {
        if (class_exists($module)) {
            $check_query = db_query("select * from app_ext_modules where module='" . $module . "'");
            if (!$check = db_fetch_array($check_query)) {
                $sql_data = [
                    'is_active' => 0,
                    'type' => $this->type,
                    'module' => $module,
                ];

                db_perform('app_ext_modules', $sql_data);
                $modules_id = db_insert_id();

                $module = new $module;

                $sql_data = [];
                foreach ($module->configuration() as $v) {
                    $sql_data[] = [
                        'modules_id' => $modules_id,
                        'cfg_key' => $v['key'],
                        'cfg_value' => $v['default'],
                    ];
                }

                db_batch_insert('app_ext_modules_cfg', $sql_data);
            }
        }
    }

    public function render_configuration($module, $modules_id)
    {
        $html = '';

        $values_schema = [];
        $values_query = db_query("select * from app_ext_modules_cfg where modules_id='" . $modules_id . "'");
        while ($values = db_fetch_array($values_query)) {
            $values_schema[$values['cfg_key']] = $values['cfg_value'];
        }

        foreach ($module->configuration() as $cfg) {
            $is_required = false;

            $cfg['key'] = $cfg['key'] ?? '';

            $value = (isset($values_schema[$cfg['key']]) ? $values_schema[$cfg['key']] : '');

            $value_html = '';
            switch ($cfg['type']) {
                case 'text':
                    $params = (isset($cfg['params']) ? $cfg['params'] : []);
                    $value_html = '<p class="form-control-static">' . $cfg['default'] . '</p>';
                    break;
                case 'input':
                    $params = (isset($cfg['params']) ? $cfg['params'] : ['class' => 'form-control input-medium']);
                    $value_html = input_tag('cfg[' . $cfg['key'] . ']', $value, $params);
                    break;
                case 'dorpdown':
                    $params = (isset($cfg['params']) ? $cfg['params'] : ['class' => 'form-control input-medium']);

                    if (isset($cfg['multiple'])) {
                        $params['multiple'] = 'multiple';
                    }

                    $value_html = select_tag(
                        'cfg[' . $cfg['key'] . ']' . (isset($cfg['multiple']) ? '[]' : ''),
                        $cfg['choices'],
                        $value,
                        $params
                    );
                    break;
            }

            $is_required = false;
            if (isset($params['class'])) {
                if (strstr($params['class'], 'required')) {
                    $is_required = true;
                }
            }

            if (isset($cfg['description'])) {
                if (strstr($cfg['description'], '%s')) {
                    $cfg['description'] = sprintf($cfg['description'], $modules_id);
                }
            }

            if ($cfg['type'] == 'section') {
                $html .= '
                    <h3 class="form-section">' . $cfg['title'] . '</h3>';
            } else {
                $html .= '
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="cfg_' . $cfg['key'] . '">' . ($is_required ? '<span class="required-label">*</span>' : '') . (isset($cfg['info']) ? tooltip_icon(
                        $cfg['info']
                    ) : '') . $cfg['title'] . '</label>
                        <div class="col-md-8">	
                                    ' . $value_html . '
                              ' . (isset($cfg['description']) ? tooltip_text($cfg['description']) : '') . '      
                        </div>			
                    </div>';
            }
        }

        return $html;
    }

    public static function get_configuration($modules_configuration, $modules_id)
    {
        $configuration = [];

        $values_schema = [];
        $values_query = db_query("select * from app_ext_modules_cfg where modules_id='" . $modules_id . "'");
        while ($values = db_fetch_array($values_query)) {
            $values_schema[$values['cfg_key']] = $values['cfg_value'];
        }

        foreach ($modules_configuration as $cfg) {
            $cfg['key'] = $cfg['key'] ?? '';

            $value = (isset($values_schema[$cfg['key']]) ? $values_schema[$cfg['key']] : '');

            $configuration[$cfg['key']] = $value;
        }

        return $configuration;
    }

    public function ipn($module_id)
    {
        $module_info_query = db_query("select * from app_ext_modules where id='" . (int)$module_id . "'");
        if ($module_info = db_fetch_array($module_info_query)) {
            $module = new $module_info['module'];
            $module->ipn($module_id);
        }
    }

    public static function status_label($status_value, $success_value)
    {
        return ($status_value == $success_value ? '<span class="label label-success">' . $status_value . '</span>' : '<span class="label label-warning">' . $status_value . '</span>');
    }

    public static function log($filename, $content)
    {
        error_log($content . "\n", 3, DIR_FS_CATALOG . '/log/' . $filename . '.txt');
    }

    public static function log_file_storage($content, $file)
    {
        $content = format_date_time(time()) . ' ' . $file['folder'] . '/' . $file['file'] . ' ' . $content;
        self::log('file_storage', $content);
    }

}
