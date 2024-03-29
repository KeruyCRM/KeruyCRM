<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
//require(component_path('entities/navigation'))
\K::view()->render(\Helpers\Urls::components_path('main/entities/navigation'));
?>

<?php
$default_selector = ['1' => \K::$fw->TEXT_YES, '0' => \K::$fw->TEXT_NO]; ?>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/entities/entities_configuration/save', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'form-horizontal']
) ?>

<div class="tabbable tabbable-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#general_info" data-toggle="tab"><?= \K::$fw->TEXT_TITLES ?></a></li>
        <li><a href="#comments_configuration" data-toggle="tab"><?= \K::$fw->TEXT_COMMENTS_TITLE ?></a></li>
        <li><a href="#redirects_configuration" data-toggle="tab"><?= \K::$fw->TEXT_REDIRECTS ?></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="general_info">
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_menu_title"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_MENU_TITLE_TOOLTIP
                    ) . \K::$fw->TEXT_MENU_TITLE; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[menu_title]',
                        \K::$fw->cfg->get('menu_title'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_menu_title"><?= \K::$fw->TEXT_MENU_ICON_TITLE; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[menu_icon]',
                        \K::$fw->cfg->get('menu_icon'),
                        ['class' => 'form-control input-large']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?= \K::$fw->TEXT_COLOR ?></label>
                <div class="col-md-9">
                    <table>
                        <tr>
                            <td>
                                <?= \Helpers\Html::input_color(
                                    'cfg[menu_icon_color]',
                                    \K::$fw->cfg->get('menu_icon_color')
                                ) ?>
                                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_ICON) ?>
                            </td>
                            <td style="padding-left: 10px;">
                                <?= \Helpers\Html::input_color(
                                    'cfg[menu_bg_color]',
                                    \K::$fw->cfg->get('menu_bg_color')
                                ) ?>
                                <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_BACKGROUND) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_listing_heading"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_LISTING_HEADING_TOOLTIP
                    ) . \K::$fw->TEXT_LISTING_HEADING; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[listing_heading]',
                        \K::$fw->cfg->get('listing_heading'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_window_heading"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_WINDOW_HEADING_TOOLTIP
                    ) . \K::$fw->TEXT_WINDOW_HEADING; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[window_heading]',
                        \K::$fw->cfg->get('window_heading'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_window_width"><?= \K::$fw->TEXT_WINDOW_WIDTH; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[window_width]',
                        [
                            '' => \K::$fw->TEXT_AUTOMATIC,
                            'ajax-modal-width-790' => \K::$fw->TEXT_WIDE,
                            'ajax-modal-width-1100' => \K::$fw->TEXT_XWIDE
                        ],
                        \K::$fw->cfg->get('window_width'),
                        ['class' => 'form-control input-medium']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_insert_button"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_INSERT_BUTTON_TITLE_TOOLTIP
                    ) . \K::$fw->TEXT_INSERT_BUTTON_TITLE; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[insert_button]',
                        \K::$fw->cfg->get('insert_button'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_reports_hide_insert_button"><?= \K::$fw->TEXT_HIDE_INSERT_BUTTON_IN_REPORTS; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[reports_hide_insert_button]',
                        $default_selector,
                        \K::$fw->cfg->get('reports_hide_insert_button'),
                        ['class' => 'form-control input-small']
                    ); ?>
                </div>
            </div>
            <h3 class="form-section "><?= \K::$fw->TEXT_DEFAULT_NOTIFICATIONS ?></h3>
            <p class="form-section-description"><?= \K::$fw->TEXT_DEFAULT_NOTIFICATIONS_INFO ?></p>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_insert_button"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_EMAIL_SUBJECT_NEW_ITEM_TOOLTIP
                    ) . \K::$fw->TEXT_EMAIL_SUBJECT_NEW_ITEM; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[email_subject_new_item]',
                        \K::$fw->cfg->get('email_subject_new_item'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_insert_button"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_EMAIL_SUBJECT_UPDATED_ITEM_TOOLTIP
                    ) . \K::$fw->TEXT_EMAIL_SUBJECT_UPDATED_ITEM; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[email_subject_updated_item]',
                        \K::$fw->cfg->get('email_subject_updated_item'),
                        ['class' => 'form-control input-large']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_disable_notification"><?= \K::$fw->TEXT_DISABLE_EMAIL_NOTIFICATIONS ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[disable_notification]',
                        $default_selector,
                        \K::$fw->cfg->get('disable_notification', 0),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_disable_internal_notification"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_DISABLE_INTERNAL_NOTIFICATIONS_INFO
                    ) . \K::$fw->TEXT_DISABLE_INTERNAL_NOTIFICATIONS ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[disable_internal_notification]',
                        $default_selector,
                        \K::$fw->cfg->get('disable_internal_notification', 0),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_disable_highlight_unread"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_DISABLE_HIGHLIGHT_UNREAD_INFO
                    ) . \K::$fw->TEXT_DISABLE_HIGHLIGHT_UNREAD ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[disable_highlight_unread]',
                        $default_selector,
                        \K::$fw->cfg->get('disable_highlight_unread', 0),
                        ['class' => 'form-control input-small']
                    ) ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="comments_configuration">
            <div class="form-group">
                <label class="col-md-3 control-label" for="cfg_use_comments"><?= \K::$fw->TEXT_USE_COMMENTS; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[use_comments]',
                        $default_selector,
                        \K::$fw->cfg->get('use_comments', 0),
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_USE_COMMENTS_TOOLTIP) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_DISPLAY_COMMENTS_ID; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[display_comments_id]',
                        $default_selector,
                        \K::$fw->cfg->get('display_comments_id'),
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_DISPLAY_COMMENTS_TOOLTIP) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_DISPLAY_LAST_COMMENT_IN_LISTING; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[display_last_comment_in_listing]',
                        $default_selector,
                        \K::$fw->cfg->get('display_last_comment_in_listing', 1),
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_DISPLAY_LAST_COMMENT_IN_LISTING_INFO) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_USE_EDITOR_IN_COMMENTS; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[use_editor_in_comments]',
                        $default_selector,
                        \K::$fw->cfg->get('use_editor_in_comments'),
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_USE_EDITOR_IN_COMMENTS_TOOLTIP) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_DISABLE_ATTACHMENTS; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[disable_attachments_in_comments]',
                        $default_selector,
                        \K::$fw->cfg->get('disable_attachments_in_comments'),
                        ['class' => 'form-control input-small']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_DISABLE_USER_AVATAR; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[disable_avatar_in_comments]',
                        $default_selector,
                        \K::$fw->cfg->get('disable_avatar_in_comments', 0),
                        ['class' => 'form-control input-small']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_image_preview_in_comments"><?= \Helpers\App::tooltip_icon(
                        \K::$fw->TEXT_USE_IMAGE_PREVIEW_TIP
                    ) . \K::$fw->TEXT_USE_IMAGE_PREVIEW; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[image_preview_in_comments]',
                        $default_selector,
                        \K::$fw->cfg->get('image_preview_in_comments', 0),
                        ['class' => 'form-control input-small']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_insert_button"><?= \K::$fw->TEXT_EMAIL_SUBJECT_NEW_COMMENT; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::input_tag(
                        'cfg[email_subject_new_comment]',
                        \K::$fw->cfg->get('email_subject_new_comment'),
                        ['class' => 'form-control input-large']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_EMAIL_SUBJECT_NEW_COMMENT_TOOLTIP) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_send_notification_to_assigned"><?= \K::$fw->TEXT_SEND_NOTIFICATION_TO_ASSIGNED_ONLY; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[send_notification_to_assigned]',
                        $default_selector,
                        \K::$fw->cfg->get('send_notification_to_assigned', 0),
                        ['class' => 'form-control input-small']
                    ); ?>
                    <?= \Helpers\App::tooltip_text(\K::$fw->TEXT_SEND_COMMENTS_NOTIFICATION_TO_ASSIGNED_INFO) ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="redirects_configuration">
            <?php
            $after_adding_selector = [
                'subentity' => \K::$fw->TEXT_REDIRECT_TO_SUBENTITY,
                'listing' => \K::$fw->TEXT_REDIRECT_TO_LISTING,
                'info' => \K::$fw->TEXT_REDIRECT_TO_INFO,
                'form' => \K::$fw->TEXT_KEEP_CURRENT_FORM_OPEN,
            ];

            $click_heading_selector = [
                'subentity' => \K::$fw->TEXT_REDIRECT_TO_SUBENTITY,
                'info' => \K::$fw->TEXT_REDIRECT_TO_INFO,
            ];

            if (\Helpers\App::is_ext_installed()) {
                $click_heading_selector = array_merge(
                    $click_heading_selector,
                    \Models\Main\Items\Items_redirects::get_reports_choices($_GET['entities_id'])
                );
            }
            ?>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_REDIRECT_AFTER_ADDING; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[redirect_after_adding]',
                        $after_adding_selector,
                        \K::$fw->cfg->get('redirect_after_adding'),
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"
                       for="cfg_use_comments"><?= \K::$fw->TEXT_REDIRECT_AFTER_CLICK_HEADING; ?></label>
                <div class="col-md-9">
                    <?= \Helpers\Html::select_tag(
                        'cfg[redirect_after_click_heading]',
                        $click_heading_selector,
                        \K::$fw->cfg->get('redirect_after_click_heading'),
                        ['class' => 'form-control input-xlarge']
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</form>

<script>
    $(function () {
        $('.tooltips').tooltip();
    });
</script>