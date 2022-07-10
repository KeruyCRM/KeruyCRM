<?php

class timeline_reports
{
    public static function get_css($timeline_reports)
    {
        if ((int)$timeline_reports['use_background'] == 0) {
            return '';
        }

        $field_info_query = db_query("select * from app_fields where id='" . $timeline_reports['use_background'] . "'");
        if (!$field_info = db_fetch_array($field_info_query)) {
            return '';
        }

        $html = '
				<style>';

        $cfg = new fields_types_cfg($field_info['configuration']);
        if ($cfg->get('use_global_list') > 0) {
            $choices_query = db_query(
                "select * from app_global_lists_choices where lists_id = '" . db_input(
                    $cfg->get('use_global_list')
                ) . "' and length(bg_color)>0"
            );
        } else {
            $choices_query = db_query(
                "select * from app_fields_choices where fields_id = '" . db_input(
                    $timeline_reports['use_background']
                ) . "' and length(bg_color)>0"
            );
        }

        while ($choices = db_fetch_array($choices_query)) {
            $rgb = convert_html_color_to_RGB($choices['bg_color']);

            $color = (($rgb[0] + $rgb[1] + $rgb[2]) < 480 ? 'white' : 'black');

            $html .= '
					.timeline-item-css-' . $choices['id'] . '{
						background: ' . $choices['bg_color'] . ' !important;						
					}
					
					.timeline-item-css-' . $choices['id'] . ' a{
						color: ' . $color . ' !important;						
					}	
								
					@media print
					{
						.timeline-event.ui-state-default.timeline-item-css-' . $choices['id'] . '{
							background: ' . $choices['bg_color'] . ' !important;						
						}			
					}
					';
        }

        $html .= '
				</style>';


        return $html;
    }

    public static function get_json($timeline_reports, $fiters_reports_id, $path)
    {
        $entity_info = db_find('app_entities', $timeline_reports['entities_id']);

        $start_date_field_info = db_find('app_fields', $timeline_reports['start_date']);
        $end_date_field_info = db_find('app_fields', $timeline_reports['end_date']);

        $start_date_column_name = ($start_date_field_info['type'] == 'fieldtype_date_added' ? 'date_added' : 'field_' . $start_date_field_info['id']);
        $end_date_column_name = ($end_date_field_info['type'] == 'fieldtype_date_added' ? 'date_added' : 'field_' . $end_date_field_info['id']);


        //build items listing
        $listing_sql_query = '';
        $listing_sql_query_select = '';

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $timeline_reports['entities_id'],
            $listing_sql_query_select
        );

        //prepare filters
        $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

        //check view assigned only access
        $listing_sql_query = items::add_access_query($timeline_reports['entities_id'], $listing_sql_query);

        //include access to parent records
        $listing_sql_query .= items::add_access_query_for_parent_entities($timeline_reports['entities_id']);

        $output_array = [];

        $listing_sql_query .= ' and e.' . $start_date_column_name . '>0 and e.' . $end_date_column_name . '>0';

        if (strlen($path)) {
            $path_info = items::parse_path($path);
            if ($path_info['parent_entity_item_id'] > 0) {
                $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
            }
        }


        $items_sql_query = "select * {$listing_sql_query_select} from app_entity_" . $timeline_reports['entities_id'] . " e where id>0 " . $listing_sql_query;
        $items_query = db_query($items_sql_query);
        while ($item = db_fetch_array($items_query)) {
            if (strlen($timeline_reports['heading_template']) > 0) {
                $options = [
                    'custom_pattern' => $timeline_reports['heading_template'],
                    'item' => $item,
                    'path' => $timeline_reports['entities_id'],
                ];

                $options['field']['configuration'] = '';

                $options['field']['entities_id'] = $timeline_reports['entities_id'];

                $fieldtype_text_pattern = new fieldtype_text_pattern();
                $title = $fieldtype_text_pattern->output($options);
            } else {
                $title = items::get_heading_field($timeline_reports['entities_id'], $item['id']);
            }

            if ($entity_info['parent_id'] > 0 and !isset($_GET['path'])) {
                $path_info = items::get_path_info($entity_info['id'], $item['id']);

                $title = str_replace('<br>', ' / ', $path_info['parent_name']) . ' / ' . $title;
            }

            $start = $item[$start_date_column_name];
            $end = $item[$end_date_column_name];

            $start_title = ($start_date_field_info['type'] == 'fieldtype_input_date' ? format_date(
                $start
            ) : format_date_time($start));
            $end_title = ($end_date_field_info['type'] == 'fieldtype_input_date' ? format_date($end) : format_date_time(
                $end
            ));

            //add +1 day to fix timelite to other report where last day is included
            if ($end_date_field_info['type'] == 'fieldtype_input_date') {
                $end = strtotime("+1 day", $end);
            }

            $className = ($timeline_reports['use_background'] > 0 ? 'timeline-item-css-' . $item['field_' . $timeline_reports['use_background']] : '');

            $output_array[] = [
                'start' => 'new Date(' . date('Y', $start) . ',' . (date('n', $start) - 1) . ',' . date(
                        'j',
                        $start
                    ) . ',' . date('H', $start) . ',' . date('i', $start) . ',0)',
                'end' => 'new Date(' . date('Y', $end) . ',' . (date('n', $end) - 1) . ',' . date(
                        'j',
                        $end
                    ) . ',' . date('H', $end) . ',' . date('i', $end) . ',0)',
                'content' => '<a data-title="' . $start_title . ' - ' . $end_title . '" data-content="' . addslashes(
                        $title
                    ) . '" onmouseover="timeLineItemPopover(this)" onmouseout="timeLineItemPopoverHide(this)" target="_blank" href="' . url_for(
                        'items/info',
                        'path=' . $timeline_reports['entities_id'] . '-' . $item['id']
                    ) . '">' . $title . '</a>',
                'className' => $className,
            ];
        }

        return str_replace(['start":"', '","end":"', '","content'],
            ['start":', ',"end":', ',"content'],
            app_json_encode($output_array));
    }
}