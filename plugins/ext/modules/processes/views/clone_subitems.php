<?php
require(component_path('ext/processes/navigation')) ?>

    <h3 class="page-title"><?php
        echo TEXT_EXT_CLONE_SUBITEMS ?></h3>

    <p><?php
        echo TEXT_EXT_CLONE_SUBITEMS_INFO ?></p>

<?php
echo button_tag(
    TEXT_ADD_RULE,
    url_for(
        'ext/processes/clone_subitems_form',
        'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id']
    ),
    true
) ?>


    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th width="80"><?php
                    echo TEXT_ACTION ?></th>
                <th><?php
                    echo TEXT_EXT_FROM_ENTITY ?></th>
                <th><?php
                    echo TEXT_EXT_TO_ENTITY ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $rules = clone_subitems::get_rules_tree($app_actions_info['id']);

            //print_rr($rules);

            if (count($rules) == 0) {
                echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            foreach ($rules as $rule) {
                $check_query = db_query(
                    "(select count(*) as total from app_entities where parent_id='" . $rule['from_entity_id'] . "')"
                );
                $check = db_fetch_array($check_query);

                $check_query = db_query(
                    "(select count(*) as total from app_entities where parent_id='" . $rule['to_entity_id'] . "')"
                );
                $check2 = db_fetch_array($check_query);

                $html = '';
                if ($check['total'] > 0 and $check2['total'] > 0) {
                    $html = ' ' . button_icon(
                            TEXT_ADD_RULE,
                            'fa fa-plus',
                            url_for(
                                'ext/processes/clone_subitems_form',
                                'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id'] . '&parent_id=' . $rule['id']
                            )
                        );
                }
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'ext/processes/clone_subitems_delete',
                                    'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id'] . '&id=' . $rule['id']
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'ext/processes/clone_subitems_form',
                                    'process_id=' . $app_process_info['id'] . '&actions_id=' . $app_actions_info['id'] . '&id=' . $rule['id'] . ($rule['parent_id'] ? '&parent_id=' . $rule['parent_id'] : '')
                                )
                            ) . $html; ?></td>
                    <td><?php
                        echo str_repeat(
                                '&nbsp;<i class="fa fa-minus" aria-hidden="true"></i>&nbsp;',
                                $rule['level']
                            ) . $app_entities_cache[$rule['from_entity_id']]['name'] ?></td>
                    <td><?php
                        echo str_repeat(
                                '&nbsp;<i class="fa fa-minus" aria-hidden="true"></i>&nbsp;',
                                $rule['level']
                            ) . $app_entities_cache[$rule['to_entity_id']]['name'] ?></td>
                </tr>
            <?php
            } ?>
            </tbody>
        </table>
    </div>

<?php
echo '<a href="' . url_for(
        'ext/processes/actions',
        'process_id=' . _get::int('process_id')
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>