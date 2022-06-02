<?php

class global_search
{
    static function has_access()
    {
        global $app_user;

        $allowed_groups = (strlen(CFG_GLOBAL_SEARCH_ALLOWED_GROUPS) ? explode(
            ',',
            CFG_GLOBAL_SEARCH_ALLOWED_GROUPS
        ) : []);

        return in_array($app_user['group_id'], $allowed_groups);
    }

    static function has_search_in_comments()
    {
        global $app_user;

        $entities_query = db_query(
            "select gs.*, e.name from app_ext_global_search_entities gs, app_entities e where gs.entities_id=e.id order by gs.sort_order,gs.id"
        );

        $count = 0;
        while ($entities = db_fetch_array($entities_query)) {
            if (!users::has_users_access_name_to_entity('view', $entities['entities_id'])) {
                continue;
            }

            $entity_cfg = new entities_cfg($entities['entities_id']);

            if ($entity_cfg->get('use_comments') == 1) {
                $count++;
            }
        }

        return ($count > 0 ? true : false);
    }

    static function render($type = 'search-form-header')
    {
        global $app_user, $app_module_path;

        if (CFG_USE_GLOBAL_SEARCH != 1) {
            return '';
        }

        if (CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER != 1) {
            return '';
        }

        if ($app_module_path == 'global_search/search') {
            return '';
        }

        $allowed_groups = (strlen(CFG_GLOBAL_SEARCH_ALLOWED_GROUPS) ? explode(
            ',',
            CFG_GLOBAL_SEARCH_ALLOWED_GROUPS
        ) : []);

        if (!in_array($app_user['group_id'], $allowed_groups)) {
            return '';
        }

        $attributes = [
            'class' => 'form-control ' . ($type == 'search-form-header' ? 'input-medium input-sm' : ''),
            'autocomplete' => 'off',
            'required' => 'required'
        ];

        $attributes['placeholder'] = (defined(
            'CFG_GLOBAL_SEARCH_INPUT_TOOLTIP'
        ) ? CFG_GLOBAL_SEARCH_INPUT_TOOLTIP : TEXT_SEARCH);

        if (strlen(CFG_GLOBAL_SEARCH_INPUT_MIN)) {
            $attributes['minlength'] = CFG_GLOBAL_SEARCH_INPUT_MIN;
        }

        if (strlen(CFG_GLOBAL_SEARCH_INPUT_MAX)) {
            $attributes['maxlength'] = CFG_GLOBAL_SEARCH_INPUT_MAX;
        }

        $html = '
					<form class="search-form ' . $type . '"  role="form" action="' . url_for('global_search/search') . '" method="post">
						<div class="input-icon right">
							<i class="fa fa-search icon-search"></i>
							' . input_tag(
                'keywords',
                (isset($_POST['keywords']) ? $_POST['keywords'] : ''),
                $attributes
            ) . '
						</div>
						<input type="submit" style="display:none">
					</form>
					';

        return $html;
    }

    static function render_fields_in_listing(
        $entities_id,
        $item_id,
        $fields_in_listing,
        $search_in_comments,
        $entity_cfg,
        $heading_field_id
    ) {
        global $app_user, $fields_access_schema_holder, $items_info_formula_sql_holder;

        if (!strlen($fields_in_listing)) {
            return '';
        }

        $fields_in_listing = explode(',', $fields_in_listing);

        if ($key = array_search($heading_field_id, $fields_in_listing)) {
            unset($fields_in_listing[$key]);
        }

        $fields_in_listing = implode(',', $fields_in_listing);

        if (!isset($fields_access_schema_holder[$entities_id])) {
            $fields_access_schema_holder[$entities_id] = users::get_fields_access_schema(
                $entities_id,
                $app_user['group_id']
            );
            $items_info_formula_sql_holder[$entities_id] = fieldtype_formula::prepare_query_select(
                $entities_id,
                '',
                false,
                ['fields_in_listing' => $fields_in_listing]
            );
        }

        $html = '';

        $items_info_sql = "select e.* {$items_info_formula_sql_holder[$entities_id]} from app_entity_" . $entities_id . " e where e.id='" . db_input(
                $item_id
            ) . "'";
        $items_query = db_query($items_info_sql);
        if ($item = db_fetch_array($items_query)) {
            $fields_array = fields::get_items_fields_data_by_id(
                $item,
                $fields_in_listing,
                $entities_id,
                $fields_access_schema_holder[$entities_id]
            );

            if (count($fields_array)) {
                $html = '<ul class="' . (is_mobile() ? '' : 'list-inline') . '" style="margin-bottom: 0px;">';
                foreach ($fields_array as $field) {
                    if (!strlen($field['value'])) {
                        continue;
                    }

                    $html .= '
								<li>' . $field['name'] . ': ' . strip_tags($field['value']) . '</li>	
							';
                }
                $html .= '</ul>';
            }
        }

        //print_r($fields_in_listing);

        return $html;
        //foreach($fields_in_listing)


    }

}