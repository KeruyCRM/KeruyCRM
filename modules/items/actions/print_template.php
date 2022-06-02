<?php

if (!export_templates::has_users_access($current_entity_id, $_GET['templates_id'])) {
    redirect_to('dashboard/access_forbidden');
}

$template_info_query = db_query("select * from app_ext_export_templates where id=" . _GET('templates_id'));
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('dashboard/page_not_found');
}

//download docx
if ($template_info['type'] == 'docx' and $app_module_action == 'print') {
    require_once(CFG_PATH_TO_PHPWORD);

    $docx = new export_templates_blocks($template_info);

    if (!isset($app_selected_items[$_POST['reports_id']])) {
        $app_selected_items[$_POST['reports_id']] = [];
    }

    if (count($app_selected_items) == 0) {
        echo TEXT_PLEASE_SELECT_ITEMS;
        exit();
    }

    $selected_items_array = $app_selected_items[$_POST['reports_id']];

    $print_template = export_templates::get_template_extra($selected_items_array, $template_info, 'template_header');

    $selected_items = implode(',', $app_selected_items[$_POST['reports_id']]);

    $filenames = [];

    if (isset($_POST['with_attachments'])) {
        $attachments = [];
        $template_filename_list = [];

        $listing_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $current_entity_id,
                ''
            ) . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        $items_query = db_query($listing_sql);
        while ($item = db_fetch_array($items_query)) {
            if (strlen($template_info['template_filename'])) {
                $pattern = new fieldtype_text_pattern;
                $use_name = $pattern->output_singe_text($template_info['template_filename'], $current_entity_id, $item);
            } else {
                $use_name = $template_info['name'] . '_' . $item['id'];
            }

            $template_filename = $docx->prepare_template_file($template_info['entities_id'], $item['id'], $item);

            $template_folder = items::get_heading_field(
                    $template_info['entities_id'],
                    $item['id'],
                    $item
                ) . ' - ' . $item['id'] . '/';

            $attachments[] = [
                'tmp_filename' => $template_filename,
                'filename' => '',
                'folder' => $template_folder,
                'use_name' => $use_name . '.docx'
            ];
            $template_filename_list[] = $template_filename;

            if (strlen($template_info['save_attachments'])) {
                $save_attachments = explode(',', $template_info['save_attachments']);

                foreach ($save_attachments as $id) {
                    if (isset($item['field_' . $id]) and strlen($item['field_' . $id])) {
                        foreach (explode(',', $item['field_' . $id]) as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => $template_folder . $app_fields_cache[$current_entity_id][$id]['name'] . '/'
                            ];
                        }
                    }
                }


                if (strstr($template_info['save_attachments'], 'comments')) {
                    $comments_query = db_query(
                        "select attachments from app_comments where entities_id={$current_entity_id} and items_id={$item['id']} and length(attachments)>0"
                    );
                    while ($comments = db_fetch_array($comments_query)) {
                        foreach (explode(',', $comments['attachments']) as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => $template_folder . TEXT_COMMENTS . '/'
                            ];
                        }
                    }
                }
            }
        }

        //print_rr($attachments);

        $zip = new ZipArchive();
        $zip_filename = $app_user['id'] . '_' . $current_entity_id . "_export_docx.zip";
        $zip_filepath = DIR_FS_TMP . $zip_filename;

        //open zip archive
        $zip->open($zip_filepath, ZipArchive::CREATE);

        //add files to archive
        $check_duplicates = [];
        foreach ($attachments as $v) {
            $file = attachments::parse_filename($v['filename']);

            $name = $v['folder'] . (isset($v['use_name']) ? $v['use_name'] : $file['name']);
            $check_duplicates[] = $name;

            $count_duplicates = array_count_values($check_duplicates);
            if ($count_duplicates[$name] > 1) {
                $path_parts = pathinfo($name);
                $name = str_replace(
                    $path_parts['filename'],
                    $path_parts['filename'] . ' (' . ($count_duplicates[$name] - 1) . ')',
                    $name
                );
            }

            if (isset($v['tmp_filename'])) {
                $file['file_path'] = DIR_FS_TMP . $v['tmp_filename'];
            }

            $zip->addFile($file['file_path'], $name);
        }

        $zip->close();

        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=" . $app_entities_cache[$current_entity_id]['name'] . '.zip');

        readfile($zip_filepath);

        //remove tmp zip
        unlink($zip_filepath);

        //remove saved template 
        foreach ($template_filename_list as $filename) {
            unlink(DIR_FS_TMP . $filename);
        }
    } else {
        //whthout attachments
        $listing_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $current_entity_id,
                ''
            ) . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        $items_query = db_query($listing_sql);
        while ($item = db_fetch_array($items_query)) {
            if (strlen($template_info['template_filename'])) {
                $pattern = new fieldtype_text_pattern;
                $use_name = $pattern->output_singe_text($template_info['template_filename'], $current_entity_id, $item);
            } else {
                $use_name = $template_info['name'] . '_' . $item['id'];
            }

            $filenames[] = [
                'filename' => $docx->prepare_template_file(
                    $template_info['entities_id'],
                    $item['id'],
                    $item
                ),
                'name' => $use_name . '.docx'
            ];
        }

        $docx->dowload_archive($filenames, $_POST['filename']);
    }

    exit();
}

