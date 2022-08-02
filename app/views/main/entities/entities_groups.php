<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
    <ul class="page-breadcrumb breadcrumb">
        <?= '
        <li>' . \Helpers\Urls::link_to(
            \K::$fw->TEXT_ENTITIES_HEADING,
            \Helpers\Urls::url_for('main/entities/entities')
        ) . '<i class="fa fa-angle-right"></i></li>				
        <li>' . \K::$fw->TEXT_ENTITIES_GROUPS . '</li>';
        ?>
    </ul>

    <p><?= \K::$fw->TEXT_ENTITIES_GROUPS_INFO ?></p>

<?= \Helpers\Html::button_tag(\K::$fw->TEXT_BUTTON_ADD, \Helpers\Urls::url_for('main/entities/entities_groups_form')) ?>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_SORT,
    \Helpers\Urls::url_for('main/entities/entities_groups_sort'),
    true,
    ['class' => 'btn btn-default']
) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th><?= \K::$fw->TEXT_ID ?></th>
                <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
                <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (\K::$fw->groups_query_count == 0) {
                echo '<tr><td colspan="8">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            //while ($v = db_fetch_array($groups_query)):
            foreach (\K::$fw->groups_query as $v):
                $v = $v->cast();
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for('main/entities/entities_groups_delete', 'id=' . $v['id'])
                        ) . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for('main/entities/entities_groups_form', 'id=' . $v['id'])
                        ); ?></td>
                    <td><?= $v['id'] ?></td>
                    <td><?= $v['name']; ?></td>
                    <td><?= $v['sort_order'] ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>
<?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
    'main/entities/entities'
) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>'; ?>