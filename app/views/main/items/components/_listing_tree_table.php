<?php

if (!defined('KERUY_CRM')) {
    exit;
}

$cfg_tree_table_editable_fields_in_listing = $listing->settings->get('editable_fields_in_listing');

$html .= '
<div class="table-scrollable ">
  <div class="table-scrollable table-wrapper slimScroll" id="slimScroll">
    <table class="tree-table table table-striped table-bordered table-hover ' . ($listing->is_resizable(
    ) ? 'table-resizable' : '') . '"  data-fixed-head="1" data-resizable="' . $listing->is_resizable(
    ) . '" ' . $listing->resizable_table_widht() . '>
      <thead>
        <tr>
          ' . ($has_with_selected ? '<th class="multiple-select-action-th">' . input_checkbox_tag(
            'select_all_items',
            $_POST['reports_id'],
            ['class' => 'select_all_items', 'is_tree_view' => 1]
        ) . '</th>' : '');

//render listing heading
$listing_fields = [];
$listing_numeric_fields = [];
$fields_query = db_query($listing->get_fields_query());
while ($v = db_fetch_array($fields_query)) {
    //check field access
    if (isset($fields_access_schema[$v['id']])) {
        if ($fields_access_schema[$v['id']] == 'hide') {
            continue;
        }
    }

    //skip fieldtype_parent_item_id for deafult listing
    if ($v['type'] == 'fieldtype_parent_item_id' and (strlen(
                $app_redirect_to
            ) == 0 or $current_entity_info['parent_id'] == 0 or $listing->report_type == 'parent_item_info_page')) {
        continue;
    }

    if ($v['type'] == 'fieldtype_dropdown_multilevel') {
        $html .= fieldtype_dropdown_multilevel::output_listing_heading($v, false, $listing);
    } else {
        $html .= '
	      <th  data-field-id="' . $v['id'] . '" ' . $listing->get_listing_col_width($v['id']) . '>
	      		<div ' . (strlen($v['short_name']) ? 'title="' . htmlspecialchars(
                    $v['long_name']
                ) . '"' : '') . '>' . fields_types::get_option($v['type'], 'name', $v['name']) . '</div>
	      </th>
	  ';
    }

    $listing_fields[] = $v;

    $field_cfg = new fields_types_cfg($v['configuration']);

    if (in_array(
            $v['type'],
            [
                'fieldtype_months_difference',
                'fieldtype_years_difference',
                'fieldtype_hours_difference',
                'fieldtype_days_difference',
                'fieldtype_mysql_query',
                'fieldtype_input_numeric',
                'fieldtype_formula',
                'fieldtype_js_formula',
                'fieldtype_input_numeric_comments'
            ]
        ) and ($field_cfg->get('calculate_totals') == 1 or $field_cfg->get('calculate_average') == 1)) {
        $listing_numeric_fields[] = $v['id'];
    }
}

$html .= '
    </tr>
  </thead>
  <tbody>
';

