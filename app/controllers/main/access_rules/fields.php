<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Access_rules;

class Fields extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Access_rules\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'fields.php';

        echo \K::view()->render($this->app_layout);
    }

    public function save()
    {
        $sql_data = [
            'entities_id' => $_GET['entities_id'],
            'fields_id' => $_POST['fields_id'],
        ];

        if (isset($_GET['id'])) {
            $access_rules_fields_info = db_find('app_access_rules_fields', $_GET['id']);
            if ($access_rules_fields_info['fields_id'] != $_POST['fields_id']) {
                db_delete_row('app_access_rules', $_GET['entities_id'], 'entities_id');
            }

            db_perform('app_access_rules_fields', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            db_perform('app_access_rules_fields', $sql_data);
        }

        redirect_to('access_rules/fields', 'entities_id=' . $_GET['entities_id']);
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            db_delete_row('app_access_rules_fields', $_GET['id']);
            db_delete_row('app_access_rules', $_GET['entities_id'], 'entities_id');
        }

        redirect_to('access_rules/fields', 'entities_id=' . $_GET['entities_id']);
    }
}