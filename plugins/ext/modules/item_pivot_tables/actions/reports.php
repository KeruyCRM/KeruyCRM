<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => db_prepare_input($_POST['name']),
            'entities_id' => $_POST['entities_id'],
            'related_entities_id' => $_POST['related_entities_id'],
            'related_entities_fields' => (isset($_POST['related_entities_fields']) ? implode(
                ',',
                $_POST['related_entities_fields']
            ) : ''),
            'allowed_groups' => (isset($_POST['allowed_groups']) ? implode(',', $_POST['allowed_groups']) : ''),
            'sort_order' => $_POST['sort_order'],
            'position' => $_POST['position'],
            'rows_per_page' => $_POST['rows_per_page'],
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? json_encode($_POST['fields_in_listing']) : ''),
        ];

        if (isset($_GET['id'])) {
            //check if entity changed

            $pivotreports = db_find('app_ext_item_pivot_tables', $_GET['id']);
            if ($pivotreports['entities_id'] != $_POST['entities_id'] or $pivotreports['related_entities_id'] != $_POST['related_entities_id']) {
                db_delete_row('app_ext_item_pivot_tables_calcs', $_GET['id'], 'reports_id');
            }

            db_perform('app_ext_item_pivot_tables', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_item_pivot_tables', $sql_data);
        }

        redirect_to('ext/item_pivot_tables/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_item_pivot_tables', $_GET['id']);

        db_delete_row('app_ext_item_pivot_tables', $_GET['id']);
        db_delete_row('app_ext_item_pivot_tables_calcs', $_GET['id'], 'reports_id');

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/item_pivot_tables/reports');
        break;

    case 'fields_in_listing':
        //print_rr($_POST);

        $obj = (isset($_POST['id']) ? db_find('app_ext_item_pivot_tables', $_POST['id']) : db_show_columns(
            'app_ext_item_pivot_tables'
        ));

        $html = '';

        if (is_array($_POST['related_entities_fields'])) {
            foreach ($_POST['related_entities_fields'] as $fields_id) {
                $fields_info_query = db_query(
                    "select f.id, f.configuration, f.entities_id, e.name as entitis_name from app_fields f, app_entities e where f.type in ('fieldtype_entity','fieldtype_entity_multilevel','fieldtype_entity_ajax') and e.id=f.entities_id and f.id='" . $fields_id . "'"
                );
                if ($fields_info = db_fetch_array($fields_info_query)) {
                    $cfg = new fields_types_cfg($fields_info['configuration']);

                    $entity_id = $cfg->get('entity_id');

                    $entity_info = db_find('app_entities', $entity_id);

                    $choices = [];

                    $fields_query = db_query(
                        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where  f.entities_id='" . $entity_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                    );
                    while ($fields = db_fetch_array($fields_query)) {
                        $choices[$fields['id']] = fields_types::get_option(
                                $fields['type'],
                                'name',
                                $fields['name']
                            ) . ' (#' . $fields['id'] . ')';
                    }


                    $fields_in_listing = (strlen($obj['fields_in_listing']) ? json_decode(
                        $obj['fields_in_listing'],
                        true
                    ) : '');


                    $html .= '
				<h3 class="form-section">' . TEXT_ENTITY . ': ' . $entity_info['name'] . '</h3>		
				<div class="form-group">
		    	<label class="col-md-4 control-label" for="type">' . TEXT_FIELDS_IN_LISTING . ' </label>
		      <div class="col-md-8">
		    	  ' . select_tag(
                            'fields_in_listing[' . $entity_id . '][]',
                            $choices,
                            (isset($fields_in_listing[$entity_id]) ? $fields_in_listing[$entity_id] : ''),
                            [
                                'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                                'multiple' => 'multiple',
                                'chosen_order' => (isset($fields_in_listing[$entity_id]) ? implode(
                                    ',',
                                    $fields_in_listing[$entity_id]
                                ) : '')
                            ]
                        ) . '
		    	  ' . tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_FIELDS_TIP) . '
		      </div>
		    </div>
				';
                }
            }
        }

        echo $html;

        exit();
        break;

    case 'related_entity_fields':
        $entities_id = _post::int('entities_id');

        $obj = (isset($_POST['id']) ? db_find('app_ext_item_pivot_tables', $_POST['id']) : db_show_columns(
            'app_ext_item_pivot_tables'
        ));


        $entities_list = [];
        $entities_list[] = $entities_id;

        $parrent_entities = entities::get_parents($entities_id);

        if (count($parrent_entities) > 0) {
            $parrent_entities = array_reverse($parrent_entities);
            $entities_list = array_merge($parrent_entities, $entities_list);
        }

        $allowed_fields_types = [
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_entity_multilevel',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_grouped_users',
            'fieldtype_radioboxes',
            'fieldtype_checkboxes',
        ];

        $choices = [];
        foreach ($entities_list as $eid) {
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('" . implode(
                    '\',\'',
                    $allowed_fields_types
                ) . "') and f.entities_id='" . $eid . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $choices[$app_entities_cache[$eid]['name']][$fields['id']] = fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    ) . ' (#' . $fields['id'] . ')';
            }
        }

        $html = '
				<div class="form-group">
		    	<label class="col-md-4 control-label" for="type">' . TEXT_FIELDS . ' </label>
		      <div class="col-md-8">
		    	  ' . select_tag(
                'related_entities_fields[]',
                $choices,
                $obj['related_entities_fields'],
                [
                    'class' => 'form-control input-xlarge required chosen-select chosen-sortable',
                    'multiple' => 'multiple',
                    'chosen_order' => $obj['related_entities_fields']
                ]
            ) . '
		    	  ' . tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_FIELDS_TIP) . '
		      </div>
		    </div>
				';

        echo $html;

        exit();

        break;
}
