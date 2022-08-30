<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(
    \K::$fw->user_roles_info['name'] . ' / ' . \K::$fw->entity_info['name']
); ?>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for(
        'main/entities/user_roles_access/set_fields_access',
        'role_id=' . \K::$fw->GET['role_id'] . '&role_entities_id=' . \K::$fw->GET['role_entities_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    )
) ?>

<div class="modal-body">
    <div class="form-body ajax-modal-width-790">
        <?php
        $html = '
      <div class="table-scrollable">
      <table class="table table-striped table-bordered table-hover">
        <tr>
          <th>' . \K::$fw->TEXT_FIELDS . '</th>
          <th>' . \K::$fw->TEXT_ACCESS . ': ' . \Helpers\Html::select_tag(
                'access_0',
                array_merge(['' => ''], \K::$fw->access_choices_default),
                '',
                ['class' => 'form-control input-medium ', 'onChange' => 'set_access_to_all_fields(this.value,0)']
            ) . '</th>
        </tr>
      ';

        foreach (\K::$fw->fields_list as $id => $field) {
            $value = (\K::$fw->access_schema[$id] ?? 'yes');

            $access_choices = (in_array($field['type'], ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_created_by']
            ) ? \K::$fw->access_choices_internal : \K::$fw->access_choices_default);

            $html .= '
        <tr>
          <td>' . $field['name'] . '</td>
          <td>' . \Helpers\Html::select_tag(
                    'access[' . $id . ']',
                    $access_choices,
                    $value,
                    ['class' => 'form-control input-medium access_group_0']
                ) . '</td>
        </tr>
      ';
        }

        $html .= '</table></div>';

        echo $html;
        ?>
    </div>
</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>

</form>