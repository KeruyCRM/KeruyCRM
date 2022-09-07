<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Export_template extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!\Models\Ext\Templates\Export_templates::has_users_access(
            \K::$fw->current_entity_id,
            \K::$fw->GET['templates_id']
        )) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        // $template_info_query = db_query("select * from app_ext_export_templates where id=" . _GET('templates_id'));

        \K::$fw->template_info = \K::model()->db_fetch_one('app_ext_export_templates', [
            'id = ?',
            \K::$fw->GET['templates_id']
        ]);

        if (!\K::$fw->template_info) {
            \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
        }

        //download docx
        if (\K::$fw->template_info['type'] == 'docx' and in_array(
                \K::$fw->app_module_action,
                ['export', 'export_pdf', 'print']
            )) {
            require_once(\K::$fw->CFG_PATH_TO_DOMPDF);

            require_once(\K::$fw->CFG_PATH_TO_PHPWORD);

            $docx = new \Models\Ext\Templates\Export_templates_blocks(\K::$fw->template_info);
            $filename = $docx->prepare_template_file(\K::$fw->current_entity_id, \K::$fw->current_item_id);

            switch (\K::$fw->app_module_action) {
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
        } else {
            //hande current dates
            \K::$fw->template_info['template_header'] = str_replace(
                '{#current_date}',
                \Helpers\App::format_date(time()),
                \K::$fw->template_info['template_header']
            );
            \K::$fw->template_info['template_header'] = str_replace(
                '{#current_date_time}',
                \Helpers\App::format_date_time(time()),
                \K::$fw->template_info['template_header']
            );
            \K::$fw->template_info['template_footer'] = str_replace(
                '{#current_date}',
                \Helpers\App::format_date(time()),
                \K::$fw->template_info['template_footer']
            );
            \K::$fw->template_info['template_footer'] = str_replace(
                '{#current_date_time}',
                \Helpers\App::format_date_time(time()),
                \K::$fw->template_info['template_footer']
            );
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'export_template.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function print()
    {
        $export_template = \K::$fw->template_info['template_header'] . \Models\Ext\Templates\Export_templates::get_html(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id,
                \K::$fw->GET['templates_id']
            ) . \K::$fw->template_info['template_footer'];

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
      		
      				' . \K::$fw->template_info['template_css'] . '	
               
            </style>
      						
						' . (\K::$fw->template_info['page_orientation'] == 'landscape' ? '<style type="text/css" media="print"> @page { size: landscape; } </style>' : '') . '      						
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
        $export_template = \K::$fw->template_info['template_header'] . \Models\Ext\Templates\Export_templates::get_html(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id,
                \K::$fw->GET['templates_id']
            ) . \K::$fw->template_info['template_footer'];

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
      		
      				' . \K::$fw->template_info['template_css'] . '
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

        $filename = str_replace(' ', '_', trim($_POST['filename']));

        require_once(\K::$fw->CFG_PATH_TO_DOMPDF);

        $dompdf = new \Dompdf\Dompdf();

        if (\K::$fw->template_info['page_orientation'] == 'landscape') {
            $dompdf->set_paper('letter', 'landscape');
        }

        $dompdf->load_html($html);
        $dompdf->render();

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
        $export_template = \K::$fw->template_info['template_header'] . \Models\Ext\Templates\Export_templates::get_html(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id,
                \K::$fw->GET['templates_id']
            ) . \K::$fw->template_info['template_footer'];

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
    			
    					' . \K::$fw->template_info['template_css'] . '
    							
    					' . (\K::$fw->template_info['page_orientation'] == 'landscape' ? '
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
        $html = str_replace(
            'src="' . \K::$fw->DIR_WS_UPLOADS,
            'src="' . \Helpers\Urls::url_for_file('') . \K::$fw->DIR_WS_UPLOADS,
            $html
        );

        $filename = str_replace(' ', '_', trim($_POST['filename'])) . '.doc';

        header("Content-Type: application/vnd.ms-word");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename={$filename}");

        echo $html;
    }

    public function export_zip()
    {
        require_once(\K::$fw->CFG_PATH_TO_DOMPDF);
        require_once(\K::$fw->CFG_PATH_TO_PHPWORD);

        $attachments = [];
        $export_templates_file = new \Models\Ext\Templates\Export_templates_file(
            \K::$fw->current_entity_id,
            \K::$fw->current_item_id
        );
        $template_filename = $export_templates_file->save(\K::$fw->template_info['id'], \K::$fw->template_info['type']);

        $attachments[] = ['filename' => $template_filename, 'folder' => ''];

        if (strlen(\K::$fw->template_info['save_attachments'])) {
            $save_attachments = explode(',', \K::$fw->template_info['save_attachments']);
            /*$item_query = db_query(
                "select * from app_entity_{\K::$fw->current_entity_id} where id={\K::$fw->current_item_id}"
            );*/

            $item = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                'id = ?',
                \K::$fw->current_item_id
            ]);

            if ($item) {
                foreach ($save_attachments as $id) {
                    if (isset($item['field_' . $id]) and strlen($item['field_' . $id])) {
                        $exp = explode(',', $item['field_' . $id]);

                        foreach ($exp as $filename) {
                            $attachments[] = [
                                'filename' => $filename,
                                'folder' => \K::$fw->app_fields_cache[\K::$fw->current_entity_id][$id]['name'] . '/'
                            ];
                        }
                    }
                }
            }

            if (strstr(\K::$fw->template_info['save_attachments'], 'comments')) {
                /*$comments_query = db_query(
                    "select attachments from app_comments where entities_id={\K::$fw->current_entity_id} and items_id={\K::$fw->current_item_id} and length(attachments)>0"
                );*/

                $comments_query = \K::model()->db_fetch('app_comments', [
                    'entities_id = ? and items_id = ? and length(attachments) > 0',
                    \K::$fw->current_entity_id,
                    \K::$fw->current_item_id
                ], [], 'attachments');

                //while ($comments = db_fetch_array($comments_query)) {
                foreach ($comments_query as $comments) {
                    $comments = $comments->cast();

                    foreach (explode(',', $comments['attachments']) as $filename) {
                        $attachments[] = ['filename' => $filename, 'folder' => \K::$fw->TEXT_COMMENTS . '/'];
                    }
                }
            }
        }

        $zip = new \ZipArchive();
        $zip_filename = \K::$fw->app_user['id'] . '_' . $template_filename . ".zip";
        $zip_filepath = \K::$fw->DIR_FS_TMP . $zip_filename;

        //open zip archive
        $zip->open($zip_filepath, \ZipArchive::CREATE);

        //add files to archive
        $check_duplicates = [];
        foreach ($attachments as $v) {
            $file = \Tools\Attachments::parse_filename($v['filename']);

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

        $file = \Tools\Attachments::parse_filename($template_filename);

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