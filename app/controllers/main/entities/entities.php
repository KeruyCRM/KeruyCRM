<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities extends \Controller
{
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

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function set_entities_filter()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::$fw->entities_filter = \K::$fw->POST['entities_filter'];

            \Helpers\Urls::redirect_to('main/entities/entities');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'display_in_menu' => \K::$fw->POST['display_in_menu'] ?? 0,
                'notes' => strip_tags(\K::$fw->POST['notes']),
                'group_id' => \K::$fw->POST['group_id'] ?? 0,
                'sort_order' => \K::$fw->POST['sort_order']
            ];

            if (isset(\K::$fw->GET['id'])) {
                \K::model()->db_update('app_entities', $sql_data, ['id = ?', \K::$fw->GET['id']]);
            } else {
                if (isset(\K::$fw->POST['parent_id'])) {
                    $sql_data['parent_id'] = \K::$fw->POST['parent_id'];
                } else {
                    $sql_data['parent_id'] = 0;
                }

                \K::model()->begin();

                $mapper = \K::model()->db_perform('app_entities', $sql_data);
                $id = \K::model()->db_insert_id($mapper);

                \Models\Main\Entities::prepare_tables($id);

                $forms_tab_id = \Models\Main\Entities::insert_default_form_tab($id);

                \Models\Main\Entities::insert_reserved_fields($id, $forms_tab_id);

                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to('main/entities');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            $msg = \Models\Main\Entities::check_before_delete(\K::$fw->GET['id']);

            if (strlen($msg) > 0) {
                \K::flash()->addMessage($msg, 'error');
            } else {
                \K::model()->begin();

                $name = \Models\Main\Entities::get_name_by_id(\K::$fw->GET['id']);

                \Tools\Related_records::delete_entities_related_items_table(\K::$fw->GET['id']);

                \Models\Main\Entities::delete(\K::$fw->GET['id']);

                \Models\Main\Entities::delete_tables(\K::$fw->GET['id']);

                \K::model()->commit();

                \K::flash()->addMessage(sprintf(\K::$fw->TEXT_WARN_DELETE_SUCCESS, $name), 'success');
            }

            \Helpers\Urls::redirect_to('main/entities');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort_groups()
    {
        if (isset(\K::$fw->POST['groups_list'])) {
            $sort_order = 0;

            \K::model()->begin();

            foreach (explode(',', str_replace('group_', '', \K::$fw->POST['groups_list'])) as $v) {
                \K::model()->db_update('app_entities_groups', ['sort_order' => $sort_order], ['id = ?', $v]);
                $sort_order++;
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::model()->begin();

            if (isset(\K::$fw->POST['entities_list_0'])) {
                $sort_order = 0;

                $exp = explode(',', str_replace('entity_', '', \K::$fw->POST['entities_list_0']));

                foreach ($exp as $v) {
                    \K::model()->db_update(
                        'app_entities',
                        ['sort_order' => $sort_order, 'group_id' => 0],
                        ['id = ?', $v]
                    );
                    $sort_order++;
                }
            }

            //$groups_query = db_query("select * from app_entities_groups order by sort_order, name");

            $groups_query = \K::model()->db_fetch('app_entities_groups', [], ['order' => 'sort_order, name']);

            //while ($groups = db_fetch_array($groups_query)) {
            foreach ($groups_query as $groups) {
                $groups = $groups->cast();

                if (isset(\K::$fw->POST['entities_list_' . $groups['id']])) {
                    $sort_order = 0;

                    $exp = explode(
                        ',',
                        str_replace('entity_', '', \K::$fw->POST['entities_list_' . $groups['id']])
                    );

                    foreach ($exp as $v) {
                        \K::model()->db_update(
                            'app_entities',
                            ['sort_order' => $sort_order, 'group_id' => $groups['id']],
                            ['id = ?', $v]
                        );
                        $sort_order++;
                    }
                }
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}