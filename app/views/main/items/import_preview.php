<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<div class="row">
    <div class="col-md-12">

        <ul class="page-breadcrumb breadcrumb noprint">
            <?= \Models\Main\Items\Items::render_breadcrumb(\K::$fw->app_breadcrumb) ?>
        </ul>

    </div>
</div>

<p><?= \K::$fw->TEXT_IMPORT_BIND_FIELDS ?></p>

<?php

if (\K::$fw->current_entity_id == 1) {
    echo '<div class="alert alert-info">' . \K::$fw->TEXT_USERS_IMPORT_NOTE . '</div>';
}

if (\K::$fw->multilevel_import > 0) {
    $choices = [];
    $choices[] = \Models\Main\Entities::get_name_by_id(\K::$fw->multilevel_import);
    foreach (\Models\Main\Entities::get_parents(\K::$fw->multilevel_import) as $entity_id) {
        $choices[] = \Models\Main\Entities::get_name_by_id($entity_id);

        if ($entity_id == \K::$fw->current_entity_id) {
            break;
        }
    }

    $choices = array_reverse($choices);

    $html = '
			
			<div class="alert alert-block alert-info fade in">				
				<h4 class="alert-heading">' . \K::$fw->TEXT_MULTI_LEVEL_IMPORT . ': <small>' . implode(
            ' <i class="fa fa-angle-right"></i> ',
            $choices
        ) . '</small></h4>
				<p>
					 ' . \K::$fw->TEXT_MULTI_LEVEL_IMPORT_NOTE . '
				</p>
			</div>
			';

    echo $html;
}

echo \Helpers\Html::form_tag(
        'import_data',
        \Helpers\Urls::url_for(
            'main/items/import/import',
            'path=' . \K::$fw->app_path . '&multilevel_import=' . \K::$fw->multilevel_import
        )
    ) . '<div id="worksheet_preview_container"> <table>';
for ($row = 0; $row < count(\K::$fw->worksheet); ++$row) {
    if ($row == 1) {
        echo '<tr><td></td>';

        for ($col = 1; $col <= count(\K::$fw->worksheet[$row]); ++$col) {
            $field_name = '';
            if (isset(\K::$fw->import_fields[$col])) {
                $field_id = \K::$fw->import_fields[$col];
                //$field_info_query = db_query("select id, entities_id from app_fields where id='" . $field_id . "'");

                $field_info = \K::model()->db_fetch_one('app_fields', [
                    'id = ?',
                    $field_id
                ], [], 'id,entities_id');

                if ($field_info) {
                    if (\K::$fw->multilevel_import > 0) {
                        $field_name .= '<small style="font-weight: normal">' . \Models\Main\Entities::get_name_by_id(
                                $field_info['entities_id']
                            ) . ':</small><br>';
                    }

                    $field_name .= \K::$fw->app_fields_cache[$field_info['entities_id']][$field_info['id']]['name'];
                }
            }

            echo '
				<td valign="top">' . \Helpers\Urls::link_to_modalbox(
                    \K::$fw->TEXT_BIND_FIELD,
                    \Helpers\Urls::url_for(
                        'main/items/import_bind',
                        'col=' . $col . '&entities_id=' . \K::$fw->current_entity_id . '&path=' . \K::$fw->app_path . '&multilevel_import=' . \K::$fw->multilevel_import
                    )
                ) . '
    			<div class="import_col" id="import_col_' . $col . '">' . (strlen(
                    $field_name
                ) ? '<b>' . $field_name . '</b>' : '-') . '</div>
      	</td>';
        }

        echo '</tr>';
    }

    echo '<tr><td>' . $row . '</td>';

    for ($col = 1; $col <= count(\K::$fw->worksheet[$row]); ++$col) {
        if (isset(\K::$fw->worksheet[$row][$col])) {
            echo '<td>' . \K::$fw->worksheet[$row][$col] . '</td>';
        } else {
            echo '<td></td>';
        }
    }

    echo '</tr>';
}
echo '</table>
  </div>
<p><label>' . \Helpers\Html::input_checkbox_tag(
        'import_first_row',
        1
    ) . ' ' . \K::$fw->TEXT_IMPORT_FIRST_ROW . '</label></p>';

