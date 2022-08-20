<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Forms_hidden_fields extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);

        $choices = [];

        $where_sql = " and f.type not in (" . \Models\Main\Fields_types::get_type_list_excluded_in_form() . ")";

        $fields_query = \Models\Main\Fields::get_query(\K::$fw->GET['entities_id'], $where_sql);

        //while ($fields = db_fetch($fields_query)) {
        foreach ($fields_query as $fields) {
            $choices[$fields['id']] = $fields['name'];
        }

        \K::$fw->choices = $choices;

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'forms_hidden_fields.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['entities_id'])) {
            $cfg = new \Models\Main\Entities_cfg(\K::$fw->GET['entities_id']);

            if (isset(\K::$fw->POST['hidden_form_fields'])) {
                $cfg->set('hidden_form_fields', implode(',', \K::$fw->POST['hidden_form_fields']));
            } else {
                $cfg->set('hidden_form_fields', '');
            }

            \Helpers\Urls::redirect_to('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id']);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}