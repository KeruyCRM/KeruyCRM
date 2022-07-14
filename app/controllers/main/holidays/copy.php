<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Holidays;

class Copy extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Holidays\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'copy.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function copy_selected()
    {
        if (\K::$fw->VERB == 'POST') {
            $selected_items = \K::$fw->POST['selected_items'] ?? '';
            $year = \K::$fw->POST['year'];

            foreach (explode(',', $selected_items) as $id) {
                //$holiday_query = db_query("select * from app_holidays where id={$id}");
                $holiday = \K::model()->db_fetch_one('app_holidays', ['id = ?' => $id]);

                if ($holiday) {
                    $sql_data = [
                        'name' => $holiday['name'],
                        'start_date' => $year . substr($holiday['start_date'], 4),
                        'end_date' => $year . substr($holiday['end_date'], 4),
                    ];

                    \K::model()->db_perform('app_holidays', $sql_data);
                }
            }

            \K::$fw->holidays_filter = $year;

            \Helpers\Urls::redirect_to('main/holidays/holidays');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}