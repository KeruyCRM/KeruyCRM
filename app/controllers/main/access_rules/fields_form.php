<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Fields_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();
    }

    public function index()
    {
        \K::$fw->obj = \K::model()->db_find('app_access_rules_fields', \K::$fw->GET['id']);

        $choices = [];
        $fields_query = \K::model()->db_query_exec(
        //TODO simple query but hard?
            "select f.id, f.type, f.name, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_autostatus','fieldtype_stages') and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->POST['entities_id'],
            'app_fields,app_forms_tabs'
        );

        //while ($v = db_fetch_array($fields_query)) {
        foreach ($fields_query as $v) {
            $choices[$v['id']] = \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']);
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields_form.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }
}