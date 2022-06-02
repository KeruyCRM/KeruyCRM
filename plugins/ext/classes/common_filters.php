<?php

class common_filters
{

    public $entities_id, $reports_id, $parent_item_id;

    function __construct($entities_id, $reports_id)
    {
        global $app_path;

        $this->entities_id = $entities_id;
        $this->reports_id = $reports_id;
        $this->parent_item_id = 0;

        $this->redirect_to = (strlen($app_path) > 0 ? 'listing' : 'report');
    }

    function render($title)
    {
        global $app_path, $app_current_users_filter, $app_user;

        $reports_query = db_query(
            "select id, name, dashboard_counter_hide_count from app_reports where entities_id='" . $this->entities_id . "' and reports_type='common_filters' and in_dashboard_counter=0 and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) order by dashboard_sort_order"
        );

        $html = '<h3 class="page-title">' . $title . '</h3>';

        if (db_num_rows($reports_query)) {
            $html = '
                <nav class="navbar navbar-default">
                    <div class="container-fluid">

                        <div class="navbar-header">
                          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-common_filters">
                            <span class="sr-only"></span>
                            <span class="fa fa-bar "></span>
                            <span class="fa fa-bar fa-align-justify"></span>
                            <span class="fa fa-bar"></span>
                            </button>
                          <div class="navbar-brand navbar-common-filters pointer" data_url="' . url_for(
                    'reports/common_filters',
                    'action=reset&redirect_to=' . $this->redirect_to . '&reports_id=' . $this->reports_id . (strlen(
                        $app_path
                    ) ? '&path=' . $app_path : '')
                ) . '">' . $title . '</div>
                        </div>
                            <div class="collapse navbar-collapse" id="navbar-common_filters">
                    <ul class="nav navbar-nav">';

            $script = '
                $(".navbar-common-filters").click(function(e){
                    url = $(this).attr("data_url")
                    
                    if(url && e.target.tagName.toLowerCase()=="div")
                    {
                        location.href = url
                    }
                })
                ';

            while ($reports = db_fetch_array($reports_query)) {
                $is_selected = false;

                if (isset($app_current_users_filter[$this->reports_id])) {
                    $is_selected = ($app_current_users_filter[$this->reports_id] == $reports['name'] ? true : false);
                }


                $html .= '<li class="common-filter-' . $reports['id'] . ' ' . ($is_selected ? 'selected' : '') . '"><a href="' . url_for(
                        'reports/common_filters',
                        'action=use&redirect_to=' . $this->redirect_to . '&reports_id=' . $this->reports_id . '&use_filters=' . $reports['id'] . (strlen(
                            $app_path
                        ) ? '&path=' . $app_path : '')
                    ) . '">' . $reports['name'] . '<span id="common_filters_' . $reports['id'] . '_count"></span></a></li>';

                if ($reports['dashboard_counter_hide_count'] != 1) {
                    $script .= '
                            $("#common_filters_' . $reports['id'] . '_count").load("' . url_for(
                            "dashboard/common_filters_count",
                            "reports_id=" . $reports['id'] . '&parent_item_id=' . $this->parent_item_id
                        ) . '");
                        ';
                }
            }

            $html .= '
								</ul>
					    </div>
					  </div>
					</nav>
					';

            $html .= '<script>' . $script . '</script>';
        }

        $reports_counter = new reports_counter();
        $reports_counter->redirect_to = $this->redirect_to;
        $reports_counter->titile = '';
        $reports_counter->common_filter_reports_id = $this->reports_id;
        $reports_counter->parent_item_id = $this->parent_item_id;
        $reports_counter->reports_query = "select * from app_reports where entities_id='" . $this->entities_id . "' and reports_type='common_filters' and in_dashboard_counter=1 and (length(users_groups)=0 or find_in_set(" . $app_user['group_id'] . ",users_groups)) order by dashboard_sort_order";
        $html .= $reports_counter->render();

        return $html;
    }

}
