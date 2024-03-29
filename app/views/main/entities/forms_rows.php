<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_ROW) ?>

<?= \Helpers\Html::form_tag(
    'fields_form',
    \Helpers\Urls::url_for(
        'main/entities/forms_rows/save',
        (isset(\K::$fw->GET['id']) ? '&id=' . \K::$fw->GET['id'] : '') . '&entities_id=' . \K::$fw->GET['entities_id'] . '&forms_tabs_id=' . \K::$fw->GET['forms_tabs_id']
    ),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <div class="form-group">
            <label class="col-md-3 control-label" for="columns"><?= \K::$fw->TEXT_COUNT_OF_COLUMNS ?></label>
            <div class="col-md-9">
                <?= \Helpers\Html::select_tag(
                    'columns',
                    ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6],
                    \K::$fw->obj['columns'],
                    ['class' => 'form-control input-small']
                ) ?>
            </div>
        </div>
        <?php
        for ($i = 1; $i <= 6; $i++) { ?>
            <div class="form-group form-group-column-<?= $i ?>">
                <label class="col-md-3 control-label" for="columns"><?= \K::$fw->TEXT_COLUMN_WIDTH . " {$i}:" ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'column' . $i . '_width',
                        [
                            '1' => '1/12',
                            '2' => '2/12',
                            '3' => '3/12',
                            '4' => '4/12',
                            '5' => '5/12',
                            '6' => '6/12',
                            '7' => '7/12',
                            '8' => '8/12',
                            '9' => '9/12',
                            '10' => '10/12',
                            '11' => '11/12',
                            '12' => '12/12'
                        ],
                        \K::$fw->obj['column' . $i . '_width'],
                        ['class' => 'form-control input-small column-width', 'column_num' => $i]
                    ) ?>
                </div>
            </div>
            <?php
        } ?>
        <div class="form-group">
            <label class="col-md-3 control-label"
                   for="field_name_new_row"><?= \K::$fw->TEXT_FIELD_NAME_IN_NEW_ROW ?></label>
            <div class="col-md-9">
                <p class="form-control-static"><?= \Helpers\Html::input_checkbox_tag(
                        'field_name_new_row',
                        '1',
                        ['checked' => \K::$fw->obj['field_name_new_row']]
                    ) ?></p>
            </div>
        </div>

        <h3 class="form-section"><?= \K::$fw->TEXT_PREVIEW ?></h3>

        <div class="row">
            <div id="preview_column1" class="col-md-<?= \K::$fw->obj['column1_width'] ?>">
                <div class="well">1</div>
            </div>
            <div id="preview_column2" class="col-md-<?= \K::$fw->obj['column2_width'] ?>">
                <div class="well">2</div>
            </div>
            <div id="preview_column3" class="col-md-<?= \K::$fw->obj['column3_width'] ?>">
                <div class="well">3</div>
            </div>
            <div id="preview_column4" class="col-md-<?= \K::$fw->obj['column4_width'] ?>">
                <div class="well">4</div>
            </div>
            <div id="preview_column5" class="col-md-<?= \K::$fw->obj['column5_width'] ?>">
                <div class="well">5</div>
            </div>
            <div id="preview_column6" class="col-md-<?= \K::$fw->obj['column6_width'] ?>">
                <div class="well">6</div>
            </div>
        </div>

        <p><?= \K::$fw->TEXT_FORMS_ROWS_INFO ?></p>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#fields_form').validate({
            ignore: '',
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                form.submit();
            }
        });

        count_columns_change(false)

        $('#columns').change(function () {
            count_columns_change(true)
        })

        $('.column-width').change(function () {
            column_num = $(this).attr('column_num')
            width = $(this).val();
            $('#preview_column' + column_num).removeClass("col-md-12 col-md-11 col-md-10 col-md-9 col-md-8 col-md-7 col-md-6 col-md-5 col-md-4 col-md-3 col-md-2 col-md-1").addClass('col-md-' + width)
        })
    });

    function count_columns_change(auto_widht) {
        count_columns = $('#columns').val()

        for (i = 1; i <= 6; i++) {
            if (i <= count_columns) {
                $('.form-group-column-' + i).show()
                $('#preview_column' + i).show()
            } else {
                $('.form-group-column-' + i).hide()
                $('#preview_column' + i).hide()
            }
        }

        if (!auto_widht) return true;

        switch (count_columns) {
            case '1':
                $('#column1_width').val(12).trigger('change')
                break;
            case '2':
                $('#column1_width').val(6).trigger('change')
                $('#column2_width').val(6).trigger('change')
                break;
            case '3':
                $('#column1_width').val(4).trigger('change')
                $('#column2_width').val(4).trigger('change')
                $('#column3_width').val(4).trigger('change')
                break;
            case '4':
                $('#column1_width').val(3).trigger('change')
                $('#column2_width').val(3).trigger('change')
                $('#column3_width').val(3).trigger('change')
                $('#column4_width').val(3).trigger('change')
                break;
            case '5':
                $('#column1_width').val(2).trigger('change')
                $('#column2_width').val(2).trigger('change')
                $('#column3_width').val(2).trigger('change')
                $('#column4_width').val(2).trigger('change')
                $('#column5_width').val(2).trigger('change')
                break;
            case '6':
                $('#column1_width').val(2).trigger('change')
                $('#column2_width').val(2).trigger('change')
                $('#column3_width').val(2).trigger('change')
                $('#column4_width').val(2).trigger('change')
                $('#column5_width').val(2).trigger('change')
                $('#column6_width').val(2).trigger('change')
                break;
        }
    }
</script>