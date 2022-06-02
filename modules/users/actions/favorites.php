<?php

switch ($app_module_action) {
    case 'reset':

        db_query("delete from app_favorites where users_id={$app_user['id']}");

        redirect_to('users/favorites');
        break;
    case 'get_listing':

        $html = '
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                    <thead>  
                      <tr>	
                        <th></th>
                        <th>' . TEXT_ENTITY . '</th>
                        <th width="100%">' . TEXT_TITLE . '</th>                        
                      </tr>
                    </thead>
                    <tbody> 		
        ';


        $listing_sql = "select f.*, e.name as entity_name from app_favorites f, app_entities e where e.id = f.entities_id and f.users_id={$app_user['id']} order by e.name, f.id";
        $listing_split = new split_page($listing_sql, 'favorites_listing');
        $favorites_query = db_query($listing_split->sql_query);
        while ($favorites = db_fetch_array($favorites_query)) {
            $html .= '
                    <tr>
                            <td><a href="#" class="favorite-icon active" data_path="' . $favorites['entities_id'] . '-' . $favorites['items_id'] . '" data_page="' . $listing_split->current_page_number . '">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                </a></td>
                            <td>' . $favorites['entity_name'] . '</td>
                            <td><a href="' . url_for(
                    'items/info',
                    'path=' . $favorites['entities_id'] . '-' . $favorites['items_id']
                ) . '">' . items::get_heading_field($favorites['entities_id'], $favorites['items_id']) . '</a></td>
                    </tr>
                ';
        }


        if ($listing_split->number_of_rows == 0) {
            $html .= '
                <tr>
                  <td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td>
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

        exit();
        break;
}
