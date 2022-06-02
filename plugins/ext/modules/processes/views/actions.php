<?php
require(component_path('ext/processes/navigation')) ?>

<h3 class="page-title"><?php
    echo TEXT_EXT_PROCESSES_ACTIONS ?></h3>

<p><?php
    echo TEXT_EXT_PROCESSES_ACTIONS_DESCRIPTION ?></p>

<?php
echo button_tag(
        TEXT_EXT_BUTTON_ADD_ACTION,
        url_for('ext/processes/actions_form', 'process_id=' . _get::int('process_id'))
    ) . ' ' . button_tag(
        '<i class="fa fa-sitemap"></i> ' . TEXT_FLOWCHART,
        url_for('ext/processes/processes_flowchart', 'process_id=' . _get::int('process_id')),
        false,
        ['class' => 'btn btn-default']
    ) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th><?php
                echo TEXT_EXT_PROCESS ?></th>
            <th><?php
                echo TEXT_IS_ACTIVE ?></th>
            <th><?php
                echo TEXT_TYPE ?></th>
            <th width="100%"><?php
                echo TEXT_NOTE ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        $actions_types = processes::get_actions_types_choices($app_process_info['entities_id']);

        $actions_query = db_query(
            "select pa.*, p.name as process_name from app_ext_processes_actions pa, app_ext_processes p where pa.process_id='" . _get::int(
                'process_id'
            ) . "' and  p.id=pa.process_id order by pa.sort_order"
        );

        if (!db_num_rows($actions_query)) {
            echo '<tr><td colspan="8">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($v = db_fetch_array($actions_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/processes/actions_delete',
                                'id=' . $v['id'] . '&process_id=' . _get::int('process_id')
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/processes/actions_form',
                                'id=' . $v['id'] . '&process_id=' . _get::int('process_id')
                            )
                        ); ?></td>
                <td><?php
                    echo $v['id'] ?></td>
                <td><?php
                    echo $v['process_name'] ?></td>
                <td><?php
                    echo render_bool_value($v['is_active']) ?></td>
                <td><?php

                    if (strstr($v['type'], 'clone_subitems_linked_entity_') or strstr(
                            $v['type'],
                            'clone_item_entity_'
                        )) {
                        echo link_to(
                            $actions_types[$v['type']],
                            url_for(
                                'ext/processes/clone_subitems',
                                'actions_id=' . $v['id'] . '&process_id=' . _get::int('process_id')
                            )
                        );

                        $count_query = db_query(
                            "select count(*) as total from app_ext_processes_clone_subitems where actions_id='" . $v['id'] . "'"
                        );
                        $count = db_fetch_array($count_query);
                        echo tooltip_text(TEXT_RULES . ': ' . $count['total']);
                    } elseif (strstr($v['type'], 'save_export_template_entity_') or strstr(
                            $v['type'],
                            'runphp_item_entity_'
                        )) {
                        echo $actions_types[$v['type']];
                    } else {
                        echo link_to(
                            $actions_types[$v['type']],
                            url_for(
                                'ext/processes/fields',
                                'actions_id=' . $v['id'] . '&process_id=' . _get::int('process_id')
                            )
                        );

                        $count_query = db_query(
                            "select count(*) as total from app_ext_processes_actions_fields  where actions_id='" . $v['id'] . "'"
                        );
                        $count = db_fetch_array($count_query);
                        echo tooltip_text(TEXT_EXT_CHANGES . ': ' . $count['total']);
                    }

                    ?></td>
                <td class="white-space-normal"><?php
                    echo nl2br($v['description']) ?></td>
                <td><?php
                    echo $v['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>

<?php
echo '<a href="' . url_for('ext/processes/processes') . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>