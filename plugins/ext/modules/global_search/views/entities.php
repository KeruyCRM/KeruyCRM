<h3 class="page-title"><?php
    echo TEXT_EXT_GLOBAL_SEARCH . ' / ' . TEXT_EXT_ENTITIES ?></h3>

<p><?php
    echo TEXT_EXT_GLOBAL_SEARCH_ENTITIES_INFO ?></p>

<?php
echo button_tag(TEXT_BUTTON_ADD, url_for('ext/global_search/form'), true) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?php
                echo TEXT_ACTION ?></th>
            <th width="100%"><?php
                echo TEXT_ENTITY ?></th>
            <th><?php
                echo TEXT_SEARCH_BY_FIELDS ?></th>
            <th><?php
                echo TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $items_query = db_query(
            "select gs.*, e.name from app_ext_global_search_entities gs, app_entities e where gs.entities_id=e.id order by gs.sort_order,gs.id"
        );


        if (db_num_rows($items_query) == 0) {
            echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        while ($items = db_fetch_array($items_query)):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?php
                    echo button_icon_delete(
                            url_for('ext/global_search/delete', 'id=' . $items['id'])
                        ) . ' ' . button_icon_edit(url_for('ext/global_search/form', 'id=' . $items['id'])) ?></td>
                <td><?php
                    echo $items['name'];
                    if (!isset($app_heading_fields_id_cache[$items['entities_id']])) {
                        echo '<div><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i>' . TEXT_ERROR_NO_HEADING_FIELD . '</i></div>';
                    }

                    ?></td>
                <td><?php
                    if (strlen($items['fields_for_search'])) {
                        foreach (explode(',', $items['fields_for_search']) as $field_id) {
                            echo $app_fields_cache[$items['entities_id']][$field_id]['name'] . '<br>';
                        }
                    }
                    ?></td>
                <td><?php
                    echo $items['sort_order'] ?></td>
            </tr>
        <?php
        endwhile ?>
        </tbody>
    </table>
</div>
