<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_input_numeric_comments
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TITLE];
    }

    public function get_configuration($params = [])
    {
        $cfg = [];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_NUMBER_FORMAT_INFO) . \K::$fw->TEXT_NUMBER_FORMAT,
            'name' => 'number_format',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'],
            'default' => \K::$fw->CFG_APP_NUMBER_FORMAT
        ];

        $cfg[] = [
            'title' => \Helpers\App::tooltip_icon(\K::$fw->TEXT_CALCULATE_TOTALS_INFO) . \K::$fw->TEXT_CALCULATE_TOTALS,
            'name' => 'calculate_totals',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_CALCULATE_AVERAGE_VALUE,
            'name' => 'calculate_average',
            'type' => 'checkbox'
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DEFAULT_VALUE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT_VALUE_INFO,
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PREFIX,
            'name' => 'prefix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_SUFFIX,
            'name' => 'suffix',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_DISPLAY_PREFIX_SUFFIX_IN_FORM,
            'name' => 'display_prefix_suffix_in_form',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        if ($params['form'] == 'comment') {
            $value = '';

            //handle default value
            if (strlen($cfg->get('default_value')) > 0) {
                $value = $cfg->get('default_value');
            }

            if ($cfg->get('display_prefix_suffix_in_form') == 1 and (strlen($cfg->get('prefix')) or strlen(
                        $cfg->get('suffix')
                    ))) {
                return '
    			<div class="input-group input-small">
						' . (strlen($cfg->get('prefix')) ? '<span class="input-group-addon">' . $cfg->get(
                            'prefix'
                        ) . '</span>' : '')
                    . \Helpers\Html::input_tag(
                        'fields[' . $field['id'] . ']',
                        $value,
                        ['class' => 'form-control input-small fieldtype_input_numeric field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '') . ' number']
                    )
                    . (strlen($cfg->get('suffix')) ? '<span class="input-group-addon">' . $cfg->get(
                            'suffix'
                        ) . '</span>' : '') .
                    '</div>
    			';
            } else {
                return \Helpers\Html::input_tag(
                    'fields[' . $field['id'] . ']',
                    $value,
                    ['class' => 'form-control input-small fieldtype_input_numeric field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '') . ' number']
                );
            }
        } else {
            return '<p class="form-control-static">' . $cfg->get('prefix') . $obj['field_' . $field['id']] . $cfg->get(
                    'suffix'
                ) . '</p>' . \Helpers\Html::input_hidden_tag(
                    'fields[' . $field['id'] . ']',
                    $obj['field_' . $field['id']]
                );
        }
    }

    public function get_fields_sum($entity_id, $item_id, $field_id)
    {
        $total = 0;

        /*$comments_query = db_query(
            "select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input(
                $item_id
            ) . "'"
        );*/

        $comments_query = \K::model()->db_fetch('app_comments', [
            'entities_id = ? and items_id = ?',
            $entity_id,
            $item_id
        ], [], 'id');

        //while ($comments = db_fetch_array($comments_query)) {
        foreach ($comments_query as $comments) {
            $comments = $comments->cast();

            /*$history_query = db_query(
                "select * from app_comments_history where comments_id='" . db_input(
                    $comments['id']
                ) . "' and fields_id='" . $field_id . "'"
            );*/

            $history_query = \K::model()->db_fetch('app_comments_history', [
                'comments_id = ? and fields_id = ?',
                $comments['id'],
                $field_id
            ], [], 'fields_value');

            //while ($history = db_fetch_array($history_query)) {
            foreach ($history_query as $history) {
                $history = $history->cast();

                $total += $history['fields_value'];
            }
        }

        return $total;
    }

    public function process($options)
    {
        return str_replace([',', ' '], ['.', ''], \K::model()->db_prepare_input($options['value']));
    }

    public function output($options)
    {
        //return non-formatted value if export
        if (isset($options['is_export']) and !isset($options['is_print'])) {
            return $options['value'];
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (strlen($cfg->get('number_format')) > 0 and strlen($options['value']) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($options['value'], $format[0], $format[1], $format[2]);
        } elseif (strstr($options['value'], '.')) {
            $value = number_format((float)$options['value'], 2, '.', '');
        } else {
            $value = $options['value'];
        }

        //add prefix and suffix
        return (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_numeric_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}