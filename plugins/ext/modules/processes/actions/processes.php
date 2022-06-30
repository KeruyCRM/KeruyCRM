<?php

if (!app_session_is_registered('processes_filter')) {
    $processes_filter = 0;
    app_session_register('processes_filter');
}

$app_title = app_set_title(TEXT_EXT_PROCESSES);

switch ($app_module_action) {
    case 'set_processes_filter':
        $processes_filter = $_POST['processes_filter'];

        redirect_to('ext/processes/processes');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'print_template' => $_POST['print_template'] ?? '',
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'access_to_assigned' => (isset($_POST['access_to_assigned']) ? implode(
                ',',
                $_POST['access_to_assigned']
            ) : ''),
            'window_width' => $_POST['window_width'],
            'confirmation_text' => $_POST['confirmation_text'],
            'warning_text' => $_POST['warning_text'],
            'allow_comments' => (isset($_POST['allow_comments']) ? 1 : 0),
            'preview_prcess_actions' => (isset($_POST['preview_prcess_actions']) ? 1 : 0),
            'notes' => strip_tags($_POST['notes']),
            'payment_modules' => (isset($_POST['payment_modules']) ? implode(',', $_POST['payment_modules']) : ''),
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'apply_fields_access_rules' => (isset($_POST['apply_fields_access_rules']) ? 1 : 0),
            'apply_fields_display_rules' => (isset($_POST['apply_fields_display_rules']) ? 1 : 0),
            'hide_entity_name' => (isset($_POST['hide_entity_name']) ? 1 : 0),
            'success_message' => $_POST['success_message'],
            'redirect_to_items_listing' => $_POST['redirect_to_items_listing'],
            'disable_comments' => (isset($_POST['disable_comments']) ? 1 : 0),
            'javascript_in_from' => $_POST['javascript_in_from'],
            'javascript_onsubmit' => $_POST['javascript_onsubmit'],
            'is_form_wizard' => $_POST['is_form_wizard'],
            'is_form_wizard_progress_bar' => $_POST['is_form_wizard_progress_bar'],
            'submit_button_title' => $_POST['submit_button_title'],
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            $process_info = db_find('app_ext_processes', $_GET['id']);

            //check entity and if it's changed remove process action
            if ($process_info['entities_id'] != $_POST['entities_id']) {
                $actions_query = db_query(
                    "select * from app_ext_processes_actions where process_id=" . _get::int('id')
                );
                while ($actions = db_fetch_array($actions_query)) {
                    db_query("delete from app_ext_processes_actions where id='" . $actions['id'] . "'");
                    db_query(
                        "delete from app_ext_processes_actions_fields where actions_id='" . db_input(
                            $actions['id']
                        ) . "'"
                    );
                }

                $reports_info_query = db_query(
                    "select * from app_reports where reports_type='process" . $_GET['id'] . "'"
                );
                if ($reports_info = db_fetch_array($reports_info_query)) {
                    db_query(
                        "delete from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'"
                    );
                    db_query("delete from app_reports where id='" . db_input($reports_info['id']) . "'");
                }
            }

            db_perform('app_ext_processes', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_processes', $sql_data);

            $insert_id = db_insert_id();
        }

        redirect_to('ext/processes/processes');
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            $obj = db_find('app_ext_processes', $_GET['id']);

            db_query("delete from app_ext_processes where id='" . db_input($_GET['id']) . "'");

            $actions_query = db_query("select * from app_ext_processes_actions where process_id=" . _get::int('id'));
            while ($actions = db_fetch_array($actions_query)) {
                db_query("delete from app_ext_processes_actions where id='" . db_input($actions['id']) . "'");
                db_query(
                    "delete from app_ext_processes_actions_fields where actions_id='" . db_input($actions['id']) . "'"
                );
                db_query(
                    "delete from app_ext_processes_clone_subitems where actions_id='" . db_input($actions['id']) . "'"
                );

                $reports_info_query = db_query(
                    "select * from app_reports where reports_type='process_action" . $actions['id'] . "'"
                );
                if ($reports_info = db_fetch_array($reports_info_query)) {
                    db_query(
                        "delete from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'"
                    );
                    db_query("delete from app_reports where id='" . db_input($reports_info['id']) . "'");
                }
            }

            $reports_info_query = db_query("select * from app_reports where reports_type='process" . $_GET['id'] . "'");
            if ($reports_info = db_fetch_array($reports_info_query)) {
                db_query("delete from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'");
                db_query("delete from app_reports where id='" . db_input($reports_info['id']) . "'");
            }

            redirect_to('ext/processes/processes');
        }
        break;

    case 'get_entities_buttons_positions':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_processes', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_processes');
        }

        $choices = [];
        $choices['default'] = TEXT_EXT_IN_RECORD_PAGE;
        $choices['menu_more_actions'] = TEXT_EXT_MENU_MORE_ACTIONS;
        $choices['menu_with_selected'] = TEXT_EXT_MENU_WITH_SELECTED;
        $choices['in_listing'] = TEXT_EXT_IN_LISTING;
        $choices['comments_section'] = TEXT_EXT_COMMENTS_SECTION;
        $choices['run_after_insert'] = TEXT_EXT_RUN_PROCESS_AFTER_RECORD_INSERT;
        $choices['run_after_update'] = TEXT_EXT_RUN_PROCESS_AFTER_RECORD_UPDATE;
        $choices['run_before_delete'] = TEXT_EXT_RUN_PROCESS_BEFORE_RECORD_DELETE;
        $choices['run_on_schedule'] = TEXT_EXT_RUN_PROCESS_ON_SCHEDULE;


        $buttons_query = db_query(
            "select id, name from app_ext_processes_buttons_groups where entities_id='" . $entities_id . "' order by sort_order, name"
        );
        while ($buttons = db_fetch_array($buttons_query)) {
            $choices['buttons_groups_' . $buttons['id']] = $buttons['name'];
        }

        $html = '
         <div class="form-group">
            <label class="col-md-3 control-label" for="access_to_assgined">' . TEXT_EXT_PROCESS_BUTTON_POSITION . '</label>
            <div class="col-md-9">
          	' . select_tag(
                'button_position[]',
                $choices,
                $obj['button_position'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '
                
                <div form_display_rules="button_position:run_on_schedule">
                    <div class="help-block">' . TEXT_EXT_RUN_PROCESS_ON_SCHEDULE_INFO . '</div>
                    <code>php -q ' . DIR_FS_CATALOG . 'cron/process.php [process_id] [item_id]</code>    
                </div>       
            </div>
          </div>
        ';


        $choices = ['' => TEXT_NONE];

        $templates_query = db_query(
            "select ep.*, e.name as entities_name from app_ext_export_templates ep, app_entities e where e.id=ep.entities_id and e.id={$entities_id} order by e.id, ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            $choices[$templates['name']][$templates['id'] . '_print'] = TEXT_PRINT;
            $choices[$templates['name']][$templates['id'] . '_printPopup'] = TEXT_PRINT . ' (POPUP)';
            $choices[$templates['name']][$templates['id'] . '_pdf'] = TEXT_SAVE . ' PDF';
            $choices[$templates['name']][$templates['id'] . '_docx'] = TEXT_SAVE . ' DOCX';
        }

        if (count($choices)) {
            $html .= '                  
                <div class="form-group">    
                    <label class="col-md-3 control-label" for="redirect_to_items_listing">' . TEXT_PRINT_TEMPLATE_AFTER_PROCESS . '</label>
                    <div class="col-md-9">	
                        ' . select_tag(
                    'print_template',
                    $choices,
                    $obj['print_template'],
                    ['class' => 'chosen-select form-control input-large']
                ) . '
                    </div>			
                </div>';
        }


        echo $html;

        exit();
        break;

    case 'get_entities_users_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_processes', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_processes');
        }

        $html = '';

        $choices = [];
        $fields_query = db_query(
            "select f.*, if(f.type in (" . fields_types::get_reserverd_data_types_list(
            ) . "),-1,t.sort_order) as tab_sort_order from app_fields f,  app_forms_tabs t where f.forms_tabs_id=t.id  and f.type in ('fieldtype_user_roles','fieldtype_users_approve','fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_created_by') and f.entities_id='" . db_input(
                $entities_id
            ) . "' order by tab_sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            if ($fields['type'] == 'fieldtype_created_by') {
                $choices[$fields['id']] = TEXT_FIELDTYPE_CREATEDBY_TITLE;
            } else {
                $choices[$fields['id']] = $fields['name'];
            }
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="access_to_assgined">' . TEXT_EXT_ACCESS_TO_ASSIGNED_USERS . '</label>
            <div class="col-md-9">
          	   ' . select_tag(
                'access_to_assigned[]',
                $choices,
                $obj['access_to_assigned'],
                ['class' => 'form-control input-large chosen-select', 'multiple' => 'multiple']
            ) . '               
            </div>
          </div>
        ';


        echo $html;

        exit();
        break;
    case 'copy':

        $process_id = _get::int('id');

        //copy process
        $process_info_query = db_query("select * from app_ext_processes where id='" . $process_id . "'");
        if ($process_info = db_fetch_array($process_info_query)) {
            $sql_data = $process_info;
            unset($sql_data['id']);
            $sql_data['name'] = $sql_data['name'] . ' (' . TEXT_EXT_NAME_COPY . ')';
            $sql_data['is_active'] = 0;

            db_perform('app_ext_processes', $sql_data);
            $new_process_id = db_insert_id();

            //copy actions
            $actions_query = db_query("select * from app_ext_processes_actions where process_id=" . $process_id);
            while ($actions = db_fetch_array($actions_query)) {
                $sql_data = $actions;
                unset($sql_data['id']);
                $sql_data['process_id'] = $new_process_id;

                db_perform('app_ext_processes_actions', $sql_data);
                $new_action_id = db_insert_id();

                //copy fields
                $fields_query = db_query(
                    "select * from app_ext_processes_actions_fields where actions_id='" . $actions['id'] . "'"
                );
                while ($fields = db_fetch_array($fields_query)) {
                    $sql_data = $fields;
                    unset($sql_data['id']);
                    $sql_data['actions_id'] = $new_action_id;

                    db_perform('app_ext_processes_actions_fields', $sql_data);
                }

                //copy actions filters
                $reports_info_query = db_query(
                    "select * from app_reports where reports_type='process_action" . $actions['id'] . "'"
                );
                if ($reports_info = db_fetch_array($reports_info_query)) {
                    $sql_data = $reports_info;
                    unset($sql_data['id']);
                    $sql_data['reports_type'] = 'process_action' . $new_action_id;

                    db_perform('app_reports', $sql_data);
                    $new_reports_id = db_insert_id();

                    $reports_filters_query = db_query(
                        "select * from app_reports_filters where reports_id='" . $reports_info['id'] . "'"
                    );
                    while ($reports_filters = db_fetch_array($reports_filters_query)) {
                        $sql_data = $reports_filters;
                        unset($sql_data['id']);
                        $sql_data['reports_id'] = $new_reports_id;

                        db_perform('app_reports_filters', $sql_data);
                    }
                }
            }

            //copy process filters
            $reports_info_query = db_query("select * from app_reports where reports_type='process" . $process_id . "'");
            if ($reports_info = db_fetch_array($reports_info_query)) {
                $sql_data = $reports_info;
                unset($sql_data['id']);
                $sql_data['reports_type'] = 'process' . $new_process_id;

                db_perform('app_reports', $sql_data);
                $new_reports_id = db_insert_id();

                $reports_filters_query = db_query(
                    "select * from app_reports_filters where reports_id='" . $reports_info['id'] . "'"
                );
                while ($reports_filters = db_fetch_array($reports_filters_query)) {
                    $sql_data = $reports_filters;
                    unset($sql_data['id']);
                    $sql_data['reports_id'] = $new_reports_id;

                    db_perform('app_reports_filters', $sql_data);
                }
            }
        }

        $alerts->add(TEXT_EXT_PROCESS_COPIED, 'success');
        redirect_to('ext/processes/processes');

        break;
}