<?php
echo ajax_modal_template_header(TEXT_LOGIN_AS_USER) ?>

    <div class="modal-body">
        <?php
        foreach (explode(',', $app_user['multiple_access_groups']) as $group_id) {
            if ($group_id != $app_user['group_id']) {
                echo '<a href="' . url_for(
                        'users/change_access_group',
                        'action=change&id=' . $group_id
                    ) . '" class="btn btn-primary btn-block">' . access_groups::get_name_by_id($group_id) . '</a>';
            }
        }
        ?>

    </div>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>