<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class User_public_profile extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        if ($_GET['entities_id'] != 1) {
            redirect_to('entities/entities_configuration', 'entities_id=1');
        }

        $cfq_query = db_query(
            "select * from app_configuration where configuration_name='CFG_PUBLIC_USER_PROFILE_FIELDS'"
        );
        if (!$cfq = db_fetch_array($cfq_query)) {
            db_perform(
                'app_configuration',
                ['configuration_value' => '', 'configuration_name' => 'CFG_PUBLIC_USER_PROFILE_FIELDS']
            );
            redirect_to('entities/user_public_profile', 'entities_id=1');
        }

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list(
            ) . "," . fields_types::get_users_types_list() . ") and f.entities_id='" . db_input(
                $_GET['entities_id']
            ) . "' and f.forms_tabs_id=t.id"
        );
        if (!$v = db_fetch_array($fields_query)) {
            $alerts->add(TEXT_USER_PUBLIC_PROFILE_NO_FIELDS, 'warning');
        }
    }

    public function index()
    {
        $public_user_profile_fields = (strlen(
            CFG_PUBLIC_USER_PROFILE_FIELDS
        ) == 0 ? '0' : CFG_PUBLIC_USER_PROFILE_FIELDS);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_public_profile.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function sort_fields()
    {
        $fields_list = [];
        foreach (explode(',', $_POST['fields_in_profile']) as $v) {
            $fields_list[] = str_replace('form_fields_', '', $v);
        }

        db_perform(
            'app_configuration',
            ['configuration_value' => implode(',', $fields_list)],
            'update',
            "configuration_name='CFG_PUBLIC_USER_PROFILE_FIELDS'"
        );
    }
}