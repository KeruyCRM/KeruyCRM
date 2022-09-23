<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Import extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!\Models\Main\Users\Users::has_access('import') or !strlen(\K::$fw->app_path)) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }

        if (!\K::app_session_is_registered('import_fields')) {
            \K::$fw->import_fields = [];
            \K::app_session_register('import_fields');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'import.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function import()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::$fw->worksheet = json_decode(stripslashes(\K::$fw->POST['worksheet']), true);
            $entities_id = \K::$fw->current_entity_id;
            $parent_item_id = \K::$fw->parent_entity_item_id;

            $entity_info = \K::model()->db_find('app_entities', $entities_id);

            $redirect_path = $entities_id;

            if ($entity_info['parent_id'] > 0) {
                /*$parent_item_query = db_query(
                    "select * from app_entity_" . $entity_info['parent_id'] . " where id='" . db_input(
                        $parent_item_id
                    ) . "'"
                );*/

                $parent_item = \K::model()->db_fetch_one('app_entity_' . (int)$entity_info['parent_id'], [
                    'id = ?',
                    $parent_item_id
                ]);

                if ($parent_item) {
                    $path_info = \Models\Main\Items\Items::get_path_info($entity_info['parent_id'], $parent_item['id']);

                    $redirect_path = $path_info['full_path'] . '/' . $entities_id;
                }
            }

            //check if any fields are binded
            if (count(\K::$fw->import_fields) == 0) {
                \K::flash()->addMessage(\K::$fw->TEXT_IMPORT_BIND_FIELDS_ERROR, 'error');
                \Helpers\Urls::redirect_to('main/items/items', 'path=' . $redirect_path);
            }

            //check required fields for users entity
            if ($entities_id == 1) {
                if (!in_array(7, \K::$fw->import_fields) or !in_array(8, \K::$fw->import_fields) or !in_array(
                        9,
                        \K::$fw->import_fields
                    )) {
                    \K::flash()->addMessage(\K::$fw->TEXT_IMPORT_BIND_USERS_FIELDS_ERROR, 'error');
                    \Helpers\Urls::redirect_to('main/items/items', 'path=' . $redirect_path);
                }
            }

            //multilevel import
            \K::$fw->multilevel_import = (int)\K::$fw->GET['multilevel_import'];

            $import_entities_list = [];
            $import_entities_list[] = \K::$fw->current_entity_id;

            if (\K::$fw->multilevel_import > 0) {
                $import_entities_list = [];
                $import_entities_list[] = \K::$fw->multilevel_import;

                foreach (\Models\Main\Entities::get_parents(\K::$fw->multilevel_import) as $entity_id) {
                    $import_entities_list[] = $entity_id;

                    if ($entity_id == \K::$fw->current_entity_id) {
                        break;
                    }
                }

                $import_entities_list = array_reverse($import_entities_list);

                //check heading
                foreach ($import_entities_list as $id) {
                    $check = false;
                    $heading_field_id = \Models\Main\Fields::get_heading_id($id);
                    foreach (\K::$fw->import_fields as $c => $v) {
                        if ($v == $heading_field_id) {
                            $check = true;
                        }
                    }

                    if (!$check) {
                        \K::flash()->addMessage(
                            sprintf(
                                \K::$fw->TEXT_MULTI_LEVEL_IMPORT_HEADING_ERROR,
                                \Models\Main\Entities::get_name_by_id($id)
                            ),
                            'error'
                        );
                        \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
                    }
                }
            }

            //check if import first row
            $first_row = (isset(\K::$fw->POST['import_first_row']) ? 0 : 1);

            //use when import users
            \K::$fw->already_exist_username = [];

            \K::$fw->count_items_added = 0;
            \K::$fw->count_items_updated = 0;

            //create choices cache to reduce sql queries
            \K::$fw->choices_names_to_id = [];
            \K::$fw->global_choices_names_to_id = [];
            \K::$fw->choices_parents_to_id = [];
            \K::$fw->global_choices_parents_to_id = [];

            \K::$fw->unique_fields = \Models\Main\Fields::get_unique_fields_list($entities_id);

            //start import
            for (\K::$fw->row = $first_row; \K::$fw->row < count(\K::$fw->worksheet); ++\K::$fw->row) {
                \K::$fw->import_entity_parent_item_id = $parent_item_id;

                if (\K::$fw->multilevel_import > 0) {
                    foreach ($import_entities_list as $import_entity_level => $import_entity_id) {
                        \K::$fw->entities_id = $import_entity_id;
                        require(\Helpers\Urls::components_path('main/items/_import.process.inc'));
                    }
                } else {
                    \K::$fw->entities_id = \K::$fw->current_entity_id;
                    require(\Helpers\Urls::components_path('main/items/_import.process.inc'));
                }
            }

            if (count(\K::$fw->already_exist_username) > 0) {
                \K::flash()->addMessage(
                    \K::$fw->TEXT_USERS_IMPORT_ERROR . ' ' . implode(', ', \K::$fw->already_exist_username),
                    'warning'
                );
            }

            switch (\K::$fw->POST['import_action']) {
                case 'import':
                    \K::flash()->addMessage(
                        \K::$fw->TEXT_COUNT_ITEMS_ADDED . ' ' . \K::$fw->count_items_added,
                        'success'
                    );
                    break;
                case 'update':
                    \K::flash()->addMessage(
                        \K::$fw->TEXT_COUNT_ITEMS_UPDATED . ' ' . \K::$fw->count_items_updated,
                        'success'
                    );
                    break;
                case 'update_import':
                    \K::flash()->addMessage(
                        \K::$fw->TEXT_COUNT_ITEMS_UPDATED . ' ' . \K::$fw->count_items_updated . '. ' . \K::$fw->TEXT_COUNT_ITEMS_ADDED . ' ' . \K::$fw->count_items_added,
                        'success'
                    );
                    break;
            }

            //reset import fields session
            \K::$fw->import_fields = [];

            \Helpers\Urls::redirect_to('main/items/items', 'path=' . $redirect_path);
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function bind_field()
    {
        if (\K::$fw->VERB == 'POST') {
            $col = \K::$fw->POST['col'];
            $filed_id = \K::$fw->POST['filed_id'];

            \K::$fw->multilevel_import = (int)\K::$fw->GET['multilevel_import'];

            if ($filed_id > 0) {
                \K::$fw->import_fields[$col] = $filed_id;

                $v = \K::model()->db_find('app_fields', $filed_id);

                if (\K::$fw->multilevel_import > 0) {
                    echo '<small style="font-weight: normal">' . \Models\Main\Entities::get_name_by_id(
                            $v['entities_id']
                        ) . ':</small><br>';
                }

                echo \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']);
            } elseif (isset(\K::$fw->import_fields[$col])) {
                unset(\K::$fw->import_fields[$col]);
                echo '';
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}