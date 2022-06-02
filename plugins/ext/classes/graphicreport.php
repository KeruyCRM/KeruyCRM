<?php

class graphicreport
{
    function __construct($report)
    {
        $this->report = $report;
    }

    function render_plot_options()
    {
        if ($this->report['show_totals'] == 0) {
            return '';
        }

        $html = "
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        align: 'left',
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontWeight: 'normal',
                            fontSize: '11px;',
                        },
                        formatter: function () {
                            return Number.isInteger(this.y) ? this.y : Highcharts.numberFormat(this.y,2);
                        }                        
                    }
                },
                line: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontFamily: 'Arial, sans-serif',
                            fontWeight: 'normal',
                            fontSize: '11px;',
                        },
                        formatter: function () {
                            return Number.isInteger(this.y) ? this.y : Highcharts.numberFormat(this.y,2);
                        }
                    },
                }
            },    
       ";

        return $html;
    }

    function remove_zero_values($xaxis, $yaxis)
    {
        if ($this->report['hide_zero'] == 0) {
            return [
                'xaxis' => $xaxis,
                'yaxis' => $yaxis,
            ];
        }

        //print_rr($yaxis);
        foreach ($xaxis as $xkey => $date) {
            $date = substr($date, 1, -1);

            $is_zero = true;

            //check if zero
            foreach ($yaxis as $field_id => $data) {
                if (isset($data[$date]) and !strstr($data[$date], 'y:0,')) {
                    $is_zero = false;
                }
            }

            //remove zero values
            if ($is_zero) {
                unset($xaxis[$xkey]);

                foreach ($yaxis as $field_id => $data) {
                    if (isset($yaxis[$field_id][$date])) {
                        unset($yaxis[$field_id][$date]);
                    }
                }
            }
        }

        return [
            'xaxis' => $xaxis,
            'yaxis' => $yaxis,
        ];
    }
}
