<?php

class import_templates
{
    static function get_choices($entities_id)
    {
        global $app_user;

        $choices = [];
        $choices[] = '';
        $templates_query = db_query(
            "select * from app_ext_import_templates where entities_id=" . (int)$entities_id . " and find_in_set(" . $app_user['group_id'] . ",users_groups) order by sort_order, name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            $choices[$templates['id']] = $templates['name'];
        }

        return $choices;
    }
}