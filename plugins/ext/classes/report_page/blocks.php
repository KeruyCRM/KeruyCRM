<?php

namespace report_page;

class blocks
{

    static function get_name($type)
    {
        $name = '';
        switch ($type) {
            case 'field':
                $name = TEXT_FIELD;
                break;
            case 'table':
                $name = TEXT_TABLE;
                break;
            case 'php':
                $name = TEXT_PHP_CODE;
                break;
            case 'html':
                $name = TEXT_HTML_CODE;
                break;
        }

        return $name;
    }

    static function delete($id)
    {
        db_query("delete from app_ext_report_page_blocks where id=" . $id);

        $block_query = db_query("select id from app_ext_report_page_blocks where parent_id='" . $id . "'");
        while ($block = db_fetch_array($block_query)) {
            self::delete($block['id']);
        }
    }

    static function get_filters_link($blocks)
    {
        $html = '';
        $filter_entity_id = false;

        if ($blocks['field_id'] > 0) {
            $field_info_query = db_query("select * from app_fields where id='" . $blocks['field_id'] . "'");
            if (!$field_info = db_fetch_array($field_info_query)) {
                return '';
            }

            $cfg = new \settings($field_info['configuration']);

            switch ($field_info['type']) {
                case 'fieldtype_entity':
                case 'fieldtype_entity_ajax':
                case 'fieldtype_related_records':
                case 'fieldtype_users':
                case 'fieldtype_users_ajax':
                case 'fieldtype_user_roles':
                case 'fieldtype_users_approve':
                    $filter_entity_id = strlen($cfg->get('entity_id')) ? $cfg->get('entity_id') : 1;
                    break;
            }
        }

        if ($filter_entity_id) {
            $html .= '<br><small>' . link_to(
                    TEXT_FILTERS . ' (' . \reports::count_filters_by_reports_type(
                        $filter_entity_id,
                        'report_page_block' . $blocks['id']
                    ) . ')',
                    url_for(
                        'default_filters/filters',
                        'reports_id=' . \default_filters::get_reports_id(
                            $filter_entity_id,
                            'report_page_block' . $blocks['id']
                        ) . '&redirect_to=report_page_block' . $blocks['id']
                    )
                ) . '</small>';
        }

        return $html;
    }
}
