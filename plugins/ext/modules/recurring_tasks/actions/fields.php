<?php

require(component_path('ext/recurring_tasks/check_access'));

//check path
$path_info = items::get_path_info($current_entity_id, $current_item_id);
if ($app_path != $path_info['full_path']) {
    redirect_to('ext/recurring_tasks/fields', 'tasks_id=' . _get::int('tasks_id') . '&path=' . $path_info['full_path']);
}

switch ($app_module_action) {
    case 'render_template_field':

        if ($_POST['fields_id'] > 0) {
            $fields_info = db_find('app_fields', $_POST['fields_id']);
            $fields_info_cfg = new fields_types_cfg($fields_info['configuration']);

            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_recurring_tasks_fields', $_POST['id']);
                $value = ['field_' . $fields_info['id'] => $obj['value']];
            } else {
                $value = ['field_' . $fields_info['id'] => ''];
            }

            $params = [
                'form' => 'comment',
                'parent_entity_item_id' => 0,
                'is_new_item' => true,
            ];

            //handle copy value for users field or doropdown if uses global list
            if (in_array(
                    $fields_info['type'],
                    ['fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_input_masked', 'fieldtype_input_email']
                ) or (in_array($fields_info['type'], ['fieldtype_dropdown']) and $fields_info_cfg->get(
                        'use_global_list'
                    ) > 0)) {
                if (strstr($obj['value'], '[')) {
                    $field_value = ['field_' . $fields_info['id'] => ''];
                    $extra_value = $obj['value'];
                } else {
                    $field_value = $value;
                    $extra_value = '';
                }

                $html = fields_types::render($fields_info['type'], $fields_info, $field_value, $params);
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
            } elseif (in_array($fields_info['type'], ['fieldtype_dropdown_multiple'])) {
                $params['form'] = '';
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            } else {
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            }


            $html .= '
            <script>
              $(".field_' . $fields_info['id'] . '").removeClass("required").removeClass("number")
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
            'tasks_id' => $_GET['tasks_id'],
            'fields_id' => $field['id'],
            'value' => $value,
        ];

        if (isset($_GET['id'])) {
            $tasks_fields_id = $_GET['id'];
        } else {
            $tasks_fields_id = null;

            //check if fields already added and update it
            $check_query = db_query(
                "select * from app_ext_recurring_tasks_fields where fields_id='" . db_input(
                    $field['id']
                ) . "' and tasks_id='" . db_input($_GET['tasks_id']) . "'"
            );
            if ($check = db_fetch_array($check_query)) {
                $tasks_fields_id = $check['id'];
            }
        }


        if (isset($tasks_fields_id)) {
            db_perform(
                'app_ext_recurring_tasks_fields',
                $sql_data,
                'update',
                "id='" . db_input($tasks_fields_id) . "'"
            );
        } else {
            db_perform('app_ext_recurring_tasks_fields', $sql_data);
        }


        redirect_to('ext/recurring_tasks/fields', 'tasks_id=' . _get::int('tasks_id') . '&path=' . $app_path);
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_recurring_tasks_fields where id='" . db_input($_GET['id']) . "'");

            redirect_to('ext/recurring_tasks/fields', 'tasks_id=' . _get::int('tasks_id') . '&path=' . $app_path);
        }
        break;
}
