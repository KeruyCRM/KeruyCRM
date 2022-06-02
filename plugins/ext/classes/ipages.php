<?php

class ipages
{
    static function get_name_by_id($id)
    {
        $page_query = db_query("select * from app_ext_ipages where id='{$id}'");

        if ($page = db_fetch_array($page_query)) {
            return (strlen($page['short_name']) ? $page['short_name'] : $page['name']);
        } else {
            return '';
        }
    }

    static function get_choices($parent_id = 0, $tree = [], $level = 0)
    {
        $ipages_query = db_query(
            "select * from app_ext_ipages where parent_id=" . $parent_id . " order by sort_order, name"
        );

        while ($pages = db_fetch_array($ipages_query)) {
            $tree[$pages['id']] = str_repeat(' - ', $level) . (strlen(
                    $pages['short_name']
                ) > 0 ? $pages['short_name'] : $pages['name']);

            $tree = self::get_choices($pages['id'], $tree, $level + 1);
        }

        return $tree;
    }

    static function get_pages()
    {
        global $app_user;

        $pages_list = [];

        $where_sql = '';

        if ($app_user['group_id'] > 0) {
            $where_sql = " where find_in_set(" . $app_user['group_id'] . ",users_groups)";
        }

        $ipages_query = db_query("select * from app_ext_ipages {$where_sql} order by sort_order, name");
        while ($ipages = db_fetch_array($ipages_query)) {
            $pages_list[] = [
                'id' => $ipages['id'],
                'name' => (strlen($ipages['short_name']) > 0 ? $ipages['short_name'] : $ipages['name']),
                'menu_icon' => $ipages['menu_icon']
            ];
        }

        return $pages_list;
    }

    static function get_menu_choices()
    {
        $choices = [];
        $choices[''] = '';

        foreach (self::get_menu_tree() as $menu) {
            $choices[$menu['id']] = str_repeat(' - ', $menu['level']) . $menu['name'];
        }

        return $choices;
    }

    static function get_menu_tree($parent_id = 0, $tree = [], $level = 0)
    {
        $ipages_query = db_query(
            "select * from app_ext_ipages where parent_id=" . $parent_id . " and is_menu=1 order by sort_order, name"
        );

        while ($pages = db_fetch_array($ipages_query)) {
            $pages['level'] = $level;

            $tree[] = $pages;

            $tree = self::get_menu_tree($pages['id'], $tree, $level + 1);
        }

        return $tree;
    }

    static function get_tree($parent_id = 0, $tree = [], $level = 0)
    {
        $ipages_query = db_query(
            "select * from app_ext_ipages where parent_id=" . $parent_id . " order by sort_order, name"
        );

        while ($pages = db_fetch_array($ipages_query)) {
            $pages['level'] = $level;

            $tree[] = $pages;

            $tree = self::get_tree($pages['id'], $tree, $level + 1);
        }

        return $tree;
    }

    static function prepare_attachments_in_text($text, $attachments)
    {
        if (strlen($attachments)) {
            $fancybox_css_class = '';

            foreach (explode(',', $attachments) as $filename) {
                $file = attachments::parse_filename($filename);

                if ($file['is_image']) {
                    if (strlen($fancybox_css_class) == 0) {
                        $fancybox_css_class = 'fancybox' . time();
                    }

                    $link = link_to(
                        $file['name'],
                        url_for(
                            'ext/ipages/view',
                            'id=' . _GET('id') . '&action=preview_attachment_image&file=' . urlencode(
                                base64_encode($filename)
                            )
                        ),
                        ['class' => $fancybox_css_class, 'title' => $file['name'], 'data-fancybox-group' => 'gallery']
                    );
                } elseif ($file['is_pdf']) {
                    $link = link_to(
                        $file['name'],
                        url_for(
                            'ext/ipages/view',
                            'id=' . _GET('id') . '&action=download_attachment&preview=1&file=' . urlencode(
                                base64_encode($filename)
                            )
                        ),
                        ['target' => '_blank']
                    );
                } else {
                    $link = link_to(
                        $file['name'],
                        url_for(
                            'ext/ipages/view',
                            'id=' . _GET('id') . '&action=download_attachment&file=' . urlencode(
                                base64_encode($filename)
                            )
                        )
                    );
                }

                $text = str_replace($file['name'], $link, $text);
            }

            return $text;
        } else {
            return $text;
        }
    }

    static function build_menu($parent_id = 0, $menu = [], $level = 0)
    {
        global $app_user;

        $ipages_query = db_query(
            "select * from app_ext_ipages p where p.parent_id=" . $parent_id . " and (find_in_set(" . $app_user['group_id'] . ",p.users_groups) or find_in_set(" . $app_user['id'] . ",p.assigned_to)) 
                                    and (select count(*) from app_entities_menu e where find_in_set(p.id,e.pages_list))=0 
                                    order by p.sort_order, p.name"
        );

        while ($pages = db_fetch_array($ipages_query)) {
            if ($level > 0 and $pages['is_menu'] == 1) {
                $pages['menu_icon'] = '';
            }

            $page = [
                'title' => (strlen($pages['short_name']) ? $pages['short_name'] : $pages['name']),
                'url' => url_for('ext/ipages/view', 'id=' . $pages['id']),
                'class' => $pages['menu_icon'],
                'icon_color' => $pages['icon_color'],
                'bg_color' => $pages['bg_color'],
                'submenu' => self::build_menu($pages['id'], [], $level + 1)
            ];

            if (!count($page['submenu'])) {
                unset($page['submenu']);
            }

            $menu[] = $page;
        }

        return $menu;
    }

}
