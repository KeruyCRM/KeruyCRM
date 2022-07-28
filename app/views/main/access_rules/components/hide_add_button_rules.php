<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<br>
<h3 class="page-title"><?= \K::$fw->TEXT_HIDE_ADD_BUTTON_RULES ?></h3>

<p><?= sprintf(
        \K::$fw->TEXT_HIDE_ADD_BUTTON_RULES_INFO,
        \K::$fw->parent_entities_info['name'],
        \K::$fw->entities_info['name']
    ) ?></p>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD_NEW_REPORT_FILTER,
    \Helpers\Urls::url_for(
        'main/access_rules/parent_filters_form',
        'reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    )
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th width="100%"><?= \K::$fw->TEXT_FIELD ?></th>
            <th><?= \K::$fw->TEXT_FILTERS_CONDITION ?></th>
            <th><?= \K::$fw->TEXT_VALUES ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (\K::$fw->db_count == 0) {
            echo '<tr><td colspan="5">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        //while ($v = db_fetch_array($filters_query)):
        foreach (\K::$fw->filters_query as $v):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for(
                            'main/access_rules/parent_filters_delete',
                            'id=' . $v['id'] . '&reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                        )
                    ) . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for(
                            'main/access_rules/parent_filters_form',
                            'id=' . $v['id'] . '&reports_id=' . \K::$fw->reports_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                        )
                    ) ?></td>
                <td><?= \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) ?></td>
                <td><?= \Models\Main\Reports\Reports::get_condition_name_by_key($v['filters_condition']) ?></td>
                <td class="nowrap"><?= \Models\Main\Reports\Reports::render_filters_values(
                        $v['fields_id'],
                        $v['filters_values'],
                        '<br>',
                        $v['filters_condition']
                    ) ?></td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>