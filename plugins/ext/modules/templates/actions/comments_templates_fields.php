<?php

$template_info_query = db_query(
    "select ep.*, e.name as entities_name from app_ext_comments_templates ep, app_entities e where e.id=ep.entities_id and ep.id='" . db_input(
        $_GET['templates_id']
    ) . "' order by e.id, ep.sort_order, ep.name"
);
if (!$template_info = db_fetch_array($template_info_query)) {
    redirect_to('ext/templates/comments_templates');
}

switch ($app_module_action) {
    case 'render_template_field':

        if ($_POST['fields_id'] > 0) {
            $fields_info = db_find('app_fields', $_POST['fields_id']);

            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_comments_templates_fields', $_POST['id']);
                $value = ['field_' . $fields_info['id'] => $obj['value']];
            } else {
                $value = ['field_' . $fields_info['id'] => ''];
            }

            $params = [
                'form' => 'comment',
                'parent_entity_item_id' => 0
            ];

            $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);

            $html .= '
            <script>
              $(".field_' . $fields_info['id'] . '").removeClass("required")
            </script>
          ';

            echo $html;
        }
        exit();
        break;
    case 'save':

        $field = db_find('app_fields', $_POST['fields_id']);


        $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');

        //prepare process options
        $process_options = [
            'class' => $field['type'],
            'value' => $value,
            'field' => $field,
        ];

        $value = fields_types::process($process_options);

        $sql_data = [
            'templates_id' => $_GET['templates_id'],
            'fields_id' => $field['id'],
            'value' => $value,
        ];

        if (isset($_GET['id'])) {
            $templates_fields_id = $_GET['id'];
        } else {
            $templates_fields_id = null;

            //check if fields already added and update it
            $check_query = db_query(
                "select * from app_ext_comments_templates_fields where fields_id='" . db_input(
                    $field['id']
                ) . "' and templates_id='" . db_input($_GET['templates_id']) . "'"
            );
            if ($check = db_fetch_array($check_query)) {
                $templates_fields_id = $check['id'];
            }
        }


        if (isset($templates_fields_id)) {
            db_perform(
                'app_ext_comments_templates_fields',
                $sql_data,
                'update',
                "id='" . db_input($templates_fields_id) . "'"
            );
        } else {
            db_perform('app_ext_comments_templates_fields', $sql_data);
        }

        redirect_to('ext/templates/comments_templates_fields', 'templates_id=' . $_GET['templates_id']);
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_comments_templates_fields where id='" . db_input($_GET['id']) . "'");

            redirect_to('ext/templates/comments_templates_fields', 'templates_id=' . $_GET['templates_id']);
        }
        break;
}