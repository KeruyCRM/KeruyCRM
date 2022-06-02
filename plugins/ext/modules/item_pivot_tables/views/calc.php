<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_ITEM_PIVOT_TABLES,
        url_for('ext/item_pivot_tables/reports')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $reports['name'] . '</li>';
?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>

<h3 class="page-title"><?php
    echo TEXT_EXT_CALCULATIONS ?></h3>

<p><?php
    echo TEXT_EXT_ITEM_PIVOT_TABLES_CALC_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_CREATE,
    url_for('ext/item_pivot_tables/calc_form', 'reports_id=' . _get::int('reports_id')),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th><?php
                echo TEXT_ID ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $reports_query = db_query(
            "select * from app_ext_item_pivot_tables_calcs where type='calc' and reports_id='" . _get::int(
                'reports_id'
            ) . "' order by name"
        );

        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/item_pivot_tables/calc_delete',
                                'reports_id=' . _get::int('reports_id') . '&id=' . $reports['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/item_pivot_tables/calc_form',
                                'reports_id=' . _get::int('reports_id') . '&id=' . $reports['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $reports['id'] ?></td>
                <td><?php
                    echo $reports['name'] ?></td>
                <td><?php
                    echo $reports['select_query'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>


<h3 class="page-title"><?php
    echo TEXT_COLUMNS ?></h3>

<?php
echo button_tag(
    TEXT_BUTTON_CREATE,
    url_for('ext/item_pivot_tables/column_form', 'reports_id=' . _get::int('reports_id')),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_NAME ?></th>
            <th><?php
                echo TEXT_FORMULA ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php

        $reports_query = db_query(
            "select * from app_ext_item_pivot_tables_calcs where type='column' and reports_id='" . _get::int(
                'reports_id'
            ) . "' order by sort_order, name"
        );

        if (db_num_rows($reports_query) == 0) {
            echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($reports = db_fetch_array($reports_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for(
                                'ext/item_pivot_tables/calc_delete',
                                'reports_id=' . _get::int('reports_id') . '&id=' . $reports['id']
                            )
                        ) . ' ' . button_icon_edit(
                            url_for(
                                'ext/item_pivot_tables/column_form',
                                'reports_id=' . _get::int('reports_id') . '&id=' . $reports['id']
                            )
                        ) ?></td>
                <td><?php
                    echo $reports['name'] ?></td>
                <td><?php
                    echo $reports['formula'] ?></td>
                <td><?php
                    echo $reports['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>


<?php
echo '<a href="' . url_for(
        'ext/item_pivot_tables/reports'
    ) . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>
