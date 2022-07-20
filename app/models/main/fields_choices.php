<?php

namespace Models\Main;

class Fields_choices
{

    public static function check_before_delete($id)
    {
        return '';
    }

    public static function get_name_by_id($id)
    {
        $obj = db_find('app_fields_choices', $id);

        return $obj['name'];
    }

    public static function get_default_id($fields_id)
    {
        $obj_query = db_query(
            "select * from app_fields_choices where fields_id = '" . db_input($fields_id) . "' and is_default=1 limit 1"
        );

        if ($obj = db_fetch_array($obj_query)) {
            return $obj['id'];
        } else {
            return 0;
        }
    }

    public static function get_tree(
        $fields_id,
        $parent_id = 0,
        $tree = [],
        $level = 0,
        $display_choices_values = '',
        $selected_values = '',
        $check_status = false
    ) {
        $where_sql = '';

        if ($check_status) {
            /*$where_sql = " and (is_active = 1 " . (strlen($selected_values) ? " or id in (" . implode(
                        ',',
                        array_map(function ($v) {
                            return (int)$v;
                        }, explode(',', $selected_values))
                    ) . ")" : '') . ") ";*/
            $where_sql = " and (is_active = 1 " . (strlen($selected_values) ? " or id in (" . \K::model(
                    )->quoteToString(explode(',', $selected_values), \PDO::PARAM_INT) . ")" : '') . ") ";
        }

        /*$choices_query = db_query(
            "select * from app_fields_choices where fields_id = '" . db_input(
                $fields_id
            ) . "' and parent_id='" . db_input($parent_id) . "' {$where_sql} order by sort_order, name"
        );*/

        $choices_query = \K::model()->db_fetch('app_fields_choices', [
            'fields_id = ? and parent_id = ?' . $where_sql,
            $fields_id,
            $parent_id
        ], ['order' => 'sort_order,name']);

        //while ($v = db_fetch_array($choices_query)) {
        foreach ($choices_query as $v) {
            $v = $v->cast();

            if ($display_choices_values == 1) {
                $v['name'] = $v['name'] . (strlen(
                        $v['value']
                    ) ? ' (' . ($v['value'] >= 0 ? '+' : '') . $v['value'] . ')' : '');
            }

            $tree[] = array_merge($v, ['level' => $level]);

            $tree = self::get_tree(
                $fields_id,
                $v['id'],
                $tree,
                $level + 1,
                $display_choices_values,
                $selected_values,
                $check_status
            );
        }

        return $tree;
    }

