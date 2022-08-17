<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_FIELDS_ACCESS ?></h3>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/entities/fields_access/set_access', 'entities_id=' . \K::$fw->GET['entities_id'])
) ?>
<?= \Helpers\Html::input_hidden_tag('ui_accordion_active', 0) ?>

<div id="accordion">
    <h3><?= \K::$fw->TEXT_ADMINISTRATOR ?></h3>
    <div>
        <?= \K::$fw->TEXT_ADMINISTRATOR_FULL_ACCESS ?>
    </div>
    <?php
    $count = 0;

    //while ($groups = db_fetch_array($groups_query)) {
    foreach (\K::$fw->groups_query as $groups) {
        $groups = $groups->cast();

        $entities_access_schema = \Models\Main\Users\Users::get_entities_access_schema(
            \K::$fw->GET['entities_id'],
            $groups['id']
        );

        if (!in_array('view', $entities_access_schema) and !in_array(
                'view_assigned',
                $entities_access_schema
            ) and \K::$fw->GET['entities_id'] != 1) {
            continue;
        }

        $count++;

        $html = '
      <div class="table-scrollable">
      <table class="table table-striped table-bordered table-hover">
        <tr>
          <th>' . \K::$fw->TEXT_FIELDS . '</th>
          <th>' . \K::$fw->TEXT_ACCESS . ': ' . \Helpers\Html::select_tag(
                'access_' . $groups['id'],
                array_merge(['' => ''], \K::$fw->access_choices_default),
                '',
                [
                    'class' => 'form-control input-medium ',
                    'onChange' => 'set_access_to_all_fields(this.value,' . $groups['id'] . ')'
                ]
            ) . '</th>
        </tr>
      ';

        $access_schema = \Models\Main\Users\Users::get_fields_access_schema(\K::$fw->GET['entities_id'], $groups['id']);

        foreach (\K::$fw->fields_list as $id => $field) {
            $value = ($access_schema[$id] ?? 'yes');

            $access_choices = (in_array(
                $field['type'],
                ['fieldtype_id', 'fieldtype_date_added', 'fieldtype_date_updated', 'fieldtype_created_by']
            ) ? \K::$fw->access_choices_internal : \K::$fw->access_choices_default);

            $html .= '
        <tr>
          <td>' . $field['name'] . '</td>
          <td>' . \Helpers\Html::select_tag(
                    'access[' . $groups['id'] . '][' . $id . ']',
                    $access_choices,
                    $value,
                    ['class' => 'form-control input-medium access_group_' . $groups['id']]
                ) . '</td>
        </tr>
      ';
        }

        $html .= '</table></div>';

        echo '
      <h3>' . $groups['name'] . '</h3>
      <div>
        ' . $html . '
      </div>
    ';
    }
    ?>

</div>
<br>
<?php
if ($count > 0) echo \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>

</form>

<script>
    $(function () {
        $("#accordion").accordion({
            heightStyle: 'content',
            active: <?=(\K::$fw->GET["ui_accordion_active"] ?? "0") ?>,
            activate: function (event, ui) {
                active = $('#accordion').accordion('option', 'active');
                $('#ui_accordion_active').val(active)
            }
        });
    });
</script>