<?php
$obj = db_find('app_ext_public_forms', $_GET['id']); ?>
<?php
echo ajax_modal_template_header(TEXT_EXT_PUBLIC_FORM . ' "' . $obj['name'] . '"') ?>

<?php
echo form_tag('public_forms', '', ['class' => 'form-horizontal']) ?>

    <div class="modal-body ajax-modal-width-790">
        <div class="form-body">

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_URL ?></label>
                <div class="col-md-9">
                    <?php
                    echo textarea_tag(
                        'url',
                        url_for('ext/public/form', 'id=' . $obj['id']),
                        ['class' => 'form-control input-xlarge select-all', 'style' => 'min-height:60px;']
                    ) ?>
                </div>
            </div>

            <?php
            $iframe_html = '<iframe src="' . url_for(
                    'ext/public/form',
                    'id=' . $obj['id']
                ) . '" width="100%" height="300"  frameborder="0" scrolling="auto" onLoad="window.scrollTo(0, 0)"></iframe>'; ?>
            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo tooltip_icon(TEXT_EXT_PB_IFRAME_INFO) . TEXT_IFRAME ?></label>
                <div class="col-md-9">
                    <?php
                    echo textarea_tag(
                        'url',
                        $iframe_html,
                        ['class' => 'form-control input-xlarge select-all', 'style' => 'min-height: 90px;']
                    ) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_EXTRA ?></label>
                <div class="col-md-9">
                    <?php
                    echo TEXT_EXT_PB_URL_PARAMS_INFO ?>
                </div>
            </div>

            <?php
            if ($obj['check_enquiry'] == 1): ?>
                <p class="form-section"><?php
                    echo TEXT_EXT_PB_CHECK_ENQUIRY ?></p>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo TEXT_URL ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'url',
                            url_for('ext/public/check', 'id=' . $obj['id']),
                            ['class' => 'form-control input-xlarge select-all', 'style' => 'min-height:60px;']
                        ) ?>
                    </div>
                </div>

                <?php
                $iframe_html = '<iframe src="' . url_for(
                        'ext/public/check',
                        'id=' . $obj['id']
                    ) . '" width="100%" height="300"  frameborder="0" scrolling="auto" onLoad="window.scrollTo(0, 0)"></iframe>'; ?>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name"><?php
                        echo tooltip_icon(TEXT_EXT_PB_IFRAME_INFO) . TEXT_IFRAME ?></label>
                    <div class="col-md-9">
                        <?php
                        echo textarea_tag(
                            'url',
                            $iframe_html,
                            ['class' => 'form-control input-xlarge select-all', 'style' => 'min-height: 90px;']
                        ) ?>
                    </div>
                </div>

            <?php
            endif ?>

        </div>
    </div>
<?php
echo ajax_modal_template_footer('hide-save-button') ?>