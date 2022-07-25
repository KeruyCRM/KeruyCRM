<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation'));
//require(component_path('entities/navigation'))           ?>

<h3 class="page-title"><?= sprintf(\K::$fw->TEXT_ACCESS_RULES_FOR_FIELD, \K::$fw->field_info['name']) ?></h3>

<p><?= \K::$fw->TEXT_ACCESS_RULES_FOR_FIELD_INFO ?></p>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD_NEW_RULE,
    \Helpers\Urls::url_for(
        'main/access_rules/rules_form',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ),
    true
) ?>

<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_RULE_FOR_FIELD ?></th>
            <th width="100%"><?= \K::$fw->TEXT_VALUES ?></th>
            <th><?= \K::$fw->TEXT_USERS_GROUPS ?></th>
            <th><?= \K::$fw->TEXT_ACCESS ?></th>
            <th><?= \K::$fw->TEXT_VIEW_ONLY ?></th>
            <th><?= \K::$fw->TEXT_NAV_COMMENTS_ACCESS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $access_choices = \Models\Main\Access_groups::get_access_choices();

        if (count(\K::$fw->form_fields_query) == 0) {
            echo '<tr><td colspan="9">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        }

        //while ($v = db_fetch_array($form_fields_query)):
        foreach (\K::$fw->form_fields_query as $v):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for(
                            'main/access_rules/rules_delete',
                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                        )
                    ) . ' ' . \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for(
                            'main/access_rules/rules_form',
                            'id=' . $v['id'] . '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                        )
                    ) ?></td>
                <td><?= \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) ?></td>
                <td>

                    <?php
                    if (strlen($v['choices'])) {
                        $cfg = new \Models\Main\Fields_types_cfg($v['configuration']);

                        if ($cfg->get('use_global_list') > 0) {
                            /*$choices_query = db_query(
                                "select * from app_global_lists_choices where lists_id = '" . db_input(
                                    $cfg->get('use_global_list')
                                ) . "' and id in (" . $v['choices'] . ") order by sort_order, name"
                            );*/
                            //TODO quoteToString
                            $choices_query = \K::model()->db_fetch('app_global_lists_choices', [
                                'lists_id = ? and id in (' . $v['choices'] . ')',
                                $cfg->get('use_global_list')
                            ], ['order' => 'sort_order,name'], 'name');
                        } else {
                            /*$choices_query = db_query(
                                "select * from app_fields_choices where fields_id = '" . db_input(
                                    $v['fields_id']
                                ) . "' and id in (" . $v['choices'] . ") order by sort_order, name"
                            );*/
                            $choices_query = \K::model()->db_fetch('app_fields_choices', [
                                'fields_id = ? and id in (' . $v['choices'] . ')',
                                $v['fields_id']
                            ], ['order' => 'sort_order,name'], 'name');
                        }

                        //while ($choices = db_fetch_array($choices_query)) {
                        foreach ($choices_query as $choices) {
                            $choices = $choices->cast();

                            echo $choices['name'] . '<br>';
                        }
                    }
                    ?>

                </td>
                <td>
                    <?php
                    if (strlen($v['users_groups'])) {
                        foreach (explode(',', $v['users_groups']) as $id) {
                            echo \Models\Main\Access_groups::get_name_by_id($id) . '<br>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    echo \K::$fw->TEXT_VIEW_ACCESS . '<br>';

                    if (strlen($v['access_schema'])) {
                        foreach (explode(',', $v['access_schema']) as $id) {
                            echo(isset($access_choices[$id]) ? $access_choices[$id] . '<br>' : '');
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (strlen($v['fields_view_only_access'])) {
                        foreach (explode(',', $v['fields_view_only_access']) as $id) {
                            echo \Models\Main\Fields::get_name_by_id($id) . '<br>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $comments_access_choices = \Models\Main\Comments::get_access_choices();
                    $comments_access_schema = ($v['comments_access_schema'] == 'no' ? '' : str_replace(
                        ',',
                        '_',
                        $v['comments_access_schema']
                    ));
                    if (isset($comments_access_choices[$comments_access_schema])) {
                        echo $comments_access_choices[$comments_access_schema];
                    }
                    ?>
                </td>
            </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table>
</div>

<?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
    'main/access_rules/fields',
    'entities_id=' . \K::$fw->GET['entities_id']
) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>'; ?>
