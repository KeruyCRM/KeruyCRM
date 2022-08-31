<?php

namespace Models\Main\Items;

class Approved_items
{
    public static function is_approved_by_user($entities_id, $items_id, $fields_id, $users_id)
    {
        /*$check_query = db_query(
            "select id, date_added from app_approved_items where entities_id='" . $entities_id . "' and items_id='" . $items_id . "' and fields_id='" . $fields_id . "' and users_id='" . $users_id . "'"
        );*/

        $check = \K::model()->db_fetch_one('app_approved_items', [
            'entities_id = ? and items_id = ? and fields_id = ? and users_id = ?',
            $entities_id,
            $items_id,
            $fields_id,
            $users_id
        ], [], 'id');

        if ($check) {
            return $check;
        } else {
            return false;
        }
    }

    public static function get_approved_users_by_field($entities_id, $items_id, $fields_id)
    {
        $users = [];

        $check_query = db_query(
            "select users_id,signature from app_approved_items where entities_id='" . $entities_id . "' and items_id='" . $items_id . "' and fields_id='" . $fields_id . "'"
        );
        while ($check = db_fetch_array($check_query)) {
            $users[$check['users_id']] = ['signature' => $check['signature']];
        }
        return $users;
    }

    public static function is_all_approved($entities_id, $items_id, $fields_id)
    {
        //$item_info_query = db_query("select field_{$fields_id} from app_entity_{$entities_id} where id={$items_id}");

        $item_info = \K::model()->db_fetch_one('app_entity_' . (int)$entities_id, [
            'id = ?',
            $items_id
        ], [], 'field_' . (int)$fields_id);

        if ($item_info) {
            /*$check_query = db_query(
                "select count(*) as total from app_approved_items where entities_id='" . $entities_id . "' and items_id='" . $items_id . "' and fields_id='" . $fields_id . "'"
            );

            $check = db_fetch_array($check_query);*/

            $check = \K::model()->db_fetch_one('app_approved_items', [
                'entities_id = ? and items_id = ? and fields_id = ?',
                $entities_id,
                $items_id,
                $fields_id
            ], [], null, ['total' => 'count(id)']);

            if ($check['total'] == count(explode(',', $item_info['field_' . $fields_id]))) {
                return true;
            }
        }
        return false;
    }
}