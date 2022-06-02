<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('process_id') . "'");
if (!$app_process_info = db_fetch_array($app_process_info_query)) {
    redirect_to('ext/processes/processes');
}

$app_actions_info_query = db_query(
    "select * from app_ext_processes_actions where process_id='" . _get::int('process_id') . "' and id='" . _get::int(
        'actions_id'
    ) . "'"
);
if (!$app_actions_info = db_fetch_array($app_actions_info_query)) {
    redirect_to('ext/processes/processes');
}

switch ($app_module_action) {
    case 'save':


        $sql_data = [
            'actions_id' => _get::int('actions_id'),
            'parent_id' => _post::int('parent_id'),
            'from_entity_id' => _post::int('from_entity_id'),
            'to_entity_id' => _post::int('to_entity_id'),
            'fields' => $_POST['fields'],
        ];

        if (isset($_GET['id'])) {
            //delete sub rules if entities changed
            $rule_info = db_find('app_ext_processes_clone_subitems', _get::int('id'));

            if ($rule_info['from_entity_id'] != $sql_data['from_entity_id'] or $rule_info['to_entity_id'] != $sql_data['to_entity_id']) {
                $rules = clone_subitems::get_rules_tree(_get::int('actions_id'), _get::int('id'));

                foreach ($rules as $rule) {
                    db_delete_row('app_ext_processes_clone_subitems', $rule['id']);
                }
            }

            db_perform('app_ext_processes_clone_subitems', $sql_data, 'update', "id='" . _get::int('id') . "'");
        } else {
            db_perform('app_ext_processes_clone_subitems', $sql_data);
        }

        redirect_to(
            'ext/processes/clone_subitems',
            'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int('actions_id')
        );
        break;
    case 'delete':

        if (isset($_GET['id'])) {
            clone_subitems::delete_rule(_get::int('actions_id'), _get::int('id'));
        }

        redirect_to(
            'ext/processes/clone_subitems',
            'process_id=' . _get::int('process_id') . '&actions_id=' . _get::int('actions_id')
        );
        break;
    case 'get_fields_schema':
        if (isset($_POST['id'])) {
            $obj = db_find('app_ext_processes_clone_subitems', $_POST['id']);
        } else {
            $obj['fields'] = '';
        }

        $html = '
				<div class="form-group">
			  	<label class="col-md-3 control-label" for="fields_id">' .
            TEXT_FIELDS .
            fields::get_available_fields_helper(
                _post::int('from_entity_id'),
                'fields',
                TEXT_EXT_FROM_ENTITY . ' #' . _post::int('from_entity_id'),
                [],
                true
            ) .
            fields::get_available_fields_helper(
                _post::int('to_entity_id'),
                'fields',
                TEXT_EXT_TO_ENTITY . ' #' . _post::int('to_entity_id'),
                [],
                true
            ) . '</label>
			    <div class="col-md-9">	
			  	  ' . textarea_tag('fields', $obj['fields'], ['class' => 'form-control input-large required']) . '
			  	  ' . tooltip_text(TEXT_EXT_CLONE_FIELDS_SCHEMA_TIP) . '
			    </div>			
			  </div>				
				';

        echo $html;

        exit();
        break;
}
