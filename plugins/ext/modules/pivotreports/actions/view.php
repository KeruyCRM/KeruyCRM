<?php

switch ($app_module_action) {
    case 'get_localization':

        header("Content-Type: application/javascript");
        header("Cache-Control: max-age=604800, public");

        if (isset($_GET['id'])) {
            $pivotreports_query = db_query(
                "select * from app_ext_pivotreports where id='" . db_input((int)$_GET['id']) . "'"
            );
            if (!$pivotreports = db_fetch_array($pivotreports_query)) {
                exit();
            }
        } else {
            $pivotreports['cfg_numer_format'] = '';
        }

        if (strlen($pivotreports['cfg_numer_format']) > 0) {
            $format = explode('/', str_replace('*', ' ', $pivotreports['cfg_numer_format']));

            $digitsAfterDecimal = $format[0];
            $decimalSep = $format[1];
            $thousandsSep = $format[2];
        } else {
            $digitsAfterDecimal = "2";
            $decimalSep = ".";
            $thousandsSep = " ";
        }

        $html = '
			(function() {
				  var callWithJQuery;
	
				  callWithJQuery = function(pivotModule) {
				    if (typeof exports === "object" && typeof module === "object") {
				      return pivotModule(require("jquery"));
				    } else if (typeof define === "function" && define.amd) {
				      return define(["jquery"], pivotModule);
				    } else {
				      return pivotModule(jQuery);
				    }
				  };
	
				  callWithJQuery(function($) {
			
				    var frFmt, frFmtInt, frFmtPct, nf, tpl;
				    nf = $.pivotUtilities.numberFormat;
				    tpl = $.pivotUtilities.aggregatorTemplates;
			
				    frFmt = nf({
					    digitsAfterDecimal: ' . $digitsAfterDecimal . ',
				      thousandsSep: "' . $thousandsSep . '",
				      decimalSep: "' . $decimalSep . '"
				    });
	
				    frFmtInt = nf({
				      digitsAfterDecimal: 0,
				      thousandsSep: "' . $thousandsSep . '",
				      decimalSep: "' . $decimalSep . '"
				    });
	
				    frFmtPct = nf({
				      digitsAfterDecimal: 1,
				      scaler: 100,
				      suffix: "%",
				      thousandsSep: "' . $thousandsSep . '",
				      decimalSep: "' . $decimalSep . '"
				    });
	
				    return $.pivotUtilities.locales.fr = {
				      localeStrings: {
				        renderError: "' . addslashes(TEXT_EXT_PIVOTREPORTS_RENDER_ERROR) . '",
				        computeError: "' . addslashes(TEXT_EXT_PIVOTREPORTS_COMPUTE_ERROR) . '",
				        uiRenderError: "' . addslashes(TEXT_EXT_PIVOTREPORTS_UIRENDER_ERROR) . '",
				        selectAll: "' . addslashes(TEXT_SELECT_ALL) . '",
				        selectNone: "' . addslashes(TEXT_SELECT_NONOE) . '",
				        tooMany: "' . addslashes(TEXT_EXT_PIVOTREPORTS_TOO_MANY) . '",
				        filterResults: "' . addslashes(TEXT_EXT_PIVOTREPORTS_FILTER_RESULTS) . '",
				        totals: "' . addslashes(TEXT_EXT_PIVOTREPORTS_TOTALS) . '",
				        vs: "' . addslashes(TEXT_EXT_PIVOTREPORTS_VS) . '",
				        by: "' . addslashes(TEXT_EXT_PIVOTREPORTS_BY) . '",
				        save: "' . addslashes(TEXT_BUTTON_SAVE) . '",
				        cancel: "' . addslashes(TEXT_BUTTON_CANCEL) . '"
				      },
	
				      aggregators: {
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_COUNT) . '": tpl.count(frFmtInt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_COUNT_UNIQUE) . '": tpl.countUnique(frFmtInt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_LIST_UNIQUE) . '": tpl.listUnique(", "),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_SUM) . '": tpl.sum(frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_INTEGER_SUM) . '": tpl.sum(frFmtInt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_AVERAGE) . '": tpl.average(frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_MINIMUM) . '": tpl.min(frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_MAXIMUM) . '": tpl.max(frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_SUM_OVER_SUM) . '": tpl.sumOverSum(frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_80_UPPER_BOUND) . '": tpl.sumOverSumBound80(true, frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_80_LOWER_BOUND) . '": tpl.sumOverSumBound80(false, frFmt),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_SUM_FRACTION_TOTAL) . '": tpl.fractionOf(tpl.sum(), "total", frFmtPct),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_SUM_FRACTION_ROWS) . '": tpl.fractionOf(tpl.sum(), "row", frFmtPct),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_SUM_FRACTION_COLUMN) . '": tpl.fractionOf(tpl.sum(), "col", frFmtPct),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_COUNT_FRACTION_TOTAL) . '": tpl.fractionOf(tpl.count(), "total", frFmtPct),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_COUNT_FRACTION_ROWS) . '": tpl.fractionOf(tpl.count(), "row", frFmtPct),
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_COUNT_FRACTION_COLUMNS) . '": tpl.fractionOf(tpl.count(), "col", frFmtPct)
				      },
	
				      renderers: {
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_TABLE) . '": $.pivotUtilities.renderers["Table"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_TABLE_BARCHART) . '": $.pivotUtilities.renderers["Table Barchart"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_HEATMAP) . '": $.pivotUtilities.renderers["Heatmap"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_ROW_HEATMAP) . '": $.pivotUtilities.renderers["Row Heatmap"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_COL_HEATMAP) . '": $.pivotUtilities.renderers["Col Heatmap"]
				      },
	
							c3_renderers:{
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_LINE_CHART) . '": $.pivotUtilities.c3_renderers["Line Chart"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_BAR_CHART) . '": $.pivotUtilities.c3_renderers["Bar Chart"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_STACKED_BAR_CHART) . '": $.pivotUtilities.c3_renderers["Stacked Bar Chart"],
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_AREA_CHART) . '": $.pivotUtilities.c3_renderers["Area Chart"],
							},
	
				      export_renderers:{
				        "' . addslashes(TEXT_EXT_PIVOTREPORTS_AGG_RENDERERS_TSV_EXPORT) . '":	 $.pivotUtilities.export_renderers["TSV Export"]
							}
				    };
				  });
	
				}).call(this);
			';

        echo $html;

        exit();
        break;
}

