<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Delete_selected extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!\Models\Main\Users\Users::has_access('delete')) {
            \Helpers\Urls::redirect_to('main/dashboard/access_forbidden');
        }
    }

    public function index()
    {
        if (!isset(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])) {
            \K::$fw->app_selected_items[\K::$fw->GET['reports_id']] = [];
        }

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'delete_selected.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function delete_selected()
    {
        if (\K::$fw->VERB == 'POST') {
            if (!isset(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']])) {
                \K::$fw->app_selected_items[\K::$fw->GET['reports_id']] = [];
            }

            if (\Models\Main\Users\Users::has_access('delete_creator')) {
                foreach (\K::$fw->app_selected_items[\K::$fw->GET['reports_id']] as $k => $items_id) {
                    /*$item_info_query = db_query(
                        "select created_by from app_entity_" . \K::$fw->current_entity_id . " where id='" . $items_id . "'"
                    );*/
                    $item_info = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
                        'id = ?',
                        $items_id
                    ], [], 'created_by');

                    if ($item_info['created_by'] != \K::$fw->app_user['id']) {
                        unset(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']][$k]);
                    }
                }
            }

            \K::model()->begin();

            if (\Models\Main\Entities::has_subentities(
                    \K::$fw->current_entity_id
                ) == 0 and \K::$fw->current_entity_id != 1) {
                if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) > 0) {
                    foreach (\K::$fw->app_selected_items[\K::$fw->GET['reports_id']] as $items_id) {
                        \Models\Main\Items\Items::delete(\K::$fw->current_entity_id, $items_id);
                    }
                }
            } elseif (\Models\Main\Entities::has_subentities(
                    \K::$fw->current_entity_id
                ) and \K::$fw->current_entity_id != 1) {
                if (count(\K::$fw->app_selected_items[\K::$fw->GET['reports_id']]) > 0) {
                    $items_to_delete = \Models\Main\Items\Items::get_items_to_delete(
                        \K::$fw->current_entity_id,
                        [\K::$fw->current_entity_id => \K::$fw->app_selected_items[\K::$fw->GET['reports_id']]]
                    );

                    foreach ($items_to_delete as $entity_id => $items_list) {
                        foreach ($items_list as $item_id) {
                            \Models\Main\Items\Items::delete($entity_id, $item_id);
                        }
                    }
                }
            }

            \K::model()->commit();

            switch (\K::$fw->app_redirect_to) {
                case 'parent_item_info_page':
                    \Helpers\Urls::redirect_to(
                        'main/items/info',
                        'path=' . \Helpers\App::app_path_get_parent_path(\K::$fw->app_path)
                    );
                    break;
                case 'dashboard':
                    \Helpers\Urls::redirect_to('main/dashboard/dashboard', substr(\K::$fw->gotopage, 1));
                    break;
                default:
                    if (strstr(\K::$fw->app_redirect_to, 'report_')) {
                        \Helpers\Urls::redirect_to(
                            'main/reports/view',
                            'reports_id=' . str_replace('report_', '', \K::$fw->app_redirect_to)
                        );
                    } elseif (strstr(\K::$fw->app_redirect_to, 'mail_info_page_')) {
                        \Helpers\Urls::redirect_to(
                            'ext/mail/info',
                            'id=' . str_replace('mail_info_page_', '', \K::$fw->app_redirect_to)
                        );
                    } else {
                        if (\K::$fw->current_item_id) {
                            \K::$fw->app_path = substr(\K::$fw->app_path, 0, -(strlen(\K::$fw->current_item_id) + 1));
                        }

                        \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path);
                    }

                    break;
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}