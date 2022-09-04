<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'entities_id' => $_POST['entities_id'],
            'action_type' => $_POST['action_type'],
            'send_to_users' => (isset($_POST['send_to_users']) ? implode(',', $_POST['send_to_users']) : ''),
            'send_to_assigned_users' => (isset($_POST['send_to_assigned_users']) ? implode(
                ',',
                $_POST['send_to_assigned_users']
            ) : ''),
            'send_to_email' => (isset($_POST['send_to_email']) ? $_POST['send_to_email'] : ''),
            'send_to_assigned_email' => (isset($_POST['send_to_assigned_email']) ? implode(
                ',',
                $_POST['send_to_assigned_email']
            ) : ''),
            'subject' => $_POST['subject'],
            'description' => $_POST['description'],
            'monitor_fields_id' => (isset($_POST['monitor_fields_id']) ? $_POST['monitor_fields_id'] : 0),
            'monitor_choices' => (isset($_POST['monitor_choices']) ? implode(',', $_POST['monitor_choices']) : ''),
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'attach_attachments' => (isset($_POST['attach_attachments']) ? 1 : 0),
            'attach_template' => (isset($_POST['attach_template']) ? implode(',', $_POST['attach_template']) : ''),
            'date_fields_id' => $_POST['date_fields_id'] ?? 0,
            'number_of_days' => $_POST['number_of_days'] ?? 0,
            'notes' => $_POST['notes']

        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_email_rules', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_email_rules', $sql_data);
        }

        redirect_to('ext/email_sending/rules', 'entities_id=' . _get::int('entities_id'));

        break;
    case 'delete':

        if (isset($_GET['id'])) {
            db_delete_row('app_ext_email_rules', $_GET['id']);
        }

        redirect_to('ext/email_sending/rules', 'entities_id=' . _get::int('entities_id'));
        break;
    case 'get_available_fields':

        $entities_id = _post::int('entities_id');
        $entities_info = db_find('app_entities', $entities_id);

        //$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserved_types_list() . ") and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");

        $fields_query = fields::get_query(
            $entities_id,
            "and f.type not in (" . fields_types::get_reserved_types_list() . ")"
        );

        if (db_num_rows($fields_query) == 0) {
            exit();
        }

        $html = '
  			<div class="dropdown">
				  <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				    ' . TEXT_AVAILABLE_FIELDS . '
				    <span class="caret"></span>
				  </button>
  			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">';

        $html .= '
  			<li>
  				<a href="#" class="insert_to_template_description" data-field="[url]">' . TEXT_URL . ' [url]</a>  	      
  	    </li>
  			<li>
  				<a href="#" class="insert_to_template_description" data-field="[comment]">' . TEXT_COMMENT . ' [comment]</a>  	      
  	    </li>
  	    <li>
  				<a href="#" class="insert_to_template_description" data-field="[id]">' . TEXT_FIELDTYPE_ID_TITLE . ' [id]</a>  	      
  	    </li>
  	    <li>
  	      <a href="#" class="insert_to_template_description" data-field="[date_added]">' . TEXT_FIELDTYPE_DATEADDED_TITLE . ' [date_added]</a>  	      
  	    </li>
  	    <li>
  	      <a href="#" class="insert_to_template_description" data-field="[created_by]">' . TEXT_FIELDTYPE_CREATEDBY_TITLE . ' [created_by]</a>  	      
  	    </li>';

        if ($entities_info['parent_id'] > 0) {
            $html .= '
  				<li>
	  	      <a href="#" class="insert_to_template_description" data-field="[parent_item_id]">' . TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE . ' [parent_item_id]</a>  	      
	  	    </li>';
        }

        while ($v = db_fetch_array($fields_query)) {
            if ($v['type'] == 'fieldtype_dropdown_multilevel') {
                $html .= fieldtype_dropdown_multilevel::output_export_template($v);
            } else {
                $html .= '
  		    <li>
  		  		<a href="#"  class="insert_to_template_description" data-field="[' . $v['id'] . ']">' . fields_types::get_option(
                        $v['type'],
                        'name',
                        $v['name']
                    ) . ' [' . $v['id'] . ']</a>  		      
  		    </li>';
            }
        }

        //parent entity fields
        if ($entities_info['parent_id'] > 0) {
            $parent_entity_name = $app_entities_cache[$entities_info['parent_id']]['name'];

            $html .= '
                <li class="divider"></li>
                <li>
                    <a href="#" ><b>' . $parent_entity_name . '</b></a>  		      
                </li>';

            $fields_query = fields::get_query(
                $entities_info['parent_id'],
                "and f.type not in (" . fields_types::get_reserved_types_list() . ")"
            );

            while ($v = db_fetch_array($fields_query)) {
                if ($v['type'] == 'fieldtype_dropdown_multilevel') {
                    $html .= fieldtype_dropdown_multilevel::output_export_template($v);
                } else {
                    $html .= '
                        <li>
                                    <a href="#"  class="insert_to_template_description" data-field="[' . $v['id'] . ']">' . fields_types::get_option(
                            $v['type'],
                            'name',
                            $v['name']
                        ) . ' [' . $v['id'] . ']</a>  		      
                        </li>';
                }
            }
        }

        $html .= '</ul></div>';

        $html .= '
  			<script>
  			$(".insert_to_template_description").click(function(){
			    html = $(this).attr("data-field").trim();
			    CKEDITOR.instances.description.insertText(html);
			  })
  			</script>
  			';

        echo $html;

        exit();

        break;
    case 'get_monitor_choices':
        $entities_id = _post::int('entities_id');
        $fields_id = _post::int('fields_id');

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_email_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_email_rules');
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
            if (!in_array($_POST['action_type'], ['edit_send_to_users', 'edit_send_to_assigned_users',])) {
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
            $obj = db_find('app_ext_email_rules', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_email_rules');
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
                <div class="col-md-9">' . TEXT_EXT_SEND_BY_DATE_CRON . ' <code>' . DIR_FS_CATALOG . 'cron/email_by_date.php</code></div>
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
                <label class="col-md-3 control-label" for="date_fields_id">' . TEXT_EXT_NUMBER_OF_DAYS . '</label>
                <div class="col-md-9">	
                      ' . input_tag(
                    'number_of_days',
                    $obj['number_of_days'],
                    ['class' => 'form-control input-medium required']
                ) . '						  	  	
                      ' . tooltip_text(TEXT_EXT_NUMBER_OF_DAYS_SEND_RULE_INFO) . '
                </div>			
            </div>
            
            ';
        }


        $choices = ['' => ''];
        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_users','fieldtype_users_approve','fieldtype_users_ajax','fieldtype_autostatus','fieldtype_stages') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $title = TEXT_FIELD;
        $tooltip = TEXT_EXT_PB_NOTIFY_FIELD_INSERT;
        $is_required = false;

        if (in_array($_POST['action_type'], ['edit_send_to_assigned_users', 'edit_send_to_users'])) {
            $title = TEXT_EXT_PB_NOTIFY_FIELD_CHANGE;
            $tooltip = '';
            $is_required = true;
        }

        $html .= '
            <div class="form-group" style="margin-top: 30px;">
                <label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . tooltip_icon(
                $tooltip
            ) . $title . '</label>
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
                </div>			
            </div>
						  
            <div id="monitor_choices_row"></div>

            <script> get_monitor_choices(); </script>	  		
        ';


        switch ($_POST['action_type']) {
            case 'edit_send_to_assigned_users':
            case 'insert_send_to_assigned_users':
            case 'comment_send_to_assigned_users':
            case 'schedule_send_to_assigned_users':


                $choices = ['' => ''];
                $fields_query = db_query(
                    "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_access_group','fieldtype_users_approve','fieldtype_user_roles','fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_created_by') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$app_entities_cache[$entities_id]['name']][$fields['id']] = fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }


                if ($app_entities_cache[$entities_id]['parent_id'] > 0) {
                    $fields_query = db_query(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_access_group','fieldtype_users_approve','fieldtype_user_roles','fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_created_by') and f.entities_id='" . $app_entities_cache[$entities_id]['parent_id'] . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $choices[$app_entities_cache[$fields['entities_id']]['name']][$fields['id']] = fields_types::get_option(
                            $fields['type'],
                            'name',
                            $fields['name']
                        );
                    }
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
                break;
            case 'edit_send_to_users':
            case 'insert_send_to_users':
            case 'comment_send_to_users':
            case 'schedule_send_to_users':

                $access_schema = users::get_entities_access_schema_by_groups($entities_id);

                $choices = ['' => ''];

                $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
                $users_query = db_query(
                    "select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where u.field_5=1 order by group_name, " . $order_by_sql
                );
                while ($users = db_fetch_array($users_query)) {
                    if (!isset($access_schema[$users['field_6']])) {
                        $access_schema[$users['field_6']] = [];
                    }

                    if ($users['field_6'] == 0 or in_array('view', $access_schema[$users['field_6']]) or in_array(
                            'view_assigned',
                            $access_schema[$users['field_6']]
                        )) {
                        $group_name = (strlen($users['group_name']) > 0 ? $users['group_name'] : TEXT_ADMINISTRATOR);
                        $choices[$group_name][$users['id']] = $app_users_cache[$users['id']]['name'];
                    }
                }


                $html .= '
        				<div class="form-group" style="margin-top: 30px;">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_EXT_SEND_TO_USERS . '</label>
							    <div class="col-md-9">	
							  	  ' . select_tag(
                        'send_to_users[]',
                        $choices,
                        $obj['send_to_users'],
                        ['class' => 'form-control input-xlarge chosen-select required', 'multiple' => 'multiple']
                    ) . '
							  	  ' . tooltip_text(TEXT_EXT_SEND_TO_USERS_INFO) . '
							    </div>			
							  </div>
        				';
                break;

            case 'edit_send_to_email':
            case 'insert_send_to_email':
            case 'comment_send_to_email':
            case 'schedule_send_to_email':
                $html .= '
        				<div class="form-group" style="margin-top: 30px;">
							  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . tooltip_icon(
                        TEXT_EXT_SEND_TO_EMAIL_TIP
                    ) . TEXT_EMAIL . '</label>
							    <div class="col-md-9">
							  	  ' . textarea_tag(
                        'send_to_email',
                        $obj['send_to_email'],
                        ['class' => 'form-control input-xlarge required']
                    ) . '							  	  
							    </div>
							  </div>
        				';
                break;

            case 'edit_send_to_assigned_email':
            case 'insert_send_to_assigned_email':
            case 'comment_send_to_assigned_email':
            case 'schedule_send_to_assigned_email':


                $choices = ['' => ''];
                $fields_query = db_query(
                    "select	f.*,	t.name	as	tab_name	from	app_fields	f,	app_forms_tabs	t	where	f.type	in	('fieldtype_input_email','fieldtype_mysql_query','fieldtype_formula')	and	f.entities_id='" . $entities_id . "'	and	f.forms_tabs_id=t.id	order	by	t.sort_order,	t.name,	f.sort_order,	f.name"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $choices[$app_entities_cache[$entities_id]['name']][$fields['id']] = fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    );
                }


                if ($app_entities_cache[$entities_id]['parent_id'] > 0) {
                    $fields_query = db_query(
                        "select	f.*,	t.name	as	tab_name	from	app_fields	f,	app_forms_tabs	t	where	f.type	in	('fieldtype_input_email','fieldtype_mysql_query','fieldtype_formula')	and	f.entities_id='" . $app_entities_cache[$entities_id]['parent_id'] . "'	and	f.forms_tabs_id=t.id	order	by	t.sort_order,	t.name,	f.sort_order,	f.name"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $choices[$app_entities_cache[$fields['entities_id']]['name']][$fields['id']] = fields_types::get_option(
                            $fields['type'],
                            'name',
                            $fields['name']
                        );
                    }
                }

                $html .= '
							<div	class="form-group">
								<label	class="col-md-3	control-label"	for="cfg_sms_send_to_record_number">' . TEXT_EXT_SEND_TO_ASSIGNED_USERS . '</label>
									<div	class="col-md-9">
										' . select_tag(
                        'send_to_assigned_email[]',
                        $choices,
                        $obj['send_to_assigned_email'],
                        [
                            'class' => 'form-control	input-xlarge	chosen-select	required',
                            'multiple' => 'multiple'
                        ]
                    ) . '
										' . tooltip_text(
                        TEXT_AVAILABLE_FIELDS . ':	' . TEXT_FIELDTYPE_INPUT_EMAIL_TITLE . ', ' . TEXT_FIELDTYPE_MYSQL_QUERY_TITLE
                    ) . '
									</div>
							</div>
							';
                break;
        }


        echo $html;

        exit();
        break;
}