<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
echo ajax_modal_template_header($field['name']) ?>

<div class="modal-body ajax-modal-width-790">
    <?php

    $cfg = new fields_types_cfg($field['configuration']);

    $options = [
        'field' => $field,
        'item' => $item,
    ];

    $items_by_query = new fieldtype_items_by_query();
    $mysql_query = $items_by_query->build_query($options);

    $items_query = db_query($mysql_query);
    $count_items = db_num_rows($items_query);

    if ($count_items > 0) {
        echo $items_by_query->get_items_list($items_query, $cfg);
    } else {
        echo TEXT_NO_RECORDS_FOUND;
    }

    ?>
</div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>

