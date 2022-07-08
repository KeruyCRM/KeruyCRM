<?php

class fieldtype_date_added
{
    public $options;

    function __construct()
    {
        $this->options = ['name' => TEXT_FIELDTYPE_DATEADDED_TITLE, 'title' => TEXT_FIELDTYPE_DATEADDED_TITLE];
    }

    function output($options)
    {
        return format_date_time($options['value']);
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = reports::prepare_dates_sql_filters($filters, $options['prefix']);

        if (count($sql) > 0) {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }
}