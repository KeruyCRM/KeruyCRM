<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Comments_access extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'comments_access.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function set_access()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            if (isset(\K::$fw->POST['access'])) {
                \K::model()->begin();

                foreach (\K::$fw->POST['access'] as $access_groups_id => $access) {
                    $sql_data = ['access_schema' => str_replace('_', ',', $access)];
                    $sql_data['entities_id'] = \K::$fw->GET['entities_id'];
                    $sql_data['access_groups_id'] = $access_groups_id;

                    \K::model()->db_perform('app_comments_access', $sql_data, [
                        'entities_id = ? and access_groups_id = ?',
                        \K::$fw->GET['entities_id'],
                        $access_groups_id

                    ]);
                    /*$acess_info_query = db_query(
                        "select access_schema from app_comments_access where entities_id='" . db_input(
                            $_GET['entities_id']
                        ) . "' and access_groups_id='" . $access_groups_id . "'"
                    );
                    if ($acess_info = db_fetch_array($acess_info_query)) {
                        db_perform(
                            'app_comments_access',
                            $sql_data,
                            'update',
                            "entities_id='" . db_input(
                                $_GET['entities_id']
                            ) . "' and access_groups_id='" . $access_groups_id . "'"
                        );
                    } else {
                        $sql_data['entities_id'] = $_GET['entities_id'];
                        $sql_data['access_groups_id'] = $access_groups_id;
                        db_perform('app_comments_access', $sql_data);
                    }*/
                }

                \K::model()->commit();

                \K::flash()->addMessage(\K::$fw->TEXT_ACCESS_UPDATED, 'success');
            }

            \Helpers\Urls::redirect_to('main/entities/comments_access', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}