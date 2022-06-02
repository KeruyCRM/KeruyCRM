<?php
echo ajax_modal_template_header(TEXT_EXT_HEADING_UPDATE) ?>

<?php
echo form_tag(
    'form-update-fields',
    url_for('ext/with_selected/update', 'action=update_selected&reports_id=' . $_GET['reports_id'])
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
    if (isset($_GET['path'])) {
        echo input_hidden_tag('path', $_GET['path']);
    }
    ?>

    <div class="modal-body">
        <div id="modal-body-content">
            <p><?php
                echo TEXT_EXT_UPDATE_CONFIRMATION ?></p>

            <?php
            $entity_info = db_find('app_entities', $reports_info['entities_id']);
            $fields_access_schema = users::get_fields_access_schema(
                $reports_info['entities_id'],
                $app_user['group_id']
            );


            //define allowed types for update
            $allowed_types = [
                'fieldtype_checkboxes',
                'fieldtype_radioboxes',
                'fieldtype_dropdown',
                'fieldtype_dropdown_multiple',
                'fieldtype_dropdown_multilevel',
                'fieldtype_input_date',
                'fieldtype_input_datetime',
                'fieldtype_input_numeric',
                'fieldtype_grouped_users',
                'fieldtype_users',
                'fieldtype_users_ajax',
                'fieldtype_boolean',
                'fieldtype_image_map',
                'fieldtype_tags',
                'fieldtype_stages',
                'fieldtype_progress',
                'fieldtype_user_status',
                'fieldtype_boolean_checkbox',
            ];

            if (strlen($app_path)) {
                $allowed_types[] = 'fieldtype_entity';
                $allowed_types[] = 'fieldtype_entity_ajax';
            }

            //get fields choices                       
            $choices = [];
            $choices[''] = TEXT_SELECT_FIELD;
            $fields_query = db_query(
                "select f.* from app_fields f, app_forms_tabs t  where f.type in ('" . implode(
                    "','",
                    $allowed_types
                ) . "') and f.entities_id='" . db_input(
                    $reports_info['entities_id']
                ) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
            );
            while ($v = db_fetch_array($fields_query)) {
                //check field access
                if (isset($fields_access_schema[$v['id']])) {
                    continue;
                }

                $cfg = new fields_types_cfg($v['configuration']);

                //handle stages
                if ($v['type'] == 'fieldtype_stages' and $cfg->get('click_action') == 'change_value_next_step') {
                    continue;
                }

                $choices[$v['id']] = fields::get_name($v);
            }
            ?>


            <div id="field-to-update">
                <div class="portlet field-to-update-portlet">
                    <div class="portlet-title">
                        <div class="caption" style="margin-top: -6px;">
                            <?php
                            echo select_tag(
                                'fields_id[]',
                                $choices,
                                '',
                                [
                                    'class' => 'form-control input-large required fields-to-update',
                                    'data-portlet-id' => '1',
                                    'onChange' => 'use_field_to_update(this)'
                                ]
                            ) ?>
                        </div>
                        <div class="tools" style="display:none">
                            <a href="javascript:;" class="remove"></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="portlet-body-1"></div>
                    </div>
                </div>
            </div>

            <div id="extra-fields-to-update">
                <button type="button" class="btn btn-default" id="button-clone-field"
                        onClick="clone_field_to_update()"><?php
                    echo TEXT_EXT_ADD_FIELD ?></button>
            </div>


        </div>
    </div>
    <?php
    $notify_html = '';

    $check_query = db_query(
        "select count(*) as total from app_fields where entities_id='" . db_input(
            $reports_info['entities_id']
        ) . "' and type in ('fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users')"
    );
    $check = db_fetch_array($check_query);

    if ($check['total'] > 0) {
        $notify_html = '<label>' . tooltip_icon(
                TEXT_DO_NOT_NOTIFY_INFO
            ) . TEXT_DO_NOT_NOTIFY . ' ' . input_checkbox_tag('do_not_notify', '1') . '</label>';
    }

    $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));
    echo ajax_modal_template_footer(TEXT_EXT_BUTTON_UPDATE, $notify_html, $count_selected_text);
    ?>

