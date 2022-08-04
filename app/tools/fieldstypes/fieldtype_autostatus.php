<?php

namespace Tools\FieldsTypes;

class Fieldtype_autostatus
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_AUTOSTATUS_TITLE, 'has_choices' => true];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED,
            'name' => 'notify_when_changed',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_NOTIFY_WHEN_CHANGED_TIP
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_TYPE,
            'name' => 'panel_type',
            'type' => 'dropdown',
            'params' => ['class' => 'form-control input-medium'],
            'choices' => ['' => ''] + stages_panel::get_type_choices()
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_COLOR,
            'name' => 'color',
            'type' => 'colorpicker'
        ];

        $cfg[\K::$fw->TEXT_STAGES_PANEL][] = [
            'title' => \K::$fw->TEXT_ACTIVE_ITEM_COLOR,
            'name' => 'color_active',
            'type' => 'colorpicker'
        ];

        $cfg[\K::$fw->TEXT_ACTION][] = [
            'html' => '<p>' . \K::$fw->TEXT_FIELDTYPE_AUTOSTATUS_ACTION_TIP . '</p>',
            'type' => 'html'
        ];

        if (\Helpers\App::is_ext_installed()) {
            $processes_chocies = [];
            $processes_chocies[0] = '';
            $processes_query = db_query(
                "select id, name from app_ext_processes where entities_id='" . _post::int(
                    'entities_id'
                ) . "' order by sort_order, name"
            );
            while ($processes = db_fetch_array($processes_query)) {
                $processes_chocies[$processes['id']] = $processes['name'];
            }

            foreach (fields_choices::get_choices(_POST('id'), false) as $choice_id => $choice_name) {
                $cfg[\K::$fw->TEXT_ACTION][] = [
                    'title' => $choice_name,
                    'name' => 'run_process_for_choice_' . $choice_id,
                    'type' => 'dropdown',
                    'choices' => $processes_chocies,
                    'params' => ['class' => 'form-control input-large']
                ];
            }
        } else {
            $cfg[\K::$fw->TEXT_ACTION][] = [
                'html' => '<div class="alert alert-warning">' . \K::$fw->TEXT_EXTENSION_REQUIRED . '</div>',
                'type' => 'html'
            ];
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '<p><table><tr><td>' . fields_choices::render_value(
                $obj['field_' . $field['id']]
            ) . '</td></tr></table></p>' . input_hidden_tag(
                'fields[' . $field['id'] . ']',
                $obj['field_' . $field['id']]
            );
    }

    public function process($options)
    {
        return $options['value'];
    }

    public function output($options)
    {
        return fields_choices::render_value($options['value']);
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        $sql_query[] = $prefix . '.field_' . $filters['fields_id'] . ($filters['filters_condition'] == 'include' ? ' in ' : ' not in ') . '(' . $filters['filters_values'] . ') ';

        return $sql_query;
    }

    public static function set($entities_id, $items_id)
    {
        /*$fields_query = db_query(
            "select * from app_fields where entities_id='" . db_input(
                $entities_id
            ) . "' and type='fieldtype_autostatus'"
        );*/
        $fields_query = \K::model()->db_fetch('app_fields', [
            'entities_id = ? and type = ?',
            $entities_id,
            'fieldtype_autostatus'
        ]);

        //while ($fields = db_fetch_array($fields_query)) {
        $forceCommit = \K::model()->forceCommit();

        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $cfg = new \Models\Main\Fields_types_cfg($fields['configuration']);

            $fields_choices = \Models\Main\Fields_choices::get_tree($fields['id'], 0, [], 0, '', '', true);
            foreach ($fields_choices as $choices) {
                /*$reports_info_query = db_query(
                    "select * from app_reports where entities_id='" . db_input(
                        $entities_id
                    ) . "' and reports_type='fields_choices" . $choices['id'] . "'"
                );*/

                $reports_info = \K::model()->db_fetch_one('app_reports', [
                    'entities_id = ? and reports_type = ?',
                    $entities_id,
                    'fields_choices' . $choices['id']
                ], [], 'id');

                if ($reports_info) {
                    \K::$fw->sql_query_having = [];

                    $listing_sql_query = \Models\Main\Reports\Reports::add_filters_query($reports_info['id'], '');

                    //prepare having query for formula fields
                    if (isset(\K::$fw->sql_query_having[$entities_id])) {
                        $listing_sql_query .= \Models\Main\Reports\Reports::prepare_filters_having_query(
                            \K::$fw->sql_query_having[$entities_id]
                        );
                    }

                    $item_info_query = \K::model()->db_query_exec(
                        "select e.* " . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                            $entities_id,
                            ''
                        ) . " from app_entity_" . $entities_id . " e where e.id = ? " . $listing_sql_query,
                        $items_id
                    );
                    if (isset($item_info_query[0])) {
                        $item_info = $item_info_query[0];

                        if ($choices['id'] != $item_info['field_' . $fields['id']] and $cfg->get(
                                'notify_when_changed'
                            ) == 1) {
                            \K::$fw->app_changed_fields[] = [
                                'name' => $fields['name'],
                                'value' => \K::$fw->app_choices_cache[$choices['id']]['name'],
                                'fields_id' => $fields['id'],
                                'fields_value' => $choices['id'],
                            ];
                        }

                        $sql_data = [
                            'field_' . $fields['id'] => $choices['id']
                        ];

                        \K::model()->db_perform(
                            'app_entity_' . $entities_id,
                            $sql_data,
                            [
                                'id = ?',
                                $items_id
                            ]
                        );

                        //run process
                        if (\Helpers\App::is_ext_installed() and ($process_id = (int)$cfg->get(
                                'run_process_for_choice_' . $choices['id']
                            )) > 0 and $choices['id'] != $item_info['field_' . $fields['id']]) {
                            //$process_info_query = db_query("select * from app_ext_processes where id={$process_id}");

                            $process_info = \K::model()->db_fetch_one('app_ext_processes', [
                                'id = ?',
                                $process_id
                            ]);

                            if ($process_info) {
                                $_post_fields = \K::$fw->POST['fields'] ?? []; //save post fields
                                \K::$fw->POST['fields'] = []; //reset post fields

                                $processes = new processes($entities_id);
                                $processes->items_id = $items_id;
                                $processes->run($process_info, false, true);

                                \K::$fw->POST['fields'] = $_post_fields; //restore post fields;
                            }
                        }
                        //break from current fields choices
                        break;
                    }
                }
            }
        }

        if ($forceCommit) {
            \K::model()->commit();
        }

        return true;
    }
}
