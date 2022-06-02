<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_PROCESSES,
        url_for('ext/processes/processes')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        $app_process_info['name'],
        url_for('ext/processes/actions', 'process_id=' . $app_process_info['id'])
    ) . '</li>';

if (isset($app_actions_info)) {
    $actions_types = processes::get_actions_types_choices($app_process_info['entities_id']);
    $breadcrumb[] = '<li><i class="fa fa-angle-right"></i>' . $actions_types[$app_actions_info['type']] . '</li>';
}
?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>