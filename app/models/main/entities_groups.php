<?php

class entities_groups
{
    static function get_name_by_id($id)
    {
        if (!$id) {
            return '';
        }

        $info_query = db_query("select name from app_entities_groups where id={$id}");
        if ($info = db_fetch_array($info_query)) {
            return $info['name'];
        } else {
            return '';
        }
    }

    static function delete($id)
    {
        db_query("delete from app_entities_groups where id={$id}");
        db_query("update app_entities set group_id=0 where group_id={$id}");
    }

    static function get_choices()
    {
        $choices = ['' => ''];
        $info_query = db_query("select id, name from app_entities_groups order by sort_order, name");
        while ($info = db_fetch_array($info_query)) {
            $choices[$info['id']] = $info['name'];
        }

        return $choices;
    }
}
