<?php
echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
echo form_tag('configuration_form', url_for('custom_php/code', 'action=save'), ['class' => 'form-horizontal']) ?>
<?php
echo input_hidden_tag('is_folder', $obj['is_folder']) ?>
<?php
echo input_hidden_tag('is_crtl_s', 0) ?>
<?php
echo input_hidden_tag('code_id', (int)($_GET['id'] ?? 0)) ?>
<div class="modal-body  <?php
echo(!$obj['is_folder'] ? 'ajax-modal-width-1100' : '') ?>">
    <div class="form-body">


        <?php
        if ($obj['is_folder']) { ?>

            <?php
        if (db_count('app_custom_php', '1', 'is_folder')): ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_PARENT ?></label>
                <div class="col-md-9">
                    <?php
                    echo select_tag(
                        'parent_id',
                        custom_php::get_folder_choices(),
                        $obj['parent_id'],
                        ['class' => 'form-control input-large']
                    ) ?>
                </div>
            </div>
        <?php
        endif; ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_NAME ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag('name', $obj['name'], ['class' => 'form-control input-large required']) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="sort_order"><?php
                    echo TEXT_SORT_ORDER ?></label>
                <div class="col-md-9">
                    <?php
                    echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                </div>
            </div>

        <?php
        }else{ ?>

        <?php
        if (db_count('app_custom_php', '1', 'is_folder')): ?>
            <div class="form-group">
                <label class="col-md-2 control-label" for="name"><?php
                    echo TEXT_PARENT ?></label>
                <div class="col-md-10">
                    <?php
                    echo select_tag(
                        'parent_id',
                        custom_php::get_folder_choices(),
                        $obj['parent_id'],
                        ['class' => 'form-control input-large']
                    ) ?>
                </div>
            </div>
        <?php
        endif; ?>

            <div class="form-group">
                <label class="col-md-2 control-label" for="is_active"><?php
                    echo TEXT_IS_ACTIVE ?></label>
                <div class="col-md-10">
                    <p class="form-control-static"><?php
                        echo input_checkbox_tag('is_active', 1, ['checked' => ($obj['is_active'] == 1 ? 'checked' : '')]
                        ) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label" for="name"><?php
                    echo TEXT_NAME ?></label>
                <div class="col-md-10">
                    <?php
                    echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="name"><?php
                    echo TEXT_ADMINISTRATOR_NOTE ?></label>
                <div class="col-md-10">
                    <?php
                    echo textarea_tag('notes', $obj['notes'], ['class' => 'form-control input-xlarge textarea-small']
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label" for="sort_order"><?php
                    echo TEXT_SORT_ORDER ?></label>
                <div class="col-md-10">
                    <?php
                    echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-xsmall']) ?>
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-2 control-label" for="name"><?php
                    echo tooltip_icon(TEXT_F11_FULLSCREEN) . TEXT_PHP_CODE ?></label>
                <div class="col-md-10" style="min-height: 400px;">
                    <?php
                    echo textarea_tag('code', $obj['code'], ['class' => '']) ?>
                    <label id="code-error" class="error" for="code"></label>
                </div>
            </div>

        <?php
        echo app_include_codemirror(['javascript', 'php', 'clike', 'css', 'xml']) ?>
            <script>
                var myCodeMirror = null
                setTimeout(function () {
                    myCodeMirror = CodeMirror.fromTextArea(document.getElementById('code'), {
                        mode: {
                            name: 'php',
                            startOpen: true
                        },
                        lineNumbers: true,
                        lineWrapping: true,
                        matchBrackets: true,
                        height: 400,
                        extraKeys: {
                            "F11": function (cm) {
                                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                            },
                            "Esc": function (cm) {
                                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                            },
                            "Ctrl-S": function () {
                                $('#is_crtl_s').val(1)
                                $('#configuration_form').submit();
                            }
                        }
                    });

                    myCodeMirror.setSize(null, 400);

                    myCodeMirror.on('change', function (cMirror) {
                        $('#code-error').html('')
                    });

                }, 300);
            </script>

        <?php
        } ?>


    </div>
</div>
<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#configuration_form').validate({
            ignore: '',
            submitHandler: function (form) {
                if ($('#is_crtl_s').val() == 1) {
                    $.ajax({
                        type: "POST",
                        url: $("#configuration_form").attr("action"),
                        data: $("#configuration_form").serializeArray()
                    }).done(function (data) {
                        $('#code_id').val(data)
                        $('#is_crtl_s').val(0)
                    });

                    return false;
                } else {
                    app_prepare_modal_action_loading(form)
                    return true;
                }
            },
            rules: {
                code: {
                    required: function () {
                        $('#code').val(myCodeMirror.getValue())
                        return true;
                    },
                    remote: {
                        type: "POST",
                        url: url_for('custom_php/code', 'action=validate'),
                        data: {
                            code_id: function () {
                                return $('#code_id').val()
                            }
                        },
                        beforeSend: function () {
                            $('#code-error').show().html('<div class="fa fa-spinner fa-spin"></div>')
                        },
                        complete: function (data) {
                            if (data.responseText.search('Fatal error') != -1) {
                                $('#code-error').show().html(data.responseText)
                            }

                        }
                    }
                }
            }
        });
    });
</script>         