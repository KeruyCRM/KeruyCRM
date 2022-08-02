<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Models\Main;

class Entities_groups
{
    static function get_name_by_id($id)
    {
        if (!$id) {
            return '';
        }

        //$info_query = db_query("select name from app_entities_groups where id={$id}");

        $info = \K::model()->db_fetch_one('app_entities_groups', [
            'id = ?',
            $id
        ], [], 'name');

        if ($info) {
            return $info['name'];
        } else {
            return '';
        }
    }

    static function delete($id)
    {
        //db_query("delete from app_entities_groups where id={$id}");
        //db_query("update app_entities set group_id=0 where group_id={$id}");
        $forceCommit = false;
        if (!\K::model()->trans()) {
            \K::model()->begin();
            $forceCommit = true;
        }

        \K::model()->db_delete('app_entities_groups', ['id = ?', $id]);
        \K::model()->db_update('app_entities', ['group_id' => 0], ['group_id = ?', $id]);

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    static function get_choices()
    {
        $choices = ['' => ''];
        //$info_query = db_query("select id, name from app_entities_groups order by sort_order, name");

        $info_query = \K::model()->db_fetch('app_entities_groups', [], ['order' => 'sort_order,name'], 'id,name');

        //while ($info = db_fetch_array($info_query)) {
        foreach ($info_query as $info) {
            $info = $info->cast();

            $choices[$info['id']] = $info['name'];
        }

        return $choices;
    }
}
