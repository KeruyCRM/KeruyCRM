<?php

namespace Tools\FieldsTypes;

class Fieldtype_google_map_directions
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_API_KEY,
            'name' => 'api_key',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_API_KEY_TIP,
            'params' => ['class' => 'form-control input-xlarge required']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ADDRESS . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_address_pattern',
                    \K::$fw->TEXT_SELECT_FIELD,
                    [
                        'fieldtype_input',
                        'fieldtype_input_masked',
                        'fieldtype_mysql_query',
                        'fieldtype_textarea',
                        'fieldtype_textarea_wysiwyg',
                        'fieldtype_text_pattern',
                        'fieldtype_text_pattern_static'
                    ]
                ),
            'name' => 'address_pattern',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_ADDRESS_TIP . '<br>' . \K::$fw->TEXT_ADDRESS_PATTERN_INFO,
            'params' => ['class' => 'form-control input-xlarge required']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'map_width',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_WIDTH_INPUT_TIP,
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HEIGHT,
            'name' => 'map_height',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_HEIGHT_INPUT_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $choices = [];
        for ($i = 3; $i <= 20; $i++) {
            $choices[$i] = $i;
        }

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEFAULT_ZOOM,
            'name' => 'zoom',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 11,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_MARKER][] = [
            'title' => \K::$fw->TEXT_LABEL,
            'name' => 'labels',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_LABEL_TIP,
            'params' => ['class' => 'form-control input-xlarge']
        ];
        $cfg[\K::$fw->TEXT_MARKER][] = [
            'title' => \K::$fw->TEXT_LABEL_COLOR,
            'name' => 'label_color',
            'type' => 'colorpicker'
        ];
        $cfg[\K::$fw->TEXT_MARKER][] = [
            'title' => \K::$fw->TEXT_ICONS,
            'name' => 'icons',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_ICONS_TIP,
            'params' => ['class' => 'form-control input-xlarge']
        ];
        $cfg[\K::$fw->TEXT_MARKER][] = [
            'title' => \K::$fw->TEXT_FIELDS_IN_POPUP,
            'name' => 'fields_in_popup',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_FIELDS_IN_POPUP_TIP,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_MODE,
            'name' => 'travel_mode',
            'type' => 'dropdown',
            'choices' => [
                '' => '',
                'DRIVING' => 'DRIVING',
                'BICYCLING' => 'BICYCLING',
                'TRANSIT' => 'TRANSIT',
                'WALKING' => 'WALKING'
            ],
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_MODE_TIP,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_OPTIMIZE_WAYPOINTS,
            'name' => 'optimizeWaypoints',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_PROVIDE_ROUTE_ALTERNATIVES,
            'name' => 'provideRouteAlternatives',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_AVOID_FERRIES,
            'name' => 'avoidFerries',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_AVOID_HIGHWAYS,
            'name' => 'avoidHighways',
            'type' => 'checkbox'
        ];
        $cfg[\K::$fw->TEXT_DIRECTIONS][] = [
            'title' => \K::$fw->TEXT_AVOID_TOLLS,
            'name' => 'avoidTolls',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_DISTANCE][] = [
            'title' => \K::$fw->TEXT_CALCULATE_TOTAL_DISTANCE,
            'name' => 'calculate_total_distance',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_DISTANCE][] = [
            'title' => \K::$fw->TEXT_DISPLAY_AS,
            'name' => 'unit_system',
            'type' => 'dropdown',
            'choices' => ['kilometers' => \K::$fw->TEXT_KILOMETERS, 'miles' => \K::$fw->TEXT_MILES],
            'params' => ['class' => 'form-control input-medium']
        ];

        $choices = ['' => ''];
        $fields_query = db_query(
            "select id, name from app_fields where type in ('fieldtype_input_numeric') and entities_id = '" . _POST(
                'entities_id'
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $cfg[\K::$fw->TEXT_DISTANCE][] = [
            'title' => \K::$fw->TEXT_SAVE_VALUE,
            'name' => 'save_value_in',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_SAVE_VALUE_TIP,
            'params' => ['class' => 'form-control input-medium']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        global $is_google_map_script, $app_user;

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //skip
        if (!strlen($cfg->get('address_pattern')) or !strlen(
                $options['value']
            ) or isset($options['is_listing']) or isset($options['is_export'])) {
            return '';
        }

        $field_id = $options['field']['id'];

        $access_rules = new access_rules($options['field']['entities_id'], $options['item']);
        $has_update_access = users::has_access('update', $access_rules->get_access_schema());

        //check fields access if have update access
        if ($has_update_access) {
            $fields_access_schema = users::get_fields_access_schema(
                $options['field']['entities_id'],
                $app_user['group_id']
            );
            if (isset($fields_access_schema[$options['field']['id']])) {
                if ($fields_access_schema[$options['field']['id']] == 'view') {
                    $has_update_access = false;
                }
            }
        }

        $html_map_center = '';
        $map_center = [];
        $markers_array = [];

        $html_directions = '';

        //bild markers
        $address_array = preg_split("/\\r\\n|\\r|\\n/", $options['value']);

        $labesl_array = preg_split("/\\r\\n|\\r|\\n/", $cfg->get('labels'));

        $icons_array = preg_split("/\\r\\n|\\r|\\n/", $cfg->get('icons'));

        $fields_in_popup_array = preg_split("/\\r\\n|\\r|\\n/", $cfg->get('fields_in_popup'));

        foreach ($address_array as $address_key => $value) {
            $value = explode("\t", $value);

            $lat = $value[0] ?? '';
            $lng = $value[1] ?? '';
            $current_address = $value[2] ?? '';

            if (strlen($lat) and strlen($lng)) {
                if (!count($map_center)) {
                    $map_center[] = $lat;
                    $map_center[] = $lng;
                }

                //configure marker label
                $label = '';
                if (isset($labesl_array[$address_key])) {
                    if (strlen($labesl_array[$address_key])) {
                        $label = 'label: {text:"' . $labesl_array[$address_key] . '"';

                        if (strlen($cfg->get('label_color'))) {
                            $label .= ',color:"' . $cfg->get('label_color') . '"';
                        }

                        $label .= '},';
                    }
                }

                //configure marker icon
                $icon = '';
                if (isset($icons_array[$address_key])) {
                    if (strlen($icons_array[$address_key])) {
                        $icon = 'icon:"' . $icons_array[$address_key] . '",';
                    }
                }

                //configure marker
                $markers_html = '
		  				var markerLatlng' . $address_key . ' = new google.maps.LatLng(' . $lat . ',' . $lng . ');';

                if (!strlen($cfg->get('travel_mode'))) {
                    $fields_in_popup_html = '';

                    if (isset($fields_in_popup_array[$address_key])) {
                        $fields_in_popup_html .= '
									<table class="table">
										<tbody>';

                        foreach (explode(',', $fields_in_popup_array[$address_key]) as $fields_id) {
                            $field_query = db_query(
                                "select * from app_fields where id='" . (int)trim($fields_id) . "'"
                            );
                            if ($field = db_fetch_array($field_query)) {
                                //prepare field value
                                $value = items::prepare_field_value_by_type($field, $options['item']);

                                $output_options = [
                                    'class' => $field['type'],
                                    'value' => $value,
                                    'field' => $field,
                                    'item' => $options['item'],
                                    'is_listing' => true,
                                    'path' => ''
                                ];

                                $value = trim(fields_types::output($output_options));

                                if (strlen(strip_tags($value)) > 255 and in_array(
                                        $field['type'],
                                        ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea']
                                    )) {
                                    $value = substr(strip_tags($value), 0, 255) . '...';
                                }

                                if (strlen($value)) {
                                    $fields_in_popup_html .= '
											<tr>
												<td valign="top" style="padding-right: 7px;">' . fields_types::get_option(
                                            $field['type'],
                                            'name',
                                            $field['name']
                                        ) . ':</td>
												<td valign="top">' . $value . '</td>
											</tr>';
                                }
                            }
                        }

                        $fields_in_popup_html .= '
										</tbody>
									</table>
									';

                        $fields_in_popup_html = addslashes(
                            str_replace(["\n", "\r", "\n\r"], '', $fields_in_popup_html)
                        );
                    }

                    $markers_html .= '	  						
			  				var marker' . $address_key . ' = new google.maps.Marker({
					            map: map,
					            position: markerLatlng' . $address_key . ',
								  		draggable: ' . ($has_update_access ? 'true' : 'false') . ',
								  		' . $label . '
								  		' . $icon . '		
					        });
								  				
							 	google.maps.event.addListener(marker' . $address_key . ', "click", function() {
				          infowindow.close();//hide the infowindow
				          infowindow.setContent(\'<div id="content"><p>' . str_replace(["\n", "\r", "\n\r"],
                            ' ',
                            nl2br(urldecode($current_address))) . '</p>' . $fields_in_popup_html . '</div>\');
				          infowindow.open(map,marker' . $address_key . ');
				        });	
				          		
				        google.maps.event.addListener(marker' . $address_key . ', "dragend", function(evt){			        		
				          		$.ajax({
											  method: "POST",
											  url: "' . url_for(
                            'items/google_map',
                            'path=' . $options['path'] . '&action=update_latlng_multiple'
                        ) . '",
											  data: { lat: evt.latLng.lat(), lng: evt.latLng.lng(),filed_id: ' . $field_id . ', address_key:' . $address_key . ' } 
											})
								});
			  				';
                }
                $markers_array[$address_key] = $markers_html;
            }
        }

        if (count($map_center)) {
            $html_map_center = '
                    var myLatlng = new google.maps.LatLng(' . $map_center[0] . ',' . $map_center[1] . ');

                      //Got result, center the map and put it out there
                    map.setCenter(myLatlng);	
            ';
        }

        //build directions if mode set
        if (strlen($cfg->get('travel_mode')) and count($markers_array) > 1) {
            $waypts_html = '';

            $first_marker_key = false;
            $last_marker_key = 0;

            //print_rr($markers_array);

            foreach ($markers_array as $k => $v) {
                $waypts_html .= '
                            waypts.push({
                                location: markerLatlng' . $k . ',
                                stopover: true
                              });
                            ';

                if ($first_marker_key === false) {
                    $first_marker_key = $k;
                }

                $last_marker_key = $k;
            }

            $html_directions = '
                var isDirectionInit = false
	  	var directionsService = new google.maps.DirectionsService();
	        var directionsRenderer = new google.maps.DirectionsRenderer({
	  						map: map,
	  						draggable: ' . ($has_update_access ? 'true' : 'false') . ',
	  					});
	  								
		 directionsRenderer.addListener("directions_changed", function() {
                        
                        if(!isDirectionInit)
                        {
                            isDirectionInit = true
                            return true
                        }
                        
                        var lat = [];
                        var lng = [];
                        result = directionsRenderer.getDirections()				
                        var myroute = result.routes[0];
		        for (var i = 0; i < myroute.legs.length; i++) 
		        {
		          //console.log(myroute.legs[i].start_location.lat())
		          lat[i] = myroute.legs[i].start_location.lat();
	  					lng[i] = myroute.legs[i].start_location.lng();
		        }
	  					
                        i=myroute.legs.length-1;
                        lat[i+1] = myroute.legs[i].end_location.lat();
                         lng[i+1] = myroute.legs[i].end_location.lng();

                        //console.log(lat)
                        //console.log(lng)
                        
                        //console.log(myroute.legs)
                        
                        calculateDistance(myroute.legs)

                        $.ajax({
                                  method: "POST",
                                  url: "' . url_for(
                    'items/google_map',
                    'path=' . $options['path'] . '&action=update_latlng_directions'
                ) . '",
                                  data: { lat: lat, lng: lng,filed_id: ' . $field_id . ' } 
                                })
						  		
                });
	  	  					  
            var waypts = [];

            ' . $waypts_html . '
	  	
  	    directionsService.route(
            {
                origin:  markerLatlng' . $first_marker_key . ',
                destination: markerLatlng' . $last_marker_key . ',
                travelMode: "' . $cfg->get('travel_mode') . '",
                waypoints: waypts,
                optimizeWaypoints: ' . ($cfg->get('optimizeWaypoints') == 1 ? 'true' : 'false') . ',
                provideRouteAlternatives: ' . ($cfg->get('provideRouteAlternatives') == 1 ? 'true' : 'false') . ',
                avoidFerries: ' . ($cfg->get('avoidFerries') == 1 ? 'true' : 'false') . ',
                avoidHighways: ' . ($cfg->get('avoidHighways') == 1 ? 'true' : 'false') . ',
                avoidTolls: ' . ($cfg->get('avoidTolls') == 1 ? 'true' : 'false') . ',                
            },
            function(response, status) {
              if (status === "OK") {
                directionsRenderer.setDirections(response);
                
                const route = response.routes[0];
                //console.log(route.legs)
                calculateDistance(route.legs)
              } else {
                window.alert("Directions request failed due to " + status);
              }
            });
                        
  	';
        }

        if (count($markers_array) or strlen($html_directions)) {
            $html = '';

            if ($is_google_map_script != true) {
                $html .= '<script src="https://maps.googleapis.com/maps/api/js?key=' . $cfg->get(
                        'api_key'
                    ) . '&libraries=places"></script>';
                $is_google_map_script = true;
            }

            $html .= '
  				
  				<script>
					  				
  					$(function(){
  						  				
						  var mapOptions = {
						    zoom: ' . $cfg->get('zoom') . ',    
						  }
						  
						  var map = new google.maps.Map(document.getElementById("goolge_map_container' . $field_id . '"), mapOptions);
						  
						  geocoder = new google.maps.Geocoder();
						
						  ' . $html_map_center . '	
						  		
						  var infowindow = new google.maps.InfoWindow();
                         
			        ' . implode('', $markers_array) . '
			        		
			        ' . $html_directions . '
						  		
						})
																		
						</script>  
					';

            $map_width = (strlen($cfg->get('map_width')) ? $cfg->get('map_width') : '470px');
            $map_height = (strlen($cfg->get('map_height')) ? $cfg->get('map_height') : '470px');

            if (!strstr($map_width, '%') and !strstr($map_width, 'px')) {
                $map_width = $map_width . 'px';
            }
            if (!strstr($map_height, '%') and !strstr($map_height, 'px')) {
                $map_height = $map_height . 'px';
            }

            $html .= '			
		<div id="goolge_map_container' . $field_id . '" style="width:100%; max-width: ' . $map_width . '; height: ' . $map_height . ';"></div> 
            ';

            $html .= '
            <script>
                function calculateDistance(legs)
                {
                    let distance = 0

                    legs.forEach((v)=>{                        
                        distance += v.distance.value
                    })
                    
                    ' . ($cfg->get(
                    'unit_system'
                ) == 'kilometers' ? 'distance = (distance/1000).toFixed(3)' : 'distance = (distance/1609).toFixed(3)') . '

                    if(distance)
                    {   
                        $(".google-map-distance").remove()
                        $("#goolge_map_container' . $field_id . '").after("<div class=\'google-map-distance\' style=\'width: 100%; max-width:' . $map_width . '\'>' . ($cfg->get(
                    'unit_system'
                ) == 'kilometers' ? \K::$fw->TEXT_TOTAL_DISTANCE_IN_KILOMETERS : \K::$fw->TEXT_TOTAL_DISTANCE_IN_MILES) . ': "+distance+"</div>")
                            
                        ' . ($cfg->get('save_value_in') > 0 ? '
                            $.ajax({
                                  method: "POST",
                                  url: "' . url_for(
                        'items/google_map',
                        'path=' . $options['path'] . '&action=save_value_in'
                    ) . '",
                                  data: { distance: distance, filed_id: ' . $cfg->get('save_value_in') . ' } 
                                }).done(function(response){
                                    if(response=="UPDATED")
                                    {                                        
                                        $(".form-group-' . $cfg->get('save_value_in') . ' td").html(\'<a href=""><i class="fa fa-refresh" aria-hidden="true"></i></a>\')
                                    }
                                })
                        ' : '') . '
                    }
                }
            </script>
                ';

            return $html;
        } else {
            return '';
        }
    }

    public static function update_items_fields($entities_id, $items_id, $item_info = false)
    {
        if (isset(\K::$fw->app_fields_cache[$entities_id])) {
            $forceCommit = \K::model()->forceCommit();

            foreach (\K::$fw->app_fields_cache[$entities_id] as $fields) {
                if ($fields['type'] == 'fieldtype_google_map_directions') {
                    $fields_id = $fields['id'];

                    $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

                    //skip if no pattern setup
                    if (!strlen($cfg->get('address_pattern'))) {
                        if ($forceCommit) {
                            \K::model()->commit();
                        }

                        return false;
                    }

                    //get item info
                    if (!$item_info) {
                        /*$item_info_query = db_query("select * from app_entity_{$entities_id} where id={$items_id}");
                        $item_info = db_fetch_array($item_info_query);*/

                        $item_info = \K::model()->db_fetch_one('app_entity_' . $entities_id, [
                            'id = ?',
                            $items_id
                        ]);
                    }

                    $is_address_updated = false;
                    $address_values = [];
                    $address_pattern_array = preg_split("/\\r\\n|\\r|\\n/", $cfg->get('address_pattern'));

                    foreach ($address_pattern_array as $address_key => $address_pattern) {
                        //get address by pattern
                        $pattern_options = [
                            'field' => $fields,
                            'item' => $item_info,
                            'custom_pattern' => $address_pattern,
                            'path' => $entities_id . '-' . $items_id,
                        ];

                        $fieldtype_text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
                        $use_address = urlencode(\K::fw()->clean($fieldtype_text_pattern->output($pattern_options)));

                        $lat = '';
                        $lng = '';
                        $current_address = '';

                        //get current address
                        if (strlen($item_info['field_' . $fields_id])) {
                            $item_address_array = preg_split("/\\r\\n|\\r|\\n/", $item_info['field_' . $fields_id]);

                            if (isset($item_address_array[$address_key])) {
                                if (strlen($item_address_array[$address_key])) {
                                    $value = explode("\t", $item_address_array[$address_key]);

                                    $address_values[$address_key] = $item_address_array[$address_key];

                                    $lat = $value[0];
                                    $lng = $value[1];
                                    $current_address = $value[2];
                                }
                            }
                        }

                        //update address if it needs
                        if ((!strlen($lat) or $use_address != $current_address) and strlen($use_address)) {
                            $url = "https://maps.google.com/maps/api/geocode/json?key=" . $cfg->get(
                                    'api_key'
                                ) . "&address=" . $use_address;

                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            $result = curl_exec($ch);
                            curl_close($ch);

                            $result = json_decode($result, true);

                            //print_rr($result);

                            if (isset($result['error_message'])) {
                                \K::flash()->addMessage(
                                    \K::$fw->TEXT_FIELD . ' "' . $fields['name'] . '": ' . $result['error_message'],
                                    'error'
                                );
                            } else {
                                $lat = $result['results'][0]['geometry']['location']['lat'];
                                $lng = $result['results'][0]['geometry']['location']['lng'];

                                $value = $lat . "\t" . $lng . "\t" . $use_address;

                                $address_values[$address_key] = $value;

                                $is_address_updated = true;

                                //echo $value;	  
                                //exit();
                            }
                        } elseif (!strlen($use_address)) {
                            $address_values[$address_key] = '';
                            $is_address_updated = true;
                        }
                    }

                    if (count($address_values) and $is_address_updated) {
                        /*db_query(
                            "update app_entity_{$entities_id} set field_{$fields_id}='" . db_input(
                                implode("\n", $address_values)
                            ) . "' where id='" . db_input($items_id) . "'"
                        );*/

                        \K::model()->db_update(
                            'app_entity_' . $entities_id,
                            ['field_' . $fields_id => implode("\n", $address_values)],
                            ['id = ?', $items_id]
                        );
                    }
                }
            }

            if ($forceCommit) {
                \K::model()->commit();
            }
        }
    }
}
