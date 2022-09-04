<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Comments_form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->GET['id']) and !\Models\Main\Users\Users::has_comments_access('update')) {
            echo \Helpers\App::ajax_modal_template(\K::$fw->TEXT_WARNING, \K::$fw->TEXT_NO_ACCESS);
        } elseif (!\Models\Main\Users\Users::has_comments_access('create')) {
            echo \Helpers\App::ajax_modal_template(\K::$fw->TEXT_WARNING, \K::$fw->TEXT_NO_ACCESS);
        } else {
            \K::$fw->entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->current_entity_id);

            \K::$fw->header_menu_button = '';

            //add templates menu in header
            if (class_exists('comments_templates')) {
                \K::$fw->header_menu_button = comments_templates::render_modal_header_menu(\K::$fw->current_entity_id);
            }

            \K::$fw->obj = \K::model()->db_find('app_comments', \K::$fw->GET['id']);

            //reply to comment
            if (isset(\K::$fw->GET['reply_to'])) {
                $reply_to_obj = \K::model()->db_find('app_comments', \K::$fw->GET['reply_to']);
                if (\K::$fw->entity_cfg->get('use_editor_in_comments') == 1) {
                    \K::$fw->obj['description'] = '<blockquote>' . $reply_to_obj['description'] . '</blockquote>' . "\n";
                } else {
                    \K::$fw->obj['description'] = $reply_to_obj['description'] . "\n";
                }
            }

            if (isset(\K::$fw->GET['description'])) {
                \K::$fw->obj['description'] = \K::model()->db_prepare_input(\K::$fw->GET['description']);
            }

            \K::$fw->app_items_form_name = 'comments_form';

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'comments_form.php';

            echo \K::view()->render(\K::$fw->subTemplate);
        }
    }
}