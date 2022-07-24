<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Tools;

class Users_login_log extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Tools\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'users_login_log.php';

        echo \K::view()->render($this->app_layout);
    }

    public function reset()
    {
        if (\K::security()->checkCsrfTokenUrl()) {
            //db_query("delete from app_users_login_log");
            \K::model()->db_delete('app_users_login_log');

            \Helpers\Urls::redirect_to('main/tools/users_login_log');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function listing()
    {
        $html = '
			<div class="table-scrollable">
				<table class="table table-striped table-bordered table-hover">
				<thead>
				  <tr>
						<th>' . \K::$fw->TEXT_TYPE . '</th>
						<th>' . \K::$fw->TEXT_DATE_ADDED . '</th>
						<th>' . \K::$fw->TEXT_IP . '</th>		
						<th>' . \K::$fw->TEXT_USERNAME . '</th>										    				    
						<th width="100%">' . \K::$fw->TEXT_NAME . '</th>
						<th>' . \K::$fw->TEXT_USERS_GROUPS . '</th>
						<th>' . \K::$fw->TEXT_EMAIL . '</th>
				  </tr>
				</thead>
				<tbody>
		';

        $where_sql = '';
        $where_value = [];

        foreach (\K::$fw->POST['filters'] as $filter) {
            if (strlen($filter['value']) > 0) {
                switch ($filter['name']) {
                    case 'type':
                        $where_sql .= " and is_success = :type";
                        $where_value[':type'] = $filter['value'];
                        break;
                    case 'users_id':
                        $where_sql .= " and users_id = :users_id";
                        $where_value[':users_id'] = $filter['value'];
                        break;
                }
            }
        }

        //$listing_sql = "select * from app_users_login_log where id > 0 {$where_sql} order by date_added desc";

        $listing_sql = \Tools\Split_page::makeQuery(
            'app_users_login_log',
            [
                'id > 0 ' . $where_sql,
            ] + $where_value,
            ['order' => 'date_added desc']
        );

        $listing_split = new \Tools\Split_page(
            $listing_sql,
            'users_login_log_listing',
            '',
            \K::$fw->CFG_APP_ROWS_PER_PAGE
        );
        //$items_query = db_query($listing_split->sql_query);
        $items_query = \K::model()->db_fetch_split(
            $listing_split->sql_query()
        );

        //while ($item = db_fetch_array($items_query)) {
        foreach ($items_query as $item) {
            $item = $item->cast();

            $html .= '
							<tr>
							  <td>' . ($item['is_success'] == 1 ? '<span class="label label-success">' . \K::$fw->TEXT_SUCCESSFUL_LOGIN . '</span>' : '<span class="label label-warning">' . \K::$fw->TEXT_LOGIN_ATTEMPT . '</span>') . '</td>			
							  <td>' . \Helpers\App::format_date_time($item['date_added']) . '</td>
								<td>' . $item['identifier'] . '</td>
								<td>' . htmlspecialchars($item['username']) . '</td>
								<td>' . (isset(\K::$fw->app_users_cache[$item['users_id']]) ? \K::$fw->app_users_cache[$item['users_id']]['name'] : '') . '</td>		
								<td>' . (isset(\K::$fw->app_users_cache[$item['users_id']]) ? (\K::$fw->app_users_cache[$item['users_id']]['group_id'] > 0 ? \K::$fw->app_users_cache[$item['users_id']]['group_name'] : \K::$fw->TEXT_ADMINISTRATOR) : '') . '</td>
								<td>' . (isset(\K::$fw->app_users_cache[$item['users_id']]) ? \K::$fw->app_users_cache[$item['users_id']]['email'] : '') . '</td>
							</tr>
					';
        }

        if ($listing_split->number_of_rows() == 0) {
            $html .= '
				    <tr>
				      <td colspan="7">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td>
				    </tr>
				  ';
        }

        $html .= '
		  </tbody>
		</table>
		</div>
		';

        //add pager
        $html .= '
		  <table width="100%">
		    <tr>
		      <td>' . $listing_split->display_count() . '</td>
		      <td align="right">' . $listing_split->display_links() . '</td>
		    </tr>
		  </table>
		';

        echo $html;
    }
}