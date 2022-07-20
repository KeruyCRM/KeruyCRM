<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

//tabs
echo \K::$fw->render_dashboard_tabs;

//dashboard pages
echo \K::$fw->render_info_blocks;
echo \K::$fw->render_info_pages;

//counters
echo \K::$fw->html;

//include sections
//require(component_path('dashboard/sections'));
echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/sections'));

foreach (\K::$fw->reports as $reports) {
    if ($reports['check']) {
        echo '
			<div class="row">
        <div class="col-md-12"><h3 class="page-title"><a href="' . \Helpers\Urls::url_for(
                'main/reports/view',
                'reports_id=' . $reports['id']
            ) . '">' . $reports['name'] . '</a></h3></div>
      </div>
			<div class="alert alert-warning">' . \K::$fw->TEXT_REPORT_ALREADY_ASSIGNED . '</div>';
    } else {
        //require(component_path('dashboard/render_standard_reports'));
        echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/render_standard_reports'));
    }

    \K::$fw->has_reports_on_dashboard = true;
}

//include common reports
//require(component_path('dashboard/common_reports'));
echo \K::view()->render(\Helpers\Urls::component_path('main/dashboard/common_reports'));

//display default dashboard msg
if (!\K::$fw->has_reports_on_dashboard and \K::$fw->app_user['group_id'] == 0) {
    echo \K::$fw->TEXT_DASHBOARD_DEFAULT_ADMIN_MSG;
} elseif (!\K::$fw->has_reports_on_dashboard) {
    echo \K::$fw->TEXT_DASHBOARD_DEFAULT_MSG;
}

//require(component_path('items/load_items_listing.js'));
//echo \K::view()->render(\Helpers\Urls::component_path('main/items/load_items_listing.js'));