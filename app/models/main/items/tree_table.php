<?php

namespace Models\Main\Items;

class Tree_table
{
    public $listing_fields;
    public $entities_id;
    public $fields_access_schema;
    public $redirect_to;
    public $is_info_page;
    public $reset_parent;
    public $users_notifications;
    public $reports_id;
    public $current_page_number;
    public $listing;
    public $listing_highlight;
    public $entity_cfg;

    public function __construct($entities_id, $listing_fields, $fields_access_schema)
    {
        $this->listing_fields = $listing_fields;
        $this->entities_id = $entities_id;
        $this->fields_access_schema = $fields_access_schema;
        $this->redirect_to = '';
        $this->is_info_page = false;
        $this->reset_parent = false;
    }

    public function render_nested($parent_id, $html = '')
    {
        global $has_with_selected, $app_selected_items, $current_path, $current_entity_info, $reports_entities_id;

        if ($this->is_info_page and !strlen($html)) {
            $where_sql = "e.id={$parent_id}";
            $this->reset_parent = true;
        } else {
            $where_sql = "e.parent_id={$parent_id}";
            $this->reset_parent = false;
        }

        $items_query = db_query(
            "select e.* " . fieldtype_formula::prepare_query_select(
                $this->entities_id
            ) . " from app_entity_{$this->entities_id} e where {$where_sql} order by e.sort_order, e.id"
        );
        while ($item = db_fetch_array($items_query)) {
            $html .= '<tr class="' . (($this->users_notifications->has($item['id']) and $this->entity_cfg->get(
                        'disable_highlight_unread'
                    ) != 1) ? 'unread-item-row' : '') . $this->listing_highlight->apply($item) . '">';

            //perpare selected checkbox
            $hide_actions_buttons = false;

            if ($has_with_selected) {
                $checkbox_html = '<td>' . input_checkbox_tag(
                        'items_' . $item['id'],
                        $item['id'],
                        [
                            'class' => 'items_checkbox',
                            'checked' => in_array($item['id'], $app_selected_items[$this->reports_id])
                        ]
                    ) . '</td>';

                //check access to action with assigned only
                if (users::has_users_access_name_to_entity('action_with_assigned', $this->entities_id)) {
                    if (users::has_access_to_assigned_item($this->entities_id, $item['id'])) {
                        $html .= $checkbox_html;
                    } else {
                        $html .= '<td></td>';

                        $hide_actions_buttons = true;
                    }
                } else {
                    $html .= $checkbox_html;
                }
            }
            //end prepare selected checkbox

            $path_info_in_report = [];

            if ($reports_entities_id > 0 and $current_entity_info['parent_id'] > 0) {
                $path_info_in_report = items::get_path_info($_POST['reports_entities_id'], $item['id'], $item);
            }

            foreach ($this->listing_fields as $field) {
                //check field access
                if (isset($this->fields_access_schema[$field['id']])) {
                    if ($this->fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                if ($field['type'] == 'fieldtype_parent_item_id' and (strlen(
                            $this->redirect_to
                        ) == 0 or $current_entity_info['parent_id'] == 0 or $this->listing->report_type == 'parent_item_info_page')) {
                    continue;
                }

                //configure editable listing
                $editable_listing = new editable_listing(
                    $this->entities_id,
                    $item,
                    $field,
                    $this->fields_access_schema,
                    $this->reports_id,
                    $this->current_page_number,
                    $this->listing->listing_type
                );

                //prepare field value
                $value = items::prepare_field_value_by_type($field, $item);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $value,
                    'field' => $field,
                    'item' => $item,
                    'is_listing' => true,
                    'redirect_to' => $this->redirect_to,
                    'reports_id' => $this->reports_id,
                    'path' => (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_path),
                    'path_info' => $path_info_in_report,
                    'hide_actions_buttons' => $hide_actions_buttons,
                    'listing_type' => $this->listing->get_listing_type(),
                ];

                if ($field['is_heading'] == 1) {
                    $path = $this->entities_id . '-' . $item['id'];

                    $data_sort_url = (users::has_access('create') ? ' data-tt-sort-url="' . url_for(
                            'items/sort_nested',
                            'path=' . $path . '&redirect_to=' . $this->redirect_to . (isset($_POST['page']) ? '&gotopage[' . $this->reports_id . ']=' . _POST(
                                    'page'
                                ) : '')
                        ) . '" ' : '');

                    $html .= '
                        <td class="' . $field['type'] . $editable_listing->td_css_class(
                        ) . '  field-' . $field['id'] . '-td item_heading_td' . (($this->listing->settings->get(
                                'heading_width_based_content'
                            ) == 1 or $this->listing->settings->get(
                                'change_col_width_in_listing'
                            ) == 1) ? ' width-auto' : '') . '" ' . $editable_listing->td_params() . '>
                            <div class="tt" ' . $data_sort_url . '  data-tt-id="item_' . $this->reports_id . '_' . $item['id'] . '" ' . (($item['parent_id'] > 0 and !$this->reset_parent) ? 'data-tt-parent="item_' . $this->reports_id . '_' . $item['parent_id'] . '"' : '') . '></div>
                            <a  class="item_heading_link" href="' . url_for(
                            'items/info',
                            'path=' . $path . '&redirect_to=subentity' . (isset($_POST['page']) ? '&gotopage[' . $this->reports_id . ']=' . _POST(
                                    'page'
                                ) : '')
                        ) . '">' . fields_types::output($output_options) . '</a>
                        ';

                    if ($this->entity_cfg->get(
                            'use_comments'
                        ) == 1 and $this->user_has_comments_access and $this->entity_cfg->get(
                            'display_last_comment_in_listing',
                            1
                        )) {
                        $html .= comments::get_last_comment_info(
                            $this->entities_id,
                            $item['id'],
                            $path,
                            $this->fields_access_schema
                        );
                    }

                    $html .= '</td>';
                } elseif ($field['type'] == 'fieldtype_dropdown_multilevel') {
                    $html .= fieldtype_dropdown_multilevel::output_listing($output_options);
                } else {
                    $td_class = (in_array(
                        $field['type'],
                        ['fieldtype_action', 'fieldtype_date_added', 'fieldtype_input_datetime']
                    ) ? $field['type'] . ' field-' . $field['id'] . '-td nowrap' : $field['type'] . ' field-' . $field['id'] . '-td');
                    $td_class .= $editable_listing->td_css_class();
                    $html .= '
                                <td class="' . $td_class . '"  ' . $editable_listing->td_params(
                        ) . '>' . fields_types::output($output_options) . '</td>
                            ';
                }
            }