while ($item = db_fetch_array($items_query)) {
    $html .= '
      <tr class="' . (($users_notifications->has($item['id']) and $entity_cfg->get(
                'disable_highlight_unread'
            ) != 1) ? 'unread-item-row' : '') . $listing_highlight->apply($item) . '">
        ';

    //perpare selected checkbox
    $hide_actions_buttons = false;

    if ($has_with_selected) {
        $checkbox_html = '<td>' . input_checkbox_tag(
                'items_' . $item['id'],
                $item['id'],
                [
                    'class' => 'items_checkbox',
                    'checked' => in_array($item['id'], $app_selected_items[$_POST['reports_id']])
                ]
            ) . '</td>';

        //check access to action with assigned only
        if (users::has_users_access_name_to_entity('action_with_assigned', $current_entity_id)) {
            if (users::has_access_to_assigned_item($current_entity_id, $item['id'])) {
                $html .= $checkbox_html;
            } else {
                $html .= '<td></td>';

                $hide_actions_buttons = true;
            }
        } else {
            $html .= $checkbox_html;
        }
    }
    //end prepare selected checkbox

    $path_info_in_report = [];

    if ($reports_entities_id > 0 and $current_entity_info['parent_id'] > 0) {
        $path_info_in_report = items::get_path_info($_POST['reports_entities_id'], $item['id'], $item);
    }

    foreach ($listing_fields as $field) {
        //check field access
        if (isset($fields_access_schema[$field['id']])) {
            if ($fields_access_schema[$field['id']] == 'hide') {
                continue;
            }
        }

        if ($field['type'] == 'fieldtype_parent_item_id' and (strlen(
                    $app_redirect_to
                ) == 0 or $current_entity_info['parent_id'] == 0 or $listing->report_type == 'parent_item_info_page')) {
            continue;
        }

        //configure editable listing
        $editable_listing = new editable_listing(
            $current_entity_id,
            $item,
            $field,
            $fields_access_schema,
            $reports_info['id'],
            $listing_split->current_page_number,
            $listing->listing_type
        );

        //prepare field value
        $value = items::prepare_field_value_by_type($field, $item);

        $output_options = [
            'class' => $field['type'],
            'value' => $value,
            'field' => $field,
            'item' => $item,
            'is_listing' => true,
            'redirect_to' => $app_redirect_to,
            'reports_id' => ($reports_entities_id > 0 ? $_POST['reports_id'] : 0),
            'path' => (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_path),
            'path_info' => $path_info_in_report,
            'hide_actions_buttons' => $hide_actions_buttons,
            'listing_type' => $listing->get_listing_type(),
        ];

        if ($field['is_heading'] == 1) {
            //get fields in popup
            $popup_html = '';
            if (strlen($_POST['force_popoup_fields'])) {
                $fields_in_popup = fields::get_items_fields_data_by_id(
                    $item,
                    $_POST['force_popoup_fields'],
                    $current_entity_id,
                    $fields_access_schema
                );

                if (count($fields_in_popup)) {
                    $popup_html = app_render_fields_popup_html($fields_in_popup, $reports_info);
                }
            }

            $path = (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path'] : $current_path . '-' . $item['id']);

            $data_sort_url = (users::has_access('create') ? ' data-tt-sort-url="' . url_for(
                    'items/sort_nested',
                    'path=' . $path . '&redirect_to=' . $app_redirect_to . '&gotopage[' . _POST(
                        'reports_id'
                    ) . ']=' . _POST('page')
                ) . '" ' : '');

            $html .= '
                <td class="' . $field['type'] . $editable_listing->td_css_class(
                ) . '  field-' . $field['id'] . '-td item_heading_td' . (($listing->settings->get(
                        'heading_width_based_content'
                    ) == 1 or $listing->settings->get(
                        'change_col_width_in_listing'
                    ) == 1) ? ' width-auto' : '') . '" ' . $editable_listing->td_params() . '>
                    <div class="tt" 
                        ' . $data_sort_url . ' data-tt-id="item_' . _POST(
                    'reports_id'
                ) . '_' . $item['id'] . '" ' . ($item['parent_id'] > 0 ? ' data-tt-parent="item_' . _POST(
                        'reports_id'
                    ) . '_' . $item['parent_id'] . '"' : '') . '>
                    </div>
                    <a ' . $popup_html . ' class="item_heading_link" href="' . url_for(
                    'items/info',
                    'path=' . $path . '&redirect_to=subentity&gotopage[' . $_POST['reports_id'] . ']=' . $_POST['page']
                ) . '">' . fields_types::output($output_options) . '</a>
                ';

            if ($entity_cfg->get('use_comments') == 1 and $user_has_comments_access and $entity_cfg->get(
                    'display_last_comment_in_listing',
                    1
                )) {
                $html .= comments::get_last_comment_info($current_entity_id, $item['id'], $path, $fields_access_schema);
            }

            $html .= '</td>';
        } elseif ($field['type'] == 'fieldtype_dropdown_multilevel') {
            $html .= fieldtype_dropdown_multilevel::output_listing($output_options);
        } else {
            $td_class = (in_array(
                $field['type'],
                ['fieldtype_action', 'fieldtype_date_added', 'fieldtype_input_datetime']
            ) ? $field['type'] . ' field-' . $field['id'] . '-td nowrap' : $field['type'] . ' field-' . $field['id'] . '-td');
            $td_class .= $editable_listing->td_css_class();
            $html .= '
                        <td class="' . $td_class . '" ' . $editable_listing->td_params() . '>' . fields_types::output(
                    $output_options
                ) . '</td>
                    ';
        }
    }

    $html .= '
      </tr>
  ';

    $tree_table = new tree_table($current_entity_id, $listing_fields, $fields_access_schema);
    $tree_table->users_notifications = $users_notifications;
    $tree_table->entity_cfg = $entity_cfg;
    $tree_table->listing_highlight = $listing_highlight;
    $tree_table->listing = $listing;
    $tree_table->reports_id = _POST('reports_id');
    $tree_table->redirect_to = $app_redirect_to;
    $tree_table->user_has_comments_access = $user_has_comments_access;
    $tree_table->current_page_number = $listing_split->current_page_number;
    $html .= $tree_table->render_nested($item['id']);
}

if ($listing_split->number_of_rows == 0) {
    $html .= '
    <tr>
      <td colspan="100">' . TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  ';
}

$html .= '
  </tbody>';

if (count($listing_numeric_fields) > 0) {
    require(component_path('items/calculate_fields_totals'));
}

$html .= '
    </table>
		<div class="tableScrollRailX"></div>
  	<div class="tableScrollBarX"></div>
  </div>
</div>
';

//add pager
$html .= '
<div class="row">
  <div class="col-md-5 col-sm-12">' . $listing_split->display_count() . '</div>
  <div class="col-md-7 col-sm-12">' . $listing_split->display_links() . '</div>
</div>
';

//show hidden blocks on info page
if ($listing_split->number_of_rows > 0) {
    $html .= '
        <script>
            $("#' . $_POST['listing_container'] . '_info_block").show();
                
            //tree table view
            $("#' . $_POST['listing_container'] . ' .tree-table").treetable();
        </script>';
}

echo $html;

