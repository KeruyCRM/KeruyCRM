<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Public_users_registration extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        $choices = ['' => \K::$fw->TEXT_NONE];
        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_type_list_excluded_in_form(
            ) . ',' . str_replace(
                "'fieldtype_user_photo',",
                '',
                \Models\Main\Fields_types::get_users_types_list()
            ) . ") and f.entities_id = '1' and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['tab_name']][$fields['id']] = \Models\Main\Fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            );
        }

        \K::$fw->choices = $choices;

        $choices2 = ['' => \K::$fw->TEXT_NONE];
        /*$users_query = db_query(
            "select u.* from app_entity_1 u where u.field_6=0 order by u.field_8, u.field_7"
        );*/
        $users_query = \K::model()->db_fetch('app_entity_1', ['field_6 = 0'], ['order' => 'field_8,field_7'], 'id');
        //while ($users = db_fetch_array($users_query)) {
        foreach ($users_query as $users) {
            $choices2[$users['id']] = \K::$fw->app_users_cache[$users['id']]['name'];
        }
        \K::$fw->choices2 = $choices2;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'public_users_registration.php';

        echo \K::view()->render($this->app_layout);
    }
}