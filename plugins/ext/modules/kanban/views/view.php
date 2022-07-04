<?php

if (isset($_GET['path'])) {
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path'];
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);

    $app_breadcrumb[] = ['title' => $reports['name']];

    require(component_path('items/navigation'));
}

$parent_entity_id = $app_entities_cache[$reports['entities_id']]['parent_id'];

$is_top_kanban = ($parent_entity_id and !isset($_GET['path'])) ? true : false;

//reports name with filters
$common_filters = new common_filters($reports['entities_id'], $fiters_reports_id);
$common_filters->redirect_to = $is_top_kanban ? 'kanban-top' . _GET('id') : 'kanban' . _GET('id');
echo $common_filters->render($reports['name']);

//listing highlight rules
$listing_highlight = new listing_highlight($reports['entities_id']);
echo $listing_highlight->render_css();

//get report entity access schema
$current_access_schema = $access_schema = users::get_entities_access_schema(
    $reports['entities_id'],
    $app_user['group_id']
);

$is_kanban_sotrtable = false;

if (users::has_access(
        'update',
        $access_schema
    ) and $app_fields_cache[$reports['entities_id']][$reports['group_by_field']]['type'] != 'fieldtype_autostatus') {
    $is_kanban_sotrtable = true;
}


$filters_preivew = new filters_preview($fiters_reports_id);
$filters_preivew->redirect_to = 'kanban' . $_GET['id'];
$filters_preivew->has_listing_configuration = true;
$filters_preivew->has_listing_configuration_fields = false;


if (isset($_GET['path'])) {
    $filters_preivew->path = $_GET['path'];
    $filters_preivew->include_parent_filters = false;
}

echo $filters_preivew->render();


$field = db_find('app_fields', $reports['group_by_field']);

$cfg = new fields_types_cfg($field['configuration']);

//use global lists if exsit
if ($cfg->get('use_global_list') > 0) {
    $kanban_choices = global_lists::get_choices($cfg->get('use_global_list'), false);
} else {
    $kanban_choices = fields_choices::get_choices($field['id'], false);
}

//print_r($funnel_choices);

foreach ($kanban_choices as $id => $value) {
    $kanban_info_choices[$id]['count'] = 0;

    if (strlen($reports['sum_by_field'])) {
        foreach (explode(',', $reports['sum_by_field']) as $k) {
            $kanban_info_choices[$id][$k] = 0;
        }
    }
}

$kanban_width = ($reports['width'] > 0 ? $reports['width'] : 300);

$app_path = (strlen($app_path) ? $app_path : $reports['entities_id']);

$count_exclude_choices = (strlen($reports['exclude_choices']) ? count(explode(',', $reports['exclude_choices'])) : 0);

$html = '
  	<div class="kanban-div">	
  		<table class="kanban-table" style="width: ' . ($kanban_width * (count(
                $kanban_choices
            ) - $count_exclude_choices)) . 'px">
  			<tr>
  		';

