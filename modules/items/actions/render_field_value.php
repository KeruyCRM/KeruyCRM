<?php

$fields_query = db_query("select * from app_fields where id='" . _get::int('fields_id') . "'");
if ($fields = db_fetch_array($fields_query)) {
    switch ($fields['type']) {
        case 'fieldtype_entity_multilevel':
        case 'fieldtype_entity_ajax':
        case 'fieldtype_entity':
        case 'fieldtype_users_ajax':
            $cfg = new fields_types_cfg($fields['configuration']);

            $item_id = _get::int('item_id');
            $parent_entity_item_id = _get::int('parent_entity_item_id');

            $obj = [];

            if (in_array($cfg->get('display_as'), ['dropdown_multiple']
                ) and $_GET['current_field_values'] != 'null') {
                $obj['field_' . $fields['id']] = $_GET['current_field_values'] . ',' . _get::int('item_id');
            } else {
                $obj['field_' . $fields['id']] = _get::int('item_id');
            }

            echo '<div id="field_' . $fields['id'] . '_row">' . fields_types::render(
                    $fields['type'],
                    $fields,
                    $obj,
                    ['parent_entity_item_id' => $parent_entity_item_id, 'form' => 'item']
                ) . '</div>';

            echo '
			    <script>
			        appHandleChosen(); 
			        app_handle_submodal_open_btn("field_' . $fields['id'] . '_row");
                                    
                 
                                                            
			    </script>';

            $field_query = db_query(
                "select * from app_fields where entities_id='" . $fields['entities_id'] . "' and type='fieldtype_entity_multilevel'"
            );
            while ($field = db_fetch_array($field_query)) {
                $cfg2 = new fields_types_cfg($field['configuration']);
                if (strlen($cfg2->get('force_parent_item_id'))) {
                    echo '
                                <script>    
                                    //try execute function from fieldtype_entity_multilevel
                                    try {
                                        force_parent_item_' . $field['id'] . '_' . $fields['id'] . '_change()
                                    } catch (err) {
                                        //console.log(err)
                                    }
                                </script>        
                                    ';
                }
            }

            switch ($fields['type']) {
                case 'fieldtype_users_ajax':
                    echo '
                                    <script>
			                $("#fields_' . $fields['id'] . '_select2_on").load("' . url_for(
                            'dashboard/select2_json',
                            'action=copy_values&form_type=items/render_field_value&entity_id=1&field_id=' . $fields['id']
                        ) . '",{item_id:' . _get::int('item_id') . '})
			            </script>
                                    ';
                    break;
                case 'fieldtype_entity_ajax':
                case 'fieldtype_entity_multilevel':
                    echo '
			            <script>
			                $("#fields_' . $fields['id'] . '_select2_on").load("' . url_for(
                            'dashboard/select2_json',
                            'action=copy_values&form_type=items/render_field_value&entity_id=' . $cfg->get(
                                'entity_id'
                            ) . '&field_id=' . $fields['id']
                        ) . '",{item_id:' . _get::int('item_id') . '})
			            </script>
			            ';
                    break;
                default:
                    echo '
			            <script>
			            
			            </script>
			            ';
                    break;
            }
            break;
    }
}

exit();