<?php

class pivot_calendars
{
    static function get_reports_id_by_calendar_entity($id, $entiteis_id)
    {
        $reports_info_query = db_query(
            "select * from app_reports where entities_id='" . db_input(
                $entiteis_id
            ) . "' and reports_type='pivot_calendars" . $id . "'"
        );
        $reports_info = db_fetch_array($reports_info_query);

        return $reports_info['id'];
    }

    static function get_calendar_id_by_calendar_entity($id)
    {
        $info_query = db_query("select calendars_id from app_ext_pivot_calendars_entities where id='" . $id . "'");
        $info = db_fetch_array($info_query);

        return $info['calendars_id'];
    }

    static function has_access($users_groups, $access = false)
    {
        global $app_user;

        if ($app_user['group_id'] == 0) {
            return true;
        }

        if (strlen($users_groups)) {
            $users_groups = json_decode($users_groups, true);

            if (!$access) {
                if (isset($users_groups[$app_user['group_id']])) {
                    return (strlen($users_groups[$app_user['group_id']]) ? true : false);
                }
            } else {
                if (isset($users_groups[$app_user['group_id']])) {
                    return ($users_groups[$app_user['group_id']] == $access ? true : false);
                }
            }
        }

        return false;
    }

    public static function get_css($reports)
    {
        if ((int)$reports['use_background'] == 0) {
            return '';
        }

        $field_info_query = db_query("select * from app_fields where id='" . $reports['use_background'] . "'");
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
                    $reports['use_background']
                ) . "' and length(bg_color)>0"
            );
        }

        while ($choices = db_fetch_array($choices_query)) {
            $rgb = convert_html_color_to_RGB($choices['bg_color']);

            $color = (($rgb[0] + $rgb[1] + $rgb[2]) < 480 ? 'white' : 'black');

            $html .= '
					.fc-item-css-' . $reports['entities_id'] . '-' . $choices['id'] . ' .fc-title{
						color: ' . $color . ' !important;
					}
					';
        }

        $html .= '
					.fc-item-css .fc-title{
						color: white !important;
					}
				</style>';


        return $html;
    }

    public static function render_legend($reports)
    {
        $html = '';

        if ($reports['display_legend'] == 1) {
            $html .= '<ul class="list-inline">';

            $items_query = db_query(
                "select ce.*, e.name from app_ext_pivot_calendars_entities ce, app_entities e where length(bg_color)>0 and e.id=ce.entities_id and ce.calendars_id='" . $reports['id'] . "' order by e.name"
            );
            while ($items = db_fetch_array($items_query)) {
                $html .= '<li style="color: ' . $items['bg_color'] . '"><i class="fa fa-square" aria-hidden="true"></i> ' . $items['name'] . '</li>';
            }

            $html .= '</ul>';
        }

        return $html;
    }
}