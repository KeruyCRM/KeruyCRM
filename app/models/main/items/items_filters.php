<?php

namespace Models\Main\Items;

class Items_filters
{
    private $entity_id, $item_id;

    public function __construct($entity_id, $item_id)
    {
        $this->entity_id = $entity_id;
        $this->item_id = $item_id;
    }

    public function check($cfg = [])
    {
        global $sql_query_having;

        $cfg = new \Tools\Settings($cfg, [
            'report_type' => '',
            'report_id' => 0,
        ]);

        $where_sql = '';

        if ($cfg->get('report_id')) {
            $where_sql = " and id=" . (int)$cfg->get('report_id');
        } else {
            $where_sql = " and reports_type='" . $cfg->get('report_type') . "'";
        }

        $reports_info_query = db_query(
            "select id from app_reports where entities_id='" . db_input($this->entity_id) . "' {$where_sql}"
        );
        if ($reports_info = db_fetch_array($reports_info_query)) {
            $listing_sql_query = '';
            $listing_sql_query_select = '';
            $listing_sql_query_having = '';
            $sql_query_having = [];

            $listing_sql_query = reports::add_filters_query($reports_info['id'], $listing_sql_query);

            //prepare having query for formula fields
            if (isset($sql_query_having[$this->entity_id])) {
                $listing_sql_query_having = reports::prepare_filters_having_query($sql_query_having[$this->entity_id]);
            }

            $listing_sql_query .= $listing_sql_query_having;

            $item_info_sql = "select e.id " . fieldtype_formula::prepare_query_select(
                    $this->entity_id
                ) . " from app_entity_" . $this->entity_id . " e  where e.id='" . $this->item_id . "' " . $listing_sql_query;

            $item_info_query = db_query($item_info_sql);
            if ($item_info = db_fetch_array($item_info_query)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}