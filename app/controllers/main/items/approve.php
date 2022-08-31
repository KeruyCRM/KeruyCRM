<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Approve extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        if (!isset(\K::$fw->app_fields_cache[\K::$fw->current_entity_id][\K::$fw->GET['fields_id']])) {
            \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
        }

        \K::$fw->cfg = new \Models\Main\Fields_types_cfg(
            \K::$fw->app_fields_cache[\K::$fw->current_entity_id][\K::$fw->GET['fields_id']]['configuration']
        );
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'approve.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function approve()
    {
        if (\K::$fw->VERB == 'POST') {
            $gotopage = '';
            if (isset(\K::$fw->POST['gotopage'])) {
                $gotopage = '&gotopage[' . key(\K::$fw->POST['gotopage']) . ']=' . current(\K::$fw->POST['gotopage']);
            }

            if (!\Models\Main\Items\Approved_items::is_approved_by_user(
                \K::$fw->current_entity_id,
                \K::$fw->current_item_id,
                \K::$fw->GET['fields_id'],
                \K::$fw->app_user['id']
            )) {
                //approve
                $sql_data = [
                    'entities_id' => \K::$fw->current_entity_id,
                    'items_id' => \K::$fw->current_item_id,
                    'fields_id' => \K::$fw->GET['fields_id'],
                    'users_id' => \K::$fw->app_user['id'],
                    'signature' => (\K::$fw->POST['signature'] ?? ''),
                    'date_added' => time(),
                ];

                \K::model()->db_perform('app_approved_items', $sql_data);

                //add comment
                if (\K::$fw->cfg->get('add_comment') == 1) {
                    $sql_data = [
                        'description' => (strlen(\K::$fw->cfg->get('comment_text')) ? \K::$fw->cfg->get(
                            'comment_text'
                        ) : \K::$fw->TEXT_APPROVED),
                        'entities_id' => \K::$fw->current_entity_id,
                        'items_id' => \K::$fw->current_item_id,
                        'date_added' => time(),
                        'created_by' => \K::$fw->app_user['id'],
                    ];

                    $mapper = \K::model()->db_perform('app_comments', $sql_data);

                    $comments_id = \K::model()->db_insert_id($mapper);

                    //send notification
                    \Helpers\App::app_send_new_comment_notification(
                        $comments_id,
                        \K::$fw->current_item_id,
                        \K::$fw->current_entity_id
                    );

                    //track changes
                    if (\Helpers\App::is_ext_installed()) {
                        $log = new track_changes(\K::$fw->current_entity_id, \K::$fw->current_item_id);
                        $log->log_comment($comments_id, []);
                    }
                }

                //run process
                if (\K::$fw->cfg->get('run_process') > 0) {
                    if (\Models\Main\Items\Approved_items::is_all_approved(
                        \K::$fw->current_entity_id,
                        \K::$fw->current_item_id,
                        \K::$fw->GET['fields_id']
                    )) {
                        \Helpers\Urls::redirect_to(
                            'main/items/processes/run',
                            'id=' . \K::$fw->cfg->get(
                                'run_process'
                            ) . '&path=' . \K::$fw->app_path . '&redirect_to=' . \K::$fw->app_redirect_to . $gotopage
                        );
                    }
                }
            }

            switch (\K::$fw->app_redirect_to) {
                case 'dashboard':
                    \Helpers\Urls::redirect_to('main/dashboard/', substr($gotopage, 1));
                    break;
                case 'items_info':
                    \Helpers\Urls::redirect_to('main/items/info', 'path=' . \K::$fw->app_path);
                    break;
                case 'items':
                    \Helpers\Urls::redirect_to(
                        'main/items/items',
                        'path=' . substr(\K::$fw->app_path, 0, -(strlen(\K::$fw->current_item_id) + 1)) . $gotopage
                    );
                    break;
                default:
                    if (strstr(\K::$fw->app_redirect_to, 'kanban')) {
                        \Helpers\Urls::redirect_to(
                            'ext/kanban/view',
                            'id=' . str_replace('kanban', '', \K::$fw->app_redirect_to) . '&path=' . \K::$fw->app_path
                        );
                    } elseif (strstr(\K::$fw->app_redirect_to, 'related_records_info_page_')) {
                        \Helpers\Urls::redirect_to(
                            'main/items/info',
                            'path=' . str_replace('related_records_info_page_', '', \K::$fw->app_redirect_to)
                        );
                    } elseif (strstr(\K::$fw->app_redirect_to, 'report_')) {
                        \Helpers\Urls::redirect_to(
                            'main/reports/view',
                            'reports_id=' . str_replace('report_', '', \K::$fw->app_redirect_to) . $gotopage
                        );
                    } else {
                        \Helpers\Urls::redirect_to('main/items/items', 'path=' . \K::$fw->app_path . $gotopage);
                    }
                    break;
            }
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}