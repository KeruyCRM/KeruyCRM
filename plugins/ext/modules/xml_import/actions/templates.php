<?php

if (!app_session_is_registered('xml_templates_filter')) {
    $xml_templates_filter = 0;
    app_session_register('xml_templates_filter');
}

switch ($app_module_action) {
    case 'set_xml_templates_filter':
        $xml_templates_filter = $_POST['xml_templates_filter'];

        redirect_to('ext/xml_import/templates');
        break;
    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'entities_id' => $_POST['entities_id'],
            'button_title' => $_POST['button_title'],
            'button_position' => (isset($_POST['button_position']) ? implode(',', $_POST['button_position']) : ''),
            'button_color' => $_POST['button_color'],
            'button_icon' => $_POST['button_icon'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'assigned_to' => (isset($_POST['assigned_to']) ? implode(',', $_POST['assigned_to']) : ''),
            'import_fields' => (isset($_POST['import_fields']) ? json_encode($_POST['import_fields']) : ''),
            'import_fields_path' => (isset($_POST['import_fields_path']) ? json_encode(
                $_POST['import_fields_path']
            ) : ''),
            'data_path' => $_POST['data_path'],
            'import_action' => $_POST['import_action'],
            'update_by_field' => $_POST['update_by_field'],
            'update_by_field_path' => $_POST['update_by_field_path'],
            'filepath' => $_POST['filepath'],
            'parent_item_id' => (isset($_POST['parent_item_id']) ? $_POST['parent_item_id'] : ''),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_xml_import_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_xml_import_templates', $sql_data);
        }

        redirect_to('ext/xml_import/templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_xml_import_templates where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/xml_import/templates');
        }
        break;
    case 'get_parent_items_choices':
        $entities_id = _post::int('entities_id');

        $obj = db_find('app_ext_xml_import_templates', $_POST['id']);

        $html = '';
        if ($app_entities_cache[$entities_id]['parent_id'] > 0) {
            $choices = [];

            if ($obj['parent_item_id'] > 0) {
                $choices[$obj['parent_item_id']] = items::get_heading_field(
                    $app_entities_cache[$entities_id]['parent_id'],
                    $obj['parent_item_id']
                );
            }

            $html = '
                <div class="form-group">
                  	<label class="col-md-3 control-label" for="import_action">' . TEXT_PARENT . '</label>
                    <div class="col-md-9">' . select_entities_tag(
                    'parent_item_id',
                    $choices,
                    $obj['parent_item_id'],
                    [
                        'class' => 'form-control input-xlarge',
                        'entities_id' => $app_entities_cache[$entities_id]['parent_id']
                    ]
                ) . '</div>
                </div>';
        }

        echo $html;
        exit();

        break;
    case 'get_fields':

        $entities_id = _post::int('entities_id');

        $obj = db_find('app_ext_xml_import_templates', $_POST['id']);

        $choices = [
            'import' => TEXT_ACTION_IMPORT_DATA,
            'update' => TEXT_ACTION_UPDATE_DATA,
            'update_import' => TEXT_ACTION_UPDATE_AND_IMPORT_DATA,
        ];


        $html = '
                <div class="form-group">
                  	<label class="col-md-4 control-label" for="import_action">' . TEXT_ACTION . '</label>
                    <div class="col-md-8">' . select_tag(
                'import_action',
                $choices,
                $obj['import_action'],
                ['class' => 'form-control input-large']
            ) . '</div>			
                </div>';

        $choices = [];
        $fields_query = db_query(
            "select f.* from app_fields f where f.type in ('fieldtype_id','fieldtype_input','fieldtype_random_value') and f.entities_id='" . $entities_id . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = fields_types::get_option($fields['type'], 'name', $fields['name']);
        }

        $html .= '
                <div class="form-group update-by-field">
                  	<label class="col-md-4 control-label" for="update_by_field">' . TEXT_UPDATE_BY_FIELD . '</label>
                    <div class="col-md-8">' . select_tag(
                'update_by_field',
                $choices,
                $obj['update_by_field'],
                ['class' => 'form-control input-large']
            ) . '</div>			
                </div> 

                <div class="form-group update-by-field">
                  	<label class="col-md-4 control-label" for="update_by_field_path">' . TEXT_EXT_XML_PATH_TO_VALUE . '</label>
                    <div class="col-md-8">' . input_tag(
                'update_by_field_path',
                $obj['update_by_field_path'],
                ['class' => 'form-control input-large']
            ) . '</div>			
                </div>

                <hr>

				<div class="form-group">
                  	<label class="col-md-4 control-label" for="data_path">' . TEXT_EXT_XML_PATH_TO_DATA_ARRAY . '</label>
                    <div class="col-md-8">' .
            input_tag('data_path', $obj['data_path'], ['class' => 'form-control input-large']) .
            tooltip_text(TEXT_EXAMPLE . ': /items/item') .
            '</div>			
                  </div> 
				';


        $import_fields = (strlen($obj['import_fields']) ? json_decode($obj['import_fields'], true) : [0 => 0]);
        $import_fields_path = (strlen($obj['import_fields_path']) ? json_decode(
            $obj['import_fields_path'],
            true
        ) : [0 => '']);

        $html .= '
  			<table class="table table-columns-cfg">
  			  <thead>
  					<tr>  						
  						<th>' . TEXT_FIELD . '</th>
                        <th>' . TEXT_EXT_XML_PATH_TO_VALUE . '</th>
                        <th></ht>    
  					</tr>
  				</thead>
  				<tbody>
  			';

        $choices = [];
        $choices[] = '-';

        $fields_query = db_query(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::skip_import_field_types(
            ) . ") and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = ($fields['is_heading'] == 1 ? '* ' : '') . fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                ) . ($fields['is_heading'] == 1 ? ' (' . TEXT_HEADING . ')' : '');
        }

        foreach ($import_fields as $k => $field_id) {
            $html .= '
      				<tr >  					
      					<td>' . select_tag(
                    'import_fields[]',
                    $choices,
                    $field_id,
                    ['class' => 'form-control input-medium chosen-select import-fields']
                ) . '</td>
                        <td width="100%">' . input_tag(
                    'import_fields_path[]',
                    $import_fields_path[$k],
                    ['class' => 'form-control', 'style' => 'font-size:13px;']
                ) . '</td>
                        <td><i onClick="remove_table_row($(this))" class="fa fa-times pointer" aria-hidden="true"></i></td>
      				</tr>
      				';
        }

        $html .= '
  				</tbody>
  			</table>

            <center>
  				<button type="button" class="btn btn-default" onClick="add_extra_column()">' . TEXT_ADD_FIELD . '</button>
  			</center>
  						
