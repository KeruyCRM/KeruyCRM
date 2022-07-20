<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

if (!\K::fw()->exists('GET.id', $reports_groups_id)) {
    $reports_groups_id = 0;
};

$sections_query = \K::model()->db_fetch(
    'app_reports_sections',
    [
        'reports_groups_id = :reports_groups_id' . ($reports_groups_id == 0 ? ' and created_by = :created_by' : ''),
        ':reports_groups_id' => $reports_groups_id
    ] + ($reports_groups_id == 0 ? [':created_by' => \K::$fw->app_user['id']] : []),
    ['order' => 'sort_order']
);

//while ($sections = db_fetch_array($sections_query)) {
foreach ($sections_query as $sections) {
    \K::$fw->sections = $sections->cast();
    if (\K::$fw->sections['count_columns'] == 2) {
        echo '
			<div class="row">
				<div class="col-md-6">	
			';

        \K::$fw->section_report = \K::$fw->sections['report_left'];
        echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/sections_reports'));

        echo '
			</div>
			<div class="col-md-6">';

        \K::$fw->section_report = \K::$fw->sections['report_right'];
        echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/sections_reports'));

        echo '
			</div>
			</div>';
    } else {
        echo '
			<div class="row">
                            <div class="col-md-12">	
			';

        \K::$fw->section_report = \K::$fw->sections['report_left'];
        echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/sections_reports'));

        echo '
                            </div>
			
			</div>';
    }

    \K::$fw->has_reports_on_dashboard = true;
}