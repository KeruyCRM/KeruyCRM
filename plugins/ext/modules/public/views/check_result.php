<h3 class="form-title"><?php
    echo(strlen($public_form['check_page_title']) > 0 ? $public_form['check_page_title'] : $public_form['name']) ?></h3>

<?php

if (strlen($public_form['check_page_fields']) and $app_item_info) {
    $html = '';
    $fields_query = db_query(
        "select f.*, t.name as tab_name, if(f.type in ('fieldtype_id','fieldtype_date_added','fieldtype_created_by','fieldtype_parent_item_id'),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.id in (" . $public_form['check_page_fields'] . ") and f.entities_id='" . $current_entity_id . "' and f.forms_tabs_id=t.id order by tab_sort_order, t.name, f.sort_order, f.name"
    );
    while ($field = db_fetch_array($fields_query)) {
        //prepare field value
        $value = items::prepare_field_value_by_type($field, $app_item_info);

        $output_options = [
            'class' => $field['type'],
            'value' => $value,
            'field' => $field,
            'item' => $app_item_info,
            'users_cache' => $app_users_cache,
            'display_user_photo' => false,
            'is_public_form' => $public_form['id'],
            'path' => $current_entity_id
        ];

        $html .= '
				<tr>
					<th>' . fields_types::get_option($field['type'], 'name', $field['name']) . '</th>
					<td>' . fields_types::output($output_options) . '</td>
				</tr>
				';
    }

    if (strlen($html)) {
        $html = '<table class="table table-striped table-bordered table-hover">' . $html . '</table>';
    }

    if ($public_form['check_page_comments']) {
        $comments = public_forms::prepare_comments($public_form, $current_entity_id, $app_item_info);
        $html .= $comments['html'];
    }


    echo $html;
}

echo '<a class="btn btn-default" href="' . url_for(
        'ext/public/check',
        'id=' . $public_form['id']
    ) . '"><i class="fa fa-arrow-circle-left"></i> ' . TEXT_BUTTON_BACK . '</a>';
