<?php

$html = '<ul class="listing-mobile">';

while ($items = db_fetch_array($items_query)) {
    if (!strlen($items['title']) or !in_array(
            $app_fields_cache[$items['entities_id']][fields::get_heading_id($items['entities_id'])]['type'],
            ['fieldtype_input', 'fieldtype_input_masked', 'fieldtype_input_email']
        )) {
        $title = items::get_heading_field($items['entities_id'], $items['id']);
    } else {
        $title = $items['title'];
    }

    if ($app_entities_cache[$items['entities_id']]['parent_id'] > 0) {
        $path_info = items::get_path_info($items['entities_id'], $items['id']);
        $title = $path_info['parent_name'] . ' <i class="fa fa-angle-right"></i> ' . $title;
    }

    $html .= '
					<li>
						 <table style="width: 100%">
							<tr>							
								<td><div>' . tooltip_text($app_entities_cache[$items['entities_id']]['name']) . '</div>
										<a  class="item_heading_link"href="' . url_for(
            'items/info',
            'path=' . $items['entities_id'] . '-' . $items['id']
        ) . '">' . $title . '</a>
										' . global_search::render_fields_in_listing(
            $items['entities_id'],
            $items['id'],
            $entities_cfg_holder[$items['entities_id']]['fields_in_listing'],
            '',
            $entities_cfg_holder[$items['entities_id']]['cfg'],
            $entities_cfg_holder[$items['entities_id']]['heading_field_id']
        ) . '
								</td>
							</tr>
						</table>
					</li>
						';
}

if ($listing_split->number_of_rows == 0) {
    $html .= '
			    <li>' . TEXT_NO_RECORDS_FOUND . '</li>
			  ';
}

$html .= '</ul>';

//add pager
$html .= '
				<div class="row">
				  <div class="col-md-4 col-sm-12">' . $listing_split->display_count() . '</div>
				  <div class="col-md-8 col-sm-12">' . $listing_split->display_links() . '</div>
				</div>
				';
