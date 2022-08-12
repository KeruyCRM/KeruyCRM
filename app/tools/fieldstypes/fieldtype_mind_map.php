<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_mind_map
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_MIND_MAP_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = ['name' => 'hide_field_if_empty', 'type' => 'hidden', 'default' => 1];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [];

        if ($obj['field_' . $field['id']] == 1) {
            $attributes['checked'] = true;
        }

        return '<p class="form-control-static">' . \Helpers\Html::input_checkbox_tag(
                'fields[' . $field['id'] . ']',
                1,
                $attributes
            ) . '</p>';
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if (isset($options['is_listing']) or isset($options['is_export'])) {
            return '';
        } elseif ($options['value'] == 1) {
            return '
      		<div class="mind-map-iframe-box mind-map-iframe-box-' . $options['field']['id'] . '">
  		 			<div class="mind-map-fullscreen-action" data_field_id="' . $options['field']['id'] . '"><i class="fa fa-arrows-alt"></i></div>
      			<iframe src="' . \Helpers\Urls::rl_for(
                    'main/mind_map/single',
                    'items_id=' . $options['item']['id'] . '&entities_id=' . $options['field']['entities_id'] . '&fields_id=' . $options['field']['id']
                ) . '" class="mind-map-iframe mind-map-iframe-' . $options['field']['id'] . '" scrolling="no" frameborder="no"></iframe>
      		</div>
      		<script>
					 $(function(){						 					
						 $( window ).resize(function() {
							 resize_mind_map_iframe_field(' . $options['field']['id'] . ')
						 });
					 })
					</script>				
      		';
        } else {
            return '';
        }
    }
}