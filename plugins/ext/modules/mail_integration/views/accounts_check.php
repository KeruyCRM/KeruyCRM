<?php
$account = db_find('app_ext_mail_accounts', $_GET['id']); ?>

<?php
echo ajax_modal_template_header($account['name']) ?>

<form class="form-horizontal">
    <div class="modal-body">
        <div class="form-body">

            <div class="form-group">
                <label class="col-md-4 control-label" for="name"><?php
                    echo TEXT_EXT_IMAP_SERVER ?></label>
                <div class="col-md-8">
                    <p class="form-control-static"><?php
                        echo $account['imap_server'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="name"><?php
                    echo TEXT_EXT_MAILBOX ?></label>
                <div class="col-md-8">
                    <p class="form-control-static"><?php
                        echo $account['mailbox'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="name"><?php
                    echo TEXT_USERNAME ?></label>
                <div class="col-md-8">
                    <p class="form-control-static"><?php
                        echo $account['login'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="name"><?php
                    echo TEXT_STATUS ?></label>
                <div class="col-md-8">
                    <?php
                    if ($conn = imap_open(
                        "{" . $account['imap_server'] . "}" . $account['mailbox'],
                        $account['login'],
                        $account['password'],
                        OP_READONLY
                    )) {
                        echo '<div class="alert alert-success">' . TEXT_OK . '</div>';
                    } else {
                        echo '<div class="alert alert-danger">' . imap_last_error() . '</div>';
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
</form>

<?php
echo ajax_modal_template_footer('hide-save-button') ?>