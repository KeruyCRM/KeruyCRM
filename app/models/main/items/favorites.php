<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Models\Main\Items;

class Favorites
{
    public $entities_id;
    public $entity_cfg;
    public $item_id;
    public $is_in_list;

    public function __construct($entities_id, $item_id)
    {
        $this->entities_id = $entities_id;
        $this->entity_cfg = new \Models\Main\Entities_cfg($entities_id);
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
        $check = \K::model()->db_fetch_one('app_favorites', [
            'users_id = ? and entities_id = ? and items_id = ?',
            \K::$fw->app_user['id'],
            $this->entities_id,
            $this->item_id
        ], ['limit' => 1], 'id');
        if ($check) {
            return true;
        } else {
            return false;
        }
    }

    public static function render_header_nofitifcation()
    {
        return '
        <li class="dropdown hot-reports" id="favorites_header_dropdown">
          ' . '
        </li>
		
        <script>
          function favorites_render_dropdown()
          {
            $("#favorites_header_dropdown").load("' . \Helpers\Urls::url_for(
                "main/dashboard/dashboard/update_favorites_header_dropdown"
            ) . '",function(){
                $(\'[data-hover="dropdown"]\').dropdownHover();
            		app_handle_scrollers();
              })
          }
		
          $(function(){
             favorites_render_dropdown()
          });
		          		
        </script>
      ';
    }

    public static function count()
    {
        $favorites = \K::model()->db_query_exec_one(
            'select count(*) as total from app_favorites f, app_entities e where e.id = f.entities_id and f.users_id = ? order by e.name, f.id',
            [\K::$fw->app_user['id']]
        );

        //$favorites = $favorites_query[0];

        return $favorites['total'];
    }

    public static function delete_by_item_id($entities_id, $items_id)
    {
        \K::model()->db_delete('app_favorites', [
            'entities_id = ? and items_id = ?',
            $entities_id,
            $items_id
        ]);
    }

    public static function render_header_dropdown()
    {
        $items_html = '';

        $favorites_query = \K::model()->db_query_exec(
            'select f.*, e.name as entity_name from app_favorites f, app_entities e where e.id = f.entities_id and f.users_id = ? order by e.name, f.id limit 10',
            [\K::$fw->app_user['id']],
            'app_favorites,app_entities'
        );

        $count_favorites = count($favorites_query);

        if (!$count_favorites) {
            return '';
        }

        //while ($favorites = db_fetch_array($favorites_query)) {
        foreach ($favorites_query as $favorites) {
            $items_html .= '
                <li>
                    <a href="' . \Helpers\Urls::url_for(
                    'main/items/info',
                    'path=' . $favorites['entities_id'] . '-' . $favorites['items_id']
                ) . '">' . \Models\Main\Items\Items::get_heading_field(
                    $favorites['entities_id'],
                    $favorites['items_id']
                ) . ' <span class="parent-name"><i class="fa fa-angle-left"></i> ' . $favorites['entity_name'] . '</span></a>
  		</li>';
        }

        $dropdown_menu_height = ($count_favorites < 11 ? ($count_favorites * 42 + 42) : 420);

        $external_html = '
            <li class="external">
                <a href="' . \Helpers\Urls::url_for(
                'users/favorites'
            ) . '">' . \K::$fw->TEXT_DISPLAYED . ': ' . $count_favorites . '. ' . \K::$fw->TEXT_GO_TO . ' "' . \K::$fw->TEXT_FAVORITES . '"<i class="fa fa-angle-right"></i></a>
            </li>
        ';

        $badge_html = ($count_favorites > 0 ? '<span class="badge badge-warning">' . self::count() . '</span>' : '');

        return '
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
              <i class="fa fa-star"></i>
              ' . $badge_html . '
            </a>
            <ul class="dropdown-menu extended tasks">
                <li style="cursor:pointer" onClick="location.href=\'' . \Helpers\Urls::url_for('main/users/favorites') . '\'">
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
    }
}