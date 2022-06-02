<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'configuration_form',
    url_for(
        'ext/email_sending/blocks',
        'action=save&entities_id=' . _get::int('entities_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('entities_id', _get::int('entities_id')) ?>
<div class="modal-body">
    <div class="form-body">

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME; ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12" style="padding-top: 5px;">
                <?php
                echo textarea_tag(
                    'description',
                    $obj['description'],
                    ['class' => 'form-control input-xlarge full-editor', 'editor-height' => 350]
                ); ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form> 

  