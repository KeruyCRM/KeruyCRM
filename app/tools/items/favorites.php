<?php

namespace Tools\Items;

class Favorites
{
    public function __construct($entities_id, $item_id)
    {
        $this->entities_id = $entities_id;
        $this->entity_cfg = new entities_cfg($entities_id);
        $this->item_id = $item_id;

        $this->is_in_list = $this->is_in_list();
    }

    public function render_icon()
    {
        if ($this->entity_cfg->get('enable_favorites') != 1) {
            return '';
        }

        $html = '<a href="#" class="favorite-icon ' . ($this->is_in_list ? 'active' : '') . '" data_path="' . $this->entities_id . '-' . $this->item_id . '">
                    <i class="fa ' . ($this->is_in_list ? 'fa-star' : 'fa-star-o') . '" aria-hidden="true"></i>
                </a>';

        return $html;
    }

    public function is_in_list()
    {
        global $app_user;

        $check_query = db_query(
            "select id from app_favorites where users_id={$app_user['id']} and entities_id={$this->entities_id} and items_id={$this->item_id} limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            return true;
        } else {
            return false;
        }
    }

    public static function render_header_nofitifcation()
    {
        $html = '
        <li class="dropdown hot-reports" id="favorites_header_dropdown">
          ' . '
        </li>
		
        <script>
          function favorites_render_dropdown()
          {
            $("#favorites_header_dropdown").load("' . url_for("dashboard/", "action=update_favorites_header_dropdown") . '",function(){
                $(\'[data-hover="dropdown"]\').dropdownHover();
            		app_handle_scrollers();
              })
          }
		
          $(function(){
             favorites_render_dropdown()
          });
		          		
        </script>
      ';

        return $html;
    }

    public static function count()
    {
        global $app_user;

        $favorites_query = db_query(
            "select count(*) as total from app_favorites f, app_entities e where e.id = f.entities_id and f.users_id={$app_user['id']} order by e.name, f.id"
        );
        $favorites = db_fetch_array($favorites_query);

        return $favorites['total'];
    }

    public static function delete_by_item_id($entities_id, $items_id)
    {
        db_query("delete from app_favorites where entities_id={$entities_id} and items_id={$items_id}");
    }

    public static function render_header_dropdown()
    {
        global $app_user, $app_entities_cache;

        $items_html = '';

        $favorites_query = db_query(
            "select f.*, e.name as entity_name from app_favorites f, app_entities e where e.id = f.entities_id and f.users_id={$app_user['id']} order by e.name, f.id limit 10"
        );
        $count_favorites = db_num_rows($favorites_query);

        if (!$count_favorites) {
            return '';
        }

        while ($favorites = db_fetch_array($favorites_query)) {
            $items_html .= '
                <li>
                    <a href="' . url_for(
                    'items/info',
                    'path=' . $favorites['entities_id'] . '-' . $favorites['items_id']
                ) . '">' . items::get_heading_field(
                    $favorites['entities_id'],
                    $favorites['items_id']
                ) . ' <span class="parent-name"><i class="fa fa-angle-left"></i> ' . $favorites['entity_name'] . '</span></a>
  		</li>';
        }

        $dropdown_menu_height = ($count_favorites < 11 ? ($count_favorites * 42 + 42) : 420);

        $external_html = '
            <li class="external">
                <a href="' . url_for(
                'users/favorites'
            ) . '">' . \K::$fw->TEXT_DISPLAYED . ': ' . $count_favorites . '. ' . \K::$fw->TEXT_GO_TO . ' "' . \K::$fw->TEXT_FAVORITES . '"<i class="fa fa-angle-right"></i></a>
            </li>
        ';

        $badge_html = ($count_favorites > 0 ? '<span class="badge badge-warning">' . self::count() . '</span>' : '');

        $html = '
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
              <i class="fa fa-star"></i>
              ' . $badge_html . '
            </a>
            <ul class="dropdown-menu extended tasks">
                <li style="cursor:pointer" onClick="location.href=\'' . url_for('users/favorites') . '\'">
                    <p>' . \K::$fw->TEXT_FAVORITES . '</p>
                </li>
                <li>
                    <ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
                        ' . $items_html . '
                        ' . $external_html . '  
                    </ul>
                </li>

            </ul>            
        ';

        return $html;
    }
}