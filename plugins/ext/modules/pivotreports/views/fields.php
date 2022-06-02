<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_PIVOTREPORTS,
        url_for('ext/pivotreports/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        $reports['name'],
        url_for('ext/pivotreports/view', 'id=' . $_GET['id'])
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_PIVOTREPORTS_FIELDS,
        url_for('ext/pivotreports/fields', 'id=' . $_GET['id'])
    ) . '</li>';

?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>

<h3 class="page-title"><?php
    echo TEXT_EXT_PIVOTREPORTS_FIELDS ?></h3>

<div class="alert alert-info"><?php
    echo TEXT_EXT_PIVOTREPORTS_FIELDS_TIP ?></div>

<?php
$entities_list = [];
$entities_list[] = $reports['entities_id'];

$parrent_entities = entities::get_parents($reports['entities_id']);

if (count($parrent_entities) > 0) {
    $parrent_entities = array_reverse($parrent_entities);
    $entities_list = array_merge($parrent_entities, $entities_list);
}

//print_r($entities_list);


$allowed_fields_types = [
    'fieldtype_date_added',
    'fieldtype_dropdown',
    'fieldtype_dropdown_multiple',
    'fieldtype_stages',
    'fieldtype_input',
    'fieldtype_boolean',
    'fieldtype_progress',
    'fieldtype_input_date',
    'fieldtype_input_datetime',
    'fieldtype_input_numeric',
    'fieldtype_input_numeric_comments',
    'fieldtype_formula',
    'fieldtype_js_formula',
    'fieldtype_entity',
    'fieldtype_entity_ajax',
    'fieldtype_users',
    'fieldtype_users_ajax',
    'fieldtype_grouped_users',
    'fieldtype_created_by',
    'fieldtype_radioboxes',
    'fieldtype_checkboxes',
    'fieldtype_mysql_query',
    'fieldtype_days_difference',
    'fieldtype_hours_difference',
    'fieldtype_years_difference',
    'fieldtype_months_difference',
    'fieldtype_dynamic_date',
    'fieldtype_entity_multilevel',
    'fieldtype_ajax_request',
    'fieldtype_tags',
    'fieldtype_php_code',
    'fieldtype_input_masked',
    'fieldtype_text_pattern',
    'fieldtype_text_pattern_static',

];


$reports_fields = [];
$reports_fields_cfg = [];
$pivotreports_fields_query = db_query(
    "select * from app_ext_pivotreports_fields where pivotreports_id='" . db_input($_GET['id']) . "'"
);
while ($pivotreports_fields = db_fetch_array($pivotreports_fields_query)) {
    $reports_fields[] = $pivotreports_fields['fields_id'];
    $reports_fields_cfg[$pivotreports_fields['fields_id']] = [
        'name' => $pivotreports_fields['fields_name'],
        'date_format' => $pivotreports_fields['cfg_date_format']
    ];
}

$html = form_tag('fiels_form', url_for('ext/pivotreports/fields', 'id=' . $_GET['id'] . '&action=save')) . '
  	<div class="table-scrollable">	
  		<table class="table table-striped table-bordered table-hover">
  			<thead>
	  			<tr>
	  				<th style="width: 30px;"></th>
	  				<th width="100%">' . TEXT_FIELDS . '</th>
			  		<th>' . TEXT_EXT_PIVOTREPORTS_FIELDS_NAME . '</th>
			  		<th>' . TEXT_DATE_FORMAT . '</th>
	  			</tr>
				</thead>
				<tbody>';

$entities_heading = '';

foreach ($entities_list as $entities_id) {
    $entities_info = db_find('app_entities', $entities_id);
    if ($entities_heading != $entities_info['name']) {
        $entities_heading = $entities_info['name'];

        $html .= '
				<tr>
					<td colspan="4" style="padding-top: 15px;"><b>' . $entities_heading . '</b></td>
				</tr>
			';
    }

    $fields_query = db_query(
        "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('" . implode(
            '\',\'',
            $allowed_fields_types
        ) . "') and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
    );
    while ($fields = db_fetch_array($fields_query)) {
        $html .= '
				<tr>
					<td>' . input_checkbox_tag(
                'fields[' . $entities_id . '][' . $fields['id'] . ']',
                $fields['id'],
                ['checked' => in_array($fields['id'], $reports_fields)]
            ) . '</td>
					<td><label for="fields_' . $entities_id . '_' . $fields['id'] . '">' . fields_types::get_option(
                $fields['type'],
                'name',
                $fields['name']
            ) . '</label></td>
					<td>' . input_tag(
                'fields_name[' . $entities_id . '][' . $fields['id'] . ']',
                (isset($reports_fields_cfg[$fields['id']]) ? $reports_fields_cfg[$fields['id']]['name'] : ''),
                ['class' => 'form-control input-medium']
            ) . '</td>
					<td>' . (in_array(
                $fields['type'],
                ['fieldtype_date_added', 'fieldtype_input_date', 'fieldtype_input_datetime']
            ) ?
                input_tag(
                    'fields_date_format[' . $entities_id . '][' . $fields['id'] . ']',
                    (isset($reports_fields_cfg[$fields['id']]) ? $reports_fields_cfg[$fields['id']]['date_format'] : ''),
                    ['class' => 'form-control input-small']
                ) : '') . '</td>
				</tr>
			';
    }
}

$html .= '
					</tbody>
				</table>
			</div>
			' . submit_tag(TEXT_BUTTON_SAVE) . ' <a href="' . url_for(
        'ext/pivotreports/reports'
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>
		</form>';

echo $html;
?>
