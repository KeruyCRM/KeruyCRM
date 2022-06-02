<h3 class="page-title"><?php
    echo TEXT_GLOBAL_VARS ?></h3>

<p><?php
    echo TEXT_GLOBAL_VARS_INFO ?></p>


<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('global_vars/form'), true) ?>
<?php
echo ' ' . button_tag(
        TEXT_ADD_FOLDER,
        url_for('global_vars/form', 'is_folder=1'),
        'true',
        ['class' => 'btn btn-default']
    ) ?>

<div class="table-scrollable">
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo '<i class="fa fa-info-circle" title="' . TEXT_ADMINISTRATOR_NOTE . '"></i' ?></th>
            <th width="100%"><?php
                echo TEXT_VALUE ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $global_vars = global_vars::get_tree();

        if (count($global_vars) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        foreach ($global_vars as $var):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(url_for('global_vars/delete', 'id=' . $var['id'])) . ' ' . button_icon_edit(
                            url_for('global_vars/form', 'id=' . $var['id'])
                        ) . ($var['is_folder'] ? ' ' . button_icon(
                                TEXT_BUTTON_CREATE,
                                'fa fa-plus',
                                url_for('global_vars/form', 'parent_id=' . $var['id'])
                            ) : '') ?></td>
                <td>
                    <?php
                    echo '<div class="tt" data-tt-id="global_var_' . $var['id'] . '" ' . ($var['parent_id'] > 0 ? 'data-tt-parent="global_var_' . $var['parent_id'] . '"' : '') . '></div>' ?>
                    <?php
                    echo($var['is_folder'] ? '<i class="fa fa-folder-o" aria-hidden="true"></i> <b>' . $var['name'] . '</b>' : input_tag(
                        'var_name_tmp[]',
                        'VAR_' . $var['name'],
                        [
                            'class' => 'form-control input-large select-all',
                            'readonly' => 'readonly',
                            'style' => 'display:inline-block'
                        ]
                    )) ?>
                </td>
                <td style="text-align:center"><?php
                    echo tooltip_icon($var['notes']) ?></td>
                <td class="white-space-normal"><?php
                    echo $var['value'] ?></td>
                <td><?php
                    echo $var['sort_order'] ?></td>
            </tr>
        <?php
        endforeach ?>
        </tbody>
    </table>
</div>