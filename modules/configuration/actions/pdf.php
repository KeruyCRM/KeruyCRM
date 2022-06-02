<?php

switch ($app_module_action) {
    case 'save':

        $font_family = strtolower(db_prepare_input($_POST['name']));

        $font_types = [];

        //upload 
        if (strlen($font_normal_filename = strtolower($_FILES['file_normal']['name'])) > 0 and substr(
                $font_normal_filename,
                -4
            ) == '.ttf') {
            move_uploaded_file($_FILES['file_normal']['tmp_name'], CFG_PATH_TO_DOMPDF_FONTS . $font_normal_filename);

            $font_types['normal'] = $font_normal_filename;
        }

        if (strlen($font_italic_filename = strtolower($_FILES['file_italic']['name'])) > 0 and substr(
                $font_italic_filename,
                -4
            ) == '.ttf') {
            move_uploaded_file($_FILES['file_italic']['tmp_name'], CFG_PATH_TO_DOMPDF_FONTS . $font_italic_filename);

            $font_types['italic'] = $font_italic_filename;
        }

        if (strlen($font_bold_filename = strtolower($_FILES['file_bold']['name'])) > 0 and substr(
                $font_bold_filename,
                -4
            ) == '.ttf') {
            move_uploaded_file($_FILES['file_bold']['tmp_name'], CFG_PATH_TO_DOMPDF_FONTS . $font_bold_filename);

            $font_types['bold'] = $font_bold_filename;
        }

        if (strlen($font_bold_italic_filename = strtolower($_FILES['file_bold_italic']['name'])) > 0 and substr(
                $font_bold_italic_filename,
                -4
            ) == '.ttf') {
            move_uploaded_file(
                $_FILES['file_bold_italic']['tmp_name'],
                CFG_PATH_TO_DOMPDF_FONTS . $font_bold_italic_filename
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
                <h2>' . CFG_APP_NAME . '</h2> 
                <span>Normal</span><br><br>    
                <b>Bold</b><br><br>    
                <i>Italic</i><br><br>    
                <b><i>Bold Italic</i></b>
            </center>
        </body>
        </html>
        ';

        //echo $html;
        //exit();

        require_once(CFG_PATH_TO_DOMPDF);

        header('Content-Type: application/pdf');

        $dompdf = new Dompdf\Dompdf();

        //print_rr($font_types);
        //exit();

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


        $dompdf->load_html($html);
        $dompdf->render();

        echo $dompdf->output();


        exit();
        break;
}