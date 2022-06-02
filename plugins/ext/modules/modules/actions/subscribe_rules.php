<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'modules_id' => $_POST['modules_id'],
            'entities_id' => $_POST['entities_id'],
            'contact_list_id' => $_POST['contact_list_id'],
            'contact_email_field_id' => $_POST['contact_email_field_id'],
            'contact_fields' => $_POST['contact_fields'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_subscribe_rules', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_subscribe_rules', $sql_data);
        }

        redirect_to('ext/modules/subscribe_rules');

        break;
    case 'delete':

        if (isset($_GET['id'])) {
            db_delete_row('app_ext_subscribe_rules', $_GET['id']);
        }

        redirect_to('ext/modules/subscribe_rules');
        break;


    case 'get_list_of_contacts':
        $modules_id = _post::int('modules_id');

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_subscribe_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_subscribe_rules');
        }

        $html = '';

        $choices = [];

        if ($modules_id > 0) {
            $modules = new modules('mailing');

            $modules_info_query = db_query("select * from app_ext_modules where id='" . $modules_id . "'");
            if ($modules_info = db_fetch_array($modules_info_query)) {
                $module = new $modules_info['module'];
                $choices = $module->get_list_id_choices($modules_info['id']);
            }
        }

        $html = '
          <div class="form-group">
          <label class="col-md-3 control-label" for="type">' . TEXT_EXT_LIST_OF_CONTACTS . '</label>
                <div class="col-md-9">
              	  
                  <div class="input-group input-xlarge">
                    ' . select_tag(
                'contact_list_id_choices',
                ['' => ''] + $choices,
                $obj['contact_list_id'],
                ['class' => 'form-control input-medium required']
            ) . '
                    <span class="input-group-btn">
                    ' . input_tag(
                'contact_list_id',
                $obj['contact_list_id'],
                ['class' => 'form-control input-medium', 'placeholder' => TEXT_ID]
            ) . '
                    </span>
                  </div>      
                      
              	  ' . tooltip_text(TEXT_EXT_LIST_OF_CONTACTS_INFO) . '
                </div>
              </div>
              <script>
              $("#contact_list_id_choices").change(function(){
                $("#contact_list_id").val($(this).val())
              })
              </script>
              ';

        echo $html;

        exit();
        break;
    case 'get_entities_fields':

        $entities_id = _post::int('entities_id');

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_subscribe_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_subscribe_rules');
        }


        $choices = [];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_user_email','fieldtype_input_email','fieldtype_mysql_query','fieldtype_php_code') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $html = '
        			<div class="form-group">
						  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_FIELD . '</label>
						    <div class="col-md-9">	
						  	  ' . select_tag(
                'contact_email_field_id',
                $choices,
                $obj['contact_email_field_id'],
                ['class' => 'form-control input-large required']
            ) . '
						  	  ' . tooltip_text(TEXT_EXT_SELECT_EMAIL_FIELD) . '		
						    </div>			
						  </div> 		
        			';

        echo $html;

        exit();
        break;
}