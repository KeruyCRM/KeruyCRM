<h3 class="page-title"><?php
    echo TEXT_EXT_ITEM_PIVOT_TABLES ?></h3>

<p><?php
    echo TEXT_EXT_ITEM_PIVOT_TABLES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_CREATE, url_for('ext/item_pivot_tables/reports_form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_REPORT_ENTITY ?></th>
            <th><?php
                echo TEXT_EXT_SELECT_DATA_FROM ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $reports_query = db_query("select * from app_ext_item_pivot_tables order by sort_order, name");

        $entities_cache = entities::get_name_cache();
        $fields_cahce = fields::get_name_cache();


        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/item_pivot_tables/reports_delete', 'id=' . $reports['id'])
                        ) . ' ' . button_icon_edit(
                            url_for('ext/item_pivot_tables/reports_form', 'id=' . $reports['id'])
                        ) ?></td>
                <td><?php
                    echo link_to(
                        $reports['name'],
                        url_for('ext/item_pivot_tables/calc', 'reports_id=' . $reports['id'])
                    );

                    if (strlen($reports['related_entities_fields'])) {
                        foreach (explode(',', $reports['related_entities_fields']) as $fields_id) {
                            $html = '';
                            $fields_info_query = db_query(
                                "select f.id, f.configuration, f.entities_id, e.name as entitis_name from app_fields f, app_entities e where f.type in ('fieldtype_entity','fieldtype_entity_multilevel','fieldtype_entity_ajax') and e.id=f.entities_id and f.id='" . $fields_id . "'"
                            );
                            if ($fields_info = db_fetch_array($fields_info_query)) {
                                $cfg = new fields_types_cfg($fields_info['configuration']);

                                $entity_id = $cfg->get('entity_id');


                                //create default fitler
                                $reports_info_query = db_query(
                                    "select * from app_reports where entities_id='" . db_input(
                                        $entity_id
                                    ) . "' and reports_type='item_pivot_tables_" . $reports['id'] . "_" . $fields_id . "'"
                                );
                                if (!$reports_info = db_fetch_array($reports_info_query)) {
                                    $sql_data = [
                                        'name' => '',
                                        'entities_id' => $entity_id,
                                        'reports_type' => 'item_pivot_tables_' . $reports['id'] . '_' . $fields_id,
                                        'in_menu' => 0,
                                        'in_dashboard' => 0,
                                        'listing_order_fields' => '',
                                        'created_by' => $app_logged_users_id,
                                    ];

                                    db_perform('app_reports', $sql_data);
                                    $reports_id = db_insert_id();

                                    reports::auto_create_parent_reports($reports_id);
                                } else {
                                    $reports_id = $reports_info['id'];
                                }

                                $panels_query = db_query(
                                    "select * from app_filters_panels where entities_id='" . $entity_id . "' and type='item_pivot_tables_" . $reports['id'] . "_" . $fields_id . "'"
                                );
                                if (!$panels = db_fetch_array($panels_query)) {
                                    $sql_data = [
                                        'position' => 'horizontal',
                                        'entities_id' => $entity_id,
                                        'type' => 'item_pivot_tables_' . $reports['id'] . '_' . $fields_id,
                                        'is_active' => 1,
                                        'is_active_filters' => 1,
                                        'users_groups' => '',
                                        'width' => '',
                                        'sort_order' => 0,
                                    ];

                                    db_perform('app_filters_panels', $sql_data);
                                    $panels_id = db_insert_id();
                                } else {
                                    $panels_id = $panels['id'];
                                }

                                $html .= '<li><small>' . TEXT_ENTITY . ' "' . $app_entities_cache[$entity_id]['name'] . '":&nbsp;&nbsp;<a href="' . url_for(
                                        'ext/item_pivot_tables/filters',
                                        'reports_id=' . $reports_id . '&pivot_reports_id=' . $reports['id']
                                    ) . '">' . TEXT_FILTERS . '</a> | <a href="javascript: open_dialog(\'' . url_for(
                                        'reports/sorting',
                                        'redirect_to=item_pivot_tables&reports_id=' . $reports_id
                                    ) . '\')">' . TEXT_SORT_ORDER . '</a> | <a href="' . url_for(
                                        'ext/filters_panels/fields',
                                        'panels_id=' . $panels_id . '&entities_id=' . $entity_id . '&redirect_to=item_pivot_tables_' . $reports['id']
                                    ) . '">' . TEXT_FILTERS_PANELS . '</a></small></li>';
                            }

                            $html .= '</ul>';

                            echo $html;
                        }
                    }

                    ?></td>
                <td><?php
                    echo $entities_cache[$reports['entities_id']] ?></td>
                <td><?php
                    echo $entities_cache[$reports['related_entities_id']] ?></td>
                <td><?php
                    echo $reports['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>