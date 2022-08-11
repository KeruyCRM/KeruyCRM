<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_google_map
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_API_KEY,
            'name' => 'api_key',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_API_KEY_TIP,
            'params' => ['class' => 'form-control input-xlarge required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ADDRESS . \Models\Main\Fields::get_available_fields_helper(
                    \K::$fw->POST['entities_id'],
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
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_ADDRESS_PATTERN_INFO,
            'params' => ['class' => 'form-control input-xlarge required']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'map_width',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_WIDTH_INPUT_TIP,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
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

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_ZOOM,
            'name' => 'zoom',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 11,
            'params' => ['class' => 'form-control input-small']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //skip
        if (!strlen($cfg->get('address_pattern')) or !strlen(
                $options['value']
            ) or isset($options['is_listing']) or isset($options['is_export'])) {
            return '';
        }

        $value = explode("\t", $options['value']);

        $lat = $value[0];
        $lng = $value[1];
        $current_address = $value[2];

        if (strlen($lat) and strlen($lng)) {
            $html = '';

            if (\K::$fw->is_google_map_script != true) {
                $html .= '<script src="https://maps.googleapis.com/maps/api/js?key=' . $cfg->get(
                        'api_key'
                    ) . '&libraries=places"></script>';
                \K::$fw->is_google_map_script = true;
            }

            $field_id = $options['field']['id'];

            $access_rules = new \Models\Main\Access_rules($options['field']['entities_id'], $options['item']);
            $has_update_access = \Models\Main\Users\Users::has_access('update', $access_rules->get_access_schema());

            $html .= '
  				<script>			
  					$(function(){		
						  var mapOptions = {
						    zoom: ' . $cfg->get('zoom') . ',    
						  }
						  
						  var map = new google.maps.Map(document.getElementById("goolge_map_container' . $field_id . '"), mapOptions);
						  
						  geocoder = new google.maps.Geocoder();
						
						  var myLatlng = new google.maps.LatLng(' . $lat . ',' . $lng . ');
                         
			        //Got result, center the map and put it out there
			        map.setCenter(myLatlng);
			                            
			        var marker = new google.maps.Marker({
			            map: map,
			            position: myLatlng,
						  		draggable: ' . ($has_update_access ? 'true' : 'false') . '
			        });
						  		
					var infowindow = new google.maps.InfoWindow();
						  		
					google.maps.event.addListener(marker, "click", function() {
			        infowindow.close();//hide the infowindow
			        infowindow.setContent(\'<div id="content">' . str_replace(["\n", "\r", "\n\r"],
                    ' ',
                    nl2br(urldecode($current_address))) . '</div>\');
			          infowindow.open(map,marker);
			        });	
			          		
			        google.maps.event.addListener(marker, "dragend", function(evt){			        		
			          		$.ajax({
										  method: "POST",
										  url: "' . \Helpers\Urls::url_for(
                    'main/items/google_map/update_latlng',
                    'path=' . $options['path']
                ) . '",
										  data: { lat: evt.latLng.lat(), lng: evt.latLng.lng(),filed_id: ' . $field_id . ' } 
										})
							});
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
                if ($fields['type'] == 'fieldtype_google_map') {
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

                    //get address by pattern
                    $pattern_options = [
                        'field' => $fields,
                        'item' => $item_info,
                        'custom_pattern' => $cfg->get('address_pattern'),
                        'path' => $entities_id . '-' . $items_id,
                    ];

                    $fieldtype_text_pattern = new \Tools\FieldsTypes\Fieldtype_text_pattern();
                    $use_address = urlencode(strip_tags($fieldtype_text_pattern->output($pattern_options)));

                    //skip if address empty
                    if (!strlen($use_address)) {
                        /*db_query(
                            "update app_entity_{$entities_id} set field_{$fields_id}='' where id='" . db_input(
                                $items_id
                            ) . "'"
                        );*/

                        \K::model()->db_update(
                            'app_entity_' . $entities_id,
                            ['field_' . $fields_id => ''],
                            ['id = ?', $items_id]
                        );

                        if ($forceCommit) {
                            \K::model()->commit();
                        }

                        return false;
                    }

                    $lat = '';
                    $lng = '';
                    $current_address = '';

                    //get current address
                    if (strlen($item_info['field_' . $fields_id])) {
                        $value = explode("\t", $item_info['field_' . $fields_id]);

                        $lat = $value[0];
                        $lng = $value[1];
                        $current_address = $value[2];
                    }

                    //update address if it needs
                    if (!strlen($lat) or $use_address != $current_address) {
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

                        if (isset($result['error_message'])) {
                            \K::flash()->addMessage(
                                \K::$fw->TEXT_FIELD . ' "' . $fields['name'] . '": ' . $result['error_message'],
                                'error'
                            );
                        } else {
                            $lat = $result['results'][0]['geometry']['location']['lat'];
                            $lng = $result['results'][0]['geometry']['location']['lng'];

                            $value = $lat . "\t" . $lng . "\t" . $use_address;

                            //echo $value;

                            /*db_query(
                                "update app_entity_{$entities_id} set field_{$fields_id}='" . db_input(
                                    $value
                                ) . "' where id='" . db_input($items_id) . "'"
                            );*/
                            \K::model()->db_update('app_entity_' . $entities_id, [
                                'field_' . $fields_id => $value
                            ], ['id = ?', $items_id]);
                        }
                    }
                }
            }

            if ($forceCommit) {
                \K::model()->commit();
            }
        }
    }
}