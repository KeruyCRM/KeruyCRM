<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->field_info['name'] . ' <i class="fa fa-angle-right"></i> <a href="' . url_for(
        'entities/user_roles',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    ) . '">' . \K::$fw->TEXT_USER_ROLES . '</a> <i class="fa fa-angle-right"></i> ' . \K::$fw->user_roles_info['name'] . ' <i class="fa fa-angle-right"></i> ' . \K::$fw->TEXT_NAV_ENTITY_ACCESS ?></h3>

<p><?= \K::$fw->TEXT_USER_ROLES_ACCESS_INFO ?></p>

<?= \Helpers\Html::form_tag(
    'pivot_access_form',
    \Helpers\Urls::url_for(
        'main/entities/user_roles_access/save',
        'role_id=' . \K::$fw->GET['role_id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
    )
) ?>
<div class="table-scrollable" style="overflow-x:visible;overflow-y:visible; ">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th></th>
            <th width="100%"><?= \K::$fw->TEXT_ENTITY ?></th>
            <th><?= \K::$fw->TEXT_VIEW_ACCESS ?></th>
            <th><?= \K::$fw->TEXT_ACCESS ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach (\Models\Main\Entities::get_tree(\K::$fw->GET['entities_id']) as $v):

            $checked = false;

            $access_schema = [];
            $comments_schema = '';

            /*$access_info_query = db_query(
                "select access_schema, comments_access from app_user_roles_access where entities_id='" . $v['id'] . "' and user_roles_id='" . db_input(
                    \K::$fw->GET['role_id']
                ) . "' and fields_id='" . \K::$fw->GET['fields_id'] . "'"
            );*/

            $access_info = \K::model()->db_fetch_one('app_user_roles_access', [
                'entities_id = ? and user_roles_id = ? and fields_id = ?',
                $v['id'],
                \K::$fw->GET['role_id'],
                \K::$fw->GET['fields_id']
            ],[],'access_schema,comments_access');

            if ($access_info) {
                $checked = true;

                $access_schema = explode(',', $access_info['access_schema']);

                $comments_schema = str_replace(',', '_', $access_info['comments_access']);
            }

            $entity_cfg = new \Models\Main\Entities_cfg($v['id']);

            ?>
            <tr>
                <td>
                    <?php
                    $params = ['class' => 'access-schema-settings access-schema-settings-flag'];

                    if ($checked) {
                        $params['checked'] = 'checked';
                    }

                    echo \Helpers\Html::input_checkbox_tag('entities[' . $v['id'] . ']', $v['id'], $params) ?></td>
                <td style="white-space: nowrap">
                    <?= '<label for="entities_' . $v['id'] . '">' . str_repeat(
                        '&nbsp;<i class="fa fa-minus" aria-hidden="true"></i>&nbsp;',
                        $v['level']
                    ) . ' ' . $v['name'] . '</label>' ?>
                </td>
                <td>
                    <div class="access-configuration-block<?= $v['id'] ?> hidden">
                        <?= \Helpers\Html::select_tag(
                            'access[' . $v['id'] . '][]',
                            \Models\Main\Access_groups::get_access_view_choices(),
                            \Models\Main\Access_groups::get_access_view_value($access_schema),
                            [
                                'id' => 'access_' . $v['id'],
                                'class' => 'form-control input-large access-schema-settings',
                                'data-entity-id' => $v['id'],
                                'onChange' => 'check_access_schema(this.value,' . $v['id'] . ')'
                            ]
                        );

                        echo '<div style="padding-top: 5px; text-align: right;">' . \Helpers\Html::button_tag(
                                \K::$fw->TEXT_NAV_FIELDS_ACCESS,
                                \Helpers\Urls::url_for(
                                    'main/entities/user_roles_fields_access',
                                    'role_id=' . \K::$fw->GET['role_id'] . '&role_entities_id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
                                ),
                                true,
                                ['class' => 'btn btn-default btn-sm']
                            ) . '</div>';
                        ?>
                    </div>
                </td>
                <td>
                    <div class="access-configuration-block<?php
                    echo $v['id'] ?> hidden">
                        <?php
                        $choices = \Models\Main\Access_groups::get_access_choices();
                        unset($choices['reports']);

                        echo \Helpers\Html::select_tag(
                            'access[' . $v['id'] . '][]',
                            $choices,
                            $access_schema,
                            [
                                'id' => 'access_schema_' . $v['id'],
                                'class' => 'form-control input-xlarge chosen-select access-schema-settings',
                                'data-entity-id' => $v['id'],
                                'multiple' => 'multiple'
                            ]
                        );

                        if ($entity_cfg->get('use_comments')) {
                            echo '<div style="padding-top: 5px; "><ul class="list-inline"><li>' . \K::$fw->TEXT_COMMENTS . ':</li><li>' . \Helpers\Html::select_tag(
                                    'comments_access[' . $v['id'] . ']',
                                    \Models\Main\Comments::get_access_choices(),
                                    $comments_schema,
                                    [
                                        'class' => 'form-control input-medium access-schema-settings',
                                        'data-entity-id' => $v['id']
                                    ]
                                ) . '</li><ul></div>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
        <?php
        endforeach ?>
        </tbody>
    </table>
</div>

</form>

<?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
    'main/entities/user_roles',
    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . \K::$fw->GET['fields_id']
) . '">' . \K::$fw->TEXT_BUTTON_BACK . '</a>' ?>

<script>
    function check_access_schema(access, entity_id) {
        if (access == '') {
            $('#access_schema_' + entity_id).val('');
            $('#access_schema_' + entity_id).trigger("chosen:updated");
            $('#comments_access_' + entity_id).val('');
        }
    }

    function check_access_schema_flag() {
        $('.access-schema-settings-flag').each(function () {
            entity_id = $(this).val();
            if ($(this).is(':checked')) {
                $('.access-configuration-block' + entity_id).removeClass('hidden')
            } else {
                $('.access-configuration-block' + entity_id).addClass('hidden')
            }
        })
    }

    $(function () {
        $('.access-schema-settings').change(function () {
            check_access_schema_flag();
            form = $('#pivot_access_form');
            $.ajax({type: "POST", url: form.attr('action'), data: form.serializeArray()});
        })

        check_access_schema_flag();
    })
</script>