    public static function get_js_level_tree(
        $fields_id,
        $parent_id = 0,
        $tree = [],
        $level = 0,
        $display_choices_values = '',
        $selected_values = ''
    ) {
        $choices_query = db_query(
            "select * from app_fields_choices where fields_id = '" . db_input(
                $fields_id
            ) . "' and parent_id='" . db_input($parent_id) . "' and (is_active=1 " . (strlen(
                $selected_values
            ) ? " or id in (" . implode(
                    ',',
                    array_map(function ($v) {
                        return (int)$v;
                    }, explode(',', $selected_values))
                ) . ")" : '') . ") order by sort_order, name"
        );

        while ($v = db_fetch_array($choices_query)) {
            if ($parent_id > 0) {
                if ($display_choices_values == 1) {
                    $v['name'] = $v['name'] . (strlen(
                            $v['value']
                        ) ? ' (' . ($v['value'] >= 0 ? '+' : '') . $v['value'] . ')' : '');
                }

                $tree[$parent_id][] = '
  					$(update_field).append($("<option>", {value: ' . $v['id'] . ',text: "' . addslashes(
                        strip_tags($v['name'])
                    ) . '"}));';
            }

            $tree = fields_choices::get_js_level_tree(
                $fields_id,
                $v['id'],
                $tree,
                $level + 1,
                $display_choices_values,
                $selected_values
            );
        }

        return $tree;
    }

    public static function get_html_tree($fields_id, $parent_id = 0, $tree = '')
    {
        $count_query = db_query(
            "select count(*) as total from app_fields_choices where fields_id = '" . db_input(
                $fields_id
            ) . "' and parent_id='" . db_input($parent_id) . "' order by sort_order, name"
        );
        $count = db_fetch_array($count_query);

        if ($count['total'] > 0) {
            $tree .= '<ol class="dd-list">';

            $choices_query = db_query(
                "select * from app_fields_choices where fields_id = '" . db_input(
                    $fields_id
                ) . "' and parent_id='" . db_input($parent_id) . "' order by sort_order, name"
            );

            while ($v = db_fetch_array($choices_query)) {
                $tree .= '<li class="dd-item" data-id="' . $v['id'] . '"><div class="dd-handle">' . $v['name'] . '</div>';

                $tree = self::get_html_tree($fields_id, $v['id'], $tree);

                $tree .= '</li>';
            }

            $tree .= '</ol>';
        }

        return $tree;
    }

    public static function sort_tree($fields_id, $tree, $parent_id = 0)
    {
        $sort_order = 0;
        foreach ($tree as $v) {
            db_query(
                "update app_fields_choices set parent_id='" . $parent_id . "', sort_order='" . $sort_order . "' where id='" . db_input(
                    $v['id']
                ) . "' and fields_id='" . db_input($fields_id) . "'"
            );

            if (isset($v['children'])) {
                self::sort_tree($fields_id, $v['children'], $v['id']);
            }

            $sort_order++;
        }
    }

    public static function get_choices(
        $fields_id,
        $add_empty = true,
        $empty_text = '',
        $display_choices_values = '',
        $selected_values = '',
        $check_status = false
    ) {
        $choices = [];

        $tree = fields_choices::get_tree(
            $fields_id,
            0,
            [],
            0,
            $display_choices_values,
            $selected_values,
            $check_status
        );

        if (count($tree) > 0) {
            if ($add_empty) {
                $choices[''] = $empty_text;
            }

            foreach ($tree as $v) {
                $choices[$v['id']] = str_repeat(' - ', $v['level']) . $v['name'];
            }
        }

        return $choices;
    }

    public static function get_choices_with_color(
        $fields_id,
        $add_empty = true,
        $empty_text = '',
        $display_choices_values = '',
        $selected_values = '',
        $check_status = false
    ) {
        $choices = [];

        $tree = fields_choices::get_tree(
            $fields_id,
            0,
            [],
            0,
            $display_choices_values,
            $selected_values,
            $check_status
        );

        if (count($tree) > 0) {
            if ($add_empty) {
                $choices[''] = $empty_text;
            }

            foreach ($tree as $v) {
                $choices[$v['id']] = ['name' => str_repeat(' - ', $v['level']) . $v['name'], 'color' => $v['bg_color']];
            }
        }

        return $choices;
    }

    public static function get_cache()
    {
        $list = [];

        //$choices_query = db_query("select * from app_fields_choices");
        $choices_query = \K::model()->db_fetch_all('app_fields_choices', null, [\K::$fw->TTL_APP, 'app_fields_choices']
        );

        //while ($v = db_fetch_array($choices_query)) {
        foreach ($choices_query as $v) {
            $v = $v->cast();

            $list[$v['id']] = $v;
        }

        return $list;
    }

    public static function render_value($values = [], $is_export = false)
    {
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        $html = '';
        foreach ($values as $id) {
            if (isset(\K::$fw->app_choices_cache[$id])) {
                if ($is_export) {
                    $html .= (strlen(
                        $html
                    ) == 0 ? \K::$fw->app_choices_cache[$id]['name'] : ', ' . \K::$fw->app_choices_cache[$id]['name']);
                } elseif (strlen(\K::$fw->app_choices_cache[$id]['bg_color']) > 0) {
                    $html .= \Helpers\App::render_bg_color_block(
                        \K::$fw->app_choices_cache[$id]['bg_color'],
                        \K::$fw->app_choices_cache[$id]['name']
                    );
                } else {
                    $html .= '<div>' . \K::$fw->app_choices_cache[$id]['name'] . '</div>';
                }
            }
        }

        return $html;
    }

    public static function render_value_with_parents($values = [], $is_export = false, $separator = '')
    {
        global $app_choices_cache;

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        $html = '';
        foreach ($values as $id) {
            if (!isset($app_choices_cache[$id])) {
                continue;
            }

            $name = self::get_parents_names(
                    $app_choices_cache[$id]['parent_id'],
                    $separator
                ) . $app_choices_cache[$id]['name'];

            if (isset($app_choices_cache[$id])) {
                if ($is_export) {
                    $html .= (strlen($html) == 0 ? $name : ', ' . $name);
                } elseif (strlen($app_choices_cache[$id]['bg_color']) > 0) {
                    $html .= render_bg_color_block($app_choices_cache[$id]['bg_color'], $name);
                } else {
                    $html .= '<div>' . $name . '</div>';
                }
            }
        }

        return $html;
    }

    public static function get_paretn_ids($id, $parents = [])
    {
        $choices_query = db_query(
            "select * from app_fields_choices where id = '" . db_input($id) . "' order by sort_order, name"
        );

        while ($v = db_fetch_array($choices_query)) {
            $parents[] = $v['id'];

            if ($v['parent_id'] > 0) {
                $parents = self::get_paretn_ids($v['parent_id'], $parents);
            }
        }

        return $parents;
    }

    static function get_parents_names($parent_id, $separator = '')
    {
        global $app_choices_cache;

        $parents = [];
        foreach (self::get_paretn_ids($parent_id) as $id) {
            $parents[] = $app_choices_cache[$id]['name'];
        }

        return count($parents) ? implode($separator, $parents) . $separator : '';
    }

    static function has_nested($id)
    {
        if (!$id) {
            return false;
        }

        $check_query = db_query("select id from app_fields_choices where parent_id={$id} and is_active=1 limit 1");
        if ($check = db_fetch_array($check_query)) {
            return true;
        } else {
            return false;
        }
    }
}