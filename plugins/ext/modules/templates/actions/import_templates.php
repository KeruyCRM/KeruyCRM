<?php

if (!app_session_is_registered('import_templates_filter')) {
    $import_templates_filter = 0;
    app_session_register('import_templates_filter');
}

switch ($app_module_action) {
    case 'set_import_templates_filter':
        $import_templates_filter = $_POST['import_templates_filter'];

        redirect_to('ext/templates/import_templates');
        break;

    case 'save':
        $sql_data = [
            'name' => $_POST['name'],
            'entities_id' => $_POST['entities_id'],
            'multilevel_import' => $_POST['multilevel_import'],
            'is_active' => (isset($_POST['is_active']) ? 1 : 0),
            'sort_order' => $_POST['sort_order'],
            'users_groups' => (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : ''),
            'import_fields' => (isset($_POST['import_fields']) ? json_encode($_POST['import_fields']) : ''),
        ];

        if (isset($_GET['id'])) {
            db_perform('app_ext_import_templates', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_ext_import_templates', $sql_data);
        }

        redirect_to('ext/templates/import_templates');
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_import_templates where id='" . db_input($_GET['id']) . "'");

            $alerts->add(TEXT_EXT_WARN_DELETE_TEMPLATE_SUCCESS, 'success');

            redirect_to('ext/templates/import_templates');
        }
        break;
    case 'get_subentities':
        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_import_templates', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_import_templates');
        }

        $entities_id = _post::int('entities_id');

        $choices = [];
        if (entities::has_subentities($entities_id)) {
            $choices[] = '';
            foreach (entities::get_tree($entities_id) as $entity) {
                $choices[$entity['id']] = str_repeat(' - ', ($entity['level'] + 1)) . $entity['name'];
            }
        }

        $html = '
  			<div class="form-group">
			  	<label class="col-md-3 control-label" for="entities_id">' . tooltip_icon(
                TEXT_MULTI_LEVEL_IMPORT_INFO
            ) . TEXT_MULTI_LEVEL_IMPORT . '</label>
			    <div class="col-md-9">' . select_tag(
                'multilevel_import',
                $choices,
                $obj['multilevel_import'],
                ['class' => 'form-control input-large']
            ) . '
			    </div>			
			  </div>
			   
			  <script>
			    	$("#multilevel_import").change(function(){
				    	load_fields_configuration();
				    })
			  </script>  		
  			';

        echo $html;

        exit();

        break;
    case 'fields_configuration':

        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_import_templates', $_POST['id']);
        } else {
            $obj = db_show_columns('app_ext_import_templates');
        }

        $import_fields = (strlen($obj['import_fields']) ? json_decode($obj['import_fields'], true) : []);

        $html = '
  			<table class="table table-columns-cfg">
  			  <thead>
  					<tr>
  						<th>' . TEXT_COLUMN . '</th>
  						<th>' . TEXT_FIELD . '</th>
  					</tr>
  				</thead>
  				<tbody>				
  			';

        $current_entity_id = _post::int('entities_id');

        $multilevel_import = (isset($_POST['multilevel_import']) ? _post::int('multilevel_import') : 0);

        $entities_list = [];
        $entities_list[$current_entity_id] = entities::get_name_by_id($current_entity_id);

        if ($multilevel_import > 0) {
            foreach (entities::get_tree($current_entity_id) as $entity) {
                $entities_list[$entity['id']] = $entity['name'];

                if ($entity['id'] == $multilevel_import) {
                    break;
                }
            }
        }

        $choices = [];
        $choices[] = '-';

        foreach ($entities_list as $entity_id => $entity_name) {
            $fields_query = db_query(
                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::skip_import_field_types(
                ) . ") and f.entities_id='" . $entity_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($fields = db_fetch_array($fields_query)) {
                $choices[$entity_name][$fields['id']] = ($fields['is_heading'] == 1 ? '* ' : '') . fields_types::get_option(
                        $fields['type'],
                        'name',
                        $fields['name']
                    ) . ($fields['is_heading'] == 1 ? ' (' . TEXT_HEADING . ')' : '');
            }
        }

        $import_fields_list = (count($import_fields) > count(range('A', 'Z')) ? $import_fields : range('A', 'Z'));

        $alphabet = range('A', 'Z');

        foreach ($import_fields_list as $k => $v) {
            if ($k < 26) {
                $letter = $alphabet[$k];
            } else {
                $k1 = floor($k / 26) - 1;
                $k2 = $k - (floor($k / 26) * 26);
                $letter = $alphabet[$k1] . $alphabet[$k2];
            }


            $html .= '
  				<tr>
  					<td>' . $letter . ' ' . ($k + 1) . '</td>
  					<td>' . select_tag(
                    'import_fields[' . $k . ']',
                    $choices,
                    (isset($import_fields[$k]) ? $import_fields[$k] : 0),
                    ['class' => 'form-control input-xlarge chosen-select import-fields']
                ) . '</td>
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
  						
  			function add_extra_column()
			  {
  				var alphabet = ["' . implode('","', $alphabet) . '"];
			    var import_fields_count = 0;
			    $("#fields_configuration .import-fields").each(function(){
			    		import_fields_count++;
			      })
  						
  				k1 = Math.floor(import_fields_count/26)-1;
  				k2 = import_fields_count - (Math.floor(import_fields_count/26)*26);  		
			    var letter = alphabet[k1]+""+alphabet[k2];
			    		
			    $(".table-columns-cfg tbody").append("<tr><td>"+letter+" "+(import_fields_count+1)+"</td><td>' . addslashes(
                select_tag(
                    'import_fields_tmp',
                    $choices,
                    0,
                    ['class' => 'form-control input-xlarge chosen-select import-fields']
                )
            ) . '</td></tr>")
			    		
			    $("#import_fields_tmp").attr("name","import_fields["+import_fields_count+"]")		
			    $("#import_fields_tmp").attr("id","import_fields_"+import_fields_count)
			    		
			   	appHandleChosen()
			  }			
  			</script>			
  						
  			';

        echo $html;

        exit();
        break;
}