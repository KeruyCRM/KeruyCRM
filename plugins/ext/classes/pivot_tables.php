<?php

class pivot_tables
{
    function __construct($pivot_table)
    {
        $this->version = '1.3.3';

        $this->pivot_table = $pivot_table;
        $this->id = $this->pivot_table['id'];
        $this->entities_id = $this->pivot_table['entities_id'];
    }

    function render_layout()
    {
        $html = '';
        if (strlen($this->pivot_table['chart_type'])) {
            switch ($this->pivot_table['chart_position']) {
                case 'right':
                    $html = '
                        <div class="row">
                            <div class="col-md-6">
                                <div id="pivot_table_' . $this->id . '"></div>
                            </div>        
                            <div class="col-md-6">
                                <div id="pivot_table_' . $this->id . '_chart" class="pivot_table_chart_side" style="height: ' . $this->get_chart_height(
                        ) . 'px;"></div>
                            </div>        
                        </div>';
                    break;
                case 'left':
                    $html = '
                        <div class="row">
                            <div class="col-md-6">
                                <div id="pivot_table_' . $this->id . '_chart" class="pivot_table_chart_side" style="height: ' . $this->get_chart_height(
                        ) . 'px;"></div>
                            </div>        
                            <div class="col-md-6">
                                <div id="pivot_table_' . $this->id . '"></div>
                            </div>                                    
                        </div>';
                    break;
                case 'top':
                    $html = ''
                        . '<div id="pivot_table_' . $this->id . '_chart" style="height: ' . $this->get_chart_height(
                        ) . 'px;"></div>'
                        . '<div id="pivot_table_' . $this->id . '"></div>';
                    break;
                case 'bottom':
                    $html = ''
                        . '<div id="pivot_table_' . $this->id . '"></div>'
                        . '<div id="pivot_table_' . $this->id . '_chart" style="height: ' . $this->get_chart_height(
                        ) . 'px;"></div>';
                    break;
                case 'only_chart':
                    $html = ''
                        . '<div class="pivot_table_bar" id="pivot_table_bar_' . $this->id . '">
                             <div class="pivot_table_bar_action"></div>
                             <div id="pivot_table_' . $this->id . '"></div>
                           </div>'
                        . '<div id="pivot_table_' . $this->id . '_chart" style="height: ' . $this->get_chart_height(
                        ) . 'px;"></div>';
                    break;
            }
        } else {
            $html = '<div id="pivot_table_' . $this->id . '"></div>';
        }

        return $html . '<p></p>';
    }

    function render_chart()
    {
        if (!strlen($this->pivot_table['chart_type'])) {
            return '';
        }

        $chart_type = $this->pivot_table['chart_type'];

        $html = '';

        if (strlen($colors = $this->get_colors())) {
            $html .= "
                Highcharts.theme = {
                    colors: [" . $colors . "]
                }

                Highcharts.setOptions(Highcharts.theme);    
                ";
        }

        switch ($chart_type) {
            case 'stacked_column':
                $chart_type = 'column';

                $html .= " 
                    Highcharts.setOptions({
                        plotOptions: {
                            column: {
                              stacking: 'normal'
                            }
                          }
                    });    
                ";
                break;
            case 'stacked_percent':
                $chart_type = 'column';

                $html .= " 
                    Highcharts.setOptions({
                        plotOptions: {
                            column: {
                              stacking: 'percent'
                            }
                          }
                    });    
                ";
                break;
            case 'stacked_area':
                $chart_type = 'area';

                $html .= "
                    Highcharts.setOptions({
                        plotOptions: {
                            area: {
                                stacking: 'normal',
                                lineColor: '#666666',
                                lineWidth: 1,
                                marker: {
                                    lineWidth: 1,
                                    lineColor: '#666666'
                                }
                            }
                        }
                    }); 
                    ";
                break;
        }

        $html .= '            
            pivot_table' . $this->id . '.on("reportcomplete", function() {
                pivot_table' . $this->id . '.off("reportcomplete");
                    
                pivot_table' . $this->id . '.highcharts.getData({
                    type: "' . $chart_type . '",                    
                }, function(data) {
                    Highcharts.chart("pivot_table_' . $this->id . '_chart", data);
                }, function(data) {
                    Highcharts.chart("pivot_table_' . $this->id . '_chart", data);
                });                  

            })';

        return $html;
    }

    function get_colors()
    {
        if (!strlen($this->pivot_table['colors'])) {
            return '';
        }

        $colors = [];

        foreach (explode(',', $this->pivot_table['colors']) as $color) {
            if (strlen($color)) {
                $colors[] = substr($color, 0, 7);
            }
        }

        return count($colors) ? "'" . implode("','", $colors) . "'" : '';
    }

    function get_height()
    {
        return ($this->pivot_table['height'] > 0 ? $this->pivot_table['height'] : 600);
    }

    function get_chart_height()
    {
        return ($this->pivot_table['chart_height'] > 0 ? $this->pivot_table['chart_height'] : $this->get_height());
    }

    function has_toolbar()
    {
        global $app_module_path;

        if ($app_module_path != 'ext/pivot_tables/view') {
            return 0;
        } else {
            return 1;
        }
    }

    function get_localization()
    {
        $file = 'js/webdatarocks/' . $this->version . '/languages/' . TEXT_APP_LANGUAGE_SHORT_CODE . '.json';
        if (is_file($file)) {
            return $file;
        } else {
            return 'js/webdatarocks/' . $this->version . '/languages/en.json';
        }
    }

    function getReport()
    {
        global $app_user;

        $use_user_id = (($app_user['group_id'] == 0 or !$this->has_access('full')) ? 0 : $app_user['id']);

        $settings_query = db_query(
            "select * from  app_ext_pivot_tables_settings where length(settings)>0 and reports_id='" . $this->id . "' and users_id='" . $use_user_id . "'"
        );
        if ($settings = db_fetch_array($settings_query)) {
            $report = json_decode($settings['settings'], true);
            $report['dataSource']['filename'] = url_for('ext/pivot_tables/view', 'action=get_csv&id=' . $this->id);

            return json_encode($report);
        } else {
            $report = [
                'dataSource' => [
                    'dataSourceType' => 'csv',
                    'filename' => url_for('ext/pivot_tables/view', 'action=get_csv&id=' . $this->id),
                ]
            ];

            return json_encode($report);
        }
    }

    function hide_actions_in_toolbar()
    {
        $html = '';
        if (!$this->has_access('full')) {
            $html = '
                <style>
                #wdr-toolbar-wrapper #wdr-toolbar li#wdr-tab-format,
                #wdr-toolbar-wrapper #wdr-toolbar li#wdr-tab-options,
                #wdr-toolbar-wrapper #wdr-toolbar li#wdr-tab-fields{
                    display:none;
                }
                </style>
                
                ';
        }

        return $html;
    }

    function has_access($access = false)
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return true;
        }

        if (strlen($this->pivot_table['users_groups'])) {
            $users_groups = json_decode($this->pivot_table['users_groups'], true);

            if (!$access) {
                if (isset($users_groups[$app_user['group_id']])) {
                    return (strlen($users_groups[$app_user['group_id']]) ? true : false);
                }
            } else {
                if (isset($users_groups[$app_user['group_id']])) {
                    return ($users_groups[$app_user['group_id']] == $access ? true : false);
                }
            }
        }

        return false;
    }

    function get_fiters_reports_id()
    {
        if (!strlen($this->pivot_table['filters_panel'])) {
            return default_filters::get_reports_id($this->entities_id, 'default_pivot_tables' . $this->id);
        } else {
            return default_filters::get_reports_id($this->entities_id, 'pivot_table' . $this->id);
        }
    }

    function get_fields_by_entity($entities_id)
    {
        $reports_fields = [];
        $reports_fields_names = [];
        $reports_fields_dates_format = [];
        $pivotreports_fields_query = db_query(
            "select * from app_ext_pivot_tables_fields where reports_id='" . db_input(
                $this->id
            ) . "' and entities_id='" . db_input($entities_id) . "'"
        );
        while ($pivotreports_fields = db_fetch_array($pivotreports_fields_query)) {
            $reports_fields[] = $pivotreports_fields['fields_id'];

            if (strlen($pivotreports_fields['fields_name']) > 0) {
                $reports_fields_names[$pivotreports_fields['fields_id']] = $pivotreports_fields['fields_name'];
            }

            if (strlen($pivotreports_fields['cfg_date_format']) > 0) {
                $reports_fields_dates_format[$pivotreports_fields['fields_id']] = $pivotreports_fields['cfg_date_format'];
            }
        }

        return [
            'reports_fields' => $reports_fields,
            'reports_fields_names' => $reports_fields_names,
            'reports_fields_dates_format' => $reports_fields_dates_format,
        ];
    }

    static function array_to_csv($output)
    {
        return implode(',', $output) . "\n";
    }

    static function css_prepare($output)
    {
        return '"' . str_replace('"', '""', trim(strip_tags($output))) . '"';
    }

    static function prepare_csv_output_for_parent_entities(
        $output_array,
        $parent_entities_listing_fields,
        $parrent_entities,
        $parent_item_id,
        $fields_dates_format
    ) {
        foreach ($parrent_entities as $entities_id) {
            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select($entities_id, '');

            $items_sql_query = "select * {$listing_sql_query_select} from app_entity_" . $entities_id . " e where id ='" . $parent_item_id . "'";
            $items_query = db_query($items_sql_query);
            if ($item = db_fetch_array($items_query)) {
                if (isset($parent_entities_listing_fields[$entities_id])) {
                    foreach ($parent_entities_listing_fields[$entities_id] as $field) {
                        $value = items::prepare_field_value_by_type($field, $item);

                        if (in_array(
                                $field['type'],
                                ['fieldtype_date_added', 'fieldtype_input_date', 'fieldtype_input_datetime']
                            ) and isset($fields_dates_format[$field['id']])) {
                            $output_array[] = pivot_tables::css_prepare(
                                i18n_date($fields_dates_format[$field['id']], $value)
                            );
                        } else {
                            $output_options = [
                                'class' => $field['type'],
                                'value' => $value,
                                'field' => $field,
                                'item' => $item,
                                'is_export' => true,
                                'reports_id' => 0,
                                'path' => '',
                                'path_info' => ''
                            ];

                            $output_array[] = pivot_tables::css_prepare(fields_types::output($output_options));
                        }
                    }
                }

                $parent_item_id = $item['parent_item_id'];
            }
        }

        return $output_array;
    }

}