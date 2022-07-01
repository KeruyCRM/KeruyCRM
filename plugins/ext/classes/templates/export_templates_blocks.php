<?php

class export_templates_blocks
{
    public $template_info, $items_list, $templateProcessor;

    function __construct($template_info)
    {
        $this->template_info = $template_info;
    }

    function prepare_template_file($entities_id, $items_id, $item = false)
    {
        global $app_user;

        $this->items_list = [];

        if (!$item) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entities_id,
                    ''
                ) . " from app_entity_" . $entities_id . " e where e.id='" . $items_id . "'"
            );
            $item = db_fetch_array($item_query);
        }

        $this->items_list[$entities_id] = $item;

        $parent_item_id = $item['parent_item_id'];

        foreach (entities::get_parents($entities_id) as $entity_id) {
            $parent_item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id='" . $parent_item_id . "'"
            );
            $parent_item = db_fetch_array($parent_item_query);

            $this->items_list[$entity_id] = $parent_item;

            $parent_item_id = $parent_item['parent_item_id'];
        }


        if (!is_file(DIR_WS_TEMPLATES . $this->template_info['filename'])) {
            die(TEXT_FILE_NOT_FOUND);
        }

        //temp file
        $temp_filename = time(
            ) . '-' . $items_id . '-' . $app_user['id'] . '-' . $entities_id . '-' . $this->template_info['id'] . '.docx';

        //PhpOffice
        //$this->templateProcessor->setValue('{3}','test');
        \PhpOffice\PhpWord\Settings::setTempDir(DIR_FS_TMP);
        $this->templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(
            DIR_WS_TEMPLATES . $this->template_info['filename']
        );
        $this->prepare_template_blocks();
        $this->templateProcessor->saveAs(DIR_FS_TMP . $temp_filename);

        return $temp_filename;
    }

    function prepare_template_blocks()
    {
        global $app_fields_cache, $app_num2str, $app_entities_cache, $app_user;

        //dates
        $this->templateProcessor->setValue('${current_date}', format_date(time()));
        $this->templateProcessor->setValue('${current_date_time}', format_date_time(time()));

        $this->templateProcessor->setComplexValue('${comments}', $this->prepare_comments_list());

        $blocks_query = db_query(
            "select b.*, f.name, f.entities_id, f.type as field_type from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where b.parent_id=0 and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and f.entities_id=e.id order by e.sort_order, e.name, f.name"
        );
        while ($blocks = db_fetch_array($blocks_query)) {
            $block_settings = new settings($blocks['settings']);

            //for subentities
            if ($blocks['field_type'] == 'fieldtype_id' and $app_entities_cache[$blocks['entities_id']]['parent_id'] == $this->template_info['entities_id']) {
                $this->prepare_template_sub_entity_blocks($blocks['id'], $blocks['entities_id'], $block_settings);

                //skip other code;
                continue;
            }

            $item = $this->items_list[$blocks['entities_id']];
            $field = $app_fields_cache[$blocks['entities_id']][$blocks['fields_id']];
            $field_value = items::prepare_field_value_by_type($field, $item);

            //related items
            if ($blocks['field_type'] == 'fieldtype_related_records') {
                $this->prepare_template_related_records($blocks, $field, $item);

                continue;
            }

            $output_options = [
                'class' => $field['type'],
                'value' => $field_value,
                'field' => $field,
                'item' => $item,
                'is_export' => true,
                'is_print' => true,
                'path' => $blocks['entities_id']
            ];

            //print_rr($output_options);

            $cfg = new fields_types_cfg($field['configuration']);

            $output_value_html = fields_types::output($output_options);
            $output_value = strip_tags($output_value_html);


            switch ($blocks['field_type']) {
                case 'fieldtype_textarea_encrypted':
                case 'fieldtype_textarea':
                    $fontStyle = [
                        'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                            'font_name'
                        ) : 'Times New Roman'),
                        'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get(
                            'font_size'
                        ) : '12'),
                        'hint' => 1, //text break
                    ];

                    $text = new \PhpOffice\PhpWord\Element\TextRun();
                    foreach (preg_split('/\r\n|\r|\n/', $output_value) as $v) {
                        $text->addText($this->prepareValue($v), $fontStyle);
                        $text->addTextBreak();
                    }

                    while ($this->templateProcessor->getVariableCount()[$blocks['id']]) {
                        $this->templateProcessor->setComplexValue($blocks['id'], $text);
                    }

                    break;
                case 'fieldtype_textarea_wysiwyg':
                    $fontStyle = [
                        'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                            'font_name'
                        ) : 'Times New Roman'),
                        'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get(
                            'font_size'
                        ) : '12'),
                        'hint' => 1, //text break
                    ];

                    $text = new \PhpOffice\PhpWord\Element\TextRun();
                    foreach (preg_split('/<br \/>|<br>|<p>|<p \/>/', $output_value_html) as $v) {
                        $text->addText($this->prepareValue(strip_tags($v)), $fontStyle);
                        $text->addTextBreak();
                    }

                    while ($this->templateProcessor->getVariableCount()[$blocks['id']]) {
                        $this->templateProcessor->setComplexValue($blocks['id'], $text);
                    }

                    break;
                case 'fieldtype_items_by_query':
                    $this->prepare_template_html_list_value($blocks['id'], $output_value_html, $block_settings);
                    break;
                case 'fieldtype_signature':
                case 'fieldtype_barcode':
                case 'fieldtype_qrcode':
                    $this->prepare_img_from_html($blocks['id'], $output_value_html, $field);
                    break;
                case 'fieldtype_date_added':
                case 'fieldtype_date_updated':
                case 'fieldtype_dynamic_date':
                case 'fieldtype_input_datetime':
                case 'fieldtype_input_date':
                    if (strlen($block_settings->get('date_format')) and $field_value > 0) {
                        $output_value = format_date($field_value, $block_settings->get('date_format'));
                    }

                    $this->templateProcessor->setValue($blocks['id'], $output_value);
                    break;
                case 'fieldtype_attachments':
                    $this->prepare_attachments($blocks['id'], $field_value, $block_settings);
                    break;
                case 'fieldtype_image':
                case 'fieldtype_image_ajax':
                    $this->prepare_image($blocks['id'], $field_value, $block_settings);
                    break;
                case 'fieldtype_created_by':
                case 'fieldtype_entity':
                case 'fieldtype_entity_ajax':
                case 'fieldtype_entity_multilevel':
                case 'fieldtype_users':
                case 'fieldtype_users_ajax':
                case 'fieldtype_users_approve':
                case 'fieldtype_user_roles':

                    $entity_id = (in_array(
                        $blocks['field_type'],
                        [
                            'fieldtype_created_by',
                            'fieldtype_users',
                            'fieldtype_users_ajax',
                            'fieldtype_users_approve',
                            'fieldtype_user_roles'
                        ]
                    ) ? 1 : $cfg->get('entity_id'));

                    switch ($block_settings->get('display_us')) {
                        case 'inline':
                            $this->prepare_template_entity_inline_blocks(
                                $blocks['id'],
                                $entity_id,
                                $field_value,
                                $block_settings
                            );
                            break;
                        case 'list':
                            $this->prepare_template_entity_list_blocks(
                                $blocks['id'],
                                $entity_id,
                                $field_value,
                                $block_settings
                            );
                            break;
                        case 'table':
                            $this->prepare_template_entity_table_blocks(
                                $blocks['id'],
                                $entity_id,
                                $field_value,
                                $block_settings
                            );
                            break;
                        case 'table_list':
                            $this->prepare_template_entity_table_list_blocks(
                                $blocks['id'],
                                $entity_id,
                                $field_value,
                                $block_settings
                            );
                            break;

                        default:
                            $this->templateProcessor->setValue($blocks['id'], $this->prepareValue($output_value));
                            $this->prepare_template_entity_blocks($blocks['id'], $entity_id, $field_value);
                            break;
                    }

                    break;
                case 'fieldtype_access_group':
                case 'fieldtype_tags':
                case 'fieldtype_grouped_users':
                case 'fieldtype_checkboxes':
                case 'fieldtype_dropdown_multiple':
                    $this->prepare_template_choices_blocks($blocks['id'], $field, $field_value, $block_settings);
                    break;
                default:
                    $this->templateProcessor->setValue($blocks['id'], $this->prepareValue($output_value));

                    if (strlen($block_settings->get('number_in_words'))) {
                        $number_in_words = $app_num2str->convert(
                            $block_settings->get('number_in_words'),
                            $field_value,
                            (strlen($block_settings->get('number_in_words') == 2) ? false : true)
                        );
                        $this->templateProcessor->setValue(
                            $blocks['id'] . ':' . $block_settings->get('number_in_words'),
                            $number_in_words
                        );
                    }
                    break;
            }
        }
    }

    function prepareValue($string)
    {
        return htmlspecialchars($string);
    }

    function prepare_comments_list()
    {
        global $app_users_cache;

        $block = self::prepare_comments_block($this->template_info['id']);
        $block_settings = new settings($block['settings']);

        $item = $this->items_list[$this->template_info['entities_id']];
        $items_id = $item['id'];
        $entities_id = $this->template_info['entities_id'];

        $fontStyle = [
            'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                'font_name'
            ) : 'Times New Roman'),
            'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get('font_size') : '12'),
            'hint' => 1, //text break
        ];

        $text = new \PhpOffice\PhpWord\Element\TextRun();

        $comments_query_sql = "select * from app_comments where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'  order by date_added desc ";
        $comments_query = db_query($comments_query_sql);
        while ($comments = db_fetch_array($comments_query)) {
            $name = ($app_users_cache[$comments['created_by']]['name'] ?? '') . ' - ' . format_date_time(
                    $comments['date_added']
                );
            $text->addText($this->prepareValue(strip_tags($name)), $fontStyle + ['bold' => true]);
            $text->addTextBreak();

            $text->addText($this->prepareValue(strip_tags($comments['description'])), $fontStyle);

            if (strlen($comments['attachments'])) {
                foreach (explode(',', $comments['attachments']) as $filename) {
                    $file = attachments::parse_filename($filename);

                    $text->addTextBreak();
                    $text->addText($this->prepareValue($file['name']), $fontStyle + ['italic' => true]);
                }
            }

            $comments_fields_query = db_query(
                "select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input(
                    $comments['id']
                ) . "' and f.id=ch.fields_id order by ch.id"
            );
            while ($field = db_fetch_array($comments_fields_query)) {
                $output_options = [
                    'class' => $field['type'],
                    'value' => $field['fields_value'],
                    'field' => $field,
                    'is_export' => true,
                    'is_print' => true,
                    'path' => $this->template_info['entities_id']
                ];

                $fontStyle2 = $fontStyle;
                $fontStyle2['size'] = ($fontStyle['size'] - 1);

                $text->addTextBreak();
                $text->addText($this->prepareValue($field['name'] . ': '), $fontStyle2 + ['bold' => true]);
                $text->addText($this->prepareValue(strip_tags(fields_types::output($output_options))), $fontStyle2);
            }

            $text->addTextBreak();
            $text->addTextBreak();
        }

        return $text;
    }

    function prepare_template_html_list_value($block_id, $value, $block_settings)
    {
        if (is_html($value)) {
            $fontStyle = [
                'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                    'font_name'
                ) : 'Times New Roman'),
                'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get('font_size') : '12'),
                'hint' => 1, //text break
            ];

            $text = new \PhpOffice\PhpWord\Element\TextRun();

            foreach (explode('</li>', $value) as $data) {
                if (strlen($v = trim(strip_tags($data)))) {
                    $text->addText($this->prepareValue($v), $fontStyle);
                    $text->addTextBreak();
                }
            }

            $this->templateProcessor->setComplexValue($block_id, $text);
        } else {
            $this->templateProcessor->setValue($block_id, $value);
        }
    }

    function prepare_template_entity_table_list_blocks(
        $parent_block_id,
        $entity_id,
        $items_id_list,
        $parent_block_settings
    ) {
        $output = [];

        if (strlen($items_id_list)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . $items_id_list . ")"
            );
            while ($item = db_fetch_array($item_query)) {
                $output[] = $item;
            }
        }

        if (count($output)) {
            $this->prepare_template_table_list_blocks($parent_block_id, $entity_id, $output, $parent_block_settings);
        } else {
            $this->templateProcessor->setValue($parent_block_id, '');
        }
    }


    function prepare_img_from_html($block_id, $html, $field)
    {
        global $app_user;

        $cfg = new fields_types_cfg($field['configuration']);

        if (strlen($html)) {
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $src = $dom->getElementsByTagName('img')->item(0)->getAttribute('src');

            $src = base64_decode(str_replace('data:image/png;base64,', '', $src));

            $tmp_filepath = DIR_FS_TMP . $app_user['id'] . $block_id . rand(1, 1000) . '.png';

            file_put_contents($tmp_filepath, $src);

            if ($cfg->get('display_field_value') == 1 or $field['type'] == 'fieldtype_signature') {
                $table_style = new \PhpOffice\PhpWord\Style\Table;
                $table_style->setBorderSize(0);

                $wordTable = new \PhpOffice\PhpWord\Element\Table();
                $wordTable->addRow();

                $wordTable->addCell(null)->addText('${' . $block_id . ':img}');

                $wordTable->addRow();

                $cell = $wordTable->addCell(null);
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText(trim(strip_tags($html)), ['size' => '11']);

                $this->templateProcessor->setComplexBlock($block_id, $wordTable);

                $options = ['path' => $tmp_filepath];
                $this->templateProcessor->setImageValue($block_id, $options);
            } else {
                $options = ['path' => $tmp_filepath];
                $this->templateProcessor->setImageValue($block_id, $options);
            }

            unlink($tmp_filepath);
        }
    }

    function prepare_template_related_records($blocks, $field, $item)
    {
        $block_id = $blocks['id'];
        $block_settings = new settings($blocks['settings']);

        $cfg = new fields_types_cfg($field['configuration']);
        $entity_id = $cfg->get('entity_id');

        $related_records = new related_records($blocks['entities_id'], $item['id']);
        $related_records->set_related_field($field['id']);
        $related_items = $related_records->get_related_items();

        if (count($related_items)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . implode(',', $related_items) . ")",
                false
            );
            $this->prepare_template_output_items($item_query, $block_id, $entity_id, $block_settings);
        } else {
            $this->templateProcessor->setValue($block_id, '');
        }
    }

    function prepare_template_sub_entity_blocks($block_id, $entity_id, $block_settings)
    {
        global $sql_query_having;


        $listing_sql_query = '';
        $listing_sql_query_join = '';
        $listing_sql_query_from = '';

        //if tree table select only top parent items first
        if ($block_settings->get('display_us') == 'tree_table') {
            $listing_sql_query = " and e.parent_id=0 ";
        }

        if ($block_settings->get('reports_id') > 0) {
            $reports_query = db_query(
                "select id, listing_order_fields from app_reports where id='" . $block_settings->get(
                    'reports_id'
                ) . "' and entities_id='" . $entity_id . "'"
            );
            if ($reports = db_fetch_array($reports_query)) {
                $listing_sql_query = reports::add_filters_query($reports['id'], $listing_sql_query, 'e');

                //prepare having query for formula fields
                if (isset($sql_query_having[$entity_id])) {
                    $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$entity_id]);
                }

                if (strlen($reports['listing_order_fields']) > 0) {
                    $info = reports::add_order_query($reports['listing_order_fields'], $entity_id);

                    $listing_sql_query .= $info['listing_sql_query'];
                    $listing_sql_query_join .= $info['listing_sql_query_join'];
                    $listing_sql_query_from .= $info['listing_sql_query_from'];
                }
            }
        }

        $item_query = db_query(
            "select e.*  " . fieldtype_formula::prepare_query_select(
                $entity_id,
                ''
            ) . " from app_entity_" . $entity_id . " e " . $listing_sql_query_join . $listing_sql_query_from . " where e.parent_item_id='" . $this->items_list[$this->template_info['entities_id']]['id'] . "' " . $listing_sql_query,
            false
        );

        $this->prepare_template_output_items($item_query, $block_id, $entity_id, $block_settings);
    }

    function prepare_template_output_items($item_query, $block_id, $entity_id, $block_settings)
    {
        $output = [];
        $output_html = [];

        while ($item = db_fetch_array($item_query)) {
            if ($block_settings->get('display_us') == 'tree_table') {
                $item['tree_level'] = 0;
                $output[] = $item;
                $output = $this->prepare_template_output_nested_items($entity_id, $item['id'], $output, 1);
            } elseif (in_array($block_settings->get('display_us'), ['table', 'table_list'])) {
                $output[] = $item;
            } else {
                if (strlen($block_settings->get('pattern'))) {
                    $text_pattern = new fieldtype_text_pattern();
                    $value = $text_pattern->output_singe_text($block_settings->get('pattern'), $entity_id, $item);
                    $output[] = $this->prepareValue(strip_tags($value));
                    $output_html[] = $value;
                } else {
                    $value = items::get_heading_field($entity_id, $item['id'], $item);
                    $output[] = $this->prepareValue(strip_tags($value));
                    $output_html[] = $value;
                }
            }
        }

        //print_rr($output);
        //exit();

        //set emplyt value;
        if (!count($output)) {
            $this->templateProcessor->setValue($block_id, '');
        }

        switch ($block_settings->get('display_us')) {
            case 'inline':
                $separator = (strlen($block_settings->get('separator')) ? $block_settings->get('separator') : '');

                $this->templateProcessor->setValue($block_id, implode($separator, $output));
                break;
            case 'list':
                $fontStyle = [
                    'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                        'font_name'
                    ) : 'Times New Roman'),
                    'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get('font_size') : '12'),
                    'hint' => (int)$block_settings->get('empty_row'), //text break
                ];

                $text = new \PhpOffice\PhpWord\Element\TextRun();

                foreach ($output_html as $value) {
                    $value = preg_split('/<br \/>|<br>|<p>|<p \/>/', $value);
                    foreach ($value as $k => $v) {
                        $text->addText($this->prepareValue(strip_tags($v)), $fontStyle);

                        if (count($value) - 1 != $k) {
                            $text->addTextBreak();
                        }
                    }

                    if ($block_settings->get('empty_row') > 0) {
                        $text->addTextBreak($block_settings->get('empty_row'));
                    } else {
                        $text->addTextBreak();
                    }
                }

                $this->templateProcessor->setComplexValue($block_id, $text);
                break;
            case 'table':
                $this->prepare_template_table_blocks($block_id, $entity_id, $output, $block_settings);
                break;
            case 'tree_table':
                $this->prepare_template_table_blocks($block_id, $entity_id, $output, $block_settings);
                break;
            case 'table_list':
                $this->prepare_template_table_list_blocks($block_id, $entity_id, $output, $block_settings);
                break;
        }
    }

    function prepare_template_output_nested_items($entity_id, $item_id, $output, $level = 0)
    {
        $item_query = db_query(
            "select e.*  " . fieldtype_formula::prepare_query_select(
                $entity_id,
                ''
            ) . " from app_entity_" . $entity_id . " e where parent_id={$item_id} order by e.sort_order, e.id"
        );
        while ($item = db_fetch_array($item_query)) {
            $item['tree_level'] = $level;
            $output[] = $item;

            $output = $this->prepare_template_output_nested_items($entity_id, $item['id'], $output, $level + 1);
        }

        return $output;
    }

    function prepare_attachments($block_id, $attachments, $settings)
    {
        //$this->templateProcessor->setValue($block_id,'attachments');

        $images_list = [];

        //prepare images list
        if (strlen($attachments)) {
            foreach (explode(',', $attachments) as $filename) {
                $file = attachments::parse_filename($filename);

                if (!is_file($file['file_path']) or !is_image($file['file_path'])) {
                    continue;
                }

                $images_list[] = $file['file_path'];
            }
        }


        if (!count($images_list)) {
            $this->templateProcessor->setValue($block_id, '');
            return true;
        }

        $options = [];

        if (strlen($settings->get('width'))) {
            $options['width'] = $settings->get('width');
        }

        if (strlen($settings->get('height'))) {
            $options['height'] = $settings->get('height');
        }

        $options['ratio'] = true;

        $grid = (strlen($settings->get('grid')) ? $settings->get('grid') : 1);

        //preapre table
        $table_style = new \PhpOffice\PhpWord\Style\Table;
        $table_style->setUnit(\PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT);
        $table_style->setWidth(100 * 50);
        $table_style->setBorderSize(0);
        $table_style->setBorderColor('#ffffff');
        $table_style->setCellSpacing(\PhpOffice\PhpWord\Shared\Converter::pointToTwip(5));
        $table_style->setCellMargin(\PhpOffice\PhpWord\Shared\Converter::pointToTwip(0));

        $wordTable = new \PhpOffice\PhpWord\Element\Table($table_style);

        $wordTable->addRow();

        $styleCell = ['valign' => 'top'];

        $count = 1;
        $count_images = count($images_list);
        foreach ($images_list as $key => $filepath) {
            $cell = $wordTable->addCell(null);
            $cell = $cell->addTextRun($this->prepare_alignment('center'));
            $cell->addText('${' . $block_id . ':img' . $key . '}');

            if ($count / $grid == floor($count / $grid) and $count != $count_images) {
                $wordTable->addRow();
            }

            $count++;
        }

        $this->templateProcessor->setComplexBlock($block_id, $wordTable);

        //preapare images
        foreach ($images_list as $key => $filepath) {
            $options['path'] = $filepath;

            $this->templateProcessor->setImageValue($block_id . ':img' . $key, $options);
        }
    }

    function prepare_image($block_id, $filename, $settings)
    {
        $file = attachments::parse_filename($filename);

        if (!strlen($filename) or !is_file($file['file_path'])) {
            $this->templateProcessor->setValue($block_id, '');
            return true;
        }

        $options = [];

        $options['path'] = $file['file_path'];

        if (strlen($settings->get('width'))) {
            $options['width'] = $settings->get('width');
        }

        if (strlen($settings->get('height'))) {
            $options['height'] = $settings->get('height');
        }

        $options['ratio'] = true;

        $this->templateProcessor->setImageValue($block_id, $options);
    }

    function prepare_user_photo($block_id, $filename, $settings)
    {
        $file = attachments::parse_filename($filename);

        if (!strlen($filename) or !is_file(DIR_WS_USERS . $file['file_sha1'])) {
            $this->templateProcessor->setValue($block_id, '');
            return true;
        }

        $options = [];

        $options['path'] = DIR_WS_USERS . $file['file_sha1'];

        if (strlen($settings->get('width'))) {
            $options['width'] = $settings->get('width');
        }

        if (strlen($settings->get('height'))) {
            $options['height'] = $settings->get('height');
        }

        $options['ratio'] = true;

        $this->templateProcessor->setImageValue($block_id, $options);
    }

    function prepare_template_entity_table_blocks($parent_block_id, $entity_id, $items_id_list, $parent_block_settings)
    {
        $output = [];

        if (strlen($items_id_list)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . $items_id_list . ")"
            );
            while ($item = db_fetch_array($item_query)) {
                $output[] = $item;
            }
        }

        if (count($output)) {
            $this->prepare_template_table_blocks($parent_block_id, $entity_id, $output, $parent_block_settings);
        } else {
            $this->templateProcessor->setValue($parent_block_id, '');
        }
    }

    function prepare_font_style($font_settings, $table_settings, $font_size = 0)
    {
        $font_style = [];

        $font_style['name'] = $table_settings->get('font_name');
        $font_style['size'] = ($font_size > 0 ? $font_size : $table_settings->get('font_size'));
        $font_style['color'] = $table_settings->get('font_color');

        if (!is_array($font_settings)) {
            return $font_style;
        }

        if (in_array('bold', $font_settings)) {
            $font_style['bold'] = true;
        }

        if (in_array('italic', $font_settings)) {
            $font_style['italic'] = true;
        }

        if (in_array('underline', $font_settings)) {
            $font_style['underline'] = 'single';
        }


        return $font_style;
    }

    function prepare_alignment($alignment)
    {
        $settings = [];
        switch ($alignment) {
            case 'left':
                $settings['alignment'] = \PhpOffice\PhpWord\SimpleType\Jc::LEFT;
                break;
            case 'center':
                $settings['alignment'] = \PhpOffice\PhpWord\SimpleType\Jc::CENTER;
                break;
            case 'right':
                $settings['alignment'] = \PhpOffice\PhpWord\SimpleType\Jc::RIGHT;
                break;
            default:
                $settings['alignment'] = \PhpOffice\PhpWord\SimpleType\Jc::LEFT;
                break;
        }

        $settings['spaceBefore'] = 0;
        $settings['spaceAfter'] = 0;
        $settings['lineHeight'] = 1.0;

        return $settings;
    }

    function prepare_cell_settings($text_direction, $bgColor = '')
    {
        $settings = [];

        switch ($text_direction) {
            case 'BTLR':
                $settings['textDirection'] = \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR;
                break;
            case 'TBRL':
                $settings['textDirection'] = \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_TBRL;
                break;
        }

        $settings['bgColor'] = $bgColor;

        $settings['valign'] = 'center';

        return $settings;
    }

    function prepare_table_settings($settings)
    {
        $table_style = new \PhpOffice\PhpWord\Style\Table;

        $table_style->setUnit(\PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT);
        $table_style->setWidth(100 * 50);

        if ($settings->get('border') > 0) {
            $table_style->setBorderSize(\PhpOffice\PhpWord\Shared\Converter::pointToTwip($settings->get('border')));
            $table_style->setBorderColor($settings->get('border_color'));
        }

        if ($settings->get('cell_spacing') > 0) {
            $table_style->setCellSpacing(
                \PhpOffice\PhpWord\Shared\Converter::pointToTwip($settings->get('cell_spacing'))
            );
        }

        if ($settings->get('cell_margin') > 0) {
            $table_style->setCellMargin(
                \PhpOffice\PhpWord\Shared\Converter::pointToTwip($settings->get('cell_margin'))
            );
        }


        return $table_style;
    }

    function prepare_extra_rows(
        $wordTable,
        $parent_block_id,
        $block_type,
        $header_bg_color = '',
        $parent_block_settings = ''
    ) {
        global $app_fields_cache;

        $rows_query = db_query(
            "select b.* from app_ext_items_export_templates_blocks b where b.block_type='" . $block_type . "' and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
        );
        while ($rows = db_fetch_array($rows_query)) {
            $blocks_query = db_query(
                "select b.* from app_ext_items_export_templates_blocks b where b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
            );

            if (db_num_rows($blocks_query)) {
                $wordTable->addRow();

                while ($blocks = db_fetch_array($blocks_query)) {
                    $settings = new settings($blocks['settings']);

                    //get field value if field is selected
                    if ($blocks['fields_id'] > 0) {
                        $item = $this->items_list[$this->template_info['entities_id']];
                        $field = $app_fields_cache[$this->template_info['entities_id']][$blocks['fields_id']];
                        $field_value = items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $field_value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'path' => $this->template_info['entities_id']
                        ];

                        $output_value = strip_tags(fields_types::output($output_options));

                        //apply number format
                        if (strlen($settings->get('number_format')) > 0 and is_numeric($output_value)) {
                            $format = explode('/', str_replace('*', '', $settings->get('number_format')));

                            $output_value = number_format($output_value, $format[0], $format[1], $format[2]);
                        }

                        $cell_value = $settings->get('content_value_prefix') . $output_value . $settings->get(
                                'content_value_suffix'
                            );
                    } else {
                        $cell_value = $settings->get('heading');
                    }


                    $cell_settings = ['bgColor' => $header_bg_color];
                    if (strlen($settings->get('colspan'))) {
                        $cell_settings['gridSpan'] = $settings->get('colspan');
                    }

                    $cell = $wordTable->addCell(null, $cell_settings);
                    $cell = $cell->addTextRun($this->prepare_alignment($settings->get('heading_alignment')));
                    $cell->addText(
                        $this->prepareValue($cell_value),
                        $this->prepare_font_style(
                            $settings->get('heading_font_style'),
                            $parent_block_settings,
                            $settings->get('heading_font_size')
                        )
                    );
                }
            }
        }

        return $wordTable;
    }


    function prepare_template_table_blocks($parent_block_id, $entity_id, $output, $parent_block_settings)
    {
        global $app_fields_cache, $app_num2str;

        $wordTable = new \PhpOffice\PhpWord\Element\Table($this->prepare_table_settings($parent_block_settings));

        $header_bg_color = (strlen($parent_block_settings->get('header_color')) ? $parent_block_settings->get(
            'header_color'
        ) : $parent_block_settings->get('table_color'));
        $table_bg_color = $parent_block_settings->get('table_color');

        //thead
        $wordTable = $this->prepare_extra_rows(
            $wordTable,
            $parent_block_id,
            'thead',
            $header_bg_color,
            $parent_block_settings
        );

        //main table header                
        $header_height = ($parent_block_settings->get(
            'header_height'
        ) > 0 ? \PhpOffice\PhpWord\Shared\Converter::pointToTwip($parent_block_settings->get('header_height')) : null);
        $wordTable->addRow($header_height);

        //line numbering heading
        if ($parent_block_settings->get('line_numbering') == 1) {
            $cell = $wordTable->addCell(
                null,
                $this->prepare_cell_settings($parent_block_settings->get('line_numbering_direction'), $header_bg_color)
            );
            $cell = $cell->addTextRun($this->prepare_alignment('center'));
            $cell->addText(
                $parent_block_settings->get('line_numbering_heading'),
                $this->prepare_font_style([], $parent_block_settings)
            );
        }

        $blocks_query = db_query(
            "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
            false
        );

        //set empy if no columns setup
        if (db_num_rows($blocks_query) == 0) {
            $this->templateProcessor->setValue($parent_block_id, '');
        }

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            $cell_name = (strlen($settings->get('heading')) ? $settings->get('heading') : fields_types::get_option(
                $blocks['field_type'],
                'name',
                $blocks['name']
            ));

            $cell = $wordTable->addCell(
                null,
                $this->prepare_cell_settings($settings->get('heading_text_direction'), $header_bg_color)
            );
            $cell = $cell->addTextRun($this->prepare_alignment($settings->get('heading_alignment')));
            $cell->addText(
                $this->prepareValue($cell_name),
                $this->prepare_font_style(
                    $settings->get('heading_font_style'),
                    $parent_block_settings,
                    $settings->get('heading_font_size')
                )
            );
        }

        //column numbering
        if ($parent_block_settings->get('column_numbering') == 1) {
            $wordTable->addRow();
            $count = 1;

            //line numbering count
            if ($parent_block_settings->get('line_numbering') == 1) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText($count, $this->prepare_font_style([], $parent_block_settings));
                $count++;
            }

            $blocks_query = db_query(
                "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
                false
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText($count, $this->prepare_font_style([], $parent_block_settings));
                $count++;
            }
        }

        //table body 
        $totals = [];
        $has_totals = false;
        $users_photos_list = [];
        $images_list = [];

        foreach ($output as $item_count => $item) {
            $wordTable->addRow();

            //line numbering count
            if ($parent_block_settings->get('line_numbering') == 1) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText(($item_count + 1), $this->prepare_font_style([], $parent_block_settings));
            }

            $blocks_query = db_query(
                "select b.*,f.is_heading, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
                false
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $field = $app_fields_cache[$blocks['entities_id']][$blocks['fields_id']];
                $field_value = items::prepare_field_value_by_type($field, $item);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field_value,
                    'field' => $field,
                    'item' => $item,
                    'is_export' => true,
                    'path' => $blocks['entities_id']
                ];

                $output_value = strip_tags(fields_types::output($output_options));

                $settings = new settings($blocks['settings']);

                //apply number format
                if (strlen($settings->get('number_format')) > 0 and is_numeric($output_value)) {
                    $format = explode('/', str_replace('*', '', $settings->get('number_format')));

                    $output_value = number_format($output_value, $format[0], $format[1], $format[2]);
                }

                //add sufix/prefix
                $output_value = $settings->get('content_value_prefix') . $output_value . $settings->get(
                        'content_value_suffix'
                    );

                //preapre some fields types
                switch ($blocks['field_type']) {
                    case 'fieldtype_date_added':
                    case 'fieldtype_date_updated':
                    case 'fieldtype_dynamic_date':
                    case 'fieldtype_input_datetime':
                    case 'fieldtype_input_date':
                        if (strlen($settings->get('date_format'))) {
                            $output_value = format_date($field_value, $settings->get('date_format'));
                        }
                        break;
                    case 'fieldtype_user_photo':
                        $output_value = '';
                        if (strlen($field_value)) {
                            $file = attachments::parse_filename($field_value);

                            if (is_file(DIR_WS_USERS . $file['file_sha1']) and is_image(
                                    DIR_WS_USERS . $file['file_sha1']
                                )) {
                                $options = [
                                    'path' => DIR_WS_USERS . $file['file_sha1'],
                                    'width' => $settings->get('width'),
                                    'height' => $settings->get('height'),
                                    'ratio' => true,
                                ];

                                $img_id = $blocks['id'] . ':' . $item['id'] . ':' . $blocks['fields_id'];

                                $output_value = '${' . $img_id . '}';

                                $images_list[$img_id] = $options;
                            }
                        }
                        break;
                    case 'fieldtype_image':
                    case 'fieldtype_image_ajax':
                        $output_value = '';
                        if (strlen($field_value)) {
                            $file = attachments::parse_filename($field_value);

                            if (is_file($file['file_path']) and is_image($file['file_path'])) {
                                $options = [
                                    'path' => $file['file_path'],
                                    'width' => $settings->get('width'),
                                    'height' => $settings->get('height'),
                                    'ratio' => true,
                                ];

                                $img_id = $blocks['id'] . ':' . $item['id'] . ':' . $blocks['fields_id'];

                                $output_value = '${' . $img_id . '}';

                                $images_list[$img_id] = $options;
                            }
                        }
                        break;
                }

                if (isset($item['tree_level']) and $item['tree_level'] > 0 and $blocks['is_heading']) {
                    $output_value = str_repeat(' - ', $item['tree_level']) . $output_value;
                }

                $cell_width = ($settings->get('cell_width') > 0 ? \PhpOffice\PhpWord\Shared\Converter::pointToTwip(
                    $settings->get('cell_width')
                ) : null);

                $cell = $wordTable->addCell($cell_width, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment($settings->get('content_alignment')));
                $cell->addText(
                    $this->prepareValue($output_value),
                    $this->prepare_font_style(
                        $settings->get('content_font_style'),
                        $parent_block_settings,
                        $settings->get('content_font_size')
                    )
                );

                //calculate totals                                
                if ($settings->get('calculate_totals') == 1 and is_numeric($field_value)) {
                    if (!isset($totals[$blocks['id']])) {
                        $totals[$blocks['id']] = 0;
                    }

                    $totals[$blocks['id']] += $field_value;

                    $has_totals = true;
                }
            }
        }

        //number of rows
        $number_of_rows = count($output);
        $number_of_rows_text = (isset($app_num2str->data[TEXT_APP_LANGUAGE_SHORT_CODE]) ? $app_num2str->convert(
            TEXT_APP_LANGUAGE_SHORT_CODE,
            $number_of_rows,
            false
        ) : $app_num2str->convert('en', $number_of_rows, false));
        $this->templateProcessor->setValue('${' . $parent_block_id . ':count}', $number_of_rows);
        $this->templateProcessor->setValue('${' . $parent_block_id . ':count_text}', $number_of_rows_text);

        //totals
        if ($has_totals) {
            $wordTable->addRow();

            if ($parent_block_settings->get('line_numbering') == 1) {
                $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color))->addText('');
            }

            $blocks_query = db_query(
                "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
                false
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $settings = new settings($blocks['settings']);

                if (isset($totals[$blocks['id']])) {
                    $output_value = $totals[$blocks['id']];

                    //apply number format
                    if (strlen($settings->get('number_format')) > 0 and is_numeric($output_value)) {
                        $format = explode('/', str_replace('*', '', $settings->get('number_format')));

                        $output_value = number_format($output_value, $format[0], $format[1], $format[2]);
                    }

                    $output_value = $settings->get('content_value_prefix') . $output_value . $settings->get(
                            'content_value_suffix'
                        );
                } else {
                    $output_value = '';
                }

                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment($settings->get('content_alignment')));
                $content_font_style = $this->prepare_font_style(
                    $settings->get('content_font_style'),
                    $parent_block_settings,
                    $settings->get('content_font_size')
                );
                $content_font_style['bold'] = true;
                $cell->addText($output_value, $content_font_style);
            }
        }

        //tfoot
        $wordTable = $this->prepare_extra_rows(
            $wordTable,
            $parent_block_id,
            'tfoot',
            $table_bg_color,
            $parent_block_settings
        );

        $this->templateProcessor->setComplexBlock($parent_block_id, $wordTable);

        //images in table
        foreach ($images_list as $image_id => $options) {
            $this->templateProcessor->setImageValue($image_id, $options);
        }
    }


    //table list  blocks
    function prepare_template_table_list_blocks($parent_block_id, $entity_id, $output, $parent_block_settings)
    {
        global $app_fields_cache, $app_num2str;


        $wordTable = new \PhpOffice\PhpWord\Element\Table($this->prepare_table_settings($parent_block_settings));

        $header_bg_color = (strlen($parent_block_settings->get('header_color')) ? $parent_block_settings->get(
            'header_color'
        ) : $parent_block_settings->get('table_color'));
        $table_bg_color = $parent_block_settings->get('table_color');

        //main table header
        $header_height = ($parent_block_settings->get(
            'header_height'
        ) > 0 ? \PhpOffice\PhpWord\Shared\Converter::pointToTwip($parent_block_settings->get('header_height')) : null);
        $wordTable->addRow($header_height);

        //line numbering heading
        if ($parent_block_settings->get('line_numbering') == 1) {
            $cell = $wordTable->addCell(
                null,
                $this->prepare_cell_settings($parent_block_settings->get('line_numbering_direction'), $header_bg_color)
            );
            $cell = $cell->addTextRun($this->prepare_alignment('center'));
            $cell->addText($parent_block_settings->get('line_numbering_heading'));
        }

        $blocks_query = db_query(
            "select b.* from app_ext_items_export_templates_blocks b where  block_type='table_list_cell' and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id",
            false
        );

        //set empy if no columns setup
        if (db_num_rows($blocks_query) == 0) {
            $this->templateProcessor->setValue($parent_block_id, '');
        }

        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new settings($blocks['settings']);

            $cell_name = (strlen($settings->get('heading')) ? $settings->get('heading') : '');

            $cell = $wordTable->addCell(
                null,
                $this->prepare_cell_settings($settings->get('heading_text_direction'), $header_bg_color)
            );
            $cell = $cell->addTextRun($this->prepare_alignment($settings->get('heading_alignment')));
            $cell->addText(
                $this->prepareValue($cell_name),
                $this->prepare_font_style(
                    $settings->get('heading_font_style'),
                    $parent_block_settings,
                    $settings->get('heading_font_size')
                )
            );
        }

        //column numbering
        if ($parent_block_settings->get('column_numbering') == 1) {
            $wordTable->addRow();
            $count = 1;

            //line numbering count
            if ($parent_block_settings->get('line_numbering') == 1) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText($count, $this->prepare_font_style([], $parent_block_settings));
                $count++;
            }

            $blocks_query = db_query(
                "select b.* from app_ext_items_export_templates_blocks b where  block_type='table_list_cell' and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id",
                false
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText($count, $this->prepare_font_style([], $parent_block_settings));
                $count++;
            }
        }

        //table body         
        foreach ($output as $item_count => $item) {
            $wordTable->addRow();

            //line numbering count
            if ($parent_block_settings->get('line_numbering') == 1) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText(($item_count + 1), $this->prepare_font_style([], $parent_block_settings));
            }

            $output_blocks = [];

            $blocks_query = db_query(
                "select b.* from app_ext_items_export_templates_blocks b where  block_type='table_list_cell' and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id",
                false
            );
            while ($blocks = db_fetch_array($blocks_query)) {
                $settings = new settings($blocks['settings']);

                $html = '';

                if (is_array($settings->get('fields'))) {
                    $html = "<table>";

                    foreach ($settings->get('fields') as $field_id) {
                        $field = $app_fields_cache[$entity_id][$field_id];
                        $field_value = items::prepare_field_value_by_type($field, $item);

                        $output_options = [
                            'class' => $field['type'],
                            'value' => $field_value,
                            'field' => $field,
                            'item' => $item,
                            'is_export' => true,
                            'is_print' => true,
                            'path' => $entity_id
                        ];


                        //print_rr($output_options);
                        //exit();

                        $output_value = str_replace(['&', '&amp;'],
                            ' ',
                            strip_tags(fields_types::output($output_options)));


                        $html .= '<tr>';

                        $css = $this->prepare_font_style_css($settings, $parent_block_settings);

                        if ($settings->get('display_field_names') == 1) {
                            if ($entity_id == 1 and strstr($field['name'], 'user_')) {
                                $field['name'] = '';
                            }

                            $html .= '<td ' . $css . ($settings->get(
                                    'field_name_column_width'
                                ) > 0 ? ' width="' . $settings->get(
                                        'field_name_column_width'
                                    ) . '"' : '') . '><b>' . strip_tags(
                                    fields_types::get_option($field['type'], 'name', $field['name'])
                                ) . '</b></td>';
                        }

                        //$css1 = str_replace('style="','style="width: 50%;',$css);                                                

                        $html .= '<td ' . $css . ($settings->get(
                                'value_column_width'
                            ) > 0 ? ' width="' . $settings->get('value_column_width') . '"' : '') . ' >' . strip_tags(
                                $output_value
                            ) . '</td>';

                        $html .= '</tr>';
                    }

                    $html .= '</table>';
                }

                //echo $html;
                //exit();

                $cell_width = ($settings->get('cell_width') > 0 ? \PhpOffice\PhpWord\Shared\Converter::pointToTwip(
                    $settings->get('cell_width')
                ) : null);

                $cell_settings = $this->prepare_cell_settings($settings->get('content_font_style'), $table_bg_color);
                $cell_settings['valign'] = 'top';

                $cell = $wordTable->addCell($cell_width, $cell_settings);
                \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $html);
            }
        }

        //number of rows
        $number_of_rows = count($output);
        $number_of_rows_text = (isset($app_num2str->data[TEXT_APP_LANGUAGE_SHORT_CODE]) ? $app_num2str->convert(
            TEXT_APP_LANGUAGE_SHORT_CODE,
            $number_of_rows,
            false
        ) : $app_num2str->convert('en', $number_of_rows, false));
        $this->templateProcessor->setValue('${' . $parent_block_id . ':count}', $number_of_rows);
        $this->templateProcessor->setValue('${' . $parent_block_id . ':count_text}', $number_of_rows_text);

        $this->templateProcessor->setComplexBlock($parent_block_id, $wordTable);

        //echo $html;
        //exit();
    }


    function prepare_font_style_css($settings, $table_settings)
    {
        $font_style = [];

        $font_style['font-family'] = $table_settings->get('font_name');
        $font_style['font-size'] = (strlen($settings->get('content_font_size')) ? $settings->get(
                'content_font_size'
            ) : $table_settings->get('font_size')) . 'pt';
        $font_style['color'] = $table_settings->get('font_color');

        $font_settings = (is_array($settings->get('content_font_style')) ? $settings->get('content_font_style') : []);

        if (in_array('bold', $font_settings)) {
            $font_style['font-weight'] = 'bold';
        }

        if (in_array('italic', $font_settings)) {
            $font_style['font-style'] = 'italic';
        }

        if (in_array('underline', $font_settings)) {
            $font_style['text-decoration'] = 'underline';
        }

        if (strlen($settings->get('content_alignment')) and $settings->get('content_alignment') != 'left') {
            $font_style['text-align'] = $settings->get('content_alignment');
        }

        if (strlen($settings->get('border'))) {
            if ($settings->get('border') == 'row') {
                $font_style['border-bottom'] = '1px solid black';
            } else {
                $font_style['border'] = '1px solid black';
            }

            if (strlen($settings->get('border_color'))) {
                $font_style['border-color'] = $settings->get('border_color');
            }
        }

        if ($settings->get('cell_margin') > 0) {
            $font_style['margin'] = $settings->get('cell_margin') . 'px';
        }


        $css = 'style="';
        foreach ($font_style as $k => $v) {
            $css .= $k . ': ' . $v . ';';
        }
        $css .= '"';

        return $css;
    }

    function prepare_template_choices_blocks($blocks_id, $field, $field_value, $block_settings)
    {
        global $app_global_choices_cache, $app_choices_cache;

        $output = [];

        $cfg = new fields_types_cfg($field['configuration']);

        if ($cfg->get('use_global_list') > 0) {
            foreach (explode(',', $field_value) as $value_id) {
                if (isset($app_global_choices_cache[$value_id])) {
                    $output[] = $app_global_choices_cache[$value_id]['name'];
                }
            }
        } else {
            foreach (explode(',', $field_value) as $value_id) {
                if (isset($app_choices_cache[$value_id])) {
                    $output[] = $app_choices_cache[$value_id]['name'];
                }
            }
        }

        if (!count($output)) {
            $this->templateProcessor->setValue($blocks_id, '');
            return true;
        }

        if ($block_settings->get('display_us') == 'list') {
            $fontStyle = [
                'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                    'font_name'
                ) : 'Times New Roman'),
                'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get('font_size') : '12'),
                'hint' => (int)$block_settings->get('empty_row'), //text break
            ];

            $text = new \PhpOffice\PhpWord\Element\TextRun();

            foreach ($output as $value) {
                $text->addText($value, $fontStyle);

                if ($block_settings->get('empty_row') > 0) {
                    $text->addTextBreak($block_settings->get('empty_row'));
                } else {
                    $text->addTextBreak();
                }
            }

            $this->templateProcessor->setComplexValue($blocks_id, $text);
        } else {
            $separator = (strlen($block_settings->get('separator')) ? $block_settings->get('separator') : '');

            $this->templateProcessor->setValue($blocks_id, implode($separator, $output));
        }
    }

    function prepare_template_entity_list_blocks($parent_block_id, $entity_id, $items_id_list, $block_settings)
    {
        $output = [];

        if (strlen($items_id_list)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . $items_id_list . ")"
            );
            while ($item = db_fetch_array($item_query)) {
                if (strlen($block_settings->get('pattern'))) {
                    $text_pattern = new fieldtype_text_pattern();
                    $output[] = $this->prepareValue(
                        strip_tags($text_pattern->output_singe_text($block_settings->get('pattern'), $entity_id, $item))
                    );
                } else {
                    $output[] = $this->prepareValue(
                        strip_tags(items::get_heading_field($entity_id, $item['id'], $item))
                    );
                }
            }
        }

        if (count($output)) {
            $fontStyle = [
                'name' => (strlen($block_settings->get('font_name')) ? $block_settings->get(
                    'font_name'
                ) : 'Times New Roman'),
                'size' => (strlen($block_settings->get('font_size')) ? $block_settings->get('font_size') : '12'),
                'hint' => (int)$block_settings->get('empty_row'), //text break
            ];

            $text = new \PhpOffice\PhpWord\Element\TextRun();

            foreach ($output as $value) {
                $text->addText($value, $fontStyle);

                if ($block_settings->get('empty_row') > 0) {
                    $text->addTextBreak($block_settings->get('empty_row'));
                } else {
                    $text->addTextBreak();
                }
            }

            $this->templateProcessor->setComplexValue($parent_block_id, $text);
        } else {
            $this->templateProcessor->setValue($parent_block_id, '');
        }
    }

    function prepare_template_entity_inline_blocks($parent_block_id, $entity_id, $items_id_list, $block_settings)
    {
        $output = [];

        if (strlen($items_id_list)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id in (" . $items_id_list . ")"
            );
            while ($item = db_fetch_array($item_query)) {
                if (strlen($block_settings->get('pattern'))) {
                    $text_pattern = new fieldtype_text_pattern();
                    $output[] = $this->prepareValue(
                        strip_tags($text_pattern->output_singe_text($block_settings->get('pattern'), $entity_id, $item))
                    );
                } else {
                    $output[] = $this->prepareValue(
                        strip_tags(items::get_heading_field($entity_id, $item['id'], $item))
                    );
                }
            }
        }

        $separator = (strlen($block_settings->get('separator')) ? $block_settings->get('separator') : '');

        $this->templateProcessor->setValue($parent_block_id, implode($separator, $output));
    }

    function prepare_template_entity_blocks($parent_block_id, $entity_id, $item_id)
    {
        global $app_fields_cache;

        $item = false;

        if (strlen($item_id)) {
            $item_query = db_query(
                "select e.*  " . fieldtype_formula::prepare_query_select(
                    $entity_id,
                    ''
                ) . " from app_entity_" . $entity_id . " e where e.id='" . $item_id . "'"
            );
            $item = db_fetch_array($item_query);
        }


        $blocks_query = db_query(
            "select b.*, f.name, f.entities_id, f.type as field_type from app_ext_items_export_templates_blocks b, app_fields f, app_entities e where b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id=" . $parent_block_id . " and f.entities_id=e.id order by e.sort_order, e.name, f.name"
        );
        while ($blocks = db_fetch_array($blocks_query)) {
            if ($item) {
                $field = $app_fields_cache[$blocks['entities_id']][$blocks['fields_id']];
                $field_value = items::prepare_field_value_by_type($field, $item);

                $output_options = [
                    'class' => $field['type'],
                    'value' => $field_value,
                    'field' => $field,
                    'item' => $item,
                    'is_export' => true,
                    'is_print' => true,
                    'path' => $blocks['entities_id']
                ];

                $output_value = $this->prepareValue(strip_tags(fields_types::output($output_options)));

                $settings = new settings($blocks['settings']);

                switch ($blocks['field_type']) {
                    case 'fieldtype_date_added':
                    case 'fieldtype_date_updated':
                    case 'fieldtype_dynamic_date':
                    case 'fieldtype_input_datetime':
                    case 'fieldtype_input_date':
                        if (strlen($settings->get('date_format'))) {
                            $output_value = format_date($field_value, $settings->get('date_format'));
                        }

                        $this->templateProcessor->setValue($blocks['id'], $output_value);
                        break;
                    case 'fieldtype_attachments':
                        $this->prepare_attachments($blocks['id'], $field_value, $settings);
                        break;
                    case 'fieldtype_image':
                    case 'fieldtype_image_ajax':
                        $this->prepare_image($blocks['id'], $field_value, $settings);
                        break;
                    case 'fieldtype_user_photo':
                        $this->prepare_user_photo($blocks['id'], $field_value, $settings);
                        break;
                    default:
                        $this->templateProcessor->setValue($blocks['id'], $output_value);
                        break;
                }
            } else {
                $this->templateProcessor->setValue($blocks['id'], '');
            }
        }
    }

    function download($temp_filename)
    {
        global $app_entities_cache;

        if (!is_file(DIR_FS_TMP . $temp_filename)) {
            die(TEXT_FILE_NOT_FOUND);
        }

        $filename = (strlen(
            $_POST['filename']
        ) ? $_POST['filename'] : $app_entities_cache[$this->template_info['entities_id']]['name']);

        // Redirect output to a client’s web browser (docx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header(
            'Content-Disposition: attachment;filename="' . addslashes(
                app_remove_special_characters($filename)
            ) . '.docx"'
        );
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        readfile(DIR_FS_TMP . $temp_filename);

        unlink(DIR_FS_TMP . $temp_filename);

        exit();
    }

    function download_pdf($temp_filename)
    {
        global $app_entities_cache;

        if (!is_file(DIR_FS_TMP . $temp_filename)) {
            die(TEXT_FILE_NOT_FOUND);
        }

        $temp_pdf_filename = DIR_FS_TMP . $temp_filename . '.pdf';

        $filename = (strlen(
            $_POST['filename']
        ) ? $_POST['filename'] : $app_entities_cache[$this->template_info['entities_id']]['name']);

        //prepare PDF
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(CFG_PATH_TO_DOMPDF);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        //Load temp file
        $phpWord = \PhpOffice\PhpWord\IOFactory::load(DIR_FS_TMP . $temp_filename);

        //Save it
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $xmlWriter->save($temp_pdf_filename);

        // Redirect output to a client’s web browser (docx)
        header('Content-Type: application/pdf');
        header(
            'Content-Disposition: attachment;filename="' . addslashes(
                app_remove_special_characters($filename)
            ) . '.pdf"'
        );
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        readfile($temp_pdf_filename);

        unlink($temp_pdf_filename);
        unlink(DIR_FS_TMP . $temp_filename);

        exit();
    }

    function print_pdf($temp_filename)
    {
        global $app_entities_cache;

        if (!is_file(DIR_FS_TMP . $temp_filename)) {
            die(TEXT_FILE_NOT_FOUND);
        }

        $temp_pdf_filename = DIR_FS_TMP . $temp_filename . '.pdf';

        $filename = (strlen(
            $_POST['filename']
        ) ? $_POST['filename'] : $app_entities_cache[$this->template_info['entities_id']]['name']);

        /*
        $phpWord = \PhpOffice\PhpWord\IOFactory::load(DIR_FS_TMP . $temp_filename);
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
        //$htmlWriter->save('test1doc.html');
        echo $htmlWriter->getContent();
        exit();         
         */

        //prepare PDF 
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(CFG_PATH_TO_DOMPDF);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        //Load temp file
        $phpWord = \PhpOffice\PhpWord\IOFactory::load(DIR_FS_TMP . $temp_filename);

        //Save it
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $xmlWriter->save($temp_pdf_filename);

        // Redirect output to a client’s web browser (docx)
        header('Content-Type: application/pdf');

        readfile($temp_pdf_filename);

        unlink($temp_pdf_filename);
        unlink(DIR_FS_TMP . $temp_filename);

        exit();
    }

    function print_html($temp_filename)
    {
        global $app_entities_cache;

        if (!is_file(DIR_FS_TMP . $temp_filename)) {
            die(TEXT_FILE_NOT_FOUND);
        }


        $phpWord = \PhpOffice\PhpWord\IOFactory::load(DIR_FS_TMP . $temp_filename);

        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
        //$htmlWriter->save('test1doc.html');
        echo $htmlWriter->getContent();

        unlink(DIR_FS_TMP . $temp_filename);

        echo '<script>window.print();</script>';

        exit();
    }

    function dowload_archive($files, $zip_filename)
    {
        $zip = new ZipArchive();
        $zip_filename = app_remove_special_characters($zip_filename) . ".zip";
        $zip_filepath = DIR_FS_TMP . time() . '-' . $zip_filename;

        //open zip archive
        $zip->open($zip_filepath, ZipArchive::CREATE);

        //add files to archive
        foreach ($files as $filename) {
            $zip->addFile(DIR_FS_TMP . $filename['filename'], $filename['name']);
        }

        $zip->close();

        //check if zip archive created
        if (!is_file($zip_filepath)) {
            exit("Error: cannot create zip archive in " . $zip_filepath);
        }

        //download file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $zip_filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zip_filepath));

        flush();

        readfile($zip_filepath);

        //delete temp zip archive file
        @unlink($zip_filepath);

        //delete temp files
        foreach ($files as $filename) {
            unlink(DIR_FS_TMP . $filename['filename']);
        }
    }

    static function get_fields_choices($fields_id, $template_id, $template_entity_id)
    {
        global $app_entities_cache, $app_fields_cache;

        $choices = [];

        $entities_list = [];
        $entities_list[] = $template_entity_id;

        //include parent entities
        foreach (entities::get_parents($template_entity_id) as $id) {
            $entities_list[] = $id;
        }

        //entities fields list
        foreach ($entities_list as $entity_id) {
            $fields_query = fields::get_query(
                $entity_id,
                " and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and f.id not in (select fields_id from app_ext_items_export_templates_blocks where block_type = 'parent' and templates_id=" . $template_id . " " . ($fields_id > 0 ? " and fields_id!=" . $fields_id : "") . ")"
            );

            while ($fields = db_fetch_array($fields_query)) {
                $choices[$app_entities_cache[$entity_id]['name']][$fields['id']] = fields_types::get_option(
                    $fields['type'],
                    'name',
                    $fields['name']
                );
            }
        }

        //include subentities
        $entities_query = db_query("select id, name from app_entities where parent_id='" . $template_entity_id . "'");
        while ($entities = db_fetch_array($entities_query)) {
            $field_query = db_query(
                "select id from app_fields where type='fieldtype_id' and entities_id=" . $entities['id']
            );
            $field = db_fetch_array($field_query);
            $choices[$entities['name']][$field['id']] = TEXT_LIST_RELATED_ITEMS;
        }


        return $choices;
    }

    static function delele_blocks_by_template_id($template_id)
    {
        $block_query = db_query(
            "select id from app_ext_items_export_templates_blocks where templates_id='" . $template_id . "' and parent_id=0"
        );
        while ($block = db_fetch_array($block_query)) {
            self::delele_block($block['id']);
        }
    }

    static function delele_block($block_id)
    {
        db_query("delete from app_ext_items_export_templates_blocks where id=" . $block_id);

        $block_query = db_query(
            "select id from app_ext_items_export_templates_blocks where parent_id='" . $block_id . "'"
        );
        while ($block = db_fetch_array($block_query)) {
            self::delele_block($block['id']);
        }
    }

    static function prepare_comments_block($templates_id)
    {
        $block_query = db_query(
            "select * from app_ext_items_export_templates_blocks where templates_id='" . $templates_id . "' and fields_id=0"
        );
        if (!$block = db_fetch_array($block_query)) {
            $settings = [
                'font_name' => 'Times New Roman',
                'font_size' => 12
            ];

            $sql_data = [
                'templates_id' => $templates_id,
                'block_type' => 'parent',
                'parent_id' => 0,
                'fields_id' => 0,
                'settings' => json_encode($settings),
                'sort_order' => 0,
            ];

            db_perform('app_ext_items_export_templates_blocks', $sql_data);
            $block_id = db_insert_id();

            $block_query = db_query("select * from app_ext_items_export_templates_blocks where id='" . $block_id . "'");
            $block = db_fetch_array($block_query);
        }

        return $block;
    }
}