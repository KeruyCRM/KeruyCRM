<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        if (isset(\K::$fw->POST['switch_to_entities_id'])) {
            \Helpers\Urls::redirect_to(
                'main/entities/entities_configuration',
                'entities_id=' . \K::$fw->POST['switch_to_entities_id']
            );
        }

        if (!\K::app_session_is_registered('entities_filter')) {
            \K::$fw->entities_filter = 0;
            \K::app_session_register('entities_filter');
        }

        \K::$fw->entities_list = \Models\Main\Entities::get_tree(0, [], 0, [], [], false, \K::$fw->entities_filter);
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities.php';

        echo \K::view()->render($this->app_layout);
    }

    public function set_entities_filter()
    {
        $entities_filter = _POST('entities_filter');

        redirect_to('entities/entities');
    }

    public function save()
    {
        $sql_data = [
            'name' => $_POST['name'],
            'display_in_menu' => $_POST['display_in_menu'] ?? 0,
            'notes' => strip_tags($_POST['notes']),
            'group_id' => $_POST['group_id'] ?? 0,
            'sort_order' => $_POST['sort_order']
        ];

        if (isset($_GET['id'])) {
            db_perform('app_entities', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        } else {
            if (isset($_POST['parent_id'])) {
                $sql_data['parent_id'] = $_POST['parent_id'];
            }

            db_perform('app_entities', $sql_data);
            $id = db_insert_id();

            entities::prepare_tables($id);

            $forms_tab_id = entities::insert_default_form_tab($id);

            entities::insert_reserved_fields($id, $forms_tab_id);
        }

        redirect_to('entities/');
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $msg = entities::check_before_delete($_GET['id']);

            if (strlen($msg) > 0) {
                $alerts->add($msg, 'error');
            } else {
                $name = entities::get_name_by_id($_GET['id']);

                related_records::delete_entities_related_items_table($_GET['id']);

                entities::delete($_GET['id']);

                entities::delete_tables($_GET['id']);

                $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            redirect_to('entities/');
        }
    }

    public function sort_groups()
    {
        if (isset($_POST['groups_list'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('group_', '', $_POST['groups_list'])) as $v) {
                db_perform('app_entities_groups', ['sort_order' => $sort_order], 'update', "id='" . db_input($v) . "'");
                $sort_order++;
            }
        }

        exit();
    }

    public function sort()
    {
        if (isset($_POST['entities_list_0'])) {
            $sort_order = 0;
            foreach (explode(',', str_replace('entity_', '', $_POST['entities_list_0'])) as $v) {
                db_perform(
                    'app_entities',
                    ['sort_order' => $sort_order, 'group_id' => 0],
                    'update',
                    "id='" . db_input($v) . "'"
                );
                $sort_order++;
            }
        }

        $groups_query = db_query("select * from app_entities_groups order by sort_order, name");
        while ($groups = db_fetch_array($groups_query)) {
            if (isset($_POST['entities_list_' . $groups['id']])) {
                $sort_order = 0;
                foreach (explode(',', str_replace('entity_', '', $_POST['entities_list_' . $groups['id']])) as $v) {
                    db_perform(
                        'app_entities',
                        ['sort_order' => $sort_order, 'group_id' => $groups['id']],
                        'update',
                        "id='" . db_input($v) . "'"
                    );
                    $sort_order++;
                }
            }
        }

        exit();
    }
}