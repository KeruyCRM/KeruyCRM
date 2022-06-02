<?php

//check report and access
$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
if ($reports_info = db_fetch_array($reports_info_query)) {
    $access_schema = users::get_entities_access_schema($reports_info['entities_id'], $app_user['group_id']);

    if (!users::has_access('copy', $access_schema)) {
        redirect_to('dashboard/access_forbidden');
    }
} else {
    redirect_to('dashboard/page_not_found');
}

$current_entity_id = $reports_info['entities_id'];

switch ($app_module_action) {
    case 'copy_selected':
        $entities_id = $reports_info['entities_id'];
        $entity_info = db_find('app_entities', $entities_id);

        //set default parent id
        $parent_item_id = 0;

        //get parent id for sub-entities                                              
        if ($entity_info['parent_id'] > 0) {
            if (strlen($_POST['copy_to']) > 0) {
                $path_info = items::get_path_info($entity_info['parent_id'], (int)$_POST['copy_to']);
                $path_array = explode('/', $path_info['full_path'] . '/' . $entities_id);
                $breadcrumb = items::get_breadcrumb($path_array);

                $go_to_title = [];
                foreach ($breadcrumb as $v) {
                    $go_to_title[] = $v['title'];
                }

                $go_to_title = implode(' <i class="fa fa-angle-right"></i> ', $go_to_title);
                $go_to_url = url_for('items/items', 'path=' . $path_info['full_path'] . '/' . $entities_id);

                $parent_item_id = (int)$_POST['copy_to'];
            }

            //parent id is requried for sub-entities
            if ($parent_item_id == 0) {
                echo '<div class="alert alert-danger">' . TEXT_COPY_ERROR_PARENT_RECORD . '</div>';
                exit();
            }
        } else {
            $entity_cfg = entities::get_cfg($entities_id);
            $go_to_title = (strlen(
                $entity_cfg['listing_heading']
            ) > 0 ? $entity_cfg['listing_heading'] : $entity_info['name']);
            $go_to_url = url_for('items/items', 'path=' . $entities_id);
        }

        $number_of_copies = _POST('number_of_copies');

        //copy records             
        if (count($app_selected_items[$_GET['reports_id']]) > 0) {
            foreach ($app_selected_items[$_GET['reports_id']] as $item_id) {
                $settigns = (isset($_POST['settings']) ? $_POST['settings'] : []);
                $copy_process = new items_copy($current_entity_id, $item_id, $settigns);

                if ($parent_item_id > 0) {
                    $copy_process->set_parent_item_id($parent_item_id);
                }

                for ($i = 1; ($i <= $number_of_copies and $i <= 100); $i++) {
                    $new_item_id = $copy_process->run();
                }
            }

            echo '
                <div class="alert alert-success">' . TEXT_COPYING_COMPLETED . '</div>
                <p>' . TEXT_GO_TO . ' <a href="' . $go_to_url . '">' . $go_to_title . '</a></p>
              ';
        }


        exit();
        break;
}