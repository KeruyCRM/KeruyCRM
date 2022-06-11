<?php
echo ajax_modal_template_header(TEXT_UNLINK) ?>

<?php
echo form_tag(
    'remove_related_items',
    url_for(
        'ext/mail/related_item',
        'action=remove_related_items&entities_id=' . _get::int('entities_id') . '&mail_groups_id=' . _get::int(
            'mail_groups_id'
        )
    )
) ?>

<div class="modal-body">
    <p><?php
        echo TEXT_PLEASE_SELECT_ITEMS ?></p>
    <?php

    $related_entities_id = _get::int('entities_id');

    $related_items = [];
    $related_items[] = 0;

    $items_query = db_query(
        "select items_id from app_ext_mail_to_items where mail_groups_id='" . _get::int(
            'mail_groups_id'
        ) . "' and entities_id='" . $related_entities_id . "'"
    );
    while ($items = db_fetch_array($items_query)) {
        $related_items[] = $items['items_id'];
    }

    $listing_sql_query = '';
    $listing_sql_query_join = '';

    //check view assigned only access
    $listing_sql_query = items::add_access_query($related_entities_id, $listing_sql_query);

    //include access to parent records
    $listing_sql_query .= items::add_access_query_for_parent_entities($related_entities_id);

    $listing_sql_query .= " and e.id in (" . implode(',', $related_items) . ")";

    $listing_sql_query .= items::add_listing_order_query_by_entity_id($related_entities_id);

    $items_sql_query = "select * from app_entity_" . $related_entities_id . " e " . $listing_sql_query_join . " where id>0 " . $listing_sql_query;
    $items_query = db_query($items_sql_query);

    if (db_num_rows($items_query) > 0) {
        echo '<div><label>' . input_checkbox_tag(
                'select_all_related_items',
                '1'
            ) . ' ' . TEXT_SELECT_ALL . '</label></div>';
    }

    while ($items = db_fetch_array($items_query)) {
        $path_info = items::get_path_info($related_entities_id, $items['id']);

        echo '<div>' . input_checkbox_tag('items[]', $items['id'], ['class' => 'remove_related_item']
            ) . ' <a href="' . url_for(
                'items/info',
                'path=' . $path_info['full_path']
            ) . '" target="_blank">' . items::get_heading_field($related_entities_id, $items['id']) . '</a></div>';
    }

    echo input_hidden_tag('related_entities_id', $related_entities_id);
    ?>

</div>

<?php
echo ajax_modal_template_footer(TEXT_UNLINK) ?>

</form>

<script>
    $("#select_all_related_items").change(function () {
        select_all_by_classname("select_all_related_items", "remove_related_item");
    });
</script>