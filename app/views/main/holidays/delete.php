<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_HEADING_DELETE) ?>

<?= \Helpers\Html::form_tag(
    'delete',
    \Helpers\Urls::url_for('main/holidays/holidays/delete', 'id=' . \K::$fw->GET['id'])
) ?>
<div class="modal-body">
    <?= \K::$fw->TEXT_ARE_YOU_SURE ?>
</div>
<?= \Helpers\App::ajax_modal_template_footer(\K::$fw->TEXT_BUTTON_DELETE) ?>

</form>