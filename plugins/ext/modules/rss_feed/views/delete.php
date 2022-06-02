<?php
echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php
$obj = db_find('app_ext_rss_feeds', $_GET['id']); ?>

<?php
echo form_tag('login', url_for('ext/rss_feed/feeds', 'action=delete&id=' . $_GET['id'])) ?>

<div class="modal-body">
    <?php
    echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION, $obj['name']) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>   