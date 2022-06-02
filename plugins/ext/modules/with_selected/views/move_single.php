<?php
echo ajax_modal_template_header(TEXT_HEADING_MOVE) ?>

<?php
echo form_tag('form-move-to', url_for('ext/with_selected/move_single', 'action=move_single&path=' . $_GET['path'])) ?>

<div class="modal-body">
    <div id="modal-body-content">
        <p><?php
            echo TEXT_MOVE_SINGLE_CONFIRMATION ?></p>

        <?php
        $entity_info = db_find('app_entities', $current_entity_id);
        if ($entity_info['parent_id'] > 0) {
            $report_info = reports::create_default_entity_report($entity_info['id'], 'entity_menu');

            echo '
      <p>' . TEXT_MOVE_TO . '</p>
      <p>' . select_entities_tag(
                    'move_to',
                    [],
                    '',
                    [
                        'entities_id' => $entity_info['parent_id'],
                        'class' => 'form-control required',
                        'data-placeholder' => TEXT_ENTER_VALUE
                    ]
                ) . '</p>
    ';
        }

        ?>
    </div>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_MOVE) ?>


</form>

<script>
    $(function () {
        $('#form-move-to').validate({
            ignore: '',
            submitHandler: function (form) {
                $('button[type=submit]', form).css('display', 'none')
                $('#modal-body-content').css('visibility', 'hidden').css('height', '1px');
                $('#modal-body-content').after('<div class="ajax-loading"></div>');

                $('#modal-body-content').load($(form).attr('action'), $(form).serializeArray(), function () {
                    $('.ajax-loading').css('display', 'none');
                    $('#modal-body-content').css('visibility', 'visible').css('height', 'auto');
                })

                return false;
            }
        })
    })
</script>