<?php

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'entities_id' => $_POST['entities_id'],
            'fields_for_search' => (isset($_POST['fields_for_search']) ? implode(
                ',',
                $_POST['fields_for_search']
            ) : ''),
            'fields_in_listing' => (isset($_POST['fields_in_listing']) ? implode(
                ',',
                $_POST['fields_in_listing']
            ) : ''),
            'sort_order' => $_POST['sort_order'],
        ];


        if (isset($_GET['id'])) {
            db_perform('app_ext_global_search_entities', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_global_search_entities', $sql_data);
        }

        redirect_to('ext/global_search/entities');

        break;
    case 'delete':
        db_delete_row('app_ext_global_search_entities', $_GET['id']);


        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, ''), 'success');

        redirect_to('ext/global_search/entities');
        break;
    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_global_search_entities', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_global_search_entities');
        }

        $choices = [];

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . fields_types::get_types_for_search_list(
            ) . ") and  f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $html = '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="allowed_groups">' . TEXT_SEARCH_BY_FIELDS . '</label>
            <div class="col-md-8">	
          	   ' . select_tag(
                'fields_for_search[]',
                $choices,
                $obj['fields_for_search'],
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => true]
            ) . '
               ' . tooltip_text(TEXT_SEARCH_BY_FIELDS_INFO) . '
            </div>			
          </div>
        ';

        $choices = [];

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_parent_item_id')  and  f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ) . ' (#' . $fields['id'] . ')';
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="allowed_groups">' . TEXT_FIELDS_IN_LISTING . '</label>
            <div class="col-md-8">
          	   ' . select_tag(
                'fields_in_listing[]',
                $choices,
                $obj['fields_in_listing'],
                [
                    'class' => 'form-control input-xlarge chosen-select chosen-sortable',
                    'multiple' => true,
                    'chosen_order' => $obj['fields_in_listing']
                ]
            ) . '               
            </div>
          </div>
        ';


        echo $html;

        exit();
        break;
}