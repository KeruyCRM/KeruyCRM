<?php
require(component_path('entities/navigation')) ?>

    <h3 class="page-title"><?php
        echo link_to(
                TEXT_NAV_FORM_CONFIG,
                url_for('entities/forms', 'entities_id=' . $_GET['entities_id'])
            ) . ' <i class="fa fa-angle-right"></i> ' . TEXT_TAB_GROUPS ?></h3>

    <p><?php
        echo TEXT_TAB_GROUPS_INFO ?></p>

<?php
echo button_tag(TEXT_ADD_FOLDER, url_for('forms_tabs/form', 'entities_id=' . _GET('entities_id'))) ?>
<?php
echo ' ' . button_tag(
        TEXT_SORT,
        url_for('forms_tabs/sort', 'entities_id=' . _GET('entities_id')),
        true,
        ['class' => 'btn btn-default']
    ) ?>


    <div class="table-scrollable">
        <table class="tree-table table table-striped table-bordered table-hover">
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
            $tabs = forms_tabs::get_tree(_GET('entities_id'));

            if (count($tabs) == 0) {
                echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            foreach ($tabs as $v):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        if ($v['is_folder']) {
                            echo button_icon_delete(
                                    url_for(
                                        'forms_tabs/delete',
                                        'id=' . $v['id'] . '&entities_id=' . _GET('entities_id')
                                    )
                                ) . ' ' . button_icon_edit(
                                    url_for('forms_tabs/form', 'id=' . $v['id'] . '&entities_id=' . _GET('entities_id'))
                                );
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        echo '<div class="tt" data-tt-id="global_var_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="global_var_' . $v['parent_id'] . '"' : '') . '></div>' ?>
                        <?php
                        echo($v['is_folder'] ? '<i class="fa fa-folder-o" aria-hidden="true"></i> <b>' . $v['name'] . '</b>' : $v['name']) ?>
                    </td>
                    <td><?php
                        echo $v['sort_order'] ?></td>
                </tr>
            <?php
            endforeach ?>
            </tbody>
        </table>
    </div>

<?php
echo '<a class="btn btn-default" href="' . url_for(
        'entities/forms',
        'entities_id=' . $_GET['entities_id']
    ) . '">' . TEXT_BUTTON_BACK . '</a>'; ?>