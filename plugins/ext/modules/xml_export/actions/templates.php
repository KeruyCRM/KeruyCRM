<?php

if (!app_session_is_registered('xml_templates_filter')) {
    $xml_templates_filter = 0;
    app_session_register('xml_templates_filter');
}

switch ($app_module_action) {
    case 'set_xml_templates_filter':
        $xml_templates_filter = $_POST['xml_templates_filter'];

        redirect_to('ext/xml_export/templates');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'template_filename' => $_POST['template_filename'],
            'transliterate_filename' => (isset($_POST['transliterate_filename']) ? 1 : 0),
            'entities_id' => $_POST['entities_id'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'template_header' => $_POST['template_header'],
            'template_body' => $_POST['template_body'],
            'template_footer' => $_POST['template_footer'],
            'is_public' => $_POST['is_public'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_xml_export_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_xml_export_templates', $sql_data);
        }

        redirect_to('ext/xml_export/templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_xml_export_templates where id='" . db_input($_GET['id']) . "'");

            $report_info_query = db_query(
                "select * from app_reports where reports_type='xml_export" . db_input($_GET['id']) . "'"
            );
            if ($report_info = db_fetch_array($report_info_query)) {
                reports::delete_reports_by_id($report_info['id']);
            }

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/xml_export/templates');
        }
        break;
    case 'get_fields':

        $obj = db_find('app_ext_xml_export_templates', $_POST['id']);

        $html = '
				<div class="form-group">
			  	<label class="col-md-3 control-label" for="users_groups">' . TEXT_START . '</label>
			    <div class="col-md-9">	
			  	  ' . textarea_tag(
                'template_header',
                $obj['template_header'],
                ['class' => 'form-control textarea-small', 'style' => 'font-size:13px;']
            ) . '
			  	  ' . tooltip_text(TEXT_EXT_XML_EXPORT_START_TIP) . '
			    </div>			
			  </div> 
			  
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="users_groups">' . TEXT_BODY . fields::get_available_fields_helper(
                $_POST['entities_id'],
                'template_body'
            ) . '</label>
			    <div class="col-md-9">	
			  	  ' . textarea_tag(
                'template_body',
                $obj['template_body'],
                ['class' => 'form-control', 'style' => 'min-height: 260px; font-size:13px;']
            ) . '
			  	  ' . tooltip_text(
                TEXT_EXT_PREPARE_TEMPLATE_FOR_SINGLE_ITEM . '<br>' . TEXT_ENTER_TEXT_PATTERN_INFO_SHORT
            ) . '
			    </div>			
			  </div>
			  
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="users_groups">' . TEXT_END . '</label>
			    <div class="col-md-9">	
			  	  ' . textarea_tag(
                'template_footer',
                $obj['template_footer'],
                ['class' => 'form-control textarea-small', 'style' => 'font-size:13px;']
            ) . '      
			    </div>			
			  </div>
			  	  		
			  <p>' . TEXT_EXT_XML_EXPORT_BODY_TIP . '</p>
				';

        echo $html;

        exit();
        break;
}