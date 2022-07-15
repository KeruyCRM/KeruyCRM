<?php

namespace Tools\FieldsTypes;

class Fieldtype_text_pattern
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_TEXT_PATTERN];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = [
            'title' => \K::$fw->TEXT_PATTERN . fields::get_available_fields_helper(
                    $_POST['entities_id'],
                    'fields_configuration_pattern'
                ),
            'name' => 'pattern',
            'type' => 'textarea',
            'tooltip' => \K::$fw->TEXT_ENTER_TEXT_PATTERN_INFO,
            'params' => ['class' => 'form-control']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '';
    }

    public function process($options)
    {
        return '';
    }

    public function output($options)
    {
        $html = '';

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $entities_id = $options['field']['entities_id'];

        $item = $options['item'];

        if (isset($options['custom_pattern'])) {
            $pattern = $options['custom_pattern'];
        } else {
            $pattern = $cfg->get('pattern');
        }

        if (strlen($pattern) > 0) {
            //handle current user name, id, item id etc
            $pattern = str_replace([
                '[current_user_name]',
                '[current_user_id]',
                '[id]',
            ], [
                (\K::$fw->app_user['name'] ?? ''),
                (\K::$fw->app_user['id'] ?? ''),
                $item['id'],
            ], $pattern);

            //handle date format ${Y-m-d}
            if (preg_match_all('/\${([^}]+)}/', $pattern, $matches)) {
                foreach ($matches[1] as $matches_key => $date_format) {
                    $pattern = str_replace($matches[0][$matches_key], date($date_format), $pattern);
                }
            }

            //num2str
            $pattern = \K::app_num2str()->prepare($pattern, $item);

            if (preg_match_all('/\[(\w+)\]/', $pattern, $matches)) {
                //use to check if formulas fields using in text pattern
                $formulas_fields = false;

                foreach ($matches[1] as $matches_key => $fields_id) {
                    $field = false;

                    if (isset(\K::$fw->app_fields_cache[$entities_id]['fieldtype_' . $fields_id])) {
                        $field = \K::$fw->app_fields_cache[$entities_id]['fieldtype_' . $fields_id];
                    } elseif (isset(\K::$fw->app_fields_cache[$entities_id][$fields_id])) {
                        $field = \K::$fw->app_fields_cache[$entities_id][$fields_id];
                    }

                    if ($field) {
                        switch ($field['type']) {
                            case 'fieldtype_parent_item_id':
                                $enitites_info = \K::$fw->app_entities_cache[$entities_id];

                                if ($enitites_info['parent_id'] > 0 and $item['parent_item_id'] > 0) {
                                    if (!isset(\K::$fw->parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']])) {
                                        $value = \K::$fw->parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']] = \Tools\Items\Items::get_heading_field(
                                            $enitites_info['parent_id'],
                                            $item['parent_item_id']
                                        );
                                    } else {
                                        $value = \K::$fw->parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']];
                                    }
                                } else {
                                    $value = '';
                                }
                                break;
                            case 'fieldtype_created_by':
                                $value = $item['created_by'];
                                break;
                            case 'fieldtype_date_added':
                                $value = $item['date_added'];
                                break;
                            case 'fieldtype_date_updated':
                                $value = $item['date_updated'];
                                break;
                            case 'fieldtype_action':
                            case 'fieldtype_id':
                                $value = $item['id'];
                                break;
                            case 'fieldtype_formula':

                                //check if formula value exist in item and if not then do extra query to calcualte it
                                if (strlen($item['field_' . $field['id']]) == 0) {
                                    //prepare formulas query
                                    if (!$formulas_fields) {
                                        $formulas_fields = \K::model()->db_query_exec_one(
                                            "select e.* " . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                                                $entities_id,
                                                ''
                                            ) . " from app_entity_" . $entities_id . " e where id = ?",
                                            $item['id']
                                        );
                                        //$formulas_fields = $formulas_fields_query[0] ?? '';
                                    }

                                    $value = $item['field_' . $field['id']] = $formulas_fields['field_' . $field['id']];
                                } else {
                                    $value = $item['field_' . $field['id']];
                                }
                                break;
                            default:
                                $value = $item['field_' . $field['id']];
                                break;
                        }

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'is_email' => ($options['is_email'] ?? false),
                            'hide_attachments_url' => ($options['hide_attachments_url'] ?? false),
                            'path' => ($options['path'] ?? '')
                        ];

                        //output full html if print option off
                        if (isset($options['is_print'])) {
                            if ($options['is_print'] == 0) {
                                unset($output_options['is_export']);
                                unset($output_options['is_print']);
                            }
                        }

                        if (in_array($field['type'], ['fieldtype_textarea_wysiwyg'])) {
                            $output = trim(\Models\Main\Fields_types::output($output_options));
                        } elseif (in_array($field['type'], ['fieldtype_user_photo'])) {
                            if (\K::$fw->app_module_path == 'dashboard/select2_users_json') {
                                $output = '<img src="' . \Helpers\Urls::url_for(
                                        'main/dashboard/select2_users_json/preview_image',
                                        'form_type=' . \K::$fw->GET['form_type'] . '&entity_id=' . \K::$fw->GET['entity_id'] . '&field_id=' . \K::$fw->GET['field_id'] . '&file=' . urlencode(
                                            base64_encode($output_options['value'])
                                        ),
                                        true
                                    ) . '">';
                            } else {
                                $output = \Models\Main\Fields_types::output($output_options);
                            }
                        } elseif (in_array($field['type'], ['fieldtype_image'])) {
                            if (strlen($output_options['value'])) {
                                if (\K::$fw->app_module_path == 'dashboard/select2_json') {
                                    $output = '<img src="' . \Helpers\Urls::url_for(
                                            'main/dashboard/select2_json/preview_image',
                                            'form_type=' . \K::$fw->GET['form_type'] . '&entity_id=' . \K::$fw->GET['entity_id'] . '&field_id=' . \K::$fw->GET['field_id'] . '&parent_entity_item_id=' . \K::$fw->GET['parent_entity_item_id'] . '&file=' . urlencode(
                                                base64_encode($output_options['value'])
                                            )
                                        ) . '">';
                                } elseif (\K::$fw->app_module_path == 'dashboard/select2_ml_json') {
                                    $output = '<img src="' . \Helpers\Urls::url_for(
                                            'main/dashboard/select2_ml_json/preview_image',
                                            'form_type=' . \K::$fw->GET['form_type'] . '&entity_id=' . \K::$fw->GET['entity_id'] . '&field_id=' . \K::$fw->GET['field_id'] . '&parent_entity_item_id=' . \K::$fw->GET['parent_entity_item_id'] . '&file=' . urlencode(
                                                base64_encode($output_options['value'])
                                            )
                                        ) . '">';
                                } elseif ($options['is_email'] == 1) {
                                    $file = \Tools\Attachments::parse_filename($output_options['value']);

                                    if ($options['hide_attachments_url'] == 1) {
                                        $output = $file['name'];
                                    } else {
                                        $output = \Helpers\Urls::link_to(
                                            $file['name'],
                                            \Helpers\Urls::url_for(
                                                'main/items/info/download_attachment',
                                                'path=' . $entities_id . '&file=' . urlencode(
                                                    base64_encode($output_options['value'])
                                                ) . '&field=' . $output_options['field']['id']
                                            ),
                                            ['target' => '_blank']
                                        );
                                    }
                                } else {
                                    $output = '<img src="' . \Helpers\Urls::url_for(
                                            'main/items/info/download_attachment',
                                            '&path=' . $entities_id . '&preview=1&file=' . urlencode(
                                                base64_encode($output_options['value'])
                                            )
                                        ) . '">';
                                }
                            } else {
                                $output = '';
                            }
                        } elseif (in_array($field['type'], ['fieldtype_parent_item_id'])) {
                            $output = $value;
                        } elseif (!isset($output_options['is_export']) and in_array(
                                $field['type'],
                                ['fieldtype_attachments', 'fieldtype_input_file', 'fieldtype_image']
                            )) {
                            $output = \Models\Main\Fields_types::output($output_options);
                        } elseif (in_array($field['type'], ['fieldtype_todo_list'])) {
                            $output = trim(\Models\Main\Fields_types::output($output_options));
                        } else {
                            $output = trim(\K::fw()->clean(\Models\Main\Fields_types::output($output_options)));
                        }

                        //handle xml pattern
                        if (isset($options['is_xml'])) {
                            if (in_array($field['type'], ['fieldtype_textarea_wysiwyg'])) {
                                $output = '<![CDATA[' . $output . ']]>';
                            } elseif (in_array($field['type'], ['fieldtype_textarea'])) {
                                //TODO $f3->decode
                                $output = '<![CDATA[' . str_replace(['&lt;', '&gt;'], ['<', '>'], $output) . ']]>';
                            } else {
                                $output = htmlspecialchars($output, ENT_XML1);
                            }
                        }

                        $pattern = str_replace($matches[0][$matches_key], $output, $pattern);
                    }
                }

                //check if fields was replaced
                if (preg_replace('/\[(\d+)\]/', '', $cfg->get('pattern')) != $pattern) {
                    $html = $pattern;
                }
            } else {
                $html = $pattern;
            }
        }

        return $html;
    }

    public function output_singe_text($text, $entities_id, $item, $options = [])
    {
        $path = ($options['path'] ?? $entities_id . '-' . $item['id']);

        $output_options = ['item' => $item];
        $output_options['field']['configuration'] = '';
        $output_options['field']['entities_id'] = $entities_id;
        $output_options['path'] = $path;
        $output_options['custom_pattern'] = $text;
        $output_options['is_print'] = ($options['is_print'] ?? true);

        $output_options['is_email'] = ($options['is_email'] ?? false);
        $output_options['hide_attachments_url'] = ($options['hide_attachments_url'] ?? false);

        if (isset($options['is_xml'])) {
            $output_options['is_xml'] = $options['is_xml'];
        }

        $text = $this->output($output_options);

        //prepare url
        $text = str_replace('[url]', url_for('items/info', 'path=' . $path), $text);

        //prepare last comment
        if (strstr($text, '[comment]')) {
            $last_comment = self::get_last_comment_info($entities_id, $item['id'], $path);
            $text = str_replace('[comment]', $last_comment, $text);
        }

        return $text;
    }

    public static function get_last_comment_info($entities_id, $items_id, $path)
    {
        global $app_users_cache, $fields_access_schema_holder, $app_user;

        if (!isset($fields_access_schema_holder[$entities_id])) {
            $fields_access_schema = $fields_access_schema_holder[$entities_id] = users::get_fields_access_schema(
                $entities_id,
                $app_user['group_id']
            );
        } else {
            $fields_access_schema = $fields_access_schema_holder[$entities_id];
        }

        $comments_query_sql = "select * from app_comments where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'  order by date_added desc limit 1";
        $items_query = db_query($comments_query_sql);
        if ($item = db_fetch_array($items_query)) {
            $descripttion = $item['description'];

            //include attachments
            if (strlen($item['attachments'])) {
                $descripttion .= "<ul style='padding: 7px 0 0 0; margin: 0px;'>";
                foreach (explode(',', $item['attachments']) as $filename) {
                    $file = attachments::parse_filename($filename);
                    $descripttion .= "<li style='list-style: none; padding:0;'><img src='" . url_for_file(
                            $file['icon']
                        ) . "'>&nbsp;" . link_to(
                            $file['name'],
                            url_for(
                                'items/info',
                                'path=' . $path . '&action=download_attachment&file=' . urlencode(
                                    base64_encode($filename)
                                )
                            )
                        ) . " (" . $file['size'] . ")</li>";
                }
                $descripttion .= "</ul>";
            }

            $html_fields = '';
            $comments_fields_query = db_query(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input(
                    $item['id']
                ) . "' and f.id=ch.fields_id order by ch.id"
            );
            while ($field = db_fetch_array($comments_fields_query)) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'path' => $path
                ];

                $html_fields .= "
            <tr>
      				<th style='text-align: left;vertical-align: top; font-size: 11px;'>&bull;&nbsp;" . htmlspecialchars(
                        $field['name']
                    ) . ":&nbsp;</th>
      				<td style='font-size: 11px;'>" . htmlspecialchars(
                        strip_tags(fields_types::output($output_options))
                    ) . "</td>
      			</tr>
        ";
            }

            //include comments fileds
            if (strlen($html_fields)) {
                $descripttion .= "<table style='padding-top: 7px;'>" . $html_fields . "</table>";
            }

            if (strlen($descripttion)) {
                $html = '<div><b>' . $app_users_cache[$item['created_by']]['name'] . ' - ' . format_date_time(
                        $item['date_added']
                    ) . '</b></div>';
                $html .= $descripttion;

                return $html;
            }
        }

        return '';
    }
}