<?php

namespace Tools\FieldsTypes;

class Fieldtype_mapbbcode
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_MAPBBCODE_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];
        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_POSITION,
            'name' => 'default_position',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT_POSITION_TIP,
            'params' => ['class' => 'form-control input-medium']
        ];

        $choices = [];
        for ($i = 3; $i <= 18; $i++) {
            $choices[$i] = $i;
        }

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_ZOOM,
            'name' => 'default_zoom',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 8,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_COORDINATES_IN_FORM,
            'name' => 'hide_coordinates',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'rows' => '3',
            'class' => 'form-control input-xlarge ' . ($field['is_heading'] == 1 ? ' autofocus' : '') . ' fieldtype_mapbbcode field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '')
        ];

        if ($cfg->get('hide_coordinates') == 1) {
            $attributes['style'] = 'display:none';
        }

        $html = textarea_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);

        $map_id = $field['id'];

        $map_params = [];

        if (strlen($cfg->get('default_position')) > 0) {
            $map_params[] = 'defaultPosition: [' . trim(preg_replace('/ +/', ',', $cfg->get('default_position'))) . ']';
        }

        if (strlen($cfg->get('default_zoom')) > 0) {
            $map_params[] = 'defaultZoom: ' . $cfg->get('default_zoom');
        }

        if ($cfg->get('hide_coordinates') == 1) {
            $html .= '<a class="btn btn-default" href="javascript: mapbb' . $map_id . '_edit();"><i class="fa fa-map-marker" aria-hidden="true"></i> ' . \K::$fw->TEXT_OPEN_MAP_EDITOR . '</a>';
        } else {
            $html .= '<a href="javascript: mapbb' . $map_id . '_edit();"><i class="fa fa-map-marker" aria-hidden="true"></i> ' . \K::$fw->TEXT_OPEN_MAP_EDITOR . '</a>';
        }

        $html .= '				
				<div id="mapbb' . $map_id . '_edit"></div>						
				<script>
					var mapBB' . $map_id . ' = "";	
					$(function(){		
						 mapBB' . $map_id . ' = new MapBBCode({' . implode(',', $map_params) . '});						   						
					})			
							
				  function mapbb' . $map_id . '_edit() 
					{								
					    mapBB' . $map_id . '.editor(\'mapbb' . $map_id . '_edit\', document.getElementById(\'fields_' . $map_id . '\'), function(res) {
					        if( res !== null )
					    		{					    		  	
					            $(\'#fields_' . $map_id . '\').val(res)
					        }
					    });
					}			
				</script>
		';

        return $html;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        $html = '';

        if (isset($options['is_export'])) {
            return $options['value'];
        } else {
            if (strlen($options['value'])) {
                $map_id = $options['field']['id'];

                $html = '
						<div id="mapbb' . $map_id . '"></div>
						<script>
							$(function(){	
								var mapBB = new MapBBCode({fullFromStart:is_mobile});
								mapBB.show(\'mapbb' . $map_id . '\', \'' . $options['value'] . '\');
							})
						</script>		
				';
            }
        }
        return $html;
    }
}