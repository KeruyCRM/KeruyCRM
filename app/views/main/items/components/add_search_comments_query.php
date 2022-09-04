<?php

if (!defined('KERUY_CRM')) {
    exit;
}

if (\Helpers\App::app_parse_search_string(\K::$fw->POST['search_keywords'], \K::$fw->search_keywords)) {
    if (isset(\K::$fw->search_keywords) && (sizeof(\K::$fw->search_keywords) > 0)) {
        \K::$fw->listing_sql_query .= ' and (';
        for ($i = 0, $n = sizeof(\K::$fw->search_keywords); $i < $n; $i++) {
            switch (\K::$fw->search_keywords[$i]) {
                case '(':
                case ')':
                case 'and':
                case 'or':
                    \K::$fw->listing_sql_query .= " " . \K::$fw->search_keywords[$i] . " ";
                    break;
                default:
                    $keyword = \K::$fw->search_keywords[$i];
                    \K::$fw->listing_sql_query .= 'description like ' . \K::model()->quote('%' . $keyword . '%');
                    break;
            }
        }
        \K::$fw->listing_sql_query .= ")";

        if (count(\K::$fw->search_keywords) == 1 and is_numeric(
                \K::$fw->search_keywords[0]
            ) and \K::$fw->entity_cfg->get('display_comments_id') == 1) {
            \K::$fw->listing_sql_query .= ' or id = ' . \K::model()->quote(
                    \K::$fw->search_keywords[0],
                    \PDO::PARAM_INT
                );
        }
    }
} else {
    echo '<div class="alert alert-danger">' . \K::$fw->TEXT_ERROR_INVALID_KEYWORDS . '</div>';
}