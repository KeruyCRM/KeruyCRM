<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of global_vars
 *
 * @author USER
 */
class global_vars
{
    private $vars;

    function __construct()
    {
        global $app_user;

        $this->vars = [];

        $vars_query = db_query("select * from app_global_vars where is_folder=0");
        while ($vars = db_fetch($vars_query)) {
            define('VAR_' . $vars->name, $vars->value);

            $this->vars['VAR_' . $vars->name] = $vars->value;
        }
    }

    function apply_to_text($text)
    {
        global $app_user;

        $this->vars['[current_user_id]'] = $app_user['id'];
        $this->vars['[current_user_group_id]'] = $app_user['group_id'];

        //foreach ($this->vars as $name => $value) {
        $name = array_keys($this->vars);
        $value = array_values($this->vars);

        //BOOST!
        /*
         * f_str_foreach    0.02364397ms      38.8kb
         * f_str_array      0.00800300ms      37.6kb
         */
        $text = str_replace($name, $value, $text);
        //}

        return $text;
    }

    static function get_tree($parent_id = 0, $tree = [], $level = 0)
    {
        $vars_query = db_query(
            "select * from app_global_vars where parent_id=" . $parent_id . " order by sort_order, name"
        );

        while ($vars = db_fetch_array($vars_query)) {
            $vars['level'] = $level;

            $tree[] = $vars;

            $tree = self::get_tree($vars['id'], $tree, $level + 1);
        }

        return $tree;
    }

    static function get_folder_choices()
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
