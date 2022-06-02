<?php

$template_info_query = db_query(
    "select ep.*, e.name as entities_name from app_ext_export_templates ep, app_entities e where e.id=ep.entities_id and ep.id='" . db_input(
        $_GET['id']
    ) . "' order by e.id, ep.sort_order, ep.name"
);
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('ext/templates/export_templates');
}

switch ($app_module_action) {
    case 'get_css':

        $label_size = (strlen($template_info['label_size']) ? explode('x', $template_info['label_size']) : [57, 40]);

        $css = '
            html{
                    background-color: #eeeeee;
            }

            body.cke_editable {
                width: ' . $label_size[0] . 'mm;
                height: ' . $label_size[1] . 'mm;
                overflow:hidden;
                padding: 5px;
                margin: 0.5cm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                margin-top: 30px;
                margin-bottom: 30px;
                font-size: 14px;    
                font-family:  Arial, Verdana, Helvetica, sans-serif;
            }
            ';

        header("Content-type: text/css");
        echo $css;

        exit();
        break;
}