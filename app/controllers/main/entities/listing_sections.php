<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Listing_sections extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();

        if (!\K::$fw->GET['entities_id']) {
            \Helpers\Urls::redirect_to('main/entities');//FIX
        }

        /*$listing_types_query = db_query(
            "select * from app_listing_types where id='" . \K::$fw->GET['listing_types_id'] . "'"
        );*/

        \K::$fw->listing_types = \K::model()->db_fetch_one('app_listing_types', [
            'id = ?',
            \K::$fw->GET['listing_types_id']
        ]);

        if (!\K::$fw->listing_types) {
            \Helpers\Urls::redirect_to('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']);
        }
    }

    public function index()
    {
        \K::$fw->align_choices = \Models\Main\Listing_types::get_sections_align_choices();

        /*$filters_query = db_query(
            "select * from app_listing_sections where listing_types_id='" . db_input(
                \K::$fw->listing_types['id']
            ) . "' order by sort_order, name"
        );*/

        \K::$fw->filters_query = \K::model()->db_fetch('app_listing_sections', [
            'listing_types_id = ?',
            \K::$fw->listing_types['id']
        ], ['order' => 'sort_order,name']);

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'listing_sections.php';

        echo \K::view()->render(\K::$fw->app_layout);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'listing_types_id' => \K::$fw->listing_types['id'],
                'name' => \K::$fw->POST['name'],
                'fields' => (isset(\K::$fw->POST['fields']) ? implode(',', \K::$fw->POST['fields']) : ''),
                'display_field_names' => (isset(\K::$fw->POST['display_field_names']) ? 1 : 0),
                'sort_order' => \K::$fw->POST['sort_order'],
                'text_align' => \K::$fw->POST['text_align'],
                'display_as' => \K::$fw->POST['display_as'],
                'width' => (\K::$fw->POST['width'] ?? ''),
            ];

            /*if (isset(\K::$fw->GET['id'])) {
                db_perform('app_listing_sections', $sql_data, 'update', "id='" . db_input(\K::$fw->GET['id']) . "'");
            } else {
                db_perform('app_listing_sections', $sql_data);
            }*/

            \K::model()->db_perform('app_listing_sections', $sql_data, [
                'id = ?',
                \K::$fw->GET['id']
            ]);

            \Helpers\Urls::redirect_to(
                'main/entities/listing_sections',
                'listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->VERB == 'POST' and isset(\K::$fw->GET['id'])) {
            \K::model()->db_delete_row('app_listing_sections', \K::$fw->GET['id']);

            \Helpers\Urls::redirect_to(
                'main/entities/listing_sections',
                'listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function sort()
    {
        if (\K::$fw->VERB == 'POST') {
            $choices_sorted = \K::$fw->POST['choices_sorted'];

            if (strlen($choices_sorted) > 0) {
                $choices_sorted = json_decode(stripslashes($choices_sorted), true);

                $sort_order = 1;

                \K::model()->begin();

                foreach ($choices_sorted as $v) {
                    $sql_data = ['sort_order' => $sort_order];

                    //db_query("update app_listing_sections set sort_order={$sort_order} where id={$v['id']}");

                    \K::model()->db_update('app_listing_sections', $sql_data, [
                        'id = ?',
                        $v['id']
                    ]);

                    $sort_order++;
                }

                \K::model()->commit();
            }

            \Helpers\Urls::redirect_to(
                'main/entities/listing_sections',
                'listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
            );
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}