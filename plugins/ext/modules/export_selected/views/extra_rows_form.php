<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag(
    'templates_form',
    url_for(
        'ext/export_selected/extra_rows',
        'templates_id=' . _GET(
            'templates_id'
        ) . '&action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '') . '&row_id=' . $row_info['id']
    ),
    ['class' => 'form-horizontal']
) ?>

<div class="modal-body">
    <div class="form-body">

        <?php
        $settings = new settings($obj['settings']); ?>


        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_HEADING ?></label>
            <div class="col-md-9"><?php
                echo input_tag('settings[heading]', $settings->get('heading'), ['class' => 'form-control input-large']
                ) ?></div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9"><?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?></div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_MERGE_CELLS . ' (colspan)' ?></label>
            <div class="col-md-9"><?php
                echo input_tag(
                        'settings[colspan]',
                        $settings->get('colspan'),
                        ['class' => 'form-control input-xsmall number']
                    ) . tooltip_text(TEXT_EXT_MERGE_CELLS_INFO) ?></div>
        </div>

        <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_FONT_SIZE ?></label>
            <div class="col-md-9"><?php
                echo input_tag(
                    'settings[heading_font_size]',
                    $settings->get('heading_font_size', ''),
                    ['class' => 'form-control input-small number']
                ) ?></div>
        </div>

        <?php
        $font_style_choices = [
            'bold' => '<i class="fa fa-bold" aria-hidden="true"></i>',
            'italic' => '<i class="fa fa-italic" aria-hidden="true"></i>',
            'underline' => '<i class="fa fa-underline" aria-hidden="true"></i>',
        ];
        ?>
        <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_FONT_STYLE ?></label>
            <div class="col-md-9"><?php
                echo select_checkboxes_button(
                    'settings[heading_font_style]',
                    $font_style_choices,
                    $settings->get('heading_font_style', '')
                ) ?></div>
        </div>

        <?php
        $alignment_choices = [
            'left' => '<i class="fa fa-align-left" aria-hidden="true"></i>',
            'center' => '<i class="fa fa-align-center" aria-hidden="true"></i>',
            'right' => '<i class="fa fa-align-right" aria-hidden="true"></i>',
        ];
        ?>
        <div class="form-group settings-list">
            <label class="col-md-3 control-label" for="fields_id"><?php
                echo TEXT_EXT_ALIGNMENT ?></label>
            <div class="col-md-9"><?php
                echo select_radioboxes_button(
                    'settings[heading_alignment]',
                    $alignment_choices,
                    $settings->get('heading_alignment', 'left')
                ) ?></div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#templates_form').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });
    });
</script>