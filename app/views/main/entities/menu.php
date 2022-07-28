<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_MENU_CONFIGURATION_MENU ?></h3>

<p><?= \K::$fw->TEXT_CONFIGURATION_MENU_EXPLAIN ?></p>

<?= \Helpers\Html::button_tag(\K::$fw->TEXT_ADD_NEW_MENU_ITEM, \Helpers\Urls::url_for('main/entities/menu_form')) ?>
<?= ' ' . \Helpers\Html::button_tag(\K::$fw->TEXT_SORT, \Helpers\Urls::url_for('main/entities/menu_sort')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
            <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (\K::$fw->countMenu == 0) {
            echo '<tr><td colspan="5">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $tree = \Models\Main\Entities_menu::get_tree();
        foreach ($tree as $v):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for('main/entities/menu_delete', 'id=' . $v['id'])
                    ) . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for('main/entities/menu_form', 'id=' . $v['id'])
                    ) . ($v['level'] < 2 ? ' ' . \Helpers\Html::button_icon(
                            \K::$fw->TEXT_BUTTON_CREATE,
                            'fa fa-plus',
                            \Helpers\Urls::url_for('main/entities/menu_form', 'parent_id=' . $v['id'])
                        ) : '') ?></td>
                <td <?= ($v['level'] ? 'style="padding-left: ' . ($v['level'] * 22) . 'px"' : '') ?> ><?php
                    echo \Helpers\App::app_render_icon(
                            strlen($v['icon']) > 0 ? $v['icon'] : 'fa-list-alt'
                        ) . ' <b>' . $v['name'] . '</b>';

                    if ($v['type'] == 'url') {
                        echo '<div style="padding-left: 19px;">- <a href="' . $v['url'] . '" target="_blank">' . \Helpers\App::app_truncate_text(
                                $v['url']
                            ) . '</a></div>';
                    } else {
                        if (strlen($v['entities_list']) > 0) {
                            /*$entities_query = db_query(
                                "select * from app_entities where id in (" . $v['entities_list'] . ") order by field(id," . $v['entities_list'] . ")"
                            );*/

                            $entities_query = \K::model()->db_fetch('app_entities', [
                                'id in (' . $v['entities_list'] . ')'
                            ], ['order' => 'field(id,' . $v['entities_list'] . ')'], 'name');

                            //while ($entities = db_fetch_array($entities_query)) {
                            foreach ($entities_query as $entities) {
                                $entities = $entities->cast();

                                echo '<div style="padding-left: 19px;">- ' . $entities['name'] . '</div>';
                            }
                        }

                        if (strlen($v['reports_list']) > 0) {
                            echo \Models\Main\Entities_menu::get_reports_list($v['reports_list']);
                        }

                        if (strlen($v['pages_list']) > 0) {
                            echo \Models\Main\Entities_menu::get_pages_list($v['pages_list']);
                        }
                    }

                    ?></td>
                <td><?= $v['sort_order'] ?></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>