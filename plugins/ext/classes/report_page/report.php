<?php

namespace report_page;

class report
{
    private $report, $entity_id, $item_id;

    function __construct($report)
    {
        $this->report = $report;

        $this->entity_id = false;
        $this->item_id = false;
        $this->item = [];
    }

    function set_item($entity_id, $item_id)
    {
        $this->entity_id = $entity_id;
        $this->item_id = $item_id;

        $parent_item_id = false;
        $entities = \entities::get_parents($this->entity_id, [$this->entity_id]);

        //print_rr($entities);

        foreach ($entities as $entity_id) {
            $where_sql = $this->entity_id == $entity_id ? "e.id={$this->item_id}" : "e.id={$parent_item_id}";

            $item_query = db_query(
                "select e.*  " . \fieldtype_formula::prepare_query_select(
                    $entity_id
                ) . " from app_entity_" . $entity_id . " e where {$where_sql}",
                false
            );
            if ($item = db_fetch_array($item_query)) {
                $parent_item_id = $item['parent_item_id'];

                if ($this->entity_id != $entity_id) {
                    foreach ($item as $k => $v) {
                        if (!strstr($k, 'field_')) {
                            unset($item[$k]);
                        }
                    }
                }

                $this->item = array_merge($this->item, $item);
            }
        }
        //print_rr($this->item);
    }

    function get_html()
    {
        $html = $this->report['description'];

        $block_query = db_query("select * from app_ext_report_page_blocks where report_id={$this->report['id']}");
        while ($block = db_fetch_array($block_query)) {
            $block_html = new blocks_html($block, $this->report);

            if ($this->entity_id) {
                $block_html->set_item($this->item);
            }

            $html = str_replace('${' . $block['id'] . '}', $block_html->render(), $html);
        }

        return $html;
    }

    static function get_buttons_by_position($entities_id, $item_id, $position, $url_params = '')
    {
        global $app_user, $app_path;


        //temp 3.1
        switch ($position) {
            case 'default':
            case 'menu_with_selected_dashboard':
            case 'menu_print':
                return '';
                break;
            case 'menu_more_actions':
            case 'menu_with_selected':
                return [];
                break;
        }
        //temp 3.1


        $reports_list = [];

        $html = '';

        $reports_query = db_query(
            "select ep.* from app_ext_report_page ep, app_entities e where ep.is_active=1 and e.id=ep.entities_id and find_in_set('" . str_replace(
                '_dashboard',
                '',
                $position
            ) . "',ep.button_position) and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) order by ep.sort_order, ep.name",
            false
        );
        while ($reports = db_fetch_array($reports_query)) {
            if (!in_array($position, ['menu_with_selected', 'menu_with_selected_dashboard'])) {
                $items_filters = new \items_filters($entities_id, $item_id);
                if (!$items_filters->check(['report_type' => 'report_page' . $reports['id']])) {
                    continue;
                }
            }

            $button_title = (strlen($reports['button_title']) ? $reports['button_title'] : $reports['name']);
            $button_icon = (strlen($reports['button_icon']) ? $reports['button_icon'] : 'fa-print');

            $style = (strlen($reports['button_color']) ? 'color: ' . $reports['button_color'] : '');

            switch ($position) {
                case 'default':
                    $html .= '<li>' . button_tag(
                            $button_title,
                            url_for('items/report_page', 'path=' . $app_path . '&report_id=' . $reports['id']),
                            true,
                            ['class' => 'btn btn-primary btn-sm btn-report-page-' . $reports['id']],
                            $button_icon
                        ) . '</li>';
                    $html .= app_button_color_css($reports['button_color'], 'btn-report-page-' . $reports['id']);
                    break;
                case 'menu_more_actions':
                    $reports_list[] = [
                        'id' => $reports['id'],
                        'name' => $button_title,
                        'entities_id' => $reports['entities_id'],
                        'button_icon' => $button_icon
                    ];
                    break;
                case 'menu_with_selected':
                    $reports_list[] = [
                        'id' => $reports['id'],
                        'name' => $button_title,
                        'entities_id' => $reports['entities_id'],
                        'button_icon' => $button_icon
                    ];
                    break;
                case 'menu_print':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for('items/report_page', 'path=' . $app_path . '&report_id=' . $reports['id']),
                            ['style' => $style]
                        ) . '</li>';
                    break;
                case 'menu_with_selected_dashboard':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for('items/print_template', 'templates_id=' . $reports['id'] . $url_params),
                            ['style' => $style]
                        ) . '</li>';
                    break;
            }
        }


        switch ($position) {
            case 'default':
            case 'menu_with_selected_dashboard':
            case 'menu_print':
                return $html;
                break;
            case 'menu_more_actions':
            case 'menu_with_selected':
                return $reports_list;
                break;
        }
    }
}
