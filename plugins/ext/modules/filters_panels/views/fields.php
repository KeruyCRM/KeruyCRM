<?php

switch (true) {
    case strstr($app_redirect_to, 'item_pivot_tables_'):
        require(component_path('ext/item_pivot_tables/filters_panels_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'pivot_tables'):
        require(component_path('ext/filters_panels/pivot_tables_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'calendar_report_'):
        require(component_path('ext/calendar/filters_panels_breadcrumb'));
        break;
}

?>

    <h3 class="page-title"><?php
        echo TEXT_FIELDS_CONFIGURATION ?></h3>

    <p><?php
        echo TEXT_PANES_FILTERS_FIELDS_CONFIGURATION_INFO ?></p>

<?php
echo button_tag(
        TEXT_BUTTON_ADD,
        url_for(
            'ext/filters_panels/fields_form',
            'panels_id=' . $panels_id . '&redirect_to=' . $app_redirect_to . '&entities_id=' . $_GET['entities_id']
        )
    ) . ' ' . button_tag(
        TEXT_BUTTON_SORT,
        url_for(
            'ext/filters_panels/fields_sort',
            'entities_id=' . $_GET['entities_id'] . '&redirect_to=' . $app_redirect_to . '&panels_id=' . $_GET['panels_id']
        ),
        true,
        ['class' => 'btn btn-default']
    ) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?php
                    echo TEXT_ACTION ?></th>
                <th width="100%"><?php
                    echo TEXT_FIELD ?></th>
                <th><?php
                    echo TEXT_DISPLAY_AS ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $fields_query = db_query(
                "select pf.*, f.name as field_name, f.type as field_type from app_filters_panels_fields pf, app_fields f where pf.fields_id=f.id and pf.panels_id='" . _get::int(
                    'panels_id'
                ) . "' order by pf.sort_order"
            );

            if (db_num_rows($fields_query) == 0) {
                echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
            }

            while ($fields = db_fetch_array($fields_query)):
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?php
                        echo button_icon_delete(
                                url_for(
                                    'ext/filters_panels/fields_delete',
                                    'panels_id=' . $panels_id . '&redirect_to=' . $app_redirect_to . '&id=' . $fields['id'] . '&entities_id=' . $_GET['entities_id']
                                )
                            ) . ' ' . button_icon_edit(
                                url_for(
                                    'ext/filters_panels/fields_form',
                                    'panels_id=' . $panels_id . '&redirect_to=' . $app_redirect_to . '&id=' . $fields['id'] . '&entities_id=' . $_GET['entities_id']
                                )
                            ) ?></td>
                    <td><?php
                        echo fields_types::get_option($fields['field_type'], 'name', $fields['field_name']) ?></td>
                    <td><?php
                        echo filters_panels::get_field_display_type_name($fields['display_type']) ?></td>
                </tr>
            <?php
            endwhile ?>
            </tbody>
        </table>
    </div>

<?php

switch (true) {
    case strstr($app_redirect_to, 'item_pivot_tables_'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/item_pivot_tables/reports'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'pivot_tables'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/pivot_tables/reports'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'calendar_report_'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/calendar/configuration_reports'), ['class' => 'btn btn-default']);
        break;
}

?>