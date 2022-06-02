<?php

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'templates_id' => $template_info['id'],
            'block_type' => 'table_list_cell',
            'parent_id' => $parent_block['id'],
            'fields_id' => 0,
            'settings' => (isset($_POST['settings']) ? json_encode($_POST['settings']) : ''),
            'sort_order' => $_POST['sort_order'],
        ];

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

        redirect_to(
            'ext/templates_docx/table_list_blocks',
            'templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
        );
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            export_templates_blocks::delele_block(_GET('id'));

            redirect_to(
                'ext/templates_docx/table_list_blocks',
                'templates_id=' . $template_info['id'] . '&parent_block_id=' . $parent_block['id']
            );
        }
        break;
}
