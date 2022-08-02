<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_groups extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        $groups_query = db_query("select * from app_entities_groups order by sort_order, name");

        \K::$fw->groups_query = '';

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_groups.php';

        echo \K::view()->render($this->app_layout);
    }

    public function save()
    {
        $sql_data = [
            'name' => $_POST['name'],
            'sort_order' => $_POST['sort_order']
        ];

        if (isset($_GET['id'])) {
            db_perform('app_entities_groups', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            if (isset($_POST['parent_id'])) {
                $sql_data['parent_id'] = $_POST['parent_id'];
            }

            db_perform('app_entities_groups', $sql_data);
            $id = db_insert_id();
        }

        redirect_to('entities/entities_groups');
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $name = entities_groups::get_name_by_id(_GET('id'));

            entities_groups::delete(_GET('id'));

            $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');

            redirect_to('entities/entities_groups');
        }
    }

    public function sort()
    {
        $choices_sorted = $_POST['choices_sorted'];

        if (strlen($choices_sorted) > 0) {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);

            $sort_order = 0;
            foreach ($choices_sorted as $v) {
                db_query("update app_entities_groups set sort_order={$sort_order} where id={$v['id']}");
                $sort_order++;
            }
        }

        redirect_to('entities/entities_groups');
    }
}