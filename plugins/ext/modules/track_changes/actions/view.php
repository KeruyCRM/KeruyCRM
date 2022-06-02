<?php

require(CFG_PATH_TO_PHPSPREADSHEET);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


$app_reports_query = db_query(
    "select * from app_ext_track_changes where id='" . _get::int(
        'reports_id'
    ) . "' and is_active=1 and (find_in_set('" . $app_user['group_id'] . "',users_groups) or find_in_set('" . $app_user['id'] . "',assigned_to))"
);
if (!$app_reports = db_fetch_array($app_reports_query)) {
    redirect_to('dashboard/access_forbidden');
}

//autoclear log
track_changes::reset($app_reports);

switch ($app_module_action) {
    case 'export':

        $export = [];
        $export_url = [];

        $export[] = [
            TEXT_TYPE,
            TEXT_ENTITY,
            TEXT_ID,
            TEXT_NAME,
            TEXT_COMMENT . ' / ' . TEXT_FIELDS,
            TEXT_USERS,
            TEXT_DATE_ADDED
        ];

        $export_url[] = '';

        $hidden_fields = [];

        //get hidden fields for user group
        if ($app_user['group_id'] > 0) {
            $fields_query = db_query(
                "select * from app_fields_access where access_groups_id='" . $app_user['group_id'] . "' and access_schema='hide'"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $hidden_fields[] = $fields['fields_id'];
            }
        }

        $items_holder = [];

        $date_row = '';

        $where_sql = '';

        $filters = [];
        $filters[] = ['name' => 'from', 'value' => $_POST['from']];
        $filters[] = ['name' => 'to', 'value' => $_POST['to']];
        $filters[] = ['name' => 'id', 'value' => $_POST['id']];
        $filters[] = ['name' => 'entities_id', 'value' => $_POST['entities_id']];
        $filters[] = ['name' => 'created_by', 'value' => $_POST['created_by']];
        $filters[] = ['name' => 'type', 'value' => $_POST['type']];

        foreach ($filters as $filter) {
            if (strlen($filter['value']) > 0) {
                switch ($filter['name']) {
                    case 'from':
                        $where_sql .= " and FROM_UNIXTIME(tcl.date_added,'%Y-%m-%d')>='" . $filter['value'] . "'";
                        break;
                    case 'to':
                        $where_sql .= " and FROM_UNIXTIME(tcl.date_added,'%Y-%m-%d')<='" . $filter['value'] . "'";
                        break;
                    case 'id':
                        $where_sql .= " and tcl.items_id='" . $filter['value'] . "'";
                        break;
                    default:
                        $where_sql .= " and tcl.{$filter['name']}='{$filter['value']}'";
                        break;
                }
            }
        }

        $where_sql .= track_changes::exclude_hidden_entities_query();

        //echo $where_sql;

        $listing_sql = "select tcl.*,tc.color_delete, tc.color_insert, tc.color_update, tc.color_comment, e.name as entity_name, c.description as comment from app_ext_track_changes_log tcl left join app_entities e on e.id=tcl.entities_id left join app_comments c on c.id=tcl.comments_id, app_ext_track_changes tc where tcl.reports_id='" . $app_reports['id'] . "' and tc.id=tcl.reports_id {$where_sql} order by tcl.id desc";
        $items_query = db_query($listing_sql);
        while ($item = db_fetch_array($items_query)) {
            if (!isset($items_holder[$item['entities_id']][$item['items_id']])) {
                $items_holder[$item['entities_id']][$item['items_id']] = [
                    'path' => items::get_path_info($item['entities_id'], $item['items_id']),
                    'name' => items::get_heading_field($item['entities_id'], $item['items_id']),
                ];
            }

            $item_info = $items_holder[$item['entities_id']][$item['items_id']];

            $html_fields = '';

            $log_fields_query = db_query(
                "select f.*, lf.value as fields_value from app_ext_track_changes_log_fields lf, app_fields f  where log_id='" . $item['id'] . "'  and f.id=lf.fields_id order by lf.log_id"
            );
            while ($field = db_fetch_array($log_fields_query)) {
                //check filed access
                if (in_array($field['id'], $hidden_fields)) {
                    continue;
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_listing' => true,
                    'path' => $item_info['path']['full_path'],
                    'path_info' => $item_info['path'],
                    'is_comments_listing' => true,
                ];

                $html_fields .= fields_types::get_option($field['type'], 'name', $field['name']) . ": " . strip_tags(
                        str_replace(["<br>", '</div>'], "\n", fields_types::output($output_options))
                    ) . "\n";
            }

            $comment = strip_tags(str_replace(["<br>", '</div>'], "\n", $item['comment']));

            if ($date_row != date('Y-m-d', $item['date_added'])) {
                $date_row = date('Y-m-d', $item['date_added']);

                $export[] = [
                    format_date($item['date_added'])
                ];

                $export_url[] = '';
            }

            $item_url = url_for('items/info', 'path=' . $item_info['path']['full_path']);

            if ($item['type'] == 'delete') {
                $item_info['name'] = $item['items_name'];
                $item_url = '';
            }


            $export[] = [
                strip_tags(track_changes::get_item_label_by_type($item)),
                strip_tags($item['entity_name']),
                $item['items_id'],
                $item_info['name'],
                $comment . $html_fields,
                strip_tags(track_changes::get_created_by_label($item)),
                format_date_time($item['date_added']),
                $item_url
            ];

            $export_url[] = $item_url;
        }

        /*
        echo '<pre>';
        print_r($export);
        print_r($export_url);
        exit();
        */


        $filename = app_remove_special_characters($app_reports['name']);

        //create Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()->setCreator($app_user['name'])
            ->setTitle(substr($filename, 0, 31));

        // Add some data
        $spreadsheet->getActiveSheet()->fromArray($export, null, 'A1');

        //autosize columns
        $highest_column = $spreadsheet->getActiveSheet()->getHighestColumn();

        for ($col = 'A'; $col != $highest_column; $col++) {
            if ($col == 'E') {
                $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth(100);
            } else {
                $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }

            $spreadsheet->getActiveSheet()->getStyle($col . '1')->getFont()->setBold(true);
        }

        $spreadsheet->getActiveSheet()->getColumnDimension($highest_column)->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getStyle($highest_column . '1')->getFont()->setBold(true);

        for ($i = 1; $i <= count($export); $i++) {
            $spreadsheet->getActiveSheet()->getStyle('E' . $i)->getAlignment()->setWrapText(true);

            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':H' . $i)->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            );


            if (strlen($export_url[$i - 1])) {
                $spreadsheet->getActiveSheet()->getCell('D' . $i)->getHyperlink()->setUrl($export_url[$i - 1]);

                // Config
                $link_style_array = [
                    'font' => [
                        'color' => ['rgb' => '0000FF'],
                        'underline' => 'single'
                    ]
                ];

                // Set it!
                $spreadsheet->getActiveSheet()->getStyle("D" . $i)->applyFromArray($link_style_array);
            }
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle(substr($filename, 0, 31));

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . addslashes($filename) . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

        exit();

        break;
    case 'listing':

        $html = '
			<div class="table-scrollable">
				<table class="table table-striped table-bordered table-hover">
				<thead>
				  <tr>						
						<th>' . TEXT_TYPE . '</th>
						<th>' . TEXT_ENTITY . '</th>
						<th>' . TEXT_ID . '</th>
				    <th width="30%">' . TEXT_NAME . '</th>
				    <th width="40%">' . TEXT_COMMENT . ' / ' . TEXT_FIELDS . '</th>
				    <th>' . TEXT_USERS . '</th>
				    <th>' . TEXT_DATE_ADDED . '</th>
				  </tr>
				</thead>
				<tbody>
		';


        $hidden_fields = [];

        //get hidden fields for user group
        if ($app_user['group_id'] > 0) {
            $fields_query = db_query(
                "select * from app_fields_access where access_groups_id='" . $app_user['group_id'] . "' and access_schema='hide'"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $hidden_fields[] = $fields['fields_id'];
            }
        }

        $items_holder = [];

        $date_row = '';

        $where_sql = '';

        foreach ($_POST['filters'] as $filter) {
            if (strlen($filter['value']) > 0) {
                switch ($filter['name']) {
                    case 'from':
                        $where_sql .= " and FROM_UNIXTIME(tcl.date_added,'%Y-%m-%d')>='" . $filter['value'] . "'";
                        break;
                    case 'to':
                        $where_sql .= " and FROM_UNIXTIME(tcl.date_added,'%Y-%m-%d')<='" . $filter['value'] . "'";
                        break;
                    case 'id':
                        $where_sql .= " and tcl.items_id='" . $filter['value'] . "'";
                        break;
                    default:
                        $where_sql .= " and tcl.{$filter['name']}='{$filter['value']}'";
                        break;
                }
            }
        }

        $where_sql .= track_changes::exclude_hidden_entities_query();

        //echo $where_sql;

        $listing_sql = "select tcl.*, tc.color_delete,tc.color_insert, tc.color_update, tc.color_comment, e.name as entity_name, c.description as comment from app_ext_track_changes_log tcl left join app_entities e on e.id=tcl.entities_id left join app_comments c on c.id=tcl.comments_id, app_ext_track_changes tc where tcl.reports_id='" . $app_reports['id'] . "' and tc.id=tcl.reports_id {$where_sql} order by tcl.id desc";
        $listing_split = new split_page($listing_sql, 'track_changes_listing', '', $app_reports['rows_per_page']);
        $items_query = db_query($listing_split->sql_query);
        while ($item = db_fetch_array($items_query)) {
            if (!isset($items_holder[$item['entities_id']][$item['items_id']])) {
                $items_holder[$item['entities_id']][$item['items_id']] = [
                    'path' => items::get_path_info($item['entities_id'], $item['items_id']),
                    'name' => items::get_heading_field($item['entities_id'], $item['items_id']),
                ];
            }

            $item_info = $items_holder[$item['entities_id']][$item['items_id']];

            $html_fields = '';

            $log_fields_query = db_query(
                "select f.*, lf.value as fields_value from app_ext_track_changes_log_fields lf, app_fields f  where log_id='" . $item['id'] . "'  and f.id=lf.fields_id order by lf.log_id"
            );
            while ($field = db_fetch_array($log_fields_query)) {
                //check filed access
                if (in_array($field['id'], $hidden_fields)) {
                    continue;
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_listing' => true,
                    'path' => $item_info['path']['full_path'],
                    'path_info' => $item_info['path'],
                    'is_comments_listing' => true,
                ];

                $html_fields .= "
		            <tr>
		      				<th style='text-align: left;vertical-align: top; font-size: 11px;'>&bull;&nbsp;" . fields_types::get_option(
                        $field['type'],
                        'name',
                        $field['name']
                    ) . ":&nbsp;</th>
		      				<td style='font-size: 11px;'>" . fields_types::output($output_options) . "</td>
		      			</tr>
		        ";
            }


            $html_fields = (strlen(
                $html_fields
            ) ? "<table style='padding-top: 7px;'>" . $html_fields . "</table>" : '');

            $comment = strip_tags($item['comment']);

            if (strlen($comment) > 300) {
                $comment = mb_substr($comment, 0, 300) . '... <a target="_new" href="' . url_for(
                        'items/info',
                        'path=' . $item_info['path']['full_path']
                    ) . '">' . TEXT_MORE_INFO . '</a>';
            }

            if ($date_row != date('Y-m-d', $item['date_added'])) {
                $date_row = date('Y-m-d', $item['date_added']);
                $html .= '
								<tr>
									<td colspan="7"><h5><b>' . format_date($item['date_added']) . '</b></h5></td>
								</tr>
								';
            }

            if ($item['type'] == 'delete') {
                $itme_name = $item['items_name'];
            } else {
                $itme_name = '<a target="_new" href="' . url_for(
                        'items/info',
                        'path=' . $item_info['path']['full_path']
                    ) . '">' . $item_info['name'] . '</a>';
            }

            $html .= '
							<tr>
								<td>' . track_changes::get_item_label_by_type($item) . '</td>
								<td>' . $item['entity_name'] . '</td>
								<td><a href="#" onClick="set_filter_by_id(' . $item['entities_id'] . ',' . $item['items_id'] . ')">' . $item['items_id'] . '</a></td>
								<td style="white-space: normal;">' . $itme_name . '</td>
								<td style="white-space: normal;">' . (strlen(
                    $comment
                ) ? $comment . '<br>' : '') . $html_fields . '</td>
								<td>' . track_changes::get_created_by_label($item) . '</td>
								<td>' . format_date_time($item['date_added']) . '</td>
							</tr>
					';
        }


        if ($listing_split->number_of_rows == 0) {
            $html .= '
				    <tr>
				      <td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td>
				    </tr>
				  ';
        }

        $html .= '
		  </tbody>
		</table>
		</div>
		';

        //add pager
        $html .= '
		  <table width="100%">
		    <tr>
		      <td>' . $listing_split->display_count() . '</td>
		      <td align="right">' . $listing_split->display_links() . '</td>
		    </tr>
		  </table>
		';

        echo $html;

        exit();
        break;
}		