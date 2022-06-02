<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_CHANGE_HISTORY,
        url_for('ext/track_changes/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $app_reports['name'] . '<i class="fa fa-angle-right"></i></li>';
$breadcrumb[] = '<li>' . TEXT_EXT_ENTITIES . '</li>';

?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>