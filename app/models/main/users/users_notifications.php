<?php

namespace Models\Main\Users;

class Users_notifications
{
    public $unread_items;

    public function __construct($entities_id)
    {
        global $app_user;

        $this->unread_items = [];

        $items_query = db_query(
            "select * from app_users_notifications where users_id='" . $app_user['id'] . "' and entities_id='" . $entities_id . "'"
        );
        while ($items = db_fetch_array($items_query)) {
            $this->unread_items[] = $items['items_id'];
        }
    }

    public function has($items_id)
    {
        //global $app_users_cfg;

        //don't highlight if configuration disabled
        if (\K::app_users_cfg()->get('disable_highlight_unread') == 1) {
            return false;
        }

        return in_array($items_id, $this->unread_items);
    }

    public static function add($name, $type, $users_id, $entities_id, $items_id)
    {
        //skip user with disabled notification
        if (\Models\Main\Users\Users_cfg::get_value_by_users_id($users_id, 'disable_internal_notification') == 1) {
            return false;
        }

        //skip current user
        if (\K::$fw->app_user['id'] == $users_id) {
            return false;
        }

        $sql_data = [
            'users_id' => $users_id,
            'entities_id' => $entities_id,
            'items_id' => $items_id,
            'name' => $name,
            'type' => $type,
            'date_added' => time(),
            'created_by' => \K::$fw->app_user['id'],
        ];

        \K::model()->db_perform('app_users_notifications', $sql_data);
    }

    public static function reset($entities_id, $items_id)
    {
        global $app_user;

        db_query(
            "delete from app_users_notifications where users_id='" . $app_user['id'] . "' and entities_id='" . $entities_id . "' and items_id='" . $items_id . "'"
        );
    }

    public static function render()
    {
        //global $app_users_cfg;

        //skip menu with disabled notification
        if (\K::app_users_cfg()->get('disable_internal_notification') == 1) {
            return false;
        }

        $html = '
        <li class="dropdown hot-reports" id="user_notifications_report">
          ' . '
        </li>
		
        <script>
          function user_notifications_report_render_dropdown()
          {
            $("#user_notifications_report").load("' . \Helpers\Urls::url_for(
                "main/dashboard/dashboard/update_user_notifications_report"
            ) . '",function(){
                $(\'[data-hover="dropdown"]\').dropdownHover();
            		app_handle_scrollers();
              })
          }
		
          $(function(){
             setInterval(function(){
              user_notifications_report_render_dropdown()
             },60000);
             
            user_notifications_report_render_dropdown()
          });
		
          
		
        </script>
      ';

        return $html;
    }

    public static function render_dropdown()
    {
        $popup_items_limit = 10;
        $items_display_count = 0;

        $items_html = '';

        /* $items_query = db_query(
             "select * from app_users_notifications where users_id='" . \K::$fw->app_user['id'] . "' order by id desc limit " . $popup_items_limit
         );*/

        $items_query = \K::model()->db_fetch('app_users_notifications', [
            'users_id = ?',
            \K::$fw->app_user['id']
        ], [
            'order' => 'id desc',
            'limit' => $popup_items_limit
        ], 'entities_id,items_id,type,name,created_by');

        //while ($items = db_fetch_array($items_query)) {
        foreach ($items_query as $items) {
            $path_info =  \Models\Main\Items\Items::get_path_info($items['entities_id'], $items['items_id']);

            $items_html .= '
          <li>
  					<a href="' . \Helpers\Urls::url_for(
                    'main/items/info',
                    'path=' . $path_info['full_path']
                ) . '">' . self::render_icon_by_type(
                    $items['type']
                ) . ' ' . $items['name'] . ' <span class="parent-name"><i class="fa fa-angle-left"></i>' . (isset(\K::$fw->app_users_cache[$items['created_by']]) ? \K::$fw->app_users_cache[$items['created_by']]['name'] : '') . '</span></a>
  				</li>
        ';

            $items_display_count++;
        }

        $items_count = \K::model()->db_count('app_users_notifications', \K::$fw->app_user['id'], 'users_id');

        if ($items_count == 0) {
            $items_html .= '
          <li>
  					<a onClick="return false;">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</a>
  				</li>
        ';
        }

        $dropdown_menu_height = ($items_display_count < 11 ? ($items_display_count * 42 + 42) : 420);

        $external_html = '';
        if ($items_count > 0) {
            $external_html = '
          <li class="external">
						<a href="' . \Helpers\Urls::url_for('main/users/notifications') . '">' . sprintf(
                    \K::$fw->TEXT_DISPLAY_NUMBER_OF_ITEMS_OPEN_REPORT,
                    $items_display_count
                ) . '</a>
					</li>
        ';
        }

        $badge_html = ($items_count > 0 ? '<span class="badge badge-warning">' . $items_count . '</span>' : '');

        return '
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				  <i class="fa fa-bell-o"></i>
				  ' . $badge_html . '
				</a>
				<ul class="dropdown-menu extended tasks">
					<li style="cursor:pointer" onClick="location.href=\'' . \Helpers\Urls::url_for(
                'main/users/notifications'
            ) . '\'">
						<p>' . \K::$fw->TEXT_USERS_NOTIFICATIONS . '</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
							' . $items_html . '
              ' . $external_html . '  
						</ul>
					</li>
          
				</ul>            
      ';
    }

    public static function render_icon_by_type($type)
    {
        $html = '';

        switch ($type) {
            case 'new_item':
                $html = '<i class="fa fa-bell-o" aria-hidden="true"></i>';
                break;
            case 'new_comment':
                $html = '<i class="fa fa-comment-o" aria-hidden="true"></i>';
                break;
            case 'updated_item':
                $html = '<i class="fa fa-refresh" aria-hidden="true"></i>';
                break;
        }

        return $html;
    }
}