<?php

if (!app_session_is_registered('export_templates_filter')) {
    $export_templates_filter = 0;
    app_session_register('export_templates_filter');
}

switch ($app_module_action) {
    case 'copy':
        $templates_id = _get::int('templates_id');
        $templates_query = db_query("select * from app_ext_export_templates where id='" . $templates_id . "'");
        if ($templates = db_fetch_array($templates_query)) {
            unset($templates['id']);
            $templates['name'] = $templates['name'] . ' (' . TEXT_EXT_NAME_COPY . ')';
            db_perform('app_ext_export_templates', $templates);
            $new_template_id = db_insert_id();

            //copy blocks
            $id_to_replace = [];
            $blocks_query = db_query(
                "select * from app_ext_items_export_templates_blocks where templates_id={$templates_id}"
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $block_id = $blocks['id'];

                unset($blocks['id']);
                $blocks['templates_id'] = $new_template_id;
                db_perform('app_ext_items_export_templates_blocks', $blocks);
                $new_block_id = db_insert_id();

                $id_to_replace[$block_id] = $new_block_id;
            }

            //prepare parent_id
            foreach ($id_to_replace as $block_id => $new_block_id) {
                db_query(
                    "update app_ext_items_export_templates_blocks set parent_id={$new_block_id} where parent_id={$block_id} and templates_id={$new_template_id}"
                );
            }
        }

        redirect_to('ext/templates/export_templates');
        break;
    case 'save_description':
        $sql_data = ['description' => $_POST['export_templates_description']];

        db_perform('app_ext_export_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");

        if (IS_AJAX) {
            exit();
        }

        redirect_to('ext/templates/export_templates');
        break;
    case 'set_export_templates_filter':
        $export_templates_filter = $_POST['export_templates_filter'];

        redirect_to('ext/templates/export_templates');
        break;
    case 'sort_templates':
        if (isset($_POST['templates'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['templates']) as $v) {
                $sql_data = ['sort_order' => $sort_order];
                db_perform(
                    'app_ext_export_templates',
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
            'label_size' => $_POST['label_size'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'template_filename' => $_POST['template_filename'],
            'save_as' => (isset($_POST['save_as']) ? implode(',', $_POST['save_as']) : ''),
            'save_attachments' => (isset($_POST['save_attachments']) ? implode(',', $_POST['save_attachments']) : ''),
            'template_css' => $_POST['template_css'],
            'page_orientation' => $_POST['page_orientation'],
            'split_into_pages' => $_POST['split_into_pages'],
            'template_header' => $_POST['template_header'],
            'template_footer' => $_POST['template_footer'],
        ];

        if (isset($_GET['id'])) {
            $export_templates = db_find('app_ext_export_templates', _GET('id'));
            if ($export_templates['entities_id'] != _POST('entities_id')) {
                reports::delete_reports_by_type('export_templates' . _GET('id'));

                export_templates_blocks::delele_blocks_by_template_id(_GET('id'));
            }

            db_perform('app_ext_export_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
            $template_id = _GET('id');
        } else {
            db_perform('app_ext_export_templates', $sql_data);
            $template_id = db_insert_id();
        }


        //upload file
        if (strlen($_FILES['filename']['name']) > 0 and (in_array(
                    $_FILES['filename']['type'],
                    ['application/vnd.openxmlformats-officedocument.wordprocessingml.document']
                ) or substr($_FILES['filename']['name'], -5) == '.docx')) {
            $filename = $template_id . '-' . $_FILES['filename']['name'];
            if (move_uploaded_file($_FILES['filename']['tmp_name'], DIR_WS_TEMPLATES . $filename)) {
                db_query(
                    "update app_ext_export_templates set filename = '" . db_input(
                        $filename
                    ) . "' where id='" . $template_id . "'"
                );
            }
        }

        redirect_to('ext/templates/export_templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_export_templates where id='" . db_input($_GET['id']) . "'");

            reports::delete_reports_by_type('export_templates' . _GET('id'));

            export_templates_blocks::delele_blocks_by_template_id(_GET('id'));

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/templates/export_templates');
        }
        break;

    case 'get_parent_fields':
        $html = '';

        if (($entities_id = $app_entities_cache[$_POST['entities_id']]['parent_id']) > 0) {
            $html = export_templates::get_available_fields_for_all_entities($entities_id, $_POST['editor']);

            if ($_POST['editor'] == 'template_header') {
                $html .= "
	  			<script>
	  				$('.template_header').click(function(){
					    html = $(this).html().trim();
					    CKEDITOR.instances.template_header.insertText(html);
					  })
	  			</script>
	  			";
            }

            if ($_POST['editor'] == 'template_footer') {
                $html .= "
	  			<script>	
					  $('.template_footer').click(function(){
					    html = $(this).html().trim();  				
					    CKEDITOR.instances.template_footer.insertText(html);
					  })
	  			</script>
	  			";
            }
        }

        echo $html;

        exit();
        break;

    case 'get_attachments_fields':

        $obj = $_GET['id'] > 0 ? db_find('app_ext_export_templates', _GET('id')) : db_show_columns(
            'app_ext_export_templates'
        );

        $choices = [];

        $fields_query = db_query(
            "select id,name,type,configuration from app_fields where entities_id='" . _POST(
                'entities_id'
            ) . "' and type in (" . fields_types::get_attachments_types_list() . ")"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $choices['comments'] = TEXT_COMMENTS;

        $html = '
                
                <div class="form-group" form_display_rules="save_as:zip">
                    <label class="col-md-3 control-label">' . tooltip_icon(
                TEXT_EXT_SAVE_ATTACHMENTS_ZIP_TIP
            ) . TEXT_ATTACHMENTS . '</label>
                    <div class="col-md-9">	
                        ' . select_tag(
                'save_attachments[]',
                $choices,
                $obj['save_attachments'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) . '	  	  
                    </div>			
                </div>
            ';

        echo $html;

        exit();
        break;
}