switch ($app_module_action) {
    case 'export_zip':

        require_once(CFG_PATH_TO_DOMPDF);

        if (!isset($app_selected_items[$_POST['reports_id']])) {
            $app_selected_items[$_POST['reports_id']] = [];
        }

        if (count($app_selected_items) == 0) {
            echo TEXT_PLEASE_SELECT_ITEMS;
            exit();
        }

        $selected_items = implode(',', $app_selected_items[$_POST['reports_id']]);

        $listing_sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $current_entity_id
            ) . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        $items_query = db_query($listing_sql);

        $attachments = [];
        $template_filename_list = [];

        while ($item = db_fetch_array($items_query)) {
            $export_templates_file = new export_templates_file($current_entity_id, $item['id']);
            $export_templates_file->filename_sufix = '(' . $current_entity_id . '-' . $item['id'] . ')';
            $template_filename = $export_templates_file->save($template_info['id'], $template_info['type']);

            $template_folder = items::get_heading_field(
                    $current_entity_id,
                    $item['id'],
                    $item
                ) . ' - ' . $item['id'] . '/';

            $use_name = str_replace($export_templates_file->filename_sufix, '', $template_filename);
            $use_name = substr($use_name, strpos($use_name, '_') + 1);

            $attachments[] = ['filename' => $template_filename, 'folder' => $template_folder, 'use_name' => $use_name];
            $template_filename_list[] = $template_filename;

            if (strlen($template_info['save_attachments'])) {
                $save_attachments = explode(',', $template_info['save_attachments']);

                foreach ($save_attachments as $id) {
                    if (isset($item['field_' . $id]) and strlen($item['field_' . $id])) {
                        foreach (explode(',', $item['field_' . $id]) as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => $template_folder . $app_fields_cache[$current_entity_id][$id]['name'] . '/'
                            ];
                        }
                    }
                }


                if (strstr($template_info['save_attachments'], 'comments')) {
                    $comments_query = db_query(
                        "select attachments from app_comments where entities_id={$current_entity_id} and items_id={$item['id']} and length(attachments)>0"
                    );
                    while ($comments = db_fetch_array($comments_query)) {
                        foreach (explode(',', $comments['attachments']) as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => $template_folder . TEXT_COMMENTS . '/'
                            ];
                        }
                    }
                }
            }
        }

        //print_rr($attachments);

        $zip = new ZipArchive();
        $zip_filename = $app_user['id'] . '_' . $current_entity_id . "_export.zip";
        $zip_filepath = DIR_FS_TMP . $zip_filename;

        //open zip archive
        $zip->open($zip_filepath, ZipArchive::CREATE);

        //add files to archive
        $check_duplicates = [];
        foreach ($attachments as $v) {
            $file = attachments::parse_filename($v['filename']);

            $name = $v['folder'] . (isset($v['use_name']) ? $v['use_name'] : $file['name']);
            $check_duplicates[] = $name;

            $count_duplicates = array_count_values($check_duplicates);
            if ($count_duplicates[$name] > 1) {
                $path_parts = pathinfo($name);
                $name = str_replace(
                    $path_parts['filename'],
                    $path_parts['filename'] . ' (' . ($count_duplicates[$name] - 1) . ')',
                    $name
                );
            }

            $zip->addFile($file['file_path'], $name);
        }

        $zip->close();

        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=" . $app_entities_cache[$current_entity_id]['name'] . '.zip');

        readfile($zip_filepath);

        //remove tmp zip
        unlink($zip_filepath);

        //remove saved template 
        foreach ($template_filename_list as $filename) {
            $file = attachments::parse_filename($filename);
            unlink($file['file_path']);
        }


        exit();
        break;
    case 'export_word':
    case 'print':

        if (!isset($app_selected_items[$_POST['reports_id']])) {
            $app_selected_items[$_POST['reports_id']] = [];
        }

        if (count($app_selected_items) == 0) {
            echo TEXT_PLEASE_SELECT_ITEMS;
            exit();
        }

        $selected_items_array = $app_selected_items[$_POST['reports_id']];

        $print_template = export_templates::get_template_extra(
            $selected_items_array,
            $template_info,
            'template_header'
        );

        $selected_items = implode(',', $app_selected_items[$_POST['reports_id']]);

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, '');

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        $items_query = db_query($listing_sql);
        $count_items = db_num_rows($items_query);
        $count = 1;

        if ($template_info['type'] == 'label') {
            $labl_size = explode('x', $template_info['label_size']);

            while ($item = db_fetch_array($items_query)) {
                $print_template .= '
                    <div style="width: ' . $labl_size[0] . 'mm; height: ' . $labl_size[1] . 'mm; float: left; margin: 5px;">' .
                    export_templates::get_html($current_entity_id, $item['id'], $_GET['templates_id']) .
                    '</div>';

                if ($count_items > 1 and $count_items != $count and $template_info['split_into_pages'] == 1) {
                    $print_template .= ($app_module_action == 'export_word' ? '<br style="clear: both; page-break-before: always">' : '<p style="clear: both; page-break-after: always;"></p>');
                }

                $count++;
            }
        } else {
            while ($item = db_fetch_array($items_query)) {
                $print_template .= export_templates::get_html($current_entity_id, $item['id'], $_GET['templates_id']);

                if ($count_items > 1 and $count_items != $count and $template_info['split_into_pages'] == 1) {
                    $print_template .= export_templates::get_template_extra(
                        $selected_items_array,
                        $template_info,
                        'template_footer'
                    );

                    $print_template .= ($app_module_action == 'export_word' ? '<br style="page-break-before: always">' : '<p style="page-break-after: always;"></p>');

                    $print_template .= export_templates::get_template_extra(
                        $selected_items_array,
                        $template_info,
                        'template_header'
                    );
                }

                $count++;
            }

            $print_template .= export_templates::get_template_extra(
                $selected_items_array,
                $template_info,
                'template_footer'
            );
        }


        $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            
            <style>               
              body { 
                  color: #000;
                  font-family: \'Open Sans\', sans-serif;
                  padding: 0px !important;
                  margin: 0px !important;                                   
               }
               
               body, table, td {
                font-size: 12px;
                font-style: normal;
               }
               
               table{
                 border-collapse: collapse;
                 border-spacing: 0px;                
               }
      		
      				' . $template_info['template_css'] . '
               
            </style>
      						
      			' . ($template_info['page_orientation'] == 'landscape' ? '<style type="text/css" media="print"> @page { size: landscape; } </style>' : '') . '			
        </head>        
        <body>
         ' . $print_template . '
         <script>
            window.print();
         </script>            
        </body>
      </html>
      ';

        if ($app_module_action == 'export_word') {
            //prepare images
            $html = str_replace('src="' . DIR_WS_UPLOADS, 'src="' . url_for_file('') . DIR_WS_UPLOADS, $html);

            $filename = str_replace(' ', '_', trim($template_info['name'])) . '.doc';

            header("Content-Type: application/vnd.ms-word");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename={$filename}");
        }

        echo $html;

        exit();

        break;
}  