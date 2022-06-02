<?php

switch ($app_module_action) {
    case 'getMapView':
        header('Content-Type: application/json');

        $image_map = new image_map_nested(_GET('fields_id'));

        $image_map->set_path($app_path);
        $image_map->set_filename($_GET['map_filename']);

        $data = $image_map->get_data();

        echo $data;

        exit();
        break;
    case 'saveElementsPositions':
        if (isset($_POST['markers'])) {
            image_map_nested::save_markers();
        }

        exit();
        break;
}