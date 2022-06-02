<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}

$app_actions_info_query = db_query(
    "select * from app_ext_processes_actions where process_id='" . _get::int('process_id') . "' and id='" . _get::int(
        'actions_id'
    ) . "'"
);
if (!$app_actions_info = db_fetch_array($app_actions_info_query)) {
    redirect_to('ext/processes/processes');
}


switch ($app_module_action) {
    case 'render_template_field':

        if ($_POST['fields_id'] > 0) {
            $html = '';

            $fields_info = db_find('app_fields', $_POST['fields_id']);
            $fields_info_cfg = new fields_types_cfg($fields_info['configuration']);

            //check field type
            if (in_array($fields_info['type'], ['fieldtype_user_roles'])) {
                echo app_alert_warning(TEXT_EXT_ENTER_MANUALLY_ONLY);
                exit();
            }

            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_processes_actions_fields', $_POST['id']);
                $value = ['field_' . $fields_info['id'] => $obj['value']];
            } else {
                $value = ['field_' . $fields_info['id'] => ''];
            }

            $params = [
                'form' => '',
                'parent_entity_item_id' => 0,
                'is_new_item' => false,
            ];


            //handle copy value for users field or doropdown if uses global list
            if (in_array(
                    $fields_info['type'],
                    [
                        'fieldtype_users_approve',
                        'fieldtype_users',
                        'fieldtype_users_ajax',
                        'fieldtype_created_by',
                        'fieldtype_input_masked',
                        'fieldtype_phone',
                        'fieldtype_input_email',
                        'fieldtype_entity',
                        'fieldtype_entity_ajax',
                        'fieldtype_entity_multilevel'
                    ]
                ) or (in_array($fields_info['type'], ['fieldtype_dropdown', 'fieldtype_dropdown_multiple']
                    ) and $fields_info_cfg->get('use_global_list') > 0)) {
                if (strstr($obj['value'], '[')) {
                    $field_value = ['field_' . $fields_info['id'] => ''];
                    $extra_value = $obj['value'];
                } else {
                    $field_value = $value;
                    $extra_value = '';
                }

                $html = fields_types::render($fields_info['type'], $fields_info, $field_value, $params);

                $html .= '<br>' . TEXT_EXT_VALUE . input_tag(
                        'fields_extra[' . $fields_info['id'] . ']',
                        $extra_value,
                        ['class' => 'form-control input-medium']
                    );

                switch ($fields_info['type']) {
                    case 'fieldtype_users':
                    case 'fieldtype_users_ajax':
                    case 'fieldtype_created_by':
                    case 'fieldtype_users_approve':
                        $html .= tooltip_text(TEXT_EXT_VALUE_USES_TIP);
                        break;
                }
            } elseif (in_array($fields_info['type'], ['fieldtype_input_date', 'fieldtype_input_datetime'])) {
                if (strlen($obj['value']) >= 10) {
                    $field_value = $value;
                    $extra_value = '';
                } else {
                    $field_value = ['field_' . $fields_info['id'] => ''];
                    $extra_value = $obj['value'];
                }

                $html = fields_types::render($fields_info['type'], $fields_info, $field_value, $params);

                $html .= TEXT_DAY . input_tag(
                        'fields_extra[' . $fields_info['id'] . ']',
                        $extra_value,
                        ['class' => 'form-control input-small']
                    ) . tooltip_text(TEXT_EXT_DATE_FIELD_ALLOWED_VALUES . '<br>' . TEXT_EXT_SPACE_TO_RESET);
            } elseif (in_array(
                $fields_info['type'],
                ['fieldtype_input_file', 'fieldtype_attachments', 'fieldtype_image', 'fieldtype_image_ajax']
            )) {
                $html .= input_tag(
                    'fields[' . $fields_info['id'] . ']',
                    $obj['value'],
                    ['class' => 'form-control input-small']
                );
            } elseif (in_array($fields_info['type'], ['fieldtype_dropdown_multiple'])) {
                $params['form'] = '';
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            } elseif (in_array($fields_info['type'], ['fieldtype_ajax_request', 'fieldtype_subentity_form'])) {
                echo '<script>$("#enter_manually").val("1")</script>';
                exit();
            } else {
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            }

            if (!strstr($app_actions_info['type'], 'edit_item_entity_')) {
                $use_fields_types = '';

                switch ($fields_info['type']) {
                    case 'fieldtype_input_numeric':
                        $use_fields_types = "'fieldtype_input_numeric','fieldtype_formula','fieldtype_input_numeric_comments'";
                        break;
                    case 'fieldtype_dropdown':
                    case 'fieldtype_dropdown_multiple':
                    case 'fieldtype_users':
                    case 'fieldtype_users_ajax':
                    case 'fieldtype_input':
                    case 'fieldtype_input_email':
                    case 'fieldtype_input_masked':
                    case 'fieldtype_input_url':
                    case 'fieldtype_input_file':
                    case 'fieldtype_attachments':
                    case 'fieldtype_image':
                    case 'fieldtype_image_ajax':
                    case 'fieldtype_textarea':
                    case 'fieldtype_textarea_wysiwyg':
                    case 'fieldtype_input_date':
                    case 'fieldtype_input_datetime':
                    case 'fieldtype_entity':
                    case 'fieldtype_entity_ajax':
                    case 'fieldtype_entity_multilevel':
                        $use_fields_types = "'" . $fields_info['type'] . "'";
                        break;
                }

                if (strlen($use_fields_types)) {
                    $use_fields = [];
                    $fields_query = db_query(
                        "select * from app_fields where entities_id='" . $app_process_info['entities_id'] . "' and type in ({$use_fields_types})"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $fields_cfg = new fields_types_cfg($fields['configuration']);

                        //check if dropdown uses global list
                        if ($use_fields_types == "'fieldtype_dropdown'" or $use_fields_types == "'fieldtype_dropdown_multiple'") {
                            if ($fields_info_cfg->get('use_global_list') != $fields_cfg->get(
                                    'use_global_list'
                                ) or !strlen($fields_info_cfg->get('use_global_list')) or !strlen(
                                    $fields_cfg->get('use_global_list')
                                )) {
                                continue;
                            }
                        }

                        if (in_array($use_fields_types, ['fieldtype_entity', 'fieldtype_entity'])) {
                            if ($fields_info_cfg->get('entity_id') != $fields_cfg->get('entity_id')) {
                                continue;
                            }
                        }

                        $use_fields[] = '
								<div>
									<table>
										<tr>
											<td><input size="4" value="[' . $fields['id'] . ']" class="form-control select-all" readonly="readonly"></td>
											<td>&nbsp;&nbsp;' . $fields['name'] . '</td>
										</tr>
									</table>
								</div>';
                    }

                    //allows use created_by value for users
                    if ($fields_info['type'] == 'fieldtype_users' or $fields_info['type'] == 'fieldtype_users_ajax') {
                        $use_fields[] = '
								<div>
									<table>
										<tr>
											<td><input size="4" value="[created_by]" class="form-control select-all" readonly="readonly"></td>
											<td>&nbsp;&nbsp;' . TEXT_CREATED_BY . '</td>
										</tr>
									</table>
								</div>';
                    }

                    if (count($use_fields)) {
                        $text = TEXT_EXT_USE_VALUE_FROM_CURRENT_RECORD . implode('', $use_fields);
                        $html .= tooltip_text($text);
                    }
                }
            }

            $html .= '
            <script>
              $(".field_' . $fields_info['id'] . '").removeClass("required").removeClass("number").removeAttr("min").removeAttr("max")
            </script>
          ';

            echo $html;
        }

        exit();
        break;
    case 'save':
        $field = db_find('app_fields', $_POST['fields_id']);


        $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');

        $extra_value = (isset($_POST['fields_extra'][$field['id']]) ? $_POST['fields_extra'][$field['id']] : '');

        if (strlen($extra_value)) {
            $value = $extra_value;
        } else {
            //prepare process options        
            $process_options = [
                'class' => $field['type'],
                'value' => $value,
                'field' => $field,
                'is_new_item' => true,
            ];

            $value = fields_types::process($process_options);
        }

        $sql_data = [
            'actions_id' => $_GET['actions_id'],
            'fields_id' => $field['id'],
            'value' => $value,
            'enter_manually' => $_POST['enter_manually'],
        ];


        if (isset($_GET['id'])) {
            $actions_fields_id = $_GET['id'];
        } else {
            $actions_fields_id = null;

            //check if fields already added and update it
            $check_query = db_query(
                "select * from app_ext_processes_actions_fields where fields_id='" . db_input(
                    $field['id']
                ) . "' and actions_id='" . db_input($_GET['actions_id']) . "'"
            );
            if ($check = db_fetch_array($check_query)) {
                $actions_fields_id = $check['id'];
            }
        }


        if (isset($actions_fields_id)) {
            db_perform(
                'app_ext_processes_actions_fields',
                $sql_data,
                'update',
                "id='" . db_input($actions_fields_id) . "'"
            );
        } else {
            db_perform('app_ext_processes_actions_fields', $sql_data);
        }


        redirect_to(
            'ext/processes/fields',
            'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int('actions_id')
        );
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $obj = db_find('app_ext_processes_actions_fields', $_GET['id']);

            db_query("delete from app_ext_processes_actions_fields where id='" . db_input($_GET['id']) . "'");

            redirect_to(
                'ext/processes/fields',
                'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int('actions_id')
            );
        }
        break;
}		
