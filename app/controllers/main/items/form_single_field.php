<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Items;

class Form_single_field extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Items\_Module::top();

        //\K::$fw->field_info_query = db_query("select * from app_fields where id='" . _GET('field_id') . "'");

        \K::$fw->field_info = \K::model()->db_fetch_one('app_fields', [
            'id = ?',
            \K::$fw->GET['field_id']
        ]);

        if (!\K::$fw->field_info) {
            \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
        }

        /*$item_info_query = db_query(
            "select * from app_entity_{\K::$fw->current_entity_id} where id='" . \K::$fw->current_item_id . "'"
        );*/

        \K::$fw->obj = \K::model()->db_fetch_one('app_entity_' . (int)\K::$fw->current_entity_id, [
            'id = ?',
            \K::$fw->current_item_id
        ]);

        if (!\K::$fw->obj) {
            \Helpers\Urls::redirect_to('main/dashboard/page_not_found');
        }
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'form_single_field.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::$fw->app_changed_fields = [];

            $item_info = \K::$fw->obj;

            $choices_values = new \Models\Main\Choices_values(\K::$fw->current_entity_id);

            //submitted field value
            $value = (\K::$fw->POST['fields'][\K::$fw->field_info['id']] ?? '');

            //current field value
            $current_field_value = (\K::$fw->obj['field_' . \K::$fw->field_info['id']] ?? '');

            //prepare process options
            $process_options = [
                'class' => \K::$fw->field_info['type'],
                'value' => $value,
                'field' => \K::$fw->field_info,
                'is_new_item' => false,
                'current_field_value' => $current_field_value,
                'item' => $item_info,
            ];

            \K::model()->begin();

            $sql_data['field_' . \K::$fw->field_info['id']] = \Models\Main\Fields_types::process($process_options);

            //prepare choices values for fields with multiple values
            $choices_values->prepare($process_options);

            //update item
            $sql_data['date_updated'] = time();
            \K::model()->db_perform('app_entity_' . (int)\K::$fw->current_entity_id, $sql_data, [
                'id = ?',
                \K::$fw->current_item_id
            ]);
            $item_id = \K::$fw->current_item_id;

            //insert choices values for fields with multiple values
            $choices_values->process($item_id);

            //autoupdate all field types
            \Models\Main\Fields_types::update_items_fields(\K::$fw->current_entity_id, $item_id);

            //atuocreate comments if fields changed
            if (count(\K::$fw->app_changed_fields)) {
                \Models\Main\Comments::add_comment_notify_when_fields_changed(
                    \K::$fw->current_entity_id,
                    $item_id,
                    \K::$fw->app_changed_fields
                );
            }

            if (\Helpers\App::is_ext_installed()) {
                //run actions after item update
                $processes = new processes(\K::$fw->current_entity_id);
                $processes->run_after_update($item_id);

                //check public form notification
                //using $item_info as item with previous values
                public_forms::send_client_notification(\K::$fw->current_entity_id, $item_info);

                //sending sms
                $modules = new modules('sms');
                $sms = new sms(\K::$fw->current_entity_id, $item_id);
                $sms->send_edit_msg($item_info);

                //subscribe
                $modules = new modules('mailing');
                $mailing = new mailing(\K::$fw->current_entity_id, $item_id);
                $mailing->update($item_info);

                //email rules
                $email_rules = new email_rules(\K::$fw->current_entity_id, $item_id);
                $email_rules->send_edit_msg($item_info);
            }

            \K::model()->commit();
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}