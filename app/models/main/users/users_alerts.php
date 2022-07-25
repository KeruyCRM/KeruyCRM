<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Models\Main\Users;

class Users_alerts
{
    public static function get_types_choices()
    {
        return [
            'warning' => \K::$fw->TEXT_ALERT_WARNING,
            'danger' => \K::$fw->TEXT_ALERT_DANGER,
            'success' => \K::$fw->TEXT_ALERT_SUCCESS,
            'info' => \K::$fw->TEXT_ALERT_INFO,
        ];
    }

    public static function get_type_by_name($name)
    {
        $types = self::get_types_choices();

        return ($types[$name] ?? '');
    }

    public static function get_location_choices()
    {
        return [
            'all' => \K::$fw->TEXT_LOCATION_ON_ALL_PAGES,
            'dashboard' => \K::$fw->TEXT_LOCATION_ON_DASHBOARD,
        ];
    }

    public static function output()
    {
        $where_sql = " and ((FROM_UNIXTIME(ua.start_date,'%Y-%m-%d') <= date_format(now(),'%Y-%m-%d') or ua.start_date = 0) and (FROM_UNIXTIME(ua.end_date,'%Y-%m-%d') >= date_format(now(),'%Y-%m-%d') or ua.end_date = 0))";

        if (\K::$fw->app_module_path == 'dashboard/dashboard') {
            $where_sql .= " and (ua.location = 'dashboard' or ua.location = 'all')";
        } else {
            $where_sql .= " and ua.location != 'dashboard'";
        }

        $where_sql .= ' and ua.id not in (select uav.alerts_id from app_users_alerts_viewed uav where uav.users_id = ' . \K::model(
            )->quote(\K::$fw->app_user['id']) . ')';

        $html = '';

        $alerts_query = \K::model()->db_query_exec(
            "select * from app_users_alerts ua where ua.is_active = 1 and ((length(ua.users_groups) = 0 and length(ua.assigned_to) = 0) or (find_in_set( ? ,ua.users_groups) or find_in_set( ? ,ua.assigned_to)) ) {$where_sql} order by ua.id desc",
            [
                \K::$fw->app_user['group_id'],
                \K::$fw->app_user['id']
            ]
        );
        //while ($alerts = db_fetch_array($alerts_query)) {
        foreach ($alerts_query as $alerts) {
            $html .= '
				<div class="alert alert-' . $alerts['type'] . '"><button type="button" class="close users-alers-close" data-id="' . $alerts['id'] . '" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4>' . $alerts['title'] . '</h4>' . $alerts['description'] . '</div>		
			';
        }

        $html .= '
			<script>
				$(function(){
					$(".users-alers-close").click(function(){
						id = $(this).attr("data-id")
						$.ajax({method:"POST",url:"' . \Helpers\Urls::url_for(
                'main/dashboard/dashboard/set_users_alerts_viewed'
            ) . '",data:{id:id}})
					})
				})	
			</script>		
		';

        return $html;
    }
}