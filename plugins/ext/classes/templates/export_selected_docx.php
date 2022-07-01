<?php

class export_selected_docx
{
    public $template_info, $items_list, $templateProcessor;

    function __construct($template_info, $selected_items)
    {
        $this->template_info = $template_info;
        $this->selected_items = $selected_items;
        $this->entities_id = $this->template_info['entities_id'];
    }

    function prepare_template_file()
    {
        global $app_user;

        if (!is_file(DIR_WS_TEMPLATES . $this->template_info['filename'])) {
            die(TEXT_FILE_NOT_FOUND);
        }

        //temp file
        $temp_filename = time() . '-' . $app_user['id'] . '-' . $this->template_info['id'] . '.docx';

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

        return $settings;
    }

    function prepare_template_blocks()
    {
        global $app_num2str, $app_user;
        //dates
        $this->templateProcessor->setValue('${current_date}', format_date(time()));
        $this->templateProcessor->setValue('${current_date_time}', format_date_time(time()));

        $this->templateProcessor->setValue('${current_user_name}', $app_user['name']);

        $this->prepare_template_table_blocks();
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

    function prepare_template_table_blocks()
    {
        global $app_fields_cache, $app_num2str;

        $parent_block_id = 0;
        $parent_block_settings = new settings($this->template_info['settings']);

        $wordTable = new \PhpOffice\PhpWord\Element\Table($this->prepare_table_settings($parent_block_settings));

        $header_bg_color = (strlen($parent_block_settings->get('header_color')) ? $parent_block_settings->get(
            'header_color'
        ) : $parent_block_settings->get('table_color'));
        $table_bg_color = $parent_block_settings->get('table_color');

        //thead
        $wordTable = $this->prepare_extra_rows($wordTable, 0, 'thead', $header_bg_color, $parent_block_settings);

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
            "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id"
        );

        //set empy if no columns setup
        if (db_num_rows($blocks_query) == 0) {
            $this->templateProcessor->setValue($parent_block_id, '');
        }

        $fields_in_listing = [];

        while ($blocks = db_fetch_array($blocks_query)) {
            $fields_in_listing[] = $blocks['fields_id'];

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
                $cell_name,
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
                "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
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
        $item_count = 0;

        $selected_items = implode(',', $this->selected_items);

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $this->entities_id,
            '',
            false,
            ['fields_in_listing' => implode(',', $fields_in_listing)]
        );

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $this->entities_id . " e where e.id in (" . $selected_items . ") order by field(id," . $selected_items . ")";
        $items_query = db_query($listing_sql, false);

        //exit();

        foreach ($items_query as $item_count => $item) {
            $item_count++;

            $wordTable->addRow();

            //line numbering count
            if ($parent_block_settings->get('line_numbering') == 1) {
                $cell = $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color));
                $cell = $cell->addTextRun($this->prepare_alignment('center'));
                $cell->addText(($item_count), $this->prepare_font_style([], $parent_block_settings));
            }

            $blocks_query = db_query(
                "select b.*,f.is_heading, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
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
                    case 'fieldtype_image_ajax':
                    case 'fieldtype_image':
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
                    $output_value,
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
        $number_of_rows = $item_count;
        $number_of_rows_text = (isset($app_num2str->data[TEXT_APP_LANGUAGE_SHORT_CODE]) ? $app_num2str->convert(
            TEXT_APP_LANGUAGE_SHORT_CODE,
            $number_of_rows,
            false
        ) : $app_num2str->convert('en', $number_of_rows, false));
        $this->templateProcessor->setValue('${count}', $number_of_rows);
        $this->templateProcessor->setValue('${count_text}', $number_of_rows_text);

        //totals
        if ($has_totals) {
            $wordTable->addRow();

            if ($parent_block_settings->get('line_numbering') == 1) {
                $wordTable->addCell(null, $this->prepare_cell_settings('', $table_bg_color))->addText('');
            }

            $blocks_query = db_query(
                "select b.*, f.name, f.entities_id, f.type as field_type,f.configuration as field_configuration from app_ext_export_selected_blocks b, app_fields f, app_entities e where  block_type='body_cell' and b.fields_id=f.id and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " and f.entities_id=e.id order by b.sort_order, b.id",
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
        $wordTable = $this->prepare_extra_rows($wordTable, 0, 'tfoot', $table_bg_color, $parent_block_settings);

        $this->templateProcessor->setComplexBlock('${table}', $wordTable);

        //images in table
        foreach ($images_list as $image_id => $options) {
            $this->templateProcessor->setImageValue($image_id, $options);
        }
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
            "select b.* from app_ext_export_selected_blocks b where b.block_type='" . $block_type . "' and b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $parent_block_id . " order by b.sort_order, b.id"
        );
        while ($rows = db_fetch_array($rows_query)) {
            $blocks_query = db_query(
                "select b.* from app_ext_export_selected_blocks b where b.templates_id = " . $this->template_info['id'] . " and b.parent_id = " . $rows['id'] . " order by b.sort_order, b.id"
            );

            if (db_num_rows($blocks_query)) {
                $wordTable->addRow();

                while ($blocks = db_fetch_array($blocks_query)) {
                    $settings = new settings($blocks['settings']);

                    $cell_value = $settings->get('heading');

                    $cell_settings = ['bgColor' => $header_bg_color];
                    if (strlen($settings->get('colspan'))) {
                        $cell_settings['gridSpan'] = $settings->get('colspan');
                    }

                    $cell = $wordTable->addCell(null, $cell_settings);
                    $cell = $cell->addTextRun($this->prepare_alignment($settings->get('heading_alignment')));
                    $cell->addText(
                        $cell_value,
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

    function download($temp_filename)
    {
        global $app_entities_cache;

        if (!is_file(DIR_FS_TMP . $temp_filename)) {
            die(TEXT_FILE_NOT_FOUND);
        }

        $filename = db_input_protect($_POST['filename']);

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

        $filename = db_input_protect($_POST['filename']);

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
}
