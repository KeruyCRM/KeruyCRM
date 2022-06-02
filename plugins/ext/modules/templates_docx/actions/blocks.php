<?php

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'templates_id' => $template_info['id'],
            'block_type' => 'parent',
            'parent_id' => 0,
            'fields_id' => _POST('fields_id'),
            'settings' => (isset($_POST['settings']) ? json_encode($_POST['settings']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

        //print_rr($_POST);
        //EXIT();

        if (isset($_GET['id'])) {
            db_perform(
                'app_ext_items_export_templates_blocks',
                $sql_data,
                'update',
                "id='" . db_input($_GET['id']) . "'"
            );
        } else {
            db_perform('app_ext_items_export_templates_blocks', $sql_data);
        }

        redirect_to('ext/templates_docx/blocks', 'templates_id=' . $template_info['id']);
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            export_templates_blocks::delele_block(_GET('id'));

            redirect_to('ext/templates_docx/blocks', 'templates_id=' . $template_info['id']);
        }
        break;
}