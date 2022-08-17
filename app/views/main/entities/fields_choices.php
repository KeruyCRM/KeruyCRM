<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

    <h3 class="page-title"><?= \K::$fw->field_info['name'] . ': ' . \K::$fw->TEXT_NAV_FIELDS_CHOICES_CONFIG ?></h3>

<?php

$html = '';
$extra_buttons = '';

switch (\K::$fw->field_info['type']) {
    case 'fieldtype_autostatus':
        $html = '<p class="note note-info">' . \K::$fw->TEXT_FIELDTYPE_AUTOSTATUS_OPTIONS_TIP . '</p>';
        $extra_buttons = \Helpers\Html::button_tag(
            '<i class="fa fa-sitemap"></i> ' . \K::$fw->TEXT_FLOWCHART,
            \Helpers\Urls::url_for(
                'main/entities/fields_choices_flowchart',
                'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
            ),
            false,
            ['class' => 'btn btn-default']
        );
        break;
    case 'fieldtype_image_map':
        $html = '<p class="note note-info">' . \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_OPTIONS_TIP . '</p>';
        break;
}

echo $html;

echo \Helpers\Html::button_tag(
        \K::$fw->TEXT_BUTTON_ADD_NEW_VALUE,
        \Helpers\Urls::url_for(
            'main/entities/fields_choices_form',
            'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
        ),
        true,
        ['class' => 'btn btn-primary']
    ) . ' ';

echo '<div class="btn-group">' .
    \Helpers\Html::button_tag(
        \K::$fw->TEXT_BUTTON_SORT,
        \Helpers\Urls::url_for(
            'main/entities/fields_choices_sort',
            'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
        ),
        true,
        ['class' => 'btn btn-default']
    ) .
    '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button>
        <ul class="dropdown-menu" role="menu">
            <li>
                    <a href="javascript: open_dialog(\'' . \Helpers\Urls::url_for(
        'main/entities/fields_choices_sort_reset',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ) . '\')">' . \K::$fw->TEXT_RESET_SORTING . '</a>
            </li>
        </ul>
        </div>';

?>

<?= $extra_buttons ?>
    <div class="table-scrollable">
        <table class="tree-table table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th>#</th>
                <th><?= \K::$fw->TEXT_IS_ACTIVE ?></th>
                <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
                <?php
                if (\K::$fw->field_info['type'] != 'fieldtype_autostatus'): ?>
                    <th><?= \K::$fw->TEXT_IS_DEFAULT ?></th>
                <?php
                endif ?>

                <th><?= \K::$fw->TEXT_BACKGROUND_COLOR ?></th>
                <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
                <th><?= \K::$fw->TEXT_VALUE ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $tree = \Models\Main\Fields_choices::get_tree(\K::$fw->GET['fields_id']);

            if (count($tree) == 0) {
                echo '<tr><td colspan="9">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            foreach ($tree as $v):

                $html = '';
                if (\K::$fw->field_info['type'] == 'fieldtype_autostatus') {
                    $count = 0;
                    /*$reports_info_query = db_query(
                        "select * from app_reports where entities_id='" . db_input(
                            \K::$fw->GET['entities_id']
                        ) . "' and reports_type='fields_choices" . $v['id'] . "'"
                    );*/

                    $reports_info = \K::model()->db_fetch_one('app_reports', [
                        'entities_id = ? and reports_type = ?',
                        \K::$fw->GET['entities_id'],
                        'fields_choices' . (int)$v['id']
                    ]);

                    if ($reports_info) {
                        $count = \K::model()->db_count('app_reports_filters', $reports_info['id'], 'reports_id');
                    }

                    $html = \Helpers\Urls::link_to(
                            str_repeat('&nbsp;-&nbsp;', $v['level']) . $v['name'],
                            \Helpers\Urls::url_for(
                                'main/entities/fields_choices_filters',
                                'choices_id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                            )
                        ) . \Helpers\App::tooltip_text(\K::$fw->TEXT_FILTERS . ': ' . $count);
                }
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/entities/fields_choices_delete',
                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                            )
                        );
                        echo ' ' . \Helpers\Html::button_icon_edit(
                                \Helpers\Urls::url_for(
                                    'main/entities/fields_choices_form',
                                    'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                                )
                            );
                        echo ' ' . \Helpers\Html::button_icon(
                                \K::$fw->TEXT_BUTTON_CREATE_SUB_VALUE,
                                'fa fa-plus',
                                \Helpers\Urls::url_for(
                                    'main/entities/fields_choices_form',
                                    'parent_id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                                )
                            );
                        ?></td>
                    <td><?= $v['id'] ?></td>
                    <td><?= \Helpers\App::render_bool_value($v['is_active']) ?></td>
                    <td>
                        <?= '<div class="tt" data-tt-id="choice_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="choice_' . $v['parent_id'] . '"' : '') . ' data-tt-sort-url="' . \Helpers\Urls::url_for(
                            'main/entities/fields_choices_sort',
                            'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id'] . '&parent_id=' . $v['id']
                        ) . '"></div>' ?>
                        <?= (\K::$fw->field_info['type'] == 'fieldtype_autostatus' ? $html : $v['name']) ?>
                    </td>

                    <?php
                    if (\K::$fw->field_info['type'] != 'fieldtype_autostatus'): ?>
                        <td><?= \Helpers\App::render_bool_value($v['is_default']) ?></td>
                    <?php
                    endif ?>

                    <td><?= \Helpers\App::render_bg_color_block($v['bg_color']) ?></td>
                    <td><?= $v['sort_order'] ?></td>
                    <td><?= $v['value'] ?></td>
                </tr>
            <?php
            endforeach ?>
            </tbody>
        </table>
    </div>

<?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
    'main/entities/fields',
    'entities_id=' . \K::$fw->GET['entities_id']
) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>'; ?>