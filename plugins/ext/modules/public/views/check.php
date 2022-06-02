<h3 class="form-title"><?php
    echo(strlen($public_form['check_page_title']) > 0 ? $public_form['check_page_title'] : $public_form['name']) ?></h3>

<?php
echo(strlen(
    $public_form['check_page_description']
) > 0 ? '<p>' . $public_form['check_page_description'] . '</p>' : '') ?>

<div class="items-form-conteiner">

    <?php
    echo form_tag(
        'check_form',
        url_for('ext/public/check', 'action=check&id=' . $public_form['id']),
        ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal']
    ) ?>

    <?php
    $html = '';

    if (strlen($public_form['check_enquiry_fields'])) {
        $fields_query = db_query(
            "select f.* from app_fields f where f.id in (" . $public_form['check_enquiry_fields'] . ") and  f.entities_id='" . db_input(
                $current_entity_id
            ) . "' order by f.sort_order, f.name"
        );
        while ($v = db_fetch_array($fields_query)) {
            if ($v['type'] == 'fieldtype_text_pattern_static') {
                $html .= '
		          <div class="form-group form-group-' . $v['id'] . '">
		          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">
		          	<span class="required-label">*</span>' .
                    ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                    fields_types::get_option($v['type'], 'name', $v['name']) .
                    '</label>
		            <div class="col-md-9">
		          	  <div id="fields_' . $v['id'] . '_rendered_value">' . input_tag(
                        'fields[' . $v['id'] . ']',
                        '',
                        ['class' => 'form-control input-medium required']
                    ) . '</div>
		              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
		            </div>
		          </div>
		        ';
            } else {
                $v['is_required'] = 1;

                switch ($v['type']) {
                    case 'fieldtype_random_value':
                    case 'fieldtype_id':
                    case 'fieldtype_auto_increment':
                        $field_type = 'fieldtype_input';
                        $v['configuration'] = '{"width":"input-small"}';
                        break;
                    case 'fieldtype_date_added':
                        $field_type = 'fieldtype_input_date';
                        break;
                    default:
                        $field_type = $v['type'];
                        break;
                }

                $html .= '
		          <div class="form-group form-group-' . $v['id'] . '">
		          	<label class="col-md-3 control-label" for="fields_' . $v['id'] . '">
		          	<span class="required-label">*</span>' .
                    ($v['tooltip_display_as'] == 'icon' ? tooltip_icon($v['tooltip']) : '') .
                    fields_types::get_option($v['type'], 'name', $v['name']) .
                    '</label>
		            <div class="col-md-9">
		          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render(
                        $field_type,
                        $v,
                        ['field_' . $v['id'] => ''],
                        ['parent_entity_item_id' => 0, 'form' => 'item', 'is_new_item' => true]
                    ) . '</div>
		              ' . ($v['tooltip_display_as'] != 'icon' ? tooltip_text($v['tooltip']) : '') . '
		            </div>
		          </div>
		        ';
            }
        }
    }

    echo $html;
    ?>

    <?php
    if (app_recaptcha::is_enabled()): ?>
        <div class="form-group">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9">
                <?php
                echo app_recaptcha::render() ?>
            </div>
        </div>
    <?php
    endif ?>

    <?php
    echo '
  <div class="modal-footer">
    <div id="form-error-container"></div>
      <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>
      <button type="submit" class="btn btn-primary btn-primary-modal-action">' . (strlen(
            $public_form['check_button_title']
        ) > 0 ? $public_form['check_button_title'] : TEXT_EXT_PB_BUTTONS_CHECK_TITLE_DEFAULT) . '</button>
  </div>';
    ?>

    </form>
</div>


<script>
    $(function () {
        $('a').attr('target', '_new');
        $('#check_form').validate();
    })
</script> 