            $html .= '</tr>';

            $html = $this->render_nested($item['id'], $html);
        }

        return $html;
    }

    public static function render_nested_items($entities_id, $item_id, $position)
    {
        global $app_user;

        //check if there is active tree_table listing type
        $listing_types_query = db_query(
            "select settings from app_listing_types where  type='tree_table' and entities_id='" . $entities_id . "' and is_active=1"
        );
        if (!$listing_types = db_fetch_array($listing_types_query)) {
            return '';
        }

        $settings = new \Tools\Settings($listing_types['settings']);

        //check position
        if ($settings->get('display_nested_records') != $position) {
            return '';
        }

        //check if has nested items
        $check_query = db_query("select id from app_entity_{$entities_id} where parent_id={$item_id} limit 1");
        if (!$check = db_fetch_array($check_query)) {
            return '';
        }

        $listing_fields = [];
        $fields_in_listing = '';
        $fields_access_schema = users::get_fields_access_schema($entities_id, $app_user['group_id']);
        $users_notifications = new users_notifications($entities_id);
        $entity_cfg = new entities_cfg($entities_id);

        $listing = new class {
            function __construct()
            {
                $this->settings = new \Tools\Settings('');
            }

            function get_listing_type()
            {
                return 'tree_table';
            }
        };

        //listing highlight rules
        $listing_highlight = new listing_highlight($entities_id);

        if (is_array($settings->get('fields_in_listing'))) {
            $fields_in_listing = implode(',', $settings->get('fields_in_listing'));
        }

        if (is_array($settings->get('fields_in_listing_info'))) {
            $fields_in_listing = implode(',', $settings->get('fields_in_listing_info'));
        }

        if (!strlen($fields_in_listing)) {
            return '';
        }

        $html = $listing_highlight->render_css() . '
        <div class="table-scrollable">
            <table class="tree-table table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                ';

        $sql = "select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name, f.name as long_name  from app_fields f where f.id in (" . $fields_in_listing . ") and  f.entities_id='" . db_input(
                $entities_id
            ) . "' order by field(f.id," . $fields_in_listing . ")";
        $fields_query = db_query($sql);
        while ($v = db_fetch_array($fields_query)) {
            //check field access
            if (isset($fields_access_schema[$v['id']]) and $fields_access_schema[$v['id']] == 'hide') {
                continue;
            }

            if ($v['type'] == 'fieldtype_dropdown_multilevel') {
                $html .= fieldtype_dropdown_multilevel::output_listing_heading($v);
            } else {
                $html .= '
                      <th  data-field-id="' . $v['id'] . '">
                            <div ' . (strlen($v['short_name']) ? 'title="' . htmlspecialchars(
                            $v['long_name']
                        ) . '"' : '') . '>' . fields_types::get_option($v['type'], 'name', $v['name']) . '</div>
                      </th>
                  ';
            }

            $listing_fields[] = $v;
        }

        $html .= '
                    </tr>
                </thead>
                <tbody>
            ';

        $tree_table = new tree_table($entities_id, $listing_fields, $fields_access_schema);
        $tree_table->users_notifications = $users_notifications;
        $tree_table->entity_cfg = $entity_cfg;
        $tree_table->listing_highlight = $listing_highlight;
        $tree_table->listing = $listing;
        $tree_table->reports_id = 0;
        $tree_table->is_info_page = true;
        $tree_table->redirect_to = 'items_info';
        $html .= $tree_table->render_nested($item_id);

        $html .= '
                </tbdoy>
            </table>
        </div>
        
        ';

        return $html;
    }

    public static function get_parents($entities_id, $item_id, $parents = [])
    {
        $item_info = db_query("select id, parent_id from app_entity_{$entities_id} where id = {$item_id}");
        if ($item = db_fetch_array($item_info)) {
            $parents[] = $item['id'];

            if ($item['parent_id'] > 0) {
                $parents = self::get_parents($entities_id, $item['parent_id'], $parents);
            }
        }

        return $parents;
    }

    public static function get_top_parent_item_id($entities_id, $item_id)
    {
        //$item_info = db_query("select id, parent_id from app_entity_{$entities_id} where id = {$item_id}");
        $item = \K::model()->db_fetch_one('app_entity_' . $entities_id, [
            'id = ?',
            $item_id
        ], [], 'id,parent_id');

        if ($item) {
            $item_id = $item['id'];

            if ($item['parent_id'] > 0) {
                $item_id = self::get_top_parent_item_id($entities_id, $item['parent_id']);
            }
        }

        return $item_id;
    }

    public static function get_items_tree($entities_id, $item_id, $tree)
    {
        //$items_query = db_query("select id from app_entity_{$entities_id} where parent_id={$item_id}");

        $items_query = \K::model()->db_fetch('app_entity_' . $entities_id, [
            'parent_id = ?',
            $item_id
        ], [], 'id');

        //while ($items = db_fetch_array($items_query)) {
        foreach ($items_query as $items){
            $items = $items->cast();

            $tree[] = $items['id'];

            $tree = self::get_items_tree($entities_id, $items['id'], $tree);
        }

        return $tree;
    }

    public static function get_html_tree($entities_id, $item_id, $tree = '')
    {
        $count_query = db_query(
            "select count(*) as total from app_entity_{$entities_id} where parent_id = '" . db_input(
                $item_id
            ) . "' order by sort_order, id"
        );
        $count = db_fetch_array($count_query);

        if ($count['total'] > 0) {
            $tree .= '<ol class="dd-list">';

            $items_query = db_query(
                "select * from app_entity_{$entities_id}  where parent_id = '" . db_input(
                    $item_id
                ) . "' order by sort_order, id"
            );

            while ($item = db_fetch_array($items_query)) {
                $tree .= '<li class="dd-item" data-id="' . $item['id'] . '"><div class="dd-handle">' . items::get_heading_field(
                        $entities_id,
                        $item['id'],
                        $item
                    ) . '</div>';

                $tree = self::get_html_tree($entities_id, $item['id'], $tree);

                $tree .= '</li>';
            }

            $tree .= '</ol>';
        }

        return $tree;
    }

    public static function get_nested_list($entities_id, $item_id, $tree = [])
    {
        $items_query = db_query(
            "select * from app_entity_{$entities_id}  where parent_id = '" . db_input(
                $item_id
            ) . "' order by sort_order, id"
        );
        while ($item = db_fetch_array($items_query)) {
            $tree[] = [
                'parent_id' => $item_id,
                'id' => $item['id']
            ];

            $tree = self::get_nested_list($entities_id, $item['id'], $tree);
        }

        return $tree;
    }

    public static function sort_tree($entities_id, $item_id, $tree)
    {
        $sort_order = 0;
        foreach ($tree as $v) {
            db_query(
                "update app_entity_{$entities_id}  set parent_id='" . $item_id . "', sort_order='" . $sort_order . "' where id='" . db_input(
                    $v['id']
                ) . "'"
            );

            if (isset($v['children'])) {
                self::sort_tree($entities_id, $v['id'], $v['children']);
            }

            $sort_order++;
        }
    }
}