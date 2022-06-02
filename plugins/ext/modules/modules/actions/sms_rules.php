<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'modules_id' => $_POST['modules_id'],
            'entities_id' => $_POST['entities_id'],
            'action_type' => $_POST['action_type'],
            'phone' => (isset($_POST['phone']) ? $_POST['phone'] : ''),
            'fields_id' => (isset($_POST['fields_id']) ? $_POST['fields_id'] : 0),
            'description' => $_POST['description'],
            'monitor_fields_id' => (isset($_POST['monitor_fields_id']) ? $_POST['monitor_fields_id'] : 0),
            'monitor_choices' => (isset($_POST['monitor_choices']) ? implode(',', $_POST['monitor_choices']) : ''),
            'send_to_assigned_users' => (isset($_POST['send_to_assigned_users']) ? implode(
                ',',
                $_POST['send_to_assigned_users']
            ) : ''),
            'date_fields_id' => $_POST['date_fields_id'] ?? 0,
            'date_type' => $_POST['date_type'] ?? '',
        ];

        if (isset($_POST['date_type'])) {
            $sql_data['number_of_days'] = $_POST['date_type'] == 'day' ? $_POST['number_of_days'] : $_POST['number_of_hours'];
        }

        if (isset($_GET['id'])) {
            db_perform('app_ext_sms_rules', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_sms_rules', $sql_data);
        }

        redirect_to('ext/modules/sms_rules');

        break;
    case 'delete':

        if (isset($_GET['id'])) {
            db_delete_row('app_ext_sms_rules', $_GET['id']);
        }

        redirect_to('ext/modules/sms_rules');
        break;
    case 'get_monitor_choices':
        $entities_id = _post::int('entities_id');
        $fields_id = _post::int('fields_id');

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_sms_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_sms_rules');
        }


        $fields_query = db_query("select * from app_fields where id='" . $fields_id . "'");
        if ($fields = db_fetch_array($fields_query)) {
            $choices = [];

            $cfg = new fields_types_cfg($fields['configuration']);

            if ($cfg->get('use_global_list') > 0) {
                $choices = global_lists::get_choices($cfg->get('use_global_list'), false);
            } else {
                $choices = fields_choices::get_choices($fields['id'], false);
            }

            $title = tooltip_icon(
                    TEXT_EXT_NOTIFY_WHEN_FIELD_VALUE_CHANGES_INFO
                ) . TEXT_EXT_NOTIFY_WHEN_FIELD_VALUE_CHANGES;
            if (!in_array(
                $_POST['action_type'],
                ['edit_send_to_number', 'edit_send_to_record_number', 'edit_send_to_user_number']
            )) {
                $title = TEXT_SELECT_SOME_VALUES;
            }

            $html = '
        			<div class="form-group">
						  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . $title . '</label>
						    <div class="col-md-9">
						  	  ' . select_tag(
                    'monitor_choices[]',
                    $choices,
                    $obj['monitor_choices'],
                    ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
                ) . '
						    </div>
						  </div>';

            echo $html;
        }
        exit();
        break;
    case 'get_entities_fields':

        $entities_id = _post::int('entities_id');

        $obj = [];

        if (isset($_POST['id']) and $_POST['id'] > 0) {
            $obj = db_find('app_ext_sms_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_sms_rules');
            $obj['number_of_days'] = 0;
        }


        $html = '';

        //send by date
        if (strstr($_POST['action_type'], 'schedule')) {
            $choices = ['' => ''];
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_jalali_calendar','fieldtype_dynamic_date') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $choices[$fields['id']] = $fields['name'];
            }

            $html .= '
            <div class="form-group" >
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">' . TEXT_EXT_SEND_BY_DATE_CRON . ' <code>' . DIR_FS_CATALOG . 'cron/sms_by_date.php</code><br>
                    ' . TEXT_EXT_ON_EACH_HOUR . '  <code>' . DIR_FS_CATALOG . 'cron/sms_by_hour.php</code>
                </div>
            </div>    
            <div class="form-group" >
                <label class="col-md-3 control-label" for="date_fields_id">' . TEXT_EXT_DATE_FIELD . '</label>
                <div class="col-md-9">	
                      ' . select_tag(
                    'date_fields_id',
                    $choices,
                    $obj['date_fields_id'],
                    ['class' => 'form-control input-large required']
                ) . '						  	  	
                      ' . tooltip_text(TEXT_EXT_DATE_FIELD_SEND_RULE_INFO) . '
                </div>			
            </div>
            
            <div class="form-group" >
                <label class="col-md-3 control-label" for="date_fields_id">' . TEXT_TYPE . '</label>
                <div class="col-md-9">
                    ' . select_tag(
                    'date_type',
                    ['day' => TEXT_DAY, 'hour' => TEXT_EXT_HOUR],
                    $obj['date_type'],
                    ['class' => 'form-control input-medium']
                ) . '
                </div>
            </div>    
            
            <div class="form-group" form_display_rules="date_type:day">
                <label class="col-md-3 control-label" for="date_fields_id"">' . TEXT_EXT_NUMBER_OF_DAYS . '</label>
                <div class="col-md-9">	
                      ' . input_tag(
                    'number_of_days',
                    $obj['number_of_days'],
                    ['class' => 'form-control input-medium required']
                ) . '						  	  	
                      ' . tooltip_text(TEXT_EXT_NUMBER_OF_DAYS_SEND_RULE_INFO) . '
                </div>			
            </div>
            
            <div class="form-group" form_display_rules="date_type:hour">
                <label class="col-md-3 control-label" for="date_fields_id"">' . TEXT_EXT_NUMBER_OF_HOURS . '</label>
                <div class="col-md-9">	
                      ' . input_tag(
                    'number_of_hours',
                    $obj['number_of_days'],
                    ['class' => 'form-control input-medium required']
                ) . '						  	  	
                      ' . tooltip_text(TEXT_EXT_NUMBER_OF_HOURS_SEND_RULE_INFO) . '
                </div>			
            </div>
                                         
            ';
        }


        $choices = ['' => ''];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_users','fieldtype_users_ajax','fieldtype_stages','fieldtype_autostatus') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $title = TEXT_FIELD;
        $tooltip = TEXT_EXT_PB_NOTIFY_FIELD_INSERT;
        $is_required = false;

        if (in_array(
            $_POST['action_type'],
            ['edit_send_to_number', 'edit_send_to_record_number', 'edit_send_to_user_number']
        )) {
            $title = TEXT_EXT_PB_NOTIFY_FIELD_CHANGE;
            $tooltip = '';
            $is_required = true;
        }

        $html .= '
        			<div class="form-group" style="margin-top: 30px;">
						  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . $title . '</label>
						    <div class="col-md-9">	
						  	  ' . select_tag(
                'monitor_fields_id',
                $choices,
                $obj['monitor_fields_id'],
                [
                    'class' => 'form-control input-large ' . ($is_required ? 'required' : ''),
                    'onChange' => 'get_monitor_choices()'
                ]
            ) . '
						  	  ' . tooltip_text($tooltip) . '		
						    </div>			
						  </div>
						  
						  <div id="monitor_choices_row"></div>
						  
						  <script> get_monitor_choices(); </script>	  		
        			';


        switch ($_POST['action_type']) {
            case 'edit_send_to_number':
            case 'insert_send_to_number':
            case 'schedule_send_to_number':
                $html .= '
        				<div class="form-group">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_number">' . TEXT_EXT_SEND_TO_NUMBER . '</label>
							    <div class="col-md-9">	
							  	  ' . input_tag(
                        'phone',
                        $obj['phone'],
                        ['class' => 'form-control input-large required']
                    ) . '
							  	  ' . tooltip_text(TEXT_EXT_SEND_TO_NUMBER_INFO) . '
							    </div>			
							  </div>
        				';
                break;
            case 'edit_send_to_record_number':
            case 'insert_send_to_record_number':
            case 'schedule_send_to_record_number':

                $choices = ['' => ''];
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_input','fieldtype_input_masked','fieldtype_parent_value','fieldtype_phone', 'fieldtype_mysql_query','fieldtype_formula') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    //check parent value
                    if ($fields['type'] == 'fieldtype_parent_value') {
                        $entities_info = db_find('app_entities', $entities_id);
                        $cfg = new fields_types_cfg($fields['configuration']);
                        if (isset($app_fields_cache[$entities_info['parent_id']][$cfg->get('field_id')])) {
                            if (!in_array(
                                $app_fields_cache[$entities_info['parent_id']][$cfg->get('field_id')]['type'],
                                ['fieldtype_input', 'fieldtype_input_masked', 'fieldtype_phone']
                            )) {
                                continue;
                            }
                        }
                    }

                    $choices[$fields['id']] = $fields['name'];
                }

                $html .= '
        				<div class="form-group">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_EXT_SEND_TO_RECORD_NUMBER . '</label>
							    <div class="col-md-9">	
							  	  ' . select_tag(
                        'fields_id',
                        $choices,
                        $obj['fields_id'],
                        ['class' => 'form-control input-large required']
                    ) . '
							  	  ' . tooltip_text(TEXT_EXT_SEND_TO_RECORD_NUMBER_INFO) . '
							    </div>			
							  </div>        				
        				';
                break;
            case 'edit_send_to_user_number':
            case 'insert_send_to_user_number':
            case 'schedule_send_to_user_number':


                $choices = ['' => ''];
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_users_approve','fieldtype_user_roles','fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_created_by') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$app_entities_cache[$entities_id]['name']][$fields['id']] = fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }


                $html .= '
        				<div class="form-group">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_EXT_SEND_TO_ASSIGNED_USERS . '</label>
							    <div class="col-md-9">	
							  	  ' . select_tag(
                        'send_to_assigned_users[]',
                        $choices,
                        $obj['send_to_assigned_users'],
                        ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                    ) . '
							  	  ' . tooltip_text(TEXT_AVAILABLE_FIELDS . ': ' . TEXT_FIELDTYPE_USERS_TITLE) . '
							    </div>			
							  </div>        				
        				';

                $choices = ['' => ''];
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_input','fieldtype_input_masked','fieldtype_phone') and f.entities_id=1 and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$fields['id']] = $fields['name'];
                }

                $html .= '
        				<div class="form-group" style="margin-top: 30px;">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_PHONE . '</label>
							    <div class="col-md-9">	
							  	  ' . select_tag(
                        'fields_id',
                        $choices,
                        $obj['fields_id'],
                        ['class' => 'form-control input-large required']
                    ) . '
							  	  ' . tooltip_text(TEXT_EXT_SEND_TO_USER_NUMBER_INFO) . '
							    </div>			
							  </div>
        				';
                break;
            case 'insert_send_to_number_in_entity':
            case 'edit_send_to_number_in_entity':
            case 'schedule_send_to_number_in_entity':

                $choices = ['' => ''];

                $entiy_fields_query = db_query(
                    "select name, id, configuration from app_fields where entities_id='" . $entities_id . "' and type in ('fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel')"
                );
                while ($entity_fields = db_fetch_array($entiy_fields_query)) {
                    $cfg = new fields_types_cfg($entity_fields['configuration']);

                    $related_entity_id = $cfg->get('entity_id');

                    $fields_query = db_query(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_input','fieldtype_input_masked','fieldtype_phone') and f.entities_id={$related_entity_id} and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $choices[$entity_fields['name']][$entity_fields['id'] . ':' . $fields['id']] = $fields['name'];
                    }
                }

                $html .= '
        				<div class="form-group" style="margin-top: 30px;">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_EXT_SEND_TO_RECORD_NUMBER . '</label>
							    <div class="col-md-9">
							  	  ' . select_tag(
                        'phone',
                        $choices,
                        $obj['phone'],
                        ['class' => 'form-control input-xlarge required chosen-select']
                    ) . '
							  	  ' . tooltip_text(TEXT_EXT_SEND_TO_RECORD_NUMBER_INFO) . '
							    </div>
							  </div>
        				';

                break;
        }


        $html .= '
            
            <div class="form-group">
	  	<label class="col-md-3 control-label" for="cfg_sms_send_to_number_text">' . TEXT_EXT_MESSAGE_TEXT . fields::get_available_fields_helper(
                $entities_id,
                'description'
            ) . '</label>
                <div class="col-md-9">' .
            textarea_tag(
                'description',
                $obj['description'],
                ['class' => 'form-control input-xlarge textarea-small required']
            ) .
            tooltip_text(TEXT_ENTER_TEXT_PATTERN_INFO) .
            '</div>			
            </div>
            ';

        echo $html;

        exit();
        break;
}