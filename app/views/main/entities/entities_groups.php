<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<ul class="page-breadcrumb breadcrumb">
    <?= '
        <li>' . link_to(TEXT_ENTITIES_HEADING, url_for('entities/entities')) . '<i class="fa fa-angle-right"></i></li>				
        <li>' . TEXT_ENTITIES_GROUPS . '</li>';
    ?>
</ul>

<p><?= TEXT_ENTITIES_GROUPS_INFO ?></p>

<?= button_tag(TEXT_BUTTON_ADD, url_for('entities/entities_groups_form')) ?>
<?= button_tag(TEXT_BUTTON_SORT, url_for('entities/entities_groups_sort'), true, ['class' => 'btn btn-default']) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= TEXT_ACTION ?></th>
            <th><?= TEXT_ID ?></th>
            <th width="100%"><?= TEXT_NAME ?></th>
            <th><?= TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (db_num_rows($groups_query) == 0) {
            echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($v = db_fetch_array($groups_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= button_icon_delete(
                            url_for('entities/entities_groups_delete', 'id=' . $v['id'])
                        ) . ' ' . button_icon_edit(url_for('entities/entities_groups_form', 'id=' . $v['id'])); ?></td>
                <td><?= $v['id'] ?></td>
                <td><?= $v['name']; ?></td>
                <td><?= $v['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>
<?= '<a class="btn btn-default" href="' . url_for('entities/entities') . '">' . TEXT_BUTTON_BACK . '</a>'; ?>