<?php

//check report and access
$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
if ($reports_info = db_fetch_array($reports_info_query)) {
    $access_schema = users::get_entities_access_schema($reports_info['entities_id'], $app_user['group_id']);

    if (!users::has_access('update', $access_schema)) {
        redirect_to('dashboard/access_forbidden');
    }
} else {
    redirect_to('dashboard/page_not_found');
}


switch ($app_module_action) {
    case 'items_select':
        $related_to_field = explode('-', $_POST['related_to_field']);

        $from_entities_id = $related_to_field[0];

        $fields_query = db_query(
            "select f.*, e.name as entity_name from app_fields f, app_entities e where f.entities_id='" . _GET(
                'entities_id'
            ) . "' and f.entities_id=e.id and f.type='fieldtype_related_records' order by e.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $cfg = new fields_types_cfg($fields['configuration']);

            if ($cfg->get('entity_id') == $from_entities_id) {
                $field_id = $fields['id'];
            }
        }

        if (!isset($field_id)) {
            exit();
        }

        $html = '
            <div class="form-group">              	
                <div class="col-md-12">	  	        
                  ' . select_tag(
                'items[]',
                [],
                '',
                ['class' => 'form-control required', 'data-placeholder' => TEXT_ENTER_VALUE, 'multiple' => 'multiple']
            ) . '
                  <label id="items-error" class="error" for="items" style="display: none"></label>    
                </div>			
              </div>
             <div class="form-group">              	
                <div class="col-md-12">' . submit_tag(TEXT_BUTTON_LINK) . '</div>
             </div>         
            ';

        $html .= input_hidden_tag('from_entities_id', $from_entities_id) . input_hidden_tag(
                'to_entities_id',
                _GET('entities_id')
            );

        $parent_entity_item_id = items::get_paretn_entity_item_id_by_path($app_path);

        $url = url_for(
            'items/select2_related_items',
            'action=select_items&entity_id=' . $from_entities_id . '&field_id=' . $field_id . '&path=' . $from_entities_id . '&parent_entity_item_id=' . $parent_entity_item_id
        );

        $html .= '
          <script>
			$(function(){
            
  			$("#items").select2({
				    width: "100%",
				    dropdownParent: $("#ajax-modal"),
				    "language":{
				      "noResults" : function () { return "' . addslashes(TEXT_NO_RESULTS_FOUND) . '"; },
				  		"searching" : function () { return "' . addslashes(TEXT_SEARCHING) . '"; },
				  		"errorLoading" : function () { return "' . addslashes(TEXT_RESULTS_COULD_NOT_BE_LOADED) . '"; },
				  		"loadingMore" : function () { return "' . addslashes(TEXT_LOADING_MORE_RESULTS) . '"; }
				    },
				    ajax: {
				  		url: "' . $url . '",
				  		dataType: "json",
				  		data: function (params) {
					      var query = {
					        search: params.term,
					        page: params.page || 1
					      }
				  		    
					      // Query parameters will be ?search=[term]&page=[page]
					      return query;
					    },
				  	},
						templateResult: function (d) { return $(d.html); },
					});
				  		    
				  $("#items").change(function (e) {
						$("#items-error").remove();
					});
						    
				})
			</script>
  			';

        echo $html;
        exit();

        break;

    case 'add_related_items':

        if (isset($_POST['items']) and isset($_POST['from_entities_id']) and isset($_POST['to_entities_id']) and count(
                $app_selected_items[$_GET['reports_id']]
            ) > 0) {
            $entities_id = $_POST['from_entities_id'];
            $related_entities_id = $_POST['to_entities_id'];

            $table_info = related_records::get_related_items_table_name($entities_id, $related_entities_id);

            $items_list = [];

            foreach ($_POST['items'] as $items_id) {
                foreach ($app_selected_items[$_GET['reports_id']] as $related_items_id) {
                    $check_query = db_query(
                        "select * from " . $table_info['table_name'] . " where entity_" . $entities_id . "_items_id=" . (int)$items_id . " and entity_" . $related_entities_id . $table_info['sufix'] . "_items_id = " . (int)$related_items_id . ""
                    );
                    if (!$check = db_fetch_array($check_query)) {
                        $sql_data = [
                            'entity_' . $entities_id . '_items_id' => $items_id,
                            'entity_' . $related_entities_id . $table_info['sufix'] . '_items_id' => $related_items_id
                        ];

                        db_perform($table_info['table_name'], $sql_data);
                    }

                    //autocreate comments
                    related_records::autocreate_comments(
                        $entities_id,
                        $items_id,
                        $related_entities_id,
                        $related_items_id
                    );
                }

                $items_list[] = ['entities_id' => $entities_id, 'id' => $items_id];
            }

            $html = '
        <div class="alert alert-success">' . TEXT_EXT_RECORDS_SUCCESSFULLY_LINKED . '</div>
      ';

            if (count($items_list) > 0) {
                $html .= '
          <p>' . TEXT_GO_TO . '</p>
            <ul>
        ';

                foreach ($items_list as $item) {
                    $path_info = items::get_path_info($item['entities_id'], $item['id']);

                    $html .= '
            <li><a href="' . url_for('items/info', 'path=' . $path_info['full_path']) . '">' . items::get_heading_field(
                            $item['entities_id'],
                            $item['id']
                        ) . '</a></li>
          ';
                }

                $html .= '</ul>';
            }

            echo $html;
        }

        exit();

        break;
}