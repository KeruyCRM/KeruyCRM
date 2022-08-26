<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>


<h3 class="page-title"><?= \K::$fw->TEXT_NAV_COMMENTS_ACCESS ?></h3>

<p><?= \K::$fw->TEXT_COMMENTS_ACCESS_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'cfg',
    \Helpers\Urls::url_for('main/entities/comments_access/set_access', 'entities_id=' . \K::$fw->GET['entities_id'])
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th><?= \K::$fw->TEXT_USERS_GROUPS ?></th>
            <th><?= \K::$fw->TEXT_ACCESS ?></th>
        </tr>
        <tr>
            <td><?= \K::$fw->TEXT_ADMINISTRATOR ?></td>
            <td><?= \K::$fw->TEXT_YES ?></td>
        </tr>
        <?php
        $groups_query = \K::model()->db_query_exec(
            "select ag.* from app_access_groups ag, app_entities_access ea where ea.access_groups_id = ag.id and ea.entities_id = ? and length(ea.access_schema) > 0 order by ag.sort_order, ag.name",
            \K::$fw->GET['entities_id'],
            'app_access_groups,app_entities_access'
        );

        //while ($v = db_fetch_array($groups_query)) {
        foreach ($groups_query as $v) {
            $schema = '';
            /*$acess_info_query = db_query(
                "select access_schema from app_comments_access where entities_id='" . db_input(
                    \K::$fw->GET['entities_id']
                ) . "' and access_groups_id='" . $v['id'] . "'"
            );*/

            $access_info = \K::model()->db_fetch_one('app_comments_access', [
                'entities_id = ? and access_groups_id = ?',
                \K::$fw->GET['entities_id'],
                $v['id']
            ], [], 'access_schema');

            if ($access_info) {
                $schema = str_replace(',', '_', $access_info['access_schema']);
            }

            echo '
      <tr>
        <td>' . $v['name'] . /*vertical-align: middle;*/'</td>
        <td>' . \Helpers\Html::select_tag(
                    'access[' . $v['id'] . ']',
                    \Models\Main\Comments::get_access_choices(),
                    $schema,
                    ['class' => 'form-control input-medium']
                ) . '</td>        
      </tr>    
    ';
        }
        ?>
    </table>
</div>

<br>
<?php
if (count($groups_query)) echo \Helpers\Html::submit_tag(\K::$fw->TEXT_BUTTON_SAVE) ?>
</form>