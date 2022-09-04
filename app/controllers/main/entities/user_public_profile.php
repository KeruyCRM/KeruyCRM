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

        if (\K::$fw->GET['entities_id'] != 1) {
            \Helpers\Urls::redirect_to('main/entities/entities_configuration', 'entities_id=1');
        }

        /*$cfq_query = db_query(
            "select * from app_configuration where configuration_name='CFG_PUBLIC_USER_PROFILE_FIELDS'"
        );*/

        $cfq = \K::model()->db_fetch_one('app_configuration', [
            'configuration_name = ?',
            'CFG_PUBLIC_USER_PROFILE_FIELDS'
        ]);

        if (!$cfq) {
            \K::model()->db_perform(
                'app_configuration',
                ['configuration_value' => '', 'configuration_name' => 'CFG_PUBLIC_USER_PROFILE_FIELDS']
            );

            \Helpers\Urls::redirect_to('main/entities/user_public_profile', 'entities_id=1');
        }

        $fields_query = \K::model()->db_query_exec_one(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
            ) . "," . \Models\Main\Fields_types::get_users_types_list(
            ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        if (!$fields_query) {
            \K::flash()->addMessage(\K::$fw->TEXT_USER_PUBLIC_PROFILE_NO_FIELDS, 'warning');
        }
    }

    public function index()
    {
        $public_user_profile_fields = (strlen(
            \K::$fw->CFG_PUBLIC_USER_PROFILE_FIELDS
        ) == 0 ? '0' : \K::$fw->CFG_PUBLIC_USER_PROFILE_FIELDS);

        \K::$fw->fields_query_in = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
            ) . "," . \K::model()->quoteToString(
                ['fieldtype_section', 'fieldtype_user_photo', 'fieldtype_user_skin', 'fieldtype_user_language']
            ) . ") and f.id in (" . $public_user_profile_fields . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by field(f.id," . $public_user_profile_fields . ")",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        \K::$fw->fields_query_notin = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
            ) . "," . \K::model()->quoteToString(
                ['fieldtype_section', 'fieldtype_user_photo', 'fieldtype_user_skin', 'fieldtype_user_language']
            ) . ") and f.id not in (" . $public_user_profile_fields . ") and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'user_public_profile.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function sort_fields()
    {
        if (\K::$fw->VERB == 'POST') {
            $fields_list = [];
            foreach (explode(',', \K::$fw->POST['fields_in_profile']) as $v) {
                $fields_list[] = str_replace('form_fields_', '', $v);
            }

            \K::model()->db_update('app_configuration', ['configuration_value' => implode(',', $fields_list)], [
                    'configuration_name = ?',
                    'CFG_PUBLIC_USER_PROFILE_FIELDS'
                ]
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}