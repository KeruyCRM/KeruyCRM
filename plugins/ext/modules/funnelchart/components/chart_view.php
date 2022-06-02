<!-- handle chart -->
<?php
if ($app_module_path == 'ext/funnelchart/view') {
    $chart_settins = ($funnelchart_type[$reports['id']] == 'funnel' ? "type: 'funnel',marginRight: 450" : "type: 'bar'");
    $click_event = 'funnelchart_items_listin(this.options.name,this.options.filter_by)';
} else {
    $chart_settins = ($funnelchart_type[$reports['id']] == 'funnel' ? "type: 'funnel',marginRight: 300" : "type: 'bar'");
    $click_event = '';
}
?>

<script>
    $(function () {

        $('#funnelchart_container_<?php echo $reports['id'] ?>').highcharts({
            chart: {
                <?php echo $chart_settins ?>
            },
            title: {
                text: '<?php echo TEXT_EXT_TOTAL . ': ' . $count_items ?>',
                x: -50
            },
            yAxis: {
                title: {
                    text: '',
                },
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}: {point.y:,.0f} </b>',
                        color: 'black',
                        softConnector: true
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                <?php echo $click_event ?>
                            }
                        }
                    },
                    neckWidth: '30%',
                    neckHeight: '10%'

                    //-- Other available options
                    // height: pixels or percent
                    // width: pixels or percent
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.point.name + ': ' + this.y + '</b><br><?php echo TEXT_EXT_CONVERSION ?>: ' + this.point.conversion <?php echo $sum_js_tip ?>;
                }
            },
            series: [{
                name: '',
                data: [
                    <?php echo implode(',', $data_js) ?>
                ]
            }
            ]
        });

    })

</script>