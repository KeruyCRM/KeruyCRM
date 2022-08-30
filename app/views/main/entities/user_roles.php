<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->field_info['name'] . ' <i class="fa fa-angle-right"></i> ' . \K::$fw->TEXT_USER_ROLES ?></h3>

<p><?= \K::$fw->TEXT_USER_ROLES_INFO ?></p>

<?php
if (!\Models\Main\Entities::has_subentities(\K::$fw->field_info['entities_id'])) {
    echo '<div class="alert alert-warning">' . \K::$fw->TEXT_USER_ROLES_ENTITIES_WARNING . '</div>';
} else {
    echo \Helpers\Html::button_tag(
            \K::$fw->TEXT_BUTTON_ADD,
            \Helpers\Urls::url_for(
                'main/entities/user_roles_form',
                'fields_id=' . \K::$fw->field_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
            )
        ) . ' ' . \Helpers\Html::button_tag(
            \K::$fw->TEXT_BUTTON_SORT,
            \Helpers\Urls::url_for(
                'main/entities/user_roles_sort',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->field_info['id']
            ),
            true,
            ['class' => 'btn btn-default']
        ) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th width="100%"><?= \K::$fw->TEXT_TITLE ?></th>
                <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count(\K::$fw->filters_query) == 0) {
                echo '<tr><td colspan="3">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            } ?>
            <?php
            //while ($v = db_fetch_array($filters_query)):
            foreach (\K::$fw->filters_query as $v):
                $v = $v->cast();
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/entities/user_roles_delete',
                                'id=' . $v['id'] . '&fields_id=' . \K::$fw->field_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        )
                        . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/entities/user_roles_form',
                                'id=' . $v['id'] . '&fields_id=' . \K::$fw->field_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= \Helpers\Urls::link_to(
                            $v['name'],
                            \Helpers\Urls::url_for(
                                'main/entities/user_roles_access',
                                'role_id=' . $v['id'] . '&fields_id=' . \K::$fw->field_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= $v['sort_order'] ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
} ?>

<?= \Helpers\Urls::link_to(
    \K::$fw->TEXT_BUTTON_BACK,
    \Helpers\Urls::url_for('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'btn btn-default']
) ?>
