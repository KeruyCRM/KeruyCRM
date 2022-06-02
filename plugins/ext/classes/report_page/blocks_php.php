<?php

namespace report_page;

class blocks_php
{

    static function render_helper($arg)
    {
        $arg = new \settings($arg);

        $html = '';
        switch ($arg->get('type')) {
            case 'item':
                $html = self::render_item_helper($arg->get('entity_id'));
                break;
            case 'total':
                $html = self::render_total_helper($arg->get('block_id'));
                break;
        }

        return $html;
    }

    static function render_item_helper($entity_id)
    {
        $html = '
            <div class="dropdown">
                <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  ' . TEXT_AVAILABLE_VALUES . '
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">
            ';

        $field_query = \fields::get_query($entity_id, "and f.type not in ('fieldtype_action')");
        while ($field = db_fetch_array($field_query)) {
            if (in_array($field['type'], \fields_types::get_reserved_types())) {
                $data_insert = '$item[\'' . str_replace('fieldtype_', '', $field['type']) . '\']';
            } else {
                $data_insert = '$item[\'field_' . $field['id'] . '\']';
            }

            $html .= '
                        <li>
                                    <a href="#"  class="insert_to_php_code" data-field="' . $data_insert . '">' . \fields_types::get_option(
                    $field['type'],
                    'name',
                    $field['name']
                ) . ' ' . $data_insert . '</a>  		      
                        </li>';
        }

        $html .= '</ul></div>';


        $html .= '
            <script>
                $(".insert_to_php_code").click(function(){
                    insert_to_code_mirror("settings_php_code",$(this).attr("data-field"))
                })
            </script>
            ';

        return $html;
    }

    static function render_total_helper($block_id)
    {
        $html = '
            <div class="dropdown">
                <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  ' . TEXT_COLUMNS . '
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">
            ';

        $blocks_query = db_query(
            "select b.*,f.type as field_type, f.name as field_name, f.entities_id as field_entity_id from app_ext_report_page_blocks b left join app_fields f on b.field_id=f.id  where block_type='body_cell'  and b.parent_id = {$block_id} order by b.sort_order, b.id"
        );
        while ($blocks = db_fetch_array($blocks_query)) {
            $settings = new \settings($blocks['settings']);


            $data_insert = '$total[\'column_' . $blocks['id'] . '\']';


            $html .= '
                        <li>
                                    <a href="#"  class="insert_to_php_code" data-field="' . $data_insert . '">' . strip_tags(
                    $settings->get('heading')
                ) . ' ' . $data_insert . '</a>  		      
                        </li>';
        }

        $html .= '</ul></div>';


        $html .= '
            <script>
                $(".insert_to_php_code").click(function(){
                    insert_to_code_mirror("settings_php_code",$(this).attr("data-field"))
                })
            </script>
            ';

        return $html;
    }
}
