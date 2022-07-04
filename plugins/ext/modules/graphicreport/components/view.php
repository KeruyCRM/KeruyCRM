<?php

$graph = new graphicreport($reports);

//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $reports['entities_id']
    ) . "' and reports_type='graphicreport" . $reports['id'] . "' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $reports['entities_id'],
        'reports_type' => 'graphicreport' . $reports['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();
} else {
    $fiters_reports_id = $reports_info['id'];
}

//prepare yaxis colors
$yaxis = [];
$yaxis_color = [];
foreach (explode(',', $reports['yaxis']) as $value) {
    $value = explode(':', $value);
    $yaxis[] = $value[0];
    $yaxis_color[$value[0]] = $value[1] ?? '';
}

//reset colors from yaxis value
$reports['yaxis'] = implode(',', $yaxis);
//print_rr($yaxis_color);

//get report entity info
$entity_info = db_find('app_entities', $reports_info['entities_id']);

//check if parent reports was not set
if ($entity_info['parent_id'] > 0 and $reports_info['parent_id'] == 0) {
    reports::auto_create_parent_reports($reports_info['id']);
}

if (!app_session_is_registered('graphicreport_filters')) {
    $graphicreport_filters = [];
    app_session_register('graphicreport_filters');
}


//set chart type
if (isset($_GET['chart_type'])) {
    $graphicreport_filters[$reports['id']]['chart_type'] = $_GET['chart_type'];
} elseif (!isset($graphicreport_filters[$reports['id']]['chart_type'])) {
    $graphicreport_filters[$reports['id']]['chart_type'] = $reports["chart_type"];
}

//set chart period
if (isset($_GET['period'])) {
    $graphicreport_filters[$reports['id']]['period'] = $_GET['period'];
} elseif (!isset($graphicreport_filters[$reports['id']]['period'])) {
    $graphicreport_filters[$reports['id']]['period'] = $reports["period"];
}

//set filter by year
if (isset($_GET['year_filter'])) {
    $graphicreport_filters[$reports['id']]['year_filter'] = $_GET['year_filter'];
} elseif (!isset($graphicreport_filters[$reports['id']]['year_filter'])) {
    $graphicreport_filters[$reports['id']]['year_filter'] = date('Y');
}

//set filter by month
if (isset($_GET['month_filter'])) {
    $graphicreport_filters[$reports['id']]['month_filter'] = $_GET['month_filter'];
} elseif (!isset($graphicreport_filters[$reports['id']]['month_filter'])) {
    $graphicreport_filters[$reports['id']]['month_filter'] = date('n');
}

//set filter by day
if (isset($_GET['day_filter'])) {
    $graphicreport_filters[$reports['id']]['day_filter'] = $_GET['day_filter'];
} elseif (!isset($graphicreport_filters[$reports['id']]['day_filter'])) {
    $graphicreport_filters[$reports['id']]['day_filter'] = date('j');
}

$chart_type = $graphicreport_filters[$reports['id']]['chart_type'];
$period = $graphicreport_filters[$reports['id']]['period'];
$year_filter = $graphicreport_filters[$reports['id']]['year_filter'];
$month_filter = $graphicreport_filters[$reports['id']]['month_filter'];
$day_filter = $graphicreport_filters[$reports['id']]['day_filter'];

//generate year filter list
$xaxis_field_info = db_find('app_fields', $reports['xaxis']);
if ($xaxis_field_info['type'] == 'fieldtype_date_added') {
    $xaxis_sql_name = "e.date_added";
} elseif ($xaxis_field_info['type'] == 'fieldtype_dynamic_date') {
    $xaxis_sql_name = fieldtype_dynamic_date::prepare_select_sql($xaxis_field_info);
    //echo $xaxis_sql_name;
} else {
    $xaxis_sql_name = "e.field_" . $reports['xaxis'];
}

$listing_sql = "select max(" . $xaxis_sql_name . ") as max_date, min(" . $xaxis_sql_name . ") as min_date from app_entity_" . $reports['entities_id'] . " e  where " . $xaxis_sql_name . ">0 limit 1";

