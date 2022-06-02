<?php

if (!app_session_is_registered('report_page_filter')) {
    $report_page_filter = 0;
    app_session_register('report_page_filter');
}

switch ($app_module_action) {
    case 'set_filter':
        $report_page_filter = $_POST['report_page_filter'];

        redirect_to('ext/report_page/reports');
        break;
    case 'save':

        $sql_data = [
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'type' => $_POST['type'],
            'use_editor' => $_POST['use_editor'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],

            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'save_filename' => $_POST['save_filename'],
            'save_as' => (isset($_POST['save_as']) ? implode(',', $_POST['save_as']) : ''),
            'page_orientation' => $_POST['page_orientation'],
            'sort_order' => $_POST['sort_order'],
        ];

        if (isset($_GET['id'])) {
            $page = db_find('app_ext_report_page', _GET('id'));
            if ($page['entities_id'] != _POST('entities_id')) {
                reports::delete_reports_by_type('report_page' . _GET('id'));
                //export_templates_blocks::delele_blocks_by_template_id(_GET('id'));
            }

            db_perform('app_ext_report_page', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_report_page', $sql_data);
        }

        redirect_to('ext/report_page/reports');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_report_page where id='" . db_input($_GET['id']) . "'");

            reports::delete_reports_by_type('report_page' . _GET('id'));

            //export_templates_blocks::delele_blocks_by_template_id(_GET('id'));

            $alerts->add(TEXT_WARN_DELETE_REPORT_SUCCESS, 'success');

            redirect_to('ext/report_page/reports');
        }

        break;
    case 'sort':
        if (isset($_POST['reports'])) {
            $sort_order = 0;
            foreach (explode(',', $_POST['reports']) as $v) {
                $sql_data = ['sort_order' => $sort_order];
                db_perform(
                    'app_ext_report_page',
                    $sql_data,
                    'update',
                    "id='" . db_input(str_replace('reports_', '', $v)) . "'"
                );
                $sort_order++;
            }
        }
        exit();
        break;

    case 'copy':
        $report_id = _get::int('id');
        $report_query = db_query("select * from app_ext_report_page where id='" . $report_id . "'");
        if ($report = db_fetch_array($report_query)) {
            unset($report['id']);
            $report['name'] = $report['name'] . ' (' . TEXT_EXT_NAME_COPY . ')';
            db_perform('app_ext_report_page', $report);
            $new_report_id = db_insert_id();
            /*
            //copy blocks
            $id_to_replace = [];
            $blocks_query = db_query("select * from app_ext_report_page_blocks where reports_id={$report_id}");
            while($blocks = db_fetch_array($blocks_query))
            {
                $block_id = $blocks['id'];
                
                unset($blocks['id']);
                $blocks['reports_id'] = $new_template_id;
                db_perform('app_ext_report_page_blocks', $blocks);
                $new_block_id = db_insert_id();
                
                $id_to_replace[$block_id] = $new_block_id;
            }
            
            //prepare parent_id
            foreach($id_to_replace as $block_id=>$new_block_id)
            {
                db_query("update app_ext_report_page_blocks set parent_id={$new_block_id} where parent_id={$block_id} and reports_id={$new_report_id}");
            }
             * 
             */
        }

        redirect_to('ext/report_page/reports');
        break;
}
