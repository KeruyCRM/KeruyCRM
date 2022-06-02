<?php

if (!app_session_is_registered('export_templates_filter')) {
    $export_templates_filter = 0;
    app_session_register('export_templates_filter');
}

switch ($app_module_action) {
    case 'copy':
        $templates_id = _get::int('templates_id');
        $templates_query = db_query("select * from app_ext_export_selected where id='" . $templates_id . "'");
        if ($templates = db_fetch_array($templates_query)) {
            unset($templates['id']);
            $templates['name'] = $templates['name'] . ' (' . TEXT_EXT_NAME_COPY . ')';
            db_perform('app_ext_export_selected', $templates);
        }
        redirect_to('ext/export_selected/templates');
        break;

    case 'set_export_templates_filter':
        $export_templates_filter = $_POST['export_templates_filter'];

        redirect_to('ext/export_selected/templates');
        break;
    case 'sort_templates':
        if (isset($_POST['templates'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['templates']) as $v) {
                $sql_data = ['sort_order' => $sort_order];
                db_perform(
                    'app_ext_export_selected',
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
            'type' => $_POST['type'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'export_fields' => (isset($_POST['export_fields']) ? implode(',', $_POST['export_fields']) : ''),
            'export_url' => $_POST['export_url'] ?? 0,
            'settings' => isset($_POST['settings']) ? json_encode($_POST['settings']) : '',
            'template_filename' => $_POST['template_filename'],
        ];

        if (isset($_GET['id'])) {
            $export_templates = db_find('app_ext_export_templates', _GET('id'));
            if ($export_templates['entities_id'] != _POST('entities_id')) {
                //export_templates_blocks::delele_blocks_by_template_id(_GET('id'));
            }

            db_perform('app_ext_export_selected', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            $template_id = _GET('id');
        } else {
            db_perform('app_ext_export_selected', $sql_data);
            $template_id = db_insert_id();
        }


        //upload file
        if (strlen($_FILES['filename']['name']) > 0 and (in_array(
                    $_FILES['filename']['type'],
                    ['application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                ) or substr($_FILES['filename']['name'], -5) == '.docx')) {
            $filename = 'Template-' . $template_id . '-' . str_replace(
                    'Template-' . $template_id . '-',
                    '',
                    $_FILES['filename']['name']
                );
            if (move_uploaded_file($_FILES['filename']['tmp_name'], DIR_WS_TEMPLATES . $filename)) {
                db_query(
                    "update app_ext_export_selected set filename = '" . db_input(
                        $filename
                    ) . "' where id='" . $template_id . "'"
                );
            }
        }

        redirect_to('ext/export_selected/templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_export_selected where id='" . db_input($_GET['id']) . "'");

            export_selected::delele_blocks_by_template_id(_GET('id'));

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/export_selected/templates');
        }
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_export_selected', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_export_selected');
        }

        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type not in ('fieldtype_action') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ) . '(#' . $fields['id'] . ')';
        }

        $html = '
            <div class="form-group">
                <label class="col-md-3 control-label" >' . TEXT_SELECT_FIELD_TO_EXPORT . '</label>
                <div class="col-md-9">	
                    ' . select_tag(
                'export_fields[]',
                $choices,
                $obj['export_fields'],
                [
                    'class' => 'form-control chosen-select chosen-sortable',
                    'chosen_order' => $obj['export_fields'],
                    'multiple' => 'multiple'
                ]
            ) . '                    
                    <label>' . input_checkbox_tag('export_url', 1, ['checked' => $obj['export_url']]
            ) . ' ' . TEXT_EXT_LINK_TO_RECORD_PAGE . '</lable>    
                </div>			
            </div>
            
        ';


        echo $html;

        exit();

        break;
}