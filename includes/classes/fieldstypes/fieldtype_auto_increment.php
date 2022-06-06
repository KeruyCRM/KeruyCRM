<?php

class fieldtype_auto_increment
{
    public $options;

    function __construct()
    {
        $this->options = ['title' => TEXT_FIELDTYPE_AUTO_INCREMENT_TITLE];
    }

    function get_configuration()
    {
        $cfg = [];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => TEXT_INPUT_SMALL,
                'input-medium' => TEXT_INPUT_MEDIUM,
                'input-large' => TEXT_INPUT_LARGE,
                'input-xlarge' => TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[TEXT_SETTINGS][] = [
            'title' => TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_VIEW_ONLY,
            'name' => 'view_only',
            'type' => 'checkbox',
            'tooltip_icon' => TEXT_VALUE_VIEW_ONLY_INFO
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => TEXT_DEFAULT_VALUE_INFO,
            'tooltip' => TEXT_DEFAULT . ': 1',
            'params' => ['class' => 'form-control input-small number']
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_STEP,
            'name' => 'step',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small'],
            'tooltip' => TEXT_DEFAULT . ': ' . 1
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];
        $cfg[TEXT_VALUE][] = [
            'title' => TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $entity_info = db_find('app_entities', $_POST['entities_id']);
        if ($entity_info['parent_id'] > 0) {
            $cfg[TEXT_VALUE][] = [
                'title' => TEXT_FIELDTYPE_AUTO_INCREMENT_SEPARATE_NUMBERING,
                'name' => 'separate_numbering',
                'type' => 'checkbox'
            ];
        }

        return $cfg;
    }

    function render($field, $obj, $params = [])
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : '')
        ];

        //set auto increment for new item
        if (isset($params['is_new_item']) and $params['is_new_item'] == 1) {
            $obj['field_' . $field['id']] = $this->increment($field);
        }

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);


        if ($cfg->get('view_only') == 1) {
            return '<p class="form-control-static">' . $cfg->get('prefix') . $obj['field_' . $field['id']] . $cfg->get(
                    'suffix'
                ) . '</p>' . input_hidden_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']]);
        } else {
            return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
        }
    }

    function increment($field)
    {
        global $app_entities_cache, $parent_entity_item_id;

        $cfg = new fields_types_cfg($field['configuration']);

        $where_sql = '';

        //handle separate numbering for each parent recored
        if ($app_entities_cache[$field['entities_id']]['parent_id'] > 0 and $parent_entity_item_id > 0 and $cfg->get(
                'separate_numbering'
            ) == 1) {
            $where_sql .= " and parent_item_id='" . $parent_entity_item_id . "'";
        }

        $check_query = db_query(
            "select id from app_entity_{$field['entities_id']} where length(field_{$field['id']})>0 {$where_sql} limit 1"
        );

        if (db_num_rows($check_query)) {
            $step = (strlen($cfg->get('step')) ? $cfg->get('step') : 1);

            if ($step < 0) {
                $min_query = db_query(
                    "select (min(field_{$field['id']}+0)) as min_value from app_entity_{$field['entities_id']} where length(field_{$field['id']})>0 " . $where_sql
                );
                $min = db_fetch_array($min_query);
                eval('$value = $min["min_value"]' . $step . ';');
            } else {
                $max_query = db_query(
                    "select (max(field_{$field['id']}+0)) as max_value from app_entity_{$field['entities_id']} where length(field_{$field['id']})>0 " . $where_sql
                );
                $max = db_fetch_array($max_query);
                $value = $max['max_value'] + $step;
            }
        } else {
            $value = (strlen($cfg->get('default_value')) ? $cfg->get('default_value') : 1);
        }

        return $value;
    }

    function process($options)
    {
        $field = $options['field'];

        $cfg = new fields_types_cfg($field['configuration']);

        //if view only then recalculate value before save
        if ($cfg->get('view_only') == 1 and isset($options['is_new_item']) and $options['is_new_item'] == 1) {
            $options['value'] = $this->increment($field);
        }

        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

        return (strlen($options['value']) ? $cfg->get('prefix') . $options['value'] . $cfg->get('suffix') : '');
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = reports::prepare_numeric_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}