//import users settings
if (\K::$fw->current_entity_id == 1) {
    echo '
  	<p>' . \K::$fw->TEXT_USERS_IMPORT_USERS_GROUP . ': ' . \Helpers\Html::select_tag(
            'users_group_id',
            \Models\Main\Access_groups::get_choices(),
            '',
            ['class' => 'form-control input-medium']
        ) . '</p>
  	<p><label>' . \Helpers\Html::input_checkbox_tag(
            'set_pwd_as_username',
            1
        ) . ' ' . \K::$fw->TEXT_IMPORT_SET_PWD_AS_USERNAME . '</label></p>';
}

//update settings
if ((\K::$fw->POST['import_action'] == 'update' or \K::$fw->POST['import_action'] == 'update_import') and \K::$fw->multilevel_import == 0) {
    $choices = ['' => ''];
    /*$fields_query = db_query(
        "select f.* from app_fields f where f.type in ('fieldtype_id','fieldtype_input','fieldtype_phone','fieldtype_random_value') and f.entities_id='" . \K::$fw->current_entity_id . "'"
    );*/

    $fields_query = \K::model()->db_fetch('app_fields', [
        'type in (' . \K::model()->quoteToString(
            ['fieldtype_id', 'fieldtype_input', 'fieldtype_phone', 'fieldtype_random_value']
        ) . ') and entities_id = ?',
        \K::$fw->current_entity_id
    ]);

    //while ($fields = db_fetch_array($fields_query)) {
    foreach ($fields_query as $fields) {
        $fields = $fields->cast();

        $choices[$fields['id']] = \Models\Main\Fields_types::get_option($fields['type'], 'name', $fields['name']);
    }

    $choices_col = ['' => ''];
    $row = 0;
    for ($col = 0; $col <= count(\K::$fw->worksheet[$row]); ++$col) {
        if (isset(\K::$fw->worksheet[$row][$col])) {
            $choices_col[$col] = \K::$fw->worksheet[$row][$col];
        }
    }

    echo '
		<h4>' . \K::$fw->TEXT_UPDATE_SETTINGS . '</h4>	
  	<p>' . \K::$fw->TEXT_UPDATE_BY_FIELD . ': ' . \Helpers\Html::select_tag(
            'update_by_field',
            $choices,
            '',
            ['class' => 'form-control input-medium required']
        ) . '</p>
  	<p>' . \K::$fw->TEXT_USE_COLUMN . ': ' . \Helpers\Html::select_tag(
            'update_use_column',
            $choices_col,
            '',
            ['class' => 'form-control input-medium required']
        ) . '</p>';
}

echo '<br>
  			<div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>' .
    \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_IMPORT, ['class' => 'btn btn-primary btn-primary-modal-action']
    ) . ' ' .
    \Helpers\Html::button_tag(
        \K::$fw->TEXT_BUTTON_BACK,
        \Helpers\Urls::url_for('main/items/items', 'path=' . \K::$fw->app_path),
        false,
        ['class' => 'btn btn-default btn-back']
    ) .
    \Helpers\Html::input_hidden_tag('worksheet', addslashes(json_encode(\K::$fw->worksheet))) .
    \Helpers\Html::input_hidden_tag('import_action', \K::$fw->POST['import_action']) . '  
</form>';
?>

<script>
    $(function () {
        $('#import_data').validate({
            submitHandler: function (form) {
                app_prepare_modal_action_loading(form)
                $('.btn-back').hide()
                return true;
            }
        })
    })

    function bind_field(col) {
        $.post("<?= \Helpers\Urls::url_for(
            'main/items/import/bind_field',
            'path=' . \K::$fw->app_path . '&multilevel_import=' . \K::$fw->multilevel_import
        ) ?>", $("#bind_field_form").serialize()).success(function (data) {

            if (data.trim() != '') {
                $('#import_col_' + col).html('<div class="binded_field_container" >' + data + '</div>');
            } else {
                $('#import_col_' + col).html('-');
            }
        });

        $('#ajax-modal').modal('hide');
        return false;
    }
</script>