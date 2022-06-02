<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'events',
    url_for(
        'ext/recurring_tasks/' . (strlen($app_redirect_to) ? $app_redirect_to : 'repeat'),
        'action=save' . (strlen(
            $app_path
        ) ? '&path=' . $app_path : '') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?php
                echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?php
                    echo input_checkbox_tag(
                        'is_active',
                        $obj['is_active'],
                        ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                    ) ?></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_type"><?php
                echo TEXT_EXT_EVENT_REPEAT_TYPE ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'repeat_type',
                    recurring_tasks::get_repeat_types(),
                    $obj['repeat_type'],
                    ['class' => 'form-control input-medium required', 'onChange' => 'display_repeat_days_by_type()']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_interval"><?php
                echo TEXT_EXT_EVENT_REPEAT_INTERVAL ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('repeat_interval', $obj['repeat_interval'], ['class' => 'form-control input-xsmall']) ?>
            </div>
        </div>

        <div class="form-group" id="repeat-days-form-group" style="display:none">
            <label class="col-md-3 control-label" for="repeat_days"><?php
                echo TEXT_EXT_EVENT_REPEAT_DAYS ?></label>
            <div class="col-md-9">
                <?php
                echo select_checkboxes_tag(
                    'repeat_days',
                    calendar::get_events_repeat_days(),
                    $obj['repeat_days'],
                    ['class' => 'form-control required']
                ) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_time"><?php
                echo TEXT_EXT_REPEAT_TIME ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag(
                    'repeat_time',
                    recurring_tasks::get_repeat_time_choices(),
                    $obj['repeat_time'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_start"><?php
                echo TEXT_EXT_REPEAT_START ?></label>
            <div class="col-md-9">
                <div class="input-group input-medium date datepicker">
                    <?php
                    echo input_tag('repeat_start', $obj['repeat_start'], ['class' => 'form-control required']) ?>
                    <span class="input-group-btn">
            <button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button>
          </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_end"><?php
                echo TEXT_EXT_REPEAT_END ?></label>
            <div class="col-md-9">
                <div class="input-group input-medium date datepicker">
                    <?php
                    echo input_tag('repeat_end', $obj['repeat_end'], ['class' => 'form-control']) ?>
                    <span class="input-group-btn">
            <button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button>
          </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="repeat_limit"><?php
                echo TEXT_EXT_EVENT_REPEAT_LIMIT ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('repeat_limit', $obj['repeat_limit'], ['class' => 'form-control input-xsmall']) ?>
                <?php
                echo tooltip_text(TEXT_EXT_NUMBER_REPETITIONS_INFO) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>

    $(function () {

        display_repeat_days_by_type();

        $('#events').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

    });

    function display_repeat_days_by_type() {
        if ($('#repeat_type').val() == 'weekly') {
            $('#repeat-days-form-group').fadeIn();
        } else {
            $('#repeat-days-form-group').fadeOut();
        }
    }

</script>  