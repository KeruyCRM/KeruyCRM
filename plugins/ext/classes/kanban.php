<?php

class kanban
{

    static function get_items_query($force_filter_by, $reports, $fiters_reports_id)
    {
        global $sql_query_having;

        $listing_sql_query = '';
        $listing_sql_query_select = '';
        $listing_sql_query_having = '';
        $listing_sql_query_join = '';
        $sql_query_having = [];

        //filter items by parent
        /* if($parent_entity_item_id>0)
          {
          $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
          } */

        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select(
            $reports['entities_id'],
            $listing_sql_query_select
        );

        //prepare filters
        $listing_sql_query = reports::add_filters_query($fiters_reports_id, $listing_sql_query);

        //prepare having query for formula fields
        if (isset($sql_query_having[$reports['entities_id']])) {
            $listing_sql_query_having = reports::prepare_filters_having_query(
                $sql_query_having[$reports['entities_id']]
            );
        }

        if (isset($_GET['path'])) {
            $path_info = items::parse_path($_GET['path']);
            if ($path_info['parent_entity_item_id'] > 0) {
                $listing_sql_query .= " and e.parent_item_id='" . $path_info['parent_entity_item_id'] . "'";
            }
        }

        $listing_sql_query .= reports::force_filter_by($force_filter_by);

        //check view assigned only access
        $listing_sql_query = items::add_access_query($reports['entities_id'], $listing_sql_query);

        $listing_sql_query .= items::add_access_query_for_parent_entities($reports['entities_id']);

        //add having query
        $listing_sql_query .= $listing_sql_query_having;

        //add order_query
        $order_info_query = db_query(
            "select listing_order_fields from app_reports where id='" . $fiters_reports_id . "'"
        );
        $order_info = db_fetch_array($order_info_query);
        if (strlen($order_info['listing_order_fields']) > 0) {
            $info = reports::add_order_query($order_info['listing_order_fields'], $reports['entities_id']);

            $listing_sql_query .= str_replace('order by', 'order by e.parent_item_id,', $info['listing_sql_query']);
            $listing_sql_query_join .= $info['listing_sql_query_join'];
        } else {
            $listing_sql_query .= " order by e.parent_item_id";
        }

        $items_sql_query = "select e.* {$listing_sql_query_select} from app_entity_" . $reports['entities_id'] . " e " . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;

        return db_query($items_sql_query, false);
    }

}
