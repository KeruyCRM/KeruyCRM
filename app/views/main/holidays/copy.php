<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_COPY) ?>

<?= \Helpers\Html::form_tag(
    'form-copy-to',
    \Helpers\Urls::url_for('main/holidays/copy/copy_selected'),
    ['class' => 'form-horizontal']
) ?>
<?= \Helpers\Html::input_hidden_tag('selected_items') ?>
<div class="modal-body">
    <div id="modal-body-content">

        <div class="form-group">
            <label class="col-md-4 control-label" for="type"><?= \K::$fw->TEXT_COPY_TO ?></label>
            <div class="col-md-8">
                <?= \Helpers\Html::input_tag(
                    'year',
                    date('Y') + 1,
                    ['class' => 'form-control input-small', 'type' => 'number']
                ) ?>
            </div>
        </div>

    </div>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_COPY) ?>

</form>

<script>
    $(function () {
        if ($('.holidays_checkbox:checked').length == 0) {
            $('#modal-body-content').html('<?= \K::$fw->TEXT_PLEASE_SELECT_ITEMS ?>')
            $('.btn-primary-modal-action').hide()
        } else {
            selected_fields_list = $('.holidays_checkbox:checked').serialize().replace(/holidays%5B%5D=/g, '').replace(/&/g, ',');
            $('#selected_items').val(selected_fields_list);
        }

        $('#form-copy-to').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });
    })
</script>