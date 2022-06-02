<?php

if (isset($_GET['path'])) {
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path'];
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title     
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);

    require(component_path('items/navigation'));
}
?>

    <h3 class="page-title"><?php
        echo $reports['name'] ?></h3>

<?php

$heading_field_id = fields::get_heading_id($reports['entities_id']);

if (!$heading_field_id) {
    echo '<div class="alert alert-warning">' . TEXT_ERROR_NO_HEADING_FIELD . '</div>';
} else {
    if (!in_array(
        $app_heading_fields_cache[$heading_field_id]['type'],
        ['fieldtype_input', 'fieldtype_textarea', 'fieldtype_textarea_wysiwyg']
    )) {
        echo '<div class="alert alert-warning">' . TEXT_ERROR_HEADING_FIELD_ONLY_INPUT_SUPPORT . '</div>';
    } else {
        $html = '
  				<div class="mind-map-iframe-box mind-map-iframe-box-0">
  		 			<div class="mind-map-fullscreen-action" data_field_id="0"><i class="fa fa-arrows-alt"></i></div>
      			<iframe src="' . url_for(
                'ext/mind_map_reports/view_map',
                'id=' . $reports['id'] . (isset($_GET['path']) ? '&path=' . $_GET['path'] : '')
            ) . '" class="mind-map-iframe mind-map-iframe-0" scrolling="no" frameborder="no"></iframe>
      		</div>
      		<script>

					 $(function(){				
    					resize_mind_map_iframe_field(0)
    		
						 $( window ).resize(function() {
							 resize_mind_map_iframe_field(0)
						 });  		 					  		 					
					 })
  		 					
  		 					
					 
					</script>				
      		';

        echo $html;
    }
}	