foreach ($kanban_choices as $choices_id => $choices_name) {
    //exclude choices
    if (in_array($choices_id, explode(',', $reports['exclude_choices']))) {
        continue;
    }

    $items_html = '';

    $items_query = kanban::get_items_query(
        $reports['group_by_field'] . ':' . $choices_id,
        $reports,
        $fiters_reports_id
    );
    while ($items = db_fetch_array($items_query)) {
        $kanban_info_choices[$choices_id]['count']++;

        //prepare sum by field
        if (strlen($reports['sum_by_field'])) {
            foreach (explode(',', $reports['sum_by_field']) as $k) {
                if (strlen($items['field_' . $k])) {
                    $kanban_info_choices[$choices_id][$k] += $items['field_' . $k];
                }
            }
        }

        //prepare description
        $description = '';

        if (strlen($reports['fields_in_listing'])) {
            $description .= '<table class="kanban-fields-in-listing">';

            foreach (explode(',', $reports['fields_in_listing']) as $fields_id) {
                $field_query = db_query(
                    "select * from app_fields where id='" . $fields_id . "' order by field(id," . $reports['fields_in_listing'] . ")"
                );
                if ($field = db_fetch_array($field_query)) {
                    //prepare field value
                    $value = items::prepare_field_value_by_type($field, $items);

                    $output_options = [
                        'class' => $field['type'],
                        'value' => $value,
                        'field' => $field,
                        'item' => $items,
                        'is_listing' => true,
                        'redirect_to' => $filters_preivew->redirect_to,
                        'reports_id' => 0,
                        'path' => $app_path . '-' . $items['id']
                    ];

                    $value = trim(fields_types::output($output_options));

                    if (strlen($value) > 255 and in_array(
                            $field['type'],
                            ['fieldtype_textarea_wysiwyg', 'fieldtype_textarea']
                        )) {
                        $value = substr(strip_tags($value), 0, 255) . '...';
                    }

                    if (strlen($value)) {
                        $description .= '
		        			<tr class="kanban-field-' . $field['id'] . '">
		        				<td  class="kanban-field-name" valign="top" style="padding-right: 7px;">' . fields_types::get_option(
                                $field['type'],
                                'name',
                                $field['name']
                            ) . '</td>
		        				<td valign="top">' . $value . '</td>
		        			</tr>';
                    }
                }
            }
            $description .= '</table>';
        }

        //prepare title
        if (strlen($reports['heading_template']) > 0) {
            $fieldtype_text_pattern = new fieldtype_text_pattern();
            $title = $fieldtype_text_pattern->output_singe_text(
                $reports['heading_template'],
                $reports['entities_id'],
                $items
            );
        } else {
            $title = items::get_heading_field($reports['entities_id'], $items['id'], $items);
        }

        //add proejct name to title
        $redirect_to = 'kanban';
        if ($is_top_kanban) {
            $title = '<small>' . items::get_heading_field(
                    $parent_entity_id,
                    $items['parent_item_id']
                ) . '</small><br>' . $title;
            $redirect_to = 'kanban-top';
        }

        $action_buttons = '<div class="kanban-actions-buttons">';


        $access_rules = new access_rules($reports['entities_id'], $items);

        if (users::has_access('update', $access_rules->get_access_schema())) {
            $action_buttons .= '<a href="#"  onClick="open_dialog(\'' . url_for(
                    'items/form',
                    'id=' . $items['id'] . '&path=' . $app_path . '&redirect_to=' . $redirect_to . $reports['id']
                ) . '\')"><i class="fa fa-edit"></i></a>';
        }

        if (users::has_access('delete', $access_rules->get_access_schema())) {
            $check = true;

            if (users::has_access(
                    'delete_creator',
                    $access_rules->get_access_schema()
                ) and $items['created_by'] != $app_user['id']) {
                $check = false;
            }

            if ($check) {
                $action_buttons .= '<a href="#"  onClick="open_dialog(\'' . url_for(
                        'items/delete',
                        'id=' . $items['id'] . '&entity_id=' . $reports['entities_id'] . '&path=' . $app_path . '&redirect_to=' . $redirect_to . $reports['id']
                    ) . '\')"><i class="fa fa-trash-o"></i></a>';
            }
        }

        $action_buttons .= '</div>';

        //reset actions buttons if no access
        if (users::has_users_access_name_to_entity('action_with_assigned', $reports['entities_id'])) {
            if (!users::has_access_to_assigned_item($reports['entities_id'], $items['id'])) {
                $action_buttons = '';
            }
        }

        $items_html .= '
	  		<li id="kanban_item_' . $items['id'] . '" class="kanban-item ' . $listing_highlight->apply(
                $items
            ) . '" ' . (!$is_kanban_sotrtable ? 'style="cursor:default"' : '') . '>
  				' . $action_buttons . '	
  				<a class="kanban-item-title" href="' . url_for(
                'items/info',
                'path=' . $app_path . '-' . $items['id']
            ) . '" target="_blank">' . $title . '</a>
  				' . $description . '
	  		</li>	
  		';
    }

    //prepare sum title  	
    $sum_html = '';
    if (strlen($reports['sum_by_field'])) {
        $sum_html = '<table class="kanban-heading-sum">';
        foreach (explode(',', $reports['sum_by_field']) as $id) {
            $sum_html .= '
  					<tr>
  						<td>' . $app_fields_cache[$reports['entities_id']][$id]['name'] . ':&nbsp;</td>
  						<th>' . fieldtype_input_numeric::number_format(
                    $kanban_info_choices[$choices_id][$id],
                    $app_fields_cache[$reports['entities_id']][$id]['configuration']
                ) . '</th>
  					</tr>';
        }
        $sum_html .= '</table>';
    }


    $color = '';
    if ($cfg->get('use_global_list') > 0) {
        if (strlen($app_global_choices_cache[$choices_id]['bg_color'])) {
            $color = 'style="border-color: ' . $app_global_choices_cache[$choices_id]['bg_color'] . '"';
        }
    } elseif (strlen($app_choices_cache[$choices_id]['bg_color'])) {
        $color = 'style="border-color: ' . $app_choices_cache[$choices_id]['bg_color'] . '"';
    }

    $add_button = '';
    if (users::has_access(
            'create',
            $access_schema
        ) and $app_fields_cache[$reports['entities_id']][$reports['group_by_field']]['type'] != 'fieldtype_autostatus') {
        if ($is_top_kanban) {
            $add_button = '<a class="btn btn-default btn-xs purple kanban-add-button" href="#" onClick="open_dialog(\'' . url_for(
                    'reports/prepare_add_item',
                    'reports_id=' . $fiters_reports_id . '&redirect_to=kanban-top' . $reports['id'] . '&fields[' . $reports['group_by_field'] . ']=' . $choices_id
                ) . '\')"><i class="fa fa-plus" aria-hidden="true"></i></a>';
        } else {
            $add_button = '<a class="btn btn-default btn-xs purple kanban-add-button" href="#" onClick="open_dialog(\'' . url_for(
                    'items/form',
                    'path=' . $app_path . '&redirect_to=kanban' . $reports['id'] . '&fields[' . $reports['group_by_field'] . ']=' . $choices_id
                ) . '\')"><i class="fa fa-plus" aria-hidden="true"></i></a>';
        }
    }

    $heading_html = '
  			<div id="kanban_heading_' . $choices_id . '" class="kanban-heading-block">
  				<div class="kanban-heading" ' . $color . '>
  					<div id="kanban_heading_content_' . $choices_id . '">			
	  					<div class="heading">' . $add_button . $choices_name . ' (' . $kanban_info_choices[$choices_id]['count'] . ')</div>
	  					<div>' . $sum_html . '</div>
	  				</div>
  				</div>
  			</div>
  			';


    $html .= '
  			<td class="kanban-table-td" style="width: ' . $kanban_width . 'px">
  			' . $heading_html . '
  			<ul id="kanban_choice_' . $choices_id . '" class="kanban-sortable">' . $items_html . '</ul>
  			</td>	
  			';
}

