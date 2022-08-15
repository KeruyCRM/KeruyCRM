<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Entities_change_structure extends \Controller
{
    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        \K::$fw->subTemplate = \K::$fw->pathSubTemplate . 'entities_change_structure.php';

        echo \K::view()->render(\K::$fw->subTemplate);
    }

    public function save()
    {
        if (\K::$fw->VERB == 'POST') {
            $entities_id = \K::$fw->POST['entities_id'];
            $move_to_entities_id = \K::$fw->POST['move_to_entities_id'];

            if (!$entities_id) {
                \Helpers\Urls::redirect_to('main/entities/entities');
            }

            \K::model()->begin();

            //reset reports type 'entity'
            //db_query("delete from app_reports where entities_id='" . $entities_id . "' and reports_type='entity'");

            \K::model()->db_delete('app_reports', [
                'entities_id = ? and reports_type = ?',
                $entities_id,
                'entity'
            ]);

            $entities_tree = [];
            $entities_tree[] = $entities_id;
            foreach (\Models\Main\Entities::get_tree($entities_id) as $v) {
                $entities_tree[] = $v['id'];
            }

            //check parent reports
            /*$reports_query = db_query(
                "select id, parent_id from app_reports where entities_id in (" . implode(
                    ',',
                    $entities_tree
                ) . ") and reports_type!='parent'"
            );*/

            $reports_query = \K::model()->db_fetch('app_reports', [
                'entities_id in (' . \K::model()->quoteToString(
                    $entities_tree,
                    \PDO::PARAM_INT
                ) . ') and reports_type != ?',
                'parent'
            ], [], 'id,parent_id');

            //while ($reports = db_fetch_array($reports_query)) {
            foreach ($reports_query as $reports) {
                $reports = $reports->cast();

                if ($reports['parent_id'] > 0) {
                    $parent_reports = \Models\Main\Reports\Reports::get_parent_reports($reports['parent_id']);
                    $parent_reports[] = $reports['parent_id'];

                    //remove parent reports
                    //db_query("delete from app_reports where id in (" . implode(',', $parent_reports) . ")");

                    \K::model()->db_delete('app_reports', [
                        ' id in (' . \K::model()->quoteToString($parent_reports, \PDO::PARAM_INT) . ')'
                    ]);

                    //reset paretn id
                    //db_query("update app_reports set parent_id=0 where id='" . $reports['id'] . "'");
                    \K::model()->db_update('app_reports', ['parent_id' => 0], ['id = ?', $reports['id']]);
                }
            }

            //change entity parent id
            /*db_query(
                "update app_entities set parent_id='" . $move_to_entities_id . "', group_id=0 where id='" . $entities_id . "'"
            );*/

            \K::model()->db_update(
                'app_entities',
                ['parent_id' => $move_to_entities_id, 'group_id' => 0],
                ['id = ?', $entities_id]
            );

            //autocreate parents reports
            /*$reports_query = db_query(
                "select id, parent_id from app_reports where entities_id in (" . implode(
                    ',',
                    $entities_tree
                ) . ") and reports_type not in ('parent','functions')"
            );*/

            $reports_query = \K::model()->db_fetch('app_reports', [
                'entities_id in (' . \K::model()->quoteToString(
                    $entities_tree,
                    \PDO::PARAM_INT
                ) . ') and reports_type not in (' . \K::model()->quoteToString(['parent', 'functions']) . ')'
            ], [], 'id,parent_id');

            //while ($reports = db_fetch_array($reports_query)) {
            foreach ($reports_query as $reports) {
                $reports = $reports->cast();

                if ($reports['parent_id'] == 0) {
                    \Models\Main\Reports\Reports::auto_create_parent_reports($reports['id']);
                }
            }

            //move entities
            if ($move_to_entities_id > 0) {
                //change parent item id
                if (isset(\K::$fw->POST['parent_item_id'])) {
                    $parent_item_id = \K::$fw->POST['parent_item_id'];

                    //db_query("update app_entity_" . $entities_id . " set parent_item_id='" . $parent_item_id . "'");
                    \K::model()->db_update('app_entity_' . $entities_id, ['parent_item_id' => $parent_item_id]);
                }
            } else {
                //reset paretn item id
                //db_query("update app_entity_" . $entities_id . " set parent_item_id=0");
                \K::model()->db_update('app_entity_' . $entities_id, ['parent_item_id' => 0]);
            }

            \K::model()->commit();

            \K::flash()->addMessage(\K::$fw->TEXT_ENTITY_STRUCTURE_CHANGED, 'success');

            \Helpers\Urls::redirect_to('main/entities/entities');
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }

    public function get_parent_items()
    {
        if (\K::$fw->VERB == 'POST') {
            $html = '';

            $entities_id = \K::$fw->POST['entities_id'];

            if ($entities_id) {
                $entities_info = \K::model()->db_find('app_entities', $entities_id);

                $choices = ['' => ''];

                $listing_sql_query = \Models\Main\Items\Items::add_listing_order_query_by_entity_id($entities_id);

                $items_query = \K::model()->db_query_exec(
                    'select e.* from app_entity_' . (int)$entities_id . " e where e.id > 0 " . $listing_sql_query
                );

                //while ($items = db_fetch_array($items_query)) {
                foreach ($items_query as $items) {
                    $parent_name = '';

                    if ($entities_info['parent_id'] > 0) {
                        $parent_name = \Models\Main\Items\Items::get_heading_field(
                                $entities_info['parent_id'],
                                $items['parent_item_id']
                            ) . ' > ';
                    }

                    $name = \Models\Main\Items\Items::get_heading_field($entities_id, $items['id']);

                    $choices[$items['id']] = $parent_name . $name;
                }

                $html = '					
					<div class="form-group">
				  	<label class="col-md-3 control-label" for="name">' . \K::$fw->TEXT_PARENT . '</label>
				    <div class="col-md-9">	
				  	  ' . \Helpers\Html::select_tag(
                        'parent_item_id',
                        $choices,
                        '',
                        ['class' => 'form-control input-xlarge chosen-select required']
                    ) . '
				  	  ' . \Helpers\App::tooltip_text(\K::$fw->TEXT_MOVE_TO_PARENT_ITEM_INFO) . '
				    </div>			
				  </div>';
            }
            echo $html;
        } else {
            \Helpers\Urls::redirect_to('main/dashboard');
        }
    }
}