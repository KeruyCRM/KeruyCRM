<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_ext_item_pivot_tables_calcs', $_GET['id']); ?>

<?php
echo form_tag(
    'login',
    url_for('ext/item_pivot_tables/calc', 'action=delete&reports_id=' . _get::int('reports_id') . '&id=' . $_GET['id'])
) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $obj['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>    