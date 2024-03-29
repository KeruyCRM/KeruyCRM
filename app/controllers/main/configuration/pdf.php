<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Configuration;

class Pdf extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Configuration\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'pdf.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $font_family = strtolower(\K::model()->db_prepare_input(\K::$fw->POST['name']));

            $font_types = [];

            //upload
            if (strlen($font_normal_filename = strtolower($_FILES['file_normal']['name'])) > 0 and substr(
                    $font_normal_filename,
                    -4
                ) == '.ttf') {
                move_uploaded_file(
                    $_FILES['file_normal']['tmp_name'],
                    \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . $font_normal_filename
                );

                $font_types['normal'] = $font_normal_filename;
            }

            if (strlen($font_italic_filename = strtolower($_FILES['file_italic']['name'])) > 0 and substr(
                    $font_italic_filename,
                    -4
                ) == '.ttf') {
                move_uploaded_file(
                    $_FILES['file_italic']['tmp_name'],
                    \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . $font_italic_filename
                );

                $font_types['italic'] = $font_italic_filename;
            }

            if (strlen($font_bold_filename = strtolower($_FILES['file_bold']['name'])) > 0 and substr(
                    $font_bold_filename,
                    -4
                ) == '.ttf') {
                move_uploaded_file(
                    $_FILES['file_bold']['tmp_name'],
                    \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . $font_bold_filename
                );

                $font_types['bold'] = $font_bold_filename;
            }

            if (strlen($font_bold_italic_filename = strtolower($_FILES['file_bold_italic']['name'])) > 0 and substr(
                    $font_bold_italic_filename,
                    -4
                ) == '.ttf') {
                move_uploaded_file(
                    $_FILES['file_bold_italic']['tmp_name'],
                    \K::$fw->CFG_PATH_TO_DOMPDF_FONTS . $font_bold_italic_filename
                );

                $font_types['bold_italic'] = $font_bold_italic_filename;
            }

            $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>            
            <style>               
             
                                               
              body { 
                font-family: ' . $font_family . ', sans-serif; 
               }                                            
            </style>
        </head>        
        <body>
            <center>
                <h1>' . $font_family . '</h1>
                <h2>' . \K::$fw->CFG_APP_NAME . '</h2> 
                <span>Normal</span><br><br>    
                <b>Bold</b><br><br>    
                <i>Italic</i><br><br>    
                <b><i>Bold Italic</i></b>
            </center>
        </body>
        </html>
        ';

            require_once(\K::$fw->CFG_PATH_TO_DOMPDF);

            header('Content-Type: application/pdf');

            $dompdf = new \Dompdf\Dompdf();

            if (count($font_types)) {
                $font_metrics = [];

                foreach ($font_types as $type => $filename) {
                    $dest = $dompdf->getOptions()->get('fontDir') . '/' . $filename;
                    $entry_name = mb_substr($dest, 0, -4);

                    $font_obj = \FontLib\Font::load($dest);
                    $font_obj->saveAdobeFontMetrics($entry_name . '.ufm');
                    $font_obj->close();

                    $font_metrics[$type] = $entry_name;
                }

                $fontMetrics = $dompdf->getFontMetrics();

                // Store the fonts in the lookup table
                $fontMetrics->setFontFamily($font_family, $font_metrics);

                // Save the changes
                $fontMetrics->saveFontFamilies();
            }

            $dompdf->loadHtml($html);
            $dompdf->render();

            echo $dompdf->output();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}