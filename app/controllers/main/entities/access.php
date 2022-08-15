<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Access extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['entities_id'])) {
            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'access.php';

            echo \K::view()->render(\K::$fw->app_layout);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function set_access()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            if (isset(\K::$fw->POST['access'])) {
                \K::model()->begin();

                foreach (\K::$fw->POST['access'] as $access_groups_id => $access) {
                    $access_schema = [];

                    foreach ($access as $v) {
                        $access_schema[] = $v;
                    }

                    $access_schema = \Models\Main\Access_groups::prepare_entities_access_schema($access_schema);

                    $sql_data = ['access_schema' => implode(',', $access_schema)];
                    $sql_data['entities_id'] = \K::$fw->GET['entities_id'];
                    $sql_data['access_groups_id'] = $access_groups_id;

                    \K::model()->db_perform('app_entities_access', $sql_data, [
                        'entities_id = ? and access_groups_id = ?',
                        \K::$fw->GET['entities_id'],
                        $access_groups_id
                    ]);
                    /*$access_info_query = db_query(
                        "select access_schema from app_entities_access where entities_id='" . db_input(
                            \K::$fw->GET['entities_id']
                        ) . "' and access_groups_id='" . $access_groups_id . "'"
                    );

                    if ($access_info) {
                        db_perform(
                            'app_entities_access',
                            $sql_data,
                            'update',
                            "entities_id='" . db_input(
                                \K::$fw->GET['entities_id']
                            ) . "' and access_groups_id='" . $access_groups_id . "'"
                        );
                    } else {
                        $sql_data['entities_id'] = \K::$fw->GET['entities_id'];
                        $sql_data['access_groups_id'] = $access_groups_id;
                        db_perform('app_entities_access', $sql_data);
                    }*/
                }

                \K::model()->commit();

                \K::flash()->addMessage(\K::$fw->TEXT_ACCESS_UPDATED, 'success');
            }

            \Helpers\Urls::redirect_to('main/entities/access', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}