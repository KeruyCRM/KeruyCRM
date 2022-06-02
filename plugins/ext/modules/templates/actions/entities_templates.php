<?php

if (!app_session_is_registered('entities_templates_filter')) {
    $entities_templates_filter = 0;
    app_session_register('entities_templates_filter');
}

switch ($app_module_action) {
    case 'set_entities_templates_filter':
        $entities_templates_filter = $_POST['entities_templates_filter'];

        redirect_to('ext/templates/entities_templates');
        break;
    case 'sort_templates':
        if (isset($_POST['templates'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['templates']) as $v) {
                $sql_data = ['sort_order' => $sort_order];
                db_perform(
                    'app_ext_entities_templates',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('template_', '', $v)) . "'"
                );
                $sort_order++;
            }
        }
        exit();
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
        ];

        if (isset($_GET['id'])) {
            //check if template entity was changed and reset fields
            $template_info = db_find('app_ext_entities_templates', $_GET['id']);
            if ($template_info['entities_id'] != $_POST['entities_id']) {
                db_query(
                    "delete from app_ext_entities_templates_fields where templates_id='" . db_input($_GET['id']) . "'"
                );
            }

            db_perform('app_ext_entities_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_entities_templates', $sql_data);
        }

        redirect_to('ext/templates/entities_templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_entities_templates where id='" . db_input($_GET['id']) . "'");
            db_query(
                "delete from app_ext_entities_templates_fields where templates_id='" . db_input($_GET['id']) . "'"
            );

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/templates/entities_templates');
        }
        break;
}