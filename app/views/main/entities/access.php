<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_ENTITY_ACCESS ?></h3>

<p><?= \K::$fw->TEXT_ENTITY_ACCESS_INFO . \K::$fw->TEXT_ENTITY_ACCESS_INFO_EXTRA ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/entities/access/set_access', 'entities_id=' . \K::$fw->GET['entities_id'])
) ?>

<table class="table table-striped table-bordered table-hover">
    <tr>
        <th><?= \K::$fw->TEXT_USERS_GROUPS ?></th>
        <th><?= \K::$fw->TEXT_VIEW_ACCESS ?></th>
        <th><?= \K::$fw->TEXT_ACCESS ?></th>
    </tr>
    <tr>
        <td><?= \K::$fw->TEXT_ADMINISTRATOR ?></td>
        <td><?= \K::$fw->TEXT_FULL_ACCESS ?></td>
        <td><?= \K::$fw->TEXT_FULL_ACCESS ?></td>
    </tr>

    <?php
    $groups_query = \K::model()->db_fetch('app_access_groups', [], ['order' => 'sort_order,name'], 'id,name');

    //while ($v = db_fetch_array($groups_query)) {
    foreach ($groups_query as $v) {
        $v = $v->cast();

        $access_schema = [];

        /*$access_info_query = db_query(
            "select access_schema from app_entities_access where entities_id='" . db_input(
                $_GET['entities_id']
            ) . "' and access_groups_id='" . $v['id'] . "'"
        );*/

        $access_info = \K::model()->db_fetch_one('app_entities_access', [
            'entities_id = ? and access_groups_id = ?',
            \K::$fw->GET['entities_id'],
            $v['id']
        ], [], 'access_schema');

        if ($access_info) {
            $access_schema = explode(',', $access_info['access_schema']);
        }

        echo '
      <tr>
        <td>' . $v['name'] . '</td>
        <td>' . \Helpers\Html::select_tag(
                'access[' . $v['id'] . '][]',
                \Models\Main\Access_groups::get_access_view_choices(),
                \Models\Main\Access_groups::get_access_view_value($access_schema),
                [
                    'id' => 'access_' . $v['id'],
                    'class' => 'form-control input-large',
                    'onChange' => 'check_access_schema(this.value,' . $v['id'] . ')'
                ]
            ) . '</td>
  			<td>' . \Helpers\Html::select_tag(
                'access[' . $v['id'] . '][]',
                \Models\Main\Access_groups::get_access_choices(),
                $access_schema,
                [
                    'id' => 'access_schema_' . $v['id'],
                    'class' => 'form-control input-xlarge chosen-select',
                    'multiple' => 'multiple'
                ]
            ) . '</td>	        
      </tr>    
    ';
    }

    ?>
</table>

<br>
<?php
if (count($groups_query)) echo \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>
</form>

<script>
    function check_access_schema(access, group_id) {
        if (access == '') {
            $('#access_schema_' + group_id).val('');
            $('#access_schema_' + group_id).trigger("chosen:updated");
        }
    }
</script>