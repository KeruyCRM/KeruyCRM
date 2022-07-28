<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

    <h3 class="page-title"><?= \K::$fw->TEXT_ACCESS_ALLOCATION_RULES ?></h3>

    <p><?= \K::$fw->TEXT_ACCESS_ALLOCATION_RULES_INFO ?></p>

<?php
if (count(\K::$fw->form_fields_query) == 0) {
    echo \Helpers\Html::button_tag(
        \K::$fw->TEXT_ADD_FIELD,
        \Helpers\Urls::url_for('main/access_rules/fields_form', 'entities_id=' . \K::$fw->GET['entities_id']),
        true
    );
}
?>
    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th>#</th>
                <th width="100%"><?= \K::$fw->TEXT_RULE_FOR_FIELD ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            if (count(\K::$fw->form_fields_query) == 0) {
                echo '<tr><td colspan="9">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            //while ($v = db_fetch_array($form_fields_query)):
            foreach (\K::$fw->form_fields_query as $v):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/access_rules/fields_delete',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/access_rules/fields_form',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= $v['id'] ?></td>
                    <td><?= '<a href="' . \Helpers\Urls::url_for(
                            'main/access_rules/rules',
                            'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $v['fields_id']
                        ) . '">' . \Models\Main\Fields_types::get_option(
                            $v['type'],
                            'name',
                            $v['name']
                        ) . '</a>' ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
if (\K::$fw->entities_info['parent_id'] != 0) {
    //require(component_path('access_rules/hide_add_button_rules'));
    \K::view()->render(\Helpers\Urls::components_path('main/access_rules/hide_add_button_rules'));
}
?>