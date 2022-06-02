<h3 class="form-title"><?php
    echo(strlen($public_form['page_title']) > 0 ? $public_form['page_title'] : $public_form['name']) ?></h3>

<?php
echo app_alert_warning(
    (strlen($public_form['inactive_message']) ? $public_form['inactive_message'] : TEXT_PAGE_NOT_FOUND_HEADING)
) ?>
