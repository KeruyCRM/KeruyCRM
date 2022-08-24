<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
\K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD_NEW_FIELD,
    \Helpers\Urls::url_for('main/entities/fields_form', 'entities_id=' . \K::$fw->GET['entities_id']),
    true
) ?>

<div class="btn-group">
    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
        <?= \K::$fw->TEXT_WITH_SELECTED ?> <i class="fa fa-angle-down"></i>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <?= \Helpers\Urls::link_to_modalbox(
                \K::$fw->TEXT_FIELDS_EXPORT,
                \Helpers\Urls::url_for('main/entities/fields_export_form', 'entities_id=' . \K::$fw->GET['entities_id'])
            ) ?>
        </li>
        <li>
            <?= \Helpers\Urls::link_to_modalbox(
                \K::$fw->TEXT_COPY_FIELDS,
                \Helpers\Urls::url_for('main/entities/fields_copy_form', 'entities_id=' . \K::$fw->GET['entities_id'])
            ) ?>
        </li>
        <li>
            <?= \Helpers\Urls::link_to_modalbox(
                \K::$fw->TEXT_EDIT_FIELDS,
                \Helpers\Urls::url_for(
                    'main/entities/fields_multiple_edit',
                    'entities_id=' . \K::$fw->GET['entities_id']
                )
            ) ?>
        </li>
    </ul>
</div>

<?= \Helpers\Html::button_tag(
    '<i class="fa fa-upload"></i>',
    \Helpers\Urls::url_for('main/entities/fields_import_form', 'entities_id=' . \K::$fw->GET['entities_id']),
    true,
    ['class' => 'btn btn-default', 'title' => \K::$fw->TEXT_IMPORT_FIELDS]
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \Helpers\Html::input_checkbox_tag('select_all_fields', '', ['class' => 'select_all_fields']) ?></th>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th>#</th>
            <th><?= \K::$fw->TEXT_FORM_TAB ?></th>
            <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
            <th><?= \K::$fw->TEXT_SHORT_NAME ?></th>
            <th><?= \K::$fw->TEXT_NOTE ?></th>
            <th><?= \K::$fw->TEXT_IS_REQUIRED ?></th>
            <th><?= \K::$fw->TEXT_IS_UNIQUE ?></th>
            <th><?= \K::$fw->TEXT_TYPE ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count(\K::$fw->fields_query) == 0) {
            echo '<tr><td colspan="10">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        //while ($v = db_fetch_array($fields_query)):
        foreach (\K::$fw->fields_query as $v):
            $cfg = new \Tools\Settings($v['configuration']);

            $heading_note = ($v['is_heading'] ? ' <span class="label label-info">' . \K::$fw->TEXT_HEADING . '</span>' : '');
            ?>
            <tr>
                <?php
                if (in_array($v['type'], \K::$fw->reserved_fields_types)) { ?>
                    <td></td>
                    <td style="white-space: nowrap;" align="center"><?= \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/entities/fields_form_internal',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= (in_array(
                            $v['type'],
                            \Models\Main\Fields_types::get_reserved_types()
                        ) ? \Helpers\App::tooltip_icon(
                            '[' . str_replace('fieldtype_', '', $v['type']) . ']'
                        ) : $v['id']) ?></td>
                    <td></td>
                    <td><?= (strlen($v['name']) ? $v['name'] : \Models\Main\Fields_types::get_title(
                            $v['type']
                        )) . $heading_note ?></td>
                    <td><?= $v['short_name'] ?></td>
                    <td></td>
                    <td><?= \Helpers\App::render_bool_value(1, true) ?></td>
                    <td></td>
                    <td class="nowrap"><?= \Models\Main\Fields_types::get_title($v['type']) ?></td>
                    <?php
                } else { ?>
                    <td><?= \Helpers\Html::input_checkbox_tag('fields[]', $v['id'], ['class' => 'fields_checkbox']
                        ) ?></td>
                    <td style="white-space: nowrap;">
                        <?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/entities/fields_delete',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/entities/fields_form',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </td>
                    <td><?= $v['id'] ?></td>
                    <td><?= $v['tab_name'] ?></td>
                    <td><?= \Models\Main\Fields_types::render_field_name(
                            $v['name'],
                            $v['type'],
                            $v['id']
                        ) . $heading_note ?></td>
                    <td><?= $v['short_name'] ?></td>
                    <td><?= \Helpers\App::tooltip_icon($v['notes'], 'left') ?></td>
                    <td><?= \Helpers\App::render_bool_value($v['is_required'], true) ?></td>
                    <td><?= \Helpers\App::render_bool_value(($cfg->get('is_unique') > 0 ? true : false), true) ?></td>
                    <td class="nowrap"><?= \Models\Main\Fields_types::get_title($v['type']) ?></td>
                    <?php
                } ?>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $('#select_all_fields').click(function () {
        select_all_by_classname('select_all_fields', 'fields_checkbox')
    })
</script>