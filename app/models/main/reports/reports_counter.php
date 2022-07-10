<?php

namespace Models\Main\Reports;

class Reports_counter
{
    public $reports_query;
    public $title;
    public $parent_item_id;
    public $common_filter_reports_id;

    function __construct()
    {
        //global $app_path;

        $this->reports_query = false;
        $this->title = false;

        $this->parent_item_id = 0;

        $this->common_filter_reports_id = false;

        $this->redirect_to = (strlen(\K::$fw->app_path) > 0 ? 'listing' : 'report');
    }

    function render()
    {
        //global $app_path, $app_current_users_filter, $app_module_path;

        $html = '';

        if (!$this->reports_query) {
            $reports_query = \K::model()->db_query_exec($this->reports_query());
        } else {
            $reports_query = \K::model()->db_query_exec($this->reports_query);
        }

        $count = 0;
        //while ($reports = db_fetch_array($reports_query)) {
        foreach ($reports_query as $reports) {
            $color_style = (strlen(
                $reports['in_dashboard_counter_color']
            ) ? 'style="color: ' . $reports['in_dashboard_counter_color'] . '"' : '');
            $color_bg_style = (strlen(
                $reports['in_dashboard_counter_bg_color']
            ) ? 'style="background-color: ' . $reports['in_dashboard_counter_bg_color'] . '"' : '');

            $reports_details = $this->get_reports_details($reports);

            $totals_html = '';

            if (count($reports_details['totals'])) {
                $totals_html = '<div class="totals" ' . ($reports['dashboard_counter_hide_count'] == 1 ? 'style="padding-left: 0"' : '') . '><table>';
                foreach ($reports_details['totals'] as $v) {
                    $totals_html .= '
							<tr>
								<th>' . $v['title'] . ':&nbsp;</th>
								<td>' . $v['value'] . '</td>
							</tr>
							';
                }
                $totals_html .= '</table></div><div style="clear:left"></div>';
            }

            if ($this->common_filter_reports_id > 0) {
                $click_url = \Helpers\Urls::url_for(
                    'main/reports/common_filters',
                    'action=use&redirect_to=' . $this->redirect_to . '&reports_id=' . $this->common_filter_reports_id . '&use_filters=' . $reports['id'] . (strlen(
                        \K::$fw->app_path
                    ) ? '&path=' . \K::$fw->app_path : '')
                );
            } else {
                $click_url = \Helpers\Urls::url_for('main/reports/view', 'reports_id=' . $reports['id']);
            }

            $is_selected = false;

            if (isset(\K::$fw->app_current_users_filter[$this->common_filter_reports_id]) and in_array(
                    \K::$fw->app_module_path,
                    ['items/items', 'reports/view']
                )) {
                $is_selected = (\K::$fw->app_current_users_filter[$this->common_filter_reports_id] == $reports['name'] ? true : false);
            }

            //Hide counter if there are no records
            if ($reports['dashboard_counter_hide_zero_count'] == 1 and $reports_details['count'] == 0) {
                continue;
            }

            if ($count > 0 and $count / 4 == floor($count / 4)) {
                $html .= '</div><div class="row">';
            }

            $html .= '
                    <div class="col-md-3 col-sm-4">
                        <div ' . $color_bg_style . ' class="stats-overview stat-block stats-default ' . ($is_selected ? 'selected' : '') . ' reports-counter-' . $reports['id'] . '" onClick="location.href=\'' . $click_url . '\'">
                            <table>
                                <tr>	
                                ' . (($reports['in_dashboard_icon'] and strlen(
                        $reports['menu_icon']
                    )) ? '<td><div class="icon">' . \Helpers\App::app_render_icon(
                        $reports['menu_icon'],
                        $color_style
                    ) . '</div></td>' : '') . '
                                        <td>

                                        <table>
                                                        <tr>
                                                                ' . ($reports['dashboard_counter_hide_count'] != 1 ? '
                                                                <td>												
                                                                        <div class="display stat ok huge">							
                                                                                <div class="percent float-left" ' . $color_style . '>
                                                                                        ' . $reports_details['count'] . '
                                                                                </div>
                                                                        </div>
                                                                </td>
                                                                ' : '') . '		
                                                                <td>
                                                                        ' . $totals_html . '
                                                                </td>
                                                        </tr>
                                                </table>		
                                                <div class="details">
                                                        <div class="title">
                                                                 ' . $reports['name'] . '
                                                        </div>
                                                        <div class="numbers">

                                                        </div>
                                                </div>										 																 		
                                        </td>
                                </tr>
                            </table>

                        </div>
                    </div>
					
            ';

            $count++;
        }

        if (strlen($html)) {
            $html = ($this->title != '' ? '<h3 class="page-title">' . (!$this->title ? \K::$fw->TEXT_STATISTICS : $this->title) . '</h3>' : '') .
                '<div class="row stats-overview-cont">
                        <div class="col-md-12">
                            <div class="row">
                                ' . $html . '
                            </div>    
                        </div>   
                    </div>';
        }

        return $html;
    }

    function get_reports_details($report_info)
    {
        global $sql_query_having;

        $sum_by_fields = [];

        if (strlen($report_info['in_dashboard_counter_fields'])) {
            $sum_by_fields = explode(',', $report_info['in_dashboard_counter_fields']);
        }

        if ($report_info['dashboard_counter_sum_by_field'] > 0) {
            $sum_by_fields[] = $report_info['dashboard_counter_sum_by_field'];
        }

        $listing_sql_query_select = '';
        $listing_sql_query = '';
        $listing_sql_query_join = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];//TODO WTF

        //prepare formulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $report_info['entities_id'],
            $listing_sql_query_select,
            false,
            ['fields_in_listing' => implode(',', $sum_by_fields), 'reports_id' => $report_info['id']]
        );

        //prepare listing query
        $listing_sql_query = reports::add_filters_query($report_info['id'], $listing_sql_query);

        //prepare having query for formula fields
        if (isset($sql_query_having[$report_info['entities_id']])) {
            $listing_sql_query_having = reports::prepare_filters_having_query(
                $sql_query_having[$report_info['entities_id']]
            );
        }

        if ($this->parent_item_id > 0) {
            $listing_sql_query .= " and e.parent_item_id='" . $this->parent_item_id . "'";
        }

        //check view assigned only access
        $listing_sql_query = items::add_access_query(
            $report_info['entities_id'],
            $listing_sql_query,
            $report_info['displays_assigned_only']
        );

        //add having query
        $listing_sql_query .= $listing_sql_query_having;

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $report_info['entities_id'] . " e " . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query . " ";
        $items_query = db_query($listing_sql, false);
        $items_count = db_num_rows($items_query);

        $sum_fields = [];

        if (count($sum_by_fields)) {
            $sum_query = [];

            $fields_query = db_query(
                "select f.* from app_fields f, app_forms_tabs t  where f.id in (" . implode(
                    ',',
                    $sum_by_fields
                ) . ") and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $sum_fields[$fields['id']] = [
                    'title' => (strlen(
                        $fields['short_name']
                    ) ? $fields['short_name'] : $fields['name']),
                    'configuration' => $fields['configuration']
                ];

                if ($fields['type'] != 'fieldtype_formula') {
                    $sum_query[] = " sum(field_" . $fields['id'] . ") as sum_field_" . $fields['id'];
                }
            }

            if (count($sum_fields)) {
                $fields_totals = [];

                //calculate totals from itesm
                while ($items = db_fetch_array($items_query)) {
                    foreach ($sum_fields as $k => $v) {
                        if (!strlen($items['field_' . $k])) {
                            continue;
                        }

                        if (isset($fields_totals[$k])) {
                            $fields_totals[$k] += $items['field_' . $k];
                        } else {
                            $fields_totals[$k] = $items['field_' . $k];
                        }
                    }
                }

                foreach ($sum_fields as $k => $v) {
                    $cfg = new fields_types_cfg($v['configuration']);

                    $value = ((isset($fields_totals[$k]) and strlen($fields_totals[$k])) ? $fields_totals[$k] : 0);

                    if (strlen($cfg->get('number_format')) > 0) {
                        $format = explode('/', str_replace('*', '', $cfg->get('number_format')));
                        $value = number_format($value, $format[0], $format[1], $format[2]);
                    } elseif (strstr($value, '.')) {
                        $value = number_format($value, 2, '.', '');
                    }

                    $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');

                    $sum_fields[$k]['value'] = $value;
                }
                //print_r($sum_fields);
            }
        }

        if ($report_info['dashboard_counter_sum_by_field'] > 0) {
            $items_count = $sum_fields[$report_info['dashboard_counter_sum_by_field']]['value'];
            unset($sum_fields[$report_info['dashboard_counter_sum_by_field']]);
        }

        return ['count' => $items_count, 'totals' => $sum_fields];
    }

    //build counter reports query with common reports
    function reports_query()
    {
        //global $app_logged_users_id, $app_user, $app_users_cfg;

        $where_sql = '';

        //check hidden common reports
        if (strlen(\K::app_users_cfg()->get('hidden_common_reports')) > 0) {
            $where_sql = " and r.id not in (" . \K::app_users_cfg()->get('hidden_common_reports') . ")";
        }

        //get common reports list
        $common_reports_list = [];
        $reports_query = \K::model()->db_query_exec(
            'select r.* from app_reports r, app_entities e, app_entities_access ea where r.entities_id = e.id and e.id = ea.entities_id and length(ea.access_schema) > 0 and ea.access_groups_id = ? and (FIND_IN_SET( ? , r.users_groups) or FIND_IN_SET( ? , r.assigned_to)) and r.in_dashboard_counter = 1 and r.reports_type = ? ' . $where_sql . ' order by r.dashboard_sort_order, r.name',
            [
                \K::$fw->app_user['group_id'],
                \K::$fw->app_user['group_id'],
                \K::$fw->app_user['id'],
                'common'
            ]
        );
        //while ($reports = db_fetch_array($reports_query)) {
        foreach ($reports_query as $reports) {
            $common_reports_list[] = $reports['id'];
        }

        //create reports query inclue common reports
        $reports_query = "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and ((r.created_by=" . \K::model(
            )->quote(
                \K::$fw->app_logged_users_id
            )
            . " and r.reports_type='standard' and  r.in_dashboard_counter=1)  " . (count(
                $common_reports_list
            ) > 0 ? " or r.id in(" . implode(
                    ',',
                    $common_reports_list
                ) . ")" : "") . ") order by r.dashboard_counter_sort_order, r.dashboard_sort_order, r.name";

        return $reports_query;
    }
}