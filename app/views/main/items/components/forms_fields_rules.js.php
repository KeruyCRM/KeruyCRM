<?php

$form_fields_query = \K::model()->db_query_exec(
    "select r.*, f.name, f.id as fields_id, f.type from app_forms_fields_rules r, app_fields f where r.is_active = 1 and  f.type in ('fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel','fieldtype_dropdown','fieldtype_dropdown_multiple','fieldtype_checkboxes','fieldtype_radioboxes','fieldtype_user_accessgroups','fieldtype_grouped_users','fieldtype_boolean_checkbox','fieldtype_boolean','fieldtype_autostatus', 'fieldtype_stages') and r.fields_id = f.id and r.entities_id = ?",
    \K::$fw->current_entity_id
);

if (count($form_fields_query) > 0) {
    $html = '';

    $rules_for_fields = [];

    //while ($form_fields = db_fetch_array($form_fields_query)) {
    foreach ($form_fields_query as $form_fields) {
        if (strlen($form_fields['visible_fields']) and strlen($form_fields['choices'])) {
            $html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="visible" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['visible_fields'] . '">';
        }

        if (strlen($form_fields['hidden_fields']) and strlen($form_fields['choices'])) {
            $html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="hidden" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['hidden_fields'] . '">';
        }

        $rules_for_fields[$form_fields['fields_id']] = $form_fields['type'];
    }

//include form rules if form exist

//and $app_layout!='public_layout.php'        

    if (\K::fw()->exists('app_items_form_name')) {
        $html .= '
		<script>
			$(function(){
			';

        $container = ((\K::$fw->AJAX and \K::$fw->app_user['id'] != 0) ? 'ajax-modal' : '');

        foreach ($rules_for_fields as $fields_id => $fields_type) {
            $html .= '
			$(".field_' . $fields_id . '").change(function(){					
				app_handle_forms_fields_display_rules(\'' . $container . '\',' . $fields_id . ',\'' . $fields_type . '\',false,false)						
			})	
			
			' . ((\K::$fw->app_module_path != 'items/info' and \K::$fw->app_module_path != 'items/comments_form' and \K::$fw->app_module_path != 'items/processes') ? 'app_handle_forms_fields_display_rules(\'' . $container . '\',' . $fields_id . ',\'' . $fields_type . '\',false,false)' : '') . '
		';

            //handle comments and process forms
            if ((\K::$fw->app_module_path == 'items/comments_form' or \K::$fw->app_module_path == 'items/processes') and isset($item_info)) {
                $field = db_find('app_fields', $fields_id);
                $cfg = new fields_types_cfg($field['configuration']);

                $is_multiple = false;

                if (in_array($field['type'], ['fieldtype_dropdown_multiple', 'fieldtype_checkboxes'])) {
                    $is_multiple = true;
                }

                if ($field['type'] == 'fieldtype_grouped_users' and in_array(
                        $cfg->get('display_as'),
                        ['checkboxes', 'dropdown_muliple']
                    )) {
                    $is_multiple = true;
                }

                $value = items::prepare_field_value_by_type($field, $item_info);

                $html .= 'app_handle_forms_fields_display_rules(\'\',' . $field['id'] . ',"","' . (strlen(
                        $value
                    ) ? $value : '0') . '",' . (int)$is_multiple . '); ';
            }
        }

        $html .= '
			})
		</script>
			';
    }

    echo $html;
}