$items_query = db_query($listing_sql);
if ($items = db_fetch_array($items_query)) {
    $years_list = [];
    for ($i = date('Y', $items['min_date']); $i <= date('Y', $items['max_date']); $i++) {
        $year = ($i == $year_filter ? '<b>' . $i . '</b>' : $i);
        $years_list[] = '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&year_filter=' . $i . '&month_filter=' . $month_filter . (isset($_GET['path']) ? '&path=' . $app_path : '')
            ) . '">' . $year . '</a>';
    }
} else {
    $years_list = [date('Y') => date('Y')];
}

//generate month filter list
$months_list = [];
$months_array = explode(',', str_replace('"', '', TEXT_DATEPICKER_MONTHS));
foreach ($months_array as $k => $v) {
    $v = ($month_filter == ($k + 1) ? '<b>' . $v . '</b>' : $v);

    $months_list[] = '<a href="' . url_for(
            'ext/graphicreport/view',
            'id=' . $reports['id'] . '&year_filter=' . $year_filter . '&month_filter=' . ($k + 1) . (isset($_GET['path']) ? '&path=' . $app_path : '')
        ) . '">' . $v . '</a>';
}

$days_list = [];
for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month_filter, $year_filter); $i++) {
    $days_list[] = '<a href="' . url_for(
            'ext/graphicreport/view',
            'id=' . $reports['id'] . '&year_filter=' . $year_filter . '&month_filter=' . $month_filter . '&day_filter=' . $i . (isset($_GET['path']) ? '&path=' . $app_path : '')
        ) . '">' . ($i < 10 ? '0' . $i : $i) . '</a>';
}

/**
 * start build listing query
 */
$listing_sql_query = '';
$select_sql_query = '';
$listing_sql_query_having = '';
$sql_query_having = [];

//add filters query
$listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

//add access query
$listing_sql_query = items::add_access_query($reports['entities_id'], $listing_sql_query);

//filter by period
if ($period == 'hourly') {
    $listing_sql_query .= " and date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y-%c-%e')='" . $year_filter . "-" . $month_filter . "-" . $day_filter . "'";
} elseif ($period == 'daily') {
    $listing_sql_query .= " and date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y-%c')='" . $year_filter . "-" . $month_filter . "'";
} elseif ($period == 'monthly') {
    $listing_sql_query .= " and date_format(FROM_UNIXTIME(" . $xaxis_sql_name . "),'%Y')='" . $year_filter . "'";
}

//prepare fields sum for formulas
$sql_query_select = fieldtype_formula::prepare_query_select($reports['entities_id'], '');

//prepare having query for formula fields
if (isset($sql_query_having[$reports['entities_id']])) {
    $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$reports['entities_id']]);
}

//prepare parent item query
if (isset($_GET['path'])) {
    $path_info = items::parse_path($_GET['path']);
    if ($path_info['parent_entity_item_id'] > 0) {
        $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
    }
}

//prepare yaxis
$yaxis = [];
if ($period == 'daily') {
    $prepare_date_str = $year_filter . '-' . ($month_filter < 10 ? '0' . $month_filter : $month_filter);

    foreach (explode(',', $reports['yaxis']) as $id) {
        for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month_filter, $year_filter); $i++) {
            $xaxis_key = trim(format_date(strtotime($prepare_date_str . '-' . ($i < 10 ? '0' . $i : $i))));
            $yaxis[$id][$xaxis_key] = 0;
        }
    }
} elseif ($period == 'monthly') {
    foreach (explode(',', $reports['yaxis']) as $id) {
        for ($i = 1; $i <= 12; $i++) {
            $xaxis_key = trim(i18n_date('F Y', strtotime($year_filter . '-' . ($i < 10 ? '0' . $i : $i) . '-01')));
            $yaxis[$id][$xaxis_key] = 0;
        }
    }
} elseif ($period == 'hourly') {
    foreach (explode(',', $reports['yaxis']) as $id) {
        for ($i = 0; $i <= 23; $i++) {
            $xaxis_key = ($i < 10 ? '0' . $i : $i);
            $yaxis[$id][$xaxis_key] = 0;
        }
    }
}


