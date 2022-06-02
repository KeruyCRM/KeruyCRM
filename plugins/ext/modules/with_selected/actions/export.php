<?php

require('includes/classes/items/items_export.php');

$template_info_query = db_query(
    "select * from app_ext_export_selected where id=" . _GET(
        'templates_id'
    ) . " and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to))"
);
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'export_docx':

        if (!isset($app_selected_items[_POST('reports_id')])) {
            $app_selected_items[_POST('reports_id')] = [];
        }

        require_once(CFG_PATH_TO_DOMPDF);

        require_once(CFG_PATH_TO_PHPWORD);

        $docx = new export_selected_docx($template_info, $app_selected_items[_POST('reports_id')]);
        $filename = $docx->prepare_template_file();

        switch ($_POST['export_type']) {
            case 'print':
                $docx->print_html($filename);
                break;
            case 'pdf':
                $docx->download_pdf($filename);
                break;
            case 'docx':
                $docx->download($filename);
                break;
        }

        exit();

        break;
    case 'export_xlsx':

        if (!isset($app_selected_items[_POST('reports_id')])) {
            $app_selected_items[_POST('reports_id')] = [];
        }

        if (count($app_selected_items[_POST('reports_id')]) > 0 and strlen($template_info['export_fields'])) {
            $current_entity_id = $template_info['entities_id'];
            $current_entity_info = db_find('app_entities', $current_entity_id);

            $listing_fields = [];
            $export = [];
            $heading = [];

            //adding reserved fields               
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . $template_info['export_fields'] . ") and f.entities_id='" . db_input(
                    $template_info['entities_id']
                ) . "' and f.forms_tabs_id=t.id order by field(f.id," . $template_info['export_fields'] . ")"
            );
            while ($fields = db_fetch_array($fields_query)) {
                if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                    $heading = array_merge(
                        $heading,
                        fieldtype_dropdown_multilevel::output_listing_heading($fields, true)
                    );
                } else {
                    $heading[] = fields_types::get_option($fields['type'], 'name', $fields['name']);
                }

                $listing_fields[] = $fields;
            }

            //adding item url
            if ($template_info['export_url'] == 1) {
                $heading[] = TEXT_URL_HEADING;
            }

            $export[] = $heading;

            $selected_items = implode(',', $app_selected_items[_POST('reports_id')]);

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select(
                $current_entity_id,
                '',
                false,
                ['fields_in_listing' => $template_info['export_fields']]
            );

            $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
            $items_query = db_query($listing_sql);
            while ($item = db_fetch_array($items_query)) {
                $row = [];

                $path_info_in_report = [];

                if ($current_entity_info['parent_id'] > 0) {
                    $path_info_in_report = items::get_path_info($current_entity_id, $item['id']);
                }

                foreach ($listing_fields as $field) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $item);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $item,
                        'is_export' => true,
                        'reports_id' => $_POST['reports_id'],
                        'path' => (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_entity_id . '-' . $item['id']),
                        'path_info' => $path_info_in_report
                    ];

                    if ($field['type'] == 'fieldtype_dropdown_multilevel') {
                        $row = array_merge($row, fieldtype_dropdown_multilevel::output_listing($output_options, true));
                    } else {
                        if (in_array($field['type'], ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea'])) {
                            $row[] = trim(fields_types::output($output_options));
                        } else {
                            $row[] = trim(strip_tags(fields_types::output($output_options)));
                        }
                    }
                }

                if ($template_info['export_url'] == 1) {
                    $row[] = url_for(
                        'items/info',
                        'path=' . (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_entity_id . '-' . $item['id'])
                    );
                }

                $export[] = $row;
            }


            //print_rr($export);
            //exit();
            //xlsx export   
            $items_export = new items_export(db_input_protect($_POST['filename']));
            $items_export->xlsx_from_array($export);
        }

        exit();
        break;

    case 'export_txt':
    case 'export_csv':

        if (!isset($app_selected_items[_POST('reports_id')])) {
            $app_selected_items[_POST('reports_id')] = [];
        }

        if (count($app_selected_items[_POST('reports_id')]) > 0 and strlen($template_info['export_fields'])) {
            $current_entity_id = $template_info['entities_id'];
            $current_entity_info = db_find('app_entities', $current_entity_id);

            $separator = "\t";
            $listing_fields = [];
            $export = [];
            $heading = [];

            $filename = db_input_protect($_POST['filename']);

            $file_extension = $app_module_action == 'export_csv' ? 'csv' : 'txt';

            //start export
            if ($file_extension == 'csv') {
                header("Content-type: Application/octet-stream");
                header("Content-disposition: attachment; filename=" . $filename . ".csv");
            } else {
                header("Content-type: text/plain");
                header("Content-disposition: attachment; filename=" . $filename . ".txt");
            }

            header("Pragma: no-cache");
            header("Expires: 0");

            //adding reserved fields            
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . $template_info['export_fields'] . ") and f.entities_id='" . db_input(
                    $template_info['entities_id']
                ) . "' and f.forms_tabs_id=t.id order by field(f.id," . $template_info['export_fields'] . ")"
            );
            while ($fields = db_fetch_array($fields_query)) {
                if ($fields['type'] == 'fieldtype_dropdown_multilevel') {
                    $heading = array_merge(
                        $heading,
                        fieldtype_dropdown_multilevel::output_listing_heading($fields, true)
                    );
                } else {
                    $heading[] = str_replace(["\n\r", "\r", "\n", $separator],
                        ' ',
                        fields_types::get_option($fields['type'], 'name', $fields['name']));
                }

                $listing_fields[] = $fields;
            }

            //adding item url
            if ($template_info['export_url'] == 1) {
                $heading[] = TEXT_URL_HEADING;
            }

            //outpout heading
            $content = implode($separator, $heading) . "\n";

            if ($file_extension == 'csv') {
                echo chr(0xFF) . chr(0xFE) . mb_convert_encoding($content, 'UTF-16LE', 'UTF-8');
            } else {
                echo $content;
            }

            $selected_items = implode(',', $app_selected_items[$_POST['reports_id']]);

            //prepare forumulas query
            $listing_sql_query_select = fieldtype_formula::prepare_query_select(
                $current_entity_id,
                '',
                false,
                ['fields_in_listing' => $template_info['export_fields']]
            );

            $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
            $items_query = db_query($listing_sql);
            while ($item = db_fetch_array($items_query)) {
                $row = [];

                $path_info_in_report = [];

                if ($current_entity_info['parent_id'] > 0) {
                    $path_info_in_report = items::get_path_info($current_entity_id, $item['id']);
                }

                foreach ($listing_fields as $field) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $item);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $item,
                        'is_export' => true,
                        'reports_id' => $_POST['reports_id'],
                        'path' => (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_entity_id . '-' . $item['id']),
                        'path_info' => $path_info_in_report
                    ];

                    if ($field['type'] == 'fieldtype_dropdown_multilevel') {
                        $row = array_merge($row, fieldtype_dropdown_multilevel::output_listing($output_options, true));
                    } else {
                        if (in_array($field['type'], ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea'])) {
                            $row[] = str_replace(["\n\r", "\r", "\n", $separator],
                                ' ',
                                trim(fields_types::output($output_options)));
                        } else {
                            $row[] = str_replace(["\n\r", "\r", "\n", $separator],
                                ' ',
                                trim(strip_tags(fields_types::output($output_options))));
                        }
                    }
                }

                if ($template_info['export_url'] == 1) {
                    $row[] = url_for(
                        'items/info',
                        'path=' . (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_entity_id . '-' . $item['id'])
                    );
                }

                //outpout row
                $content = implode($separator, $row) . "\n";
                if ($file_extension == 'csv') {
                    echo chr(0xFF) . chr(0xFE) . mb_convert_encoding($content, 'UTF-16LE', 'UTF-8');
                } else {
                    echo $content;
                }
            }
        }

        exit();
        break;
}

