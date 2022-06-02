<?php

switch ($app_module_action) {
    case 'copy_selected':
        $selected_items = $_POST['selected_items'] ?? '';
        $year = $_POST['year'];

        foreach (explode(',', $selected_items) as $id) {
            $holiday_query = db_query("select * from app_holidays where id={$id}");
            if ($holiday = db_fetch_array($holiday_query)) {
                $sql_data = [
                    'name' => $holiday['name'],
                    'start_date' => $year . substr($holiday['start_date'], 4),
                    'end_date' => $year . substr($holiday['end_date'], 4),
                ];

                db_perform('app_holidays', $sql_data);
            }
        }

        $holidays_filter = $year;

        redirect_to('holidays/holidays');
        break;
}

