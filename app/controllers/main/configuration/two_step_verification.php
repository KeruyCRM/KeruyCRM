<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Two_step_verification extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        \K::$fw->two_step_verification_info['is_checked'] = true;

        $choices = ['' => ''];
        $fields_query = \K::model()->db_query_exec(
            'select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (?,?,?) and f.entities_id = 1 and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name',
            ['fieldtype_input', 'fieldtype_input_masked', 'fieldtype_phone']
        );
        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['id']] = $fields['name'];
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'two_step_verification.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }
}