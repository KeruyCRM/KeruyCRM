<?php

class smart_input
{
    public $entities_id;

    function __construct($entities_id)
    {
        $this->entities_id = $entities_id;
    }

    function render()
    {
        global $js_includes;

        $modules = new modules('smart_input');

        $html = '';

        $rules_query = db_query(
            "select m.module, m.id as modules_id, sir.fields_id, sir.type, sir.rules from app_ext_smart_input_rules sir, app_ext_modules m where sir.modules_id=m.id and m.is_active=1 and sir.entities_id='" . $this->entities_id . "'"
        );
        while ($rules = db_fetch_array($rules_query)) {
            $module = new $rules['module'];

            $html .= $module->render($rules['modules_id'], $rules);
        }

        return $html;
    }

    static function render_js_includes()
    {
        global $js_includes;

        $modules = new modules('smart_input');

        $html = '';

        $rules_query = db_query(
            "select m.module, m.id as modules_id, sir.fields_id, sir.type, sir.rules from app_ext_smart_input_rules sir, app_ext_modules m where sir.modules_id=m.id and m.is_active=1 group by m.id"
        );
        while ($rules = db_fetch_array($rules_query)) {
            $module = new $rules['module'];

            $html .= $module->render_js_includes($rules['modules_id']);
        }

        return $html;
    }

    static function render_module_itnegration_types_by_id($modules_id, $type)
    {
        $modules_query = db_query("select * from app_ext_modules where id='" . $modules_id . "'");
        if ($modules = db_fetch_array($modules_query)) {
            $module = new $modules['module'];

            return $module->render_itnegration_types($type);
        }

        return '';
    }

    static function render_module_itnegration_type_name($modules_id, $type)
    {
        $modules_query = db_query("select * from app_ext_modules where id='" . $modules_id . "'");
        if ($modules = db_fetch_array($modules_query)) {
            $module = new $modules['module'];

            return $module->render_itnegration_type_name($type);
        }

        return '';
    }

    static function render_module_itnegration_rules_by_id($modules_id, $rules, $entity_field_html)
    {
        $modules_query = db_query("select * from app_ext_modules where id='" . $modules_id . "'");
        if ($modules = db_fetch_array($modules_query)) {
            $module = new $modules['module'];

            return $module->render_itnegration_rules($rules, $entity_field_html);
        }

        return '';
    }
}