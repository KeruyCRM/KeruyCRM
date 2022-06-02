<?php

class license
{
    static public function check()
    {
        global $app_module_path, $alerts, $app_plugin_path;
        return true;
        if ($app_plugin_path != 'plugins/ext/') {
            return true;
        }

        if (!in_array($app_module_path, ['ext/license/key', 'ext/ext/ext', 'ext/ext/install']) and defined(
                'CFG_PLUGIN_EXT_INSTALLED'
            )) {
            if (!defined('CFG_PLUGIN_EXT_LICENSE_KEY')) {
                $alerts->add(TEXT_EXT_LICENSE_KEY_NOT_SET, 'error');

                redirect_to('ext/license/key');
            } elseif (!license::check_key()) {
                redirect_to('ext/license/key');
            }
        }
    }

    static public function check_key()
    {
        return true;
        $domain_name = $_SERVER['HTTP_HOST'];

        $domain_name = str_replace('www.', '', $domain_name);

        $key = '';
        for ($i = 0; $i < strlen($domain_name); $i++) {
            $key .= ord($domain_name[$i]) * (strlen($domain_name) + ord($domain_name[0]));
        }

        foreach (explode('&', CFG_PLUGIN_EXT_LICENSE_KEY) as $license_key) {
            if (trim($license_key) == trim($key)) {
                return true;
            }
        }

        return false;
    }
}