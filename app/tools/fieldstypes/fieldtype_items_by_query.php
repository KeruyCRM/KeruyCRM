<?php

namespace Tools\FieldsTypes;

class Fieldtype_items_by_query
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_ITEMS_BY_QUERY_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY,
            'name' => 'entity_id',
            'tooltip_icon' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY_TOOLTIP,
            'type' => 'dropdown',
            'choices' => entities::get_choices(),
            'params' => [
                'class' => 'form-control input-xlarge chosen-select',
                'onChange' => 'fields_types_ajax_configuration(\'fields_for_search_box\',this.value)'
            ]
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'name' => 'fields_for_search_box',
            'type' => 'ajax',
            'html' => '<script>fields_types_ajax_configuration(\'fields_for_search_box\',$("#fields_configuration_entity_id").val())</script>'
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_DEBUG_MODE,
            'name' => 'debug_mode',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_AS][] = [
            'title' => \K::$fw->TEXT_IN_ITEM_PAGE,
            'name' => 'display_as',
            'type' => 'dropdown',
            'choices' => ['number' => \K::$fw->TEXT_NUMBER_OF_RECORDS, 'list' => \K::$fw->TEXT_LIST],
            'default' => 'dropdown',
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_AS][] = [
            'title' => \K::$fw->TEXT_ITEMS_LISTING,
            'name' => 'display_as_in_listing',
            'type' => 'dropdown',
            'choices' => ['number' => \K::$fw->TEXT_NUMBER_OF_RECORDS, 'list' => \K::$fw->TEXT_LIST],
            'default' => 'dropdown',
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_AS][] = [
            'title' => tooltip_icon(\K::$fw->TEXT_DISPLAY_NAME_AS_LINK_INFO) . \K::$fw->TEXT_DISPLAY_NAME_AS_LINK,
            'name' => 'display_as_link',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_DISPLAY_AS][] = [
            'title' => \K::$fw->TEXT_DEFAULT_TEXT,
            'name' => 'default_text',
            'type' => 'input',
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_TOTAL . ' (#)',
            'params' => ['class' => 'form-control input-medium']
        ];

        return $cfg;
    }

    public function get_ajax_configuration($name, $value)
    {
        $cfg = [];

        switch ($name) {
            case 'fields_for_search_box':
                $entities_id = $value;

                $cfg[] = [
                    'title' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY .
                        fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_where_query',
                            entities::get_name_by_id($entities_id)
                        ) .
                        '<div style="padding-top: 2px;">' . fields::get_available_fields_helper(
                            $_POST['entities_id'],
                            'fields_configuration_where_query',
                            entities::get_name_by_id($_POST['entities_id'])
                        ) . '</div>',
                    'name' => 'where_query',
                    'type' => 'textarea',
                    'tooltip' => \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY_TIP,
                    'params' => ['class' => 'form-control input-xlarge code']
                ];

                $cfg[] = [
                    'title' => \K::$fw->TEXT_HEADING_TEMPLATE . fields::get_available_fields_helper(
                            $entities_id,
                            'fields_configuration_heading_template'
                        ),
                    'name' => 'heading_template',
                    'type' => 'textarea',
                    'tooltip_icon' => \K::$fw->TEXT_HEADING_TEMPLATE_INFO,
                    'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
                    'params' => ['class' => 'form-control input-xlarge code']
                ];

                break;
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return false;
    }

    public function process($options)
    {
        return false;
    }

    public function output($options)
    {
        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

        $mysql_query = $this->build_query($options);

        $debug_query = ($cfg->get(
            'debug_mode'
        ) == 1 ? '<div class="alert alert-warning" style="font-size: 11px; margin: 5px; padding: 3px; font-family:monospace;">' . htmlspecialchars(
                $mysql_query
            ) . '</div>' : '');

        $items_query = db_query($mysql_query);
        $count_items = db_num_rows($items_query);

        //print
        if (isset($options['is_print']) and $cfg->get('display_as') == 'list') {
            return $this->get_items_list_simple($items_query, $cfg);
        }

        //output export
        if (isset($options['is_export']) or isset($options['is_email'])) {
            if ($cfg->get('display_as_in_listing') == 'list') {
                $list = $this->get_items_list_array($items_query, $cfg);

                return implode(', ', $list);
            } else {
                $html = (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : \K::$fw->TEXT_TOTAL . ' (#)');
                $html = (strstr($html, '#') ? str_replace('#', $count_items, $html) : $html . $count_items);

                return $html;
            }
        }

        //displya number of records
        if (($cfg->get('display_as') == 'number' and !isset($options['is_listing'])) or ($cfg->get(
                    'display_as_in_listing'
                ) == 'number' and isset($options['is_listing']))) {
            $html = (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : \K::$fw->TEXT_TOTAL . ' (#)');
            $html = (strstr($html, '#') ? str_replace('#', $count_items, $html) : $html . $count_items);

            $result = items::parse_path($options['path']);
            if ($result['item_id'] == 0) {
                $options['path'] = $options['path'] . '-' . $options['item']['id'];
            }

            return $debug_query . link_to_modalbox(
                    $html,
                    url_for(
                        'items/items_by_query',
                        'path=' . $options['path'] . '&fields_id=' . $options['field']['id']
                    )
                );
        }

        //display list of records

        if ($count_items == 0) {
            return $debug_query;
        }

        $html = $this->get_items_list($items_query, $cfg);

        return $debug_query . $html;
    }

    public function get_items_list($items_query, $cfg)
    {
        $html = '<ul class="list">';
        while ($item = db_fetch_array($items_query)) {
            if (strlen($heading_template = $cfg->get('heading_template'))) {
                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $item_name = $fieldtype_text_pattern->output_singe_text(
                    $heading_template,
                    $cfg->get('entity_id'),
                    $item
                );
            } else {
                $item_name = items::get_heading_field($cfg->get('entity_id'), $item['id'], $item);
            }

            if ($cfg->get('display_as_link') == 1) {
                $item_name = '<a href="' . url_for(
                        'items/info',
                        'path=' . $cfg->get('entity_id') . '-' . $item['id']
                    ) . '">' . $item_name . '</a>';
            }

            $html .= '
                <li>' . $item_name . '</li>
                ';
        }
        $html .= '</ul>';

        return $html;
    }

    public function get_items_list_simple($items_query, $cfg)
    {
        $html = '<ul>';
        while ($item = db_fetch_array($items_query)) {
            if (strlen($heading_template = $cfg->get('heading_template'))) {
                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $item_name = $fieldtype_text_pattern->output_singe_text(
                    $heading_template,
                    $cfg->get('entity_id'),
                    $item
                );
            } else {
                $item_name = items::get_heading_field($cfg->get('entity_id'), $item['id'], $item);
            }

            $html .= '
                <li>' . $item_name . '</li>
                ';
        }
        $html .= '</ul>';

        return $html;
    }

    public function get_items_list_array($items_query, $cfg)
    {
        $list = [];
        while ($item = db_fetch_array($items_query)) {
            if (strlen($heading_template = $cfg->get('heading_template'))) {
                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $item_name = $fieldtype_text_pattern->output_singe_text(
                    $heading_template,
                    $cfg->get('entity_id'),
                    $item
                );
            } else {
                $item_name = items::get_heading_field($cfg->get('entity_id'), $item['id'], $item);
            }

            $list[] = trim($item_name);
        }

        return $list;
    }

    public function build_query($options)
    {
        global $app_user, $app_entities_cache, $app_fields_cache;

        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

        //print_rr($options);

        $mysql_query_where = trim($cfg->get('where_query'));

        $parent_entity_item_id = $options['item']['parent_item_id'];

        $field = $options['field'];

        //prepare parent values
        if ($parent_entity_item_id > 0 and $app_entities_cache[$field['entities_id']]['parent_id'] > 0) {
            $item_info_query = db_query(
                "select * from app_entity_" . $app_entities_cache[$field['entities_id']]['parent_id'] . " where id=" . $parent_entity_item_id
            );
            if ($item_info = db_fetch_array($item_info_query)) {
                foreach ($item_info as $k => $v) {
                    if (strstr($k, 'field_')) {
                        $k = str_replace('field_', '', $k);
                        $mysql_query_where = str_replace('[' . $k . ']', $v, $mysql_query_where);
                    }
                }

                //check next parent
                $parent_entity_id = $app_entities_cache[$field['entities_id']]['parent_id'];

                if ($app_entities_cache[$parent_entity_id]['parent_id'] > 0 and $item_info['parent_item_id'] > 0) {
                    $item_info_query = db_query(
                        "select * from app_entity_" . $app_entities_cache[$parent_entity_id]['parent_id'] . " where id=" . $item_info['parent_item_id']
                    );
                    if ($item_info = db_fetch_array($item_info_query)) {
                        foreach ($item_info as $k => $v) {
                            if (strstr($k, 'field_')) {
                                $k = str_replace('field_', '', $k);
                                $mysql_query_where = str_replace('[' . $k . ']', $v, $mysql_query_where);
                            }
                        }
                    }
                }
            }
        }

        $mysql_query_where = str_replace('[current_user_id]', $app_user['id'], $mysql_query_where);
        $mysql_query_where = str_replace('[TODAY]', get_date_timestamp(date('Y-m-d')), $mysql_query_where);

        //replace current item value
        foreach ($options['item'] as $field_key => $field_value) {
            $field_key = str_replace('field_', '', $field_key);
            $mysql_query_where = str_replace('[' . $field_key . ']', $field_value, $mysql_query_where);
        }

        //prepare entity fields
        foreach ($app_fields_cache[$cfg->get('entity_id')] as $fields_id => $field) {
            $mysql_query_where = str_replace('[' . $fields_id . ']', 'e.field_' . $fields_id, $mysql_query_where);
        }

        $sql = "select e.* " . fieldtype_formula::prepare_query_select(
                $cfg->get('entity_id'),
                ''
            ) . " from app_entity_" . $cfg->get('entity_id') . " e " . (strlen(
                $mysql_query_where
            ) ? " where " . $mysql_query_where : '');

        return $sql;
    }
}