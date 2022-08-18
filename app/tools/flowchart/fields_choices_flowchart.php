<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\Flowchart;

class Fields_choices_flowchart
{
    public $nodes;
    public $edges;
    public $height;
    public $height_step;
    public $y;
    public $y_step;

    function __construct()
    {
        $this->y = 20;
        $this->y_step = 70;

        $this->height = 0;
        $this->height_step = 150;

        $this->nodes = [];
        $this->edges = [];
    }

    function prepare_data($fields_id)
    {
        $tree = \Models\Main\Fields_choices::get_tree($fields_id);

        $previous_id = 0;

        foreach ($tree as $v) {
            $filters_title = '';
            //$reports_type = 'fields_choices' . (int)$v['id'];
            /*$reports_info_query = db_query(
                "select * from app_reports where entities_id='" . db_input(
                     \K::$fw->GET['entities_id']
                ) . "' and reports_type='{$reports_type}'"
            );*/

            $reports_info = \K::model()->db_fetch_one('app_reports', [
                'entities_id = ? and reports_type = ?',
                \K::$fw->GET['entities_id'],
                'fields_choices' . (int)$v['id']
            ], [], 'id');

            if ($reports_info) {
                $filters_query = \K::model()->db_query_exec(
                    "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id = f.id where rf.reports_id = ? order by rf.id",
                    $reports_info['id'],
                    'app_reports_filters,app_fields'
                );

                foreach ($filters_query as $filters) {
                    $filters_title .= \Models\Main\Fields_types::get_option(
                            $filters['type'],
                            'name',
                            $filters['name']
                        ) . ": " . \Models\Main\Reports\Reports::get_condition_name_by_key(
                            $filters['filters_condition']
                        ) . ' ' . \Models\Main\Reports\Reports::render_filters_values(
                            $filters['fields_id'],
                            $filters['filters_values'],
                            ', ',
                            $filters['filters_condition']
                        ) . '<br>';
                }
            }

            $id = $v['id'];

            //handle nodes
            $this->nodes[] = "{ data: { id: 'choice_filter_{$id}',name: '" . addslashes(
                    $filters_title
                ) . "'}, classes:'choice_filter', position: { x: 0, y: {$this->y} }}";
            $this->nodes[] = "{ data: { id: 'choice_{$id}',name: '" . addslashes(
                    $v['name']
                ) . "'}, classes:'choice', position: { x: 90, y: {$this->y} }}";

            $this->y += $this->y_step;

            $this->height += $this->height_step;

            //hande edges
            $this->edges[] = "{ data: { id: 'edge_{$id}_{$id}', source: 'choice_filter_{$id}', target: 'choice_{$id}',label: '" . addslashes(
                    \K::$fw->TEXT_YES
                ) . "'} }";

            if ($previous_id > 0) {
                $this->edges[] = "{ data: { id: 'edge_{$previous_id}_{$id}', source: 'choice_filter_{$previous_id}', target: 'choice_filter_{$id}',label: '" . addslashes(
                        \K::$fw->TEXT_NO
                    ) . "'} }";
            }

            $previous_id = $id;
        }
    }
}