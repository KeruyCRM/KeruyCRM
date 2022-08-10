<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_date_added
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::$fw->TEXT_FIELDTYPE_DATEADDED_TITLE,
            'title' => \K::$fw->TEXT_FIELDTYPE_DATEADDED_TITLE
        ];
    }

    public function output($options)
    {
        return \Helpers\App::format_date_time($options['value']);
    }

    public function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = \Models\Main\Reports\Reports::prepare_dates_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}