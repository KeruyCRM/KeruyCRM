<?php

$entity_info = db_find('app_entities', $current_reports_info['entities_id']);

switch (true) {
    case strstr($app_redirect_to, 'pivot_tables'):
        require(component_path('default_filters/pivot_tables_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'public_map'):
        require(component_path('default_filters/public_map_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'resource_timeline_entities'):
        require(component_path('default_filters/resource_timeline_entities_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'resource_timeline'):
        require(component_path('default_filters/resource_timeline_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'rss_feed'):
        require(component_path('default_filters/rss_feed_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'process'):
        require(component_path('default_filters/process_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'report_page_block'):
        require(component_path('default_filters/report_page_block_breadcrumb'));
        break;
    case strstr($app_redirect_to, 'report_page'):
        require(component_path('default_filters/report_page_breadcrumb'));
        break;
}
?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>

<?php

$reports_list[] = $current_reports_info['id'];
$reports_list = reports::get_parent_reports($current_reports_info['id'], $reports_list);

foreach ($reports_list as $reports_id) {
    $report_info = db_find('app_reports', $reports_id);
    $entity_info = db_find('app_entities', $report_info['entities_id']);

    $parent_reports_param = '';
    if ($current_reports_info['id'] != $reports_id) {
        $parent_reports_param = '&parent_reports_id=' . $reports_id;
    }
    ?>

    <div class="panel panel-default">
        <div class="panel-heading"><?php
            echo TEXT_FILTERS_FOR_ENTITY . ': <b>' . $entity_info['name'] . '</b>' ?></div>
        <div class="panel-body">

            <?php
            echo button_tag(
                TEXT_BUTTON_ADD_NEW_REPORT_FILTER,
                url_for(
                    'default_filters/filters_form',
                    'redirect_to=' . $app_redirect_to . '&reports_id=' . $current_reports_info['id'] . $parent_reports_param
                )
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
                            echo TEXT_FILTERS_CONDITION ?></th>
                        <th><?php
                            echo TEXT_VALUES ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (db_count('app_reports_filters', $reports_id, 'reports_id') == 0) {
                        echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
                    } ?>
                    <?php
                    $filters_query = db_query(
                        "select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input(
                            $reports_id
                        ) . "' order by rf.id"
                    );
                    while ($v = db_fetch_array($filters_query)):
                        ?>
                        <tr>
                            <td style="white-space: nowrap;"><?php
                                echo button_icon_delete(
                                        url_for(
                                            'default_filters/filters_delete',
                                            'redirect_to=' . $app_redirect_to . '&id=' . $v['id'] . '&reports_id=' . $current_reports_info['id'] . $parent_reports_param
                                        )
                                    ) . ' ' . button_icon_edit(
                                        url_for(
                                            'default_filters/filters_form',
                                            'redirect_to=' . $app_redirect_to . '&id=' . $v['id'] . '&reports_id=' . $current_reports_info['id'] . $parent_reports_param
                                        )
                                    ) ?></td>
                            <td><?php
                                echo fields_types::get_option($v['type'], 'name', $v['name']) ?></td>
                            <td><?php
                                echo reports::get_condition_name_by_key($v['filters_condition']) ?></td>
                            <td class="nowrap"><?php
                                echo reports::render_filters_values(
                                    $v['fields_id'],
                                    $v['filters_values'],
                                    '<br>',
                                    $v['filters_condition']
                                ) ?></td>
                        </tr>
                    <?php
                    endwhile ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <?php
}

switch (true) {
    case strstr($app_redirect_to, 'pivot_tables'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/pivot_tables/reports'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'public_map'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/map_reports/reports'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'resource_timeline_entities'):
        echo link_to(
            TEXT_BUTTON_BACK,
            url_for('ext/resource_timeline/entities', 'calendars_id=' . $resource_report_info['id']),
            ['class' => 'btn btn-default']
        );
        break;
    case strstr($app_redirect_to, 'resource_timeline'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/resource_timeline/reports'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'rss_feed'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/rss_feed/feeds'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'process'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/processes/processes'), ['class' => 'btn btn-default']);
        break;
    case strstr($app_redirect_to, 'report_page_block'):
        echo link_to(
            TEXT_BUTTON_BACK,
            url_for('ext/report_page/blocks&report_id=' . $process_report_info['id']),
            ['class' => 'btn btn-default']
        );
        break;
    case strstr($app_redirect_to, 'report_page'):
        echo link_to(TEXT_BUTTON_BACK, url_for('ext/report_page/reports'), ['class' => 'btn btn-default']);
        break;
}

?>
  


