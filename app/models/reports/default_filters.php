<?php

namespace Models\Reports;

class Default_filters
{
    static function get_reports_id($entities_id, $reports_type)
    {
        global $app_logged_users_id;

        //create default filter
        $reports_info_query = db_query(
            "select id from app_reports where entities_id='" . db_input(
                $entities_id
            ) . "' and reports_type='" . $reports_type . "'"
        );
        if (!$reports_info = db_fetch_array($reports_info_query)) {
            $sql_data = [
                'name' => '',
                'entities_id' => $entities_id,
                'reports_type' => $reports_type,
                'in_menu' => 0,
                'in_dashboard' => 0,
                'listing_order_fields' => '',
                'created_by' => $app_logged_users_id,
            ];

            db_perform('app_reports', $sql_data);
            $reports_id = db_insert_id();

            reports::auto_create_parent_reports($reports_id);
        } else {
            $reports_id = $reports_info['id'];
        }

        return $reports_id;
    }
}