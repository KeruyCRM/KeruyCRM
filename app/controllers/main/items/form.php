<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Form extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();
    }

    public function index()
    {
        //checking access
        if (isset(\K::$fw->GET['id']) and !\Models\Main\Users\Users::has_access('update')) {
            echo \Helpers\App::ajax_modal_template(\K::$fw->TEXT_WARNING, \K::$fw->TEXT_NO_ACCESS);
        } elseif (!isset(\K::$fw->GET['id']) and (!\Models\Main\Users\Users::has_access(
                    'create'
                ) or !\Models\Main\Access_rules::has_add_buttons_access(
                    \K::$fw->current_entity_id,
                    \K::$fw->parent_entity_item_id
                ))) {
            echo \Helpers\App::ajax_modal_template(\K::$fw->TEXT_WARNING, \K::$fw->TEXT_NO_ACCESS);
        } else {
            \K::$fw->obj = \K::model()->db_find('app_entity_' . (int)\K::$fw->current_entity_id, \K::$fw->GET['id']);

            if (!isset(\K::$fw->GET['id'])) {
                //prepare start/end dates if add item from calendar report
                if (strstr(\K::$fw->app_redirect_to, 'calendarreport')) {
                    //require(component_path('items/items_form_calendar_report_prepare'));
                    echo \K::view()->render(
                        \Helpers\Urls::components_path('main/items/items_form_calendar_report_prepare')
                    );
                }

                //prepare start/end dates if add item from pivot calendar report
                if (strstr(\K::$fw->app_redirect_to, 'pivot_calendars')) {
                    //require(component_path('items/items_form_pivot_calendar_report_prepare'));
                    echo \K::view()->render(
                        \Helpers\Urls::components_path('main/items/items_form_pivot_calendar_report_prepare')
                    );
                }

                //prepare start/end dates if add item from resource timeline report
                if (strstr(\K::$fw->app_redirect_to, 'resource_timeline')) {
                    //require(component_path('ext/resource_timeline/items_form_prepare'));
                    echo \K::view()->render(\Helpers\Urls::components_path('ext/resource_timeline/items_form_prepare'));
                }

                //prepare start/end dates if add item from gantt report
                if (strstr(\K::$fw->app_redirect_to, 'ganttreport')) {
                    //require(component_path('items/items_form_gantt_report_prepare'));
                    echo \K::view()->render(
                        \Helpers\Urls::components_path('main/items/items_form_gantt_report_prepare')
                    );
                }

                //autofill related fields to mail
                if (isset(\K::$fw->GET['mail_groups_id'])) {
                    //require(component_path('ext/mail/auto_fill_fields'));
                    echo \K::view()->render(\Helpers\Urls::components_path('ext/mail/auto_fill_fields'));
                }

                //prepare subentity form
                if (strstr(\K::$fw->app_redirect_to, 'subentity_form')) { //TODO Not exist file?
                    //require(component_path('items/subentity_form_prepare'));
                    echo \K::view()->render(\Helpers\Urls::components_path('main/items/subentity_form_prepare'));
                }
            }

            \K::$fw->entity_cfg = new \Models\Main\Entities_cfg(\K::$fw->current_entity_id);

            \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'form.php';

            echo \K::view()->render(\K::$fw->app_layout);
        }
    }
}