<script>
			
  function remove_table_row(row)
  {
    row.closest("tr").remove();
  }

  function add_extra_column()
  {  							    		
    $(".table-columns-cfg tbody").append("<tr><td>' . addslashes(
                select_tag(
                    'import_fields[]',
                    $choices,
                    0,
                    ['class' => 'form-control input-medium chosen-select import-fields']
                )
            ) . '</td><td>' . addslashes(
                input_tag('import_fields_path[]', '', ['class' => 'form-control', 'style' => 'font-size:13px;'])
            ) . '</td><td><i onClick=\"remove_table_row($(this))\" class=\"fa fa-times pointer\"></i></td></tr>")
    					    			    		
   	appHandleChosen()
  }	
   
  function check_import_action()
  {
    if($("#import_action").val()=="update" || $("#import_action").val()=="update_import")
    {
        $(".update-by-field").show()
    } 
    else
    {
        $(".update-by-field").hide()
    }    
  }

$(function(){
    check_import_action()
    
    $("#import_action").change(function(){
        check_import_action()                      
    })
})

</script>			
  						
  			';

        echo $html;

        exit();
        break;
    case 'import_from_url':
        $template_info_query = db_query(
            "select * from app_ext_xml_import_templates where length(filepath)>0 and id='" . _get::int('id') . "'"
        );
        if ($template_info = db_fetch_array($template_info_query)) {
            $xml_import = new xml_import('', $template_info);

            $xml_import->get_file_by_path();

            $xml_errors = $xml_import->has_xml_errors();

            if (!strlen($xml_errors)) {
                $parent_entity_item_id = $template_info['parent_item_id'];

                $msg = $xml_import->import_data();

                $alerts->add($msg, 'success');
            }

            $xml_import->unlink_import_file();
        }

        redirect_to('ext/xml_import/templates');
        break;
}
