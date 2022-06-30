<?php

$app_title = app_set_title(TEXT_EXT_PUBLIC_FORMS);

switch ($app_module_action) {
    case 'copy':

        $form_query = db_query("select * from app_ext_public_forms where id='" . _GET('id') . "'");
        if ($form = db_fetch_array($form_query)) {
            unset($form['id']);
            $form['name'] = $form['name'] . ' (' . TEXT_EXT_NAME_COPY . ')';
            db_perform('app_ext_public_forms', $form);
        }

        redirect_to('ext/public_forms/public_forms');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'parent_item_id' => (isset($_POST['parent_item_id']) ? $_POST['parent_item_id'] : 0),
            'is_active' => $_POST['is_active'],
            'inactive_message' => $_POST['inactive_message'],
            'hide_parent_item' => (isset($_POST['hide_parent_item']) ? 1 : 0),
            'notes' => strip_tags($_POST['notes']),
            'page_title' => $_POST['page_title'],
            'button_save_title' => $_POST['button_save_title'],
            'description' => $_POST['description'],
            'successful_sending_message' => $_POST['successful_sending_message'],
            'after_submit_action' => $_POST['after_submit_action'],
            'after_submit_redirect' => $_POST['after_submit_redirect'],
            'user_agreement' => $_POST['user_agreement'],
            'hidden_fields' => (isset($_POST['hidden_fields']) ? implode(',', $_POST['hidden_fields']) : ''),
            'customer_name' => (isset($_POST['customer_name']) ? implode(',', $_POST['customer_name']) : ''),
            'customer_email' => $_POST['customer_email'],
            'customer_message_title' => $_POST['customer_message_title'],
            'customer_message' => $_POST['customer_message'],
            'admin_name' => $_POST['admin_name'],
            'admin_email' => $_POST['admin_email'],
            'admin_notification' => (isset($_POST['admin_notification']) ? 1 : 0),
            'form_css' => $_POST['form_css'],
            'form_js' => $_POST['form_js'],
            'check_enquiry' => (isset($_POST['check_enquiry']) ? 1 : 0),
            'disable_submit_form' => (isset($_POST['disable_submit_form']) ? 1 : 0),
            'check_page_title' => $_POST['check_page_title'],
            'check_page_description' => $_POST['check_page_description'],
            'check_button_title' => $_POST['check_button_title'],
            'check_enquiry_fields' => (isset($_POST['check_enquiry_fields']) ? implode(
                ',',
                $_POST['check_enquiry_fields']
            ) : ''),
            'check_page_fields' => (isset($_POST['check_page_fields']) ? implode(
                ',',
                $_POST['check_page_fields']
            ) : ''),
            'check_page_comments' => (isset($_POST['check_page_comments']) ? 1 : 0),
            'check_page_comments_heading' => $_POST['check_page_comments_heading'],
            'check_page_comments_fields' => (isset($_POST['check_page_comments_fields']) ? implode(
                ',',
                $_POST['check_page_comments_fields']
            ) : ''),
            'notify_field_change' => $_POST['notify_field_change'],
            'notify_message_title' => $_POST['notify_message_title'],
            'notify_message_body' => $_POST['notify_message_body'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_public_forms', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_public_forms', $sql_data);
        }

        redirect_to('ext/public_forms/public_forms');
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_public_forms where id='" . db_input($_GET['id']) . "'");

            redirect_to('ext/public_forms/public_forms');
        }
        break;
    case 'get_parent_item_settings':
        $obj = db_find('app_ext_public_forms', $_GET['id']);
        $entities_id = _post::int('entities_id');

        $html = '';

        if (($parent_id = $app_entities_cache[$entities_id]['parent_id']) > 0) {
            $html = '
  					
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="hidden_fields">' . $app_entities_cache[$parent_id]['name'] . '</label>
			    <div class="col-md-9">' . select_tag(
                    'parent_item_id',
                    items::get_choices($parent_id, true, TEXT_NONE),
                    $obj['parent_item_id'],
                    ['class' => 'form-control input-xlarge chosen-select']
                ) . '
			    		<label id="hide_parent_item_label">' . input_checkbox_tag(
                    'hide_parent_item',
                    1,
                    ['checked' => $obj['hide_parent_item']]
                ) . ' ' . TEXT_HIDE_DROPDOWN . '</label>
			    </div>
			  </div>  
			    				
			  <script>
			   	$("#parent_item_id").change(function(){
			    	check_parent_item_label()			
  				})
			  </script>
  		';
        }

        echo $html;
        exit();

        break;
    case 'get_available_fields':

        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form(
            ) . ") and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['tab_name']][$fields['id']] = $fields['name'];
        }

        $obj = db_find('app_ext_public_forms', $_GET['id']);

        $html = '
  						    	
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="hidden_fields">' . tooltip_icon(
                TEXT_EXT_FB_HIDDEN_FIELDS_INFO
            ) . TEXT_EXT_FB_HIDDEN_FIELDS . '</label>
			    <div class="col-md-9">' . select_tag(
                'hidden_fields[]',
                $choices,
                $obj['hidden_fields'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
			    </div>			
			  </div>
	
			    		
			  <script>
			    appHandleChosen()
			    $(\'[data-toggle="tooltip"]\').tooltip()
			  </script>  		
  		';

        echo $html;
        exit();
        break;


    case 'get_client_fields':

        $choices_input = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type='fieldtype_input' and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices_input[$fields['id']] = $fields['name'];
        }

        $choices_email = [];
        $choices_email[''] = '';
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type='fieldtype_input_email' and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices_email[$fields['id']] = $fields['name'];
        }

        $obj = db_find('app_ext_public_forms', $_GET['id']);

        $html = '
  					
			
			  <h3 class="form-section form-section-desc">' . TEXT_EXT_PB_CUSTOMER_NOTIFICATION . '</h3>
			
			  <p>' . TEXT_EXT_PB_CUSTOMER_NOTIFICATION_INFO . '</p>
			
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="customer_name">' . tooltip_icon(
                TEXT_EXT_PB_CUSTOMER_NAME_INFO
            ) . TEXT_NAME . '</label>
			    <div class="col-md-9">' . select_tag(
                'customer_name[]',
                $choices_input,
                $obj['customer_name'],
                ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
            ) . '
			    </div>
			  </div>
			
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="customer_email">' . tooltip_icon(
                TEXT_EXT_PB_CUSTOMER_EMAIL_INFO
            ) . TEXT_EMAIL . '</label>
			    <div class="col-md-9">' . select_tag(
                'customer_email',
                $choices_email,
                $obj['customer_email'],
                ['class' => 'form-control input-large']
            ) . '
			    </div>
			  </div>
			  
			  <script>
			    appHandleChosen()
			    $(\'[data-toggle="tooltip"]\').tooltip()
			  </script>
  		';

        echo $html;
        exit();
        break;

    case 'get_check_page_available_fields':


        $choices_check = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type  in ('fieldtype_id','fieldtype_date_added','fieldtype_input_email','fieldtype_random_value','fieldtype_phone','fieldtype_auto_increment','fieldtype_barcode','fieldtype_input_protected','fieldtype_input','fieldtype_input_vpic','fieldtype_text_pattern_static') and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices_check[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_section') and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['tab_name']][$fields['id']] = fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            );
        }


        $obj = db_find('app_ext_public_forms', $_GET['id']);

        $html = '
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="check_enquiry_fields">' . tooltip_icon(
                TEXT_EXT_FB_CHECK_FIELDS_INFO
            ) . TEXT_EXT_FB_CHECK_FIELDS . '</label>
			    <div class="col-md-9">' . select_tag(
                'check_enquiry_fields[]',
                $choices_check,
                $obj['check_enquiry_fields'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
			    ' . tooltip_text(
                TEXT_AVAILABLE_FIELDS . ': ' . TEXT_FIELDTYPE_ID_TITLE . ', ' . TEXT_FIELDTYPE_DATEADDED_TITLE . ', ' . TEXT_FIELDTYPE_INPUT_EMAIL_TITLE . ', ' . TEXT_FIELDTYPE_RANDOM_VALUE
            ) . '
			    </div>			
			  </div>	
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="check_page_fields">' . tooltip_icon(
                TEXT_EXT_FB_CHECK_PAGE_FIELDS_INFO
            ) . TEXT_EXT_FB_CHECK_PAGE_FIELDS . '</label>
			    <div class="col-md-9">' . select_tag(
                'check_page_fields[]',
                $choices,
                $obj['check_page_fields'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
			    </div>
			  </div>
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="check_page_comments">' . TEXT_EXT_PB_CHECK_PAGE_COMMENTS . '</label>
			    <div class="col-md-9">	
			  	  <div class="form-control-static">' . input_checkbox_tag(
                'check_page_comments',
                1,
                ['checked' => $obj['check_page_comments']]
            ) . '</div>
			    </div>			
			  </div>
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="check_page_comments_heading">' . TEXT_EXT_PB_CHECK_PAGE_COMMENTS_HEADING . '</label>
			    <div class="col-md-9">' . input_tag(
                'check_page_comments_heading',
                $obj['check_page_comments_heading'],
                ['class' => 'form-control input-large']
            ) . '
			    </div>			
			  </div>';


        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.comments_status=1 and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['tab_name']][$fields['id']] = fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            );
        }

        $html .= '
			  <div class="form-group">
			  	<label class="col-md-3 control-label" for="check_page_fields">' . tooltip_icon(
                TEXT_EXT_PB_CHECK_PAGE_COMMENTS_FIELDS_INFO
            ) . TEXT_EXT_PB_CHECK_PAGE_COMMENTS_FIELDS . '</label>
			    <div class="col-md-9">' . select_tag(
                'check_page_comments_fields[]',
                $choices,
                $obj['check_page_comments_fields'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
			    </div>
			  </div>';

        $choices = [];
        $choices[''] = '';
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_dropdown','fieldtype_radioboxes') and f.entities_id='" . _post::int(
                'entities_id'
            ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['tab_name']][$fields['id']] = fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            );
        }

        $html .= '
			 <div class="form-group">
			  	<label class="col-md-3 control-label" for="notify_field_change">' . tooltip_icon(
                TEXT_EXT_PB_NOTIFY_FIELD_CHANGE_INFO
            ) . TEXT_EXT_PB_NOTIFY_FIELD_CHANGE . '</label>
			    <div class="col-md-9">' . select_tag(
                'notify_field_change',
                $choices,
                $obj['notify_field_change'],
                ['class' => 'form-control input-large ']
            ) . '			
			  </div>
				
			  
			  <script>
			    appHandleChosen()
			  	appHandleUniformCheckbox();
			    $(\'[data-toggle="tooltip"]\').tooltip()
			  </script>
  		';

        echo $html;
        exit();
        break;
}