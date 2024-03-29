<?php

namespace Models\Main;

class Listing_types
{

    static function get_types()
    {
        return ['table', 'tree_table', 'list', 'grid', 'mobile'];
    }

    static function get_type_title($type)
    {
        switch ($type) {
            case 'table':
                $title = \K::$fw->TEXT_TABLE;
                break;
            case 'tree_table':
                $title = \K::$fw->TEXT_TREE_TABLE;
                break;
            case 'list':
                $title = \K::$fw->TEXT_LIST;
                break;
            case 'grid':
                $title = \K::$fw->TEXT_GRID;
                break;
            case 'mobile':
                $title = \K::$fw->TEXT_MOBILE;
                break;
        }

        return $title;
    }

    //autocreate listing types if not exist	
    static function prepare_types($entities_id)
    {
        $forceCommit = \K::model()->forceCommit();

        foreach (self::get_types() as $type) {
            /*$check_query = db_query(
                "select * from app_listing_types where entities_id='" . $entities_id . "' and type='" . $type . "'"
            );*/

            $check = \K::model()->db_fetch_one('app_listing_types', [
                'entities_id = ? and type = ?',
                $entities_id,
                $type
            ], [], 'id');

            if (!$check) {
                $sql_data = [
                    'entities_id' => $entities_id,
                    'type' => $type,
                    'is_default' => $type == 'table' ? 1 : 0,
                    'is_active' => $type == 'table' ? 1 : 0,
                ];

                \K::model()->db_perform('app_listing_types', $sql_data);
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    static function get_default($entities_id)
    {
        /*$info_query = db_query(
            "select * from app_listing_types where entities_id='" . $entities_id . "' and is_default=1 and is_active=1"
        );
        if ($info = db_fetch_array($info_query)) {*/
        $info = \K::model()->db_fetch_one('app_listing_types', [
            'entities_id = ? and is_default = 1 and is_active = 1',
            $entities_id
        ], [], 'type');

        if ($info) {
            return $info['type'];
        } else {
            return 'table';
        }
    }

    static function has_mobile($entities_id)
    {
        /*$check_query = db_query(
            "select id from app_listing_types where is_active=1 and type='mobile' and entities_id='" . $entities_id . "'"
        );*/
        $check_query = \K::model()->db_fetch_one('app_listing_types', [
            'is_active = 1 and type = ? and entities_id = ?',
            'mobile',
            $entities_id
        ], [], 'id');
        //if ($check = db_fetch_array($check_query)) {

        if ($check_query) {
            return true;
        } else {
            return false;
        }
    }

    static function has_tree_table($entities_id)
    {
        /*$check_query = db_query(
            "select id from app_listing_types where is_active=1 and type='tree_table' and entities_id='" . $entities_id . "'"
        );*/

        $check = \K::model()->db_fetch_one('app_listing_types', [
            'is_active = 1 and type = ? and entities_id = ?',
            'tree_table',
            $entities_id
        ], [], 'id');

        if ($check) {
            return true;
        } else {
            return false;
        }
    }

    static function get_sections_next_order($listing_types_id)
    {
        /*$info_query = db_query(
            "select max(sort_order) as max_sort_order from app_listing_sections where listing_types_id={$listing_types_id}"
        );
        $info = db_fetch_array($info_query);*/

        $info = \K::model()->db_fetch_one('app_listing_sections', [
            'listing_types_id = ?',
            $listing_types_id
        ], [], null, ['max_sort_order' => 'max(sort_order)'], 0);

        return $info['max_sort_order'] + 1;
    }

    static function get_sections_align_choices()
    {
        return [
            'left' => \K::$fw->TEXT_ALIGN_LEFT,
            'center' => \K::$fw->TEXT_ALIGN_CENTER,
            'right' => \K::$fw->TEXT_ALIGN_RIGHT,
        ];
    }

    static function get_sections_display_choices()
    {
        return [
            'list' => \K::$fw->TEXT_TABLE,
            'inline' => \K::$fw->TEXT_INLINE_LIST,
        ];
    }

    static function get_sections_align_icon($align)
    {
        switch ($align) {
            case 'left':
                $icon = '<i class="fa fa-align-left"></i>';
                break;
            case 'right':
                $icon = '<i class="fa fa-align-right"></i>';
                break;
            case 'center':
                $icon = '<i class="fa fa-align-center"></i>';
                break;
        }

        return $icon;
    }

    static function render_switches($reports_info, $listing_type)
    {
        $choices = [];
        $info_query = db_query(
            "select type from app_listing_types where is_active=1 and type!='mobile' and entities_id='" . $reports_info['entities_id'] . "'"
        );
        while ($info = db_fetch_array($info_query)) {
            $choices[] = $info['type'];
        }

        $html = '';

        if (count($choices) > 1) {
            $html .= '<ul class="list-inline listing-types-switches">';

            foreach ($choices as $type) {
                $icon = '';
                switch ($type) {
                    case 'table':
                        $icon = '<i class="fa fa-table"></i>';
                        break;
                    case 'tree_table':
                        $icon = '<i class="fa fa-sitemap"></i>';
                        break;
                    case 'list':
                        $icon = '<i class="fa fa-list"></i>';
                        break;
                    case 'grid':
                        $icon = '<i class="fa fa-th"></i>';
                        break;
                }

                if (isset($_GET['path'])) {
                    $url = url_for(
                        'items/items',
                        'path=' . $_GET['path'] . '&action=set_listing_type&type=' . $type . '&reports_id=' . $reports_info['id']
                    );
                } else {
                    $url = url_for(
                        'reports/view',
                        'action=set_listing_type&type=' . $type . '&reports_id=' . $reports_info['id']
                    );
                }

                $html .= '<li><a href="' . $url . '" class="btn btn-xs btn-default ' . ($listing_type == $type ? 'active' : '') . '">' . $icon . '</a></li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }

    static function has_action_field($type, $entities_id)
    {
        $info_query = db_query(
            "select id from app_listing_types where is_active=1 and type='" . $type . "' and entities_id='" . $entities_id . "'"
        );
        if ($info = db_fetch_array($info_query)) {
            $fields = [];
            $listing_sections_query = db_query(
                "select fields from app_listing_sections where listing_types_id={$info['id']} order by sort_order, name"
            );
            while ($listing_sections = db_fetch_array($listing_sections_query)) {
                if (strlen($listing_sections['fields'])) {
                    $fields = array_merge($fields, explode(',', $listing_sections['fields']));
                }
            }

            if (count($fields)) {
                $check_query = db_query(
                    "select id from app_fields where id in (" . implode(',', $fields) . ") and type='fieldtype_action'"
                );
                if ($check = db_fetch_array($check_query)) {
                    return true;
                }
            }
        }

        return false;
    }

    static function get_choices($entities_id)
    {
        $choices = ['' => TEXT_DEFAULT];
        $info_query = db_query(
            "select type from app_listing_types where is_active=1 and type!='mobile' and entities_id='" . $entities_id . "'"
        );
        while ($info = db_fetch_array($info_query)) {
            $choices[$info['type']] = self::get_type_title($info['type']);
        }

        return $choices;
    }

}
