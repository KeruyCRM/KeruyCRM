<?php

//check access
if ($app_user['group_id'] > 0) {
    redirect_to('dashboard/access_forbidden');
}

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'users_groups' => (isset($_POST['access']) ? json_encode($_POST['access']) : ''),
            'fields_id' => $_POST['fields_id'],
            'in_menu' => (isset($_POST['in_menu']) ? $_POST['in_menu'] : 0),
            'scale' => $_POST['scale'],
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_image_map', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_image_map', $sql_data);
        }

        redirect_to('ext/image_map/reports');

        break;
    case 'delete':
        $obj = db_find('app_ext_image_map', $_GET['id']);

        db_delete_row('app_ext_image_map', $_GET['id']);

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['name']), 'success');

        redirect_to('ext/image_map/reports');
        break;


    case 'get_entities_fields':

        $entities_id = $_POST['entities_id'];
        $entities_info = db_find('app_entities', $entities_id);

        $obj = [];

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_image_map', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_image_map');
        }

        $html = '';


        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_image_map') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="allowed_groups">' . TEXT_FIELD . '</label>
            <div class="col-md-8">	
          	   ' . select_tag(
                'fields_id',
                $choices,
                $obj['fields_id'],
                ['class' => 'form-control input-large required']
            ) . '
               ' . tooltip_text(TEXT_AVAILABLE_FIELS . ': ' . TEXT_FIELDTYPE_IMAGE_MAP_TITLE) . '
            </div>			
          </div>
        ';

        echo $html;

        exit();
        break;
}