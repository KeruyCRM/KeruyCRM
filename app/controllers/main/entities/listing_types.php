<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_types extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        //autocreate listing types if not exist
        \Models\Main\Listing_types::prepare_types(\K::$fw->GET['entities_id']);
    }

    public function index()
    {
        /*$listing_types_query = db_query(
            "select * from app_listing_types where entities_id='" . _get::int('entities_id') . "'"
        );*/

        \K::$fw->listing_types_query = \K::model()->db_fetch('app_listing_types', [
            'entities_id = ?',
            \K::$fw->GET['entities_id']
        ]);

        \K::$fw->fields_query = \K::model()->db_query_exec(
            "select r.*, f.name, f.type, f.configuration from app_listing_highlight_rules r, app_fields f where f.id = r.fields_id and r.entities_id = ? order by r.sort_order, r.id",
            \K::$fw->GET['entities_id'],
            'app_listing_highlight_rules,app_field'
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_types.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'is_active' => (isset(\K::$fw->POST['is_active']) ? 1 : 0),
                'is_default' => (isset(\K::$fw->POST['is_default']) ? 1 : 0),
                'width' => (\K::$fw->POST['width'] ?? ''),
                'settings' => (isset(\K::$fw->POST['settings']) ? json_encode(\K::$fw->POST['settings']) : ''),
            ];

            \K::model()->begin();

            //reset is_default flag
            if (isset(\K::$fw->POST['is_default'])) {
                /*db_query(
                    "update app_listing_types set is_default=0 where entities_id ='" . db_input(
                        \K::$fw->GET['entities_id']
                    ) . "'"
                );*/

                \K::model()->db_update('app_listing_types', ['is_default' => 0], [
                    'entities_id = ?',
                    \K::$fw->GET['entities_id']
                ]);
            }

            if (isset(\K::$fw->GET['id'])) {
                //db_perform('app_listing_types', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");

                \K::model()->db_update('app_listing_types', $sql_data, [
                    'id = ?',
                    \K::$fw->GET['id']
                ]);

                //reset reports listing type if it's inactive
                /*$check_query = db_query(
                    "select type from app_listing_types where id ='" . db_input(\K::$fw->GET['id']) . "' and is_active=0"
                );*/

                $check = \K::model()->db_fetch_one('app_listing_types', [
                    'id = ? and is_active = 0',
                    \K::$fw->GET['id']
                ], [], 'type');

                if ($check) {
                    //db_query("update app_reports set listing_type='' where listing_type='" . $check['type'] . "'");

                    \K::model()->db_update('app_reports', ['listing_type' => ''], [
                        'listing_type = ?',
                        $check['type']
                    ]);
                }
            } else {
                \K::model()->db_perform('app_listing_types', $sql_data);
            }

            //check is_default flag
            /*$check_query = db_query(
                "select * from app_listing_types where entities_id ='" . db_input(
                    \K::$fw->GET['entities_id']
                ) . "' and is_default=1"
            );*/

            $check = \K::model()->db_fetch_one('app_listing_types', [
                'entities_id = ? and is_default = 1',
                \K::$fw->GET['entities_id']
            ], [], 'id');

            if (!$check) {
                /*db_query(
                    "update app_listing_types set is_default=1 where entities_id ='" . db_input(
                        \K::$fw->GET['entities_id']
                    ) . "' and type='table'"
                );*/

                \K::model()->db_update('app_listing_types', ['is_default' => 1], [
                    'entities_id = ? and type = ?',
                    \K::$fw->GET['entities_id'],
                    'table'
                ]);
            }

            \K::model()->commit();

            \Helpers\Urls::redirect_to('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}