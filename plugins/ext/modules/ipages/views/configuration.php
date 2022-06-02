<h3 class="page-title"><?php
    echo TEXT_EXT_IPAGES ?></h3>


<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/ipages/configuration_form'), true) ?>
<?php
echo ' ' . button_tag(TEXT_ADD_NEW_MENU_ITEM, url_for('ext/ipages/menu_form')) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_SHORT_NAME ?></th>
            <th><?php
                echo TEXT_EXT_USERS_GROUPS ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ipages = ipages::get_tree();


        if (count($ipages) == 0) {
            echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        foreach ($ipages as $pages):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/ipages/configuration_delete', 'id=' . $pages['id'])
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/ipages/' . ($pages['is_menu'] ? 'menu_form' : 'configuration_form'),
                                'id=' . $pages['id']
                            )
                        ) . ($pages['is_menu'] ? ' ' . button_icon(
                                TEXT_BUTTON_CREATE,
                                'fa fa-plus',
                                url_for('ext/ipages/configuration_form', 'parent_id=' . $pages['id'])
                            ) : '') ?></td>
                <td <?php
                echo($pages['level'] ? 'style="padding-left: ' . ($pages['level'] * 22) . 'px"' : '') ?> > <?php
                    echo($pages['is_menu'] ? '<i class="fa fa-folder-o" aria-hidden="true"></i> ' . $pages['name'] : link_to(
                        $pages['name'],
                        url_for('ext/ipages/configuration_description', 'id=' . $pages['id'])
                    )) ?></td>
                <td><?php
                    echo $pages['short_name'] ?></td>
                <td>
                    <?php
                    if (strlen($pages['users_groups']) > 0) {
                        $users_groups_list = [];
                        foreach (explode(',', $pages['users_groups']) as $users_groups_id) {
                            $users_groups_list[] = access_groups::get_name_by_id($users_groups_id);
                        }

                        echo implode('<br>', $users_groups_list) . '<br>';
                    }

                    if (strlen($pages['assigned_to']) > 0) {
                        $users_list = [];
                        foreach (explode(',', $pages['assigned_to']) as $users_id) {
                            $users_list[] = users::get_name_by_id($users_id);
                        }

                        echo implode('<br>', $users_list);
                    }
                    ?>
                </td>
                <td><?php
                    echo $pages['sort_order'] ?></td>
            </tr>
        <?php
        endforeach ?>
        </tbody>
    </table>
</div>