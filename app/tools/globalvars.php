<?php

namespace Tools;

class GlobalVars extends \Prefab
{
    private $vars;

    public function __construct()
    {
        $this->vars = [];

        $vars_query = \K::model()->db_fetch(
            'app_global_vars', ['is_folder = ?', 0],
            [],
            null,
            [\K::$fw->TTL_APP, 'app_global_vars']
        );

        //while ($vars = db_fetch($vars_query)) {
        foreach ($vars_query as $vars) {
            \K::$fw->{'VAR_' . $vars->name} = $vars->value;
            $this->vars['VAR_' . $vars->name] = $vars->value;
        }
    }

    public function apply_to_text($text)
    {
        $app_user = \K::$fw->app_user;

        $this->vars['[current_user_id]'] = $app_user['id'];
        $this->vars['[current_user_group_id]'] = $app_user['group_id'];

        $name = array_keys($this->vars);
        $value = array_values($this->vars);

        return str_replace($name, $value, $text);
    }

    public static function get_tree($parent_id = 0, $tree = [], $level = 0)
    {
        //"select * from app_global_vars where parent_id = :parent_id order by sort_order, name",
        //[':parent_id' => $parent_id]
        $vars_query = \K::model()->db_fetch(
            'app_global_vars',
            ['parent_id = ?', $parent_id],
            ['order' => 'sort_order, name']
        );

        //while ($vars = db_fetch_array($vars_query)) {
        foreach ($vars_query as $vars) {
            $vars = $vars->cast();
            $vars['level'] = $level;

            $tree[] = $vars;

            $tree = self::get_tree($vars['id'], $tree, $level + 1);
        }

        return $tree;
    }

    public static function get_folder_choices()
    {
        $choices = [];
        $choices[''] = '';

        foreach (self::get_tree() as $v) {
            if ($v['is_folder']) {
                $choices[$v['id']] = str_repeat(' - ', $v['level']) . $v['name'];
            }
        }

        return $choices;
    }
}