//print_rr($yaxis);

$listing_sql = "select " . $xaxis_sql_name . " as xaxis_field, e.* " . $sql_query_select . " from app_entity_" . $reports['entities_id'] . " e  where " . $xaxis_sql_name . ">0 " . $listing_sql_query . " order by " . $xaxis_sql_name;
$items_query = db_query($listing_sql, false);
while ($item = db_fetch_array($items_query)) {
    //print_r($item);

    if ($period == 'daily') {
        $xaxis_key = trim(format_date($item['xaxis_field']));
    } elseif ($period == 'monthly') {
        $xaxis_key = trim(i18n_date('F Y', $item['xaxis_field']));
    } elseif ($period == 'yearly') {
        $xaxis_key = trim(i18n_date('Y', $item['xaxis_field']));
    } elseif ($period == 'hourly') {
        $xaxis_key = trim(i18n_date('H', $item['xaxis_field']));
    }

    foreach (explode(',', $reports['yaxis']) as $id) {
        if (!strlen($item['field_' . $id])) {
            $item['field_' . $id] = 0;
        }

        if (!isset($yaxis[$id][$xaxis_key])) {
            $yaxis[$id][$xaxis_key] = $item['field_' . $id];
        } else {
            $yaxis[$id][$xaxis_key] += $item['field_' . $id];
        }
    }
}

//prepare yaxis in yearly mode if empty
if ($period == 'yearly' and !count($yaxis)) {
    foreach (explode(',', $reports['yaxis']) as $id) {
        $xaxis_key = trim(i18n_date('Y', time()));
        $yaxis[$id][$xaxis_key] = 0;
    }
}


//prepare data to display
foreach ($yaxis as $id => $data) {
    $field_info = $app_fields_cache[$reports['entities_id']][$id];

    foreach ($data as $k => $v) {
        $yaxis[$id][$k] = "{
            y:{$v},
            name:'" . fieldtype_input_numeric::number_format($v, $field_info['configuration']) . "',
            field_name:'" . addslashes($field_info['name']) . "',
            " . ((isset($yaxis_color[$id]) and strlen($yaxis_color[$id])) ? "color: '" . $yaxis_color[$id] . "'" : '') . "
         }";
    }
}

//print_r($yaxis);

$yaxis_html = [];
$xaxis = [];

foreach (current($yaxis) as $k => $v) {
    $xaxis[] = "'" . $k . "'";
}

//remove zero value
$v = $graph->remove_zero_values($xaxis, $yaxis);
//print_rr($v);
$xaxis = $v['xaxis'];
$yaxis = $v['yaxis'];

foreach ($yaxis as $id => $data) {
    $field_info = $app_fields_cache[$reports['entities_id']][$id];

    $yaxis_html[] = '{
        name:"' . addslashes($field_info['name']) . '",        
        data:[' . implode(',', $data) . '],
        ' . ((isset($yaxis_color[$id]) and strlen($yaxis_color[$id])) ? 'color: "' . $yaxis_color[$id] . '"' : '') . '
      }';
}

//print_r($yaxis_html);
//print_r($xaxis);
//include filters

