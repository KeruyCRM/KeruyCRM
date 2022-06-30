<?php
echo ajax_modal_template_header(TEXT_EXT_SHARE_THIS_CALENDAR) ?>

<?php
echo form_tag('form_ics', icalendar::generate_url($_GET['type'], $_GET['id'] ?? 0) . '&download=1') ?>
<div class="modal-body">
    <p><?php
        echo TEXT_EXT_SHARE_THIS_CALENDAR_TIP ?></p>

    <?php
    echo textarea_tag(
        'url',
        icalendar::generate_url($_GET['type'], $_GET['id'] ?? 0) . '#' . time(),
        ['class' => 'form-control textarea-small select-all', 'readonly' => 'readonly']
    ) ?>
    <?php
    echo tooltip_text(TEXT_EXT_ENABLE_ICAL_GMT_TIP) ?>
</div>

<?php
echo ajax_modal_template_footer(TEXT_DOWNLOAD . ' .ics') ?>
</form>