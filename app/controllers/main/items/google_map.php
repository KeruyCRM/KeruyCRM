<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Google_map extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        \Helpers\Urls::redirect_to('main/dashboard');
    }

    public function save_value_in()
    {
        if (\K::$fw->VERB == 'POST') {
            $filed_id = (int)\K::$fw->POST['filed_id'];
            $distance = (is_numeric(\K::$fw->POST['distance']) ? \K::$fw->POST['distance'] : 0);

            /*$item_info_query = db_query(
                "select field_{$filed_id} from app_entity_{\K::$fw->current_entity_id} where id={\K::$fw->current_item_id}"
            );*/

            $item_info = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                'id = ?',
                \K::$fw->current_item_id
            ], [], 'field_' . $filed_id);

            if ($item_info and $item_info['field_' . $filed_id] != $distance) {
                /*db_query(
                    "update app_entity_{\K::$fw->current_entity_id} set field_{$filed_id}={$distance} where id={\K::$fw->current_item_id}"
                );*/

                \K::model()->db_update(
                    'app_entity_' . (int)\K::$fw->current_entity_id,
                    ['field_' . $filed_id => $distance],
                    [
                        'id = ?',
                        \K::$fw->current_item_id
                    ]
                );

                echo 'UPDATED';
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_latlng()
    {
        if (\K::$fw->VERB == 'POST') {
            $filed_id = (int)\K::$fw->POST['filed_id'];

            /*$item_info_query = db_query(
                "select field_{$filed_id} from app_entity_{\K::$fw->current_entity_id} where id={\K::$fw->current_item_id}"
            );*/

            $item_info = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                'id = ?',
                \K::$fw->current_item_id
            ], [], 'field_' . $filed_id);

            if ($item_info and strlen($item_info['field_' . $filed_id])) {
                //get current address
                $value = explode("\t", $item_info['field_' . $filed_id]);

                $current_address = $value[2];

                $value = \K::$fw->POST['lat'] . "\t" . \K::$fw->POST['lng'] . "\t" . $current_address;

                /*db_query(
                    "update app_entity_{\K::$fw->current_entity_id} set field_{$filed_id}='" . db_input(
                        $value
                    ) . "' where id='" . db_input(\K::$fw->current_item_id) . "'"
                );*/

                \K::model()->db_update(
                    'app_entity_' . (int)\K::$fw->current_entity_id,
                    ['field_' . $filed_id => $value],
                    [
                        'id = ?',
                        \K::$fw->current_item_id
                    ]
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_latlng_multiple()
    {
        if (\K::$fw->VERB == 'POST') {
            $filed_id = (int)\K::$fw->POST['filed_id'];
            $address_key = \K::$fw->POST['address_key'];

            /*$item_info_query = db_query(
                "select field_{$filed_id} from app_entity_{\K::$fw->current_entity_id} where id={\K::$fw->current_item_id}"
            );*/

            $item_info = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                'id = ?',
                \K::$fw->current_item_id
            ], [], 'field_' . $filed_id);

            if ($item_info and strlen($item_info['field_' . $filed_id])) {
                //get current address

                $item_address_array = preg_split("/\\r\\n|\\r|\\n/", $item_info['field_' . $filed_id]);

                if (isset($item_address_array[$address_key])) {
                    if (strlen($item_address_array[$address_key])) {
                        $value = explode("\t", $item_address_array[$address_key]);

                        $item_address_array[$address_key] = \K::$fw->POST['lat'] . "\t" . \K::$fw->POST['lng'] . "\t" . $value[2];
                    }
                }

                /*db_query(
                    "update app_entity_{\K::$fw->current_entity_id} set field_{$filed_id}='" . db_input(
                        implode("\n", $item_address_array)
                    ) . "' where id='" . db_input(\K::$fw->current_item_id) . "'"
                );*/

                \K::model()->db_update(
                    'app_entity_' . (int)\K::$fw->current_entity_id,
                    ['field_' . $filed_id => implode("\n", $item_address_array)],
                    [
                        'id = ?',
                        \K::$fw->current_item_id
                    ]
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function update_latlng_directions()
    {
        if (\K::$fw->VERB == 'POST') {
            $filed_id = (int)\K::$fw->POST['filed_id'];
            $lat = \K::$fw->POST['lat'];
            $lng = \K::$fw->POST['lng'];

            /*$item_info_query = db_query(
                "select field_{$filed_id} from app_entity_{\K::$fw->current_entity_id} where id={\K::$fw->current_item_id}"
            );*/

            $item_info = \K::model()->db_fetch_one('app_entity_' . \K::$fw->current_entity_id, [
                'id = ?',
                \K::$fw->current_item_id
            ], [], 'field_' . $filed_id);

            if ($item_info and strlen($item_info['field_' . $filed_id])) {
                $item_address_array = preg_split("/\\r\\n|\\r|\\n/", $item_info['field_' . $filed_id]);

                foreach ($lat as $address_key => $lat_value) {
                    if (isset($item_address_array[$address_key])) {
                        if (strlen($item_address_array[$address_key])) {
                            $value = explode("\t", $item_address_array[$address_key]);

                            $item_address_array[$address_key] = $lat_value . "\t" . $lng[$address_key] . "\t" . $value[2];
                        }
                    }
                }

                /*db_query(
                    "update app_entity_{\K::$fw->current_entity_id} set field_{$filed_id}='" . db_input(
                        implode("\n", $item_address_array)
                    ) . "' where id='" . db_input(\K::$fw->current_item_id) . "'"
                );*/

                \K::model()->db_update(
                    'app_entity_' . (int)\K::$fw->current_entity_id,
                    ['field_' . $filed_id => implode("\n", $item_address_array)],
                    [
                        'id = ?',
                        \K::$fw->current_item_id
                    ]
                );
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}