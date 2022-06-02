<?php

if ($reports['is_public_access'] == 0) {
    require(component_path('ext/map_reports/view_filters'));
} else {
    $fiters_reports_id = default_filters::get_reports_id($reports['entities_id'], 'public_map' . $reports['id']);
}

$map_reports = new map_reports($reports, $fiters_reports_id, $field_info);

if (!$map_reports->latlng) {
    $map_reports->latlng = '45.26329,34.10156';
}

?>

<link rel="stylesheet" href="js/leaflet/src/leaflet.css"/>
<script src="js/leaflet/src/leaflet.js"></script>

<script src="js/mapbbcode-master/src/controls/Leaflet.Search.js"></script>

<div id="map" style="height: 600px"></div>

<script>

    resize_map()

    var map = L.map('map');

    if (L.Control.Search)
        map.addControl(new L.Control.Search({title: '<?php echo TEXT_SEARCH ?>'}));

    map.setView([<?php echo $map_reports->latlng ?>], <?php echo $reports['zoom']?>);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    <?php echo $map_reports->render_js() ?>

    $(function () {
        $(window).resize(function () {
            resize_map()
        });
    })

    function resize_map() {
        if ($('.page-title').length) {
            height = $(window).height() - $('.page-title').height() - $('.portlet-filters-preview').height() - 150;

            if ($('.navbar-items').length) {
                height = height - $('.navbar-items').height() - 50;
            }
        } else {
            height = $(window).height()
        }

        $('#map').css('height', height)
    }

</script>	
