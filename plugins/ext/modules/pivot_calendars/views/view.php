<h3 class="page-title"><?php
    echo $reports['name'] . icalendar::get_url($reports['enable_ical'], 'pivot_report', $reports['id']) ?></h3>

<?php
require(component_path('ext/pivot_calendars/report')); ?>
