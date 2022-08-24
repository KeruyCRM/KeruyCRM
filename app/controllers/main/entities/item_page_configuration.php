<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Item_page_configuration extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');
        }

        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);
    }

    public function index()
    {
        \K::$fw->default_selector = ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO];

        $choices = [];
        $choices['3-9'] = '20% - 80%';
        $choices['4-8'] = '30% - 70%';
        $choices['5-7'] = '40% - 60%';
        $choices['6-6'] = '50% - 50%';
        $choices['7-5'] = '60% - 40%';
        $choices['8-4'] = '70% - 30%';
        $choices['9-3'] = '80% - 20%';
        $choices['12-12'] = '100% - 0%';

        \K::$fw->choices = $choices;

        $choices2 = [];
        $choices2['1'] = \K::$fw->TEXT_ONE_COLUMN;
        $choices2['one_column_tabs'] = \K::$fw->TEXT_ONE_COLUMN_TABS;
        $choices2['one_column_accordion'] = \K::$fw->TEXT_ONE_COLUMN_ACCORDION;
        $choices2['2'] = \K::$fw->TEXT_TWO_COLUMNS;

        \K::$fw->choices2 = $choices2;

        $choices3 = [];
        $fields_query = \K::model()->db_query_exec(
            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.is_heading != 1 and f.entities_id = ? and f.forms_tabs_id = t.id order by t.sort_order, t.name, f.sort_order, f.name",
            \K::$fw->GET['entities_id'],
            'app_fields,app_forms_tabs'
        );

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices3[$fields['tab_name']][$fields['id']] = (strlen(
                $fields['name']
            ) ? $fields['name'] : \Models\Main\Fields_types::get_title($fields['type']));
        }

        \K::$fw->choices3 = $choices3;

        $choices4 = [];
        $choices4['left_column'] = \K::$fw->TEXT_LEFT_COLUMN;
        $choices4['right_column'] = \K::$fw->TEXT_RIGHT_COLUMN;

        \K::$fw->choices4 = $choices4;

        //$entities_query = db_query("select * from app_entities where parent_id = '" . db_input(_get::int('entities_id')) . "'");

        \K::$fw->entities_query = $a = \K::model()->db_fetch('app_entities', [
            'parent_id = ?',
            \K::$fw->GET['entities_id']
        ]);

        /*$fields_query2 = db_query(
            "select id, name, configuration, entities_id from app_fields where entities_id!='" . db_input(
                _get::int('entities_id')
            ) . "' and type in ('fieldtype_entity','fieldtype_entity_ajax','fieldtype_entity_multilevel')"
        );*/

        \K::$fw->fields_query2 = \K::model()->db_fetch('app_fields', [
            'entities_id != ? and type in (' . \K::model()->quoteToString(
                ['fieldtype_entity', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']
            ) . ')',
            \K::$fw->GET['entities_id']
        ], [], 'id,name,configuration,entities_id');

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'item_page_configuration.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->POST['cfg']['item_page_hidden_fields'])) {
                \K::$fw->POST['cfg']['item_page_hidden_fields'] = '';
            }

            foreach (\K::$fw->POST['cfg'] as $k => $v) {
                \K::$fw->cfg->set($k, $v);
            }

            \K::flash()->addMessage(\K::$fw->TEXT_CONFIGURATION_UPDATED, 'success');

            \Helpers\Urls::redirect_to(
                'main/entities/item_page_configuration',
                'entities_id=' . \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}