<?php

$panels_id = _get::int('panels_id');
$entities_id = _get::int('entities_id');

switch ($app_module_action) {
    case 'save':

        $sql_data = [
            'panels_id' => $panels_id,
            'entities_id' => $entities_id,
            'fields_id' => $_POST['fields_id'],
            'title' => $_POST['title'],
            'width' => (isset($_POST['width']) ? $_POST['width'] : ''),
            'exclude_values' => (isset($_POST['exclude_values']) ? implode(',', $_POST['exclude_values']) : ''),
            'display_type' => (isset($_POST['display_type']) ? $_POST['display_type'] : ''),
            'search_type_match' => (isset($_POST['search_type_match']) ? $_POST['search_type_match'] : ''),
            'height' => (isset($_POST['height']) ? $_POST['height'] : ''),

        ];

        if (isset($_GET['id'])) {
            db_perform('app_filters_panels_fields', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            $fields_query = db_query(
                "select max(sort_order) as max_sort_order from app_filters_panels_fields where panels_id='" . _get::int(
                    'panels_id'
                ) . "'"
            );
            $fields = db_fetch_array($fields_query);

            $sql_data['sort_order'] = $fields['max_sort_order'] + 1;

            db_perform('app_filters_panels_fields', $sql_data);
        }

        redirect_to(
            'ext/filters_panels/fields',
            'panels_id=' . $panels_id . '&redirect_to=' . $app_redirect_to . '&entities_id=' . $_GET['entities_id']
        );

        break;

    case 'delete':

        db_delete_row('app_filters_panels_fields', _get::int('id'));

        redirect_to(
            'ext/filters_panels/fields',
            'panels_id=' . $panels_id . '&redirect_to=' . $app_redirect_to . '&entities_id=' . $_GET['entities_id']
        );
        break;

    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];

        if (strlen($choices_sorted) > 0) {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);

            $sort_order = 0;
            foreach ($choices_sorted as $v) {
                db_query("update app_filters_panels_fields set sort_order={$sort_order} where id={$v['id']}");
                $sort_order++;
            }
        }

        redirect_to(
            'ext/filters_panels/fields',
            'entities_id=' . $_GET['entities_id'] . '&redirect_to=' . $app_redirect_to . '&panels_id=' . $panels_id
        );
        break;
}

