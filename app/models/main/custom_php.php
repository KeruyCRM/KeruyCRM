<?php

namespace Models\Main;

class Custom_php
{
    public static function include()
    {
        $exclude_code_id = false;

        //skip in custom_php page
        if (isset($_SERVER['REQUEST_URI']) and strstr(
                $_SERVER['REQUEST_URI'],
                'module=custom_php/code'
            ) and isset($_POST['code_id']) and $_POST['code_id'] > 0) {
            $exclude_code_id = _POST('code_id');
        }

        /*$code_query = db_query(
            "select * from app_custom_php where is_folder=0 and is_active=1 " . ($exclude_code_id ? " and id!=" . $exclude_code_id : "")
        );*/
        $code_query = \K::model()->db_fetch(
            'app_custom_php',
            [
                'is_folder = 0 and is_active = 1' . ($exclude_code_id ? ' and id != ?' : ''),
                ($exclude_code_id ? $exclude_code_id : '')
            ],
            [],
            'code',//FIX
            [\K::$fw->TTL_APP, 'app_custom_php']
        );

        //while ($code = db_fetch($code_query)) {
        foreach ($code_query as $code) {
            if (strlen($code->code)) {
                try {
                    eval($code->code);
                } catch (\Error $e) {
                    //skip code with erros
                }
            }
        }
    }

    public static function get_tree($parent_id = 0, $tree = [], $level = 0)
    {
        /*$code_query = db_query(
            "select * from app_custom_php where parent_id=" . $parent_id . " order by sort_order, name"
        );*/
        $code_query = \K::model()->db_fetch(
            'app_custom_php',
            ['parent_id = ?', $parent_id],
            ['order' => 'sort_order, name']
        );

        //while ($code = db_fetch_array($code_query)) {
        foreach ($code_query as $code) {
            $code = $code->cast();

            $code['level'] = $level;

            $tree[] = $code;

            $tree = self::get_tree($code['id'], $tree, $level + 1);
        }

        return $tree;
    }

    public static function get_folder_choices()
    {
        $choices = [];
        $choices[''] = '';

        foreach (self::get_tree() as $v) {
            if ($v['is_folder']) {
                $choices[$v['id']] = custom_php . phpstr_repeat(' - ', $v['level']);
            }
        }

        return $choices;
    }
}