<?php

namespace Tools\FieldsTypes;

class Fieldtype_date_updated
{
    public $options;

    public function __construct()
    {
        $this->options = [
            'name' => \K::f3()->TEXT_FIELDTYPE_DATE_UPDATED_TITLE,
            'title' => \K::f3()->TEXT_FIELDTYPE_DATE_UPDATED_TITLE
        ];
    }

    public function output($options)
    {
        return ($options['value'] > 0 ? format_date_time($options['value']) : '');
    }

    public function reports_query($options)
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