<?php
echo ajax_modal_template_header(TEXT_HEADING_MOVE) ?>

<?php
echo form_tag(
    'form-move-to',
    url_for('ext/with_selected/move', 'action=move_selected&reports_id=' . $_GET['reports_id'])
) ?>

<?php
echo input_hidden_tag('redirect_to', $app_redirect_to) ?>

<?php
if (!isset($app_selected_items[$_GET['reports_id']])) {
    $app_selected_items[$_GET['reports_id']] = [];
}

if (count($app_selected_items[$_GET['reports_id']]) == 0) {
    echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
} else {
    ?>

    <div class="modal-body">
        <div id="modal-body-content">
            <p><?php
                echo TEXT_MOVE_CONFIRMATION ?></p>

            <?php
            $entity_info = db_find('app_entities', $reports_info['entities_id']);
            if ($entity_info['parent_id'] > 0) {
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
    $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));
    echo ajax_modal_template_footer(TEXT_BUTTON_MOVE, '', $count_selected_text);
    ?>

<?php
} ?>
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