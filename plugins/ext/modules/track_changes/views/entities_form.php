<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'track_changes_form',
    url_for(
        'ext/track_changes/entities',
        'action=save&reports_id=' . _get::int('reports_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <?php
        $choices = ['' => ''] + entities::get_choices();
        ?>
        <div class="form-group">
            <label class="col-md-3 control-label" for="entities_id"><?php
                echo TEXT_REPORT_ENTITY ?></label>
            <div class="col-md-9"><?php
                echo select_tag(
                    'entities_id',
                    $choices,
                    $obj['entities_id'],
                    ['class' => 'form-control input-large required']
                ) ?>
            </div>
        </div>

        <div id="available_fields"></div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#track_changes_form').validate({ignore: ''});

        load_available_fields();

        $('#entities_id').change(function () {
            load_available_fields();
        })
    });


    function load_available_fields() {
        $('#available_fields').html('');
        $('#available_fields').addClass('ajax-loading');
        $('#available_fields').load('<?php echo url_for(
            "ext/track_changes/entities",
            "action=get_available_fields&reports_id=" . _get::int('reports_id') . "&id=" . $obj['id']
        ) ?>', {entities_id: $('#entities_id').val()}, function () {
            $('#available_fields').removeClass('ajax-loading');
        })
    }
</script>   
    
 
