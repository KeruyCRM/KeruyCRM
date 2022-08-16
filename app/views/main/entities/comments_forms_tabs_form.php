<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(
    (isset(\K::$fw->GET['id']) ? \K::$fw->TEXT_HEADING_EDIT_FORM_TAB : \K::$fw->TEXT_HEADING_NEW_FORM_TAB)
) ?>

<?= \Helpers\Html::form_tag(
    'forms_form',
    \Helpers\Urls::url_for(
        'main/entities/comments_form/save_tab',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">
        <?= \Helpers\Html::input_hidden_tag('entities_id', \K::$fw->GET['entities_id']) ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?= \K::$fw->TEXT_NAME ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::input_tag(
                    'name',
                    \K::$fw->obj['name'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#forms_form').validate();
    });
</script>