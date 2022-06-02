<h3 class="page-title"><?php
    echo TEXT_MENU_CONFIGURATION_MENU ?></h3>

<p><?php
    echo TEXT_CONFIGURATION_MENU_EXPLAIN ?></p>

<?php
echo button_tag(TEXT_ADD_NEW_MENU_ITEM, url_for('entities/menu_form')) ?>
<?php
echo ' ' . button_tag(TEXT_SORT, url_for('entities/menu_sort')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_count('app_entities_menu') == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        $tree = entities_menu::get_tree();
        foreach ($tree as $v) {
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('entities/menu_delete', 'id=' . $v['id'])) . ' ' . button_icon_edit(
                            url_for('entities/menu_form', 'id=' . $v['id'])
                        ) . ($v['level'] < 2 ? ' ' . button_icon(
                                TEXT_BUTTON_CREATE,
                                'fa fa-plus',
                                url_for('entities/menu_form', 'parent_id=' . $v['id'])
                            ) : '') ?></td>
                <td <?php
                echo($v['level'] ? 'style="padding-left: ' . ($v['level'] * 22) . 'px"' : '') ?> ><?php
                    echo app_render_icon(
                            strlen($v['icon']) > 0 ? $v['icon'] : 'fa-list-alt'
                        ) . ' <b>' . $v['name'] . '</b>';

                    if ($v['type'] == 'url') {
                        echo '<div style="padding-left: 19px;">- <a href="' . $v['url'] . '" target="_blank">' . app_truncate_text(
                                $v['url']
                            ) . '</a></div>';
                    } else {
                        if (strlen($v['entities_list']) > 0) {
                            $entities_query = db_query(
                                "select * from app_entities where id in (" . $v['entities_list'] . ") order by field(id," . $v['entities_list'] . ")"
                            );
                            while ($entities = db_fetch_array($entities_query)) {
                                echo '<div style="padding-left: 19px;">- ' . $entities['name'] . '</div>';
                            }
                        }

                        if (strlen($v['reports_list']) > 0) {
                            echo entities_menu::get_reports_list($v['reports_list']);
                        }

                        if (strlen($v['pages_list']) > 0) {
                            echo entities_menu::get_pages_list($v['pages_list']);
                        }
                    }

                    ?></td>
                <td><?php
                    echo $v['sort_order'] ?></td>
            </tr>
        <?php
        } ?>
        </tbody>
    </table>
</div>