$html .= '
  			</tr>
  		</table>
  	</div>
  		';


//added sortable
if ($is_kanban_sotrtable) {
    $html .= '
		  <script>
			  $(function() {         
			    	$( "ul.kanban-sortable" ).sortable({
			    		connectWith: "ul.kanban-sortable",
                        over: function (e, ui) {
	  					    $(".kanban-sortable").removeClass("ul-kanban-hover")
	  					    target_id = $(e.target).attr("id").replace("kanban_choice_","");
	  					    $("#kanban_choice_"+target_id).addClass("ul-kanban-hover")                                                      	  					                            
                        },
	  					create: function( event, ui ) {
	  					   prepare_kanban_padding();     
                        },
	  					stop: function( event, ui ) {
	  					    $(".kanban-sortable").removeClass("ul-kanban-hover")
	  					    prepare_kanban_padding();   
                        },    
			    		update: function(event,ui){  
			          
	  						var choices_id = this.id.replace("kanban_choice_","")
	  							  						
	  					    $("#kanban_heading_"+choices_id).addClass("kanban-heading-loading");	  					    
	  						
	  						if(ui.sender)
	  						{
	  							//alert(this.id+" - "+ui.item.attr("id"))  							
	  							item_id = ui.item.attr("id").replace("kanban_item_","")
	  							$.ajax({type: "POST",url: \'' . url_for(
            "ext/kanban/view",
            "action=sort&id=" . $reports['id'] . "&path=" . $app_path
        ) . '\',data: {choices_id:choices_id,item_id:item_id}}).done(function(data){
									if(data.length>0)
			    					{
			    						//alert(data)
			    						obj = JSON.parse(data)
			    						for (var k in obj) {
											  //console.log("obj." + k + " = " + obj[k]);
											  
			    							$("#kanban_heading_"+k).removeClass("kanban-heading-loading");
			    							$("#kanban_heading_content_"+k).html(obj[k])
											}
									}
			    		               
									});
							  }
			    		      else  
			    		      {
			    		        $("#kanban_heading_"+choices_id).removeClass("kanban-heading-loading");

	  						    $(".kanban-sortable").removeClass("ul-kanban-hover")
                              }
	  						  						  								        		         
			        }
			    	});
			      
			
			  });  
			</script>
	  ';
}


echo $html;
?>

<style>
    .kanban-heading-block-transform .kanban-heading {
        background: #fafafa;
    }

    .kanban-heading-loading {
        color: #bfbfbf;
    }

    .ul-kanban-hover {
        background: #e6e6e6;
    }
</style>

<script>

    function prepare_kanban_padding() {
        //get max height
        max_hight = 0;
        $('.kanban-sortable').each(function () {
            max_hight = ($(this).height() > max_hight ? $(this).height() : max_hight)
        })

        //console.log(max_hight);

        //set padding
        $('.kanban-sortable').each(function () {
            if ($(this).height() < max_hight) {
                padding = max_hight - $(this).height();
                $(this).css("padding-bottom", padding + 'px')
            }
        })
    }

    $(function () {
        //fix heading block	
        offset_top = $('.kanban-heading-block').offset().top - $('.header').height();

        if ($(window).width() < 973) {
            offset_top = offset_top + 50;
        }

        //hander scrol action
        $(window).bind('scroll', function () {
            var scrollTop = $(this).scrollTop();

            if (scrollTop > offset_top) {
                $('.kanban-heading-block').css('transform', 'translateY(' + (scrollTop - offset_top) + 'px)');
                $('.kanban-heading-block').addClass('kanban-heading-block-transform')
            } else {
                $('.kanban-heading-block').css('transform', 'none');
                $('.kanban-heading-block').removeClass('kanban-heading-block-transform')
            }
        });

    });

</script>



