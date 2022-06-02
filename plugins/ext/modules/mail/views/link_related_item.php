<?php
echo ajax_modal_template_header(TEXT_LINK_RECORD) ?>

<?php
$entity_info = db_find('app_entities', _get::int('entities_id'));
?>

<?php
echo form_tag(
    'add_related_items',
    url_for(
        'ext/mail/related_item',
        'action=add_related_item&entities_id=' . _get::int('entities_id') . '&mail_groups_id=' . _get::int(
            'mail_groups_id'
        )
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body ajax-modal-width-790">

    <div class="form-group">
        <label class="col-md-4 control-label"><?php
            echo $entity_info['name'] ?>:</label>
        <div class="col-md-8">
            <?php
            echo select_entities_tag(
                'items[]',
                [],
                '',
                ['entities_id' => $entity_info['id'], 'class' => 'required', 'multiple' => 'multiple']
            ) ?>
        </div>
    </div>

</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $("#add_related_items").validate({
            ignore: "",
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });


    })
</script>
    
    
 