if ($app_module_path == 'ext/graphicreport/view') {
    $filters_preivew = new filters_preview($fiters_reports_id);
    $filters_preivew->redirect_to = 'graphicreport' . $reports['id'];
    $filters_preivew->has_listing_configuration = false;

    if (isset($_GET['path'])) {
        $filters_preivew->path = $_GET['path'];
        $filters_preivew->include_parent_filters = false;
    }

    echo $filters_preivew->render();
    ?>

    <?php

    $html = '';

    $url_params = (isset($_GET['path']) ? '&path=' . $app_path : '');

    $chart_type_list = [
        'line' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&chart_type=line' . $url_params
            ) . '">' . TEXT_EXT_CHART_TYPE_LINE . '</a>',
        'column' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&chart_type=column' . $url_params
            ) . '">' . TEXT_EXT_CHART_TYPE_COLUMN . '</a>'
    ];

    $html .= select_button_tag(
        $chart_type_list,
        ($chart_type == 'line' ? TEXT_EXT_CHART_TYPE_LINE : TEXT_EXT_CHART_TYPE_COLUMN)
    );

    $period_list = [
        'hourly' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&period=hourly' . $url_params
            ) . '">' . TEXT_HOURLY . '</a>',
        'daily' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&period=daily' . $url_params
            ) . '">' . TEXT_DAILY . '</a>',
        'monthly' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&period=monthly' . $url_params
            ) . '">' . TEXT_MONTHLY . '</a>',
        'yearly' => '<a href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . '&period=yearly' . $url_params
            ) . '">' . TEXT_YEARLY . '</a>'
    ];

    $period_name_list = [
        'hourly' => TEXT_HOURLY,
        'daily' => TEXT_DAILY,
        'monthly' => TEXT_MONTHLY,
        'yearly' => TEXT_YEARLY
    ];

    $html .= select_button_tag($period_list, $period_name_list[$period]);

    if ($period == 'daily') {
        $html .= select_button_tag($months_list, $months_array[$month_filter - 1]) . ' ' . select_button_tag(
                $years_list,
                $year_filter
            );
    } elseif ($period == 'monthly') {
        $html .= select_button_tag($years_list, $year_filter);
    } elseif ($period == 'hourly') {
        //echo select_button_tag($days_list, $day_filter) . ' ' . select_button_tag($months_list, $months_array[$month_filter - 1]) . ' ' . select_button_tag($years_list, $year_filter);
        $html .= '
            <div class="btn-group">
                <div class="input-group input-medium date datepicker datepicker-filter" style="width: 160px !important" is-init="0">' .
            input_tag(
                'filter_by_day',
                $year_filter . '-' . ($month_filter < 10 ? '0' . $month_filter : $month_filter) . '-' . ($day_filter < 10 ? '0' . $day_filter : $day_filter),
                ['class' => 'form-control', 'readonly' => true]
            ) . '
                        <span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
                </div>
            </div>
            <script>
            $(function(){            
                $(".datepicker-filter").on("changeDate", function() {
                    let selected_date = $("#filter_by_day").val().split("-")
                                        
                    if(selected_date.length!=3) return false
                    
                    if($(this).attr("is-init")==0)
                    {
                        $(this).attr("is-init",1)
                    }
                    else
                    {                              
                        location.href="' . url_for(
                'ext/graphicreport/view',
                'id=' . $reports['id'] . (isset($_GET['path']) ? '&path=' . $app_path : '')
            ) . 'year_filter="+selected_date[0]+"&month_filter="+parseInt(selected_date[1])+"&day_filter="+parseInt(selected_date[2])
                    }
                });
            })
            </script>
           ';
    }

    echo $html;
}


?>

<p>
<div id="graphicreport_container<?php
echo $reports['id'] ?>" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</p>

<script type="text/javascript">

    $(function () {
        $('#graphicreport_container<?php echo $reports['id'] ?>').highcharts({
            chart: {
                type: '<?php echo $chart_type ?>'
            },
            title: {
                text: '<?php echo(count($yaxis_html) == 0 ? TEXT_NO_RECORDS_FOUND : "") ?>'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: [<?php echo implode(',', $xaxis) ?>],
                labels: {
                    rotation: -90
                }
            },
            yAxis: {
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.axis.defaultLabelFormatter.call(this);
                    }
                }
            },
            <?php echo $graph->render_plot_options() ?>
            tooltip: {
                formatter: function () {
                    return '<span style="font-size: 10px;">' + this.x + '</span><br><b>' + this.point.field_name + ': </b>' + this.point.name;
                }
            },

            series: [<?php echo implode(',', $yaxis_html) ?>]
        });
    });

</script>