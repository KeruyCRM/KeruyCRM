<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Уxport_template extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!export_templates::has_users_access($current_entity_id, _get::int('templates_id'))) {
            redirect_to('dashboard/access_forbidden');
        }

        $template_info_query = db_query("select * from app_ext_export_templates where id=" . _GET('templates_id'));
        if (!$template_info = db_fetch_array($template_info_query)) {
            redirect_to('dashboard/page_not_found');
        }

        //download docx
        if ($template_info['type'] == 'docx' and in_array($app_module_action, ['export', 'export_pdf', 'print'])) {
            require_once(CFG_PATH_TO_DOMPDF);

            require_once(CFG_PATH_TO_PHPWORD);

            $docx = new export_templates_blocks($template_info);
            $filename = $docx->prepare_template_file($current_entity_id, $current_item_id);

            switch ($app_module_action) {
                case 'print':
                    $docx->print_html($filename);
                    break;
                case 'export_pdf':
                    $docx->download_pdf($filename);
                    break;
                case 'export':
                    $docx->download($filename);
                    break;
            }

            exit();
        }

        //hande current dates
        $template_info['template_header'] = str_replace(
            '{#current_date}',
            format_date(time()),
            $template_info['template_header']
        );
        $template_info['template_header'] = str_replace(
            '{#current_date_time}',
            format_date_time(time()),
            $template_info['template_header']
        );
        $template_info['template_footer'] = str_replace(
            '{#current_date}',
            format_date(time()),
            $template_info['template_footer']
        );
        $template_info['template_footer'] = str_replace(
            '{#current_date_time}',
            format_date_time(time()),
            $template_info['template_footer']
        );
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'export_template.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function print()
    {
        $export_template = $template_info['template_header'] . export_templates::get_html(
                $current_entity_id,
                $current_item_id,
                $_GET['templates_id']
            ) . $template_info['template_footer'];

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
         ' . $export_template . '
         <script>
            window.print();
         </script>            
        </body>
      </html>
      ';

        echo $html;
    }

    public function export()
    {
        $export_template = $template_info['template_header'] . export_templates::get_html(
                $current_entity_id,
                $current_item_id,
                $_GET['templates_id']
            ) . $template_info['template_footer'];

        $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            
            <style>               
              body { 
                font-family:   DejaVu Sans, sans-serif;                 
               }
               
              body, table, td {
                font-size: 12px;
                font-style: normal;
              }
              
              table{
                border-collapse: collapse;
                border-spacing: 0px;                
              }
                                          
              c{
                font-family: STXihei;
                font-style: normal;
                font-weight: 400;
              }
      		
      				' . $template_info['template_css'] . '
            </style>
        </head>        
        <body>
         ' . $export_template . '            
        </body>
      </html>
      ';

        //Handle Chinese & Japanese symbols
        $html = preg_replace('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', '<c>${0}</c>', $html);
        $html = str_replace('。', '.', $html);

        //Handle Korean symbols
        $html = preg_replace('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', '<c>${0}</c>', $html);

        //echo $html;
        //exit();

        $filename = str_replace(' ', '_', trim($_POST['filename']));

        require_once(CFG_PATH_TO_DOMPDF);

        $dompdf = new Dompdf\Dompdf();

        if ($template_info['page_orientation'] == 'landscape') {
            $dompdf->set_paper('letter', 'landscape');
        }

        $dompdf->load_html($html);
        $dompdf->render();

        //$dompdf->stream($filename);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename . '.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        flush();

        echo $dompdf->output();
    }

    public function export_word()
    {
        $export_template = $template_info['template_header'] . export_templates::get_html(
                $current_entity_id,
                $current_item_id,
                $_GET['templates_id']
            ) . $template_info['template_footer'];

        $html = '<html>
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
    							
    					' . ($template_info['page_orientation'] == 'landscape' ? '
    							@page section{ size:841.7pt 595.45pt;mso-page-orientation:landscape;margin:1.25in 1.0in 1.25in 1.0in;mso-header-margin:.5in;mso-footer-margin:.5in;mso-paper-source:0; }
    							div.section {page:section;}
    							' : '') . '
        
            </style>
        </head>
        <body>    							
         <div class="section">' . $export_template . '</div>         
        </body>
      </html>
      ';

        //prepare images
        $html = str_replace('src="' . DIR_WS_UPLOADS, 'src="' . url_for_file('') . DIR_WS_UPLOADS, $html);

        $filename = str_replace(' ', '_', trim($_POST['filename'])) . '.doc';

        header("Content-Type: application/vnd.ms-word");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename={$filename}");

        echo $html;
    }

    public function export_zip()
    {
        require_once(CFG_PATH_TO_DOMPDF);

        require_once(CFG_PATH_TO_PHPWORD);

        $attachments = [];
        $export_templates_file = new export_templates_file($current_entity_id, $current_item_id);
        $template_filename = $export_templates_file->save($template_info['id'], $template_info['type']);

        $attachments[] = ['filename' => $template_filename, 'folder' => ''];

        if (strlen($template_info['save_attachments'])) {
            $save_attachments = explode(',', $template_info['save_attachments']);
            $item_query = db_query("select * from app_entity_{$current_entity_id} where id={$current_item_id}");
            if ($item = db_fetch_array($item_query)) {
                foreach ($save_attachments as $id) {
                    if (isset($item['field_' . $id]) and strlen($item['field_' . $id])) {
                        foreach (explode(',', $item['field_' . $id]) as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => $app_fields_cache[$current_entity_id][$id]['name'] . '/'
                            ];
                        }
                    }
                }
            }

            if (strstr($template_info['save_attachments'], 'comments')) {
                $comments_query = db_query(
                    "select attachments from app_comments where entities_id={$current_entity_id} and items_id={$current_item_id} and length(attachments)>0"
                );
                while ($comments = db_fetch_array($comments_query)) {
                    foreach (explode(',', $comments['attachments']) as $filename) {
                        $attachments[] = ['filename' => $filename, 'folder' => TEXT_COMMENTS . '/'];
                    }
                }
            }
        }

        //print_rr($attachments);

        $zip = new ZipArchive();
        $zip_filename = $app_user['id'] . '_' . $template_filename . ".zip";
        $zip_filepath = DIR_FS_TMP . $zip_filename;

        //open zip archive
        $zip->open($zip_filepath, ZipArchive::CREATE);

        //add files to archive
        $check_duplicates = [];
        foreach ($attachments as $v) {
            $file = attachments::parse_filename($v['filename']);

            $name = $v['folder'] . $file['name'];
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

        $file = attachments::parse_filename($template_filename);

        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=" . $file['name'] . '.zip');

        readfile($zip_filepath);

        //remove tmp zip
        unlink($zip_filepath);

        //remove saved template
        unlink($file['file_path']);
    }
}