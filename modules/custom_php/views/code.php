<h3 class="page-title"><?php
    echo TEXT_CUSTOM_PHP ?></h3>

<p><?php
    echo TEXT_CUSTOM_PHP_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('custom_php/form'), true) ?>
<?php
echo ' ' . button_tag(TEXT_ADD_FOLDER, url_for('custom_php/form', 'is_folder=1'), 'true', ['class' => 'btn btn-default']
    ) ?>

<div class="table-scrollable">
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_NAME ?></th>
            <th width="100%"><?php
                echo TEXT_ADMINISTRATOR_NOTE ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $code_tree = custom_php::get_tree();

        if (count($code_tree) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        foreach ($code_tree as $code):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('custom_php/delete', 'id=' . $code['id'])) . ' ' . button_icon_edit(
                            url_for('custom_php/form', 'id=' . $code['id'])
                        ) . ($code['is_folder'] ? ' ' . button_icon(
                                TEXT_BUTTON_CREATE,
                                'fa fa-plus',
                                url_for('custom_php/form', 'parent_id=' . $code['id'])
                            ) : '') ?></td>
                <td>
                    <?php
                    echo !$code['is_folder'] ? render_bool_value($code['is_active']) : ''; ?>
                </td>
                <td>
                    <?php
                    echo '<div class="tt" data-tt-id="global_var_' . $code['id'] . '" ' . ($code['parent_id'] > 0 ? 'data-tt-parent="global_var_' . $code['parent_id'] . '"' : '') . '></div>' ?>
                    <?php
                    echo($code['is_folder'] ? '<i class="fa fa-folder-o" aria-hidden="true"></i> <b>' . $code['name'] . '</b>' : link_to_modalbox(
                        $code['name'],
                        url_for('custom_php/form', 'id=' . $code['id'])
                    )) ?>
                </td>
                <td class="white-space-normal"><?php
                    echo $code['notes'] ?></td>
                <td><?php
                    echo $code['sort_order'] ?></td>
            </tr>
        <?php
        endforeach ?>
        </tbody>
    </table>
</div>