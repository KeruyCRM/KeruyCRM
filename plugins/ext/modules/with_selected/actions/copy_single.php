<?php

if (!isset($_GET['path'])) {
    redirect_to('dashboard/page_not_found');
}

$path_info = items::parse_path($_GET['path']);

$current_entity_id = $path_info['entity_id'];
$current_item_id = $path_info['item_id'];

if ($current_item_id == 0) {
    redirect_to('dashboard/page_not_found');
}

$path_info = items::get_path_info($current_entity_id, $current_item_id);

if ($_GET['path'] != $path_info['full_path']) {
    redirect_to('items/info', 'path=' . $path_info['full_path']);
}

//get access schema for current entity
$current_access_schema = users::get_entities_access_schema($current_entity_id, $app_user['group_id']);

//checking access
if (!users::has_access('copy')) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'copy_single':
        $entities_id = $current_entity_id;
        $entity_info = db_find('app_entities', $entities_id);

        //set default parent id
        $parent_item_id = 0;

        //get parent id for sub-entities                                              
        if ($entity_info['parent_id'] > 0) {
            if (strlen($_POST['copy_to']) > 0) {
                $parent_item_id = (int)$_POST['copy_to'];
            }

            //parent id is requried for sub-entities
            if ($parent_item_id == 0) {
                echo '<div class="alert alert-danger">' . TEXT_COPY_ERROR_PARENT_RECORD . '</div>';
                exit();
            }
        }

        //copy records             
        if ($current_item_id > 0) {
            $settigns = (isset($_POST['settings']) ? $_POST['settings'] : []);
            $copy_process = new items_copy($current_entity_id, $current_item_id, $settigns);

            if ($parent_item_id > 0) {
                $copy_process->set_parent_item_id($parent_item_id);
            }

            $number_of_copies = _POST('number_of_copies');

            if ($new_item_id = $copy_process->run()) {
                $path_info = items::get_path_info($current_entity_id, $new_item_id);

                echo '
                    <div class="alert alert-success">' . TEXT_COPYING_COMPLETED . '</div>
                    <p><a href="' . url_for(
                        'items/info',
                        'path=' . $path_info['full_path']
                    ) . '">' . TEXT_GO_TO_COPIED_RECORD . '</a></p>
                ';

                //run extra copy process if number od copies more then 1
                for ($i = 2; ($i <= $number_of_copies and $i <= 100); $i++) {
                    $copy_process->run();
                }
            }
        }


        exit();
        break;
}