<?php
} ?>
</form>

<script>
    $(function () {

        $('#form-update-fields').submit(function () {

            //check fields
            error = false;
            $('#form-update-fields .fields-to-update').each(function () {
                if ($(this).val() == '') {
                    error = true;

                    $(this).addClass("input-error").delay(1000).queue(function (next) {
                        $(this).removeClass("input-error");
                        next();
                    });
                }
            })

            //check numeric fields
            $('#form-update-fields .numeric-fields').each(function () {
                if ($(this).val() == '') {
                    error = true;
                    $(this).after('<div class="error ' + $(this).attr('id') + '"><?php echo TEXT_ENTER_VALUE ?></div>')
                } else {
                    if (!$(this).val().match(/^[0-9.]+$/) && !$(this).val().match(/^([\+\-\*\/])([0-9.]+)$/) && !$(this).val().match(/^([\+\-\*\/])([0-9.]+)([%])$/)) {
                        error = true;
                        $(this).after('<div class="error ' + $(this).attr('id') + '"><?php echo TEXT_ENTER_CORRECT_VALUE ?></div>')
                    }
                }

                $(this).focus(function () {
                    $('.' + $(this).attr('id')).remove();
                })
            })

            //check date fields
            $('#form-update-fields .date-fields').each(function () {
                if ($(this).val() == '') {
                    error = true;
                    $(this).after('<div class="error ' + $(this).attr('id') + '"><?php echo TEXT_ENTER_VALUE ?></div>')
                } else {
                    if (!$(this).val().match(/^(\d{4})-(\d{2})-(\d{2})$/) && !$(this).val().match(/^([\+\-])([0-9.]+)$/)) {
                        error = true;
                        $(this).after('<div class="error ' + $(this).attr('id') + '"><?php echo TEXT_ENTER_CORRECT_VALUE ?></div>')
                    }
                }

                $(this).focus(function () {
                    $('.' + $(this).attr('id')).remove();
                })
            })

            if (error) {
                return false;
            }

            $('button[type=submit]', this).css('display', 'none')
            $('#modal-body-content').css('visibility', 'hidden').css('height', '1px');
            $('#modal-body-content').after('<div class="ajax-loading"></div>');

            $('#modal-body-content').load($(this).attr('action'), $(this).serializeArray(), function () {
                $('.ajax-loading').css('display', 'none');
                $('#modal-body-content').css('visibility', 'visible').css('height', 'auto');
            })

            return false;
        })
    })

    function use_field_to_update(obj) {
        portlet_id = $(obj).attr('data-portlet-id');
        $('#portlet-body-' + portlet_id).html('<div class="ajax-loading"></div>');
        $('#portlet-body-' + portlet_id).load('<?php echo url_for(
                "ext/with_selected/update",
                "action=get_field_values&reports_id=" . $_GET["reports_id"] . (isset($_GET["path"]) ? "&path=" . $_GET["path"] : "")
            ) . "&fields_id=" ?>' + $(obj).val(), function (response, status, xhr) {
            if (status == "error") {
                $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
            } else {
                appHandleUniform();
                jQuery(window).resize();
            }
        });
    }

    function clone_field_to_update() {
        rnd = Math.floor((Math.random() * 1000) + 1)

        html = $('#field-to-update').html().replace('data-portlet-id="1"', 'data-portlet-id="' + rnd + '"').replace('id="portlet-body-1"', 'id="portlet-body-' + rnd + '"');
        $('#button-clone-field').before(html);
        $('#portlet-body-' + rnd).html('');

        $('#extra-fields-to-update .portlet-title .tools').css('display', 'inline-block');

        jQuery(window).resize();
    }
</script>