//check if report exist
$pivotreports_query = db_query("select * from app_ext_pivotreports where id='" . db_input((int)$_GET['id']) . "'");
if (!$pivotreports = db_fetch_array($pivotreports_query)) {
    redirect_to('dashboard/page_not_found');
}

if (!in_array($app_user['group_id'], explode(',', $pivotreports['allowed_groups'])) and $app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}


//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $pivotreports['entities_id']
    ) . "' and reports_type='pivotreports" . $pivotreports['id'] . "' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $pivotreports['entities_id'],
        'reports_type' => 'pivotreports' . $pivotreports['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();

    reports::auto_create_parent_reports($fiters_reports_id);
} else {
    $fiters_reports_id = $reports_info['id'];
}


switch ($app_module_action) {
    case 'set_view_mode':
        $sql_data = ['view_mode' => _get::int('view_mode')];

        if ($app_user['group_id'] > 0 and $pivotreports['allow_edit'] == 1) {
            db_perform(
                'app_ext_pivotreports_settings',
                $sql_data,
                'update',
                "reports_id='" . (int)$_GET['id'] . "' and users_id='" . $app_user['id'] . "'"
            );
        } else {
            db_perform('app_ext_pivotreports', $sql_data, 'update', "id='" . (int)$_GET['id'] . "'");
        }

        redirect_to('ext/pivotreports/view', 'id=' . _get::int('id'));
        break;
    case 'update_settings':

        $sql_data = ['reports_settings' => addslashes($_POST['reports_settings'])];

        if ($app_user['group_id'] > 0 and $pivotreports['allow_edit'] == 1) {
            db_perform(
                'app_ext_pivotreports_settings',
                $sql_data,
                'update',
                "reports_id='" . (int)$_GET['id'] . "' and users_id='" . $app_user['id'] . "'"
            );
        } else {
            db_perform('app_ext_pivotreports', $sql_data, 'update', "id='" . (int)$_GET['id'] . "'");
        }

        exit();

        break;

    case 'get_csv':

        //get fields
        $reports_fields_info = pivotreports::get_fields_by_entity($pivotreports['id'], $pivotreports['entities_id']);
        $reports_fields = $reports_fields_info['reports_fields'];
        $reports_fields_names = $reports_fields_info['reports_fields_names'];
        $reports_fields_dates_format = $reports_fields_info['reports_fields_dates_format'];


        //get parent entities
        $parrent_entities = entities::get_parents($pivotreports['entities_id']);

        $output_array = [];
        $listing_fields = [];
        $parent_entities_listing_fields = [];
        $parent_entities_fields_dates_format = [];

        //adding fields
        if (count($reports_fields)) {
            $fields_query = db_query(
                "select * from app_fields where id in (" . implode(',', $reports_fields) . ") order by id"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $listing_fields[] = $fields;
                $name = (isset($reports_fields_names[$fields['id']]) ? $reports_fields_names[$fields['id']] : fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ));
                $output_array[] = pivotreports::css_prepare($name);
            }
        }

        //added parent entities fields
        if (count($parrent_entities) > 0) {
            foreach ($parrent_entities as $entities_id) {
                $reports_fields_info = pivotreports::get_fields_by_entity($pivotreports['id'], $entities_id);
                $reports_fields = $reports_fields_info['reports_fields'];
                $reports_fields_names = $reports_fields_info['reports_fields_names'];

                //prepare fields dates format
                $parent_entities_fields_dates_format = $parent_entities_fields_dates_format + $reports_fields_info['reports_fields_dates_format'];

                if (count($reports_fields)) {
                    $fields_query = db_query(
                        "select f.*, e.name as entity_name from app_fields f left join app_entities e on f.entities_id=e.id where f.id in (" . implode(
                            ',',
                            $reports_fields
                        ) . ") order by f.id"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $parent_entities_listing_fields[$entities_id][] = $fields;

                        $name = (isset($reports_fields_names[$fields['id']]) ? $reports_fields_names[$fields['id']] : $fields['entity_name'] . ': ' . fields_types::get_option(
                                $fields['type'],
                                'name',
                                $fields['name']
                            ));
                        $output_array[] = pivotreports::css_prepare($name);
                    }
                }
            }
        }

        //output heading
        echo pivotreports::array_to_csv($output_array);

        //build items listing
        $listing_sql_query = '';
        $listing_sql_query_select = '';
        $listing_sql_query_having = '';
        $sql_query_having = [];

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $pivotreports['entities_id'],
            $listing_sql_query_select
        );

        //prepare filters
        $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

        //prepare having query for formula fields
        if (isset($sql_query_having[$pivotreports['entities_id']])) {
            $listing_sql_query_having = reports::prepare_filters_having_query(
                $sql_query_having[$pivotreports['entities_id']]
            );
        }

        //check view assigned only access
        $listing_sql_query = items::add_access_query($pivotreports['entities_id'], $listing_sql_query);

        //include access to parent records
        $listing_sql_query .= items::add_access_query_for_parent_entities($pivotreports['entities_id']);

        $items_sql_query = "select * {$listing_sql_query_select} from app_entity_" . $pivotreports['entities_id'] . " e where id>0 " . $listing_sql_query . $listing_sql_query_having;
        $items_query = db_query($items_sql_query);
        while ($item = db_fetch_array($items_query)) {
            $output_array = [];

            foreach ($listing_fields as $field) {
                $value = items::prepare_field_value_by_type($field, $item);

                //use custom date format for for dates
                if (in_array(
                        $field['type'],
                        ['fieldtype_date_added', 'fieldtype_input_date', 'fieldtype_input_datetime']
                    ) and isset($reports_fields_dates_format[$field['id']]) and (int)$value > 0) {
                    $output_array[] = pivotreports::css_prepare(
                        i18n_date($reports_fields_dates_format[$field['id']], $value)
                    );
                } else {
                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $item,
                        'is_export' => true,
                        'reports_id' => $fiters_reports_id,
                        'path' => '',
                        'path_info' => ''
                    ];

                    $output_array[] = pivotreports::css_prepare(fields_types::output($output_options));
                }
            }

            //prepare parent output if exist
            if (count($parent_entities_listing_fields) > 0) {
                $output_array = pivotreports::prepare_csv_output_for_parent_entities(
                    $output_array,
                    $parent_entities_listing_fields,
                    $parrent_entities,
                    $item['parent_item_id'],
                    $parent_entities_fields_dates_format
                );
            }

            //output items
            echo pivotreports::array_to_csv($output_array);
        }

        exit();
        break;

    case 'print':

        //print_r($_POST);
        $export_content = db_prepare_html_input($_POST['pivot_export_content']);

        $html = '
      <html>
        <head>
						<title>' . $pivotreports['name'] . '</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
            <style>
              body {
                  color: #000;
                  font-family: \'Open Sans\', sans-serif;
                  padding: 0px !important;
                  margin: 0px !important;
               }
		
				       table{
                 border-collapse: collapse;
                 border-spacing: 0px;
								 font-size: 8pt;
               }
		
        			th {
							    text-align: left;
							}
		
							table.pvtTable thead tr th, table.pvtTable tbody tr th {
						    background-color: #f3f3f3;
						    border: 1px solid #CDCDCD;
						    font-size: 8pt;
						    padding: 5px;
							}
		
							table.pvtTable tbody tr td {
							    color: #3D3D3D;
							    padding: 5px;
							    background-color: #FFF;
							    border: 1px solid #CDCDCD;
							    vertical-align: top;
							    text-align: right;
							}
		
							.pvtTotal, .pvtGrandTotal {
							    font-weight: bold;
							}
		
            </style>
        </head>
        <body>
         ' . $export_content . '
         <script>
            window.print();
         </script>
        </body>
      </html>
      ';

        echo $html;

        exit();
        break;
}