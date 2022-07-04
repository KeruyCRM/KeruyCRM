<?php

//create default entity report for logged user
$reports_info_query = db_query(
    "select * from app_reports where entities_id='" . db_input(
        $reports['entities_id']
    ) . "' and reports_type='image_map" . $reports['id'] . "' and created_by='" . $app_logged_users_id . "'"
);
if (!$reports_info = db_fetch_array($reports_info_query)) {
    $sql_data = [
        'name' => '',
        'entities_id' => $reports['entities_id'],
        'reports_type' => 'image_map' . $reports['id'],
        'in_menu' => 0,
        'in_dashboard' => 0,
        'listing_order_fields' => '',
        'created_by' => $app_logged_users_id,
    ];

    db_perform('app_reports', $sql_data);
    $fiters_reports_id = db_insert_id();

    reports::auto_create_parent_reports($fiters_reports_id);
} else {
    $fiters_reports_id = $reports_info['id'];
}


if ($app_module_path == 'ext/image_map/view') {
    $filters_preivew = new filters_preview($fiters_reports_id);
    $filters_preivew->redirect_to = 'image_map' . $reports['id'];
    $filters_preivew->has_listing_configuration = false;

    if (isset($_GET['path'])) {
        $filters_preivew->path = $_GET['path'];
        $filters_preivew->include_parent_filters = false;
    }

    echo $filters_preivew->render();
}

echo '
    	<div class="image-map-iframe-box">
				<div class="image-map-fullscreen-action"><i class="fa fa-arrows-alt"></i></div>
     		<iframe src="' . url_for(
        'image_map/reports',
        'reports_id=' . $reports['id'] . '&fiters_reports_id=' . $fiters_reports_id . (isset($_GET['map_id']) ? '&map_id=' . _get::int(
                'map_id'
            ) : '')
    ) . '" class="image-map-iframe" scrolling="no" frameborder="no" ></iframe>
      </div>';
?>

<script>

    $(function () {
        resize_image_map_iframe();

        $(window).resize(function () {
            resize_image_map_iframe()
        });
    })

</script>	
