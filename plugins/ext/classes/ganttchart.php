<?php

class ganttchart
{

    static public function get_columns_config($reports, $is_read_only = false)
    {
        global $app_user;

        $custom_columns = [];

        if (strlen($reports['fields_in_listing']) > 0) {
            $fields_access_schema = users::get_fields_access_schema($reports['entities_id'], $app_user['group_id']);

            $fields_query = db_query(
                "select * from app_fields where entities_id='" . $reports['entities_id'] . "' and id in (" . $reports['fields_in_listing'] . ") order by field(id," . $reports['fields_in_listing'] . ")"
            );
            while ($field = db_fetch_array($fields_query)) {
                //check field access
                if (isset($fields_access_schema[$field['id']])) {
                    if ($fields_access_schema[$field['id']] == 'hide') {
                        continue;
                    }
                }
                if ($field['type'] == 'fieldtype_progress') {
                    $custom_columns[] = '{name:"field_' . $field['id'] . '", label:"' . addslashes(
                            fields_types::get_option($field['type'], 'name', $field['name'])
                        ) . '", align: "left", template:function(obj){ return obj.field_' . $field['id'] . '+"%" }}';
                } else {
                    $custom_columns[] = '{name:"field_' . $field['id'] . '", label:"' . addslashes(
                            fields_types::get_option($field['type'], 'name', $field['name'])
                        ) . '", align: "left", template:function(obj){ return obj.field_' . $field['id'] . ' }}';
                }
            }
        }

        $entity_info = db_find('app_entities', $reports['entities_id']);
        $entity_cfg = entities::get_cfg($reports['entities_id']);
        $entitiy_name = (strlen(
            $entity_cfg['listing_heading']
        ) > 0 ? $entity_cfg['listing_heading'] : $entity_info['name']);

        $grid_width = 600 + (count($custom_columns) * 40);

        $date_colum_width = strstr($reports['gantt_date_format'], 'H') ? 105 : 75;

        $html = '
			gantt.config.columns=[
			    {name:"text",       label:"' . addslashes($entitiy_name) . '", tree:true,min_width:150 },
			    ' . (count($custom_columns) ? implode(',', $custom_columns) . ',' : '') .
            (strstr($reports['default_fields_in_listing'], 'start_date') ? '{name:"start_date", label:"' . addslashes(
                    TEXT_EXT_GANTT_START_DATE_SHORT
                ) . '", align: "center", width: ' . $date_colum_width . ' },' : '') .
            (strstr($reports['default_fields_in_listing'], 'end_date') ? '{name:"end_date", label:"' . addslashes(
                    TEXT_EXT_GANTT_END_DATE_SHORT
                ) . '", align: "center", width: ' . $date_colum_width . ',
			    		template:function(task){
					         return gantt.date.add(task.end_date, -1, gantt.config.duration_unit)
					     }, 
					},' : '') .
            (strstr($reports['default_fields_in_listing'], 'duration') ? '{name:"duration", label:"' . addslashes(
                    TEXT_EXT_GANTT_DURATION_SHORT
                ) . '", align: "center", width: 40 },' : '') .
            ((ganttchart::users_has_full_access(
                    $reports
                ) and !$is_read_only) ? '{name:"add",        label:"" } ' : '') . '
			];	
			    		
			gantt.config.grid_width = ' . ($reports['grid_width'] ? $reports['grid_width'] : $grid_width) . ';
						
		';

        return $html;
    }

    static public function get_access_by_report($ganttchart_id, $groups_id)
    {
        $info_query = db_query(
            "select * from app_ext_ganttchart_access where ganttchart_id='" . db_input(
                $ganttchart_id
            ) . "' and access_groups_id='" . db_input($groups_id) . "'"
        );
        if ($info = db_fetch_array($info_query)) {
            return $info['access_schema'];
        } else {
            return '';
        }
    }

    static public function users_has_access($ganttchart_id)
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return true;
        }

        $info_query = db_query(
            "select * from app_ext_ganttchart_access where ganttchart_id='" . db_input(
                $ganttchart_id
            ) . "' and access_groups_id='" . db_input($app_user['group_id']) . "'"
        );
        if ($info = db_fetch_array($info_query)) {
            return true;
        } else {
            return false;
        }
    }

    static public function users_has_full_access($reports)
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return true;
        }

        $info_query = db_query(
            "select * from app_ext_ganttchart_access where ganttchart_id='" . db_input(
                $reports['id']
            ) . "' and access_groups_id='" . db_input($app_user['group_id']) . "' and access_schema='full'"
        );
        if ($info = db_fetch_array($info_query)) {
            $access_schema = users::get_entities_access_schema($reports['entities_id'], $app_user['group_id']);

            if (users::has_access('create', $access_schema) and users::has_access('update', $access_schema)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static function get_duration_unit($reports)
    {
        global $app_fields_cache;

        if ($app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] == 'fieldtype_input_datetime') {
            return 'hour';
        }

        if ($app_fields_cache[$reports['entities_id']][$reports['end_date']]['type'] == 'fieldtype_dynamic_date') {
            $cfg = new settings($app_fields_cache[$reports['entities_id']][$reports['end_date']]['configuration']);
            if (stristr($cfg->get('date_format'), 'h') or stristr($cfg->get('date_format'), 'g')) {
                return 'hour';
            }
        }

        return 'day';
    }

    static function has_date_added($reports)
    {
        global $app_fields_cache;

        return ($app_fields_cache[$reports['entities_id']][$reports['start_date']]['type'] == 'fieldtype_date_added' ? true : false);
    }

    static function get_date_grid_format($reports)
    {
        $format = '%d/%m/%Y';

        switch ($reports['gantt_date_format']) {
            case 'MM/DD/YYYY':
                $format = '%m/%d/%Y';
                break;
            case 'MM/DD/YYYY H:i':
                $format = '%m/%d/%Y %H:%i';
                break;
            case 'DD/MM/YYYY':
                $format = '%d/%m/%Y';
                break;
            case 'DD/MM/YYYY H:i':
                $format = '%d/%m/%Y %H:%i';
                break;
        }

        return $format;
    }
}
