<?php

/*
 * api
 * https://yandex.ru/dev/maps/jsapi/doc/2.1/ref/reference/Placemark.html
 */

$reports_entities_query = db_query(
    "select * from app_ext_pivot_map_reports_entities where reports_id=" . $reports['id'] . " order by id limit 1"
);
$reports_entities = db_fetch_array($reports_entities_query);

$cfg = new fields_types_cfg(
    $app_fields_cache[$reports_entities['entities_id']][$reports_entities['fields_id']]['configuration']
);

$map_reports = new pivot_map_reports($reports);


$lat = 55.76;
$lng = 37.64;

if (strlen($reports['latlng'])) {
    $latlng = explode(',', $reports['latlng']);
    $lat = $latlng[0];
    $lng = $latlng[1];
}

$html = '';

$html .= '
    <script src="https://api-maps.yandex.ru/2.1/?apikey=' . $cfg->get('api_key') . '&lang=' . $cfg->get('lang') . '" type="text/javascript"></script>
    ';

$html .= '
<script>
 
$(function(){

	
        ymaps.ready(init);
        function init(){
            var myMap = new ymaps.Map("yandex_map_container", {
                center: [' . $lat . ', ' . $lng . '],
                zoom: ' . $reports['zoom'] . '
            });

            ' . $map_reports->render_yandex_js() . '
                
            //центровка карты по всем точкам
            myMap.setBounds(myMap.geoObjects.getBounds(), {
                checkZoomRange: true,
                zoomMargin: 130
            });
        }
            
			
	set_goolge_map_height();
			
	$( window ).resize(function(){
		set_goolge_map_height();
	})		

})
			
function set_goolge_map_height()
{
    if($(".header").length)
    {
        $("#yandex_map_container").height($( window ).height()-$(".portlet-filters-preview").height()-$(".header").height()-150);
    }
    else
    {
        $("#yandex_map_container").height($( window ).height())
    }
}			
					
</script>
				
<div id="yandex_map_container" style="width:100%;  height: 600px;"></div>
';

echo(count($map_reports->markers) ? $html : '<div class="alert alert-warning">' . TEXT_NO_RECORDS_FOUND . '</div>');

