<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Holidays;

class Holidays extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Holidays\_Module::top();

        if (!\K::app_session_is_registered('holidays_filter')) {
            \K::$fw->holidays_filter = date('Y');
            \K::app_session_register('holidays_filter');
        }
    }

    public function index()
    {
        \K::$fw->choices = \Models\Main\Holidays::get_year_choices();

        \K::$fw->groups_query = \K::model()->db_fetch(
            'app_holidays',
            [
                'year(start_date) = ?',
                \K::$fw->holidays_filter
            ],
            ['order' => 'start_date desc']
        );

        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'holidays.php';

        echo \K::view()->render($this->app_layout);
    }

    public function set_holidays_filter()
    {
        if (\K::$fw->VERB == 'POST') {
            \K::$fw->holidays_filter = \K::$fw->POST['holidays_filter'];

            \Helpers\Urls::redirect_to('main/holidays/holidays');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $sql_data = [
                'name' => \K::$fw->POST['name'],
                'start_date' => \K::$fw->POST['start_date'],
                'end_date' => \K::$fw->POST['end_date'],
            ];

            if (\K::fw()->exists('GET.id')) {
                \K::model()->db_perform('app_holidays', $sql_data, ['id = ?', \K::$fw->GET['id']]);
            } else {
                \K::model()->db_perform('app_holidays', $sql_data);
            }

            \Helpers\Urls::redirect_to('main/holidays/holidays');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function delete()
    {
        if (\K::$fw->GET['id'] and \K::$fw->VERB == 'POST') {
            \K::model()->db_delete_row('app_holidays', \K::$fw->GET['id']);

            \Helpers\Urls::redirect_to('main/holidays/holidays');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}